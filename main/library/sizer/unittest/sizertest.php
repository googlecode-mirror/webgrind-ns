<?php

$root = '/var/www/Labs/chilikcms/libs/webgrind/sizer/';

include $root.'interface/fs.php';
include $root.'sizer.php';
include $root.'version/sizer_v1.php';

$files = get_included_files();

$path = '/var/www/Labs/';
$dir = scandir("/var/www/Labs/");

$match = preg_grep('/(\.php)/xm', $dir);

$start = microtime(TRUE);
$mem = memory_get_peak_usage();

echo "<pre>";
for($i=0; $i<2; ++$i)
{

if($i === 0) $sizer = new Entity\Sizer;
else { $sizer = new Entity\Sizer_v1; }
echo "\nMethod: ", '$sizer->info($size);',"\n\n";

foreach($match as $val)
{
    $size = filesize($path.$val);
    echo $val," : ", $sizer->info($size), "\n";
}

echo "\n\nMethod: ", '$sizer->info($size, \'%01.0f %s\');',"\n\n";
foreach($match as $val)
{
    $size = filesize($path.$val);
    echo $val," : ", $sizer->info($size, '%01.0f %s'), "\n";
}

/*
 *  invoke method
 */

echo "\n\nMethod: ", '$sizer($size);',"\n\n";
foreach($match as $val)
{
    $size = filesize($path.$val);
    echo $val," : ", $sizer($size), "\n";
}

echo "\n\nMethod: ", '$sizer($size, \'%01.0f %s\');',"\n\n";
foreach($match as $val)
{
    $size = filesize($path.$val);
    echo $val," : ", $sizer($size, '%01.0f %s'), "\n";
}

foreach($files as $val)
{
    $size = filesize($val);
    echo $val," : ", $sizer->info($size), "\n";
}

$endmem = $sizer->info(memory_get_peak_usage() - $mem);
echo sprintf("\nElapsed: %01.5f ms and Memory Used: %01.0f\n",microtime(TRUE) - $start, $endmem); 
}
echo "</pre>";


