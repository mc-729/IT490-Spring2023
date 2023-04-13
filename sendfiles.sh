#!/bin/bash

# Change these variables to match your setup
SOURCE_DIR="/home/jonathan/git/IT490-Spring2023/authentication"
ZIP_NAME="files.zip"
DEST_USER="jonathan"
DEST_HOST="192.168.191.172"
DEST_FOLDER="authentication"
DEST_DIR="/home/jonathan/git/IT490-Spring2023"

# Zip files
echo "Zipping files..."
cd $SOURCE_DIR && zip -r $ZIP_NAME *

# Transfer zipped file using SSH keys
echo "Transferring files..."
scp $SOURCE_DIR/$ZIP_NAME $DEST_USER@$DEST_HOST:$DEST_DIR

# Unzip files on remote server
echo "Unzipping files on remote server..."
ssh $DEST_USER@$DEST_HOST "cd $DEST_DIR && unzip -o $ZIP_NAME -d $DEST_FOLDER && rm $ZIP_NAME"

echo "File transfer complete."
