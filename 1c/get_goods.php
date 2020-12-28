<?php
//echo 'It Works';

//------------------- Receipt stuff ----------------
$receipt_matches = glob('../../ftp/From1C/*_Chek.PDF');
$filename_receipt = $receipt_matches[0];
$deal_id = trim($filename_receipt,'../../ftp/From1C/._Check.PDF');
$base64_receipt = base64_encode(file_get_contents($filename_receipt));

//------------------- Invoice stuff ----------------
$invoice_matches = glob('../../ftp/From1C/*_Order.PDF');
$filename_invoice = $invoice_matches[0];
$base64_invoice = base64_encode(file_get_contents($filename_invoice));

//echo $deal_id;
//echo $base64_invoice;

$dealupdate = array(
    'id' => $deal_id,
    'fields' => array('UF_CRM_1606904604' => array(
                                            'fileData' => ["Товарный-чек.pdf", $base64_receipt]
                                                   ),
                      'UF_CRM_1606904558' => array(
                                            'fileData' => ["Товарная-накладная.pdf", $base64_invoice]
                                                  )
                     )
 );

$queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.update.json';
$queryData1 = http_build_query($dealupdate);

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
//$decoded_data = json_decode($result2, true);
//$dealID = $decoded_data['result'][0]['ID'];
//print_r($decoded_data);

unlink($filename_invoice);
unlink($filename_receipt);

file_put_contents('logs/pdf_handler.log', date("d/m/y - H:i -", time()).' '.$deal_id.' '.$filename_receipt.' '.$filename_invoice."\n", FILE_APPEND);


// ------------------- Платежное поручение ---------
$pp_matches = glob('../../ftp/From1C/PlatezhnoePoruchenieVkhodyashchee*.json');
$filename_pp = $pp_matches[0];
$dealID = substr($filename_pp, 58, 6);


if(isset($dealID)) {

    $querydata = array(
        'id' => $dealID
     );
    
    $queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.get.json';
    $queryData1 = http_build_query($querydata);
    
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
    $categoryid = $decoded_data['result']['CATEGORY_ID'];
    
    if ($categoryid == '1') {
        $stageid = 'C1:WON';
    }
    elseif ($categoryid == '2'){
        $stageid = 'C2:WON';
    }
    elseif ($categoryid == '3'){
        $stageid = 'C3:WON';
    }
    elseif ($categoryid == '7'){
        $stageid = 'C7:WON';
    }
    elseif ($categoryid == '8'){
        $stageid = 'C8:WON';
    }
//echo $stageid."\n";
file_put_contents('logs/pdf_handler.log', date("d/m/y - H:i -", time()).' '.$dealID.' '.$stageid.' '.$filename_pp."\n", FILE_APPEND);
    //----------------- update stage of deal ---------------
    
    $stagerequest = array(
        'id' => $dealID,
        'fields' => array('STAGE_ID' => $stageid)
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
    //print_r($decoded_data);
    
    unlink($filename_pp);

    }
    
    
    echo 'Нет платежного поручения'."\n"; 
    

// ------------------- Кассовый ордер -------------------
$pko_matches = glob('../../ftp/From1C/PrikhodnyyKassovyyOrder*.json');
$filename_pko = $pko_matches[0];
$dealID = substr($filename_pko, 48, 6);




    if(isset($dealID)) {

        $querydata = array(
            'id' => $dealID
         );
        
        $queryUrl1 = 'https://bitrix.cooker.kz/rest/1/wy6bf0od2nkqahie/crm.deal.get.json';
        $queryData1 = http_build_query($querydata);
        
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
        $categoryid = $decoded_data['result']['CATEGORY_ID'];
        
        if ($categoryid == '1') {
            $stageid = 'C1:WON';
        }
        elseif ($categoryid == '2'){
            $stageid = 'C2:WON';
        }
        elseif ($categoryid == '3'){
            $stageid = 'C3:WON';
        }
        elseif ($categoryid == '7'){
            $stageid = 'C7:WON';
        }
        elseif ($categoryid == '8'){
            $stageid = 'C8:WON';
        }
    //echo $stageid."\n";

    file_put_contents('logs/pdf_handler.log', date("d/m/y - H:i -", time()).' '.$dealID.' '.$stageid.' '.$filename_pko."\n", FILE_APPEND);
       
    //----------------- update stage of deal ---------------
        
        $stagerequest = array(
            'id' => $dealID,
            'fields' => array('STAGE_ID' => $stageid)
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
        //print_r($decoded_data);
        
        unlink($filename_pko);
    
        }
        
        echo 'Нет кассового ордера'."\n"; 

?>