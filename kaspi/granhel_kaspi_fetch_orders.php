<?php

$currenttimestamp = round(microtime(true) * 1000);
$lasttimestamp = $currenttimestamp - "1209600000";

//---------------------- получаем все новые заказа из Kaspi.kz --------------------

$url = 'https://kaspi.kz/shop/api/v2/orders?page[number]=0&page[size]=20&filter[orders][state]=NEW&filter[orders][creationDate][$ge]=' . $lasttimestamp . '&filter[orders][creationDate][$le]=' . $currenttimestamp . '';


$headers = array(
    'Content-Type: application/vnd.api+json',
    'X-Auth-Token: HW/+vMuU8gLuEmUcftcQ4M3e/acxgRaw5AISTsG+iFo=',
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
    $totalPrice = $data['attributes']['totalPrice'];
    $formattedAddress = $data['attributes']['deliveryAddress']['formattedAddress'];
    $state = $data['attributes']['state'];
    $firstName = $data['attributes']['customer']['firstName'];
    $lastName = $data['attributes']['customer']['lastName'];
    $phone = $data['attributes']['customer']['cellPhone'];
    $deliverymode = $data['attributes']['deliveryMode'];
    $paymentMode = $data['attributes']['paymentMode'];
    $id = $data['id'];
    $isKaspiDelivery = $data['attributes']['isKaspiDelivery'];
    //$KaspiStatus = $data['attributes']['status'];


//------------------ KaspiStatus ---------------------
/*
if ($KaspiStatus == 'APPROVED_BY_BANK') {
    $KaspiStatus = 'Одобрен банком';
} elseif ($KaspiStatus == 'ACCEPTED_BY_MERCHANT') {
    $KaspiStatus = 'Принят на обработку продавцом';
}
*/
//----------------- isKaspiDelivery ------------------

if ($isKaspiDelivery) {
    $CATEGORY_ID = 3;
} else {
    $CATEGORY_ID = 2;
}


//----------------- способ доставки ------------------

if ($deliverymode == 'DELIVERY_LOCAL') {
    $deliveryMethod = 'Доставка в пределах населённого пункта';
} elseif ($deliverymode == 'DELIVERY_PICKUP') {
    $deliveryMethod = 'Самовывоз';
} elseif ($deliverymode == 'DELIVERY_REGIONAL_PICKUP') {
    $deliveryMethod = 'Региональная доставка до точки самовывоза';
} elseif ($deliverymode == 'DELIVERY_REGIONAL_TODOOR') {
    $deliveryMethod = 'Региональная доставка до дверей';
}

//--------------------- способ оплаты ----------------------

if ($paymentMode == 'PAY_WITH_CREDIT') {
    $paymentMethod = 'Покупка в кредит';
} elseif ($paymentMode == 'PREPAID') {
    $paymentMethod = 'Безналичная оплата';
}


//-------------------------------------------------------------


//-------------------- get goods from Kaspi -----------------

$getentries = 'https://kaspi.kz/shop/api/v2/orders/'. $id .'/entries';

$headers = array(
    'Content-Type: application/vnd.api+json',
    'X-Auth-Token: 8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=',
);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $getentries);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$entries = curl_exec($ch);
$arr1 = json_decode($entries, true);
    $entryid = $arr1['data'][0]['id'];
    $quantity = $arr1['data'][0]['attributes']['quantity'];
    $basePrice = $arr1['data'][0]['attributes']['basePrice'];
    $deliveryCost = $arr1['data'][0]['attributes']['deliveryCost'];

// ----------------- get goodID from Kaspi -------------------

$getgoodid = 'https://kaspi.kz/shop/api/v2/orderentries/'. $entryid .'/product';

$headers = array(
    'Content-Type: application/vnd.api+json',
    'X-Auth-Token: 8bGlNl0QHUlCUGbYS0iHnyF5z/sBVY9aVMlTsDw7TTw=',
);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $getgoodid);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "GET"); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$curlgoodid = curl_exec($ch);
$arr2 = json_decode($curlgoodid, true);
    
$goodid = $arr2['data']['attributes']['code'];


//----------------- get productID from BX24 ----------------

$getproductID = array('filter' => array('PROPERTY_70' => $goodid));

