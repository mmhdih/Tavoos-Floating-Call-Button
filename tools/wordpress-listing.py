#!/usr/bin/env python3
"""Guard and verify a readme/assets-only WordPress.org update; never handles secrets."""
import argparse
import configparser
import hashlib
import json
import os
from pathlib import Path
import re
import shutil
import subprocess
import urllib.request
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[1]
SLUG = 'tavoos-floating-call-button'
URL = f'https://plugins.svn.wordpress.org/{SLUG}'
VERSION = '1.3.1'
SOURCE = ROOT / 'docs/wordpress-org'
RELEASE = json.loads((ROOT / '.github/releases/1.3.1.json').read_text())['files']
REQUEST = ROOT / '.github/wordpress-listing/request.json'


def run(*args):
    return subprocess.check_output(args, text=True).strip()


def hashes(root):
    result = {}
    for p in root.rglob('*'):
        if '.svn' in p.parts:
            continue
        if p.is_symlink():
            raise ValueError(f'Unexpected symlink: {p}')
        if p.is_file():
            result[p.relative_to(root).as_posix()] = hashlib.sha256(p.read_bytes()).hexdigest()
    return result


def validate_source():
    req = json.loads(REQUEST.read_text())
    assert type(req['publish']) is bool
    assert req['version'] == VERSION and req['slug'] == SLUG
    readme = (SOURCE / 'readme.txt').read_text()
    assert re.search(r'^Stable tag: 1\.3\.1$', readme, re.M)
    assert (SOURCE / 'readme.txt').stat().st_size < 100_000
    assert len(readme.split('== Description ==')[0].strip().split('\n')[-1]) <= 150
    for heading in ['Description', 'Installation', 'Frequently Asked Questions', 'Screenshots', 'Changelog', 'Upgrade Notice']:
        assert f'== {heading} ==' in readme
    captions = re.findall(r'^\d+\. ', readme.split('== Screenshots ==')[1].split('== Changelog ==')[0], re.M)
    assets = hashes(SOURCE / 'assets')
    assert len(captions) == 13
    for i in range(1, 14):
        assert f'screenshot-{i}.png' in assets
    for name in assets:
        assert re.fullmatch(r'(banner-(772x250|1544x500)|icon-(128x128|256x256)|screenshot-\d+(-fa_IR)?)\.png', name), name
        raw = (SOURCE / 'assets' / name).read_bytes()
        assert raw[:8] == b'\x89PNG\r\n\x1a\n'
        width, height = int.from_bytes(raw[16:20], 'big'), int.from_bytes(raw[20:24], 'big')
        if name.startswith(('banner-', 'icon-')):
            assert (width, height) == tuple(map(int, re.search(r'(\d+)x(\d+)', name).groups()))
        assert len(raw) < (4_000_000 if name.startswith('banner') else 1_000_000 if name.startswith('icon') else 10_000_000)
    actual = {'readme_sha256': hashlib.sha256((SOURCE / 'readme.txt').read_bytes()).hexdigest(), 'assets': assets}
    assert req['readme_sha256'] == actual['readme_sha256']
    assert req['assets'] == assets
    for path, expected in RELEASE.items():
        assert hashlib.sha256((ROOT / path).read_bytes()).hexdigest() == expected, f'Frozen Git release file changed: {path}'
    print(f'Validated 13 captions, {len(assets)} assets, stable tag {VERSION}; all 12 Git release files remain frozen.')
    return req


def verify_runtime(path, permitted_readmes):
    current = hashes(path)
    assert set(current) == set(RELEASE), f'Unexpected package paths: {path}'
    for name, expected in RELEASE.items():
        if name == 'readme.txt':
            assert current[name] in permitted_readmes, f'Unreviewed remote readme: {path}'
        else:
            assert current[name] == expected, f'Executable/release file differs: {path}/{name}'
    assert 'Stable tag: 1.3.1' in (path / 'readme.txt').read_text()
    return current


