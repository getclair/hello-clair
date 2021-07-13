#!/bin/bash

ZIPFILE='hello-clair-main.zip'
ZIPPATH='https://clair-assets.s3.amazonaws.com/hello-clair-main.zip'
UNZIPPATH='hello-clair-main'
DEST=/usr/local/bin

declare -a SCRIPTS
SCRIPTS=('clair' 'helloclair')

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

# Download and unzip the zip file
execute "curl" "$ZIPPATH" "-o" "./$ZIPFILE"
unzip -o "./$ZIPFILE"

# Move the files and update permissions
for script in "${SCRIPTS[@]}";
do
  mv "./$UNZIPPATH/$script" "$DEST/"
  execute "chmod" "a+x" "$DEST/${script}"
done

# Cleanup
rm "./$ZIPFILE"
rm -r "./$UNZIPPATH"

