# Release 1.3.1

Production files are fixed by `.github/releases/1.3.1.json`. The source patch was
reviewed against `4d2a9c6e75ad20ee49e1ac899abb118677dc0193`. The original kit's
ZIP hash is provenance; a rebuilt ZIP can have different archive metadata.
Verify all 12 file hashes rather than claiming a recreated ZIP has that hash.

Before publishing, run `bash tools/run-wordpress-tests.sh`, inspect the source
and workflow changes, and commit the reviewed code. Then run
`python3 tools/verify-wordpress-release.py build /tmp/tavoos-build` with a new,
nonexistent destination. Development files are excluded by `.gitattributes`.

The WordPress workflow is intentionally restricted to 1.3.1 on
`claude/trusting-thompson-ymhjs8`. A push changing the release manifest triggers
credential-free regression tests and an SVN staging dry-run. Only after those
pass does it create immutable Git tag `1.3.1` and deploy that exact commit.
Manual dispatch defaults to dry-run. An explicit `dry_run=false` dispatch also
publishes. Pull requests and other refs do not enter the deployment jobs.
Only the deployment action receives repository secrets `SVN_USERNAME` and
`SVN_PASSWORD`; only the tag job has GitHub Contents write permission.

The initial-release guard accepts empty SVN trunk/tags or an already published
identical 1.3.1. It refuses different content and never moves an existing Git
tag. Check committed SVN trunk and tags/1.3.1, the WordPress plugin information
API, and every file in the public download ZIP. An SVN commit alone does not
prove the directory is public. If WordPress requires release confirmation,
the WordPress account owner must complete it in the plugin release dashboard.

GitHub's separate `release.yml` is triggered by `v*` tags. The bare `1.3.1` tag
created with GITHUB_TOKEN does not trigger that workflow. After WordPress
publication is verified, push `v1.3.1` at the same reviewed commit using a
supported writable connection to generate the GitHub release and install ZIP.
Never move an existing tag.

## Later versions

Pushing `v1.3.2` by itself builds only a GitHub release. This WordPress workflow
and verifier must not be reused unchanged for 1.3.2. For each later WordPress
release, review version/Stable tag/changelog together, generate a new approved
file manifest, update the version-specific workflow/verifier, and replace the
empty-SVN initial-release guard with a guard that permits the reviewed existing
history while refusing to overwrite the target SVN tag. Run tests and an
explicit staging dry-run before publishing the new immutable version. Keep the
branch/ref checks, pinned actions, credential separation and post-publish checks.

## Validation in this cloud session

- 20 WordPress API integration checks passed on WordPress 6.9.4 / PHP 8.3.31
  with MariaDB 11.4, including backslashes, migration, and uninstall ownership.
- The same 20 checks passed on WordPress 5.6.2 / PHP 7.4.16.
- All six production PHP files parsed on PHP 8.3.31 and PHP 7.2.34; both JS files
  parsed with Node. PHP 7.2 runtime integration was not tested.
- Frontend HTTP/asset checks and authenticated settings access passed.
- Eleven Chromium checks passed for desktop/mobile visibility, menu toggle,
  Escape, JavaScript errors, and authenticated admin; screenshots were reviewed.
- All 12 package files matched the kit manifest. Negative verification checks
  rejected modified, unexpected, missing files and symlinks.
- The kit's earlier Plugin Check / 65 PHP / 7 HTTP / 10 DOM results are historical
  evidence, not checks rerun by this script. MySQL and multisite are separate
  checks and must not be inferred from the MariaDB single-site results.
