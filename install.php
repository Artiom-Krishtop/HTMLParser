<?php

require_once 'utilits/autoloader.php';

use Utilits\Helper;
use ORM\SiteInfoSQL;
use Configs\DataBaseConfig;
use ORM\Connection;

define('ROOT', __DIR__);

Connection::createDB(DataBaseConfig::GetConfig());

$siteInfoTable = new SiteInfoSQL(DataBaseConfig::GetConfig(), 'site_info_list');

$urlList = Helper::getSitesURLList();

$data = [];
$counter = 0;

foreach ($urlList as $url) {
   
   $counter++;
   $data[] = [$url];
   
   echo $url ."\n";

   if ($counter == 10000) {
      
      $siteInfoTable->add($data, ['SITE_NAME']);
      $data = [];
      $counter = 0;
   }
}

