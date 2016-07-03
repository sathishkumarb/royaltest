<?php
namespace Product\Model;   
use Zend\Crypt\BlockCipher;	#for encryption
class ProductAttributes
{  
    public $group_setting_id;
    public $group_setting_group_id;
    public $group_activity_settings;
	
    public function exchangeArray($data)
    {
        $this->group_setting_id     = (isset($data['group_setting_id'])) ? $data['group_setting_id'] : null;
        $this->group_setting_group_id = (isset($data['group_setting_group_id'])) ? $data['group_setting_group_id'] : null;
        $this->group_activity_settings  = (isset($data['group_activity_settings'])) ? $data['group_activity_settings'] : null;
		$this->group_member_join_type  = (isset($data['group_member_join_type'])) ? $data['group_member_join_type'] : null;		
		$this->group_privacy_settings     = (isset($data['group_privacy_settings'])) ? $data['group_privacy_settings'] : null;
    }
	
	// Add the following method: This will be Needed for Edit. Please do not change it.
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
		
}