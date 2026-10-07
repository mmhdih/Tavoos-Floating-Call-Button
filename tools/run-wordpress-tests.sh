#!/bin/bash
set -euo pipefail
export TAVOOS_SOURCE
TAVOOS_SOURCE=$(cd "$(dirname "$0")/.." && pwd)
export TAVOOS_TEST_PASSWORD
TAVOOS_TEST_PASSWORD=$(python3 -c 'import secrets; print(secrets.token_hex(24))')
project="tavoos-release-test-${GITHUB_RUN_ID:-$$}"
compose=(docker compose -p "$project" -f "$TAVOOS_SOURCE/tools/wordpress-tests.compose.yml")
cleanup() { "${compose[@]}" down --volumes >/dev/null; }
trap cleanup EXIT
"${compose[@]}" up -d --wait --wait-timeout 120
for attempt in {1..60}; do
  if "${compose[@]}" exec -T wordpress test -f /var/www/html/wp-config.php; then break; fi
  sleep 1
done
site_url="http://$("${compose[@]}" port wordpress 80)"
"${compose[@]}" exec -T -e "DEV_SITE_URL=$site_url" wordpress php wp-content/plugins/tavoos-floating-call-button/tools/bootstrap-wordpress-tests.php
"${compose[@]}" exec -T wordpress sh -c 'cd wp-content/plugins/tavoos-floating-call-button; for file in tavoos-floating-call-button.php uninstall.php includes/*.php; do php -l "$file" || exit; done'
"${compose[@]}" exec -T wordpress php wp-content/plugins/tavoos-floating-call-button/tools/test-wordpress-release.php
node --check "$TAVOOS_SOURCE/assets/js/admin.js"
node --check "$TAVOOS_SOURCE/assets/js/frontend.js"
python3 - "$site_url" <<'PY'
import sys
from urllib.request import urlopen
base = sys.argv[1]
html = urlopen(base, timeout=30).read().decode()
assert 'id="tavoos-fcb"' in html
assert 'tel:+15551234567' in html
assert 'assets/css/frontend.css' in html and 'assets/js/frontend.js' in html
for asset in ['css/frontend.css', 'js/frontend.js']:
    assert len(urlopen(base + '/wp-content/plugins/tavoos-floating-call-button/assets/' + asset, timeout=30).read()) > 100
print('PASS: HTTP button, phone URL and both frontend assets')
PY
