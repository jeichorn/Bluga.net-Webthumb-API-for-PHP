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

// setup db connection
$pdo = new PDO($DB_DSN,$DB_USER,$DB_PASSWORD);
$stmtJob = $pdo->prepare('update jobs set end_time = ? where job_id = ?');

$id = $_GET['id'];
$urlId = $_GET['url_id'];

$url = 'http://webthumb.bluga.net/data/'
	.substr($id,-2).'/'.substr($id,-4,-2).'/'.substr($id,-6,-4).
	"/$id-thumb_$THUMB_SIZE.jpg";

$request = new PEAR2_HTTP_Request($url);
$request->requestToFile($OUTPUT_DIR.'/'.$urlId.'.jpg');
$stmtJob->execute(array(date('Y-m-d H:i:s'),$id));

echo "Were good thanks";
