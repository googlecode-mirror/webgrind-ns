<?php

namespace WebGrind\Entity;

class IOWrite implements WGFileSpec
{
    function process($data, $file, $mode = 'wb')
    {
        $fp = fopen($file, $mode);
        $this->doWrite($fp, $data);
        fclose($fp);
        
        unset($fp);
    }
    
    private function writestatpack($fp, $data)
    {
        fwrite($fp, pack(
                    
                    self::NR_FORMAT.'*', 
                    $data['summedSelfCost'], 
                    $data['summedInclusiveCost'], 
                    $data['invocationCount'],
                    sizeof($data['calledFromInformation']),
                    sizeof($data['subCallInformation'])
                    
                ));
    }
    
    private function writeinfopack($fp, $data)
    {
        fwrite($fp, pack(
                    
                    self::NR_FORMAT.'*', 
                    $data['functionNr'], 
                    $data['line'], 
                    $data['callCount'],
                    $data['summedCallCost']
                    
                ));
    }
    
    private function writefilename($fp, $file, $filename)
    {
        fwrite($fp, $file."\n".$filename."\n");
    }
    
    private function writeheaders($fp, $headers)
    {
        foreach($headers as $val) { fwrite($fp, $val); }
    }
    
    private function writeaddress($fp, $functionAddress)
    {
        foreach($functionAddress as $address) { fwrite($fp, pack(self::NR_FORMAT, $address)); }
    }
    
    private function doWrite($fp, $data)
    {
        $functions = $data['functions'];
        
        $functionAddress = array(); 
        $functionCount = sizeof($functions);
        
        fwrite($fp, pack(self::NR_FORMAT.'*', self::WG_VERSION, 0, $functionCount));
        fseek($fp, self::NR_SIZE * $functionCount, SEEK_CUR);
        
        foreach($functions as $functionName => $function)
        {
            $functionAddress [] = ftell($fp);
            
            $this->writestatpack($fp, $function);
            
            foreach($function['calledFromInformation'] as $call)
            {
                $this->writeinfopack($fp, $call);
            }
            
            foreach($function['subCallInformation'] as $call)
            {
                $this->writeinfopack($fp, $call);
            }
            
            $this->writefilename($fp, $function['filename'], $functionName);
        }
        
        $header = ftell($fp);
        $this->writeheaders($fp, $data['headers']);
        
        fseek($fp, self::NR_SIZE, SEEK_SET);
        fwrite($fp, pack(self::NR_FORMAT, $header));
        fseek($fp, self::NR_SIZE, SEEK_CUR);
        
        $this->writeaddress($fp, $functionAddress);
    }
}
