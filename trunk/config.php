<?php

namespace WebGrind;

class Config
{
    static $checkVersion                    = TRUE;
	static $hideWebgrindProfiles            = FALSE;
	
	static $storageDir                      = '/your/webgrind/data/here';
	static $profilerDir                     = '/your/xdebug/data/here';
	
	static $preprocessedSuffix              = '.webgrind';
	
	static $defaultTimezone                 = 'Asia/Jakarta';
	static $dateFormat                      = 'Y-m-d H:i:s';
	static $defaultCostFormat               = 'msec'; // 'percent', 'usec' or 'msec'
	static $defaultFunctionPercentage       = 90;
	static $defaultHideInternalFunctions    = FALSE;
	
	static $fileUrlFormat                   = 'index.php?op=fileviewer&file=%1$s&line=%2$d'; // Built in fileviewer
	
	static $webgrindVersion                 = '1.1';
	
	static function xdebugOutputFormat() { return '/^cachegrind\.out\..+$/'; }
	
	static function xdebugOutputDir() { return realpath(self::$profilerDir).DS; }
	static function storageDir() { return realpath(self::$storageDir).DS; }
}
