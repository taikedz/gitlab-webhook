#!/bin/bash

# Default user on Ubuntu
# Change to differnt ID, or username
user='#1000'
update_script='update.sh'

path="$1"; shift

cd "$path"
sudo -u "$user" git pull

if [[ -f "$update_script" ]]; then
    sudo -u "$user" "./$update_script"
fi
