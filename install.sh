#!/bin/bash

TEMP=./helloclair
DEST=/usr/local/bin
ZIPFILE='helloclair.zip'
ZIPPATH='https://clair-assets.s3.amazonaws.com/helloclair.zip'

declare -a SCRIPTS
SCRIPTS=('builds/clair::clair' 'helloclair::helloclair')

abort() {
  printf "%s\n" "$@"
  exit 1
}

shell_join() {
  local arg
  printf "%s" "$1"
  shift
  for arg in "$@"; do
    printf " "
    printf "%s" "${arg// /\ }"
  done
}

execute() {
  if ! "$@"; then
    abort "$(printf "Failed during: %s" "$(shell_join "$@")")"
  fi
}

# Create the tmp local folder
rm - "$TEMP"
mkdir -p "$TEMP"

# Download and unzip the zip file
execute "curl" "$ZIPPATH" "-o" "./$ZIPFILE"
unzip -o "./$ZIPFILE" -d "$TEMP"

# Move the files and update permissions
for index in "${SCRIPTS[@]}";
do
  SRC="${index%%::*}"
  SCRIPT="${index##*::}"
  mv "$TEMP/$SRC" "$DEST/${SCRIPT}"
  execute "chmod" "a+x" "$DEST/${SCRIPT}"
done

# Cleanup
rm "$ZIPFILE"
rm -r "$TEMP"