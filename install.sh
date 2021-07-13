#!/bin/zsh

TEMP=./helloclair
DEST=/usr/local/bin
ZIPFILE='./helloclair.zip'
ZIPPATH='https://clair-assets.s3.amazonaws.com/helloclair.zip'

declare -a SCRIPTS
SCRIPTS=('builds/clair::clair' 'helloclair::helloclair')

# Create the tmp local folder
[ ! -d "$TEMP" ] && mkdir -p "$TEMP"

# Download and unzip the zip file
curl "$ZIPPATH" -o "$ZIPFILE"
unzip -o "$ZIPFILE" -d "$TEMP"

# Move the files and update permissions
for index in "${SCRIPTS[@]}";
do
  SRC="${index%%::*}"
  SCRIPT="${index##*::}"
  mv "$TEMP/$SRC" "$DEST/${SCRIPT}"
  chmod a+x "$DEST/${SCRIPT}"
done

# Cleanup
rm "$ZIPFILE"
rm -r "$TEMP"
