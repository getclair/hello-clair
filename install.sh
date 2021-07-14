#!/bin/zsh

DEST=/usr/local/bin
ZIPFILE='helloclair.zip'
ZIPPATH='https://clair-assets.s3.amazonaws.com/helloclair.zip'

declare -a SCRIPTS
SCRIPTS=('builds/clair::clair' 'helloclair::helloclair')

# Create the tmp local folder
TEMP=$(mktemp -d -t ci-$(date +%Y-%m-%d-%H-%M-%S)-clair)

# Download and unzip the zip file
curl "$ZIPPATH" -o "$TEMP/$ZIPFILE"
unzip -o "$TEMP/$ZIPFILE" -d "$TEMP"

# Move the files and update permissions
for index in "${SCRIPTS[@]}";
do
  SRC="${index%%::*}"
  SCRIPT="${index##*::}"
  mv "$TEMP/$SRC" "$DEST/${SCRIPT}"
  chmod a+x "$DEST/${SCRIPT}"
done

# Cleanup
rm -r "$TEMP"

# Exit
echo $?