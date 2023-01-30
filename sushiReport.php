<?php

/**
 * Class SushiReport
 * Gets xml from each journal and process it to return a CSV.
 */
class SushiReport {

    // Variables de configuración
    private $config_file;

    private $base_urls;
    private $xslt_file;

    private $report;
    private $release;
    private $results_file;
    private $silent;
    private $timeout;

    private $begin_date;
    private $end_date;


    // Sushi constructor: Set the variables from parameters and/or config file.
    public function __construct($config_file)
    {

        $this->config_file=$config_file;
        $this->config_file = isset($config_file)?$config_file:"config.json";

        $this->checkConfigFile($config_file);
        $config = json_decode(file_get_contents($this->config_file), true);

        $this->base_urls = is_array($config['base_urls'])?$config['base_urls']:"";
        $this->xslt_file = isset($config['xslt_file'])?"config/".$config['xslt_file']:"";

        $this->report = isset($config['report'])?$config['report']:"";
        $this->release = isset($config['release'])?$config['release']:"";
        $this->results_file = isset($config['results_file'])?$config['results_file']:"";
        $this->silent = isset($config['silent'])?$config['silent']:false;
        $this->timeout = isset($config['timeout'])?$config['timeout']:60;

        $yesterday = date('Y-m-d',strtotime("-1 days"));
        $this->begin_date = isset($config['begin_date'])?$config['begin_date']:$yesterday;
        $this->end_date = isset($config['end_date'])?$config['end_date']:$yesterday;

        $this->showConfig();
    }

    // Sushi runner: Gets xml from each journal and process it to return a CSV.  
    public function run() {

        $this->checkRequirements();
	    $this->print("> Harvesting and processing...\n");

        // Getting the full journal list
        foreach ($this->base_urls as $journal => $base_url) {

            $this->print("--> Processing journal: $journal\n");

            // Loading and processing the xml for each journal
            $results = $this->loadXML($journal, $this->xslt_file);
         
            if ($this->results_file != '') {
                file_put_contents($this->results_file, $results . PHP_EOL, FILE_APPEND);
            } else {
                printf ("$results\n\n");
            }

        }
    }

    // Helper function to print based on "silent" config parameter (verbose by default).
    public function print ($message) {
        if (! $this->silent) {
	        printf ("$message");
	    }
    }

    // Helper function to show how the config is processed (only when verbose mode).
    public function showConfig () {
        if (! $this->silent) {
            printf ("\n====================================================\n");
            printf ("  - Config file:  " . $this->config_file . "\n");
            printf ("  - XSLT file:    " . $this->xslt_file . "\n");
            printf ("  - Base urls:    " . count($this->base_urls) . "\n");
            printf ("  - Results file: " . $this->results_file . "\n");
            printf ("  - Date range:   " . $this->begin_date . " to " . $this->end_date . "\n");
            printf ("  - Report:       " . $this->report . "\n");
            printf ("  - Release:      " . $this->release . "\n");
            printf ("  - Timeout:      " . $this->timeout . "\n");
	        printf ("====================================================\n\n");
        }
    }

    // Helper to check if config file exists.
    public function checkConfigFile () {
        if (!file_exists($this->config_file)) {
            printf ("\nError: config file [$this->config_file] not found.\n\n");
            printf ("Check file name and permisions and the syntax of your call:\n");
            printf ("  $ php sushiReport.php config.json\n");
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
        if (!extension_loaded('xsl')) {
            die("Error: xsl extension not loaded.");
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

    // Gets the XML using curl.
    public function getXML($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

        // Error management (ie: 404, timeouts...)
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        try {
            $result = curl_exec($curl);
            if ($result === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
        } catch (Exception $e) {
            // Just in case you like to manage the exception:
            // error_log("Error loading the url: \n$url\n" . $e->getMessage());
            $result = false;
        }
        curl_close($curl);
        return $result;
    }
    

    // Helper to load the XML from the specified url and process it with the specified xslt file.
    public function loadXML($journal) {

        $url = $this->base_urls[$journal] . $this->queryString();
    
        // Adding some info for verbose mode:
        $this->print("\n--> URL String: $url");

        $result="";

        // Loading XML from an URL
        $xml_string = $this->getXML($url);
        if ($xml_string) {
    
            // Loading the XSLT specified in the config.json file
            $xsl = new DOMDocument();
            if (!file_exists($this->xslt_file)) {
                die("Error: xslt_file not found\n");
            }
            $xsl->load($this->xslt_file);
            
            // Creating a new DOM Document to load the XML
            $xml = new DOMDocument();
            $xml->loadXML($xml_string);
        
            // Creating a new XSLT processor to import the style and convert to CSV
            $proc = new XSLTProcessor();
            $proc->importStylesheet($xsl);
        
            // Doing the transformation and returning the result
            $result = $proc->transformToXML($xml);
        
        }

        // result is empty when we got an error.
        if ( trim($result) == "" ) {
            $result = "\nNo data avaliable: Probably sushi-lite plugin is not enabled in $journal";
        }

        return $result;
    }
    
}

/************
 *   Main   * 
 ************/

//printf("> Loading configuration...\n");

$config_file = "config.json";
if(isset($argv[1])){
    $config_file = $argv[1];
}

$sushiReport = new SushiReport($config_file);
$sushiReport->run();
$sushiReport->print("> Process FINISHED!\n");
