#!/bin/bash
#
# This is an example script to add in your crontab.
# Edit it to fit your needs (variables, uncomment, extend...)
#

APP_PATH="${APP_PATH:-/usr/src/sushiReport}"
WEB_PATH="${WEB_PATH:-/var/www/html/counter}"

cd "${APP_PATH}"
php sushiReport.php config/config-JR1.json yesterday >> ${WEB_PATH}/service-JR1.csv
php sushiReport.php config/config-AR1.json yesterday >> ${WEB_PATH}/service-AR1.csv

# It could also be called outside the container as follows:
# docker run --rm -v "${APP_PATH}/config/config-JR1.json:${APP_PATH}/config.json" -it sushi-report:latest >> ${WEB_PATH}/service-JR.csv
# docker run --rm -v "${APP_PATH}/config/config-AR1.json:${APP_PATH}/config.json" -it sushi-report:latest >> ${WEB_PATH}/service-AR.csv
