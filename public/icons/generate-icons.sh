#!/bin/bash
# Generate PWA icons from source SVG
# Requires: ImageMagick (convert command)
# Usage: ./generate-icons.sh

SIZES=(72 96 128 144 152 192 384 512)
SOURCE="placeholder.svg"

for size in "${SIZES[@]}"; do
  convert "$SOURCE" -resize ${size}x${size} "icon-${size}x${size}.png"
  echo "Generated icon-${size}x${size}.png"
done

# Generate Apple Touch Icon
convert "$SOURCE" -resize 180x180 "apple-touch-icon-180x180.png"

# Generate shortcut icons
convert "$SOURCE" -resize 96x96 "shortcut-inventory.png"
convert "$SOURCE" -resize 96x96 "shortcut-inbounds.png"
convert "$SOURCE" -resize 96x96 "shortcut-shipments.png"

echo "All icons generated!"
