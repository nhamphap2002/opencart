<?php

class VM_COMMWEB_HOSTED_API {

    public $log;
    public $_live_url = "https://paymentgateway.commbank.com.au/api/nvp/version/36";
    public $_checkout_url_js = 'https://paymentgateway.commbank.com.au/checkout/version/36/checkout.js';
    public $commweb_merchant_id;
    public $commweb_api_password;
    public $merchant_name;
    public $commweb_checkout_method;
    public $debug;

    function __construct($commweb_merchant_id, $commweb_api_password, $merchant_name, $commweb_checkout_method, $debug) {
        $this->commweb_merchant_id = $commweb_merchant_id;
        $this->commweb_api_password = $commweb_api_password;
        $this->merchant_name = $merchant_name;
        $this->commweb_checkout_method = $commweb_checkout_method;
        $this->debug = $debug;
    }

    public function log($filelog, $contentlog) {
        file_put_contents(dirname(dirname(__FILE__)) . '/' . $filelog, $contentlog, FILE_APPEND);
    }

    public function getMerchantId() {
        return $this->commweb_merchant_id;
    }

    public function getApiPassword() {
        return $this->commweb_api_password;
    }

    public function getMerchantName() {
        return $this->merchant_name;
    }

    public function getApiUsername() {
        $merchant_id = $this->commweb_merchant_id;
        return 'merchant.' . $merchant_id;
    }

    public function getCheckoutSession($order, $id_for_commweb) {

        $amount = number_format($order['total'], 2, '.', '') * 100;
        $merchant = $this->commweb_merchant_id;
        $apiPassword = $this->commweb_api_password;
        $url = $this->_live_url;

        $fields = array(
            'apiOperation' => urlencode('CREATE_CHECKOUT_SESSION'),
            'apiPassword' => urlencode($apiPassword),
            'apiUsername' => urlencode($this->getApiUsername()),
            'merchant' => urlencode($merchant),
            'order.id' => urlencode($id_for_commweb),
            'order.amount' => urlencode($amount),
            'order.currency' => urlencode('AUD')
        );
        $fields_string = '';
        if ($this->debug) {
            $this->log('commweb.log', 'Checkout session request: ' . print_r($fields, true));
        }
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($result != '') {
            $arr_session_id = null;
            parse_str(html_entity_decode($result), $arr_session_id);
            if ($this->debug) {
                $this->log('commweb.log', date('Y-m-d H:i:s') . '\n Checkout session response: ' . print_r($arr_session_id, true) . '\n');
            }
            if (isset($arr_session_id['result']) && $arr_session_id['result'] == 'ERROR') {
                $session_id = '';
            } else {
                $session_id = $arr_session_id['session_id'];
                $_SESSION['SuccessIndicator'] = $arr_session_id['successIndicator'];
                $_SESSION['CurrentOrderId'] = $order['order_id'];
            }
        } else {
            $this->log('commweb.log', date('Y-m-d H:i:s') . '\n Error: ' . print_r($error, true) . '\n');
            $session_id = '';
        }

        return $session_id;
    }

    public function getOrderCommwebDetail($id_for_commweb) {
        $url = $this->_live_url;
        $fields = array(
            'apiOperation' => urlencode('RETRIEVE_ORDER'),
            'apiPassword' => urlencode($this->getApiPassword()),
            'apiUsername' => urlencode($this->getApiUsername()),
            'merchant' => urlencode($this->getMerchantId()),
            'order.id' => urlencode($id_for_commweb)
        );
        $fields_string = '';
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = str_replace("%5B0%5D", '', $result);
        $output = null;
        parse_str(html_entity_decode($result), $output);
        if ($this->debug) {
            $this->log('commweb.log', date('Y-m-d H:i:s') . '\n Order detail from commweb: ' . print_r($output, true) . '\n');
        }
        return $output;
    }

}
