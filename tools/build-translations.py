#!/usr/bin/env python3
"""Rebuild the translation template and the Persian translation.

    python3 tools/build-translations.py

- Extracts every translatable string from the plugin's PHP files (and the plugin
  header) into translations/tavoos-floating-call-button.pot for review. The frozen
  languages/ template is only updated during a separately approved release.
- Rewrites translations/tavoos-floating-call-button-fa_IR.po from the new
  template, keeping existing translations, and compiles the matching .mo.
  Those files are not part of the plugin package; import the .po on
  translate.wordpress.org.

Untranslated strings are listed at the end; fill them in the .po and run again.
"""
import datetime
import os
import re
import struct
import sys

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DOMAIN = 'tavoos-floating-call-button'
MAIN = os.path.join(ROOT, DOMAIN + '.php')
# Keep the reviewed 1.3.1 package frozen; stage extraction outside the release.
POT = os.path.join(ROOT, 'translations', DOMAIN + '.pot')
LANG = 'fa_IR'
PO = os.path.join(ROOT, 'translations', '%s-%s.po' % (DOMAIN, LANG))
MO = os.path.join(ROOT, 'translations', '%s-%s.mo' % (DOMAIN, LANG))
PLURAL = 'nplurals=2; plural=(n > 1);'
SKIP_DIRS = {'.git', '.github', 'docs', 'tools', 'translations', 'node_modules'}
HEADERS = ('Plugin Name', 'Plugin URI', 'Description', 'Author', 'Author URI')

CALL = re.compile(
    r"(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'"
    + re.escape(DOMAIN) + r"'\s*\)"
)
TRANSLATORS = re.compile(r"/\*\s*translators:(.*?)\*/", re.S)


def version():
    m = re.search(r'^\s*\*\s*Version:\s*(\S+)', open(MAIN, encoding='utf-8').read(), re.M)
    return m.group(1) if m else ''


def extract():
    order, entries = [], {}

    def add(msgid, ref, comment):
        if msgid not in entries:
            entries[msgid] = {'refs': [], 'comment': None}
            order.append(msgid)
        if ref:
            entries[msgid]['refs'].append(ref)
        if comment and not entries[msgid]['comment']:
            entries[msgid]['comment'] = comment

    for dirpath, dirnames, files in os.walk(ROOT):
        dirnames[:] = sorted(d for d in dirnames if d not in SKIP_DIRS)
        for name in sorted(files):
            if not name.endswith('.php'):
                continue
            path = os.path.join(dirpath, name)
            rel = os.path.relpath(path, ROOT)
            src = open(path, encoding='utf-8').read()
            for m in CALL.finditer(src):
                msgid = m.group(1).replace("\\'", "'").replace('\\\\', '\\')
                line = src.count('\n', 0, m.start()) + 1
                before = '\n'.join(src[:m.start()].split('\n')[-4:])
                c = TRANSLATORS.search(before)
                add(msgid, '%s:%d' % (rel, line), 'translators: ' + c.group(1).strip() if c else None)

    main = open(MAIN, encoding='utf-8').read()
    for header in HEADERS:
        m = re.search(r'^\s*\*\s*' + header + r':\s*(.+)$', main, re.M)
        if m:
            add(m.group(1).strip(), None, header + ' of the plugin')
    return order, entries


def read_po(path):
    """msgid -> msgstr from an existing .po (single-line and multi-line strings)."""
    if not os.path.exists(path):
        return {}
    out, key, cur, field = {}, None, None, None

    escapes = {'n': '\n', 't': '\t', '"': '"', '\\': '\\'}

    def unq(s):
        return re.sub(r'\\(.)', lambda m: escapes.get(m.group(1), m.group(1)), s.strip()[1:-1])

    for raw in open(path, encoding='utf-8'):
        line = raw.strip()
        if line.startswith('msgid '):
            if key is not None and cur is not None:
                out[key] = cur
            key, cur, field = unq(line[6:]), None, 'id'
        elif line.startswith('msgstr '):
            cur, field = unq(line[7:]), 'str'
        elif line.startswith('"'):
            if field == 'id':
                key += unq(line)
            elif field == 'str':
                cur += unq(line)
    if key is not None and cur is not None:
        out[key] = cur
    out.pop('', None)
    return out


