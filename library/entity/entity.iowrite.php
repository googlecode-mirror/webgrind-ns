<?php

namespace Entity;

class IOWrite
{
    function process($data, $file, $mode = 'wb')
    {
        $fp = fopen($file, $mode);
        
        fwrite($fp, $data);
        fclose($fp);
        
        unset($fp);
        
        return;
    }
}
