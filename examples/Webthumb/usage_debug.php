<pre>
<?php

// bring in your configuration, api yet etc
require_once 'config.php';

ini_set('max_execution_time',90);

$url = 'http://webthumb.bluga.net';

try {
    $webthumb = new Bluga_Webthumb();

    // enable debug to see what xml is being sent
    $webthumb->debug = true;

    // you might change the default if one of the PEAR2 adapters is giving you problems
    // but its mainly used for testing, the fastest adapter should be choosen by default
    // The 4 Adapters are:
    // PEAR2_HTTP_Request_Adapter_Http
    // PEAR2_HTTP_Request_Adapter_Phpstream
    // PEAR2_HTTP_Request_Adapter_Curl
    // PEAR2_HTTP_Request_Adapter_Phpsocket
    //$webthumb->httpRequestAdapter = new PEAR2_HTTP_Request_Adapter_Phpstream();

    $webthumb->setApiKey($APIKEY);
    $job = $webthumb->addUrl($url,'medium2', 1024, 768);
    $webthumb->submitRequests();

    while (!$webthumb->readyToDownload()) {
        sleep(5);
	echo "Checking Job Status\n";
        $webthumb->checkJobStatus();
    } // while (!$webthumb->ready_to_download())

    $webthumb->fetchToFile($job);
    echo '<img src="'.$job->status->id.'.jpg'.'" /><br />';

} catch (Exception $e) {
    echo "We got an Exception\n";
    echo $e->getMessage()."\n\n";
    echo $e->getTraceAsString();
}

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>

</pre>
