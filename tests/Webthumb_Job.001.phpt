--TEST--
XML Generation Empty Job
--FILE--
<?php
require_once '_setup.inc';

$job = new Bluga_Webthumb_Job();
echo $job->asXML();
?>
--EXPECT--
<?xml version="1.0"?>
<request/>
