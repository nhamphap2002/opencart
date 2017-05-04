<?php

class ControllerExtensionPaymentSecurepay extends Controller {

    public function index() {
        $this->language->load('extension/payment/securepay');

        $data['button_confirm'] = $this->language->get('button_confirm');
        if ($this->config->get('securepay_enviroment') == 'test')
            $data['gateway_url'] = 'https://test.payment.securepay.com.au/secureframe/invoice';
        else
            $data['gateway_url'] = 'https://payment.securepay.com.au/secureframe/invoice';

        $merchant_id = $this->config->get('securepay_merchant_id');
        $transaction_password = $this->config->get('securepay_transaction_password');
        $merchant_id = $this->config->get('securepay_merchant_id');
        $securepay_currency = $this->config->get('securepay_currency');
        $display_cardholder_name = $this->config->get('securepay_display_cardholder_name');
        $display_securepay_receipt = $this->config->get('securepay_display_securepay_receipt');
        $template_type = $this->config->get('securepay_template_type');
        $iframe_width = $this->config->get('securepay_iframe_width');
        $iframe_height = $this->config->get('securepay_iframe_height');
        $transaction_type = $this->config->get('securepay_transaction_type');

        $surcharge = $this->config->get('securepay_surcharge');
        $surcharge_visa = $this->config->get('securepay_surcharge_visa');
        $surcharge_visa_value = $this->config->get('securepay_surcharge_visa_value');
        $surcharge_mastercard = $this->config->get('securepay_surcharge_mastercard');
        $surcharge_mastercard_value = $this->config->get('securepay_surcharge_mastercard_value');
        $surcharge_amex = $this->config->get('securepay_surcharge_amex');
        $surcharge_amex_value = $this->config->get('securepay_surcharge_amex_value');

        $this->load->model('checkout/order');

        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        $amount = number_format($order_info['total'], 2, '.', '') * 100;

        $return_url = $this->config->get('config_ssl') . 'catalog/controller/extension/payment/securepay_return.php';
        //$this->url->link('extension/payment/securepay/success', '', 'SSL');//'http://demo.vnphpexpert.com/opencart_2/test.php';//
        $notify_url = $this->url->link('extension/payment/securepay/callback', '', 'SSL');
        $cancel_url = $this->config->get('config_ssl') . 'catalog/controller/extension/payment/securepay_return.php'; //$this->url->link('checkout/checkout');

        $fp_timestamp = gmdate('Ymdhis');
        $fingerprint = $merchant_id . '|' . $transaction_password . '|' . $transaction_type . '|' . $order_id . '|' . $amount . '|' . $fp_timestamp;
        $fingerprint = hash('sha1', $fingerprint);

        $parameters = array(
            "bill_name" => "transact",
            "merchant_id" => $merchant_id,
            "primary_ref" => $order_id,
            "txn_type" => $transaction_type,
            "currency" => $securepay_currency,
            "amount" => $amount,
            "fp_timestamp" => $fp_timestamp,
            "fingerprint" => $fingerprint,
            "return_url" => $return_url,
            "return_url_target" => 'parent',
            "cancel_url" => $cancel_url,
            "callback_url" => $notify_url,
            "template" => $template_type,
        );


        if ($display_securepay_receipt == '1')
            $parameters['display_receipt'] = 'yes';
        else
            $parameters['display_receipt'] = 'no';

        if ($display_cardholder_name == '1')
            $parameters['display_cardholder_name'] = 'yes';
        else
            $parameters['display_cardholder_name'] = 'no';

        if ($surcharge == 1) {
            $surcharge_visa_percen = 0;
            $surcharge_mastercard_percen = 0;
            $surcharge_amex_percen = 0;

            //visa
            if ($surcharge_visa == 'flat') {
                $surcharge_visa_percen = round($surcharge_visa_value / $order_info['total'], 2);
            } elseif ($surcharge_visa == 'percentage') {
                $surcharge_visa_percen = round($surcharge_visa_value, 2);
            }

            //mastercard
            if ($surcharge_mastercard == 'flat') {
                $surcharge_mastercard_percen = round($surcharge_mastercard_value / $order_info['total'], 2);
            } elseif ($surcharge_mastercard == 'percentage') {
                $surcharge_mastercard_percen = round($surcharge_mastercard_value, 2);
            }

            //amex
            if ($surcharge_amex == 'flat') {
                $surcharge_amex_percen = round($surcharge_amex_value / $order_info['total'], 2);
            } elseif ($surcharge_amex == 'percentage') {
                $surcharge_amex_percen = round($surcharge_amex_value, 2);
            }

            if ($surcharge_visa_percen > 0)
                $parameters['surcharge_rate_v'] = $surcharge_visa_percen;

            if ($surcharge_mastercard_percen > 0)
                $parameters['surcharge_rate_m'] = $surcharge_mastercard_percen;

            if ($surcharge_amex_percen > 0)
                $parameters['surcharge_rate_a'] = $surcharge_amex_percen;
        }

        $iframe = '';
        $target = '';
        if ($template_type == 'iframe') {
            $width = !empty($iframe_width) ? $iframe_width . 'px' : '60%';
            $height = !empty($iframe_height) ? $iframe_height . 'px' : '400px';

            $iframe = '<iframe name="securepay_chekout_frame" src="" id="securepay_chekout_frame" width="' . $width . '" height="' . $height . '" style="display:none;"></iframe>  ';
            $target = 'target="securepay_chekout_frame"';
        }
        $data['iframe'] = $iframe;
        $data['target'] = $target;
        //save card info
        $parameters["store"] = "yes";
        $parameters["store_type"] = "PAYOR";
        $parameters["payor"] = 'securepay_' . $this->customer->getId();
        
        
        
        $data['parameters'] = $parameters;
        
        $data['action_pay_saved_card'] = $this->url->link('extension/payment/securepay/paymentWithSavedCard', '', 'SSL');
        $data["orderid"] = $order_id;
        $data["payor"] = 'securepay_' . $this->customer->getId();
        
        if (file_exists(DIR_APPLICATION . 'view/theme/' . $this->config->get('config_template') . '/extension/payment/securepay.tpl')) {
            return $this->load->view(DIR_APPLICATION . 'view/theme/' . $this->config->get('config_template') . '/extension/payment/securepay.tpl', $data);
        } else {
            return $this->load->view('extension/payment/securepay.tpl', $data);
        }
    }

