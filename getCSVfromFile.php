<?php


/******************************/
/**      CONFIGURATION       **/
/******************************/

define('FILE_JOURNALS', 'journals.lst');
define('FILE_XSLT', 'sushi-xml2csv.xslt');


function main() {

    checkDependencies();

    $base_url = trim(file_get_contents(FILE_JOURNALS));
    $url = "$base_url". queryString();

    printf("===========================================================\n");
    printf("Processing: $base_url \n");
    /*printf("Date Range: ");
    printf("Report: ");
    printf("Release: ");*/
    printf("===========================================================\n");

    $xsl = new DOMDocument();
    $xsl->load(FILE_XSLT);

    $xml = new DOMDocument();
    $xml = cargarXML($url);
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);
    echo $proc->transformToXML($xml);
}


function checkDependencies () {
    if (! function_exists('curl_init')) {
        echo 'cURL is not installed';
        die();
    }

}


function queryString () {
    $report = 'JR1';
    $release = '4.1';
    $begin_date = '2022-01-01';
    $end_date = '2022-12-31';
    $queryString = "/sushiLite/v1_7/GetReport?Report=$report&Release=$release&BeginDate=$begin_date&EndDate=$end_date";
    return $queryString;
}

function cargarXML($url) {
    printf ("Getting: $url\n");
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



function cargarXMLFail($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}



function cargarXMLCertificate($url = null) {

    $context=null;
    if (PHP_VERSION_ID >= 70100) {
        $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]);
        $xml = new DOMDocument();
        $xml->load($url, LIBXML_NONET);
    } else {
        $xml = new DOMDocument();
        $xml->load($url);
    }
    return $xml;
}

main();

