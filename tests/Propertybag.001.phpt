--TEST--
Test that property bag sorting for iteration works
--FILE--
<?php
require_once '_setup.inc';

$bag = new Bluga_Propertybag(array('one','two','three'));

$bag->two = 2;
$bag->three = 3;
$bag->one = 1;

foreach($bag as $k => $v) {
	echo "$k:$v\n";
}
?>
--EXPECT--
one:1
two:2
three:3
