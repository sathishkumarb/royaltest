<?php
namespace Product\Model;
class Product
{  	 
	public $product_id;
    public $product_name;
    public $product_color;
	public $product_brand;
	public $product_status;
	public $product_description;
	public $product_added_timestamp;

    public function exchangeArray($data)
    {
        $this->product_id     = (isset($data['product_id'])) ? $data['product_id'] : null;
        $this->product_name = (isset($data['product_name'])) ? $data['product_name'] : null;
        $this->product_color  = (isset($data['product_color'])) ? $data['product_color'] : null;
		$this->product_brand  = (isset($data['product_brand'])) ? $data['product_brand'] : null;
		$this->product_status  = (isset($data['product_status'])) ? $data['product_status'] : null;
		$this->product_description  = (isset($data['product_description'])) ? $data['product_description'] : null;
		$this->product_added_timestamp  = (isset($data['product_added_timestamp'])) ? $data['product_added_timestamp'] : null;
    }	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy() {
        return get_object_vars($this);
    } 
	public function selectFormatAllGroup($data) {
		$selectObject = array();
		foreach($data as $product){			 
			$selectObject[$product->product_id] = $product->product_name;			
		}	
		return $selectObject;	//return blank array
	} 
}