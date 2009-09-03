<?php
// require autoloaders for Bluga code and bundled PEAR2 code
// You will need to fix these paths if your not running from an svn checkout
require_once dirname(__FILE__).'/../../../Bluga/PEAR2/Autoload.php';
require_once dirname(__FILE__).'/../../../Bluga/Autoload.php';

require_once dirname(__FILE__).'/config.php';

// check for a config file in your home dir
// you can set the items above in .webthumb.php in your home dir
$home = getenv('HOME');
if (file_exists("$home/.webthumb.php")) {
	include "$home/.webthumb.php";
}

// setup db connection
$pdo = new PDO($DB_DSN,$DB_USER,$DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


// setup a new Webthumb wrapper
$webthumb = new Bluga_Webthumb();
$webthumb->setApiKey($APIKEY);

// query the urls db 
// you might want to make this more complex
$query = "select url_id, url from urls"; 

// make requests in batches of 50
// sleep a bit between to be nice
$batch = 0;
foreach($pdo->query($query) as $row) {
	$j = $webthumb->addUrl($row['url'],$THUMB_SIZE, 1024, 768);
	$j->options->notify = $NOTIFY_URL.'?url_id='.$row['url_id'];
	$j->user['url_id'] = $row['url_id'];

	if ($batch++ > 50) {
		$batch = 0;
		submitJobs($webthumb);
	}
}
submitJobs($webthumb);

function submitJobs($webthumb) {
	global $pdo;
	// prepare a jobs statement for inserting
	$stmtJobs = $pdo->prepare("insert into jobs (job_id, url_id, start_time) values(?,?,?)");

	echo date('Y-m-d H:i:s').": Submitting ".count($webthumb->jobs)." jobs\n";
	$webthumb->submitRequests();

	// add a job entry to the db for tracking
	foreach($webthumb->jobs as $job) {
		$stmtJobs->execute(array($job->status['id'],$job->user['url_id'],$job->status['start_time']));
	}
}
