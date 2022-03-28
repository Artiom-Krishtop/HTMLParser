<?php

require_once 'utilits/autoloader.php';

use ORM\SQL;
use Configs\DataBaseConfig;
use ORM\SiteInfoSQL;
use Utilits\Logger;

$sql = new SiteInfoSQL(DataBaseConfig::GetConfig());

// $sql->deleteTable(DataBaseConfig::getSiteInfoTableName());
$sql->createTable();
$sql->structTable();

// $sql->add('www.tut.by', 'bitrix', 'B24', true);
Logger::log('HELLO WORLD');

$filter = ['SITE_NAME' => 'www.tut.by','SITE_CMS' => 'bitrix', 'VERSION_CMS' => 'B24'];
// $filter =[];
$select = ['SITE_NAME', 'SITE_CMS', 'VERSION_CMS', 'WIDGET'];
$sql->get(DataBaseConfig::getSiteInfoTableName(), $select, $filter);


