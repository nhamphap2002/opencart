<?php

/*
 * Created on : May 03, 2017, 9:54:39 AM
 * Author: Tran Trong Thang
 * Email: trantrongthang1207@gmail.com
 * Skype: trantrongthang1207
 *  */

class ModelExtensionPaymentCommweb extends Model {

    public function install() {
        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "commweb_order` (
			  `commweb_order_id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_id` int(11) NOT NULL,
			  `date_added` DATETIME NOT NULL,
			  `date_modified` DATETIME NOT NULL,
			  `capture_status` ENUM('Complete','NotComplete') DEFAULT NULL,
			  `currency_code` CHAR(3) NOT NULL,
			  `authorization_id` VARCHAR(30) NOT NULL,
			  `total` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`commweb_order_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "commweb_order_transaction` (
			  `commweb_order_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
			  `commweb_order_id` int(11) NOT NULL,
			  `transaction_id` CHAR(20) NOT NULL,
			  `parent_id` CHAR(20) NOT NULL,
			  `date_added` DATETIME NOT NULL,
			  `note` VARCHAR(255) NOT NULL,
			  `msgsubid` CHAR(38) NOT NULL,
			  `receipt_id` CHAR(20) NOT NULL,
			  `payment_type` ENUM('none','echeck','instant', 'refund', 'void') DEFAULT NULL,
			  `payment_status` CHAR(20) NOT NULL,
			  `pending_reason` CHAR(50) NOT NULL,
			  `transaction_entity` CHAR(50) NOT NULL,
			  `amount` DECIMAL( 10, 2 ) NOT NULL,
			  `debug_data` TEXT NOT NULL,
			  `call_data` TEXT NOT NULL,
			  PRIMARY KEY (`commweb_order_transaction_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci
		");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "commweb_order_transaction`");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "commweb_order`");
    }

    public function getPayPalOrder($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "commweb_order` WHERE `order_id` = '" . (int) $order_id . "'");

        return $query->row;
    }

    public function editPayPalOrderStatus($order_id, $capture_status) {
        $this->db->query("UPDATE `" . DB_PREFIX . "commweb_order` SET `capture_status` = '" . $this->db->escape($capture_status) . "', `date_modified` = NOW() WHERE `order_id` = '" . (int) $order_id . "'");
    }

    public function addTransaction($transaction_data, $request_data = array()) {
        if ($request_data) {
            $serialized_data = json_encode($request_data);

            $this->db->query("UPDATE " . DB_PREFIX . "commweb_order_transaction SET call_data = '" . $this->db->escape($serialized_data) . "' WHERE commweb_order_transaction_id = " . (int) $commweb_order_transaction_id . " LIMIT 1");
        }



        $this->db->query("INSERT INTO `" . DB_PREFIX . "commweb_order_transaction` SET `commweb_order_id` = '" . (int) $transaction_data['commweb_order_id'] . "', `transaction_id` = '" . $this->db->escape($transaction_data['transaction_id']) . "', `parent_id` = '" . $this->db->escape($transaction_data['parent_id']) . "', `date_added` = NOW(), `note` = '" . $this->db->escape($transaction_data['note']) . "', `msgsubid` = '" . $this->db->escape($transaction_data['msgsubid']) . "', `receipt_id` = '" . $this->db->escape($transaction_data['receipt_id']) . "', `payment_type` = '" . $this->db->escape($transaction_data['payment_type']) . "', `payment_status` = '" . $this->db->escape($transaction_data['payment_status']) . "', `pending_reason` = '" . $this->db->escape($transaction_data['pending_reason']) . "', `transaction_entity` = '" . $this->db->escape($transaction_data['transaction_entity']) . "', `amount` = '" . (float) $transaction_data['amount'] . "', `debug_data` = '" . $this->db->escape($transaction_data['debug_data']) . "'");

        return $this->db->getLastId();
    }

    public function updateTransaction($transaction) {
        $this->db->query("UPDATE " . DB_PREFIX . "commweb_order_transaction SET commweb_order_id = " . (int) $transaction['commweb_order_id'] . ", transaction_id = '" . $this->db->escape($transaction['transaction_id']) . "', parent_id = '" . $this->db->escape($transaction['parent_id']) . "', date_added = '" . $this->db->escape($transaction['date_added']) . "', note = '" . $this->db->escape($transaction['note']) . "', msgsubid = '" . $this->db->escape($transaction['msgsubid']) . "', receipt_id = '" . $this->db->escape($transaction['receipt_id']) . "', payment_type = '" . $this->db->escape($transaction['payment_type']) . "', payment_status = '" . $this->db->escape($transaction['payment_status']) . "', pending_reason = '" . $this->db->escape($transaction['pending_reason']) . "', transaction_entity = '" . $this->db->escape($transaction['transaction_entity']) . "', amount = '" . $this->db->escape($transaction['amount']) . "', debug_data = '" . $this->db->escape($transaction['debug_data']) . "', call_data = '" . $this->db->escape($transaction['call_data']) . "' WHERE commweb_order_transaction_id = '" . (int) $transaction['commweb_order_transaction_id'] . "'");
    }

    public function getPaypalOrderByTransactionId($transaction_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "commweb_order_transaction WHERE transaction_id = '" . $this->db->escape($transaction_id) . "'");

