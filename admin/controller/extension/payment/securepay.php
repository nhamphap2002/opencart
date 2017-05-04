<?php

class ControllerExtensionPaymentSecurepay extends Controller {

    private $error = array();

    public function index() {
        $this->load->language('extension/payment/securepay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('securepay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
        $data['entry_transaction_password'] = $this->language->get('entry_transaction_password');
        $data['entry_currency'] = $this->language->get('entry_currency');
        $data['entry_display_cardholder_name'] = $this->language->get('entry_display_cardholder_name');
        $data['entry_display_securepay_receipt'] = $this->language->get('entry_display_securepay_receipt');
        $data['entry_template_type'] = $this->language->get('entry_template_type');
        $data['entry_iframe_width'] = $this->language->get('entry_iframe_width');
        $data['entry_iframe_height'] = $this->language->get('entry_iframe_height');
        $data['entry_transaction_type'] = $this->language->get('entry_transaction_type');
        //surcharge

        $data['entry_enviroment'] = $this->language->get('entry_enviroment');

        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant_id'])) {
            $data['error_merchant_id'] = $this->error['merchant_id'];
        } else {
            $data['error_merchant_id'] = '';
        }
        if (isset($this->error['transaction_password'])) {
            $data['error_transaction_password'] = $this->error['transaction_password'];
        } else {
            $data['error_transaction_password'] = '';
        }

        $data['breadcrumbs'][] = array(
            'href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('text_payment'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'href' => $this->url->link('extension/securepay', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/payment/securepay', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['securepay_merchant_id'])) {
            $data['securepay_merchant_id'] = $this->request->post['securepay_merchant_id'];
        } else {
            $data['securepay_merchant_id'] = $this->config->get('securepay_merchant_id');
        }

        if (isset($this->request->post['securepay_transaction_password'])) {
            $data['securepay_transaction_password'] = $this->request->post['securepay_transaction_password'];
        } else {
            $data['securepay_transaction_password'] = $this->config->get('securepay_transaction_password');
        }

        if (isset($this->request->post['securepay_currency'])) {
            $data['securepay_currency'] = $this->request->post['securepay_currency'];
        } else {
            $data['securepay_currency'] = $this->config->get('securepay_currency');
        }
        ///////////
        if (isset($this->request->post['securepay_display_cardholder_name'])) {
            $data['securepay_display_cardholder_name'] = $this->request->post['securepay_display_cardholder_name'];
        } else {
            $data['securepay_display_cardholder_name'] = $this->config->get('securepay_display_cardholder_name');
        }
        if (isset($this->request->post['securepay_display_securepay_receipt'])) {
            $data['securepay_display_securepay_receipt'] = $this->request->post['securepay_display_securepay_receipt'];
        } else {
            $data['securepay_display_securepay_receipt'] = $this->config->get('securepay_display_securepay_receipt');
        }
        if (isset($this->request->post['securepay_template_type'])) {
            $data['securepay_template_type'] = $this->request->post['securepay_template_type'];
        } else {
            $data['securepay_template_type'] = $this->config->get('securepay_template_type');
        }
        if (isset($this->request->post['securepay_iframe_width'])) {
            $data['securepay_iframe_width'] = $this->request->post['securepay_iframe_width'];
        } else {
            $data['securepay_iframe_width'] = $this->config->get('securepay_iframe_width');
        }
        if (isset($this->request->post['securepay_iframe_height'])) {
            $data['securepay_iframe_height'] = $this->request->post['securepay_iframe_height'];
        } else {
            $data['securepay_iframe_height'] = $this->config->get('securepay_iframe_height');
        }
        if (isset($this->request->post['securepay_transaction_type'])) {
            $data['securepay_transaction_type'] = $this->request->post['securepay_transaction_type'];
        } else {
            $data['securepay_transaction_type'] = $this->config->get('securepay_transaction_type');
        }////////////

        if (isset($this->request->post['securepay_enviroment'])) {
            $data['securepay_enviroment'] = $this->request->post['securepay_enviroment'];
        } else {
            $data['securepay_enviroment'] = $this->config->get('securepay_enviroment');
        }
        if (isset($this->request->post['securepay_surcharge'])) {
            $data['securepay_surcharge'] = $this->request->post['securepay_surcharge'];
        } else {
            $data['securepay_surcharge'] = $this->config->get('securepay_surcharge');
        }
        //visa
        if (isset($this->request->post['securepay_surcharge_visa'])) {
            $data['securepay_surcharge_visa'] = $this->request->post['securepay_surcharge_visa'];
        } else {
            $data['securepay_surcharge_visa'] = $this->config->get('securepay_surcharge_visa');
        }
        if (isset($this->request->post['securepay_surcharge_visa_value'])) {
            $data['securepay_surcharge_visa_value'] = $this->request->post['securepay_surcharge_visa_value'];
        } else {
            $data['securepay_surcharge_visa_value'] = $this->config->get('securepay_surcharge_visa_value');
        }
        //Mastercard
        if (isset($this->request->post['securepay_surcharge_mastercard'])) {
            $data['securepay_surcharge_mastercard'] = $this->request->post['securepay_surcharge_mastercard'];
        } else {
            $data['securepay_surcharge_mastercard'] = $this->config->get('securepay_surcharge_mastercard');
        }
        if (isset($this->request->post['securepay_surcharge_mastercard_value'])) {
            $data['securepay_surcharge_mastercard_value'] = $this->request->post['securepay_surcharge_mastercard_value'];
        } else {
            $data['securepay_surcharge_mastercard_value'] = $this->config->get('securepay_surcharge_mastercard_value');
        }
        //American Express
        if (isset($this->request->post['securepay_surcharge_amex'])) {
            $data['securepay_surcharge_amex'] = $this->request->post['securepay_surcharge_amex'];
        } else {
            $data['securepay_surcharge_amex'] = $this->config->get('securepay_surcharge_amex');
        }
        if (isset($this->request->post['securepay_surcharge_amex_value'])) {
            $data['securepay_surcharge_amex_value'] = $this->request->post['securepay_surcharge_amex_value'];
        } else {
            $data['securepay_surcharge_amex_value'] = $this->config->get('securepay_surcharge_amex_value');
        }

