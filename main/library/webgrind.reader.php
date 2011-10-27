<?php

namespace WebGrind;
use WebGrind\Entity as WGEntity;

class Reader implements WGEntity\WGFileSpec
{   
    private $headers;
    private $costFormat;
    
    private $headersPos;
    private $functionsPos;
    
    function __construct($dataFile, $costFormat)
    {
        $this->fp           = fopen($dataFile,'rb');
        $this->costFormat   = $costFormat;
        
        $this->init();
    }
    
    function getFunctionCount()
    {
        return count($this->functionsPos);
    }
    
    function formatCost($cost, $format = NULL)
	{
		if($format === NULL) $format = $this->costFormat;
			
	    if ($format == 'percent') 
	    {
	        $total = $this->getHeader('summary');
    		$result = ($total === 0) ? 0 : ($cost * 100)/$total;
    		
    		return number_format($result, 2, '.', '');
	    } 
		
		if ($format === 'msec') 
		{
			return round($cost/1000, 0);
	    }

	    return $cost;
	}
	
	function getHeader($header)
	{
		$headers = $this->getHeaders();
		return isset($headers[$header]) ? $headers[$header] : '';
	}
	
	function getHeaders()
	{
		if($this->headers === NULL)
		{ 
			$this->seek($this->headersPos);
			
			$this->headers['runs'] = 0;
			
			while($line = $this->readLine())
			{
				$parts = explode(': ',$line);
				
				if ($parts[0] == 'summary') 
				{
				    $this->headers['runs']++;
				    
				    if(isset($this->headers['summary'])) { $this->headers['summary'] += $parts[1]; }
                    else { $this->headers['summary'] = $parts[1]; }
                    
				} else { $this->headers[$parts[0]] = $parts[1]; }
			}
		}
		
		return $this->headers;
	}
	
	function getSubCallInfo($functionNr, $subCallNr)
	{
		$this->seek($this->functionPos[$functionNr] + self::NR_SIZE * 3);
		
		$calledFromInfoCount = $this->read();
		
		$this->seek( ( ($calledFromInfoCount + $subCallNr) * self::CALLINFORMATION_LENGTH + 1 ) * self::NR_SIZE, SEEK_CUR);
		
		$data = $this->read(self::CALLINFORMATION_LENGTH);

	    $result = array(
	        'functionNr'=>$data[0], 
	        'line'=>$data[1], 
	        'callCount'=>$data[2], 
	        'summedCallCost'=>$data[3]
	    );
		
        $result['summedCallCost'] = $this->formatCost($result['summedCallCost']);

		return $result;
	}
	
	function getCalledFromInfo($functionNr, $calledFromNr)
	{
		$this->seek($this->functionPos[$functionNr] + self::NR_SIZE * (self::CALLINFORMATION_LENGTH * $calledFromNr + 5));
		
		$data = $this->read(self::CALLINFORMATION_LENGTH);

	    $result = array(
	        'functionNr'=>$data[0], 
	        'line'=>$data[1], 
	        'callCount'=>$data[2], 
	        'summedCallCost'=>$data[3]
	    );
		
        $result['summedCallCost'] = $this->formatCost($result['summedCallCost']);

		return $result;
	}
	
	function getFunctionInfo($nr)
	{
		$this->seek($this->functionPos[$nr]);
		
		list($summedSelfCost, $summedInclusiveCost, $invocationCount, $calledFromCount, $subCallCount) = $this->read(5);
		
		$this->seek(self::NR_SIZE * self::CALLINFORMATION_LENGTH * ($calledFromCount + $subCallCount), SEEK_CUR);
		
		$file = $this->readLine();
		
		$function = $this->readLine();

	   	$result = array(
    	    'file'=>$file, 
   		    'functionName'=>$function, 
   		    'summedSelfCost'=>$summedSelfCost,
   		    'summedInclusiveCost'=>$summedInclusiveCost, 
   		    'invocationCount'=>$invocationCount,
			'calledFromInfoCount'=>$calledFromCount,
			'subCallInfoCount'=>$subCallCount
   		);
   		
        $result['summedSelfCost'] = $this->formatCost($result['summedSelfCost']);
        
        $result['summedInclusiveCost'] = $this->formatCost($result['summedInclusiveCost']);

		return $result;
	}
	
    private function init()
    {
        list($version, $this->headersPos, $functionCount) = $this->read(3);
		
		if($version!=self::FILE_FORMAT_VERSION) 
		{ throw new Exception('Datafile not correct version. Found '.$version.' expected '.self::WG_VERSION); }
		
		$this->functionsPos = $this->read($functionCount);		
    }
    
    private function read($numbers = 1)
    {
		$values = unpack(self::NR_FORMAT.$numbers, fread($this->fp, self::NR_SIZE * $numbers));
		
		if($numbers == 1)
			return $values[1];
		else 
			return array_values($values); // reindex and return
	}
	
	private function readLine()
	{
		$result = fgets($this->fp);
		
		if($result)
			return trim($result);
		else
			return $result;
	}
	
	private function seek($offset, $whence=SEEK_SET)
	{
		return fseek($this->fp, $offset, $whence);
	}
}
