<?php
namespace Product\Model;

class Category
{  
    public $category_id;
    public $category_title;
    public $category_icon;
    public $category_desc;
	public $category_status;
    public function exchangeArray($data)
    {
        $this->category_id     = (isset($data['category_id'])) ? $data['category_id'] : null;
        $this->category_title = (isset($data['category_title'])) ? $data['category_title'] : null;
        $this->category_icon = (isset($data['category_icon'])) ? $data['category_icon'] : null;
        $this->category_desc  = (isset($data['category_desc'])) ? $data['category_desc'] : null;
		$this->category_status  = (isset($data['category_status'])) ? $data['category_status'] : null;		 
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }	
	public function selectFormatAllCategory($data){		
		$selectObject = array();				
		foreach($data as $category){
			$selectObject[$category->category_id] = $category->category_title;			
		}		
		return $selectObject;
	}
 	
}