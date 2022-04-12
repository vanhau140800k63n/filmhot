<?php

namespace App\Services;

class MovieService
{
    public  function  __construct() {}
    
    public function getData($url) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'lang: en',
                'versioncode: 11',
                'clienttype: ios_jike_default'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $convert =json_decode($response,true);

        return $convert['data'];
    }

    public function searchWithKeyWord($keyWord) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://ga-mobile-api.loklok.tv/cms/app/search/v1/searchWithKeyWord',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "searchKeyWord": "'.$keyWord.'",
                "size": 50,
                "sort": "",
                "searchType": ""
            }',
            CURLOPT_HTTPHEADER => array(
                'lang: en',
                'versioncode: 11',
                'clienttype: ios_jike_default',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $convert =json_decode($response,true);

        return $convert['data'];
    }
}
