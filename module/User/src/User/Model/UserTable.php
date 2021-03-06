<?php  
namespace User\Model;
use Zend\Db\Sql\Select ;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
class UserTable extends AbstractTableGateway
{
    protected $table = 'y2m_user';
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new User());
        $this->initialize();
    }
    public function getUserFromEmail($email){       
        $rowset = $this->select(array('user_email' => $email));
        return $rowset->current();
    }
	public function saveUser(User $user){
       $data = array(
            'user_given_name' => $user->user_given_name,
            'user_first_name'  => $user->user_first_name,
			'user_middle_name'  => $user->user_middle_name,
			'user_last_name'  => $user->user_last_name,
			'user_profile_name' =>$user->user_profile_name,
			'user_status'  => $user->user_status,
			'user_added_ip_address'  => $user->user_added_ip_address,
			'user_email'  => $user->user_email,
			'user_password'  => $user->user_password,
			'user_gender'  => $user->user_gender,
			'user_timeline_photo_id'  => $user->user_timeline_photo_id,			 
			'user_profile_photo_id'  => $user->user_profile_photo_id,			 
			'user_mobile'  => $user->user_mobile,
			'user_verification_key'  => $user->user_verification_key,
			'user_modified_timestamp'  => date("Y-m-d H:i:s"),
			'user_modified_ip_address'  => $user->user_modified_ip_address,	
			'user_register_type'  => $user->user_register_type,
			'user_fbid'			=> $user->user_fbid,
			'user_accessToken'	=> $user->user_accessToken,
			'user_locale' =>$user->user_locale,
        );
		 $user_id = (int)$user->user_id;
        if ($user_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
						 		 
        } else {
            if ($this->getUser($user_id)) { $this->update($data, array('user_id' => $user_id)); } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
	public function getUser($user_id){
        $user_id  = (int) $user_id;
        $rowset = $this->select(array('user_id' => $user_id));
        return $rowset->current();        
    }
	public function checkProfileNameExist($string){
		$select = new Select;
		$select->from('y2m_user')
			   ->columns(array('user_id'))
			   ->where(array('user_profile_name'=>$string));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();	
		if(!empty($row)&&$row->user_id!=''){
			return true;
		}
		else{
			return false;
		}
	}
	public function getUserByProfilename($profile_name){
        $profile_name  = (string) $profile_name;
        $rowset = $this->select(array('user_profile_name' => $profile_name));
        return $rowset->current();        
    }
	public function  getProfileDetails($user_id){
		$select = new select();
		$select->from('y2m_user')
			   ->columns(array("user_id"=>"user_id","user_given_name"=>"user_given_name","user_first_name"=>"user_first_name","user_middle_name"=>"user_middle_name","user_last_name"=>"user_last_name","user_profile_name"=>"user_profile_name","user_email"=>"user_email","user_gender"=>"user_gender","user_mobile"=>"user_mobile","user_status"=>"user_status","user_register_type"=>"user_register_type","user_fbid"=>"user_fbid","user_timezone_id"=>"user_timezone_id"))
			   ->join("y2m_country","y2m_country.country_id = y2m_user_profile.user_profile_country_id",array("country_title","country_code","country_id"),"left")
			   ->join("y2m_city","y2m_city.city_id = y2m_user_profile.user_profile_city_id",array("city_name"=>"name","city_id"=>"city_id"),"left")
			   			    
			   ->where(array("y2m_user.user_id"=>$user_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();	
		return $row;
	}
	public function updateUser($data,$user_id){ 
		if ($this->getUser($user_id)) {	$this->update($data, array('user_id' => $user_id));return true;} else {	throw new \Exception('Form id does not exist');}		
	}
	public function checkUserVarification($code,$user){ 
		$rowset = $this->select(array('user_verification_key'=>$code));
        $row = $rowset->current();
		if($row){
			if($row->user_status==0&&md5(md5('userId~'.$row->user_id))==$user)return $row->user_id;	else return false;
		}
		else{return false;}
	}
	public function getUserProfilePic($user_id){
		$select = new Select;
		$select->from('y2m_user_profile_photo')
			   ->columns(array('biopic'=>'profile_photo'))
			   ->join('y2m_user','y2m_user.user_profile_photo_id = y2m_user_profile_photo.profile_photo_id',array())
			   ->where(array('y2m_user.user_id = '.$user_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return $resultSet->current();
	}
	 public function checkEmailExists($email,$user_id){       
        $select = new Select;		 
		$select->from('y2m_user')
			   ->columns(array('user_id'))
			   ->where(array('user_email'=>$email))
			   ->where('user_id !='.$user_id);
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		//echo $select->getSqlString();exit;		
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();	
		if(empty($row)){return 1;}else{return 0;}
    }
	public function searchUser($search,$limit,$offset){
		$select = new select();
		$key= explode(" ", $search);
		$where = '';
		for($i = 0; $i < count($key); $i++){
			$where.= ' OR y2m_user.user_given_name LIKE "%'.$key[$i].'%"';
		}
		$select->from('y2m_user')
			   ->columns(array("user_id"=>"user_id","user_given_name"=>"user_given_name","user_first_name"=>"user_first_name","user_middle_name"=>"user_middle_name","user_last_name"=>"user_last_name","user_profile_name"=>"user_profile_name","user_email"=>"user_email","user_gender"=>"user_gender","user_mobile"=>"user_mobile","user_register_type"=>"user_register_type","user_fbid"=>"user_fbid","user_timezone_id"=>"user_timezone_id"))
			   ->join("y2m_user_profile","y2m_user_profile.user_profile_user_id = y2m_user.user_id",array("user_profile_dob","user_profile_about_me","user_profile_profession","user_profile_profession_at","user_profile_city_id","user_profile_country_id","user_address","user_profile_current_location","user_profile_phone","user_profile_emailme_id","user_profile_notifyme_id"),"left")
			   ->join("y2m_country","y2m_country.country_id = y2m_user_profile.user_profile_country_id",array("country_title","country_code","country_id"),"left")
			   ->join("y2m_city","y2m_city.city_id = y2m_user_profile.user_profile_city_id",array("city_name"=>"name","city_id"=>"city_id"),"left")
			   ->join(array("profile_photo"=>"y2m_user_profile_photo"),"profile_photo.profile_photo_id = y2m_user.user_profile_photo_id",array("profile_photo"=>"profile_photo"),"left")			    
			   ->where(array("y2m_user.user_status"=>'live'))
			   ->where(array("y2m_user.user_given_name LIKE '%".$search."%' OR y2m_user.user_email LIKE '%".$search."%' ".$where." OR y2m_user.user_id IN (SELECT user_tag_user_id FROM y2m_user_tag INNER JOIN y2m_tag ON y2m_user_tag.user_tag_tag_id = y2m_tag.tag_id WHERE y2m_tag.tag_title LIKE '%".$search."%' )"));
			   $select->limit($limit);
		$select->offset($offset);	
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->toArray();	
		 
	}
	public function getUserByEmail($email){
		$select = new Select;
		$select->from("y2m_user")
			->columns(array('*'))
			->where(array("user_email"=>$email));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->current(); 
	}
	public function getUserByFbid($fbid){
		$select = new Select;
		$select->from("y2m_user")
			->columns(array('*'))
			->where(array("user_fbid"=>$fbid));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		// echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	  
		return $resultSet->current(); 
	}
	public function getCountOfAllUsers($status,$search){
		$select = new select();
		$select->from('y2m_user')
			   ->columns(array(new Expression('COUNT(distinct(y2m_user.user_id)) as user_count')))		    			    
			   ;

		if($search!=''){
			$select->where('(y2m_user.user_given_name LIKE "%'.$search.'%" OR y2m_user.user_first_name LIKE "%'.$search.'%" OR y2m_user.user_last_name LIKE "%'.$search.'%" OR y2m_user.user_email LIKE "%'.$search.'%")');			
		}
	
		if($status!='all'){
			$select->where(array("y2m_user.user_status"=>$status));
		}
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return  $resultSet->current()->user_count;
	}

	public function getCountOfAllUsersFilter($order='ASC',$gender=null, $agerange=null, $field_country=null, $field_city=null, $dateperiod=null, $datebetween=null){ 
		$results = new ResultSet();

		$userFilter = null;
		$diffuserSql = null;
		$flag_field_exist = null;
		$timedifffrom = null;
		$timediffto = null;

		switch( $dateperiod ) {
			case "week":
				$diffuserSql = " YEARWEEK(`user_added_timestamp`) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ";
			break;
			case "month":
				$diffuserSql = " DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= `user_added_timestamp` ";
			break;
			case "period":
				$datebetween = explode("/", $datebetween);
				$timedifffrom = $datebetween[0];
				$timediffto = $datebetween[1];
				$diffuserSql = " unix_timestamp( `user_added_timestamp` ) BETWEEN unix_timestamp( '".$timedifffrom."' ) AND unix_timestamp( '".$timediffto."' )";
			break;
			default:
				$diffuserSql = '';
			break;
		}

		if ( $field_country && $field_city ) {
			$userFilter = "WHERE b.`user_profile_country_id` = ".$field_country." AND  b.`user_profile_city_id` = ".$field_city;
			$flag_field_exist = true;
		}
		else if ( $field_country && !$field_city ) {
			$userFilter = "WHERE b.`user_profile_country_id` = ".$field_country;
			$flag_field_exist = true;
		}
		else if ( !$field_country && $field_city ) {
			$userFilter = "WHERE b.`user_profile_city_id` = ".$field_city;
			$flag_field_exist = true;
		}
		else {
			if ($diffuserSql) $userFilter = "WHERE" . $diffuserSql;
		}

		if ($flag_field_exist == true) {
			if ($diffuserSql) $userFilter.= " AND" . $diffuserSql;
		}

		if($gender && $userFilter) {
			$userFilter.= " AND a.`user_gender` = '" . $gender."'" ;
		} else if($gender && !$userFilter) {
			$userFilter = " WHERE a.`user_gender` = '" . $gender."'" ;
		}

		$splitrange = null;

		if ($agerange){
		
			$temp_filter =null;
			if( strpos($agerange, '-') ) {
				
				$splitrange = explode("-",$agerange);
				$temp_filter = "TIMESTAMPDIFF(YEAR,b.`user_profile_dob`,CURDATE()) between " . $splitrange[0]." and ".$splitrange[1] ;
			}
			elseif( strpos($agerange, '>') ) {
				
				$splitrange = explode(">",$agerange);
				$temp_filter = "TIMESTAMPDIFF(YEAR,b.`user_profile_dob`,CURDATE()) > " . $splitrange[0];
			}
			
			if($agerange && $userFilter) {
				$userFilter.= " AND " ;
			} else if($agerange && !$userFilter) {
				$userFilter = " WHERE ";
			}
			$userFilter.= $temp_filter;
		}

		$sql = "select 
				  user_id
				from 
				  `y2m_user` a join `y2m_user_profile` b on a.`user_id` = b.`user_profile_user_id`
				".$userFilter."
				order by user_id ".$order;
		
		$statement = $this->adapter-> query($sql); 
		$results = $statement -> execute();	
		return $results->count();
	}
	public function getAllUsersFilter($limit, $offset, $order='ASC',$gender=null, $agerange=null, $field_country=null, $field_city=null, $dateperiod=null, $datebetween=null){
		$results = new ResultSet();

		$userFilter = null;
		$diffuserSql = null;
		$flag_field_exist = null;
		$timedifffrom = null;
		$timediffto = null;

		switch( $dateperiod ) {
			case "week":
				$diffuserSql = " YEARWEEK(`user_added_timestamp`) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ";
			break;
			case "month":
				$diffuserSql = " DATE_SUB(CURDATE(),INTERVAL 1 MONTH) <= `user_added_timestamp` ";
			break;
			case "period":
				$datebetween = explode("/", $datebetween);
				$timedifffrom = @$datebetween[0];
				$timediffto = @$datebetween[1];
				$diffuserSql = " unix_timestamp( `user_added_timestamp` ) BETWEEN unix_timestamp( '".$timedifffrom."' ) AND unix_timestamp( '".$timediffto."' )";
			break;
			default:
				$diffuserSql = '';
			break;
		}

		if ( $field_country && $field_city ) {
			$userFilter = "WHERE b.`user_profile_country_id` = ".$field_country." AND  b.`user_profile_city_id` = ".$field_city;
			$flag_field_exist = true;
		}
		else if ( $field_country && !$field_city ) {
			$userFilter = "WHERE b.`user_profile_country_id` = ".$field_country;
			$flag_field_exist = true;
		}
		else if ( !$field_country && $field_city ) {
			$userFilter = "WHERE b.`user_profile_city_id` = ".$field_city;
			$flag_field_exist = true;
		}
		else {
			if ($diffuserSql) $userFilter = "WHERE" . $diffuserSql;
		}


		if ($flag_field_exist == true) {
			if ($diffuserSql) $userFilter.= " AND" . $diffuserSql;
		}

		if($gender && $userFilter) {
			$userFilter.= " AND a.`user_gender` = '" . $gender."'" ;
		} else if($gender && !$userFilter) {
			$userFilter = " WHERE a.`user_gender` = '" . $gender."'" ;
		}

		$splitrange = null;

		if ($agerange){

			$temp_filter =null;
			if( strpos($agerange, '-') ) {

				$splitrange = explode("-",$agerange);
				$temp_filter = "TIMESTAMPDIFF(YEAR,b.`user_profile_dob`,CURDATE()) between " . $splitrange[0]." and ".$splitrange[1] ;
			}
			elseif( strpos($agerange, '>') ) {

				$splitrange = explode(">",$agerange);
				$temp_filter = "TIMESTAMPDIFF(YEAR,b.`user_profile_dob`,CURDATE()) > " . $splitrange[0];
			}

			if($agerange && $userFilter) {
				$userFilter.= " AND " ;
			} else if($agerange && !$userFilter) {
				$userFilter = " WHERE ";
			}
			$userFilter.= $temp_filter;
		}

		$sql = "select
				  a.user_given_name, a.user_gender, a.user_first_name,a.user_profile_name,a.user_email,
				  TIMESTAMPDIFF(YEAR,b.`user_profile_dob`,CURDATE()) AS age
				from
				  `y2m_user` a join `y2m_user_profile` b on a.`user_id` = b.`user_profile_user_id`
				".$userFilter."
				order by user_id ".$order." limit ".$limit." offset ".$offset;


       // echo $sql;
		$statement = $this->adapter-> query($sql);
		$results = $statement -> execute();
		return $results;
	}

	public function getAllUsers($offset, $limit, $search){ 
		$results = new ResultSet();        
		$select = new Select;
		$select->from("y2m_user")
				->columns(array('user_id','user_given_name','user_first_name','user_middle_name','user_last_name','user_profile_name',
				   'user_status'))
				->where(array('user_status'=> 'live') );	  
		if($search != ''){
			$select->where->like('user_given_name', '%'.$search.'%' );	  
		}
		$select->limit($limit);
		$select->offset($offset);	   
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString();die();
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$resultSet = $resultSet->toArray();           	        
		return $resultSet;
	}
	public function  getUserProfileInfo($user_id){
		$select = new select();
		$select->from('y2m_user')
			   ->columns(array("user_id"=>"user_id","user_given_name"=>"user_given_name","user_first_name"=>"user_first_name","user_middle_name"=>"user_middle_name","user_last_name"=>"user_last_name","user_profile_name"=>"user_profile_name","user_email"=>"user_email","user_gender"=>"user_gender","user_mobile"=>"user_mobile","user_status"=>"user_status",
			   ->join("y2m_user_profile","y2m_user_profile.user_profile_user_id = y2m_user.user_id",array("user_profile_dob","user_profile_about_me","user_profile_profession","user_profile_profession_at","user_profile_city_id","user_profile_country_id","user_address","user_profile_current_location","user_profile_phone","user_profile_emailme_id","user_profile_notifyme_id"),"left")
			   ->join("y2m_country","y2m_country.country_id = y2m_user_profile.user_profile_country_id",array("country_title","country_code","country_id"),"left")
			   ->join("y2m_city","y2m_city.city_id = y2m_user_profile.user_profile_city_id",array("city_name"=>"name","city_id"=>"city_id"),"left")
			   ->join(array("profile_photo"=>"y2m_user_profile_photo"),"profile_photo.profile_photo_id = y2m_user.user_profile_photo_id",array("profile_photo"=>"profile_photo"),"left")
               
			   ->where(array("y2m_user.user_id"=>$user_id));
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);
		//echo $select->getSqlString($this->adapter->getPlatform());exit;
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		$row =  $resultSet->current();
		return $row;
	}
}
