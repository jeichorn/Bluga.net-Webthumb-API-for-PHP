--TEST--
XML Generation Webthumb_Request_Submit with a job in it
--FILE--
<?php
require_once '_setup.inc';

Bluga_Webthumb_Request::$USE_PRETTY_PRINT = true;
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
  <version>2</version>
  <request>
    <url>test</url>
  </request>
</webthumb>
