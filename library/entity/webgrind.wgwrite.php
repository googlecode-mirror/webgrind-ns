<?php

namespace WebGrind;
use WebGrind\Entity as WGEntity;

class WGWrite
{
    function __construct($file, $data)
    {
        $this->file = $file;
        $this->data = $data;
        
        unset($data);
    }
    
    function doWrite()
    {
        $write = new WGEntity\IOWriteWebGrind;
        $write->process($this->data, $this->file, 'w+b');
    }
    
    function __destruct() { unset($this->data, $this->file, $this); }
}
