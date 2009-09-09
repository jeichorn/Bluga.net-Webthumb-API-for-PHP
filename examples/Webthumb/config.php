<?php
// require autoloader for Bluga
require_once '../../Bluga/Autoload.php';

// Your apikey goes here
$APIKEY = "INSERT API KEY HERE";

// check for a config file in your home dir
$home = getenv('HOME');
if (file_exists("$home/.webthumb.php")) {
	include "$home/.webthumb.php";
}
