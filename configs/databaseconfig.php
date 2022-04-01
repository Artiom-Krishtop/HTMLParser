<?php

namespace Configs;

class DataBaseConfig{

    // connect DB
    private const HOST = 'localhost';
    private const LOGIN = 'root';
    private const PASSWORD = '';
    private const DB_NAME = 'bitrix';

    // table name
    private const SITE_INFO_LIST = 'site_info_list';

    public static function GetConfig()
    {
        return [
            'hostname' => static::HOST,
            'username' => static::LOGIN,
            'password' => static::PASSWORD,
            'database' => static::DB_NAME,
        ];
    }

    public static function getDBName()
    {
        return static::DB_NAME;
    } 

    public static function getSiteInfoTableName()
    {
        return static::SITE_INFO_LIST;
    }
}