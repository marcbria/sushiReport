# sushiReport
A small harvester that collects COUNTER statistics from multiple OJS (sushi-lite plugin required).

# Syntax
```
$ php sushi.php [configfile.json]
```

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

# Dependencies
PHP 7+ (with php-xml, php-curl)

# Author
Marc Bria - marc.bria(a)uab.cat
