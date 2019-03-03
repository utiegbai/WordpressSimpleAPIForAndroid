<?php
/**
* Author: Idongesit Ntuk
* Date: 15/6/2017
* Project: calitunesAds
*/
class Api {
    private static $_data;
    private static $_basepath = "http://old.calitunes.com/";

    public static function get($api)
    {
        $api_url =self::$_basepath.$api;

        $ch = curl_init();
            curl_setopt ($ch,  CURLOPT_URL, $api_url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_USERAGENT, 'Calitunes Ads');
        $response = curl_exec($ch);

        if(!$response){
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }else{
            return json_decode($response);
        }

        curl_close($ch);
    }
}
?>