<?php

/*
 *  This is a source written from scratch with look and feel still 
 *  like the Original author: Joakim Nygård and Jacob Oettinger.
 *
 *  This Web Application depends on PHP Extension, xDebug, 
 *  source written by: Derick Rethans. 
 *
 *  WebGrind-Ns is rewritten from scratch by me, Suwandi Tan 
 *  from Indonesia for Chilik Framework
 *
 *  Copyright (C) 2011  Suwandi Tan
 *
 *  WebGrind-Ns is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  WebGrind-Ns is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 
namespace WebGrind;
use WebGrind\Entity as WGEntity;

define('DS', DIRECTORY_SEPARATOR);
define('DOCROOT', realpath(dirname(__FILE__)).DS);

class App
{
    private static $start;
    private static $mem;
    private static $finish;
    private static $endmem;
    
    public static function start()
    {
        if(version_compare(PHP_VERSION, '5.3', '<')) throw new Exception('WebGrind-Ns needs PHP version 5.3 or greater. Current version of your PHP is: '.PHP_VERSION);
        
        if (ini_get('date.timezone') == '') date_default_timezone_set( Config::$defaultTimezone );
        
        self::$start    = microtime(TRUE);
        self::$mem      = memory_get_peak_usage();
        
        ob_start();
    }
    
    public static function run()
    {
        $op = isset($_GET['op'])? $_GET['op'] : 'default';
        switch($op)
        {
            case 'file_list':
                static::doFileList();
                break;
                
            case 'function_list':
                static::doFunctionList();
                break;
            
            case 'callinfo_list':
                static::doCallInfoList();
                break;
            
            case 'fileviewer':
                static::doViewFile();
                break;
            
            case 'version':
                static::doGetVersion();
                break;
            
            default:
                static::doDefault();
                break;
        }
    }
    
    public static function stop()
    {
        self::$finish       = round((microtime(TRUE) - self::$start), 5);
        self::$endmem       = (memory_get_peak_usage() - self::$mem) / 1024;
        
        ob_end_flush();
    }
    
    private static function doFileList()
    {
        echo json_encode(FileHandler::instance()->getTraceList());
    }
    
    private static function doFunctionList()
    {
        $dataFile = @$_GET['dataFile'];
        $costFormat = isset($_GET['costFormat'])? $_GET['costFormat'] : Config::$defaultCostformat;
        
        if($dataFile === '0')
        {
            $file = FileHandler::instance()->getTraceList();
            $dataFile = $file[0]['filename'];
        }
        
        $reader = new WGEntity\WGReader(Config::$storageDir.DS.$dataFile);
        $reader->setCostFormat($costFormat);
        $reader->doRead();
        
        $functions = array();
        $shownTotal = 0;
        
        $breakdown = array('internal' => 0, 'user' => 0, 'class' => 0, 'include' => 0);
        
        for($i=1, $max=$reader->getFunctionCount() ; $i<$max; ++$i)
        {
            $functionInfo = $reader->getFunctionInfo($i);
            
            if (strpos($functionInfo['functionName'], 'php::') !== FALSE) 
            {
		        $breakdown['internal'] += $functionInfo['summedSelfCost'];
		        $humanKind = 'internal';
		        $kind = 'blue';
		    } 
		    elseif (false !== strpos($functionInfo['functionName'], 'require_once::') ||
    		          false !== strpos($functionInfo['functionName'], 'require::') || 
    		          false !== strpos($functionInfo['functionName'], 'include_once::') ||
    		          false !== strpos($functionInfo['functionName'], 'include::')) 
    		{
                $breakdown['include'] += $functionInfo['summedSelfCost'];
		        $humanKind = 'include';
		        $kind = 'grey';
		    } 
		    else 
		    {
		        if (false !== strpos($functionInfo['functionName'], '->') || false !== strpos($functionInfo['functionName'], '::')) 
		        {
		            $breakdown['class'] += $functionInfo['summedSelfCost'];
    		        $humanKind = 'class';
    		        $kind = 'green';
		        } 
		        else 
		        {
		            $breakdown['user'] += $functionInfo['summedSelfCost'];
		            $humanKind = 'procedural';
    		        $kind = 'orange';
    		    }
            }
            
			if (!(int) $_GET['hideInternals'] || strpos($functionInfo['functionName'], 'php::') === false) 
			{
    			$shownTotal += $functionInfo['summedSelfCost'];
				$functions[$i] = $functionInfo;
    			$functions[$i]['nr'] = $i;
				$functions[$i]['kind'] = $kind;
				$functions[$i]['humanKind'] = $humanKind;
    		} 

        }
        
        usort($functions, function ($a, $b)
                {
	                $a = $a['summedSelfCost'];
	                $b = $b['summedSelfCost'];

	                if ($a == $b) { return 0; }
	                
	                return ($a > $b) ? -1 : 1;
                });
		
		$remainingCost = $shownTotal * $_GET['showFraction'];
		
		$result['functions'] = array();
		
		foreach($functions as $function)
		{
			$remainingCost -= $function['summedSelfCost'];
						
			$result['functions'][] = $function;
			if($remainingCost < 0) break;
		}
		
		$result['summedInvocationCount'] = $reader->getFunctionCount();
        $result['summedRunTime'] = $reader->formatCost($reader->getHeader('summary'), 'msec');
		$result['dataFile'] = $dataFile;
		$result['invokeUrl'] = $reader->getHeader('cmd');
		$result['runs'] = $reader->getHeader('runs');
		$result['breakdown'] = $breakdown;
		$result['mtime'] = date(Config::$dateFormat,filemtime(Config::xdebugOutputDir().$dataFile));
		
		echo json_encode($result);
	}
    
    private static function doCallInfoList()
    {
        $file = @$_GET['file'];
        $functionNr = @$_GET['functionNr'];
        $costFormat = isset($_GET['costFormat'])? $_GET['costFormat'] : Config::$defaultCostformat;
        
        $reader = new WGEntity\WGReader(Config::$storageDir.DS.$file);
        $reader->setCostFormat($costFormat);
        $reader->doRead();
        
        $function = $reader->getFunctionInfo($functionNr);
        
        $result = array('calledFrom' => array(), 'subCalls' => array());
        $foundInvocations = 0;
        
        for($i=0, $max = $function['calledFromInfoCount']; $i < $max; ++$i)
        {
            $invocation = $reader->getCalledFromInfo($functionNr, $i);
            
            $foundInvocations += $invocation['callCount'];
            $callerInfo = $reader->getFunctionInfo($invocation['functionNr']);
            
            $invocation['file'] = $callerInfo['file'];
            $invocation['callerFunctionName'] = $callerInfo['functionName'];
            $result['calledFrom'][] = $invocation;
        }
        
        $result['calledByHost'] = ($foundInvocations<$function['invocationCount']);
		
		for($i=0, $max = $function['subCallInfoCount']; $i < $max; ++$i)
		{
			$invocation = $reader->getSubCallInfo($functionNr, $i);
			$callInfo = $reader->getFunctionInfo($invocation['functionNr']);
			$invocation['file'] = $function['file']; 
			$invocation['callerFunctionName'] = $callInfo['functionName'];
			$result['subCalls'][] = $invocation;
		}
		
		echo json_encode($result);
    }
    
    private static function doViewFile()
    {
        $file = @$_GET['file'];
        $line = @$_GET['line'];
        
        if($file AND ! empty($file))
        {
	        $message = '';
	        
	        if( ! file_exists($file))
	        {
		        $message = $file.' does not exist.';
	        } 
	        else if( ! is_readable($file))
	        {
		        $message = $file.' is not readable.';
		    } 
		    else if(is_dir($file))
		    {
		        $message = $file.' is a directory.';
	        } 		
        } 
        else 
        {
	        $message = 'No file to view';
        }
        
        require 'templates/fileviewer.phtml';
    }
    
    private static function doGetVersion()
    {
        $response = @file_get_contents('http://jokke.dk/webgrindupdate.json?version='.Config::$webgrindVersion);	
        echo $response;
    }
    
    private static function doDefault()
    {
        require_once 'templates'.DS.'index.phtml';
    }
}
