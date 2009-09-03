--TEST--
XML Generation Webthumb_Request_Submit with a job in it
--FILE--
<?php
require_once '_setup.inc';

$r = new Bluga_Webthumb_Request_Submit('apikey');
$j = new Bluga_Webthumb_Job();
$j->options->url = 'test';
$r->jobs[] = $j;

echo $r->asXML();
?>
--EXPECT--
<?xml version="1.0"?>
<webthumb>
  <apikey>apikey</apikey>
  <request>
    <url>test</url>
  </request>
</webthumb>
