<?php
require_once 'config.php';

$source = array();
$tmp = file('urls.txt');
foreach($tmp as $line)
{
	$source[trim($line)] = trim($line);
}

echo "URL list contains: ".count($source)."\n";

while(count($source) > 0)
{ 
	// watch requesting too many urls at once, my server might respond with a 
	// 100 code and the php http code doesn't handle that yet
	$urls = array_splice($source,0,25); 
	try {
	    echo "building request\n";
	    $webthumb = new Bluga_Webthumb();
	    $webthumb->setApiKey($APIKEY);
		$i = 0;
		$jobs = array();
		foreach($urls as $url) {
			try {
				echo "Adding url: $url\n";
				$j = $webthumb->addUrl($url,'medium2', 1024, 768);
				$j->file = str_replace(array(':',':','/','&'),'_',substr($url,7));
				$jobs[$i] = $j;
				$i++;
			}
			catch(Exception $e) {
				echo "Error adding a url -- ".$e->getMessage()."\n";
			}
		}

		echo "Requesting $i thumbs\n";
		$webthumb->submitRequests();

	    while (!$webthumb->readyToDownload()) {
		sleep(12);
		echo "Checking Job Status\n";
		$webthumb->checkJobStatus();
	    } // while (!$webthumb->ready_to_download())

		$webthumb->fetchAll('thumbs');

		foreach($webthumb->failedJobs as $url => $job)
		{
			echo "No job submitted for: $url\n";
		}

	} catch (Exception $e) {
	//    var_dump($e);
		echo "Exception handler\n";
		echo $e->getMessage();

		echo "\n\n";
	}
}

?>
