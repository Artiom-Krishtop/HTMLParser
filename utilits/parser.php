<?php 

namespace Utilits;

use Utilits\Logger;

require_once 'utilits/autoloader.php';

class Parser{

   private const CONNECTTIMEOUT = 5;
   private const CURL_TIMEOUT = 10;

   protected $siteURL;
   protected $counter = 0;
   protected $html;
   protected $adminURL = [
      'bitrix' => 'bitrix/admin/',
      'wordpress' => '/wp-admin',
      'DLE' => '/admin.php',
      'Drupal' => ['/user', '/admin'],
      'HostCMS' => '/admin',
      'Joomla' => '/administrator',
      'MODX' => '/manager', 
      'OpenCart' => '/admin',
      'Typo3' => '/typo3',
      'UNI'=> '/admin'

   ];

   function __construct($url)
   {
      $this->siteURL = $url;
      $this->html = $this->request($this->siteURL);
   }

   public function parse()
   {
      if(!empty($this->isBitrixCMS())) return $this->isBitrixCMS();

      return [];
   }

   public function getHtml()
   {
      return $this->html;
   }

   public function getSiteUrl()
   {
      return $this->siteURL;
   }

   protected function request($url, $data = [])
   {
      $url = $this->siteURL;

      if (!empty($data)) {
         $url .= '?'.http_build_query($data);
      }       

      $ch = curl_init($this->siteURL);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECTTIMEOUT);
      curl_setopt($ch, CURLOPT_TIMEOUT, self::CURL_TIMEOUT);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      $html = curl_exec($ch);
      $error = curl_error($ch);
      $curlInfo = curl_getinfo($ch);

      curl_close($ch);

      if (!empty($error)) {
         
         Logger::errorLog('CURL', $error);
      }

      if($curlInfo['http_code'] != 200)
      {
         Logger::errorLog('CURL', 'Requset returned code '. $curlInfo['http_code']);

         return $html = '';
      }
      
      return $html;
   }

   protected function isBitrixCMS()
   {
      $data = [];

      $pattern = '/(\/bitrix\/|\/local\/|bx-)/';

      if (preg_match($pattern, $this->html)) {

         $data['SITE_CMS'] = 'bitrix';

         if ($this->find(['landing24'])) {
            
            $data['VERSION_CMS'] = 'Sites24';

         }else {

            $data['VERSION_CMS'] = 'BUS';
         }

         if($this->find(['/crm/site_button/'])){

            $data['WIDGET'] = true;
         }
      }

      return $data;
   }

   protected function find(array $arrNeedle){

      foreach ($arrNeedle as $str) {
         
         if(strpos($this->html, $str) !== false){
   
            return true;
         }
   
      }
      return false;
   }
} 