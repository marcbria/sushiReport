<?php

class SushiReport {

    // Variables de configuración
    private $base_urls;
    private $xslt_filename;
    private $report;
    private $release;
    private $begin_date;
    private $end_date;

    public function __construct($config_file = 'config.json')
    {
        if (!file_exists($config_file)) {
            die("Error: config file not found");
        }
        $config = json_decode(file_get_contents($config_file), true);
        $this->base_urls = $config['base_urls'];
        $this->xslt_filename = $config['xslt_filename'];
        $this->report = $config['report'];
        $this->release = $config['release'];
        $this->begin_date = $config['begin_date'];
        $this->end_date = $config['end_date'];
    }

    public function run() {

        $this->checkRequirements();

        // Recorremos las urls base
        foreach ($this->base_urls as $base_url) {
            $url = $base_url . $this->queryString();

	    $result = $this->loadXML($url, $this->xslt_filename);
	    echo $result;
	    //file_put_contents("result.xml", $result);

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

    public function getXML($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public function loadXML($url, $xslt_filename) {
        // Cargamos el XML desde la URL (gestión de errores).
        $xml_string = $this->getXML($url);
        if ($xml_string === false) {
            die("Error loading XML");
        }

        // Cargamos el XSLT indicado en el config.json
        $xsl = new DOMDocument();
        if (!file_exists($xslt_filename)) {
            die("Error: xslt_file not found");
        }
        $xsl->load($xslt_filename);

        // Creamos un nuevo documento y cargamos el XML
        $xml = new DOMDocument();
        $xml->loadXML($xml_string);

        // Creamos un nuevo procesador XSLT y importamos el estilo
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);

        // Realizamos la transformación y devolvemos el resultado
        return $proc->transformToXML($xml);
    }


}

echo "--> Start harvasting...";

$config_file = "config.json";
if(isset($argv[1])){
    $config_file = $argv[1];
}

echo "--> Processing files...";

$sushiReport = new SushiReport($config_file);
$sushiReport->run();

echo "--> Process finished";
