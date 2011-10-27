<?php

namespace WebGrind\Entity;

class IOReadWebGrind 
{
    const ENTRY_POINT   = '{main}';
    
    private $headers     = array();
    private $functions   = array();
    private $function;
    private $nextNr      = 0;
    private $lnr;
    private $fp;
    
    function process($data, $file, $mode = 'rb')
    {
        $this->fp = fopen($file, $mode);
        
        $data = array();
        $this->nextNr = 0;
        
        while(($line = fgets($this->fp)))
        {
            $typ = rtrim(substr($line, 0, 3),'=').'=';
            
            switch($typ)
            {
                case 'fl='  :
                    $this->doFl($this->fp, $line);
                    break;
                    
                case 'cfn=' :
                    $this->doFn($this->fp, $line);
                    break;
                    
                default:
                    $this->doDef($line);
                    break;
                    
            }
            
            unset($line);
        }
        
        fclose($this->fp);
        return array('headers' => $this->headers, 'functions' => $this->functions);
    }
    
    private function doFl(& $fp, $line)
    {
        list($function) = fscanf($fp, 'fn=%s');
        
        if( ! isset($this->functions[$function]))
        {
            $this->functions[$function] = array(
                        'filename'              => substr(trim($line), 3),
                        'invocationCount'       => 0,
                        'line'                  => -1,
                        'nr'                    => ++$this->nextNr,
                        'count'                 => 0,
                        'summedSelfCost'        => 0,
                        'summedInclusiveCost'   => 0,
                        'calledFromInformation' => array(),
                        'subCallInformation'    => array(),
                    );
        }
        
        ++$this->functions[$function]['invocationCount'];
        
        if(self::ENTRY_POINT === $function)
        {
            fgets($fp);
            $this->headers [] = fgets($fp);
            fgets($fp);
        }
        
        list($lnr, $cost) = fscanf($fp, '%d %d');
        
        $this->functions[$function]['summedSelfCost'] += $cost;
        $this->functions[$function]['summedInclusiveCost'] += $cost;
    }
    
    private function doFn(& $fp, $line)
    {
        $cfn = substr(trim($line), 4);
	    
	    fgets($fp);
	    
	    list($lnr, $cost) = fscanf($fp, '%d %d');
	    
	    $this->functions[$function]['summedInclusiveCost'] += $cost;
	    
	    if( ! isset($functions[$cfn]['calledFromInformation'][$this->function.':'.$lnr]) )
	    {
	        $this->functions[$cfn]['calledFromInformation'][$function.':'.$lnr] = array(
	                'functionNr'        => $this->functions[$function]['nr'],
	                'line'              => $lnr,
	                'callCount'         => 0,
	                'summedCallCost'    => 0,
	            );
	    }
	    
        ++$this->functions[$cfn]['calledFromInformation'][$function.':'.$lnr]['callCount'];
        $this->functions[$cfn]['calledFromInformation'][$function.':'.$lnr]['summedCallCost'] += $cost;
        
        if( ! isset($this->functions[$function]['subCallInformation'][$cfn.':'.$lnr]) )
        {
            $this->functions[$function]['subCallInformation'][$cfn.':'.$lnr] = array(
                    'functionNr' => $functions[$cfn]['nr'],
                    'line'              => $lnr,
	                'callCount'         => 0,
	                'summedCallCost'    => 0,
                );
        }
	    
	    ++$this->functions[$function]['subCallInformation'][$cfn.':'.$lnr]['callCount'];
        $this->functions[$function]['subCallInformation'][$cfn.':'.$lnr]['summedCallCost'] += $cost;
    }
    
    private function doDef($line)
    {
        if(stripos($line, ': ') !== FALSE) $this->headers [] = $line;
    }
} 
