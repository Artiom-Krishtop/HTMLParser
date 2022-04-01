<?php

namespace Utilits;

require_once 'utilits/autoloader.php';

class Helper{

   private const DIR_FILES_LIST_SITES = '/sites_lists/'; 

   public static function getSitesURLList()
   {
      $sitesList = [];

      $dir = ROOT. self::DIR_FILES_LIST_SITES;

      if (!file_exists($dir)) {
         mkdir($dir);
      }

      $files = scandir($dir);

      unset($files[0], $files[1]);

      foreach ($files as $file) {

         $filePath = $dir . $file;

         $sitesList = array_merge($sitesList, explode("\n",file_get_contents($filePath)));
      }

      foreach ($sitesList as &$url) {
         
         if(!preg_match('/(http|www)/', $url)){

            $url = 'https://'.$url .'/';
         }
      }

      unset($url);
      
      return $sitesList;
   }
}