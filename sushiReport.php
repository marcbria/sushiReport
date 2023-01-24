<?php


/**
 * Class Sushi
 * Gets xml from each journal and process it to return a CSV.
 */
class SushiReport {

    // Variables de configuración
    private $config_file;
    private $xslt_file;
    private $base_urls;
    private $report;
    private $release;
    private $begin_date;
    private $end_date;

    // Sushi constructor: Set the variables from parameters and/or config file.
    public function __construct($config_file, $period = null)
    {

        $this->config_file=$config_file;
        $this->checkConfigFile($config_file);
        $config = json_decode(file_get_contents($this->config_file), true);
        $this->base_urls = $config['base_urls'];
        $this->xslt_file = "config/" . $config['xslt_file'];
        $this->report = $config['report'];
        $this->release = $config['release'];
        $this->begin_date = $config['begin_date'];
        $this->end_date = $config['end_date'];

        if ($period == "yesterday") {
            $this->begin_date = date('Y-m-d',strtotime("-1 days"));
            $this->end_date = date('Y-m-d',strtotime("-1 days"));
        }

        $this->showConfig();
    }

    // Sushi runner: Gets xml from each journal and process it to return a CSV.  
    public function run() {

        $this->checkRequirements();

        printf("> Harvesting and processing...\n");

        // Getting the full journal list
        foreach ($this->base_urls as $journal => $base_url) {

            printf("--> Processing journal: $journal");

            // Cargamos y procesamos el xml de cada revista
            $result = $this->loadXML($journal, $this->xslt_file);
         
	        //file_put_contents("result-" . $this->config_file . ".csv", $result);
            printf ("$result\n\n");

        }
    }

    public function showConfig () {

        printf ("\n====================================================\n");
        printf ("  - Config file: " . $this->config_file . "\n");
        printf ("  - XSLT file:   " . $this->xslt_file . "\n");
        printf ("  - Base urls:   " . count($this->base_urls) . "\n");
        printf ("  - Date range:  " . $this->begin_date . " to " . $this->end_date . "\n");
        printf ("  - Report:      " . $this->report . "\n");
        printf ("  - Release:     " . $this->release . "\n");
        printf ("====================================================\n\n");

    }

    // Helper to check php requirements.
    public function checkConfigFile () {
        if (!file_exists($this->config_file)) {
            printf ("\nError: config file [$this->config_file] not found.\n\n");
            printf ("Check file name and permisions and the syntax of your call:\n");
            printf ("  $ php sushi yourConfigFile.json\n");
            die();
        }
    }

    // Helper to check php requirements.
    public function checkRequirements () {
        if (!extension_loaded('curl')) {
            die("Error: cURL extension not loaded.");
        }
        if (!extension_loaded('xml')) {
            die("Error: xml extension not loaded.");
        }
    }

    // Helper to build the query string.
    public function queryString () {
        $queryString = "/sushiLite/v1_7/GetReport?".
                "Report=$this->report&".
                "Release=$this->release&".
                "BeginDate=$this->begin_date&".
                "EndDate=$this->end_date";
        return $queryString;
    }

    // Helper to get the XML via curl.
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

    // Helper to load the XML from the specified url and process it with the specified xslt file.
    public function loadXML($journal) {

        $url = $this->base_urls[$journal] . $this->queryString();

        // Cargamos el XML desde la URL (gestión de errores).
        $xml_string = $this->getXML($url);
        if ($xml_string === false) {
            die("Error loading XML\n");
        }

        // Cargamos el XSLT indicado en el config.json
        $xsl = new DOMDocument();
        if (!file_exists($this->xslt_file)) {
            die("Error: xslt_file not found\n");
        }
        $xsl->load($this->xslt_file);

        // Creamos un nuevo documento y cargamos el XML
        $xml = new DOMDocument();
        $xml->loadXML($xml_string);

        // Creamos un nuevo procesador XSLT y importamos el estilo
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);

        // Realizamos la transformación y devolvemos el resultado
        $result = $proc->transformToXML($xml);

        if ( trim($result) == "" ) {
            $result = "\nNo data avaliable: Probably sushi-lite plugin is not enabled in $journal";
        }

        return $result;
    }


}

/************
 *   Main   * 
 ************/

printf("> Loading configuration...\n");

$config_file = "config/config.json";
if(isset($argv[1])){
    $config_file = $argv[1];
}

$period = "";
if(isset($argv[2])){
    $period = $argv[2];
}

$sushiReport = new SushiReport($config_file, $period);
$sushiReport->run();

printf ("> Process FINISHED!\n");
