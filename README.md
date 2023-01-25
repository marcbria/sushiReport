# sushiReport
A small and high configurable harvester that collects COUNTER statistics from multiple OJS (sushi-lite plugin required) and returns data in CSV format.

# Syntax
```
$ php sushiReport.php [<configfile.json> [yesterday]]
```

- ```configfile.json```: path to the config file. Default assumes ./config.json.
- ```yesterday```: Overwrites config dates to be "yesteday". Useful for cron daily calls.


# Config 

- ```xslt_filename```: name of the xsl transformations file.
- ```report```: sushi report type (JR1, AR1).
- ```release```: release number (ie: 4.1).
- ```begin_date```: starting period.
- ```end_date```: end period.
- ```base_url```: array with the base urls of the journals to collect.

```
{
    "xslt_filename": "sushi-xml2csv.xslt",
    "report": "JR1",
    "release": "4.1",
    "begin_date": "2022-01-01",
    "end_date": "2022-12-31",
    "base_urls": {
      "analisi": "https://analisi.cat",
	    "anuarioiet": "https://revistes.uab.cat/anuarioiet"
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

You can avoid former steps just running the image from Docker Hub (warning: images still not uploaded yet)
and creating a file volume with your personalized config.json as follows:

```
$ docker run --rm -v "$(PWD)"/myconfig.json:/usr/share/sushiReport/config.json:ro -i sushi-report:latest
```


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

We defined two config files:
- ```config-JR1.json```: Daily range, with our full list of journals and JR1 data.
- ```config-AR1.json```: Daily range, with our full list of journals and AR1 data.

We created two daily crons (at 00:30 and 00:40) calling the script with those
two config files with "yesterday" parameter.

Our cron looks like this:

```
30 00 * * * docker run --rm -i sushi-report:latest php sushiReport.php config/uab/config-JR1.json yesterday >> /home/dojo/sites/common/counter/2023-service.csv
```

Data is appened to files in a public web folder to let us (and editors) download it.
Protect your download folder if you don't like to make this cvs public (althogh IMHO, don't make much sense).


# TBD

This script is still beta. Further testing is required and it could be extended as follows:

- [ ] Add journal as first column in AR1 results.
- [ ] Push image to Docker Hub.
- [ ] Test against non OJS sources.
- [ ] Let the script save results (instead of stdout).
- [ ] Entrypoint with "test, config, show & save" actions for the Dockerimage.
- [ ] When save: Year rotation.
- [ ] When save: Journal rotation.
- [ ] Rethink calling parameters.


# Dependencies
PHP 7+ (with php-xml, php-xsl, php-curl)

# Author
Marc Bria - marc.bria(a)uab.cat

