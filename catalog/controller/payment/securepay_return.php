<?php
if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
}
else{
        $protocol = 'http';
}
$address_url =  $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$address_url = str_replace('catalog/controller/payment/securepay_return.php', 'index.php?route=payment/securepay/success&', $address_url);
$address_url .= http_build_query($_POST);
header('location:' . $address_url);
exit;