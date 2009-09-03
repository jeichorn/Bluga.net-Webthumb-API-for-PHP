--TEST--
XML Generation Empty Webthumb_Request
--FILE--
<?php
require_once '_setup.inc';

$r = new Bluga_Webthumb_Request('apikey');
echo $r->asXML();
?>
--EXPECT--
<?xml version="1.0"?>
<webthumb>
  <apikey>apikey</apikey>
</webthumb>
