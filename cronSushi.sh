#!/bin/bash
#
# This is an example script to add in your crontab.
# It will run a dockerized sushiReport container and will store results in OUT_PATH.
#
# Edit it to fit your needs (variables, uncomment, extend...) or use environtment variables.
#
# IMPORTANT: If you use the dockerize version, notice some paths are relative to the container.
# Best way to overwrite those files is using volumes, as in example 3.
# 
# If you prefer, you can call the script setting environment variables in your cron, as follows:
# export CFG_PATH="/home/docker/services/sushiReport"; export OUT_PATH=$CFG_PATH; ./cronSushi.sh
#

APP_PATH="${APP_PATH:-/usr/src/sushiReport}"
CFG_PATH="${CFG_PATH:-/usr/src/sushiReport/config}"
OUT_PATH="${OUT_PATH:-$APP_PATH}"
APP_IMAGE="${APP_IMAGE:-marcbria/sushi-report:latest}"

cd "${APP_PATH}"

echo "Running cronSushi.sh with:"
echo "- APP_PATH:  ${APP_PATH}"
echo "- CFG_PATH:  ${CFG_PATH}"
echo "- OUT_PATH:  ${OUT_PATH}"
echo "- APP_IMAGE: ${APP_IMAGE}"
echo ""

# Example 1: Stdout is redirected to a file. 
# If its runned in a container, you will need to create a volume to recover this results file.
## php sushiReport.php config/config-JR1.json >> ${OUT_PATH}/results-JR1.csv

# Example 2: Config requests to store output in results_file:
# If its runned in a container, you will need to create a volume to recover this results file.
## php sushiReport.php config/config-AR1.json

# Example 3: Calling outside 
# It could also be called outside the container as follows:
docker run --rm -v "${CFG_PATH}/config-JR1.json:${APP_PATH}/config.json" -it ${APP_IMAGE} >> ${OUT_PATH}/service-JR.csv
docker run --rm -v "${CFG_PATH}/config-AR1.json:${APP_PATH}/config.json" -it ${APP_IMAGE} >> ${OUT_PATH}/service-AR.csv
