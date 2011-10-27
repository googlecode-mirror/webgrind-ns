<?php

namespace Entity;
use \Base;

class Sizer_v1 implements Base\FS
{
    private static $version = '1.0';
    
    public function info($size, $format = NULL)
    {
        $sizes = array(
                        self::BYTE, 
                        self::KB, 
                        self::MB, 
                        self::GB, 
                        self::TB, 
                        self::PB, 
                        self::EB, 
                        self::ZB, 
                        self::YB
                 );
        
	    if ($format === null) { $format = '%01.2f %s'; }

		$lastsizestring = end($sizes);

		foreach ($sizes as $sizestring) 
		{
	        if ($size < 1024) { break; }
	        if ($sizestring != $lastsizestring) { $size /= 1024; }
	    }
	    
	    if ($sizestring == $sizes[0]) { $format = '%01d %ss'; }
	    return sprintf($format, $size, $sizestring);
    }
    
    function __invoke()
    {
        $args = func_get_args();
        return $this->info($args[0]);
    }
}
