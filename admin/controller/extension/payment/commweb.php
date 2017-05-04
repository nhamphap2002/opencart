<?php

/*
 * Created on : May 03, 2017, 9:54:39 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 *  */
/*
 * Card number* 5111111111111118
 * Expiry date* 05 / 17
 * Cardholder name* admin test
 * Security code* 100
 * Street address Whiskey St
 * Postcode / Zipcode 4556
 * Country Australia
 * State / Province 
 */

class ControllerExtensionPaymentCommweb extends Controller {

    private $error = array();

    public function index() {
        //print_r($this->request->post);exit();
        $this->load->language('extension/payment/commweb');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            $this->model_setting_setting->editSetting('commweb', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
        $data['entry_api_password'] = $this->language->get('entry_api_password');
        $data['entry_checkout_method'] = $this->language->get('entry_checkout_method');
        $data['entry_3d_secure'] = $this->language->get('entry_3d_secure');
        $data['entry_debug_log'] = $this->language->get('entry_debug_log');
        //surcharge

        $data['entry_order_status'] = $this->language->get('entry_order_status');
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
        if (isset($this->error['api_password'])) {
            $data['error_api_password'] = $this->error['api_password'];
        } else {
            $data['error_api_password'] = '';
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
            'href' => $this->url->link('extension/commweb', 'token=' . $this->session->data['token'], 'SSL'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/payment/commweb', 'token=' . $this->session->data['token'], 'SSL');

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['commweb_merchant_id'])) {
            $data['commweb_merchant_id'] = $this->request->post['commweb_merchant_id'];
        } else {
            $data['commweb_merchant_id'] = $this->config->get('commweb_merchant_id');
        }

        if (isset($this->request->post['commweb_api_password'])) {
            $data['commweb_api_password'] = $this->request->post['commweb_api_password'];
        } else {
            $data['commweb_api_password'] = $this->config->get('commweb_api_password');
        }

        ///////////
        if (isset($this->request->post['commweb_checkout_method'])) {
            $data['commweb_checkout_method'] = $this->request->post['commweb_checkout_method'];
        } else {
            $data['commweb_checkout_method'] = $this->config->get('commweb_checkout_method');
        }
        //print_r($this->request->post);exit();
        if (isset($this->request->post['commweb_3d_secure'])) {
            $data['commweb_3d_secure'] = $this->request->post['commweb_3d_secure'];
        } else {
            $data['commweb_3d_secure'] = $this->config->get('commweb_3d_secure');
        }
        if (isset($this->request->post['commweb_debug_log'])) {
            $data['commweb_debug_log'] = $this->request->post['commweb_debug_log'];
        } else {
            $data['commweb_debug_log'] = $this->config->get('commweb_debug_log');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


        if (isset($this->request->post['commweb_status'])) {
            $data['commweb_status'] = $this->request->post['commweb_status'];
        } else {
            $data['commweb_status'] = $this->config->get('commweb_status');
        }

        if (isset($this->request->post['commweb_order_status_id'])) {
            $data['commweb_order_status_id'] = $this->request->post['commweb_order_status_id'];
        } else {
            $data['commweb_order_status_id'] = $this->config->get('commweb_order_status_id');
        }

        if (isset($this->request->post['commweb_geo_zone_id'])) {
            $data['commweb_geo_zone_id'] = $this->request->post['commweb_geo_zone_id'];
        } else {
            $data['commweb_geo_zone_id'] = $this->config->get('commweb_geo_zone_id');
        }
        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        if (isset($this->request->post['commweb_sort_order'])) {
            $data['commweb_sort_order'] = $this->request->post['commweb_sort_order'];
        } else {
            $data['commweb_sort_order'] = $this->config->get('commweb_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/commweb.tpl', $data));
    }

    public function install() {
        $this->load->model('extension/payment/commweb');

        $this->model_extension_payment_commweb->install();
    }

    public function orderAction() {
        if ($this->config->get('commweb_status')) {
            $this->load->model('extension/payment/commweb');

            $commweb_order = $this->model_extension_payment_commweb->getOrder($this->request->get['order_id']);

            if (!empty($commweb_order)) {
                $data['order_id'] = $this->request->get['order_id'];
                $data['token'] = $this->request->get['token'];
                $data['commweb_order'] = $commweb_order;

                return $this->load->view('extension/payment/commweb_order.tpl', $data);
            }
        }
    }

    public function rebate() {
        $json = array();

        if (isset($this->request->post['order_id']) && !empty($this->request->post['order_id'])) {
            $this->load->model('extension/payment/commweb');

            $commweb_order = $this->model_extension_payment_commweb->getOrder($this->request->post['order_id']);

            $rebate_response = $this->model_extension_payment_commweb->rebate($this->request->post['order_id']);

            if (isset($rebate_response['banktxnID']) && $rebate_response['banktxnID'] != '') {
                $this->model_extension_payment_commweb->updateRebateStatus($commweb_order['commweb_order_id'], 1);
                $rebate_status = 1;
                $json['msg'] = 'Refunded success!';

                //update order status to refunded
                $msg = 'Refund ID: ' . $rebate_response['banktxnID'];
                $data = array(
                    'order_status_id' => $this->config->get('commweb_refund_status_id'),
                    'notify' => False,
                    'comment' => $msg,
                );
                $this->model_extension_payment_commweb->addOrderHistory($this->request->post['order_id'], $data);

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
        if (!$this->user->hasPermission('modify', 'extension/payment/commweb')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['commweb_merchant_id']) {
            $this->error['merchant_id'] = $this->language->get('error_merchant_id');
        }
        if (!$this->request->post['commweb_api_password']) {
            $this->error['transaction_password'] = $this->language->get('error_api_password');
        }

        if (!$this->error) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

?>