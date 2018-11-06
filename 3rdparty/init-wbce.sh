#!/usr/bin/env bash
#
# Copyright 2010-2018 OpenEstate.org
#

URL="https://github.com/WBCE/WBCE_CMS/archive/1.3.2.tar.gz"

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TEMP_DIR="$DIR/temp"
WBCE_DIR="$DIR/wbce"
set -e

echo ""
echo "Downloading latest version of WBCE..."
mkdir -p "$TEMP_DIR"
rm -Rf "$TEMP_DIR/wbce.tar.gz"
curl -L \
  -o "$TEMP_DIR/wbce.tar.gz" \
  "$URL"
if [ ! -f "$TEMP_DIR/wbce.tar.gz" ]; then
    echo "ERROR: WBCE was not properly downloaded!"
    exit 1
fi

echo ""
echo "Extracting WBCE..."
rm -Rf "$WBCE_DIR"
rm -Rf "$TEMP_DIR/wbce"
mkdir -p "$TEMP_DIR/wbce"
cd "$TEMP_DIR/wbce"
tar xfz "$TEMP_DIR/wbce.tar.gz"
mv "$(ls -1)/wbce" "$WBCE_DIR"
rm -Rf "$TEMP_DIR/wbce"

echo ""
echo "WBCE was successfully extracted!"
echo "to: $WBCE_DIR"
