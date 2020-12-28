<?php

//file_put_contents('log.txt', print_r($_REQUEST, true));

$KaspiCabinet = $_REQUEST['KaspiCabinet'];

$KaspiID = $_REQUEST['KaspiID'];

$cancellationReason = $_REQUEST['cancellationReason'];

//$KaspiStatusID = $_REQUEST['KaspiStatusID'];

if($KaspiCabinet == 'Nicecooker master') {

    $Token = '8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=';

} else

    $Token = 'HW/+vMuU8gLuEmUcftcQ4M3e/acxgRaw5AISTsG+iFo=';

if ($cancellationReason == 'Отказ покупателя') {

    $cancellationReason = 'BUYER_CANCELLATION_BY_MERCHANT';

} elseif ($cancellationReason == 'Не удалось дозвониться до покупателя') {
    
    $cancellationReason = 'BUYER_NOT_REACHABLE';

} elseif ($cancellationReason == 'Нет в наличии') {

    $cancellationReason = 'MERCHANT_OUT_OF_STOCK';
}

    

//file_put_contents('log.txt', $cancellationReason, FILE_APPEND);

$url = 'https://kaspi.kz/shop/api/v2/orders';

$fields_string = ['data' => array(
                                        'type'       => 'orders',
                                        'id'         => $KaspiID,
                                        'attributes' => array(
                                                                'status' => 'CANCELLED',
                                                                'cancellationReason' => $cancellationReason,
                                                             )

                                 )
                   
                 ];

$headers = array(
    'Content-Type: application/vnd.api+json',
    "X-Auth-Token: $Token",
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

file_put_contents('log.txt', print_r($arr, true), FILE_APPEND);

?>