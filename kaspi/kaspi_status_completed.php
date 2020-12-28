<?php

$KaspiCabinet = $_REQUEST['KaspiCabinet'];

$KaspiID = $_REQUEST['KaspiID'];

$SMSCode = $_REQUEST['SMSCode'];

if($KaspiCabinet == 'Nicecooker master') {

    $Token = '8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=';

} else

    $Token = 'HW/+vMuU8gLuEmUcftcQ4M3e/acxgRaw5AISTsG+iFo=';


$url = 'https://kaspi.kz/shop/api/v2/orders';

$fields_string = ['data' => array(
                                        'type'       => 'orders',
                                        'id'         => $KaspiID,
                                        'attributes' => array(
                                                                'status' => 'COMPLETED',
                                                             )

                                      )
                   
                 ];

$headers = array(
    'Content-Type: application/vnd.api+json',
    "X-Auth-Token: $Token",
    "X-Security-Code: $SMSCode",
    'X-Send-Code: true',
                );

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields_string));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//execute post

$result = curl_exec($ch);

$arr = json_decode($result, true);

file_put_contents('log.txt', print_r($result), FILE_APPEND);

?>
