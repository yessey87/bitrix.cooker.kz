<?php

//------------------- Receipt stuff ----------------
$receipt_matches = glob('../../ftp/From1C/*_Chek.PDF');
$filename_receipt = $receipt_matches[0];
$deal_id = trim($filename_receipt,'../../ftp/From1C/._Check.PDF');
$base64_receipt = base64_encode(file_get_contents($filename_receipt));

//------------------- Invoice stuff ----------------
$invoice_matches = glob('../../ftp/From1C/*_Order.PDF');
$filename_invoice = $invoice_matches[0];
$base64_invoice = base64_encode(file_get_contents($filename_invoice));

// ------------------- Платежное поручение ---------
$payment_matches = glob('../../ftp/From1C/PlatezhnoePoruchenieVkhodyashchee*.json');
$filename_payment = $payment_matches[0];
$dealID = substr($filename_payment, 58, 6);
echo $dealID;


//---------------------------------------------------
$dealupdate = array(
    'id' => $deal_id,
    'fields' => array('UF_CRM_1606904604' => array(
                                            'fileData' => ["Товарный-чек.pdf", $base64_receipt]
    )),
    'fields' => array('UF_CRM_1606904558' => array(
                                            'fileData' => ["Товарная-накладная.pdf", $base64_invoice]
))
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
$decoded_data = json_decode($result2, true);
//$dealID = $decoded_data['result'][0]['ID'];
//print_r($decoded_data);

//unlink($filename_invoice);
//unlink($filename_receipt);

file_put_contents('logs/pdf_hadler.log', date("d/m/y - H:i -", time()).' '.$filename_receipt.' '.$filename_invoice."\n", FILE_APPEND);

//------- update bx24 deal status ---------------

//--------- kaspi almaty: ОПЛАЧЕНО - C2:9 ------------
//--------- kaspi Казахстан: ОПЛАЧЕНО - C3:4 ------------

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
file_put_contents('logs/pdf_hadler.log', date("d/m/y - H:i -", time()).' '.$categoryid.' '.$filename_receipt.' '.$filename_invoice."\n", FILE_APPEND);

if ($categoryid == '2'){
    $stageid = 'C2:9';
}
else $stageid = 'C3:4';

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

}

echo 'nothing to do'; 

?>