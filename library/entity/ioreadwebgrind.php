<?php

namespace WebGrind\Entity;

class IOReadWebGrind 
{
    const ENTRY_POINT   = '{main}';
    
    private $headers     = array();
    private $functions   = array();
    private $nextNr      = 0;
    
    function process($data, $file, $mode = 'rb')
    {
        $fp = fopen($file, $mode);
        
        $data = array();
        $this->nextNr = 0;
        
        while(($line = fgets($fp)))
        {
            $typ = rtrim(substr($line, 0, 3),'=').'=';
            
            if($typ === 'fl=') { $function = $this->doFl($fp, $line); }
            else if($typ === 'cfn=') { $this->doFn($fp, $line, $function); }
            else if(strpos($line,': ')!==false) { $this->doDef($line); }
            
            /*
            switch($typ)
            {
                case 'fl='  :
                    $function = $this->doFl($fp, $line);
                    break;
                    
                case 'cfn=' :
                    $this->doFn($fp, $line, $function);
                    break;
                    
                default:
                    $this->doDef($line);
                    break;
                    
            }
            
            unset($line);
            */
        }
        
        fclose($fp);
        return array('headers' => $this->headers, 'functions' => $this->functions);
    }
    
    private function doFl($fp, $line)
    {
        list($function) = fscanf($fp, 'fn=%s');
        
        if( ! isset($this->functions[$function]))
        {
            $this->functions[$function] = array(
                        'filename'              => substr(trim($line), 3),
                        'functionName'          => $function,
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
        
        $this->functions[$function]['linenumber'] = $lnr;
        $this->functions[$function]['summedSelfCost'] += $cost;
        $this->functions[$function]['summedInclusiveCost'] += $cost;
        
        return $function;
    }
    
    private function doFn($fp, $line, $function)
    {
        $cfn = substr(trim($line), 4);
        
	    fgets($fp);
	    
	    list($lnr, $cost) = fscanf($fp, '%d %d');
	    
	    $this->functions[$function]['summedInclusiveCost'] += $cost;
	    
	    if( ! isset($this->functions[$cfn]['calledFromInformation'][$function.':'.$lnr]) )
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
                    'functionNr' => $this->functions[$cfn]['nr'],
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
