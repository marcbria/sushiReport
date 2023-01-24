# sushiReport
A small harvester that collects COUNTER statistics from multiple OJS (sushi-lite plugin required) and returns data in CSV format.

# Syntax
```
$ php sushiReport.php [<configfile.json> [yesterday]]
```

- configfile.json: path to the config file. Default assumes ./config.json.
- yesterday: Overwrites config dates to be "yesteday". Useful for cron daily calls.


# Config 

- xslt_filename: name of the xsl transformations file.
- report: sushi report type (JR1, AR1).
- release: release number (ie: 4.1).
- begin_date: starting period.
- end_date: end period.
- base_url: array with the base urls of the journals to collect.

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
     && php -m | grep -q xml && echo "XML:  Found" || echo "XML: Not found" \
     && php -m | grep -q xml && echo "XML:  Found" || echo "XML: Not found" \
     && php -m | grep -q curl && echo "cURL: Found" || echo "cURL: Not found" 
   ```

2. Clone the repository locally.
3. Configure the application (see the examples in ./config/)
4. Run the script (see the syntax)

## Docker

Ensure you have docker installed and 

1. Clone the repository locally:

2. Add or modify config files and build your image:

```$ docker build -t sushi-report:latest .```

3. Test the container and see syntax:

```$ docker run --rm -i sushi-report:latest php sushiReport.php```

4. Run it with your own parameters:

```$ docker run --rm -i sushi-report:latest php sushiReport.php```

<!-- TODO: Alternatively, you can avoid all this steps using the image in docker hub
and creating a file volume with your personalized config.json:
```$ docker run --rm -v "$(pwd)"/myconfig.json:/usr/share/sushiReport/config.json:ro``` -->

# Cases of use

## Service global usage

As a publishing service, we like to know the usage of all our journals. 
As far as we have multiple (single-tenant) OJS installations, we need
an alternative way to get this global picture.

## Multiple sources

Journal articles could be republished by third party (repositories, indexers...).
Editors would like to know the impact of their work in a centralized place.

# Usage

You can decide how to use this script. Our approach was:

We defined two config files:
- config-JR1.json: Daily range, with our full list of journals and JR1 data.
- config-AR1.json: Daily range, with our full list of journals and AR1 data.

We created two daily crons (at 00:30 and 00:40) calling the script with those
two config files with "yesterday" parameter.

Data is appened to files in a public web folder to let us (and editors) download it.

# Dependencies
PHP 7+ (with php-xml, php-curl)

# Author
Marc Bria - marc.bria(a)uab.cat
