<?php
// fix this include if you aren't running from the examples dir
require_once '/home/jeichorn/sandbox/Bluga/trunk/Bluga/PEAR2/Autoload.php';

// put config.php in the same dir as the notify script or
if (file_exists(dirname(__FILE__).'/config.php')) {
	include dirname(__FILE__).'/config.php';
}

// check for a config file in your home dir
// you can set the items above in .webthumb.php in your home dir
$home = getenv('HOME');
if (file_exists("$home/.webthumb.php")) {
	include "$home/.webthumb.php";
}

file_put_contents("/tmp/notify_post.log", json_encode($_GET)."\n".json_encode($_POST)."\n");

if ($_GET['notify_key'] != $NOTIFY_KEY)
{
	echo "Oh no someone is trying to hack me\n";
}
else
{
	// download the key, use fetch api
	$webthumb = new Bluga_Webthumb();
	$webthumb->setApiKey($APIKEY);

	$job = new Bluga_Webthumb_Job();
	$job->status['id'] = $_POST['id'];

	$webthumb->fetchToFile($job, $OUTPUT_DIR."/".$_GET['file'], $THUMB_SIZE);

	echo "Wrote file to: ".$OUTPUT_DIR.$_GET['file']."\n";
	echo "And everything worked\n";
}
