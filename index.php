#!/usr/bin/php
<?php

require_once 'utilits/autoloader.php';

use Configs\DataBaseConfig;
use ORM\SiteInfoSQL;
use Utilits\Logger;
use Utilits\Helper;
use Utilits\Parser;

set_time_limit(0);

$siteInfoTable = new SiteInfoSQL(DataBaseConfig::GetConfig(), 'site_info_list');

// $siteInfoTable->deleteTable();
// dd($siteInfoTable->get());
// die();

$urlList = Helper::getSitesURLList();

$data = [];
$counter = 0;

// foreach ($urlList as $url) {
   
//    $counter++;
//    $data[] = [$url];

//    if ($counter == 100) {
      
//       $siteInfoTable->add($data, ['SITE_NAME']);
//       $data = [];
//       $counter = 0;
//    }
// }

foreach ($urlList as $key => $url) {

   Logger::log('Start parse '.$url);
   
   $parser = new Parser($url);

   if(!empty($parser->getHtml())){

      $res = $parser->parse();

      if(!empty($res)){

         $filter =[
            'SITE_NAME' => $parser->getSiteUrl()
         ];
   
         $siteInfoTable->update($res, $filter);
      }
   }
}
