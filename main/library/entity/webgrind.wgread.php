<?php

namespace WebGrind;
use WebGrind\Entity as WGEntity;

class WGRead
{
    private $file;
    private $cache;
    
    function __construct($file) { $this->file = $file; $this->cache = file_exists($file.'.serial'); }
    
    function doRead()
    {
        if($this->cache)
        {
            $read = new WGEntity\IORead;
            $result = unserialize($read->process('',$this->file.'.serial','rb'));
        }
        else
        {
            $read = new WGEntity\IOReadWebGrind;
            $result = $read->process('', $this->file, 'rb');
            
            $write = new WGEntity\IOWrite;
            $write->process(serialize($result), $this->file.'.serial', 'wb');
        }
        
        return $result;
    }
    
    function __destruct() { unset($this->file, $this); }
}
