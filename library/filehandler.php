<?php

namespace WebGrind;
use \Entity;

class FileHandler
{
    private static $instance;
    
    private $files = array();
    
    function instance()
    {
        if( ! empty(self::$instance)) return self::$instance;
        
        return self::$instance = new self;
    }
    
    function __construct()
    {
        $this->files = $this->getFiles(Config::xdebugOutputFormat(), Config::xdebugOutputDir());
        uasort($this->files, array($this, 'compare'));
    }
    
    public function getTraceList()
    {
        $result = array();
        $sizer = new Entity\Sizer;
        
        foreach($this->files as $filename => $file)
        {
            $result [] = array(
                'filename'  => $filename,
                'invokeUrl' => 'xDebug Files',
                'filesize'  => $sizer->info($file['filesize'], '%01.0f %s'),
            );
        }
        
        return $result;
    }
    
    public function getTraceReader($file, $costFormat)
    {
        $prepFile = WebGrind\Config::storageDir().$file.WebGrind\Config::$preprocessedSuffix;
        
        WebGrind\Preprocessor::parse(WebGrind\Config::xdebugOutputDir().$file, $prepFile);
        
        if( ! file_exists($prepFile)) throw new Exception('Cannot find ReaderFile: '.$prepFile);
        
        return new WebGrind\Reader($prepFile, $costFormat);
    }
    
    private function compare($b, $a)
    {
        return strcmp($a['mtime'],$b['mtime']);
    }
    
    private function getFiles($format, $dir)
    {
        $list = preg_grep($format, scandir($dir));
        
        $files = array();
        
        foreach($list as $file)
        {
            if(Config::$hideWebgrindProfiles) continue;
            
            $files[$file] = array(
                'absoluteFilename'  => $dir.$file,
                'mtime'             => filemtime($dir.$file),
                'preprocessed'      => TRUE,
                'invokeURL'         => '',
                'filesize'          => filesize($dir.$file),
            );
        }
        
        return $files;
    }
    
}
