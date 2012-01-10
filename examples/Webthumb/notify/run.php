<?php
// this is a really simple notify example, not tracking of urls is done, 
// the downside of this is its hard to prevent multi requests for the same url from being made
require_once dirname(__FILE__).'/config.php';


// check for a config file in your home dir
// you can set the items above in .webthumb.php in your home dir
$home = getenv('HOME');
if (file_exists("$home/.webthumb.php")) {
	include "$home/.webthumb.php";
}


// setup a new Webthumb wrapper
$webthumb = new Bluga_Webthumb();
$webthumb->setApiKey($APIKEY);

if (!isset($argv[1]))
{
	echo "php run.php url\nphp run.php http://bluga.net\n";
	die(1);
}

$url = $argv[1];
$j = $webthumb->addUrl($url,$THUMB_SIZE, 1024, 768);
$file = preg_replace('/[^A-Za-z0-9]/', '', $url)."-".date('YmdHis');
$j->options->notify = array('url' => $NOTIFY_URL."?notify_key=$NOTIFY_KEY&file=$file", 'post' => 'true');

echo date('Y-m-d H:i:s').": Submitting ".count($webthumb->jobs)." jobs\n";
$webthumb->submitRequests();

foreach($webthumb->jobs as $job) {
	echo "Added ".$job->status['id']." at ".$job->status['start_time']."\n";
}
