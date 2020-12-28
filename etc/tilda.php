<?php

function writeToLog($data) { 
    $log = "\n------------------------\n"; 
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n"; file_put_contents(getcwd() . '/tilda.log', 
    $log, FILE_APPEND); 
    return true;
} 

writeToLog($_POST);

$_POST["Textarea"] .= "<br><br>Товары:<br>";

$producty = array();
foreach($_POST["payment"]["products"] as $item){
    $producty[] = array(
        'sku' => $item["sku"], 
        'price' => $item["price"], 
        'quantity' => $item["quantity"]);
        $_POST["Textarea"] .= $item["name"]." [".$item["quantity"]."шт] [".$item["price"]."]<br>";
}

$itemsForBitrix = array();
foreach($producty as $item){
    $itemsForBitrix[] = array(
        'PRODUCT_ID' => getProductFromBitrixByCode($item["sku"]),
        'PRICE' => $item["price"], 
        'QUANTITY' => $item["quantity"]
    );
}

$leadID = addLead($_POST)["result"];
$contactID = addContact($_POST)["result"];
setContact($leadID, $contactID);


setProducts($leadID, $itemsForBitrix);

function addLead($theOrder){
    
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.deal.add.json';
    
    $queryData = http_build_query(array(
    'fields' => array(
        'TITLE' => "Заказ из сайта ".$theOrder["payment"]["orderid"],
        "CATEGORY_ID" => "7",
        'UF_CRM_1600946946' => $theOrder["адрес"],
        'UF_CRM_1601011806' => $theOrder["Варианты_доставки"],
        'UF_CRM_1601018943' => $theOrder["paymentsystem"],
        
        'COMMENTS' => $theOrder["Textarea"]
    ),
    'params' => array("REGISTER_SONET_EVENT" => "Y")
));
    
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
    curl_close($curl);
    
    $result = json_decode($result, 1);
    return $result;
    if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br>";
}

function addContact($theOrder){
    // Формируем URL в переменной $queryUrl для отправки сообщений в лиды Битрикс24, где
    // указываем [вашеназвание], [идентификаторпользователя] и [код_вебхука]
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.contact.add.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
    'fields' => array(
        
        'NAME' => $theOrder["Name"],
        'LAST_NAME' => "",
        //'ADDRESS' => $theOrder["order"]["delivery_address"],
        'EMAIL' => Array(
            "n0" => Array(
                "VALUE" => $theOrder["Email"],
                "VALUE_TYPE" => "WORK",
            ),
        ),
        'PHONE' => Array(
            "n0" => Array(
                "VALUE" => $theOrder["Phone"],
                "VALUE_TYPE" => "WORK",
            ),
        ),
    ),
    'params' => array("REGISTER_SONET_EVENT" => "Y")
));
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
    curl_close($curl);
    
    $result = json_decode($result, 1);
    return $result;
    if (array_key_exists('error', $result)) echo "Ошибка при создании контакта: ".$result['error_description']."<br>";

}

function setContact($idDeal, $idContact){
     // Формируем URL в переменной $queryUrl для отправки сообщений в лиды Битрикс24, где
    // указываем [вашеназвание], [идентификаторпользователя] и [код_вебхука]
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.deal.contact.add.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
    'id' => $idDeal,
    'fields' => array(
        "CONTACT_ID" => $idContact
        ),
    
    'params' => array("REGISTER_SONET_EVENT" => "Y")
));
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
    curl_close($curl);
    
    $result = json_decode($result, 1);
  
    if (array_key_exists('error', $result)) echo "Ошибка при создании контакта: ".$result['error_description']."<br>";
}

function getProductFromBitrixByCode($code){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.product.list.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'select' => array(
            'id'
            ),
        'filter' => array(
            'PROPERTY_74' => $code//"CAK-52890 (BL)"
            ),
        
        'params' => array("REGISTER_SONET_EVENT" => "Y")
    ));
    
    // Обращаемся к Битрикс24 при помощи функции curl_exec
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $queryUrl,
        CURLOPT_POSTFIELDS => $queryData,
    ));
    $resultTovar = curl_exec($curl);
    curl_close($curl);
    $resultTovar = json_decode($resultTovar, 1);
    
    if (array_key_exists('error', $resultTovar)) echo "Ошибка поиска товара: ".$resultTovar['error_description']."<br>";
    
    return $resultTovar["result"][0]["ID"];
}

function setProducts($leadId, $itemsForBitrix){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.deal.productrows.set.json';
// Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'id' => $leadId,
        'rows' => $itemsForBitrix,
        'params' => array("REGISTER_SONET_EVENT" => "Y")
    ));
    
    // Обращаемся к Битрикс24 при помощи функции curl_exec
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
    curl_close($curl);
    $result = json_decode($result, 1);
    
    
    
    if (array_key_exists('error', $result)) echo "Ошибка при вставке в лид: ".$result['error_description']."<br>";
}
