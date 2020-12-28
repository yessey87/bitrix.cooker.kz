<?php
echo "Cooker 1c script<br>";
echo "It works!<br>";
$dealID = $_REQUEST['dealID'];
$getDealProducts = array('id' => $_REQUEST['dealID']);
file_put_contents('log.txt', $dealID."\n", FILE_APPEND);

$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.productrows.get.json';
$queryData0 = http_build_query($getDealProducts);

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
//file_put_contents('log.txt', print_r($decoded_data,true)."\n", FILE_APPEND);

$goodid0 = $decoded_data[result][0]['PRODUCT_ID'];
$goodid1 = $decoded_data[result][1]['PRODUCT_ID'];
$goodid2 = $decoded_data[result][2]['PRODUCT_ID'];
$goodid3 = $decoded_data[result][3]['PRODUCT_ID'];
$goodid4 = $decoded_data[result][4]['PRODUCT_ID'];

$goodname0 = $decoded_data[result][0]['PRODUCT_NAME'];
$goodname1 = $decoded_data[result][1]['PRODUCT_NAME'];
$goodname2 = $decoded_data[result][2]['PRODUCT_NAME'];
$goodname3 = $decoded_data[result][3]['PRODUCT_NAME'];
$goodname4 = $decoded_data[result][4]['PRODUCT_NAME'];

$goodprice0 = $decoded_data[result][0]['PRICE'];
$goodprice1 = $decoded_data[result][1]['PRICE'];
$goodprice2 = $decoded_data[result][2]['PRICE'];
$goodprice3 = $decoded_data[result][3]['PRICE'];
$goodprice4 = $decoded_data[result][4]['PRICE'];


$goodquantity0 = $decoded_data[result][0]['QUANTITY'];
$goodquantity1 = $decoded_data[result][1]['QUANTITY'];
$goodquantity2 = $decoded_data[result][2]['QUANTITY'];
$goodquantity3 = $decoded_data[result][3]['QUANTITY'];
$goodquantity4 = $decoded_data[result][4]['QUANTITY'];

// --------------- get GUID0 ---------------------

$getGUIDparam = array('id' => $goodid0);
$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.product.get.json';
$queryData0 = http_build_query($getGUIDparam);
        
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
        $decoded_data1 = json_decode($result0, true);
      
        $GUID0 = $decoded_data1[result]['PROPERTY_73']['value'];
        //file_put_contents('log.txt', print_r($decoded_data,true)."\n", FILE_APPEND);
        //file_put_contents('log.txt', $goodid0."\n", FILE_APPEND);

// --------------- get GUID1 ---------------------

$getGUIDparam = array('id' => $goodid1);
$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.product.get.json';
$queryData0 = http_build_query($getGUIDparam);
        
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
        $decoded_data2 = json_decode($result0, true);
      
        $GUID1 = $decoded_data2[result]['PROPERTY_73']['value'];
        //file_put_contents('log.txt', print_r($decoded_data,true)."\n", FILE_APPEND);
        //file_put_contents('log.txt', $GUID1."\n", FILE_APPEND);
        
// --------------- get GUID2 ---------------------

$getGUIDparam = array('id' => $goodid2);
$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.product.get.json';
$queryData0 = http_build_query($getGUIDparam);
        
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
        $decoded_data3 = json_decode($result0, true);
      
        $GUID2 = $decoded_data3[result]['PROPERTY_73']['value'];
        //file_put_contents('log.txt', print_r($decoded_data,true)."\n", FILE_APPEND);
        //file_put_contents('log.txt', $GUID2."\n", FILE_APPEND);.


// --------------- get GUID3 ---------------------

$getGUIDparam = array('id' => $goodid3);
$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.product.get.json';
$queryData0 = http_build_query($getGUIDparam);
        
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
        $decoded_data4 = json_decode($result0, true);
      
        $GUID3 = $decoded_data4[result]['PROPERTY_73']['value'];
        //file_put_contents('log.txt', print_r($decoded_data,true)."\n", FILE_APPEND);
        //file_put_contents('log.txt', $GUID3."\n", FILE_APPEND);

// --------------- get GUID2 ---------------------

$getGUIDparam = array('id' => $goodid4);
$queryUrl0 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.product.get.json';
$queryData0 = http_build_query($getGUIDparam);
        
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
        $decoded_data5 = json_decode($result0, true);
      
        $GUID4 = $decoded_data5[result]['PROPERTY_73']['value'];
        //file_put_contents('log.txt', print_r($decoded_data,true)."\n", FILE_APPEND);
        //file_put_contents('log.txt', $goodid0.' '.$goodid1.' '.$goodid2.' '.$goodid3.' '.$goodid4."\n", FILE_APPEND);


$headers = array(
    'dealID',
    'Kontragent_UID',
    'contactName',
    'contactPhone',
    'DeliveryAddress',
    'courierName',
    'Summ',
    'goodid0',
    'goodGUID0',
    'goodname0',
    'goodprice0',
    'goodquantity0',
    'goodid1',
    'goodGUID1',
    'goodname1',
    'goodprice1',
    'goodquantity1',
    'goodid2',
    'goodGUID2',
    'goodname2',
    'goodprice2',
    'goodquantity2',
    'goodid3',
    'goodGUID3',
    'goodname3',
    'goodprice3',
    'goodquantity3',
    'goodid4',
    'goodGUID4',
    'goodname4',
    'goodprice4',
    'goodquantity4',
);

$data = array(array(
    'dealID'       => $_REQUEST['dealID'],
    'Kontragent_UID' => $_REQUEST['Kontragent_UID'],
    'contactName'  => $_REQUEST['contactName'],
    'contactPhone' => $_REQUEST['contactPhone'],
    'DeliveryAddress' => $_REQUEST['DeliveryAddress'],
    'courierName'  => $_REQUEST['courier'],
    'Summ'         => $_REQUEST['Summ'],
    'goodid0'      => $goodid0,
    'goodGUID0'    => $GUID0,
    'goodname0'    => $goodname0,
    'goodprice0'   => $goodprice0,
    'goodquantity0'=> $goodquantity0,
    'goodid1'      => $goodid1,
    'goodGUID1'    => $GUID1,
    'goodname1'    => $goodname1,
    'goodprice1'   => $goodprice1,
    'goodquantity1'=> $goodquantity1,
    'goodid2'      => $goodid2,
    'goodGUID2'    => $GUID2,
    'goodname2'    => $goodname2,
    'goodprice2'   => $goodprice2,
    'goodquantity2'=> $goodquantity2,
    'goodid3'      => $goodid3,
    'goodGUID3'    => $GUID3,
    'goodname3'    => $goodname3,
    'goodprice3'   => $goodprice3,
    'goodquantity3'=> $goodquantity3,
    'goodid4'      => $goodid4,
    'goodGUID4'    => $GUID4,
    'goodname4'    => $goodname4,
    'goodprice4'   => $goodprice4,
    'goodquantity4'=> $goodquantity4,

)
);




$fp = fopen('../../../ftp/To1C/deal_'.$dealID.'.csv', 'w');
fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
fputcsv($fp, $headers, "%");

foreach($data as $fields) {
    fputcsv($fp, $fields, "%");
}

fclose($fp);


file_put_contents('../logs/make_csv_LP.log', date("d/m/y - H:i -", time()).' '.'dealID_'.$dealID."\n", FILE_APPEND);

?>