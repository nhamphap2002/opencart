<?php 
class ModelExtensionPaymentSecurepay extends Model {
  	public function getMethod($address) {
		$this->load->language('extension/payment/securepay');
		
		if ($this->config->get('securepay_status')) {
      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('securepay_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
			if (!$this->config->get('securepay_geo_zone_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
      		  	$status = TRUE;
      		} else {
     	  		$status = FALSE;
			}	
      	} else {
			$status = FALSE;
		}
		
		$method_data = array();
		if ($status) {  
      		$method_data = array( 
        		'code'         => 'securepay',
        		'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('securepay_sort_order')
      		);
    	}
   
    	return $method_data;
  	}
	
	public function addOrder($order_info, $transaction_id) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "securepay_order` SET `order_id` = '" . (int)$order_info['order_id'] . "', `transaction_id` = '" . $this->db->escape($transaction_id) . "', `total` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . "'");

		return $this->db->getLastId();
	}
        
        /**
         * add card info to db
         * @author Ho Ngoc Hang<kemly.vn@gmail.com>
        */
        public function addCard($card_data, $customer_id) {
		$this->db->query("INSERT into " . DB_PREFIX . "securepay_card SET customer_id = '" . (int)$customer_id . "', securepay_payor = '" . $this->db->escape($card_data['payor']) . "', securepay_pan = '" . $this->db->escape($card_data['pan']) . "'");

		return $this->db->getLastId();
	}

        /**
         * edit card info of customer
         * @author Ho Ngoc Hang<kemly.vn@gmail.com>
        */
	public function updateCard($card_data, $customer_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "securepay_card SET securepay_payor = '" . $this->db->escape($card_data['payor']) . "', securepay_pan = '" . $this->db->escape($card_data['pan']) . "' WHERE customer_id = '" . (int)$customer_id . "'");
	}
        
        /**
         * get info card of customer
         * @author Ho Ngoc Hang<kemly.vn@gmail.com>
         * @param type $customer_id
         * @return boolean
         */
        public function getCard($customer_id) {
		$qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "securepay_card WHERE customer_id = '" . (int)$customer_id . "'");

		if ($qry->num_rows) {
			return $qry->row;
		} else {
			return false;
		}
	}
        
        public function payByXml($order_id, $securepay_payor) {
		require_once('securepay/securepay_xml_api.php');
		$this->load->model('checkout/order');
                $securepay_order = $this->model_checkout_order->getOrder($order_id);

		//if (!empty($securepay_order)) {
			$merchant_id = $this->config->get('securepay_merchant_id');
			$enviroment = $this->config->get('securepay_enviroment');
			$transaction_password	= $this->config->get('securepay_transaction_password');
			
			$payment_mode = ($enviroment == 'live' ? SECUREPAY_GATEWAY_MODE_PERIODIC_LIVE : SECUREPAY_GATEWAY_MODE_PERIODIC_TEST);

			$txn_object = new securepay_xml_transaction($payment_mode, $merchant_id, $transaction_password, '');
                        
			$banktxnID = $txn_object->processTrigger((float)$securepay_order['total'], $securepay_payor);
			
			if (  !$banktxnID  )
			{
				return false;
			} else {
				return array('banktxnID' => $banktxnID);
			}
//		} else {
//			return false;
//		}
	}
}
?>