# sushiReport
A small and high configurable harvester that collects COUNTER statistics from multiple OJS (sushi-lite plugin required) and returns data in CSV format. 

# Syntax
```
$ php sushiReport.php [<config_file.json>]
```

- ```config_file.json```: path to the config file. Default assumes ./config.json.

# Quickstart

1. Ensure you have docker [installed](https://docs.docker.com/get-docker) and running.
2. Create a myconfig.json with your preferences (see Config section).
```
$ wget https://raw.githubusercontent.com/marcbria/sushiReport/main/config.json -O myconfig.json && vim myconfig.json
```
3. Run the script as follows:
```
$ docker run --rm -v "myconfig.json:/usr/src/sushiReport/config.json" -i marcbria/sushi-report:latest
```
4. Got in trouble? Read the documentation...


# Config 

- ```xslt_filename```: name of the xsl transformations file.
- ```report```: sushi report type (JR1, AR1).
- ```release```: release number (ie: 4.1).
- ```results_file```: If is set, results will be appened to this file.
- ```silent```: If 'true', verbosity will be reduced to zero (only data and errors returned).
- ```timeout```: Overwrites php.ini timeout (default is 60).
- ```begin_date```: If is set, the starting period. Otherwise, yesterday will be asumed.
- ```end_date```: If is set, the ending period. Otherwise, yesterday will be asumed.
- ```base_url```: An array with the base urls of the journals to collect.

```
{
    "xslt_filename": "sushi-xml2csv.xslt",
    "report": "JR1",
    "release": "4.1",
    "silent": false,
    "timeout": 100,
    "begin_date": "2022-01-01",
    "end_date": "2022-12-31",
    "base_urls": {
      "journal01": "https://journal01.foo",
      "journal02": "https://journal02.foo"
    }
}
```

# Installation

## Host 

1. Ensure your host fits the requeriments.

   ```
   $ php -v | grep cli \
     && echo "PLUGINS: " \
     && php -m | grep -q xml && echo "XML:  Found" || echo "XML: Not found. Install libxml2-dev and the xml extension PHP." \
     && php -m | grep -q xsl && echo "XSL:  Found" || echo "XSL: Not found. Install libxslt-dev and the xsl extension for PHP." \
     && php -m | grep -q curl && echo "cURL: Found" || echo "cURL: Not found. Install curl and the curl extension for PHP." 
   ```

2. Clone the repository locally.
   ```
   $ git clone https://github.com/marcbria/sushiReport.git
   ```

3. Configure the application (see the examples in ./config/)

4. Run the script (see the syntax)
   ```
   $ php sushiReport.php config/config-JR1.json yesterday
   ```


## Docker (recommended)

Ensure you have docker installed and 

1. Clone the repository locally:
   ```
   $ git clone https://github.com/marcbria/sushiReport.git
   ```

2. Add or modify config files and build your image:

   ```
   $ docker build -t sushi-report:latest .
   ```

3. Test the container and see syntax:

   ```
   $ docker run --rm -i sushi-report:latest php sushiReport.php
   ```

4. Run it with your own parameters:

   ```
   $ docker run --rm -i sushi-report:latest php sushiReport.php config/yourConfig.json yesterday
   ```

### Alternative calls (TODO)

As expained in the quickstart, you can avoid all former steps running the image from Docker Hub 
(warning: images still not uploaded yet) and creating a file volume with your personalized 
config.json. The call will be as follows:

```
$ docker run --rm -v "$(pwd)/myconfig.json:/usr/src/sushiReport/config.json -i sushi-report:latest
```

Container will run with default paramenters and will asume config.json as the config file, so it's enough
to create/overwrite this config.json file to run the script.


# Cases of use

## Service global usage

As a publishing service, we like to know the usage of all our journals. 
As far as we have multiple (single-tenant) OJS installations, we need
an alternative way to get this global picture.

## Multiple sources

Journal articles could be republished by third party (repositories, indexers...).
Editors would like to know the impact of their work in a centralized place.


# Usage example

You can decide how to use this script. Our approach was:

Define two config files:
- ```config-JR1.json```: Daily range, with your full list of journals and JR1 data.
- ```config-AR1.json```: Daily range, with your full list of journals and AR1 data.

Create a bash script wrapper (see cronSushi.sh) to call sushiReport.php with the proper parameters,
and we add the script calls in cron.

sushiReport is called without dates to only process yesterday's data.
Data is append to files in a public web folder to let the service members download it.


# TBD

This script is still beta. Further testing is required and it could be extended as follows:

- [x] Add journal as first column in AR1 results.
- [x] Push image to Docker Hub.
- [ ] Add unit tests.
- [ ] Test against non OJS sources.
- [x] Let the script save results (instead of stdout).
- [ ] Entrypoint with "test, run" actions for the Dockerimage.
- [x] Remove calling parameters: All based on config file values.
- [ ] Think filename autorotations: Daily, Monthly, Yearly...
- [ ] Overwrite config settings with ENV variables.


# Dependencies
PHP 7+ (with php-xml, php-xsl, php-curl)

# Author
Marc Bria - marc.bria(a)uab.cat (Universitat Autònoma de Barcelona)