    public function callback() {
        $this->load->model('extension/payment/securepay');

        $merchant_id = $this->config->get('securepay_merchant_id');
        $transaction_password = $this->config->get('securepay_transaction_password');
        $transaction_type = $this->config->get('securepay_transaction_type');

        $order_id = $_POST["refid"];
        $fingerprint = $_POST["fingerprint"];
        $timestamp = $_POST["timestamp"];
        $amount = $_POST["amount"];
        $summarycode = $_POST["summarycode"];

        $txnid = $_POST["txnid"];
        $rescode = $_POST["rescode"];

        $fingerprint_string = $merchant_id . '|' . $transaction_password . '|' . $order_id . '|' . $amount . '|' . $timestamp . '|' . $summarycode;
        $fingerprint_hash = hash('sha1', $fingerprint_string);
        if ($fingerprint_hash == $fingerprint && in_array($rescode, array('00', '08', '11'))) {
            //success
            $this->load->model('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            $msg = '';

            if (in_array($transaction_type, array(0, 2, 4, 6))) {
                //payment transaction
                $msg = 'Transaction ID: ' . $txnid;
                //log transaction info to database
                $securepay_order_id = $this->model_extension_payment_securepay->addOrder($order_info, $txnid);
                //Save card info
                if ($order_info['customer_id'] > 0) {
                    if (!$this->model_extension_payment_securepay->getCard($order_info['customer_id'])) {
                        $this->model_extension_payment_securepay->addCard($this->request->post, $order_info['customer_id']);
                    } else {
                        $this->model_extension_payment_securepay->updateCard($this->request->post, $order_info['customer_id']);
                    }
                }
            } else {
                //auth transaction
                $msg = 'Auth ID: ' . $txnid;
            }
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('securepay_order_status_id'), $msg);
        } else {
            //false
        }
    }

    public function success() {
        $merchant_id = $this->config->get('securepay_merchant_id');
        $transaction_password = $this->config->get('securepay_transaction_password');

        $order_id = $_GET["refid"];
        $fingerprint = $_GET["fingerprint"];
        $timestamp = $_GET["timestamp"];
        $amount = $_GET["amount"];
        $summarycode = $_GET["summarycode"];

        $rescode = $_GET["rescode"];

        $fingerprint_string = $merchant_id . '|' . $transaction_password . '|' . $order_id . '|' . $amount . '|' . $timestamp . '|' . $summarycode;
        $fingerprint_hash = hash('sha1', $fingerprint_string);
        if ($fingerprint_hash == $fingerprint && in_array($rescode, array('00', '08', '11'))) {
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        } else {
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }
    }

    public function paymentWithSavedCard() {
        echo 'tét';exit();
        if (isset($this->request->post['order_id']) && !empty($this->request->post['order_id'])) {
            $this->load->model('extension/payment/securepay');
            $order_id = $this->request->post['order_id'];           
            $response = $this->model_extension_payment_securepay->payByXml($order_id, $this->request->post['payor']);
            print_r($response);exit();
            if (isset($response['banktxnID']) && $response['banktxnID'] != '') {
                $this->load->model('checkout/order');
                $order_info = $this->model_checkout_order->getOrder($order_id);
                $securepay_order_id = $this->model_extension_payment_securepay->addOrder($order_info, $response['banktxnID']);
               
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('securepay_order_status_id'), "Payment successful");
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
            }else{
                $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
            }
        } else {
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }
    }
}

?>