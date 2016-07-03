<?php
namespace Order\Model;

class Order
{  
    public $order_id;
    public $order_ref_id;
    public $order_date;
	public $order_user_id;
	public $order_status;
    public function exchangeArray($data)
    {
        $this->order_id     = (isset($data['order_id'])) ? $data['order_id'] : null;
        $this->order_ref_id = (isset($data['order_ref_id'])) ? $data['order_ref_id'] : null;
        $this->order_date = (isset($data['order_date'])) ? $data['order_date'] : null;
        $this->order_user_id  = (isset($data['order_user_id'])) ? $data['order_user_id'] : null;
		$this->order_status  = (isset($data['order_status'])) ? $data['order_status'] : null;		 
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
	public function selectFormatAllOrder($data){		
		$selectObject = array();				
		foreach($data as $order){
			$selectObject[$order->order_ref_id] = $order->order_ref_id;			
		}		
		return $selectObject;
	}
 	
}