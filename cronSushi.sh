#!/bin/bash
#
# This is an example script to add in your crontab.
# Edit it to fit your needs (variables, uncomment, extend...)
#

APP_PATH="${APP_PATH:-/usr/src/sushiReport}"
OUT_PATH="${WEB_PATH:-$APP_PATH}"

cd "${APP_PATH}"

# Example 1: Stdout is redirected to a file. 
# If its runned in a container, you will need to create a volume to recover this results file.
php sushiReport.php config/config-JR1.json >> ${OUT_PATH}/results-JR1.csv

# Example 2: Config requests to store output in results_file:
# If its runned in a container, you will need to create a volume to recover this results file.
php sushiReport.php config/config-AR1.json

# It could also be called outside the container as follows:
# docker run --rm -v "${APP_PATH}/config/config-JR1.json:${APP_PATH}/config.json" -it sushi-report:latest >> ${WEB_PATH}/service-JR.csv
# docker run --rm -v "${APP_PATH}/config/config-AR1.json:${APP_PATH}/config.json" -it sushi-report:latest >> ${WEB_PATH}/service-AR.csv
