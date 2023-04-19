#!/bin/bash

# Configuration
REMOTE_USER="jonathan"
REMOTE_PATH="/var/www/MyLiqourCabinet/application"
LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo"
ZIP_NAME="dev_frontend_1.15.zip"
REMOTE_HOST="192.168.191.15"
EXCLUDE_OPTION=" -x 'rabbitMQ/*'"
# Zip files on the remote server
ssh "${REMOTE_USER}@${REMOTE_HOST}" "cd ${REMOTE_PATH} && zip -r ${ZIP_NAME} . --exclude *.ini${EXCLUDE_OPTION}"



# Rsync the zipped file to the local machine
rsync -avzP --remove-source-files "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/${ZIP_NAME}" "${LOCAL_PATH}/${ZIP_NAME}"