def checkout(destination):
    assert not destination.exists(), f'Refuse to reuse working copy: {destination}'
    print(run('svn', 'checkout', '--non-interactive', '--depth', 'immediates', URL, str(destination)))
    for sub in ['assets', 'trunk', 'tags']:
        print(run('svn', 'update', '--non-interactive', '--set-depth', 'immediates' if sub == 'tags' else 'infinity', str(destination / sub)))
    assert (destination / 'tags/1.3.1').exists(), 'Existing 1.3.1 SVN tag is required'
    print(run('svn', 'update', '--non-interactive', '--set-depth', 'infinity', str(destination / 'tags/1.3.1')))


def prepare(state):
    req = validate_source()
    state.mkdir(parents=True, exist_ok=False)
    wc = state / 'preview'
    checkout(wc)
    allowed_readmes = {RELEASE['readme.txt'], req['readme_sha256']}
    before = {sub: verify_runtime(wc / sub, allowed_readmes) for sub in ['trunk', 'tags/1.3.1']}
    before['assets'] = hashes(wc / 'assets')
    before['tags'] = run('svn', 'list', '--non-interactive', URL + '/tags/')
    before['revision'] = run('svn', 'info', '--show-item', 'revision', str(wc))
    print('Fresh SVN baseline:', json.dumps(before, sort_keys=True))
    merged = ROOT / '.github/listing-staged-assets'
    assert not merged.exists()
    shutil.copytree(wc / 'assets', merged, ignore=shutil.ignore_patterns('.svn'))
    shutil.copytree(SOURCE / 'assets', merged, dirs_exist_ok=True)
    for sub in ['trunk', 'tags/1.3.1']:
        shutil.copy2(SOURCE / 'readme.txt', wc / sub / 'readme.txt')
    shutil.copytree(merged, wc / 'assets', dirs_exist_ok=True)
    print(run('svn', 'add', '--force', str(wc / 'assets')))
    for name in req['assets']:
        run('svn', 'propset', 'svn:mime-type', 'image/png', str(wc / 'assets' / name))
    changes = ET.fromstring(run('svn', 'status', '--xml', str(wc)))
    permitted = {'trunk/readme.txt', 'tags/1.3.1/readme.txt'} | {'assets/' + n for n in req['assets']}
    changed = []
    for entry in changes.findall('.//entry'):
        rel = Path(entry.attrib['path']).relative_to(wc).as_posix()
        assert rel in permitted, f'Change outside approved listing scope: {rel}'
        status = entry.find('wc-status')
        assert status.attrib['item'] in ['normal', 'modified', 'added'], status.attrib
        changed.append(rel)
    expected = {'assets': hashes(merged), 'readme_sha256': req['readme_sha256'], 'tags': before['tags']}
    (state / 'expected.json').write_text(json.dumps(expected, indent=2))
    (state / 'before.json').write_text(json.dumps(before, indent=2))
    print('DRY-RUN SCOPE:', json.dumps(sorted(changed)))
    print(run('svn', 'diff', str(wc / 'trunk/readme.txt')))
    # 10up's readme-only mode reads this working-tree file; the Git object stays frozen.
    shutil.copy2(SOURCE / 'readme.txt', ROOT / 'readme.txt')
    # Ensure PNG MIME properties are applied when 10up adds new assets.
    config_path = Path.home() / '.subversion/config'
    config = configparser.ConfigParser(interpolation=None)
    config.read(config_path)
    for section in ['miscellany', 'auto-props']:
        if not config.has_section(section):
            config.add_section(section)
    config['miscellany']['enable-auto-props'] = 'yes'
    config['auto-props']['*.png'] = 'svn:mime-type=image/png'
    with config_path.open('w') as stream:
        config.write(stream)
    publish = req['publish']
    if os.environ.get('GITHUB_EVENT_NAME') == 'workflow_dispatch':
        publish = os.environ.get('LISTING_MANUAL_PUBLISH') == 'true'
    verify_only = req.get('verify_only', False)
    assert type(verify_only) is bool
    assert not (publish and verify_only), 'Verification-only requests cannot publish'
    if publish:
        workflow = (ROOT / '.github/workflows/wordpress-listing.yml').read_text()
        assert re.search(r'^  cancel-in-progress: false$', workflow, re.M), 'Restore non-cancelling concurrency before publication'
    if os.environ.get('GITHUB_OUTPUT'):
        with open(os.environ['GITHUB_OUTPUT'], 'a') as f:
            f.write(f'publish={str(publish).lower()}\n')
            f.write(f'verify_only={str(verify_only).lower()}\n')
    print('Prepared publication:', publish, '(false means credential-free dry-run only)')


