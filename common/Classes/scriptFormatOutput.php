<?php

/**
 * Class to format the output showed by the scripts.
 * Default utilities like progressBars, colorize output and echo&log can be achieved easily with this class.
 */
class scriptFormatOutput {
    const NONE = 0;
    const SUCCESS = 1;
    const FAILURE = 2;
    const WARNING = 3;
    const NOTE = 4;
    
    public function colorize($text, $status) {
        $out = "";
        switch($status) {
            case scriptFormatOutput::SUCCESS:
                $out = "[42m"; //Green background
                break;
            case scriptFormatOutput::FAILURE:
                $out = "[41m"; //Red background
                break;
            case scriptFormatOutput::WARNING:
                $out = "[43m"; //Yellow background
                 break;
            case scriptFormatOutput::NOTE:
                 $out = "[44m"; //Blue background
                 break;
            case scriptFormatOutput::NONE:
            default:
                return $text;
        }

        return chr(27) . "$out" . "$text" . chr(27) . "[0m";
    }
    
    public function CLI_echo($message, $status = scriptFormatOutput::NONE, $breakSpace = TRUE){
        echo scriptFormatOutput::colorize($message, $status) . ($breakSpace===TRUE? "\n": "");
    }
    
    public function CLI_echoAndLog($message, $file, $trace = "logGlb", $status = scriptFormatOutput::NONE, $breakSpace = TRUE){
        echo scriptFormatOutput::CLI_echo($message, $status, $breakSpace);
        utils::log($message, $file, $trace);
        
        if($status == scriptFormatOutput::FAILURE){
            utils::log($message, "logError", $trace);        
        }
    }
    
    public function CLI_progressBAR($status, $progressText = ""){
        $status_round = round($status, 2);
        
        echo "\r";
        $progressBar = "[ ";
        $progress_length = 25;
        for($progress = 0; $progress < $progress_length; $progress++){
            $progressBar .= (($progress < ($status_round/(100/$progress_length)))? "#" : " ");
        }
        $progressBar .= " ]";
        
        scriptFormatOutput::CLI_echo("{$progressText} \t {$progressBar} ({$status_round} %)   ", scriptFormatOutput::NOTE, FALSE);        
        scriptFormatOutput::CLI_show();        
    }
    
    public function CLI_echoSpace(){
        echo "\n";
    }
    
    public function CLI_promp(){
        $handle = fopen ("php://stdin","r");
        $line = fgets($handle);
        fclose($handle);
        $line = trim($line);
        return $line;
    }
    
    public function CLI_show(){
        ob_flush();
        flush();
    }
    
    public function CLI_getTime(){
        return microtime(true);        
    }
    
    public function CLI_getTimeScript($time_start, $time_end){
        return bcsub($time_end, $time_start, 4);
    }
}
