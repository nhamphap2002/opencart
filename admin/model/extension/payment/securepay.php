<?php
class ModelExtensionPaymentSecurepay extends Model {
	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "securepay_order` (
			  `securepay_order_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `order_id` INT(11) NOT NULL,
			  `transaction_id` CHAR(50) NOT NULL,
			  `rebate_status` INT(1) DEFAULT NULL,
			  `total` DECIMAL( 10, 2 ) NOT NULL,
			  PRIMARY KEY (`securepay_order_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
                $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "securepay_card` (
			  `card_id` INT(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` INT(11) NOT NULL,
			  `securepay_payor` VARCHAR(50) NOT NULL,
			  `securepay_pan` VARCHAR(50) NOT NULL,
			  PRIMARY KEY (`card_id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
	}

	public function rebate($order_id) {
		require_once('securepay/securepay_xml_api.php');
		$securepay_order = $this->getOrder($order_id);

		if (!empty($securepay_order) && $securepay_order['rebate_status'] != 1) {
			$merchant_id = $this->config->get('securepay_merchant_id');
			$enviroment = $this->config->get('securepay_enviroment');
			$transaction_password	= $this->config->get('securepay_transaction_password');
			
			$payment_mode = ($enviroment == 'live' ? SECUREPAY_GATEWAY_MODE_LIVE : SECUREPAY_GATEWAY_MODE_TEST);

			$txn_object = new securepay_xml_transaction($payment_mode, $merchant_id, $transaction_password, '');
			$banktxnID = $txn_object->processCreditRefund($securepay_order['total'], $order_id, $securepay_order['transaction_id']);
			
			if (  !$banktxnID  )
			{
				return false;
			} else {
				return array('banktxnID' => $banktxnID);
			}
		} else {
			return false;
		}
	}

	public function updateRebateStatus($securepay_order_id, $status) {
		$this->db->query("UPDATE `" . DB_PREFIX . "securepay_order` SET `rebate_status` = '" . (int)$status . "' WHERE `securepay_order_id` = '" . (int)$securepay_order_id . "'");
	}

	public function getOrder($order_id) {
		$qry = $this->db->query("SELECT * FROM `" . DB_PREFIX . "securepay_order` WHERE `order_id` = '" . (int)$order_id . "' LIMIT 1");

		if ($qry->num_rows) {
			$order = $qry->row;
			return $order;
		} else {
			return false;
		}
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