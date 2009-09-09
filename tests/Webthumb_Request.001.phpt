--TEST--
XML Generation Empty Webthumb_Request
--FILE--
<?php
require_once '_setup.inc';

Bluga_Webthumb_Request::$USE_PRETTY_PRINT = true;
$r = new Bluga_Webthumb_Request('apikey');
echo $r->asXML();
?>
--EXPECT--
<?xml version="1.0"?>
<webthumb>
  <apikey>apikey</apikey>
  <version>2</version>
</webthumb>
