<?php
class SushiReport {
    private $base_url;
    private $report;
    private $release;
    private $begin_date;
    private $end_date;
    private $queryString;
    private $config;

    public function __construct() {
        $this->config = json_decode(file_get_contents('config.json'), true);
        $this->base_url = $this->config['base_url'];
        $this->report = $this->config['report'];
        $this->release = $this->config['release'];
        $this->begin_date = $this->config['begin_date'];
        $this->end_date = $this->config['end_date'];
        $this->queryString = "/sushiLite/v1_7/GetReport?Report=$this->report&Release=$this->release&BeginDate=$this->begin_date&EndDate=$this->end_date";
    }
    
    public function main() {
        $this->checkDependencies();
        $url = $this->base_url . $this->queryString;
        printf("===========================================================\n");
        printf("Processing: $url \n");
        printf("===========================================================\n");
        
        $xsl = new DOMDocument();
        $xsl->load("sushi-xml2csv.xslt");
        
        $xml = new DOMDocument();
        $xml = $this->cargarXML($url);
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
        echo $proc->transformToXML($xml);
    }

    private function checkDependencies() {
        if (function_exists('curl_init')) {
            echo 'cURL is installed';
        } else {
            echo 'cURL is not installed';
            die();
        }
    }

    private function cargarXML($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl);
        curl_close($curl);
        if ($result === false) {
            echo "Error loading XML: " . curl_error($curl);
            return;
        }
        $xml = new DOMDocument();
        $xml->loadXML($result);
        return $xml;
    }

    private function cargarXML2($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
}

$sushiReport = new SushiReport();
$sushiReport->main();

