#!/bin/bash

# Default user on Ubuntu
# Change to differnt ID, or username
user='#1000'

path="$1"; shift

cd "$path"
sudo -u "$user" git pull