/** HERE IS THE RESULT ON MY COMPUTER: 07 Oct 2011

SUMMARY:
    Sizer version 1.0: 0.01478 ms
    Sizer version 1.1: 0.00587 ms (2.52x faster)

Method: $sizer->info($size);

Lab_tpl.php : 6.71 KB
bench.php : 1.28 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 2.59 KB
desain.php : 669 bytes
di.php : 1.25 KB
faster.php : 2.76 KB
getmyuid.php : 24 bytes
grep.php : 1.99 KB
httpreq.php : 2.61 KB
include.php : 271 bytes
index.php : 7.59 KB
index_labs.php : 7.69 KB
index_new.php : 1.13 KB
indextest.php : 438 bytes
info.php : 1.26 KB
iofile.php : 1.70 KB
isfiletest.php : 705 bytes
lite.php : 15.16 KB
markdown.php : 84.12 KB
mdsyntax.php : 7.85 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 100.92 KB
regexroute.php : 1.62 KB
run-tests.php : 67.70 KB
run-tests2.php : 67.68 KB
statictest.php : 986 bytes
storage.class.php : 1.52 KB
stream.php : 2.59 KB
tag11.php : 1.61 KB
testecho.php : 549 bytes
tracefile-analyser.php : 4.83 KB
useragent.php : 1.48 KB


Method: $sizer->info($size, '%01.0f %s');

Lab_tpl.php : 7 KB
bench.php : 1 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 3 KB
desain.php : 669 bytes
di.php : 1 KB
faster.php : 3 KB
getmyuid.php : 24 bytes
grep.php : 2 KB
httpreq.php : 3 KB
include.php : 271 bytes
index.php : 8 KB
index_labs.php : 8 KB
index_new.php : 1 KB
indextest.php : 438 bytes
info.php : 1 KB
iofile.php : 2 KB
isfiletest.php : 705 bytes
lite.php : 15 KB
markdown.php : 84 KB
mdsyntax.php : 8 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 101 KB
regexroute.php : 2 KB
run-tests.php : 68 KB
run-tests2.php : 68 KB
statictest.php : 986 bytes
storage.class.php : 2 KB
stream.php : 3 KB
tag11.php : 2 KB
testecho.php : 549 bytes
tracefile-analyser.php : 5 KB
useragent.php : 1 KB


Method: $sizer($size);

Lab_tpl.php : 6.71 KB
bench.php : 1.28 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 2.59 KB
desain.php : 669 bytes
di.php : 1.25 KB
faster.php : 2.76 KB
getmyuid.php : 24 bytes
grep.php : 1.99 KB
httpreq.php : 2.61 KB
include.php : 271 bytes
index.php : 7.59 KB
index_labs.php : 7.69 KB
index_new.php : 1.13 KB
indextest.php : 438 bytes
info.php : 1.26 KB
iofile.php : 1.70 KB
isfiletest.php : 705 bytes
lite.php : 15.16 KB
markdown.php : 84.12 KB
mdsyntax.php : 7.85 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 100.92 KB
regexroute.php : 1.62 KB
run-tests.php : 67.70 KB
run-tests2.php : 67.68 KB
statictest.php : 986 bytes
storage.class.php : 1.52 KB
stream.php : 2.59 KB
tag11.php : 1.61 KB
testecho.php : 549 bytes
tracefile-analyser.php : 4.83 KB
useragent.php : 1.48 KB


Method: $sizer($size, '%01.0f %s');

Lab_tpl.php : 6.71 KB
bench.php : 1.28 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 2.59 KB
desain.php : 669 bytes
di.php : 1.25 KB
faster.php : 2.76 KB
getmyuid.php : 24 bytes
grep.php : 1.99 KB
httpreq.php : 2.61 KB
include.php : 271 bytes
index.php : 7.59 KB
index_labs.php : 7.69 KB
index_new.php : 1.13 KB
indextest.php : 438 bytes
info.php : 1.26 KB
iofile.php : 1.70 KB
isfiletest.php : 705 bytes
lite.php : 15.16 KB
markdown.php : 84.12 KB
mdsyntax.php : 7.85 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 100.92 KB
regexroute.php : 1.62 KB
run-tests.php : 67.70 KB
run-tests2.php : 67.68 KB
statictest.php : 986 bytes
storage.class.php : 1.52 KB
stream.php : 2.59 KB
tag11.php : 1.61 KB
testecho.php : 549 bytes
tracefile-analyser.php : 4.83 KB
useragent.php : 1.48 KB
/var/www/Labs/chilikcms/libs/webgrind/sizer/unittest/sizertest.php : 1.43 KB
/var/www/Labs/chilikcms/libs/webgrind/sizer/interface/fs.php : 630 bytes
/var/www/Labs/chilikcms/libs/webgrind/sizer/sizer.php : 1005 bytes
/var/www/Labs/chilikcms/libs/webgrind/sizer/version/sizer_v1.php : 1011 bytes

Elapsed: 0.00587 ms and Memory Used: 0

Method: $sizer->info($size);

Lab_tpl.php : 6.71 KB
bench.php : 1.28 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 2.59 KB
desain.php : 669 bytes
di.php : 1.25 KB
faster.php : 2.76 KB
getmyuid.php : 24 bytes
grep.php : 1.99 KB
httpreq.php : 2.61 KB
include.php : 271 bytes
index.php : 7.59 KB
index_labs.php : 7.69 KB
index_new.php : 1.13 KB
indextest.php : 438 bytes
info.php : 1.26 KB
iofile.php : 1.70 KB
isfiletest.php : 705 bytes
lite.php : 15.16 KB
markdown.php : 84.12 KB
mdsyntax.php : 7.85 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 100.92 KB
regexroute.php : 1.62 KB
run-tests.php : 67.70 KB
run-tests2.php : 67.68 KB
statictest.php : 986 bytes
storage.class.php : 1.52 KB
stream.php : 2.59 KB
tag11.php : 1.61 KB
testecho.php : 549 bytes
tracefile-analyser.php : 4.83 KB
useragent.php : 1.48 KB


Method: $sizer->info($size, '%01.0f %s');

Lab_tpl.php : 7 KB
bench.php : 1 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 3 KB
desain.php : 669 bytes
di.php : 1 KB
faster.php : 3 KB
getmyuid.php : 24 bytes
grep.php : 2 KB
httpreq.php : 3 KB
include.php : 271 bytes
index.php : 8 KB
index_labs.php : 8 KB
index_new.php : 1 KB
indextest.php : 438 bytes
info.php : 1 KB
iofile.php : 2 KB
isfiletest.php : 705 bytes
lite.php : 15 KB
markdown.php : 84 KB
mdsyntax.php : 8 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 101 KB
regexroute.php : 2 KB
run-tests.php : 68 KB
run-tests2.php : 68 KB
statictest.php : 986 bytes
storage.class.php : 2 KB
stream.php : 3 KB
tag11.php : 2 KB
testecho.php : 549 bytes
tracefile-analyser.php : 5 KB
useragent.php : 1 KB


Method: $sizer($size);

Lab_tpl.php : 6.71 KB
bench.php : 1.28 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 2.59 KB
desain.php : 669 bytes
di.php : 1.25 KB
faster.php : 2.76 KB
getmyuid.php : 24 bytes
grep.php : 1.99 KB
httpreq.php : 2.61 KB
include.php : 271 bytes
index.php : 7.59 KB
index_labs.php : 7.69 KB
index_new.php : 1.13 KB
indextest.php : 438 bytes
info.php : 1.26 KB
iofile.php : 1.70 KB
isfiletest.php : 705 bytes
lite.php : 15.16 KB
markdown.php : 84.12 KB
mdsyntax.php : 7.85 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 100.92 KB
regexroute.php : 1.62 KB
run-tests.php : 67.70 KB
run-tests2.php : 67.68 KB
statictest.php : 986 bytes
storage.class.php : 1.52 KB
stream.php : 2.59 KB
tag11.php : 1.61 KB
testecho.php : 549 bytes
tracefile-analyser.php : 4.83 KB
useragent.php : 1.48 KB


Method: $sizer($size, '%01.0f %s');

Lab_tpl.php : 6.71 KB
bench.php : 1.28 KB
cache.php : 502 bytes
controller_test.php : 976 bytes
dcore.php : 2.59 KB
desain.php : 669 bytes
di.php : 1.25 KB
faster.php : 2.76 KB
getmyuid.php : 24 bytes
grep.php : 1.99 KB
httpreq.php : 2.61 KB
include.php : 271 bytes
index.php : 7.59 KB
index_labs.php : 7.69 KB
index_new.php : 1.13 KB
indextest.php : 438 bytes
info.php : 1.26 KB
iofile.php : 1.70 KB
isfiletest.php : 705 bytes
lite.php : 15.16 KB
markdown.php : 84.12 KB
mdsyntax.php : 7.85 KB
obstorage.class.php : 457 bytes
pacindex.php : 180 bytes
phpliteadmin.php : 100.92 KB
regexroute.php : 1.62 KB
run-tests.php : 67.70 KB
run-tests2.php : 67.68 KB
statictest.php : 986 bytes
storage.class.php : 1.52 KB
stream.php : 2.59 KB
tag11.php : 1.61 KB
testecho.php : 549 bytes
tracefile-analyser.php : 4.83 KB
useragent.php : 1.48 KB
/var/www/Labs/chilikcms/libs/webgrind/sizer/unittest/sizertest.php : 1.43 KB
/var/www/Labs/chilikcms/libs/webgrind/sizer/interface/fs.php : 630 bytes
/var/www/Labs/chilikcms/libs/webgrind/sizer/sizer.php : 1005 bytes
/var/www/Labs/chilikcms/libs/webgrind/sizer/version/sizer_v1.php : 1011 bytes

Elapsed: 0.01478 ms and Memory Used: 0
*/

