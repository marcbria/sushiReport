<?php

class SushiReport {

    // Variables de configuración
    private $base_urls;
    private $xslt_filename;
    private $report;
    private $release;
    private $begin_date;
    private $end_date;

    public function __construct()
    {
        $config = json_decode(file_get_contents('config.json'), true);
        $this->base_urls = $config['base_urls'];
        $this->xslt_filename = $config['xslt_filename'];
        $this->report = $config['report'];
        $this->release = $config['release'];
        $this->begin_date = $config['begin_date'];
        $this->end_date = $config['end_date'];
    }

    public function main() {

        $this->checkRequirements();

        // Recorremos las urls base
        foreach ($this->base_urls as $base_url) {
            $url = $base_url . $this->queryString();

            $xsl = new DOMDocument();
            $xsl->load($this->xslt_filename);

            $xml = new DOMDocument();
            $xml = $this->cargarXML($url);
            $proc = new XSLTProcessor();
            $proc->importStylesheet($xsl);
            echo $proc->transformToXML($xml);
        }
    }

    public function checkRequirements () {
        if (!extension_loaded('curl')) {
            die("Error: cURL extension not loaded.");
        }
        if (!extension_loaded('xml')) {
            die("Error: xml extension not loaded.");
        }
    }

    public function queryString () {
        $queryString = "/sushiLite/v1_7/GetReport?".
		"Report=$this->report&".
		"Release=$this->release&".
		"BeginDate=$this->begin_date&".
		"EndDate=$this->end_date";
        return $queryString;
    }

    public function cargarXML($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl);
	curl_close($curl);
	$xml = new DOMDocument();
	$xml->loadXML($result);
	return $xml;
    }
}

$sushiReport = new SushiReport();
$sushiReport->main();

