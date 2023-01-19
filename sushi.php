<?php


/**
 * Class Sushi
 * Gets xml from each journal and process it to return a CSV.
 */
class SushiReport {

    // Variables de configuración
    private $base_urls;
    private $xslt_filename;
    private $report;
    private $release;
    private $begin_date;
    private $end_date;

    // Sushi constructor: Set the variables from the config file.
    public function __construct($config_file = 'config.json', $period)
    {

        $this->checkConfigFile($config_file);
        $config = json_decode(file_get_contents($config_file), true);
        $this->base_urls = $config['base_urls'];
        $this->xslt_filename = $config['xslt_filename'];
        $this->report = $config['report'];
        $this->release = $config['release'];
        $this->begin_date = $config['begin_date'];
        $this->end_date = $config['end_date'];

        if ($period == "yesterday") {
            $this->begin_date = date('d.m.Y',strtotime("-1 days"));
            $this->end_date = date('d.m.Y',strtotime("-1 days"));
        }

        //$this->showConfig();
    }

    // Sushi runner: Gets xml from each journal and process it to return a CSV.  
    public function run() {

        $this->checkRequirements();

        // Getting the full journal list
        foreach ($this->base_urls as $journal => $base_url) {

            printf("\n--> Processing journal: $journal\n");

            // Cargamos y procesamos el xml de cada revista
            $result = $this->loadXML($journal, $this->xslt_filename);
         

	        //file_put_contents("result-" . $this->$config_file . ".csv", $result);
            echo "$result";

        }
    }

    public function showConfig () {

        printf ("  - Config file: " . $config_file . "\n");
        printf ("  - XSLT file:   " . $this->xslt_filename . "\n");
        printf ("  - Base urls:   " . count($this->base_urls) . "\n");
        printf ("  - Report:      " . $this->report = $config['report']. "\n");
        printf ("  - Release:     " . $this->release = $config['release']. "\n");

    }

    // Helper to check php requirements.
    public function checkConfigFile ($config_file = 'config.json') {
        if (!file_exists($config_file)) {
            printf ("\nError: config file [$config_file] not found.\n\n");
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
    public function loadXML($journal, $xslt_filename) {

        $url = $this->base_urls[$journal] . $this->queryString();

        // Cargamos el XML desde la URL (gestión de errores).
        $xml_string = $this->getXML($url);
        if ($xml_string === false) {
            die("Error loading XML\n");
        }

        // Cargamos el XSLT indicado en el config.json
        $xsl = new DOMDocument();
        if (!file_exists($xslt_filename)) {
            die("Error: xslt_file not found\n");
        }
        $xsl->load($xslt_filename);

        // Creamos un nuevo documento y cargamos el XML
        $xml = new DOMDocument();
        $xml->loadXML($xml_string);

        // Creamos un nuevo procesador XSLT y importamos el estilo
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);

        // Realizamos la transformación y devolvemos el resultado
        $result = $proc->transformToXML($xml);

        if ( trim($result) == "" ) {
            $result = "No data avaliable: Probably sushi-lite plugin is not enabled in $journal\n";
        }

        return $result;
    }


}

/************
 *   Main   * 
 ************/

printf("> Loading configuration...\n");

$config_file = "config.json";
if(isset($argv[1])){
    $config_file = $argv[1];
}

$period = "";
if(isset($argv[2])){
    $period = $argv[2];
}

printf("> Harvesting and processing...\n");

$sushiReport = new SushiReport($config_file, $period);
$sushiReport->run();

printf ("\n\n> Process FINISHED!\n");
