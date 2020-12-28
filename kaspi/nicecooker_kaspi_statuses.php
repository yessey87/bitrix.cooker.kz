<?php

$currenttimestamp = round(microtime(true) * 1000);
$lasttimestamp = $currenttimestamp - "1209600000";

//---------------------- Проверяем заказы "на подписании" --------------------

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=50&filter[orders][state]=SIGN_REQUIRED&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


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


foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
    if ($isKaspiDelivery) {
        $CATEGORY_ID = 3;
    } else {
        $CATEGORY_ID = 2;
    }
    
    //echo $code;
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
    //print_r($decoded_data)."\n";
    //echo $dealID;

//-------------------- обновляем статус сделки BX24 -----------------

$state = 'C'.$CATEGORY_ID.':PREPARATION';

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $state)
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

//---------------------- Проверяем заказы "самовывоз" --------------------

$currenttimestamp = round(microtime(true) * 1000);
$lasttimestamp = $currenttimestamp - "604800000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=100&filter[orders][state]=PICKUP&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


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


foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
    if ($isKaspiDelivery) {
        $CATEGORY_ID = 3;
    } else {
        $CATEGORY_ID = 2;
    }
    
    //echo $code;
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

$state = 'C2:PREPAYMENT_INVOICE';

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $state)
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

//---------------------- Проверяем заказы "Доставка" --------------------

$currenttimestamp = round(microtime(true) * 1000);
$lasttimestamp = $currenttimestamp - "604800000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=200&filter[orders][state]=DELIVERY&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


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


foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
    if ($isKaspiDelivery) {
        $CATEGORY_ID = 3;
    } else {
        $CATEGORY_ID = 2;
    }
    
    //echo $code;
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

$state = 'C2:EXECUTING';

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $state)
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

//---------------------- Проверяем заказы "Kaspi доставка" --------------------

$currenttimestamp = round(microtime(true) * 1000);
$lasttimestamp = $currenttimestamp - "604800000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=400&filter[orders][state]=KASPI_DELIVERY&filter[orders][status]=ACCEPTED_BY_MERCHANT&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


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


foreach($arr as $key => $value) {

    //print_r($arr);
    if ($key == 'included' || $key == 'meta') {
        break;
    }   

    foreach($value as $data) {


    $code = $data['attributes']['code'];
    //$state = $data['attributes']['state'];
    $waybill = $data['attributes']['kaspiDelivery']['waybill'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    
    if ($isKaspiDelivery) {
        $CATEGORY_ID = 3;
    } else {
        $CATEGORY_ID = 2;
    }
    
    //echo $code;
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
    //echo print_r($decoded_data)."\n";
    echo $dealID."\n\n";

//-------------------- обновляем статус сделки BX24 -----------------

$state = 'C3:PREPAYMENT_INVOICE';

$stagerequest = array(
                        'id' => $dealID,
                        'fields' => array('STAGE_ID' => $state,
                                          'UF_CRM_1608038448057' => $waybill
                                         )
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
    //echo $dealID."\n";
    //echo print_r($decoded_data)."\n";


    }
}


//---------------------- Проверяем заказы "CANCELLED" --------------------

$currenttimestamp = round(microtime(true) * 1000);
$lasttimestamp = $currenttimestamp - "604800000";

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=200&filter[orders][state]=ARCHIVE&filter[orders][status]=CANCELLED&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


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
        $STAGEID = 'C3:FINAL_INVOICE';
    }elseif ($isKaspiDelivery == false) {
        $STAGEID = 'C2:1';
    }  

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
                                          'UF_CRM_1601552934' => 'Отменён')
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



?>