<?php

namespace CM;


class Order_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;   
         
    }


    function update_order_meta_from_cxml($order, $senderIdentity, $totalAmount, $currency, $cxmlOrderID) {
        if (!empty($senderIdentity)) {
            $value = (string) $senderIdentity;
            $order->update_meta_data('_sender_identity', $value);
        }
        if (!empty($totalAmount) && !empty($currency)) {
            $total = (string) $totalAmount;
            $orderCurrency = (string) $currency;
            $order->update_meta_data('_total_amount', $total . ' ' . $orderCurrency);
        }
        if (!empty($cxmlOrderID)) {
            $requestOrderID = (string) $cxmlOrderID;
            $order->update_meta_data('_cxml_order_id', $requestOrderID);
        }
    
        // Save changes
        $order->save();
    }
    
}
  