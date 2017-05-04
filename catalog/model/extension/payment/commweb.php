<?php

class ModelExtensionPaymentCommweb extends Model {

    public function getMethod($address) {
        $this->load->language('extension/payment/commweb');

        $status = TRUE;

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

    public function addOrder($order_info, $transaction_id) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "commweb_order` SET `order_id` = '" . (int) $order_info['order_id'] . "', `transaction_id` = '" . $this->db->escape($transaction_id) . "', `total` = '" . $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false) . "'");

        return $this->db->getLastId();
    }

    /**
     * add card info to db
     * @author Ho Ngoc Hang<kemly.vn@gmail.com>
     */
    public function addCard($card_data, $customer_id) {
        $this->db->query("INSERT into " . DB_PREFIX . "commweb_card SET customer_id = '" . (int) $customer_id . "', commweb_payor = '" . $this->db->escape($card_data['payor']) . "', commweb_pan = '" . $this->db->escape($card_data['pan']) . "'");

        return $this->db->getLastId();
    }

    /**
     * edit card info of customer
     * @author Ho Ngoc Hang<kemly.vn@gmail.com>
     */
    public function updateCard($card_data, $customer_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "commweb_card SET commweb_payor = '" . $this->db->escape($card_data['payor']) . "', commweb_pan = '" . $this->db->escape($card_data['pan']) . "' WHERE customer_id = '" . (int) $customer_id . "'");
    }

    /**
     * get info card of customer
     * @author Ho Ngoc Hang<kemly.vn@gmail.com>
     * @param type $customer_id
     * @return boolean
     */
    public function getCard($customer_id) {
        $qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "commweb_card WHERE customer_id = '" . (int) $customer_id . "'");

        if ($qry->num_rows) {
            return $qry->row;
        } else {
            return false;
        }
    }

    public function payByXml($order_id, $commweb_payor) {
        require_once('commweb/commweb_xml_api.php');
        $this->load->model('checkout/order');
        $commweb_order = $this->model_checkout_order->getOrder($order_id);

        //if (!empty($commweb_order)) {
        $merchant_id = $this->config->get('commweb_merchant_id');
        $enviroment = $this->config->get('commweb_enviroment');
        $transaction_password = $this->config->get('commweb_transaction_password');

        $payment_mode = ($enviroment == 'live' ? SECUREPAY_GATEWAY_MODE_PERIODIC_LIVE : SECUREPAY_GATEWAY_MODE_PERIODIC_TEST);

        $txn_object = new commweb_xml_transaction($payment_mode, $merchant_id, $transaction_password, '');

        $banktxnID = $txn_object->processTrigger((float) $commweb_order['total'], $commweb_payor);

        if (!$banktxnID) {
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