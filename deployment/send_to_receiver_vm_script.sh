#!/bin/bash

# Configuration

LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo"
ZIP_NAME="dev_frontend_1.03.zip"
RECEIVER_USER="jonathan"
RECEIVER_HOST="192.168.191.172"
RECEIVER_FOLDER="application"
RECEIVER_DIR="/var/www/MyLiqourCabinet"
RECEIVER_PASS="gorrin89"

# Transfer zipped file using Rsync to the receiver VM
echo "Transferring files to receiver VM..."
rsync -avzP "${LOCAL_PATH}/${ZIP_NAME}" "${RECEIVER_USER}@${RECEIVER_HOST}:${RECEIVER_DIR}"

# Unzip files on remote server
echo "Unzipping files on remote server..."
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "cd ${RECEIVER_DIR} && unzip -o ${ZIP_NAME} -d ${RECEIVER_FOLDER} && echo ${RECEIVER_PASS} | sudo -S rm ${ZIP_NAME} "

# Restart the service
echo "Restarting the service..."
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "echo ${RECEIVER_PASS} | sudo -S systemctl stop rabbitMQDatabase.service"
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "echo ${RECEIVER_PASS} | sudo -S systemctl start rabbitMQDatabase.service"
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "echo ${RECEIVER_PASS} | sudo -S systemctl stop rabbitMQAPI.service"
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "echo ${RECEIVER_PASS} | sudo -S systemctl start rabbitMQAPI.service"
ssh "${RECEIVER_USER}@${RECEIVER_HOST}" "echo ${RECEIVER_PASS} | sudo -S service apache2 restart "

echo "File transfer, unzip, and service restart complete."
