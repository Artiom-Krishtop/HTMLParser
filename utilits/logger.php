<?php

namespace Utilits;

require_once ROOT.'/utilits/autoloader.php';

class Logger{

    protected const DIR_TO_LOG = '/logs/log/';
    protected const DIR_TO_ERROR_LOG = '/logs/error_log/';

    public static function log($text)
    {
        $textLog = date("H:i:s") . "\n" . $text . "\n***********************************************\n";

        $filePath = self::getFilePath(self::DIR_TO_LOG);

        echo $textLog;
        file_put_contents($filePath, $textLog, FILE_APPEND);
    }

    protected static function getFilePath($dir)
    {
        $filePath = ROOT . $dir;
        $filePath.= date('Y') . '/';

        self::checkDir($filePath);

        $filePath.= date('F_j') . '.txt';

        return $filePath;
    }

    protected static function checkDir($dir)
    {
        if(!file_exists($dir))
        {
            mkdir($dir, 0777, true);    
        }
    }

    public static function errorLog($type, $text, $url = '-')
    {
        $textLog = date("H:i:s") . "\nType : $type ;Url : $url;Error :$text\n***********************************************\n";

        $filePath = self::getFilePath(self::DIR_TO_ERROR_LOG);

        echo $textLog;
        file_put_contents($filePath, $textLog, FILE_APPEND);

    }
}