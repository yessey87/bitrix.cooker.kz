<?

function writeToLog($data) { 
    $log = "\n------------------------\n"; 
    $log .= print_r($data, 1);
    $log .= "\n------------------------\n"; file_put_contents(getcwd() . '/test.log', 
    $log, FILE_APPEND); 
    return true;
} 

$id = getNumber($_REQUEST["data"]["FIELDS"]["ID"]);

$number = $id["result"]["PHONE"];


$update = array();

foreach($number as $one){
    if(substr($one["VALUE"], 0, 1) === "7"){
        $one["VALUE"] = substr_replace($one["VALUE"], "8", 0, 1);
    }
    else if(substr($one["VALUE"], 0, 2) === "+7"){
        $one["VALUE"] = substr_replace($one["VALUE"], "8", 0, 2);
    }
    $update[] = $one;
}

updateNumber($_REQUEST["data"]["FIELDS"]["ID"], $update);

function updateNumber($id, $params){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/h7hyub9p9x7am91w/crm.contact.update.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'id' => $id,
        "fields" => array("PHONE" => $params),
        "params" => array("REGISTER_SONET_EVENT" => "Y") 
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

function getNumber($id){
    $queryUrl = 'https://bitrix.cooker.kz/rest/1/h7hyub9p9x7am91w/crm.contact.get.json';
    // Формируем параметры для создания лида в переменной $queryData
    $queryData = http_build_query(array(
        'id' => $id
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