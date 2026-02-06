#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_DIR="$ROOT_DIR/dist"
PACKAGE_NAME="com_cluborganisation"

mkdir -p "$OUTPUT_DIR"

zip -r "$OUTPUT_DIR/${PACKAGE_NAME}.zip" \
  administrator/components/com_cluborganisation \
  components/com_cluborganisation \
  -x "*.DS_Store" "*/.gitkeep" "*/.gitignore"

echo "Created $OUTPUT_DIR/${PACKAGE_NAME}.zip"
