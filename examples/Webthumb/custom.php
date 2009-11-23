<pre>
<?php

// bring in your configuration, api yet etc
require_once 'config.php';

ini_set('max_execution_time',90);

$url = 'http://webthumb.bluga.net/';

try {
    $webthumb = new Bluga_Webthumb();
    $webthumb->setApiKey($APIKEY);

    // set which image to download and browser size
    $job = $webthumb->addUrl($url,'custom', 1024, 1024); 


    // set the size of the custom thumbnail
    $job->options->customThumbnail = array('width' => 1024, 'height' => 1024); 
    $job->options->fullthumb = 1;

    // if your making a lot of requests use notification, set it like
    // $job->options->notify = 'http://example.com/notify.php';
    // see bulk_with_db for a complete example
    $webthumb->submitRequests();

    // wait for the job to be finished and download, don't do this if you use notify
    while (!$webthumb->readyToDownload()) {
        sleep(5);
	echo "Checking Job Status\n";
        $webthumb->checkJobStatus();
    }

    $webthumb->fetchToFile($job);
    echo '<img src="'.$job->status->id.'.jpg'.'" /><br />';

} catch (Exception $e) {
    var_dump($e->getMessage());
}

?>

</pre>

