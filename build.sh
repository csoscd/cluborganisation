#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
OUTPUT_DIR="$ROOT_DIR/dist"
PACKAGE_NAME="com_cluborganisation"
MANIFEST_SOURCE="$ROOT_DIR/administrator/components/com_cluborganisation/cluborganisation.xml"

mkdir -p "$OUTPUT_DIR"

BUILD_DIR="$(mktemp -d)"
trap 'rm -rf "$BUILD_DIR"' EXIT

cp "$MANIFEST_SOURCE" "$BUILD_DIR/${PACKAGE_NAME}.xml"
cp -R "$ROOT_DIR/administrator" "$BUILD_DIR/administrator"
cp -R "$ROOT_DIR/components" "$BUILD_DIR/components"

(
  cd "$BUILD_DIR"
  zip -r "$OUTPUT_DIR/${PACKAGE_NAME}.zip" \
    "${PACKAGE_NAME}.xml" \
    administrator/components/com_cluborganisation \
    components/com_cluborganisation \
    -x "*.DS_Store" "*/.gitkeep" "*/.gitignore"
)

echo "Created $OUTPUT_DIR/${PACKAGE_NAME}.zip"
