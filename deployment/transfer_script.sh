#!/bin/bash

# Sender VM variables
SENDER_USER="jonathan"
SENDER_HOST="192.168.191.15"
SOURCE_DIR="/home/jonathan/git/IT490-Spring2023/authentication"
ZIP_NAME="files.zip"

# Receiver VM variables
RECEIVER_USER="jonathan"
RECEIVER_HOST="192.168.191.172"
RECEIVER_FOLDER="authentication"
RECEIVER_DIR="/home/jonathan/git/IT490-Spring2023"

# SSH into sender VM, zip files, transfer files to receiver VM, and unzip files on the receiver VM
echo "Running commands on sender VM and transferring files to receiver VM..."
ssh -t $SENDER_USER@$SENDER_HOST << EOF
  # Zip files
  echo "Zipping files..."
  cd $SOURCE_DIR && zip -r $ZIP_NAME *

  # Transfer zipped file using SSH keys
  echo "Transferring files..."
  scp $SOURCE_DIR/$ZIP_NAME $RECEIVER_USER@$RECEIVER_HOST:$RECEIVER_DIR

  # Unzip files on remote server
  echo "Unzipping files on remote server..."
  ssh $RECEIVER_USER@$RECEIVER_HOST "cd $RECEIVER_DIR && unzip -o $ZIP_NAME -d $RECEIVER_FOLDER && rm $ZIP_NAME"

  # Cleanup: Remove zip file on sender VM
  rm $SOURCE_DIR/$ZIP_NAME

  echo "File transfer complete."
EOF