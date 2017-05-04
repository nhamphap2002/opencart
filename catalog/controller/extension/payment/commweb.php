<?php

if (!class_exists('VM_COMMWEB_HOSTED_API')) {
    require_once('commweb/class-commweb-api.php');
}

class ControllerExtensionPaymentCommweb extends Controller {

    public function index() {
        $this->language->load('extension/payment/commweb');

        $data['button_confirm'] = $this->language->get('button_confirm');

        $merchant_id = $this->config->get('commweb_merchant_id');
        $api_password = $this->config->get('commweb_api_password');


        $this->load->model('checkout/order');

        $order_id = $this->session->data['order_id'];
        $order_info = $this->model_checkout_order->getOrder($order_id);

        $amount = number_format($order_info['total'], 2, '.', '') * 100;

        $return_url = $this->config->get('config_ssl') . 'catalog/controller/extension/payment/commweb_return.php';
        //$this->url->link('extension/payment/commweb/success', '', 'SSL');//'http://demo.vnphpexpert.com/opencart_2/test.php';//
        $notify_url = $this->url->link('extension/payment/commweb/callback', '', 'SSL');
        $cancel_url = $this->config->get('config_ssl') . 'catalog/controller/extension/payment/commweb_return.php'; //$this->url->link('checkout/checkout');
        //save card info
        $parameters["store"] = "yes";
        $parameters["store_type"] = "PAYOR";
        $parameters["payor"] = 'commweb_' . $this->customer->getId();



        $data['parameters'] = $parameters;

        $data['action_showform'] = $this->url->link('extension/payment/commweb/showformpaymentCommweb&on=' . $order_id, '', 'SSL');
        $data["orderid"] = $order_id;
        $data["payor"] = 'commweb_' . $this->customer->getId();

        if (file_exists(DIR_APPLICATION . 'view/theme/' . $this->config->get('config_template') . '/extension/payment/commweb.tpl')) {
            return $this->load->view(DIR_APPLICATION . 'view/theme/' . $this->config->get('config_template') . '/extension/payment/commweb.tpl', $data);
        } else {
            return $this->load->view('extension/payment/commweb.tpl', $data);
        }
    }

    public function callback() {
        $this->load->model('extension/payment/commweb');

        $merchant_id = $this->config->get('commweb_merchant_id');
        $transaction_password = $this->config->get('commweb_transaction_password');
        $transaction_type = $this->config->get('commweb_transaction_type');

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
                $commweb_order_id = $this->model_extension_payment_commweb->addOrder($order_info, $txnid);
                //Save card info
                if ($order_info['customer_id'] > 0) {
                    if (!$this->model_extension_payment_commweb->getCard($order_info['customer_id'])) {
                        $this->model_extension_payment_commweb->addCard($this->request->post, $order_info['customer_id']);
                    } else {
                        $this->model_extension_payment_commweb->updateCard($this->request->post, $order_info['customer_id']);
                    }
                }
            } else {
                //auth transaction
                $msg = 'Auth ID: ' . $txnid;
            }
            $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('commweb_order_status_id'), $msg);
        } else {
            //false
        }
    }

    public function success() {
        $merchant_id = $this->config->get('commweb_merchant_id');
        $transaction_password = $this->config->get('commweb_transaction_password');

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

    public function showformpaymentCommweb() {

        $data['merchant_id'] = $merchant_id = $this->config->get('commweb_merchant_id');
        $data['api_password'] = $api_password = $this->config->get('commweb_api_password');
        $data['merchant_name'] = $merchant_name = 'Commweb';
        $data['checkout_method'] = $checkout_method = $this->config->get('commweb_checkout_method');
        $data['debug'] = $debug = $this->config->get('commweb_debug_log');


        $this->load->model('checkout/order');

        $order_id = $_REQUEST['on'];
        $order_info = $this->model_checkout_order->getOrder($order_id);
        echo $merchant_id;
        print_r($order_info);
        exit();


        $commweb = new VM_COMMWEB_HOSTED_API($merchant_id, $api_password, $merchant_name, $checkout_method, $debug);

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('extension/payment/formcommweb', $data));
    }

}

?>