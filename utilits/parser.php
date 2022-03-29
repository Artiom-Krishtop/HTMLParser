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
      if(!preg_match('/(http|www)/', $url)){

         $url = 'https://'.$url .'/';
      }

      $this->siteURL = $url;
      $this->html = $this->request($this->siteURL);
   }

   public function parse()
   {
      // $this->isBitrixCMS();  
   }

   protected function request($url, $data = [], $returnCode = false)
   {
      dd($url, false);

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
      dd($curlInfo['http_code'], false);
      curl_close($ch);

      // if ($curlInfo['http_code'] == 301 && $this->counter != 3) {

      //    $this->counter++;

      //    Logger::log($this->siteURL . ' returned code 301. Redirect to '.$curlInfo['redirect_url']);

      //    $this->request($curlInfo['redirect_url']);
      // }

      if ($returnCode) {
         
         return $curlInfo['http_code'];
      }

      if (!empty($error)) {
         
         Logger::errorLog('CURL', $error);
      }
      
      $this->counter = 0;

      return $html;
   }

   protected function isBitrixCMS()
   {
      $code = $this->checkUrlAdmin('bitrix');
      dd($code, false);
      if ($code === 200) {
         # code...
      }
      return true;
      $pattern = '/((href|src)=.+(bitrix|local).+)|(class=.+bx-.+)/';
      $pattern = '/(\/bitrix\/|\/local\/|bx-)/';

      if (preg_match($pattern, $this->html)) {
         
         if ($this->isSites24()) {
            # code...
         }
      }
   }

   protected function isSites24(){
      return false;
   }

   protected function checkUrlAdmin($nameCMS)
   {
      if (is_array($this->adminURL[$nameCMS])) {
         
         $listCode = [];

         foreach ($this->adminURL[$nameCMS] as $adminUrl) {
            
            $url = $this->siteURL.$adminUrl;

            $listCode[] = $this->request($url, [], true);
         }

         return $listCode;

      }else {
         
         $url = $this->siteURL.$this->adminURL[$nameCMS];
         
         return $this->request($url, [], true);
      }
   }
} 