def verify(state):
    expected = json.loads((state / 'expected.json').read_text())
    wc = state / 'published'
    checkout(wc)
    for sub in ['trunk', 'tags/1.3.1']:
        verify_runtime(wc / sub, {expected['readme_sha256']})
    assert hashes(wc / 'assets') == expected['assets'], 'Published asset hashes differ'
    properties = ET.fromstring(run('svn', 'proplist', '--xml', '--verbose', '--recursive', str(wc / 'assets')))
    mime = {Path(t.attrib['path']).name: t.findtext("property[@name='svn:mime-type']") for t in properties.findall('target')}
    for name in json.loads(REQUEST.read_text())['assets']:
        assert mime.get(name) == 'image/png', f'Incorrect PNG MIME property: {name}'
    assert run('svn', 'list', '--non-interactive', URL + '/tags/') == expected['tags'], 'SVN tag list changed'
    print('VERIFIED SVN PUBLICATION: readme and asset hashes match; all 11 non-readme package files in trunk/stable tag unchanged; no new SVN tag.')
    print(run('svn', 'log', '-l', '1', URL))


def public(state):
    # Public/API/CDN propagation is independent of a verified SVN commit.
    api = 'https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request%5Bslug%5D=' + SLUG + '&request%5Bfields%5D%5Bbanners%5D=1&request%5Bfields%5D%5Bicons%5D=1&request%5Bfields%5D%5Bscreenshots%5D=1'
    request = urllib.request.Request(api, headers={'User-Agent': 'Tavoos-listing-verification/1.0'})
    try:
        with urllib.request.urlopen(request, timeout=30) as response:
            data = json.load(response)
        result = {k: data.get(k) for k in ['slug','version','last_updated','banners','icons','screenshots']}
        result['section_names'] = list(data.get('sections',{}))
        result['description_matches'] = '18 selectable preset icons' in data.get('sections',{}).get('description','')
        print('PUBLIC API:', json.dumps(result, ensure_ascii=False, sort_keys=True))
        assert data.get('version') == VERSION, 'Public version differs; investigate'
        (state / 'public-api.json').write_text(json.dumps(result, ensure_ascii=False, indent=2))
        with urllib.request.urlopen('https://wordpress.org/plugins/' + SLUG + '/', timeout=30) as response:
            page = response.read().decode()
        print('PUBLIC PAGE:', json.dumps({'updated_description': '18 selectable preset icons' in page, 'has_screenshot_13': 'screenshot-13' in page, 'has_banner': 'banner-772x250' in page or 'banner-1544x500' in page}))
        expected = json.loads((state / 'expected.json').read_text())
        checks = []
        for filename in ['banner-772x250.png','banner-1544x500.png','icon-128x128.png','icon-256x256.png','screenshot-1.png','screenshot-13.png']:
            url = f'https://ps.w.org/{SLUG}/assets/{filename}'
            try:
                with urllib.request.urlopen(url, timeout=30) as response:
                    digest = hashlib.sha256(response.read()).hexdigest()
                checks.append({'file': filename, 'url': url, 'matches': digest == expected['assets'][filename]})
            except Exception as error:
                checks.append({'file': filename, 'url': url, 'error': str(error)})
        print('PUBLIC CDN:', json.dumps(checks))
        if not result['description_matches'] or not all(c.get('matches') for c in checks):
            print('PUBLIC CACHE PENDING/UNVERIFIED: SVN verification is authoritative for deployment; public propagation needs recheck.')
    except Exception as error:
        print('PUBLIC VERIFICATION INCOMPLETE:', type(error).__name__, str(error))
        raise


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('command', choices=['source', 'prepare', 'verify', 'public'])
    parser.add_argument('--state', type=Path, default=Path('/tmp/tavoos-listing-state'))
    args = parser.parse_args()
    if args.command == 'source':
        validate_source()
    else:
        globals()[args.command](args.state.resolve())
