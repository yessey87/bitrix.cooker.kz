<?php

//---------------------- Проверяем заказы "CANCELLING" --------------------

$currenttimestamp = round(microtime(true) * 1000);
//$currenttimestamp = '1603130401000';
$lasttimestamp = $currenttimestamp - "1209600000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=1000&filter[orders][state]=KASPI_DELIVERY&filter[orders][status]=CANCELLING&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


$headers = array(
    'Content-Type: application/vnd.api+json',
    'X-Auth-Token: 8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=',
);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
//curl_setopt($ch,CURLOPT_POST, true);
//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//execute post

$result = curl_exec($ch);

$arr = json_decode($result, true);
print_r($arr)."/n";

foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    //$KaspiStatus = $data['attributes']['status'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
   

    if ($isKaspiDelivery == true) {
        $STAGEID = 'C3:1';
    
    

//------------------------- получаем ID сделок из BX24 -------------------

    $orderrequest = array('filter' => array('UF_CRM_1600936951' => $code));

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.list.json';
$queryData1 = http_build_query($orderrequest);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData1,
));

$result1 = curl_exec($curl);
$decoded_data = json_decode($result1, true);
$dealID = $decoded_data['result'][0]['ID'];
    //print_r($decoded_data);
    //echo $dealID;

//-------------------- обновляем статус сделки BX24 -----------------

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $STAGEID,
                                          'UF_CRM_1601552934' => 'Ожидает отмены')
                     );

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.update.json';
$queryData1 = http_build_query($stagerequest);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData1,
));

$result2 = curl_exec($curl);
$decoded_data = json_decode($result2, true);
//$dealID = $decoded_data['result'][0]['ID'];
    //print_r($decoded_data);

        }  
    }
}

//---------------------- Проверяем заказы "KASPI_DELIVERY_RETURN_REQUESTED" --------------------

$currenttimestamp = round(microtime(true) * 1000);
//$currenttimestamp = '1603130401000';
$lasttimestamp = $currenttimestamp - "1209600000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=1000&filter[orders][state]=KASPI_DELIVERY&filter[orders][status]=KASPI_DELIVERY_RETURN_REQUESTED&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


$headers = array(
    'Content-Type: application/vnd.api+json',
    'X-Auth-Token: 8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=',
);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
//curl_setopt($ch,CURLOPT_POST, true);
//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//execute post

$result = curl_exec($ch);

$arr = json_decode($result, true);
print_r($arr)."/n";

foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    //$KaspiStatus = $data['attributes']['status'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
   

    if ($isKaspiDelivery == true) {
        $STAGEID = 'C3:1';
    
    

//------------------------- получаем ID сделок из BX24 -------------------

    $orderrequest = array('filter' => array('UF_CRM_1600936951' => $code));

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.list.json';
$queryData1 = http_build_query($orderrequest);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData1,
));

$result1 = curl_exec($curl);
$decoded_data = json_decode($result1, true);
$dealID = $decoded_data['result'][0]['ID'];
    //print_r($decoded_data);
    //echo $dealID;

//-------------------- обновляем статус сделки BX24 -----------------

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $STAGEID,
                                          'UF_CRM_1601552934' => 'Ожидает возврата')
                     );

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.update.json';
$queryData1 = http_build_query($stagerequest);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData1,
));

$result2 = curl_exec($curl);
$decoded_data = json_decode($result2, true);
//$dealID = $decoded_data['result'][0]['ID'];
    //print_r($decoded_data);

        }  
    }
}


//---------------------- Проверяем заказы "RETURNED" --------------------

$currenttimestamp = round(microtime(true) * 1000);
//$currenttimestamp = '1603130401000';
$lasttimestamp = $currenttimestamp - "1209600000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=1000&filter[orders][state]=ARCHIVE&filter[orders][status]=RETURNED&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


$headers = array(
    'Content-Type: application/vnd.api+json',
    'X-Auth-Token: 8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=',
);

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
//curl_setopt($ch,CURLOPT_POST, true);
//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

//execute post

$result = curl_exec($ch);

$arr = json_decode($result, true);
print_r($arr)."/n";

foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    //$KaspiStatus = $data['attributes']['status'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
   

    if ($isKaspiDelivery == true) {
        $STAGEID = 'C3:5';
    
    

//------------------------- получаем ID сделок из BX24 -------------------

    $orderrequest = array('filter' => array('UF_CRM_1600936951' => $code));

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.list.json';
$queryData1 = http_build_query($orderrequest);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData1,
));

$result1 = curl_exec($curl);
$decoded_data = json_decode($result1, true);
$dealID = $decoded_data['result'][0]['ID'];
    //print_r($decoded_data);
    //echo $dealID;

//-------------------- обновляем статус сделки BX24 -----------------

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $STAGEID,
                                          'UF_CRM_1601552934' => 'Возвращён')
                     );

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.update.json';
$queryData1 = http_build_query($stagerequest);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData1,
));

$result2 = curl_exec($curl);
$decoded_data = json_decode($result2, true);
//$dealID = $decoded_data['result'][0]['ID'];
    //print_r($decoded_data);

        }  
    }
}

?>