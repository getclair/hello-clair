#!/bin/bash

DEST=./test

declare -a scripts
scripts['clair']='https://github.com/getclair/hello-clair/blob/main/builds/clair?raw=true'
scripts['helloclair']=https://raw.githubusercontent.com/getclair/hello-clair/main/helloclair

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


for i in "${!scripts[@]}"
do
  echo $i
  echo ${scripts[$i]}
#  execute "curl" "${array[$i]}" "-o" "$DEST/$i"
#  execute "chmod" "a+x" "$DEST/$i"
done

