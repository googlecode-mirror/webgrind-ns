<?php

namespace WebGrind;

class Preprocessor
{
    public static function parse($fin, $fout)
    {
        if( ! file_exists($fin)) throw new Exception('Cannot find the file: '. $fin);
        
        $read = new WGRead($fin);
        $result = $read->doRead();
            
        $write = new WGWrite($fout, $result);
        $write->doWrite();
        
        unset($read, $write);
    }
}
