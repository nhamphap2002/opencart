<?php

/*
 * Created on : May 03, 2017, 9:54:39 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 *  */

class ModelExtensionPaymentCommweb extends Model {

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/commweb');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('commweb_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");
        if ($this->config->get('commweb_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('commweb_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $currencies = array(
            'AUD',
            'CAD',
            'EUR',
            'GBP',
            'JPY',
            'USD',
            'NZD',
            'CHF',
            'HKD',
            'SGD',
            'SEK',
            'DKK',
            'PLN',
            'NOK',
            'HUF',
            'CZK',
            'ILS',
            'MXN',
            'MYR',
            'BRL',
            'PHP',
            'TWD',
            'THB',
            'TRY',
            'RUB'
        );

        if (!in_array(strtoupper($this->session->data['currency']), $currencies)) {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => 'commweb',
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get('commweb_sort_order')
            );
        }

        return $method_data;
    }

    public function addOrder($order_data) {
        /**
         * 1 to 1 relationship with order table (extends order info)
         */
        $this->db->query("INSERT INTO `" . DB_PREFIX . "commweb_order` SET
			`order_id` = '" . (int) $order_data['order_id'] . "',
			`date_added` = NOW(),
			`date_modified` = NOW(),
			`capture_status` = '" . $this->db->escape($order_data['capture_status']) . "',
			`currency_code` = '" . $this->db->escape($order_data['currency_code']) . "',
			`total` = '" . (float) $order_data['total'] . "',
			`authorization_id` = '" . $this->db->escape($order_data['authorization_id']) . "'");

        return $this->db->getLastId();
    }

    public function addTransaction($transaction_data) {
        /**
         * 1 to many relationship with commweb order table, many transactions per 1 order
         */
        $this->db->query("INSERT INTO `" . DB_PREFIX . "commweb_order_transaction` SET
			`commweb_order_id` = '" . (int) $transaction_data['commweb_order_id'] . "',
			`transaction_id` = '" . $this->db->escape($transaction_data['transaction_id']) . "',
			`parent_id` = '" . $this->db->escape($transaction_data['parent_id']) . "',
			`date_added` = NOW(),
			`note` = '" . $this->db->escape($transaction_data['note']) . "',
			`msgsubid` = '" . $this->db->escape($transaction_data['msgsubid']) . "',
			`receipt_id` = '" . $this->db->escape($transaction_data['receipt_id']) . "',
			`payment_type` = '" . $this->db->escape($transaction_data['payment_type']) . "',
			`payment_status` = '" . $this->db->escape($transaction_data['payment_status']) . "',
			`pending_reason` = '" . $this->db->escape($transaction_data['pending_reason']) . "',
			`transaction_entity` = '" . $this->db->escape($transaction_data['transaction_entity']) . "',
			`amount` = '" . (float) $transaction_data['amount'] . "',
			`debug_data` = '" . $this->db->escape($transaction_data['debug_data']) . "'");
    }

}

?>