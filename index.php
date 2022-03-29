<?php

require_once 'utilits/autoloader.php';

use Configs\DataBaseConfig;
use ORM\SiteInfoSQL;
use Utilits\Logger;
use Utilits\Helper;
use Utilits\Parser;

$siteInfoTable = new SiteInfoSQL(DataBaseConfig::GetConfig(), 'site_info_list');

// $sql->add(['SITE_NAME' => 'www.tut.by', 'SITE_CMS' => 'bitrix', 'VERSION_CMS' => 'BUS', 'WIDGET' => false]);

// $filter = ['SITE_NAME' => 'www.tut.by','SITE_CMS' => 'bitrix'];
// // $filter =[];
// $select = ['SITE_NAME', 'SITE_CMS', 'VERSION_CMS', 'WIDGET'];
// $sql->get($select, $filter);

$urlList = Helper::getSitesURLList();
// $urlList = ['https://deal.by'];
$counter = 0;

foreach ($urlList as $key => $url) {
   
   $counter++;
   if($counter == 40) break;

   $parser = new Parser($url);

   $parser->parse();

}
