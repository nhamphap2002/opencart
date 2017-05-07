<?php
/*
 * Created on : May 03, 2017, 9:54:39 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 *  */
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

        $notify_url = $this->url->link('extension/payment/commweb/callback', '', 'SSL');
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

        $commweb = new VM_COMMWEB_HOSTED_API($merchant_id, $api_password, $merchant_name, $checkout_method, $debug);

        $data['commweb'] = $commweb;

        $id_for_commweb = $order_id;
        $_SESSION['id_for_commweb'] = $id_for_commweb;
        $checkout_session_id = $commweb->getCheckoutSession($order_info, $id_for_commweb);

        if ($checkout_method == 'Lightbox') {
            $payment_method = 'Checkout.showLightbox();';
        } else {
            $payment_method = 'Checkout.showPaymentPage();';
        }

        $data['payment_method'] = $payment_method;
        $data['id_for_commweb'] = $order_id;
        $data['checkout_session_id'] = $checkout_session_id;
        $total = number_format($order_info['total'], 2, '.', '') * 100;
        $data['total'] = $total;
        //$data['complete_callback'] = $this->url->link('checkout/success');
        $data['complete_callback'] = $complete_callback = $this->url->link('extension/payment/commweb/callback&on=' . $order_id, '', true);
        $data['cancel_callback'] = $cancel_callback = $this->url->link('checkout/checkout', '', true);


        $data['street'] = $street = $order_info['payment_address_1'];
        $data['city'] = $city = $order_info['payment_city'];
        $data['billing_postcode'] = $billing_postcode = $order_info['payment_postcode'];
        $data['state'] = $state = $order_info['payment_zone_code'];
        $data['country'] = $country = $order_info['payment_iso_code_3'];

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $image_loading = $data['image_loading'] = $this->config->get('config_ssl') . 'catalog/view/theme/default/image/loading.gif';
        } else {
            $image_loading = $data['image_loading'] = $this->config->get('config_url') . 'catalog/view/theme/default/image/loading.gif';
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        if ($checkout_method == 'Lightbox') {
            $this->response->setOutput($this->load->view('extension/payment/commwebform', $data));
        } else {
            $logger = new Log('test.log');
            $logger->write($complete_callback);
            ob_start();
            ?>
            <html>
                <head>
                    <title><?php echo $commweb->merchant_name; ?></title>
                </head>
                <body>
                    <style>
                        #loading{
                            position: fixed;
                            left: 0px;
                            top: 0px;
                            width: 100%;
                            height: 100%;
                            z-index: 9999;
                            background: url('<?php echo $image_loading;
            ?>') 50% 50% no-repeat;
                        }
                    </style>

                    <script src="<?php echo $commweb->_checkout_url_js; ?>" 
                            data-error="errorCallback"
                            data-complete="completeCallback"
                            data-cancel="cancelCallback">
                    </script>

                    <script type="text/javascript">
                        completeCallback = "<?php echo $complete_callback; ?>";
                        cancelCallback = "<?php echo $cancel_callback; ?>";
                        function errorCallback(error) {
                            console.log(JSON.stringify(error))
                            alert(JSON.stringify(error.explanation));
                        }
                    </script>
                    <script type="text/javascript">
                        Checkout.configure({
                            merchant: "<?php echo $commweb->commweb_merchant_id; ?>",
                            session: {
                                id: "<?php echo $checkout_session_id; ?>"
                            },
                            order: {
                                amount: "<?php echo $total; ?>",
                                currency: "AUD",
                                description: "Commweb Order",
                                id: "<?php echo $id_for_commweb; ?>"
                            },
                            billing: {
                                address: {
                                    street: "<?php echo $street; ?>",
                                    city: "<?php echo $city; ?>",
                                    postcodeZip: "<?php echo $billing_postcode; ?>",
                                    stateProvince: "<?php echo $state; ?>",
                                    country: "<?php echo $country; ?>"
                                }
                            },
                            interaction: {
                                merchant: {
                                    name: "<?php echo $commweb->merchant_name; ?>"
                                }
                            }
                        });
            <?php echo $payment_method; ?>
                    </script>
                    <div id="loading"></div>
                </body>
            </html>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
            exit();
        }
    }

    public function callback() {
        //extension/payment/commweb/callback&resultIndicator=3d13197a35394e73&sessionVersion=ecfefa3c06
        $data['merchant_id'] = $merchant_id = $this->config->get('commweb_merchant_id');
        $data['api_password'] = $api_password = $this->config->get('commweb_api_password');
        $data['merchant_name'] = $merchant_name = 'Commweb';
        $data['checkout_method'] = $checkout_method = $this->config->get('commweb_checkout_method');
        $data['debug'] = $debug = $this->config->get('commweb_debug_log');
        $data['commweb_3d_secure'] = $commweb_3d_secure = $this->config->get('commweb_3d_secure');

        $order_id = $_REQUEST['on'];
        $commweb = new VM_COMMWEB_HOSTED_API($merchant_id, $api_password, $merchant_name, $checkout_method, $debug);
        $order_detail_commweb = $commweb->getOrderCommwebDetail($order_id);

        $this->load->model('checkout/order');
        $this->load->model('extension/payment/commweb');
        $order_info = $this->model_checkout_order->getOrder($order_id);

        if ($commweb->debug)
            $commweb->log('commweb.log', date('Y-m-d H:i:s') . "\n Response from Complete callback of commweb: \n" . print_r($order_detail_commweb, true) . "\n");
        $order_status_id = $this->config->get('commweb_order_status_id');
        if ($order_detail_commweb['result'] == 'SUCCESS') {
            if ($commweb_3d_secure) {
                if (isset($order_detail_commweb['transaction_3DSecure_authenticationStatus']) && $order_detail_commweb['transaction_3DSecure_authenticationStatus'] == 'AUTHENTICATION_SUCCESSFUL') {
                    $process_order = true;
                } else {
                    $process_order = false;
                }
            } else {
                $process_order = true;
            }
            if ($process_order == true) {
                $this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
                //add order to commweb table
                $commweb_order_data = array(
                    'order_id' => $order_id,
                    'capture_status' => $order_detail_commweb['result'],
                    'currency_code' => $order_detail_commweb['currency'],
                    'authorization_id' => $order_detail_commweb['transaction_transaction_acquirer_merchantId'],
                    'total' => $order_detail_commweb['amount']
                );

                $commweb_order_id = $this->model_extension_payment_commweb->addOrder($commweb_order_data);

                //add transaction to commweb transaction table
                $commweb_transaction_data = array(
                    'commweb_order_id' => $commweb_order_id,
                    'transaction_id' => $order_detail_commweb['transaction_transaction_id'],
                    'parent_id' => '',
                    'note' => '',
                    'msgsubid' => '',
                    'receipt_id' => $order_detail_commweb['transaction_order_id'],
                    'payment_type' => $order_detail_commweb['sourceOfFunds_provided_card_scheme'],
                    'payment_status' => $order_detail_commweb['status'],
                    'pending_reason' => $order_detail_commweb['transaction_device_ipAddress'],
                    'transaction_entity' => $order_detail_commweb['transaction_device_browser'],
                    'amount' => $order_detail_commweb['transaction_order_amount'],
                    'debug_data' => json_encode($order_detail_commweb)
                );

                $this->model_extension_payment_commweb->addTransaction($commweb_transaction_data);
                $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
            } else {
                $this->response->redirect($this->url->link('checkout/success', 'Your transaction was unsuccessful, please check your details and try again(error account 3d). Please contact the server administrator', 'SSL'));
            }
        } else {
            $this->response->redirect($this->url->link('checkout/success', 'Your transaction was unsuccessful, please check your details and try again. Please contact the server administrator', 'SSL'));
        }
    }

}
?>