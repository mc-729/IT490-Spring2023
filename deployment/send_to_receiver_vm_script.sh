#!/bin/bash

# Configuration

LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo"
ZIP_NAME="dev_DB_1.22.zip"
RECEIVER_USER="jonathan"
RECEIVER_HOST="192.168.191.172"
RECEIVER_FOLDER="authentication"
RECEIVER_DIR="/home/jonathan/git/IT490-Spring2023/"

# Transfer zipped file using Rsync to the receiver VM
echo "Transferring files to receiver VM..."
rsync -avzP "${LOCAL_PATH}/${ZIP_NAME}" "${RECEIVER_USER}@${RECEIVER_HOST}:${RECEIVER_DIR}"

# Unzip files on remote server
echo "Unzipping files on remote server..."
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "cd ${RECEIVER_DIR} && unzip -o ${ZIP_NAME} -d ${RECEIVER_FOLDER} && rm ${ZIP_NAME}"

echo "File transfer and unzip complete."