def quote(s):
    return '"' + s.replace('\\', '\\\\').replace('"', '\\"').replace('\n', '\\n') + '"'


def write_po(path, order, entries, translations, is_pot):
    now = datetime.datetime.now(datetime.timezone.utc).strftime('%Y-%m-%d %H:%M+0000')
    head = {
        'Project-Id-Version': 'Tavoos Floating Call Button ' + version(),
        'Report-Msgid-Bugs-To': 'https://github.com/mmhdih/Tavoos-Floating-Call-Button/issues',
        'POT-Creation-Date': now,
        'PO-Revision-Date': 'YEAR-MO-DA HO:MI+ZONE' if is_pot else now,
        'Last-Translator': '' if is_pot else 'Repository contributors (automated update; human review pending)',
        'Language-Team': '' if is_pot else 'Persian (Iran)',
        'Language': '' if is_pot else LANG,
        'MIME-Version': '1.0',
        'Content-Type': 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding': '8bit',
        'X-Domain': DOMAIN,
    }
    if not is_pot:
        head['Plural-Forms'] = PLURAL
    lines = [
        '# Copyright (C) %d Mahdi Habibi | Tavoos Web' % datetime.date.today().year,
        '# This file is distributed under the GPLv2 or later.',
        'msgid ""',
        'msgstr ""',
    ] + [quote('%s: %s\n' % kv) for kv in head.items()]
    for msgid in order:
        e = entries[msgid]
        lines.append('')
        if e['comment']:
            lines.append('#. ' + e['comment'])
        if e['refs']:
            lines.append('#: ' + ' '.join(e['refs']))
        if re.search(r'%(\d+\$)?[sd]', msgid):
            lines.append('#, php-format')
        lines.append('msgid ' + quote(msgid))
        lines.append('msgstr ' + quote('' if is_pot else translations.get(msgid, '')))
    os.makedirs(os.path.dirname(path), exist_ok=True)
    open(path, 'w', encoding='utf-8').write('\n'.join(lines) + '\n')
    return head


def write_mo(path, head, order, translations):
    msgs = {'': ''.join('%s: %s\n' % kv for kv in head.items())}
    for msgid in order:
        if translations.get(msgid):
            msgs[msgid] = translations[msgid]
    keys = sorted(msgs)
    ids, strs, index = b'', b'', []
    for k in keys:
        kb, vb = k.encode('utf-8'), msgs[k].encode('utf-8')
        index.append((len(ids), len(kb), len(strs), len(vb)))
        ids += kb + b'\0'
        strs += vb + b'\0'
    n = len(keys)
    key_start = 28 + 16 * n
    val_start = key_start + len(ids)
    koff, voff = [], []
    for io, il, so, sl in index:
        koff += [il, io + key_start]
        voff += [sl, so + val_start]
    data = struct.pack('Iiiiiii', 0x950412DE, 0, n, 28, 28 + 8 * n, 0, 0)
    data += struct.pack('%di' % len(koff), *koff) + struct.pack('%di' % len(voff), *voff) + ids + strs
    open(path, 'wb').write(data)


def main():
    order, entries = extract()
    translations = read_po(PO)
    write_po(POT, order, entries, {}, True)
    head = write_po(PO, order, entries, translations, False)
    write_mo(MO, head, order, translations)
    missing = [s for s in order if not translations.get(s) ]
    print('%d strings, %d translated -> %s' % (len(order), len(order) - len(missing), os.path.relpath(PO, ROOT)))
    if missing:
        print('Untranslated:')
        for s in missing:
            print('  ' + s)
    return 1 if missing else 0


if __name__ == '__main__':
    sys.exit(main())
