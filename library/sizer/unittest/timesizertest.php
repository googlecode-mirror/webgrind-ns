<?php

$root = '/var/www/Labs/chilikcms/libs/quickprof/sizer/';

include $root.'interface/ts.php';
include $root.'timesizer.php';

echo "<pre>";

$start = microtime();
$files = get_included_files();

for($i=0, $max = 10000; $i < $max; ++$i) {}
$tsizer = new Entity\timeSizer;

$elapsed = microtime() - $start;
echo "ELAPSED: ", $elapsed, "<br/>\n";

echo "TEST DEFAULT: ", $tsizer($elapsed), "<br/>\n";

echo "TEST 1: ", $tsizer($elapsed, 'us') ,"<br/>\n";

echo "TEST 2: ", $tsizer($elapsed, 'ms') ,"<br/>\n";

echo "TEST 3: ", $tsizer($elapsed, 's') ,"<br/>\n";

echo "TEST 4: ", $tsizer($elapsed, 'm') ,"<br/>\n";

echo "TEST 5: ", $tsizer($elapsed, 'h') ,"<br/>\n";


//for($i=0, $max = 100000000; $i < $max; ++$i) {}

$elapsed = rand(100,600);
echo "ELAPSED: ", $elapsed, "<br/>\n";

echo "TEST DEFAULT: ", $tsizer($elapsed), "<br/>\n";

echo "TEST 1: ", $tsizer($elapsed, 'us') ,"<br/>\n";

echo "TEST 2: ", $tsizer($elapsed, 'ms') ,"<br/>\n";

echo "TEST 3: ", $tsizer($elapsed, 's') ,"<br/>\n";

echo "TEST 4: ", $tsizer($elapsed, 'm') ,"<br/>\n";

echo "TEST 5: ", $tsizer($elapsed, 'h') ,"<br/>\n";


echo "</pre>";
