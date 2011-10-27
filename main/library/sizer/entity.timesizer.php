<?php

namespace Entity;
use \Base;

class TimeSizer implements Base\TS
{
    public function info($time, $viewpoint = 'ms', $round = 5)
    {
        $ret = $time;
        if(stripos(' ', $time) > 0) 
        {
            $ret = explode(' ', $time);
            $ret = float($ret[1]); + float($ret[0]);
        }
        
        $formats = array(
                        self::US, 
                        self::MS, 
                        self::S,
                        self::M,
                        self::H,
                 );
        $multiplier = 1;
        switch($viewpoint)
        {
            case self::US:
                $multiplier = 1000000;
                $formatter = 0;
                break;
                
            case self::MS:
                $multiplier = 1000;
                $formatter = 1;
                break;
                
            case self::S:
                $multiplier = 1;
                $formatter = 2;
                break;
                
            case self::M:
                $multiplier = 1/60;
                $formatter = 3;
                break;
            
            case self::H:
                $multiplier = 1/(60 * 60);
                $formatter = 4;
                break;
                
            default:
                break;
        }
        
        $ret = $ret * $multiplier;
        return round($ret, $round) . ' ' . $formats[$formatter];
		
	}
    
    function __invoke()
    {
        $args = func_get_args();
        return $this->info($args[0], isset($args[1])? $args[1] : 'ms');
    }
}


