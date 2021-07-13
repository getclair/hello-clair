#!/bin/zsh

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
sudo [ ! -d "$TEMP" ] && sudo $_mkdir -p "$TEMP"

# Download and unzip the zip file
execute "sudo" "curl" "$ZIPPATH" "-o" "./$ZIPFILE"
execute "sudo" "unzip" "-o" "./$ZIPFILE" "-d" "$TEMP"

# Move the files and update permissions
for index in "${SCRIPTS[@]}";
do
  SRC="${index%%::*}"
  SCRIPT="${index##*::}"
  execute "sudo" "mv" "$TEMP/$SRC" "$DEST/${SCRIPT}"
  execute "sudo" "chmod" "a+x" "$DEST/${SCRIPT}"
done

# Cleanup
execute "sudo" "rm" "$ZIPFILE"
execute "sudo" "rm" "-r" "$TEMP"
