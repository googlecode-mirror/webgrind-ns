<?php

namespace WebGrind\Entity;

class IORead
{
    const CHUNK = 65536;
    
    function process($data, $file, $mode = 'rb')
    {
        $fp = fopen($file, $mode);
        while ( ! feof($fp)) { $data .= fread($fp, self::CHUNK); }
        
        fclose($fp);
        unset($fp);
        return $data;
    }
}
