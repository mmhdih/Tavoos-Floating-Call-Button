#!/usr/bin/env python3
"""Build or verify the exact reviewed WordPress.org 1.3.1 release."""
import hashlib
import io
import json
from pathlib import Path
import re
import subprocess
import sys
import tarfile

REPO = Path(__file__).resolve().parents[1]
RELEASE = json.loads((REPO / ".github/releases/1.3.1.json").read_text())
EXPECTED = RELEASE["files"]


def verify(root):
    files = {}
    for path in root.rglob("*"):
        if path.is_symlink():
            raise ValueError(f"Unexpected symlink: {path}")
        if path.is_file():
            files[path.relative_to(root).as_posix()] = hashlib.sha256(path.read_bytes()).hexdigest()
    if files != EXPECTED:
        missing = sorted(EXPECTED.keys() - files.keys())
        extra = sorted(files.keys() - EXPECTED.keys())
        changed = sorted(k for k in files.keys() & EXPECTED.keys() if files[k] != EXPECTED[k])
        raise ValueError(f"Release mismatch: missing={missing}, extra={extra}, changed={changed}")
    header = (root / "tavoos-floating-call-button.php").read_text()
    readme = (root / "readme.txt").read_text()
    version = re.search(r"^\s*\*\s*Version:\s*(\S+)", header, re.M)
    stable = re.search(r"^Stable tag:\s*(\S+)", readme, re.M)
    if not version or not stable or version[1] != "1.3.1" or stable[1] != "1.3.1":
        raise ValueError("Plugin version and Stable tag must both be 1.3.1")
    print(f"Verified all {len(files)} reviewed release files and version 1.3.1.")


def build(root):
    archive = subprocess.check_output(["git", "archive", "--format=tar", "HEAD"], cwd=REPO)
    with tarfile.open(fileobj=io.BytesIO(archive)) as tar:
        members = [m for m in tar.getmembers() if not m.isdir()]
        if {m.name for m in members} != EXPECTED.keys() or any(not m.isfile() for m in members):
            raise ValueError("Git archive contains unexpected paths or file types")
        root.mkdir(parents=True, exist_ok=False)
        for member in members:
            destination = root / member.name
            destination.parent.mkdir(parents=True, exist_ok=True)
            destination.write_bytes(tar.extractfile(member).read())
    verify(root)


if __name__ == "__main__":
    if len(sys.argv) != 3 or sys.argv[1] not in {"build", "verify"}:
        raise SystemExit("Usage: verify-wordpress-release.py build|verify DIRECTORY")
    (build if sys.argv[1] == "build" else verify)(Path(sys.argv[2]).resolve())