$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.product.list.json';
$queryData0 = http_build_query($getproductID);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl0,
    CURLOPT_POSTFIELDS => $queryData0,
));
$result0 = curl_exec($curl);
$decoded_data = json_decode($result0, true);
$productID = $decoded_data['result'][0]['ID'];

//---------------------------------------------------------

//echo $code;
//print_r($arr1);
//file_put_contents('kaspi_fetch_orders.log', $result, FILE_APPEND);

//----------------------- check orderid in bx24 --------------

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
$iscode = $decoded_data['total'];

if($iscode != '0' ) {
    
    echo 'Заказ с №'. $code .' уже существует';
    continue;
} else

    echo 'Всё ок! Создаем заказ с №'. $code .'...';


//--------------------- check contact in bx24 ---------------

$contactrequest = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.duplicate.findbycomm?ENTITY_TYPE=CONTACT&TYPE=PHONE&VALUES[]=+7'.$phone.'';

$ch1 = curl_init();
curl_setopt($ch,CURLOPT_URL, $contactrequest);
//curl_setopt($ch,CURLOPT_POST, true);
//curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, "GET"); 

//execute post

$result1 = curl_exec($ch);
$decodedresult = json_decode($result1, true);
//print_r($decodedresult);

$checkcontact = $decodedresult['result']['CONTACT']['0'];

if(isset($checkcontact)) {

    $contact_id = $checkcontact;
    echo 'Контакт найден с id '. $contact_id .'';
    
}
else {
    echo 'контакт не найден, создаем контакт...';

    $addcontact = array('fields' => array(
        'NAME' => $firstName,
        'LAST_NAME' => $lastName,
        'OPENED' => 'Y',
        'TYPE_ID' => 'CLIENT',
        'PHONE' => ['0' => [
            'VALUE' => '+7'.''.$phone,
            'VALUE_TYPE' => 'WORK',
        ]],

            
        )
    );


    $queryUrl = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.contact.add.json';
$queryData = http_build_query($addcontact);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl,
    CURLOPT_POSTFIELDS => $queryData,
));

$result = curl_exec($curl);
$decodedresult = json_decode($result, true);
    $contact_id = $decodedresult['result'];
//print_r($addcontact);
}

//---------- accept order -------------------------

$Token = 'HW/+vMuU8gLuEmUcftcQ4M3e/acxgRaw5AISTsG+iFo=';

$url = 'https://kaspi.kz/shop/api/v2/orders';

$fields_string = ['data' => array(
                                        'type'       => 'orders',
                                        'id'         => $id,
                                        'attributes' => array(
                                                                'status' => 'ACCEPTED_BY_MERCHANT',
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

// -------- create order in BX24 -------------------


$orderparams = array(
    "fields" => array(
    'UF_CRM_1600936951' => $code,
    'TITLE' => 'Заказ от kaspi.kz №' . $code . '',
    'CATEGORY_ID' => $CATEGORY_ID,
    'TYPE_ID' => 'GOODS',
    'SOURCE_DESCRIPTION' => 'Granhel master',
    'CONTACT_ID' => $contact_id,
    'UF_CRM_1600751520103' => $formattedAddress,
    'UF_CRM_1601011806' => $deliveryMethod,
    'UF_CRM_1601018943' => $paymentMethod,
    'UF_CRM_1601552934' => 'Принят на обработку продавцом',
    'UF_CRM_1602004237' => $id,

                                                )
                    );


$queryUrl = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.add.json';
$queryData = http_build_query($orderparams);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl,
    CURLOPT_POSTFIELDS => $queryData,
));

$result = curl_exec($curl);
$decoded_data = json_decode($result, true);
$orderID = $decoded_data['result'];
sleep(3);

echo $productID;
//--------------- add Product ------------------------

$addProduct = array(
    'id' => $orderID,
    'rows' => array(
        array(
        'PRODUCT_ID' => $productID,
        'PRICE' => $basePrice,
        'QUANTITY' => $quantity,
        ),
            array(
                'PRODUCT_ID' => '716',
                'PRICE' => $deliveryCost,
                'QUANTITY' => '1',
            ),
        )

    );


$queryUrl = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.productrows.set.json';
$queryData = http_build_query($addProduct);

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl,
    CURLOPT_POSTFIELDS => $queryData,
));

$result = curl_exec($curl);

        
    }
}

?>