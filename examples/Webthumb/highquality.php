<pre>
<?php

// high quality fullsize output png thumbnail example

// bring in your configuration, api yet etc
require_once 'config.php';

ini_set('max_execution_time',90);

$url = 'http://webthumb.bluga.net/';

try {
    $webthumb = new Bluga_Webthumb();
    $webthumb->setApiKey($APIKEY);

    // -1 for auto height
    $job = $webthumb->addUrl($url,'full', 1024, -1); 

    // enable fullthumb
    $job->options->fullthumb = 1;
    $job->options->outputType = 'png';

    // if your making a lot of requests use notification, set it like
    // $job->options->notify = 'http://example.com/notify.php';
    // see bulk_with_db for a complete example
    $webthumb->submitRequests();

    // status webpage
    echo "http://webthumb.bluga.net/pickup?id={$job->status->id}\n";

    // wait for the job to be finished and download, don't do this if you use notify
    while (!$webthumb->readyToDownload()) {
        sleep(5);
	echo "Checking Job Status\n";
        $webthumb->checkJobStatus();
    }

    $webthumb->fetchToFile($job);
    echo '<img src="'.$job->status->id.'.jpg'.'" />'."\n";

} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>

</pre>

