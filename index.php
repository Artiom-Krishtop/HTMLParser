#!/usr/bin/php
<?php

require_once 'utilits/autoloader.php';

use Configs\DataBaseConfig;
use ORM\SiteInfoSQL;
use Utilits\Logger;
use Utilits\Helper;
use Utilits\Parser;

set_time_limit(0);

define('ROOT', __DIR__);
define('LIMIT', 1000);

$siteInfoTable = new SiteInfoSQL(DataBaseConfig::GetConfig(), 'site_info_list');

$urlList = $siteInfoTable->get(['SITE_NAME'], ['CHECKED' => false], 'ASC');

foreach ($urlList as $key => $url) {

   Logger::log('Start parse '.$url['SITE_NAME']);

   
   $parser = new Parser($url['SITE_NAME']);
   
   if(!empty($parser->getHtml())){

      $res = $parser->parse();
      
      if(!empty($res)){
         
         $filter =[
            'SITE_NAME' => $parser->getSiteUrl(),
         ];

         $res['CHECKED'] = true;
         
         $siteInfoTable->update($res, $filter);

         continue;
      }
   }

   $siteInfoTable->update(['CHECKED' => true], ['SITE_NAME' => $url['SITE_NAME']]);
}
exit("FINISH\n");?>