        if (isset($this->request->post['securepay_order_status_id'])) {
            $data['securepay_order_status_id'] = $this->request->post['securepay_order_status_id'];
        } else {
            $data['securepay_order_status_id'] = $this->config->get('securepay_order_status_id');
        }

        if (isset($this->request->post['securepay_refund_status_id'])) {
            $data['securepay_refund_status_id'] = $this->request->post['securepay_refund_status_id'];
        } else {
            $data['securepay_refund_status_id'] = $this->config->get('securepay_refund_status_id');
        }

        $data['currency_codes'] = array(
            'AUD',
            'BRL',
            'CAD',
            'CZK',
            'DKK',
            'EUR',
            'HKD',
            'HUF',
            'ILS',
            'JPY',
            'MYR',
            'MXN',
            'NOK',
            'NZD',
            'PHP',
            'PLN',
            'GBP',
            'SGD',
            'SEK',
            'CHF',
            'TWD',
            'THB',
            'TRY',
            'USD',
        );

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['securepay_geo_zone_id'])) {
            $data['securepay_geo_zone_id'] = $this->request->post['securepay_geo_zone_id'];
        } else {
            $data['securepay_geo_zone_id'] = $this->config->get('securepay_geo_zone_id');
        }

        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['securepay_status'])) {
            $data['securepay_status'] = $this->request->post['securepay_status'];
        } else {
            $data['securepay_status'] = $this->config->get('securepay_status');
        }

        if (isset($this->request->post['securepay_sort_order'])) {
            $data['securepay_sort_order'] = $this->request->post['securepay_sort_order'];
        } else {
            $data['securepay_sort_order'] = $this->config->get('securepay_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/securepay.tpl', $data));
    }

    public function install() {
        $this->load->model('extension/payment/securepay');

        $this->model_extension_payment_securepay->install();
    }

    public function orderAction() {
        if ($this->config->get('securepay_status')) {
            $this->load->model('extension/payment/securepay');

            $securepay_order = $this->model_extension_payment_securepay->getOrder($this->request->get['order_id']);

            if (!empty($securepay_order)) {
                $data['order_id'] = $this->request->get['order_id'];
                $data['token'] = $this->request->get['token'];
                $data['securepay_order'] = $securepay_order;

                return $this->load->view('extension/payment/securepay_order.tpl', $data);
            }
        }
    }

    public function rebate() {
        $json = array();

        if (isset($this->request->post['order_id']) && !empty($this->request->post['order_id'])) {
            $this->load->model('extension/payment/securepay');

            $securepay_order = $this->model_extension_payment_securepay->getOrder($this->request->post['order_id']);

            $rebate_response = $this->model_extension_payment_securepay->rebate($this->request->post['order_id']);

            if (isset($rebate_response['banktxnID']) && $rebate_response['banktxnID'] != '') {
                $this->model_extension_payment_securepay->updateRebateStatus($securepay_order['securepay_order_id'], 1);
                $rebate_status = 1;
                $json['msg'] = 'Refunded success!';

                //update order status to refunded
                $msg = 'Refund ID: ' . $rebate_response['banktxnID'];
                $data = array(
                    'order_status_id' => $this->config->get('securepay_refund_status_id'),
                    'notify' => False,
                    'comment' => $msg,
                );
                $this->model_extension_payment_securepay->addOrderHistory($this->request->post['order_id'], $data);

                $json['data'] = array();
                $json['data']['rebate_status'] = $rebate_status;
                $json['error'] = false;
            } else {
                $json['error'] = true;
                $json['msg'] = 'Unable to rebate';
            }
        } else {
            $json['error'] = true;
            $json['msg'] = 'Missing data';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/securepay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['securepay_merchant_id']) {
            $this->error['merchant_id'] = $this->language->get('error_merchant_id');
        }
        if (!$this->request->post['securepay_transaction_password']) {
            $this->error['transaction_password'] = $this->language->get('error_transaction_password');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>