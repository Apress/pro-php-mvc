<?php

class Logger
{
    protected $_file;
    protected $_entries;
    protected $_start;
    protected $_end;
    
    protected function _sum($values)
    {
        $count = 0;
        
        foreach ($values as $value)
        {
            $count += $value;
        }
        
        return $count;
    }
    
    protected function _average($values)
    {
        return $this->_sum($values) / sizeof($values);
    }
    
    public function __construct($options)
    {
        if (!isset($options["file"]))
        {
            throw new Exception("Log file invalid.");
        }
        
        $this->_file = $options["file"];
        $this->_entries = array();
        $this->_start = microtime();
    }
    
    public function log($message)
    {
        $this->_entries[] = array(
            "message" => "[" . date("Y-m-d H:i:s") . "]" . $message,
            "time" => microtime()
        );
    }
    
    public function __destruct()
    {
        $messages = "";
        $last = $this->_start;
        $times = array();
        
        foreach ($this->_entries as $entry)
        {
            $messages .= $entry["message"] . "\n";
            $times[] = $entry["time"] - $last;
            $last = $entry["time"];
        }
        
        $messages .= "Average: " . $this->_average($times);
        $messages .= ", Longest: " . max($times);
        $messages .= ", Shortest: " . min($times);
        $messages .= ", Total: " . (microtime() - $this->_start);
        $messages .= "\n";
        
        file_put_contents($this->_file, $messages, FILE_APPEND);
    }
}