<?php
require_once dirname(__FILE__).'/../../../Bluga/Autoload.php';

// PUT Config here, or you can put it
// in .webthumb.php in your home dir

// Your apikey goes here
$APIKEY = "INSERT API KEY HERE";

// http://us2.php.net/manual/en/pdo.connections.php
$OUTPUT_DIR = '/tmp/thumbs';
$THUMB_SIZE = 'large'; // http://webthumb.bluga.net/apidoc#request

$NOTIFY_KEY = '1234asdgasvd';
$NOTIFY_URL = 'http://webthumb.bluga.net/notify_post.php';  // url where you put the notify script

