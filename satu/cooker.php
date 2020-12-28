<?php
define('AUTH_TOKEN', 'a88a91073bb075dc494d0dcf64c06bfb4af25a32');  // Your authorization token
define('HOST', 'my.satu.kz');  // e.g.: my.prom.ua, my.tiu.ru, my.satu.kz, my.deal.by, my.prom.md


class EvoExampleClient {

    function EvoExampleClient($token) {
        $this->token = $token;
    }

    function make_request($method, $url, $body) {
        $headers = array (
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . HOST . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (strtoupper($method) == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    /**
     * Получить список заказов
     * @param string $status Возможные статусы заказов: pending - вновь созданный; received - принят в обработку; canceled - отменен
     * @return array
     */
    function get_order_list($status = NULL) {
        $url = '/api/v1/orders/list?limit=10';
		if ( !is_null($status) )
		{
			$url .= '?'.http_build_query(array('status'=>$status));
		}        
        $method = 'GET';

        $response = $this->make_request($method, $url, NULL);

        return $response;
    }

    function get_order_by_id($id) {
        $url = '/api/v1/orders/' . $id;
        $method = 'GET';

        $response = $this->make_request($method, $url, NULL);

        return $response;
    }

    /*
     * Изменять статус заказа.
     * @param array $ids Массив номеров заказов
     * @param string $status Статус [ pending, received, delivered, canceled, draft, paid ]
     * @param string $cancellation_reason Только для статуса canceled [ not_available, price_changed, buyers_request, not_enough_fields, duplicate, invalid_phone_number, less_than_minimal_price, another ]
     * @param string $cancellation_text Толкьо для причины отмены "price_changed", "not_enough_fields" или "another"
     * @return array
     */
    /*function set_order_status($ids, $status, $cancellation_reason = NULL, $cancellation_text = NULL) {
        $url = '/api/v1/orders/set_status';
        $method = 'POST';

        $body = array (
            'status'=> $status,
            'ids'=> $ids
        );
        if ( $status === 'canceled' )
        {
            $body['cancellation_reason'] = $cancellation_reason;

            if ( in_array($cancellation_reason,array('price_changed', 'not_enough_fields', 'another')) )
                $body['cancellation_text'] = $cancellation_text;
        }


        $response = $this->make_request($method, $url, $body);

        return $response;
    }*/
}

if (empty(AUTH_TOKEN)) {
    throw new Exception('Sorry, there\'s no any AUTH_TOKEN');
}

$client = new EvoExampleClient(AUTH_TOKEN);



// echo var_dump($order_list);

//$order_id = $order_list['orders'][0]['id'];

//$order = $client->get_order_by_id("4066323");
// echo var_dump($order);

//$set_status_result = $client->set_order_status((array) $order_id, 'received', NULL, NULL);
// echo var_dump($set_status_result);

//$order = $client->get_order_by_id("4066722");
//echo var_dump($order["order"]["products"]);


function getAllLeads(){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.deal.list.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'order' => array("ID" => 'DESC'),
        'select' => array("UF_CRM_1600944472"),
        'filter' => array("CATEGORY_ID" => "1")
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
    
    if (array_key_exists('error', $result)) echo "Ошибка при получении лидов: ".$result['error_description']."<br>";
    return $result;
}

function compare($client){
    
    
    //bitrix
    $added = getAllLeads();
    
    $bitrixArray = array();
    foreach($added["result"] as $addedOne){
        $bitrixArray[] = $addedOne["UF_CRM_1600944472"];
    }
    //----------
    //satu
    $order_list = $client->get_order_list();
    if (empty($order_list['orders'])) {
        throw new Exception('Sorry, there\'s no any order');
    }
    
    $satuArray = array();
    foreach($order_list["orders"] as $addedOne){
        $satuArray[] = (string) $addedOne["id"];
    }
    //----------
    
    echo var_dump($bitrixArray, "<br>");
    echo "<br><br><br>";
    echo var_dump($satuArray, "<br>");

    
    foreach($satuArray as $satuOne){
        if(!in_array($satuOne, $bitrixArray, true)){
            
            $order = $client->get_order_by_id($satuOne);

            echo var_dump($leadID, $contactID);
            echo "<br><br><br><br>";
            
            $order["order"]["client_notes"] .= "<br><br>Товары:<br>";
            
            $producty = array();
            foreach($order["order"]["products"] as $item){
                $producty[] = array(
                    'sku' => $item["sku"], 
                    'price' => preg_replace('/[^0-9,]/', '', $item["price"]), 
                    'quantity' => $item["quantity"]);
                $order["order"]["client_notes"] .= $item["name"]." [".$item["quantity"]."шт] [".$item["price"]."]<br>";
            }
            sleep(1);
            $itemsForBitrix = array();
            foreach($producty as $item){
                $itemsForBitrix[] = array(
                    'PRODUCT_ID' => getProductFromBitrixByCode($item["sku"]),
                    'PRICE' => $item["price"], 
                    'QUANTITY' => $item["quantity"]
                    );
            }
            
            $leadID = addLead($order)["result"];
            $contactID = addContact($order)["result"];
            setContact($leadID, $contactID);
            
            sleep(1);
            
            setProducts($leadID, $itemsForBitrix);
        }
    }
    

    
}

compare($client);

//$order = $client->get_order_by_id("4066323");

//echo var_dump(getAllLeads());

/*foreach($order_list["orders"] as $orderOne){
    echo var_dump($orderOne["id"],"<br>");
}*/


/*
$producty = array();
foreach($order["order"]["products"] as $item){
    $producty[] = array(
        'sku' => $item["sku"], 
        'price' => preg_replace('/[^0-9,]/', '', $item["price"]), 
        'quantity' => $item["quantity"]);
        echo var_dump("<br>", $item["price"]);
}
echo var_dump($producty,"<br>");
*/
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

function addContact($theOrder){
    // Формируем URL в переменной $queryUrl для отправки сообщений в лиды Битрикс24, где
    // указываем [вашеназвание], [идентификаторпользователя] и [код_вебхука]
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.contact.add.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
    'fields' => array(
        
        'NAME' => $theOrder["order"]["client_first_name"],
        'LAST_NAME' => $theOrder["order"]["client_last_name"],
        //'ADDRESS' => $theOrder["order"]["delivery_address"],
        'EMAIL' => Array(
            "n0" => Array(
                "VALUE" => $theOrder["order"]["email"],
                "VALUE_TYPE" => "WORK",
            ),
        ),
        'PHONE' => Array(
            "n0" => Array(
                "VALUE" => $theOrder["order"]["phone"],
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

function addLead($theOrder){
    // Формируем URL в переменной $queryUrl для отправки сообщений в лиды Битрикс24, где
    // указываем [вашеназвание], [идентификаторпользователя] и [код_вебхука]
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.deal.add.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
    'fields' => array(
        'TITLE' => "Заказ из сайта ".$theOrder["order"]["id"],
        "CATEGORY_ID" => "1",
        'UF_CRM_1600946946' => $theOrder["order"]["delivery_address"],
        'UF_CRM_1601011806' => $theOrder["order"]["delivery_option"]["name"],
        'UF_CRM_1601018943' => $theOrder["order"]["payment_option"]["name"],
        'UF_CRM_1600944472' => $theOrder["order"]["id"],
        
        'COMMENTS' => $theOrder["order"]["client_notes"]
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
    $result = curl_exec($curl);
    curl_close($curl);
    
    $result = json_decode($result, 1);
    return $result;
    if (array_key_exists('error', $result)) echo "Ошибка при сохранении лида: ".$result['error_description']."<br>";
}



/*$itemsForBitrix = array();
foreach($producty as $item){
    $itemsForBitrix[] = array(
        'PRODUCT_ID' => getProductFromBitrixByCode($item["sku"]),
        'PRICE' => $item["price"], 
        'QUANTITY' => $item["quantity"]
        );
}
echo var_dump($itemsForBitrix);
*/
//поиск
function getProductFromBitrixByCode($code){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.product.list.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'select' => array(
            'id'
            ),
        'filter' => array(
            'CODE' => $code//"CAK-52890 (BL)"
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


//вcтавка
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
