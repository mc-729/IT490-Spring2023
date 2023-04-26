

# Configuration
REMOTE_USER="jonathan"
REMOTE_PATH="/home/jonathan/git/IT490-Spring2023/authentication"
LOCAL_PATH="/home/it490/git/IT490-Spring2023/deployment/package_repo"
ZIP_NAME="archive.zip"
REMOTE_HOST="192.168.191.15"
# Zip files on the remote server
ssh "${REMOTE_USER}@${REMOTE_HOST}" "cd ${REMOTE_PATH} && zip -r ${ZIP_NAME} ."

# Rsync the zipped file to the local machine
rsync -avzP --remove-source-files "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/${ZIP_NAME}" "${LOCAL_PATH}/${ZIP_NAME}"
