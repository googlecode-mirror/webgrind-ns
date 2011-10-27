<?php

namespace WebGrind\Entity;
use \WebGrind as WG, \Entity;

class WGReader
{
    private $file;
    private $cache;
    private $result;
    
    private $costFormat;
    
    private $headers;
    private $functions;
    
    function __construct($file) { $this->file = $file; $this->cache = file_exists($file.'.serial'); }
    
    function doRead()
    {
        if($this->cache)
        {
            $read = new Entity\IORead;
            $result = unserialize($read->process('',$this->file.'.serial','rb'));
        }
        else
        {
            $read = new IORead;
            $result = $read->process('', $this->file, 'rb');
        }
        
        $this->headers = NULL;
        
        $this->result = $result;
        $this->headers = $this->getHeaders();
        $this->functions = $this->getFunctions();
        
        $this->costFormat = (empty($this->costFormat))? WG\Config::$defaultCostFormat : $this->costFormat;
        
        return $result;
    }
    
    function setcostFormat($format) { $this->costFormat = $format; }
    function getcostFormat()        { return $this->costFormat; }
    
    function getFunctionCount()     { return sizeof($this->functions); }
    
    function formatCost($cost, $format) 
    {
        if(empty($format)) $format = WG\Config::$defaultCostFormat;
        
        switch($format)
        {
            case 'percent':
                return $this->getPercent($cost);
                
            case 'msec':
                return round(($cost / 1000), 0);
                
            default:
                return $cost;
        }
    }
    
    function getHeaders() 
    {
        if( ! empty($this->headers)) return $this->headers;
        
        $headers = $this->result['headers'];
        $tamp = array();
        
        foreach($headers as $val)
        {
            $tmp = explode(':', $val);
            $tamp[$tmp[0]] = trim($tmp[1]);
        }
        
        return $this->headers = $tamp;
    }
    
    function getFunctions()
    {
        if( ! empty($this->functions) ) return $this->functions;
        
        $this->functions[0] = 'Init';
        $functions = $this->result['functions'];
        foreach($functions as $key => &$val)
        {
            $this->functions [] = array($val);
        }
        
        return $this->functions;
    }
    
    function getHeader($header) 
    {
        return isset($this->headers[$header]) ? $this->headers[$header] : '';
    }
    
    function getFunctionInfo($Nr) 
    {
        $data = $this->functions[$Nr];
        $fn = 0;
        
        $result = array(
            'file'                  => $data[$fn]['filename'],
            'functionName'          => $data[$fn]['functionName'],
            'summedSelfCost'        => $this->formatCost($data[$fn]['summedSelfCost'], $this->costFormat),
            'summedInclusiveCost'   => $this->formatCost($data[$fn]['summedInclusiveCost'], $this->costFormat),
            'invocationCount'       => $data[$fn]['invocationCount'],
            'calledFromInfoCount'   => sizeof($data[$fn]['calledFromInformation']),
            'subCallInfoCount'      => sizeof($data[$fn]['subCallInformation']),
            'nr'                    => $data[$fn]['nr'],
            'line'                  => $data[$fn]['line'],
            'linenumber'            => $data[$fn]['linenumber'],
        );
        
        return $result;
    }
    
    private function seekInfo($data, $Nr)
    {
        $count = 0;
        foreach($data as $key => $val)
        {
            if($count === $Nr) { $data = $val; break; }
            ++$count;
        }
        
        return $data;
    }
    
    private function getInfo($typ, $functionNr, $Nr)
    {
        $fn = 0;
        
        switch($typ)
        {
            case 'calledfrom':
                $data = $this->functions[$functionNr][$fn]['calledFromInformation'];
                break;
                
            case 'subcall':
                $data = $this->functions[$functionNr][$fn]['subCallInformation'];
                break;
        }
        
        $data = $this->seekInfo($data, $Nr);
        
        $result = array(
	        'functionNr'        => $data['functionNr'], 
	        'line'              => $data['line'], 
	        'callCount'         => $data['callCount'], 
	        'summedCallCost'    => $this->formatCost($data['summedCallCost'], $this->costFormat)
	    );
	    
	    return $result;
    }
    
    function getCalledFromInfo($functionNr, $calledFromNr) 
    {
        return $this->getInfo('calledfrom', $functionNr, $calledFromNr);
    }
        
    function getSubCallInfo($functionNr, $subCallNr) 
    {
        return $this->getInfo('subcall', $functionNr, $subCallNr);
    }

    private function getPercent($cost)
    {
        $total = $this->getHeader('summary');
        $result = ($total === 0)? 0 : ($cost/$total) * 100; 
        
        return number_format($result, 2, '.', '');
    }
    
    function __destruct() { unset($this->file, $this); }
}
