#!/bin/bash

# Change these variables to match your setup
ZIP_NAME="files.zip"
DEST_DIR="/home/jonathan/git/IT490-Spring2023

# Unzip and unpack files
echo "Unpacking files..."
unzip -o $DEST_DIR/$ZIP_NAME -d $DEST_DIR

# Cleanup
echo "Cleaning up..."
rm $DEST_DIR/$ZIP_NAME

echo "Files unpacked successfully."
