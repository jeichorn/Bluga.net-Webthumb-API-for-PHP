<?php
// PUT Config here, or you can put it
// in .webthumb.php in your home dir

// Your apikey goes here
$APIKEY = "INSERT API KEY HERE";

// http://us2.php.net/manual/en/pdo.connections.php
$DB_DSN = 'mysql:host=localhost;dbname=test';
$DB_USER = 'dbuser';
$DB_PASSWORD = 'dbpassword';
$OUTPUT_DIR = '/tmp/thumbs';
$THUMB_SIZE = 'large'; // http://webthumb.bluga.net/apidoc#request

$NOTIFY_URL = 'http://bluga.net/notify.php';  // url where you put the notify script

