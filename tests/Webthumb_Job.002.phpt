--TEST--
XML Generation All Options filled
--FILE--
<?php
require_once '_setup.inc';

$job = new Bluga_Webthumb_Job();
$job->options->url = "http://bluga.net/test";
$job->options->outputType = "png";
$job->options->width = 1024;
$job->options->height = 1024;
$job->options->fullthumb = 1;
$job->options->customThumbnail = array('width'=>120,'height'=>120);
$job->options->effect = "mirror";
$job->options->delay = 5;
$job->options->excerpt = array('x'=>400,'y'=>400,'height'=>200,'width'=>200);
$job->options->notify = "http://webthumb.bluga.net/notify.php?blah=blah&blah=blah2";
echo $job->asXML();
?>
--EXPECT--
<?xml version="1.0"?>
<request>
  <url>http://bluga.net/test</url>
  <outputType>png</outputType>
  <width>1024</width>
  <height>1024</height>
  <fullthumb>1</fullthumb>
  <customThumbnail width="120" height="120"></customThumbnail>
  <effect>mirror</effect>
  <delay>5</delay>
  <excerpt>
    <x>400</x>
    <y>400</y>
    <height>200</height>
    <width>200</width>
  </excerpt>
  <notify>http://webthumb.bluga.net/notify.php?blah=blah&amp;blah=blah2</notify>
</request>