        return $query->rows;
    }

    public function getFailedTransaction($commweb_order_transaction_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "commweb_order_transaction WHERE commweb_order_transaction_id = '" . (int) $commweb_order_transaction_id . "'");

        return $query->row;
    }

    public function getLocalTransaction($transaction_id) {
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "commweb_order_transaction WHERE transaction_id = '" . $this->db->escape($transaction_id) . "'")->row;

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    public function getTransaction($transaction_id) {
        $call_data = array(
            'METHOD' => 'GetTransactionDetails',
            'TRANSACTIONID' => $transaction_id,
        );

        return $this->call($call_data);
    }

    public function getCurrencies() {
        return array(
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
    }

    public function getOrderId($transaction_id) {
        $query = $this->db->query("SELECT `o`.`order_id` FROM `" . DB_PREFIX . "commweb_order_transaction` `ot` LEFT JOIN `" . DB_PREFIX . "commweb_order` `o`  ON `o`.`commweb_order_id` = `ot`.`commweb_order_id`  WHERE `ot`.`transaction_id` = '" . $this->db->escape($transaction_id) . "' LIMIT 1");

        return $query->row['order_id'];
    }

    public function getCapturedTotal($commweb_order_id) {
        $query = $this->db->query("SELECT SUM(`amount`) AS `amount` FROM `" . DB_PREFIX . "commweb_order_transaction` WHERE `commweb_order_id` = '" . (int) $commweb_order_id . "' AND `pending_reason` != 'authorization' AND (`payment_status` = 'Partially-Refunded' OR `payment_status` = 'Completed' OR `payment_status` = 'Pending') AND `transaction_entity` = 'payment'");

        return $query->row['amount'];
    }

    public function getRefundedTotal($commweb_order_id) {
        $query = $this->db->query("SELECT SUM(`amount`) AS `amount` FROM `" . DB_PREFIX . "commweb_order_transaction` WHERE `commweb_order_id` = '" . (int) $commweb_order_id . "' AND `payment_status` = 'Refunded' AND `parent_id` != ''");

        return $query->row['amount'];
    }

    public function getRefundedTotalByParentId($transaction_id) {
        $query = $this->db->query("SELECT SUM(`amount`) AS `amount` FROM `" . DB_PREFIX . "commweb_order_transaction` WHERE `parent_id` = '" . $this->db->escape($transaction_id) . "' AND `payment_type` = 'refund'");

        return $query->row['amount'];
    }

    public function cleanReturn($data) {
        $data = explode('&', $data);

        $arr = array();

        foreach ($data as $k => $v) {
            $tmp = explode('=', $v);
            $arr[$tmp[0]] = urldecode($tmp[1]);
        }

        return $arr;
    }

    public function log($data, $title = null) {
        if ($this->config->get('pp_express_debug')) {
            $this->log->write('PayPal Express debug (' . $title . '): ' . json_encode($data));
        }
    }

    public function getOrder($order_id) {
        $qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "commweb_order` WHERE `order_id` = '" . (int) $order_id . "' LIMIT 1");

        if ($qry->num_rows) {
            $order = $qry->row;
            $order['transactions'] = $this->getTransactions($order['commweb_order_id']);
            $order['captured'] = $this->totalCaptured($order['commweb_order_id']);
            return $order;
        } else {
            return false;
        }
    }

    public function totalCaptured($commweb_order_id) {
        $qry = $this->db->query("SELECT SUM(`amount`) AS `amount` FROM `" . DB_PREFIX . "commweb_order_transaction` WHERE `commweb_order_id` = '" . (int) $commweb_order_id . "' AND `pending_reason` != 'authorization' AND (`payment_status` = 'Partially-Refunded' OR `payment_status` = 'Completed' OR `payment_status` = 'Pending') AND `transaction_entity` = 'payment'");

        return $qry->row['amount'];
    }

    public function getTransactions($commweb_order_id) {
        $query = $this->db->query("SELECT `ot`.*, (SELECT COUNT(`ot2`.`commweb_order_id`) FROM `" . DB_PREFIX . "commweb_order_transaction` `ot2` WHERE `ot2`.`parent_id` = `ot`.`transaction_id`) AS `children` FROM `" . DB_PREFIX . "commweb_order_transaction` `ot` WHERE `commweb_order_id` = '" . (int) $commweb_order_id . "' ORDER BY `date_added` ASC");

        return $query->rows;
    }

    public function addOrderHistory($order_id, $data, $store_id = 0) {
        $json = array();

        $this->load->model('setting/store');

        $store_info = $this->model_setting_store->getStore($store_id);

        if ($store_info) {
            $url = $store_info['ssl'];
        } else {
            $url = HTTPS_CATALOG;
        }

        if (isset($this->session->data['cookie'])) {
            $curl = curl_init();

            // Set SSL if required
            if (substr($url, 0, 5) == 'https') {
                curl_setopt($curl, CURLOPT_PORT, 443);
            }

            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);
            curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url . 'index.php?route=api/order/history&order_id=' . $order_id);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_COOKIE, session_name() . '=' . $this->session->data['cookie'] . ';');

            $json = curl_exec($curl);

            curl_close($curl);
        }

        return $json;
    }

}
