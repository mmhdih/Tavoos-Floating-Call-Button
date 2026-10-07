#!/usr/bin/env python3
"""Check the current singular gettext catalog without changing release files."""
import gettext
import importlib.util
from pathlib import Path
import re

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location('translations', ROOT / 'tools/build-translations.py')
builder = importlib.util.module_from_spec(spec)
spec.loader.exec_module(builder)
order, _ = builder.extract()
po = builder.read_po(builder.PO)
pot = builder.read_po(builder.POT)
assert set(order) == set(po) == set(pot), 'Extracted source and catalog keys differ'
assert all(po.values()), 'Empty translations'
assert len(order) == len(po), 'Duplicate source entries'
source = Path(builder.PO).read_text()
assert '#, fuzzy' not in source, 'Fuzzy translations require review'
fmt = re.compile(r'%(?:\d+\$)?[sd]')
for key, value in po.items():
    assert sorted(fmt.findall(key)) == sorted(fmt.findall(value)), f'Placeholder mismatch: {key}'
with open(builder.MO, 'rb') as stream:
    compiled = gettext.GNUTranslations(stream)
assert compiled.info()['x-domain'] == builder.DOMAIN
assert compiled.info()['language'] == 'fa_IR'
assert compiled.info()['content-type'] == 'text/plain; charset=UTF-8'
for key, value in po.items():
    assert compiled.gettext(key) == value, f'MO mismatch: {key}'
assert compiled.plural(0) == 0 and compiled.plural(1) == 0 and compiled.plural(2) == 1
print(f'PASS: {len(order)}/{len(order)} entries; source/POT/PO/MO agreement, format placeholders, metadata, Persian plural rule.')
