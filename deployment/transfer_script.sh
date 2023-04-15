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

# Control VM folder to save zip
CONTROL_VM_ZIP_DIR="./saved_zips"

# Create the folder to save zip on control VM if it doesn't exist
mkdir -p $CONTROL_VM_ZIP_DIR

# SSH into sender VM, zip files, transfer files to receiver VM, save files on control VM, and unzip files on the receiver VM
echo "Running commands on sender VM and transferring files to receiver VM and control VM..."
ssh -t $SENDER_USER@$SENDER_HOST << EOF
  # Zip files
  echo "Zipping files..."
  cd $SOURCE_DIR && zip -r $ZIP_NAME *

  # Transfer zipped file using SSH keys to the receiver VM
  echo "Transferring files to receiver VM..."
  scp $SOURCE_DIR/$ZIP_NAME $RECEIVER_USER@$RECEIVER_HOST:$RECEIVER_DIR

  # Transfer zipped file using SSH keys to the control VM
  echo "Transferring files to control VM..."
  scp $SOURCE_DIR/$ZIP_NAME $USER@localhost:$CONTROL_VM_ZIP_DIR

  # Unzip files on remote server
  echo "Unzipping files on remote server..."
  ssh $RECEIVER_USER@$RECEIVER_HOST "cd $RECEIVER_DIR && unzip -o $ZIP_NAME -d $RECEIVER_FOLDER && rm $ZIP_NAME"

  # Cleanup: Remove zip file on sender VM
  rm $SOURCE_DIR/$ZIP_NAME

  echo "File transfer complete."
EOF