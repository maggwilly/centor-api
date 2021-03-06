<?php
namespace AppBundle\Service;

class FMCManager
{

 const HEADERS=array(
    "Authorization: key=AAAASmMMRcQ:APA91bH5tLRAx_5qHJu3tPB7VUtHN6QaocQoMfn5-cD7wVPaD59PIZ66aLzwKkM1iMoSfUqR1tD6HQHPQWmzH9Hp5EzNmOZA7S_32oLwppqXleDSfwflGbklwTYrQZ3kel4Xo5FZGJZ4",
      "content-type: application/json"
   );
const FCM_URL = "https://fcm.googleapis.com/fcm/send";

public function __construct(){}

  public function sendMessage($data, $json_decode=true)
    {
        $content = json_encode($data);
        $curl = curl_init(self::FCM_URL);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::HEADERS);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 ) {}
        curl_close($curl);
        $response = json_decode($json_response, true);
        return $json_decode?$response:$json_response;
    }


    public function sendOrGetData($url,$data,$costum_method,$json_decode=true,$headers=array())
    {    $content ='';
        if(!is_null($data))
           $content = json_encode($data);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST , $costum_method);
        if(!is_null($data))
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        $json_response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ( $status != 200 ) {}
        curl_close($curl);
        $response = json_decode($json_response, true);
        return $json_decode?$response:$json_response;
    }
}