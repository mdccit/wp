<?php

namespace CM;


class Order_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;   
         
    }


    function update_order_meta_from_cxml($order, $senderIdentity, $totalAmount, $currency, $cxmlOrderID) {
        if (!empty($senderIdentity)) {
            $order->update_meta_data('_sender_identity', $senderIdentity);
        }
        if (!empty($totalAmount) && !empty($currency)) {
            $order->update_meta_data('_total_amount', $totalAmount . ' ' . $currency);
        }
        if (!empty($cxmlOrderID)) {
            $order->update_meta_data('_cxml_order_id', $cxmlOrderID);
        }
    
        // Save changes
        $order->save();
    }
    
}
  