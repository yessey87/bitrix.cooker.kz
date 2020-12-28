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
    function set_order_status($ids, $status, $cancellation_reason = NULL, $cancellation_text = NULL) {
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
    }
    
    
    
}

if (empty(AUTH_TOKEN)) {
    throw new Exception('Sorry, there\'s no any AUTH_TOKEN');
}

$client = new EvoExampleClient(AUTH_TOKEN);

//$set_status_result = $client->set_order_status((array) 4066722, "delivered", NULL, NULL);
//echo var_dump($set_status_result);
//set_order_status("", "delivered");


setStatus($_REQUEST, $client);
//writeToLog($_REQUEST, NULL);


//echo var_dump(getLead("255"));
//print_r($_REQUEST); 
//writeToLog($_REQUEST, 'incoming'); 
/** * Write data to log file. * * @param mixed $data * @param string $title * * @return bool */ 
function writeToLog($data, $stage) { 
    $log = "\n------------------------\n"; 
    $log .= print_r($data, 1);
    $log .= print_r("//", 1);
    $log .= "\n------------------------\n"; file_put_contents(getcwd() . '/hook.log', 
    $log, FILE_APPEND); 
    return true;
} 
//$order = $client->get_order_by_id("4055178");

//echo var_dump($order);

function setStatus($data, $client){
    
    $lead = getLead($data["ID"]);
    writeToLog($lead, NULL);
    
    
    
    if(strcmp($lead["result"]["STAGE_ID"],"C1:1") == 0 || strcmp($lead["result"]["STAGE_ID"],"C1:3") == 0 || 
    strcmp($lead["result"]["STAGE_ID"],"C1:5") == 0 || strcmp($lead["result"]["STAGE_ID"],"C1:6") == 0){
        
        $idSatu = $lead["result"]["UF_CRM_1600944472"];
        return $client->set_order_status((array) (int)$idSatu,"received", NULL, NULL);
        
    }
    
    else if(strcmp($lead["result"]["STAGE_ID"],"C1:4") == 0){
      
        $idSatu = $lead["result"]["UF_CRM_1600944472"];
        return $client->set_order_status((array) (int)$idSatu,"delivered", NULL, NULL);
       
    }
    
    else if(strcmp($lead["result"]["STAGE_ID"],"C1:WON") == 0){
        
        $idSatu = $lead["result"]["UF_CRM_1600944472"];
        return $client->set_order_status((array) (int)$idSatu,"paid", NULL, NULL);
      
    }
    
    else if(strcmp($lead["result"]["STAGE_ID"], "C1:LOSE") == 0){
        
        $idSatu = $lead["result"]["UF_CRM_1600944472"];
        return $client->set_order_status((array) (int)$idSatu,"canceled", "another", "From Bitrix");
       
    }
    
    
}



function getLead($id){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/3ff1r35zhp155k7y/crm.deal.get.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'id' => $id,
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
    
    if (array_key_exists('error', $result)) echo "Ошибка при получении лидов: ".$result['error_description']."<br>";
    return $result;
}
// echo var_dump($order_list);

//$order_id = $order_list['orders'][0]['id'];



//$set_status_result = $client->set_order_status((array) $order_id, 'received', NULL, NULL);
// echo var_dump($set_status_result);

//$order = $client->get_order_by_id("4066722");
//echo var_dump($order["order"]["products"]);



