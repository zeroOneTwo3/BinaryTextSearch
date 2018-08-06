<?php

include 'BinaryTextSearch.php';

$filename = "example";
$string = "other";

$fbs = new \BinaryTextSearch\BinaryTextSearch($filename);

var_dump( $fbs->search($string));
echo "<br>";
var_dump( $fbs->search("abc"));
echo "<br>";
var_dump( $fbs->search("abaca"));