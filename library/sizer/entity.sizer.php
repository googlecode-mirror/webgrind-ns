<?php

namespace Entity;
use \Base;

class Sizer implements Base\FS
{
    const KILO = 1024;
    
    private static $version = '1.1';
    
    public static function info($size, $format = NULL)
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
        $total = count($sizes);

		while($total-- AND $size > self::KILO) 
		{
		    $size /= self::KILO;
		}
        
        $index = count($sizes) - $total - 1;
        if ($index == 0) { $format = '%01d %ss'; }
	    return sprintf($format, $size, $sizes[$index]);
    }
    
    /*
    function __invoke()
    {
        $args = func_get_args();
        return $this->info($args[0]);
    }
    */
}
