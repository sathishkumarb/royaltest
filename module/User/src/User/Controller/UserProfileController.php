<?php 
namespace User\Controller;
use Zend\View\Helper\HeadScript;
use \Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel; 
use Zend\View\Renderer\PhpRenderer;
use Zend\Session\Container; 
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter; 
use Zend\Crypt\Password\Bcrypt;	
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart; 
use Tag\Model\UserTag;
use User\Model\User;
use Notification\Model\UserNotification; 
class UserProfileController extends AbstractActionController
{    
	protected $userTable;
	protected $userTagTable;
	protected $userProfileTable;
	protected $tagTable;
	protected $userFriendTable;
	protected $userFriendRequestTable;
	protected $userNotificationTable;
	protected $userGroupTable;
	protected $groupTable;
	protected $activityTable;
	protected $discussionTable;
	protected $groupMediaTable;
	protected $likeTable;
	protected $commentTable;
	protected $tagCategoryTable;
	protected $timezoneTable;
	protected $emailmeTable;
	protected $notifymeTable;
	protected $userProfilePhotoTable;
	protected $activityRsvpTable;
	protected $groupTagTable;
	protected $groupMediaContentTable;
	protected $localesTable;
	protected function getViewHelper($helperName){
    	return $this->getServiceLocator()->get('viewhelpermanager')->get($helperName);
	}	 
    public function memberprofileAction(){
		 			 
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$session = New Container('language');
			$serviceManager = $this->getServiceLocator();
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$viewModel->setVariable( 'current_Profile', $profilename);			
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;
			$this->layout()->identity = $identity;			 		 			 
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$config = $this->getServiceLocator()->get('Config');			 
			$viewModel->setVariable('image_folders',$config['image_folders']);
			$serviceManager = $this->getServiceLocator();
			if(!empty($userinfo)&&$userinfo->user_id){
				$profileWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profile',
											'member_profile'     => $profilename,							 
										));
				if($userinfo->user_id == $identity->user_id){
					$viewModel->setVariable( 'myprofile', 1);
				}else{$viewModel->setVariable( 'myprofile', 0);}
				$viewModel->addChild($profileWidget, 'profileWidget');
				$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
				$viewModel->setVariable( 'friends_count' , $friends_count);	
				$profile_type = ($userinfo->user_id!=$identity->user_id)?'other':'mine';
				$intTotalGroups      = $this->getUserGroupTable()->fetchAllUserGroupCount( $userinfo->user_id,$identity->user_id,'',$profile_type);
				$viewModel->setVariable( 'group_count' , $intTotalGroups['group_count']);
				$profile_data = $this->getUserTable()->getProfileDetails($userinfo->user_id);
				$user_tags = $this->getUserTagTable()->getAllUserTagsWithCategiry($userinfo->user_id);
				$viewModel->setVariable( 'profile_data' , $profile_data);
				$user_profileData = $this->getUserTable()->getProfileDetails($identity->user_id);				 
				$viewModel->setVariable( 'userinfo', $user_profileData);				
				$viewModel->setVariable( 'error', $error);
				$viewModel->setVariable( 'user_tags' , $user_tags); 
				$viewModel->setVariable( 'flashmessage' , $this->flashMessenger()->getMessages()); 
				return $viewModel; 
			}else{
				$error =  $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));		
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
	}
	public 	function profileAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$viewModel->setVariable( 'myprofile',0);
		$config = $this->getServiceLocator()->get('Config');
		$friendship_status ='add friend';
		$viewModel->setVariable('image_folders',$config['image_folders']);
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$session = New Container('language');
			$serviceManager = $this->getServiceLocator();
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			$viewModel->setVariable( 'profilename', $profilename);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id == $myinfo->user_id){
					$viewModel->setVariable( 'myprofile', 1);
				}else{	
					if($this->getUserFriendTable()->isFriend($myinfo->user_id,$userinfo->user_id)){	$friendship_status = 'friends';}
					else if($this->getUserFriendTable()->isRequested($myinfo->user_id,$userinfo->user_id)){$friendship_status = 'requested';}
					else if($this->getUserFriendTable()->isPending($myinfo->user_id,$userinfo->user_id)){$friendship_status = 'pending';}
					else{$friendship_status = 'add friend'; }					
				}
				$viewModel->setVariable( 'friendship_status', $friendship_status);
				$profile_data = $this->getUserTable()->getProfileDetails($userinfo->user_id);
				$user_tags = $this->getUserTagTable()->getAllUserTagsWithCategiry($userinfo->user_id); 
				$profile_data->country_title = $serviceManager->get('translator')->translate($profile_data->country_title);
				$profile_data->city_name = $serviceManager->get('translator')->translate($profile_data->city_name);
				$profile_data->country_code_locale = $serviceManager->get('translator')->translate($profile_data->country_code);
				$viewModel->setVariable( 'profile_data' , $profile_data);	
				$viewModel->setVariable( 'error', $error);
				$viewModel->setVariable( 'user_tags' , $user_tags); 
				$arrmyIntrests = $this->getUserTagTable()->getAllUserTags($identity->user_id);				 
				$myIntrests = array();
				if(!empty($arrmyIntrests)){
					foreach($arrmyIntrests as $list){
						$myIntrests[] =array(
							'category_id'=>$list['category_id'],
							'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
							'tag_id'=>$list['tag_id'],
							'tag_category_title'=>$list['tag_category_title'],
							'tag_category_icon'=>$list['tag_category_icon'],
						);
					}
				}				
				$viewModel->setVariable( 'myIntrests' , $myIntrests);	
				$arr_userIntrests = $this->getUserTagTable()->getAllUserTags($userinfo->user_id);
				$userIntrests = array();
				if(!empty($arr_userIntrests)){
					foreach($arr_userIntrests as $list){
						$userIntrests[] =array(
							'category_id'=>$list['category_id'],
							'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
							'tag_id'=>$list['tag_id'],
							'tag_category_title'=>$list['tag_category_title'],
							'tag_category_icon'=>$list['tag_category_icon'],
						);
					}
				}	
				$viewModel->setVariable( 'userIntrests' , $userIntrests);					
				return $viewModel; 
			}else{
				$error =  $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));		
				return $result;
			}
		}else{
			$error =  $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");			 
		}
	}
	public function updateProfileAction(){ 
		$error = '';
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id == $myinfo->user_id){
					$request   = $this->getRequest();
					if ($request->isPost()){
						$post = $request->getPost();
						$data_profile = array();
						$data = array();
						if($post['user_name']!=''&&$post['country']!=""&&$post['city']!=''){
							$data['user_given_name'] = $post['user_name'];
							$data['user_gender'] = $post['gender'];
							$data_profile['user_profile_user_id'] = $myinfo->user_id;
							$data_profile['user_profile_city_id'] = $post['city'];
							$data_profile['user_profile_country_id'] = $post['country'];
							$data_profile['user_profile_about_me'] = $post['about'];
							$data_profile['user_profile_dob'] = date("Y-m-d",strtotime($post['dateofbirth']));
							if($this->getUserTable()->updateUser($data,$myinfo->user_id)){
								$this->getUserProfileTable()->updateUserProfile($data_profile,$myinfo->user_id);
								$userinfo = $this->getUserTable()->getUser($myinfo->user_id);
								$storage = $auth->getStorage();
								$storage->write($userinfo);
							}else{$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again");}
						}else{$error = $serviceManager->get('translator')->translate("Name, country and city fields are must to enter");}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;		
	}
	public function updateTagsAction(){ 
		$error = '';
		$user_tags = array();
		$userIntrests = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id == $myinfo->user_id){
					$request   = $this->getRequest();
					if ($request->isPost()){
						$post = $request->getPost(); 
						$objUser = new User();
						if(!empty($post['tags'])){
							foreach($post['tags'] as $tags){
								$data_usertags = array();
								$tag_hystory = $this->getTagTable()->getTag($tags);
								$tag_exist =  $this->getUserTagTable()->checkUserTag($identity->user_id,$tags); 
								if(!empty($tag_hystory)&&$tag_hystory->tag_id!=''&&empty($tag_exist)){
									$data_usertags['user_tag_user_id'] = $identity->user_id;
									$data_usertags['user_tag_tag_id'] = $tags;
									$data_usertags['user_tag_added_ip_address'] = $objUser->getUserIp();
									$objUsertag = new UserTag();
									$objUsertag->exchangeArray($data_usertags);
									$this->getUserTagTable()->saveUserTag($objUsertag);
								}							
							}
							$this->getUserTagTable()->deleteAllUserTags($identity->user_id,$post['tags']);
							$user_tags = $this->getUserTagTable()->getAllUserTagsWithCategiry($identity->user_id);
							$arr_userIntrests = $this->getUserTagTable()->getAllUserTags($userinfo->user_id);
							$userIntrests = array();
							if(!empty($arr_userIntrests)){
								foreach($arr_userIntrests as $list){
									$userIntrests[] =array(
										'category_id'=>$list['category_id'],
										'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
										'tag_id'=>$list['tag_id'],
										'tag_category_title'=>$list['tag_category_title'],
										'tag_category_icon'=>$list['tag_category_icon'],
									);
								}
							}
						}else{	
							$this->getUserTagTable()->deleteAllUserTags($identity->user_id);
						}						
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		
		$return_array['user_tags'] = $user_tags;
		$return_array['userIntrests'] = $userIntrests;		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;		
	}
	public function myintrestsAction(){
		$error = '';
		$tag_category = array();
		$tags = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			 
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($myinfo)&&$myinfo->user_id){				 
				$request   = $this->getRequest();
				if ($request->isPost()){					 
					$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($identity->user_id);
					$arr_tags = $this->getUserTagTable()->getAllUserTags($identity->user_id);
					$tags = array();
					if(!empty($arr_tags)){
						foreach($arr_tags as $list){
							$tags[] =array(
								'category_id'=>$list['category_id'],
								'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
								'tag_id'=>$list['tag_id'],
								'tag_category_title'=>$list['tag_category_title'],
								'tag_category_icon'=>$list['tag_category_icon'],
							);
						}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}				 
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		
		$return_array['tags'] = $tags;	
		$return_array['tag_category'] = $tag_category;			
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function sentFriendRequestAction(){
		$error = '';		 
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id != $myinfo->user_id){
					$request   = $this->getRequest();
					if ($request->isPost()){
						if(!$this->getUserFriendTable()->isFriend($myinfo->user_id,$userinfo->user_id)&&!$this->getUserFriendTable()->isRequested($myinfo->user_id,$userinfo->user_id)&&!$this->getUserFriendTable()->isPending($myinfo->user_id,$userinfo->user_id)){
							$request_data['user_friend_request_sender_user_id'] = $identity->user_id;
							$request_data['user_friend_request_friend_user_id'] = $userinfo->user_id;
							$request_data['user_friend_request_status'] = "requested";
							if($this->getUserFriendRequestTable()->sendFriendRequest($request_data)){
								$error = '';
								$config = $this->getServiceLocator()->get('Config');
								$base_url = $config['pathInfo']['base_url'];								 
								$msg = '<a href="'.$base_url.$myinfo->user_profile_name.'">'.$identity->user_given_name." Sent you a friend request</a>";
								$subject = 'Friend request';
								$from = 'admin@jeera.me';
								$process = 'requested';
								$this->UpdateNotifications($userinfo->user_id,$msg,1,$subject,$from,$identity->user_id,$identity->user_id,$process);							 
							}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}
						}else{$error = $serviceManager->get('translator')->translate("Already requested");}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;			 		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function acceptFriendRequestAction(){
		$error = '';		 
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id != $myinfo->user_id){
					$request   = $this->getRequest();
					if ($request->isPost()){
						if($this->getUserFriendTable()->isPending($myinfo->user_id,$userinfo->user_id)){
							if($this->getUserFriendTable()->AcceptFriendRequest($myinfo->user_id,$userinfo->user_id)){								 
								$this->getUserFriendRequestTable()->makeRequestTOProcessed($myinfo->user_id,$userinfo->user_id);
								$config = $this->getServiceLocator()->get('Config');
								$base_url = $config['pathInfo']['base_url'];								 
								$msg = '<a href="'.$base_url.$identity->user_profile_name.'">'.$identity->user_given_name." accept your friend request</a>";
								$subject = 'Friend request';
								$from = 'admin@jeera.me';
								$process = 'accepted';
								$this->UpdateNotifications($userinfo->user_id,$msg,2,$subject,$from,$identity->user_id,$identity->user_id,$process);
							}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}						 
						}else{$error = $serviceManager->get('translator')->translate("Already processed this request");}
					}else{$error =  $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error =  $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;			 		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function rejectFriendRequestAction(){
		$error = '';		 
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id != $myinfo->user_id){
					$request   = $this->getRequest();
					if ($request->isPost()){
						if($this->getUserFriendTable()->isPending($myinfo->user_id,$userinfo->user_id)){
							if($this->getUserFriendRequestTable()->DeclineFriendRequest($myinfo->user_id,$userinfo->user_id)){								 
								 $error ='';
							}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}						 
						}else{$error = $serviceManager->get('translator')->translate("Already processed this request");}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;			 		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function unFriendRequestAction(){
		$error = '';		 
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				if($userinfo->user_id != $myinfo->user_id){
					$request   = $this->getRequest();
					if ($request->isPost()){
						if($this->getUserFriendTable()->isFriend($myinfo->user_id,$userinfo->user_id)){
							if($this->getUserFriendTable()->RemoveFrined($myinfo->user_id,$userinfo->user_id)){								 
								$this->getUserFriendRequestTable()->DeclineFriendRequest($myinfo->user_id,$userinfo->user_id);
							}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}						 
						}else{$error = $serviceManager->get('translator')->translate("Already processed this request");}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;			 		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function friendsAction(){ 
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$requests = $this->params()->fromQuery('requests');
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$session = New Container('language');
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$viewModel->setVariable( 'current_Profile', $profilename);
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			if(!empty($userinfo)&&$userinfo->user_id){
				$viewModel->setVariable( 'profilename', $identity->user_profile_name);				
				if($userinfo->user_id == $identity->user_id){
					$viewModel->setVariable( 'myprofile', 1);
				}else{$viewModel->setVariable( 'myprofile', 0);}
				$profileWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profile',
											'member_profile'     => $profilename,							 
										));
										
				 
				$viewModel->addChild($profileWidget, 'profileWidget');
				$profile_type = ($userinfo->user_id!=$identity->user_id)?'other':'mine';
				$intTotalGroups      = $this->getUserGroupTable()->fetchAllUserGroupCount( $userinfo->user_id,$identity->user_id,'',$profile_type);
				$viewModel->setVariable( 'group_count' , $intTotalGroups['group_count']);
				$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
				$viewModel->setVariable( 'friends_count' , $friends_count);	
				$request_count  = $this->getUserFriendRequestTable()->getAllReuqestsCount($userinfo->user_id);
				$viewModel->setVariable( 'request_count' , $request_count);	
				$sent_count  = $this->getUserFriendRequestTable()->getAllSentCount($userinfo->user_id);
				$viewModel->setVariable( 'sent_count' , $sent_count);	
				$friendslist = $this->getUserFriendTable()->getAllFriends($userinfo->user_id,$identity->user_id,0,10);
				$arrFriends = array();
				$viewModel->setVariable( 'showRequests' , 0);
				if($userinfo->user_id == $identity->user_id&&$requests==1){	
					$viewModel->setVariable( 'showRequests' , 1);
				}				
				$mutual_friends_count =  0;
				if($userinfo->user_id != $identity->user_id){
					$mutual_friends_count = $this->getUserFriendTable()->getAllMutualFriendsCount($userinfo->user_id,$identity->user_id)->friends_count;
				}		
				$viewModel->setVariable( 'mutual_friends_count' , $mutual_friends_count);				
				foreach($friendslist as $list){ 
					$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($list->user_id);
					$objcreated_group_count = $this->getUserGroupTable()->getCreatedGroupCount($list->user_id);
					if(!empty($objcreated_group_count)){
					$created_group_count = $objcreated_group_count->created_group_count;
					}else{$created_group_count =0;}
					$arrFriends[] = array(
								'user_given_name' =>$list->user_given_name,
								'user_id' =>$list->user_id,
								'user_profile_name' =>$list->user_profile_name,
								'user_fbid' => $list->user_fbid,
								'country_title' =>$serviceManager->get('translator')->translate($list->country_title),
								'country_code' =>$list->country_code,
								'city' =>$serviceManager->get('translator')->translate($list->name),
								'profile_photo' =>$list->profile_photo,								 
								'group_count' =>$list->group_count,
								'created_group_count' =>$created_group_count,
								'is_friend' =>$list->is_friend,
								'is_requested' =>$list->is_requested,
								'get_request' =>$list->get_request,
								'tag_category_count' =>count($tag_category),
								'tag_category' =>$tag_category,
								);
				}
				 
				$config = $this->getServiceLocator()->get('Config');				 
				$viewModel->setVariable('image_folders',$config['image_folders']);
				$viewModel->setVariable( 'friendslist' , $arrFriends);	
				$viewModel->setVariable( 'error', $error);
				 	
				return $viewModel; 
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));		
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
	}
	public function moreFriendsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arrFriends = array(); 
		$friends_count = 0;
		$request_count  = 0;
		$mutual_friends_count = 0;
		$sent_count  = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					$page = $post['page_counter'];
					$offset = ($page)?($page)*10:0;	
					$friendslist = $this->getUserFriendTable()->getAllFriends($userinfo->user_id,$identity->user_id,$offset,10);					
					foreach($friendslist as $list){ 
						$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($list->user_id);
						$objcreated_group_count = $this->getUserGroupTable()->getCreatedGroupCount($list->user_id);
						if(!empty($objcreated_group_count)){
						$created_group_count = $objcreated_group_count->created_group_count;
						}else{$created_group_count =0;}
						$arrFriends[] = array(
							'user_given_name' =>$list->user_given_name,
							'user_id' =>$list->user_id,
							'user_profile_name' =>$list->user_profile_name,								 
							'country_title' =>$serviceManager->get('translator')->translate($list->country_title),
							'country_code' =>$list->country_code,
							'city' =>$serviceManager->get('translator')->translate($list->name),
							'profile_photo' =>$list->profile_photo,	
							'user_fbid' => $list->user_fbid,							
							'group_count' =>$list->group_count,
							'created_group_count' =>$created_group_count,
							'is_friend' =>$list->is_friend,
							'is_requested' =>$list->is_requested,
							'get_request' =>$list->get_request,
							'tag_category_count' =>count($tag_category),
							'tag_category' =>$tag_category,
						);
					}
					$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
					$mutual_friends_count =  0;
					if($userinfo->user_id != $identity->user_id){
						$mutual_friends_count = $this->getUserFriendTable()->getAllMutualFriendsCount($userinfo->user_id,$identity->user_id)->friends_count;
					}else{
						$request_count  = $this->getUserFriendRequestTable()->getAllReuqestsCount($userinfo->user_id);					 
						$sent_count  = $this->getUserFriendRequestTable()->getAllSentCount($userinfo->user_id);	
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['arrFriends'] = $arrFriends;	
		$return_array['friends_count'] = $friends_count;
		$return_array['request_count'] = $request_count;
		$return_array['mutual_friends_count'] = $mutual_friends_count;
		$return_array['sent_count'] = $sent_count;		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function receivedRequestsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arrFriends = array(); 
		$friends_count = 0;
		$request_count  = 0;
		$mutual_friends_count = 0;
		$sent_count  = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					$page = $post['page_counter'];
					$offset = ($page)?($page)*10:0;	
					$friendslist = $this->getUserFriendRequestTable()->getAllFriendReuqests($identity->user_id,$offset,10);					
					foreach($friendslist as $list){ 
						$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($list->user_id);
						$objcreated_group_count = $this->getUserGroupTable()->getCreatedGroupCount($list->user_id);
						if(!empty($objcreated_group_count)){
						$created_group_count = $objcreated_group_count->created_group_count;
						}else{$created_group_count =0;}
						$arrFriends[] = array(
							'user_given_name' =>$list->user_given_name,
							'user_id' =>$list->user_id,
							'user_fbid' =>$list->user_fbid,
							'user_profile_name' =>$list->user_profile_name,								 
							'country_title' =>$list->country_title,
							'country_code' =>$list->country_code,
							'city' =>$list->name,
							'profile_photo' =>$list->profile_photo,	
							'user_fbid' => $list->user_fbid,							
							'group_count' =>$list->group_count,
							'created_group_count' =>$created_group_count,							 
							'tag_category_count' =>count($tag_category),
							'tag_category' =>$tag_category,
						);
					}
					$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
					$mutual_friends_count =  0;
					if($userinfo->user_id != $identity->user_id){
						$mutual_friends_count = $this->getUserFriendTable()->getAllMutualFriendsCount($userinfo->user_id,$identity->user_id)->friends_count;
					}else{
						$request_count  = $this->getUserFriendRequestTable()->getAllReuqestsCount($userinfo->user_id);					 
						$sent_count  = $this->getUserFriendRequestTable()->getAllSentCount($userinfo->user_id);	
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['arrFriends'] = $arrFriends;	
		$return_array['friends_count'] = $friends_count;
		$return_array['request_count'] = $request_count;
		$return_array['mutual_friends_count'] = $mutual_friends_count;
		$return_array['sent_count'] = $sent_count;				
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function sentRequestsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arrFriends = array(); 
		$friends_count = 0;
		$request_count  = 0;
		$mutual_friends_count = 0;
		$sent_count  = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					$page = $post['page_counter'];
					$offset = ($page)?($page)*10:0;	
					$friendslist = $this->getUserFriendRequestTable()->getAllFriendSentReuqests($identity->user_id,$offset,10);					
					foreach($friendslist as $list){ 
						$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($list->user_id);
						$objcreated_group_count = $this->getUserGroupTable()->getCreatedGroupCount($list->user_id);
						if(!empty($objcreated_group_count)){
						$created_group_count = $objcreated_group_count->created_group_count;
						}else{$created_group_count =0;}
						$arrFriends[] = array(
							'user_given_name' =>$list->user_given_name,
							'user_id' =>$list->user_id,
							'user_fbid' =>$list->user_fbid,
							'user_profile_name' =>$list->user_profile_name,								 
							'country_title' =>$list->country_title,
							'country_code' =>$list->country_code,
							'city' =>$list->name,
							'profile_photo' =>$list->profile_photo,	
							'user_fbid' => $list->user_fbid,						 
							'group_count' =>$list->group_count,
							'created_group_count' =>$created_group_count,							 
							'tag_category_count' =>count($tag_category),
							'tag_category' =>$tag_category,
						);
					}
					$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
					$mutual_friends_count =  0;
					if($userinfo->user_id != $identity->user_id){
						$mutual_friends_count = $this->getUserFriendTable()->getAllMutualFriendsCount($userinfo->user_id,$identity->user_id)->friends_count;
					}else{
						$request_count  = $this->getUserFriendRequestTable()->getAllReuqestsCount($userinfo->user_id);					 
						$sent_count  = $this->getUserFriendRequestTable()->getAllSentCount($userinfo->user_id);	
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['arrFriends'] = $arrFriends;	
		$return_array['friends_count'] = $friends_count;
		$return_array['request_count'] = $request_count;
		$return_array['mutual_friends_count'] = $mutual_friends_count;
		$return_array['sent_count'] = $sent_count;				
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function mutualFriendsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arrFriends = array(); 
		$friends_count = 0;
		$request_count  = 0;
		$mutual_friends_count = 0;
		$sent_count  = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();
			$profilename = $this->params('member_profile');
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					$page = $post['page_counter'];
					$offset = ($page)?($page)*10:0;	
					$friendslist = $this->getUserFriendTable()->getAllMutualFriends($userinfo->user_id,$identity->user_id,$offset,10);					
					foreach($friendslist as $list){ 
						$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($list->user_id);
						$objcreated_group_count = $this->getUserGroupTable()->getCreatedGroupCount($list->user_id);
						if(!empty($objcreated_group_count)){
						$created_group_count = $objcreated_group_count->created_group_count;
						}else{$created_group_count =0;}
						$arrFriends[] = array(
							'user_given_name' =>$list->user_given_name,
							'user_id' =>$list->user_id,
							'user_fbid' =>$list->user_fbid,
							'user_profile_name' =>$list->user_profile_name,								 
							'country_title' =>$list->country_title,
							'country_code' =>$list->country_code,
							'city' =>$list->name,
							'profile_photo' =>$list->profile_photo,
							'user_fbid' => $list->user_fbid,							
							'group_count' =>$list->group_count,
							'created_group_count' =>$created_group_count,							 
							'tag_category_count' =>count($tag_category),
							'tag_category' =>$tag_category,
						);
					}
					$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
					$mutual_friends_count =  0;
					if($userinfo->user_id != $identity->user_id){
						$mutual_friends_count = $this->getUserFriendTable()->getAllMutualFriendsCount($userinfo->user_id,$identity->user_id)->friends_count;
					}else{
						$request_count  = $this->getUserFriendRequestTable()->getAllReuqestsCount($userinfo->user_id);					 
						$sent_count  = $this->getUserFriendRequestTable()->getAllSentCount($userinfo->user_id);	
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['arrFriends'] = $arrFriends;
		$return_array['friends_count'] = $friends_count;
		$return_array['request_count'] = $request_count;
		$return_array['mutual_friends_count'] = $mutual_friends_count;
		$return_array['sent_count'] = $sent_count;
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function feedsAction(){  
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) { 
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();			 
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			$session = New Container('language');
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){
				$user_profileData = $this->getUserTable()->getProfileDetails($identity->user_id);
				$userGroups = $this->getUserGroupTable()->fetchAllActiveGroupsOfaUser($identity->user_id);
				$upcoming_activity = $this->getActivityTable()->getActivityForMap($identity->user_id);
				$viewModel->setVariable( 'upcoming_activity', $upcoming_activity);
				$viewModel->setVariable( 'userGroups', $userGroups);
				$viewModel->setVariable( 'profilename', $userinfo->user_profile_name);
				$viewModel->setVariable( 'userinfo', $user_profileData);				
				$config = $this->getServiceLocator()->get('Config');				 
				$viewModel->setVariable('image_folders',$config['image_folders']);					 
				$viewModel->setVariable( 'error', $error);				 	 
				return $viewModel; 
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));		
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));} 
	}
	public function timeAgo($time_ago){ //echo $time_ago;die();
		$time_ago = strtotime($time_ago);
		$cur_time   = time();
		$time_elapsed   = $cur_time - $time_ago;
		$seconds    = $time_elapsed ;
		$minutes    = round($time_elapsed / 60 );
		$hours      = round($time_elapsed / 3600);
		$days       = round($time_elapsed / 86400 );
		$weeks      = round($time_elapsed / 604800);
		$months     = round($time_elapsed / 2600640 );
		$years      = round($time_elapsed / 31207680 );
		// Seconds
		if($seconds <= 60){
			return "0m";
		}
		//Minutes
		else if($minutes <=60){
			if($minutes==1){
				return "1m";
			}
			else{
				return $minutes."m";
			}
		}
		//Hours
		else if($hours <=24){
			if($hours==1){
				return "1h";
			}else{
				return $hours."h";
			}
		}
		//Days
		else if($days <= 7){
			if($days==1){
				return "1d";
			}else{
				return $days."d";
			}
		}
		//Weeks
		else if($weeks <= 4.3){
			if($weeks==1){
				return "1w";
			}else{
				return $weeks."w";
			}
		}
		//Months
		else if($months <=12){
			if($months==1){
				return "1mo";
			}else{
				return $months."mo";
			}
		}
		//Years
		else{
			if($years==1){
				return "1yr";
			}else{
				return $years."yr";
			}
		}
	}
	public function  get_youtube_id_from_url($url){
		if (stristr($url,'youtu.be/'))
			{preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID); return isset($final_ID[4])?$final_ID[4]:''; }
		else 
			{@preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|channel\/)([a-z_A-Z0-9\-]{11})/i', $url, $IDD); return isset($IDD[5])?$IDD[5]:''; }
	}
	public function exploreAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$similar = $this->params()->fromQuery('similar');
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$this->layout()->identity = $identity;
			$session = New Container('language');
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			$viewModel->setVariable('image_folders',$config['image_folders']);
			$viewModel->setVariable('profilename',$identity->user_profile_name);
			$similarTagCategories = array();
			if(!empty($similar)){
				$similarTagCategories = $this->getGroupTagTable()->getAllGroupTagCategiry($similar);
			}
			$viewModel->setVariable( 'similarTagCategories' ,  $similarTagCategories);
			if(!empty($userinfo)&&$userinfo->user_id){
				$group_count = $this->getUserTagTable()->getCountOfAllMatchedGroupsofUser($identity->user_id)->group_count;
				$viewModel->setVariable( 'group_count' ,  $group_count);
				$user_tags = $this->getUserTagTable()->getAllUserTagsWithCategiry($userinfo->user_id);
				$viewModel->setVariable( 'user_tags' ,  $user_tags);
				$profile_data = $this->getUserTable()->getProfileDetails($userinfo->user_id);
				$viewModel->setVariable( 'profile_data' , $profile_data);
				$arrcategories		= $this->getTagCategoryTable()->getActiveCategories();	
				$categories = array();
				if(!empty($arrcategories)){
					foreach($arrcategories as $list){
						$categories[] = array(
										'tag_category_id' => $list['tag_category_id'],
										'tag_category_title' => $list['tag_category_title'],
										'tag_category_icon' => $list['tag_category_icon'],
										'tag_category_desc' => $list['tag_category_desc'],
										'tag_category_title_locale' =>$serviceManager->get('translator')->translate($list['tag_category_title']),
										);
					}
				}
				$viewModel->setVariable( 'categories' , $categories);
				$myIntrests = $this->getUserTagTable()->getAllUserTags($identity->user_id);
				$viewModel->setVariable( 'myIntrests' , $myIntrests);
				return $viewModel;
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
	}
	public function settingsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$session = New Container('language');			 
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;
			$profilename = $identity->user_given_name;
			$viewModel->setVariable( 'profilename', $profilename);			 
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){			
				$profile_data = $this->getUserTable()->getProfileDetails($userinfo->user_id);	
				$notify_content = $profile_data['user_profile_notifyme_id'];
				$user_email_me_content = $profile_data['user_profile_emailme_id'];
				$viewModel->setVariable( 'user_email_me_content' , $user_email_me_content);
				$viewModel->setVariable( 'user_notify_me_content' , $notify_content);
				$viewModel->setVariable( 'profile_data' , $profile_data);
				$viewModel->setVariable( 'error', $error);
				$timezones = $this->getTimezoneTable()->getTimezonesList();
				$viewModel->setVariable( 'timezones', $timezones);
				$arr_notifyContent = $this->getNotifymeTable()->getcontentList();
				$notifyContent = array();
				if(!empty($arr_notifyContent)){
					foreach($arr_notifyContent as $list){
						$notifyContent[] = array(
										'notify_id' => $list['notify_id'],
										'notify_content' => $list['notify_content'],
										'notify_content_locale' => $serviceManager->get('translator')->translate($list['notify_content']),
										);
					}
				}
				$viewModel->setVariable( 'notifyContent', $notifyContent);
				$arr_emailmecontent = $this->getEmailmeTable()->getcontentList();
				$emailmecontent = array();
				if(!empty($arr_emailmecontent)){
					foreach($arr_emailmecontent as $list){
						$emailmecontent[] = array(
										'emailme_id' => $list['emailme_id'],
										'emailme_content' => $list['emailme_content'],
										'emailme_content_locale' => $serviceManager->get('translator')->translate($list['emailme_content']),
										);
					}
				}
				$viewModel->setVariable( 'emailmecontent', $emailmecontent);
				$locales = $this->getLocalesTable()->fetchAll();
				$session = New Container('language');
				$locales_selected = (isset($session->language))?$session->language:'';
				if(!empty($userinfo->user_locale)){
					$localedata = $this->getLocalesTable()->getLocales($userinfo->user_locale);
					$locales_selected =$localedata->locales_code;
				}
				$viewModel->setVariable( 'locales', $locales);
				$viewModel->setVariable( 'locales_selected', $locales_selected);
				return $viewModel;
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
		
	}
	public function saveSettingsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arrFriends = array(); 
		$friends_count = 0;
		$request_count  = 0;
		$mutual_friends_count = 0;
		$sent_count  = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();			
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					if(!empty($post)){
						switch($post['settings_type']){
							case 'account':
								$user_name = $post['user_name'];
								$user_email = $post['user_email'];
								$timezone = $post['timezone']; 
								$phone = $post['phone'];
								$error = ($user_name == ''&&$user_name==null&&$user_name=='undefined')?$serviceManager->get('translator')->translate('User name required'):$error;
								$error = ($user_email == ''&&$user_email==null&&$user_email=='undefined')?$serviceManager->get('translator')->translate('User email required'):$error;
								$error = (!filter_var($user_email, FILTER_VALIDATE_EMAIL))?$serviceManager->get('translator')->translate('Enter a valid email address'):$error;								
								$error = (!$this->getUserTable()->checkEmailExists($user_email,$identity->user_id))?$serviceManager->get('translator')->translate('Email already exist'):$error;
								 
								if($error==''){ 
									$data['user_given_name'] = $user_name;
									$data_profile['user_profile_user_id'] = $identity->user_id;
									$data['user_email'] 		= $user_email;
									$data['user_timezone_id'] 	= $timezone;
									$data_profile['user_profile_phone'] = $phone;								 
									if($this->getUserTable()->updateUser($data,$identity->user_id)){
										$this->getUserProfileTable()->updateUserProfile($data_profile,$identity->user_id);
										$userinfo = $this->getUserTable()->getUser($identity->user_id);
										$storage = $auth->getStorage();
										$storage->write($userinfo);
									}else{$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again");}
								}
							break;
							case 'notification':
								$data_profile['user_profile_emailme_id'] = ($post['EmailmeId']!='')?implode(',',$post['EmailmeId']):'';
								$data_profile['user_profile_notifyme_id'] = ($post['NotifymeId']!='')?implode(',',$post['NotifymeId']):'';
								$this->getUserProfileTable()->updateUserProfile($data_profile,$identity->user_id);
							break;
							case 'password':
								$bcrypt = new Bcrypt();
								$currentPassword = $post['current_password'];
								$user_password = $post['user_password'];
								$error = ($currentPassword == ''&&$currentPassword==null&&$currentPassword=='undefined')?$serviceManager->get('translator')->translate('Current Password Required'):$error;
								$error = ($user_password == ''&&$user_password==null&&$user_password=='undefined')?$serviceManager->get('translator')->translate('New Password Required'):$error;
								$cPassword = strip_tags($currentPassword);							 
								$error =(!$bcrypt->verify($cPassword, $userinfo->user_password))?$error = $serviceManager->get('translator')->translate('Current password is wrong'):$error;
								 
								if($error==''){
									$password = strip_tags($user_password);
									$password = $bcrypt->create(trim($password));
									$data['user_password'] = $password;									
									if($this->getUserTable()->updateUser($data,$identity->user_id)){
									;
									}else{$error =$serviceManager->get('translator')->translate("Some error occurred. Please try again");}
								} 
							break;
						}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function updateBioAction(){
		$error = '';
		$auth = new AuthenticationService();	
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();			
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					if(!empty($post)){
						 $data['user_profile_about_me'] 		= $post['bio'];
						 $this->getUserProfileTable()->updateUserProfile($data,$identity->user_id);
					}else{$error =  $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function updatProfilePicAction(){
		$error = '';
		$auth = new AuthenticationService();	 
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();			
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($userinfo)&&$userinfo->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					if(!empty($post)){						 
						$img = $_POST['imageData'];  
						if($img!=''){
							$img = str_replace('data:image/png;base64,', '', $img);
							$img = str_replace(' ', '+', $img);
							$data = base64_decode($img);
							$config = $this->getServiceLocator()->get('Config');
							$imagePath_dir = $config['pathInfo']['ROOTPATH'].'/public/datagd/profile/'.$identity->user_id.'/';
							$filename = 'profile_'.$identity->user_id.''.time().'.png';	
							$imagePath = $config['pathInfo']['ROOTPATH'].'/public/datagd/profile/'.$identity->user_id.'/'.$filename;
							//echo $imagePath;die();
							if(!is_dir($imagePath_dir)){							
								mkdir($imagePath_dir);
							} 
							$photodata= array();
							if(file_put_contents($imagePath, $data)){
								$photodata['profile_user_id'] = $identity->user_id;
								$photodata['profile_photo'] = $filename;
								$photodata['user_profile_photo_status'] = 'available';
								$insert_id = $this->getUserProfilePhotoTable()->addUserProfilePic($photodata);
								$user_data = array();
								if($insert_id){
									$user_data['user_profile_photo_id'] = $insert_id;
									$this->getUserTable()->updateUser($user_data,$identity->user_id);
								}else{$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again");}
							}							 
						}else{$error = $serviceManager->get('translator')->translate("Image not available");}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getFeedsAction(){
		$error = '';
		$feeds = array();
		$auth = new AuthenticationService();	
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) { 
			$identity  = $auth->getIdentity();			
			$userinfo  = $this->getUserTable()->getUser($identity->user_id);
			$request   = $this->getRequest();
			if($request->isPost()){ 
				$page   = $this->getRequest()->getPost('page');
                $limit  =10;
				$page   =($page>0)?$page-1:0;
				$offset = $page*$limit;
				$type	= $this->getRequest()->getPost('type');
				$group  = $this->getRequest()->getPost('group');
				$activity  = $this->getRequest()->getPost('activity');
				$group_id = '';
				if($group!='All'){
					$group_info = $this->getGroupTable()->getGroupForSEO($group);
					if(!empty($group_info)){$group_id = $group_info->group_id;}
				}
				if(!empty($userinfo)&&$userinfo->user_id){				 
					$feeds_list = $this->getGroupTable()->getNewsFeeds($identity->user_id,$type,$group_id,$activity,$limit,$offset);				 
					foreach($feeds_list as $list){
						$is_admin = 0;
						if($this->getUserGroupTable()->checkOwner($list['group_id'],$list['user_id'])){
							$is_admin = 1;
						}
						$is_logged_user_admin = 0;
						if($this->getUserGroupTable()->checkOwner($list['group_id'],$identity->user_id)){
							$is_logged_user_admin = 1;
						}
						switch($list['type']){
							case "New Activity":
							$activity_details = array();
							$activity = $this->getActivityTable()->getActivityForFeed($list['event_id'],$identity->user_id);
							$SystemTypeData   = $this->groupTable->fetchSystemType("Activity");
							$like_details     = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
							$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
							$str_liked_users  = '';
							$arr_likedUsers = array(); 
							if(!empty($like_details)&&isset($like_details['likes_counts'])){  
								$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
								
								if($like_details['is_liked']==1){
									$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
								}
								if($like_details['likes_counts']>0&&!empty($liked_users)){
									foreach($liked_users as $likeuser){
										$arr_likedUsers[] = $likeuser['user_given_name'];
									}
								}
								 
							}
							$rsvp_count = $this->getActivityRsvpTable()->getCountOfAllRSVPuser($activity->group_activity_id)->rsvp_count;
							$attending_users = array();
							if($rsvp_count>0){
								$attending_users = $this->getActivityRsvpTable()->getJoinMembers($activity->group_activity_id,3,0);
								//print_r($attending_users);die();
							}
							$allow_join = (strtotime($activity->group_activity_start_timestamp)>strtotime("now"))?1:0;
							$activity_details = array(
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_location_lat" => $activity->group_activity_location_lat,
													"group_activity_location_lng" => $activity->group_activity_location_lng,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => date("M d,Y h:i a",strtotime($activity->group_activity_start_timestamp)),												 
													"user_given_name" => $list['user_given_name'],
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],
													"group_id" =>$list['group_id'],	
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],
													"profile_photo" => $list['profile_photo'],	
													"user_fbid" => $list['user_fbid'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],
													"liked_users"	=>$arr_likedUsers,
													"rsvp_count" =>($activity->rsvp_count)?$activity->rsvp_count:0,
													"rsvp_friend_count" =>($activity->friend_count)?$activity->friend_count:0,
													"is_going"=>$activity->is_going,
													"attending_users" =>$attending_users,
													"allow_join" =>$allow_join,
													'is_admin'=>$is_admin,
													'is_logged_user_admin' =>$is_logged_user_admin,
													);
							$feeds[] = array('content' => $activity_details,
											'type'=>$list['type'],
											'time'=>$this->timeAgo($list['update_time']),
							); 							
							break;
							case "New Status":
								$discussion_details = array();
								$discussion = $this->getDiscussionTable()->getDiscussionForFeed($list['event_id']);
								$SystemTypeData = $this->groupTable->fetchSystemType("Discussion");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id);
								$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
								$str_liked_users = '';
								$arr_likedUsers = array();
								if(!empty($like_details)&&isset($like_details['likes_counts'])){  
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
									
									if($like_details['is_liked']==1){
										$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}
								$discussion_details = array(
													"group_discussion_id" => $discussion->group_discussion_id,
													"group_discussion_content" => $discussion->group_discussion_content,
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],
													"group_id" =>$list['group_id'],												
													"user_given_name" => $list['user_given_name'],
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],												 
													"profile_photo" => $list['profile_photo'],
													"user_fbid" => $list['user_fbid'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],
													"liked_users"	=>$arr_likedUsers,
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],
													'is_admin'=>$is_admin,
													'is_logged_user_admin' =>$is_logged_user_admin,
													);
								$feeds[] = array('content' => $discussion_details,
												'type'=>$list['type'],
												'time'=>$this->timeAgo($list['update_time']),
								); 
							break;
							case "New Media":
								$media_details = array();
								$media = $this->getGroupMediaTable()->getMediaForFeed($list['event_id']); 
								$media_contents = $this->getGroupMediaContentTable()->getMediaContents(json_decode($media->media_content));
								$media_files = [];
								foreach($media_contents as $mfile){
									if($mfile['media_type'] == 'youtube'){
										$media_files[] = array(
												'id'=>$mfile['media_content_id'],
												'files'=>$mfile['content'],
												'video_id'=>$this->get_youtube_id_from_url($mfile['content']),
												'media_type'=>$mfile['media_type'],
												);
									}else{
										$media_files[] = array(
														'id'=>$mfile['media_content_id'],
														'files'=>$mfile['content'],
														'media_type'=>$mfile['media_type'],
														);
									}
								}								 
								$SystemTypeData = $this->groupTable->fetchSystemType("Media");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id);
								$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
								$str_liked_users = '';
								$arr_likedUsers = array();
								if(!empty($like_details)&&isset($like_details['likes_counts'])){  
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
									
									if($like_details['is_liked']==1){
										$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}
								$media_details = array(
													"group_media_id" => $media->group_media_id,													 
													"media_content" => $media->media_content,
													"media_caption" => $media->media_caption,
													"media_files" => $media_files,
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],	
													"group_id" =>$list['group_id'],
													"album_title"=>$media->album_title,
													"user_given_name" => $list['user_given_name'],
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],												 
													"profile_photo" => $list['profile_photo'],
													"user_fbid" => $list['user_fbid'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],	
													"liked_users"	=>$arr_likedUsers,	
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],	
													'is_admin'=>$is_admin,		
													'is_logged_user_admin' =>$is_logged_user_admin,													
													);
								$feeds[] = array('content' => $media_details,
												'type'=>$list['type'],
												'time'=>$this->timeAgo($list['update_time']),
								); 
							break;
						}
					} 
				}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");} 
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;
		$return_array['feeds'] = $feeds;
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getMyFeedsAction(){
		$error = '';
		$feeds = array();
		$auth = new AuthenticationService();	
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) { 
			$identity  = $auth->getIdentity();			
			$myinfo  = $this->getUserTable()->getUser($identity->user_id);
			$request   = $this->getRequest();
			if($request->isPost()){ 
				$page   = $this->getRequest()->getPost('page');
                $limit  =10;
				$page   =($page>0)?$page-1:0;
				$offset = $page*$limit;
				$type	= $this->getRequest()->getPost('type');
				$profilename  = $this->getRequest()->getPost('profile');
				$userinfo = $this->getUserTable()->getUserByProfilename($profilename); 
				if(!empty($userinfo)&&$userinfo->user_id){				 
					$feeds_list = $this->getGroupTable()->getMyFeeds($userinfo->user_id,$identity->user_id,$type,$limit,$offset);		
					//print_r($feeds_list);die();
					foreach($feeds_list as $list){
						$is_admin = 0;
						if($this->getUserGroupTable()->checkOwner($list['group_id'],$list['user_id'])){
							$is_admin = 1;
						}
						$is_logged_user_admin = 0;
						if($this->getUserGroupTable()->checkOwner($list['group_id'],$identity->user_id)){
							$is_logged_user_admin = 1;
						}
						switch($list['type']){
							case "New Activity":
							$activity_details = array();
							$activity = $this->getActivityTable()->getActivityForFeed($list['event_id'],$identity->user_id);
							$SystemTypeData   = $this->groupTable->fetchSystemType("Activity");
							$like_details     = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
							$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
							$str_liked_users  = '';
							$logged_user_ismember = 0;
							if($this->getUserGroupTable()->is_member($identity->user_id,$list['group_id'])){
								$logged_user_ismember = 1;
							}
							$arr_likedUsers = array();
							if(!empty($like_details)){  
								$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
								
								if($like_details['is_liked']==1){
									$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
								}
								if($like_details['likes_counts']>0&&!empty($liked_users)){
									foreach($liked_users as $likeuser){
										$arr_likedUsers[] = $likeuser['user_given_name'];
									}
								}
								 
							}
							$rsvp_count = $this->getActivityRsvpTable()->getCountOfAllRSVPuser($activity->group_activity_id)->rsvp_count;
							$attending_users = array();
							if($rsvp_count>0){
								$attending_users = $this->getActivityRsvpTable()->getJoinMembers($activity->group_activity_id,3,0);
							}
							$allow_join = (strtotime($activity->group_activity_start_timestamp)>strtotime("now"))?1:0;
							$activity_details = array(
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_location_lat" => $activity->group_activity_location_lat,
													"group_activity_location_lng" => $activity->group_activity_location_lng,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => date("M d,Y h:i a",strtotime($activity->group_activity_start_timestamp)),												 
													"user_given_name" => $list['user_given_name'],
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],
													"group_id" =>$list['group_id'],	
													"group_type"=>$list['group_type'],	
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],												 
													"profile_photo" => $list['profile_photo'],
													"user_fbid" => $list['user_fbid'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],
													"liked_users"	=>$arr_likedUsers,
													"rsvp_count" =>($activity->rsvp_count)?$activity->rsvp_count:0,
													"rsvp_friend_count" =>($activity->friend_count)?$activity->friend_count:0,
													"is_going"=>$activity->is_going,
													"attending_users" =>$attending_users,
													"allow_join" =>$allow_join,
													'is_admin'=>$is_admin,
													'logged_user_ismember'=>$logged_user_ismember,
													'is_logged_user_admin'=>$is_logged_user_admin,													
													);
							$feeds[] = array('content' => $activity_details,
											'type'=>$list['type'],
											'time'=>$this->timeAgo($list['update_time']),
							); 							
							break;
							case "New Status":
								$discussion_details = array();
								$discussion = $this->getDiscussionTable()->getDiscussionForFeed($list['event_id']);
								$SystemTypeData = $this->groupTable->fetchSystemType("Discussion");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id);
								$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
								$str_liked_users = '';
								$arr_likedUsers = array();
								$logged_user_ismember = 0;
								if($this->getUserGroupTable()->is_member($identity->user_id,$list['group_id'])){
									$logged_user_ismember = 1;
								}
								if(!empty($like_details)&&isset($like_details['likes_counts'])){
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);									
									if($like_details['is_liked']==1){
									$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}
								$discussion_details = array(
													"group_discussion_id" => $discussion->group_discussion_id,
													"group_discussion_content" => $discussion->group_discussion_content,
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],
													"group_id" =>$list['group_id'],
													"group_type"=>$list['group_type'],	
													"user_given_name" => $list['user_given_name'],
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],												 
													"profile_photo" => $list['profile_photo'],
													"user_fbid" => $list['user_fbid'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],
													"liked_users"	=>$arr_likedUsers,
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],
													'is_admin'=>$is_admin,	
													'logged_user_ismember'=>$logged_user_ismember,
													'is_logged_user_admin'=>$is_logged_user_admin,
													);
								$feeds[] = array('content' => $discussion_details,
												'type'=>$list['type'],
												'time'=>$this->timeAgo($list['update_time']),
								); 
							break;
							case "New Media":
								$media_details = array();
								$media = $this->getGroupMediaTable()->getMediaForFeed($list['event_id']);
								$media_contents = $this->getGroupMediaContentTable()->getMediaContents(json_decode($media->media_content));
								$media_files = [];
								foreach($media_contents as $mfile){
									if($mfile['media_type'] == 'youtube'){
										$media_files[] = array(
												'id'=>$mfile['media_content_id'],
												'files'=>$mfile['content'],
												'video_id'=>$this->get_youtube_id_from_url($mfile['content']),
												'media_type'=>$mfile['media_type'],
												);
									}else{
										$media_files[] = array(
														'id'=>$mfile['media_content_id'],
														'files'=>$mfile['content'],
														'media_type'=>$mfile['media_type'],
														);
									}
								}							 
								$SystemTypeData = $this->groupTable->fetchSystemType("Media");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id);
								$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
								$str_liked_users = '';
								$logged_user_ismember = 0;
								if($this->getUserGroupTable()->is_member($identity->user_id,$list['group_id'])){
									$logged_user_ismember = 1;
								}
								$arr_likedUsers = array();
								if(!empty($like_details)&&isset($like_details['likes_counts'])){
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
									
									if($like_details['is_liked']==1){
										$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}
								$media_details = array(
													"group_media_id" => $media->group_media_id, 
													"media_content" => $media->media_content,
													"media_caption" => $media->media_caption,
													"media_files" => $media_files,
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],	
													"group_id" =>$list['group_id'],
													"group_type"=>$list['group_type'],	
													"user_given_name" => $list['user_given_name'],
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],												 
													"profile_photo" => $list['profile_photo'],
													"user_fbid" => $list['user_fbid'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],	
													"liked_users"	=>$arr_likedUsers,	
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],	
													'is_admin'=>$is_admin,	
													'logged_user_ismember'=>$logged_user_ismember,
													'is_logged_user_admin'=>$is_logged_user_admin,
													);
								$feeds[] = array('content' => $media_details,
												'type'=>$list['type'],
												'time'=>$this->timeAgo($list['update_time']),
								); 
							break;
						}
					}		 
				}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");} 
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;
		$return_array['feeds'] = $feeds;
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function activitiesAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$session = New Container('language');
			$serviceManager = $this->getServiceLocator();
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$identity = $auth->getIdentity();			 		
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;
			$this->layout()->identity = $identity;			 		 			 
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
			$viewModel->setVariable( 'current_Profile', $identity->user_profile_name);
			$config = $this->getServiceLocator()->get('Config');			 
			$viewModel->setVariable('image_folders',$config['image_folders']);
			if(!empty($userinfo)&&$userinfo->user_id){
				$profileWidget = $this->forward()->dispatch('User\Controller\UserProfile', array(
											'action' => 'profile',
											'member_profile'     => $identity->user_profile_name,							 
										));
				$viewModel->addChild($profileWidget, 'profileWidget');
				$friends_count = $this->getUserFriendTable()->getFriendsCount($userinfo->user_id)->friends_count;
				$viewModel->setVariable( 'friends_count' , $friends_count);	
				$profile_type = ($userinfo->user_id!=$identity->user_id)?'other':'mine';
				$intTotalGroups      = $this->getUserGroupTable()->fetchAllUserGroupCount( $userinfo->user_id,$identity->user_id,'',$profile_type);
				$viewModel->setVariable( 'group_count' , $intTotalGroups['group_count']);
				$profile_data = $this->getUserTable()->getProfileDetails($userinfo->user_id);				 
				$viewModel->setVariable( 'profile_data' , $profile_data);
				$user_profileData = $this->getUserTable()->getProfileDetails($identity->user_id);				 
				$viewModel->setVariable( 'userinfo', $user_profileData);
				$objCommentsCount  = $this->getCommentTable()->getUserCommentCount($identity->user_id);
				$CommentsCount = (!empty($objCommentsCount))?$objCommentsCount->comment_count:0;
				$viewModel->setVariable( 'CommentsCount', $CommentsCount);
				$objlikesCount  = $this->getLikeTable()->getUserLIkeCount($identity->user_id);
				$LikesCount = (!empty($objlikesCount))?$objlikesCount->like_count:0;
				$viewModel->setVariable( 'LikesCount', $LikesCount);	
				$viewModel->setVariable( 'error', $error);				 
				return $viewModel; 
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));		
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
	}
	public function getMyActivitiesAction(){
		$error = '';
		$feeds = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();		
		if ($auth->hasIdentity()) { 
			$identity  = $auth->getIdentity();			
			$myinfo  = $this->getUserTable()->getUser($identity->user_id);
			$request   = $this->getRequest();
			if($request->isPost()){ 
				$page   = $this->getRequest()->getPost('page');
                $limit  =10;
				$page   =($page>0)?$page-1:0;
				$offset = $page*$limit;
				$type	= $this->getRequest()->getPost('type');			 
				if(!empty($myinfo)&&$myinfo->user_id){				 
					$feeds_list = $this->getGroupTable()->getMyActivity($myinfo->user_id,$type,$limit,$offset);				 
					foreach($feeds_list as $list){
						$is_admin = 0;
						if($this->getUserGroupTable()->checkOwner($list['group_id'],$list['user_id'])){
							$is_admin = 1;
						}
						$is_logged_user_admin = 0;
						if($this->getUserGroupTable()->checkOwner($list['group_id'],$identity->user_id)){
							$is_logged_user_admin = 1;
						}
						switch($list['type']){
							case "New Activity":
							$activity_details = array();
							$arr_likedUsers = array();
							$activity = $this->getActivityTable()->getActivityForFeed($list['event_id'],$identity->user_id);
							$SystemTypeData   = $this->groupTable->fetchSystemType("Activity");
							$like_details     = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
							$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
							$str_liked_users  = '';
							if(!empty($like_details)){  
								$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);								 
								if($like_details['is_liked']==1){
									$arr_likedUsers[] =  $serviceManager->get('translator')->translate('you');
								}
								if($like_details['likes_counts']>0&&!empty($liked_users)){
									foreach($liked_users as $likeuser){
										$arr_likedUsers[] = $likeuser['user_given_name'];
									}
								}
								 
							}
							$rsvp_count = $this->getActivityRsvpTable()->getCountOfAllRSVPuser($activity->group_activity_id)->rsvp_count;
							$attending_users = array();
							if($rsvp_count>0){
								$attending_users = $this->getActivityRsvpTable()->getJoinMembers($activity->group_activity_id,3,0);
							}
							$allow_join = (strtotime($activity->group_activity_start_timestamp)>strtotime("now"))?1:0;
							$activity_details = array(
													"group_activity_id" => $activity->group_activity_id,
													"group_activity_title" => $activity->group_activity_title,
													"group_activity_location" => $activity->group_activity_location,
													"group_activity_location_lat" => $activity->group_activity_location_lat,
													"group_activity_location_lng" => $activity->group_activity_location_lng,
													"group_activity_content" => $activity->group_activity_content,
													"group_activity_start_timestamp" => date("M d,Y h:i a",strtotime($activity->group_activity_start_timestamp)),												 
													"user_given_name" => $list['user_given_name'],
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],
													"group_id" =>$list['group_id'],	
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],
													"user_fbid" => $list['user_fbid'],										 
													"profile_photo" => $list['profile_photo'],	
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],
													"liked_users"	=>$arr_likedUsers,
													"rsvp_count" =>($activity->rsvp_count)?$activity->rsvp_count:0,
													"rsvp_friend_count" =>($activity->friend_count)?$activity->friend_count:0,
													"is_going"=>$activity->is_going,
													"attending_users" =>$attending_users,
													"allow_join" =>$allow_join,
													'is_admin'=>$is_admin,
													'is_logged_user_admin'=>$is_logged_user_admin,
													);
							$feeds[] = array('content' => $activity_details,
											'type'=>$list['type'],
											'time'=>$this->timeAgo($list['update_time']),
							); 							
							break;
							case "New Status":
								$discussion_details = array();
								$discussion = $this->getDiscussionTable()->getDiscussionForFeed($list['event_id']);
								$SystemTypeData = $this->groupTable->fetchSystemType("Discussion");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id);
								$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
								$str_liked_users = '';
								$arr_likedUsers = array();
								if(!empty($like_details)&&isset($like_details['likes_counts'])){
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
									 
									if($like_details['is_liked']==1){
										$arr_likedUsers[] =  $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}
								$discussion_details = array(
													"group_discussion_id" => $discussion->group_discussion_id,
													"group_discussion_content" => $discussion->group_discussion_content,
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],
													"group_id" =>$list['group_id'],												
													"user_given_name" => $list['user_given_name'],
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],
													"user_fbid" => $list['user_fbid'],
													"profile_photo" => $list['profile_photo'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],
													"liked_users"	=>$arr_likedUsers,
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],
													'is_admin'=>$is_admin,
													'is_logged_user_admin'=>$is_logged_user_admin,
													);
								$feeds[] = array('content' => $discussion_details,
												'type'=>$list['type'],
												'time'=>$this->timeAgo($list['update_time']),
								); 
							break;
							case "New Media":
								$media_details = array();								 
								$media = $this->getGroupMediaTable()->getMediaForFeed($list['event_id']); 
								$media_contents = $this->getGroupMediaContentTable()->getMediaContents(json_decode($media->media_content));
								$media_files = [];
								foreach($media_contents as $mfile){
									if($mfile['media_type'] == 'youtube'){
										$media_files[] = array(
												'id'=>$mfile['media_content_id'],
												'files'=>$mfile['content'],
												'video_id'=>$this->get_youtube_id_from_url($mfile['content']),
												'media_type'=>$mfile['media_type'],
												);
									}else{
										$media_files[] = array(
														'id'=>$mfile['media_content_id'],
														'files'=>$mfile['content'],
														'media_type'=>$mfile['media_type'],
														);
									}
								}		
								$SystemTypeData = $this->groupTable->fetchSystemType("Media");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id);
								$comment_details  = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id); 
								$str_liked_users = '';
								$arr_likedUsers = array();
								if(!empty($like_details)&&isset($like_details['likes_counts'])){
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$list['event_id'],$identity->user_id,2,0);
									 
									if($like_details['is_liked']==1){
										$arr_likedUsers[] =  $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									  
								}
								$media_details = array(
													"group_media_id" => $media->group_media_id,
													"media_content" => $media->media_content,
													"media_caption" => $media->media_caption,
													"media_files" => $media_files,
													"group_title" =>$list['group_title'],
													"group_seo_title" =>$list['group_seo_title'],	
													"group_id" =>$list['group_id'],													
													"user_given_name" => $list['user_given_name'],
													"user_id" => $list['user_id'],
													"user_profile_name" => $list['user_profile_name'],
													"user_fbid" => $list['user_fbid'],
													"profile_photo" => $list['profile_photo'],
													"like_count"	=>$like_details['likes_counts'],
													"is_liked"	=>$like_details['is_liked'],	
													"liked_users"	=>$arr_likedUsers,	
													"comment_counts"	=>$comment_details['comment_counts'],
													"is_commented"	=>$comment_details['is_commented'],	
													'is_admin'=>$is_admin,
													'is_logged_user_admin'=>$is_logged_user_admin,
													);
								$feeds[] = array('content' => $media_details,
												'type'=>$list['type'],
												'time'=>$this->timeAgo($list['update_time']),
								); 
							break;
						}
					}		 
				}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;
		$return_array['feeds'] = $feeds;
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getFriendsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arrFriends = array(); 
		$friends_count = 0;
		$request_count  = 0;
		$mutual_friends_count = 0;
		$sent_count  = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {			 
			$identity = $auth->getIdentity();			 
			if(!empty($identity)&&$identity->user_id){	
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost(); 
					$page = $post['page_counter'];
					$str_search = $post['str_search'];
					$offset = ($page)?($page)*10:0;	
					$friendslist = $this->getUserFriendTable()->getFriendsForSearchWithLimit($identity->user_id,$str_search,$offset,10);				
					 					
					foreach($friendslist as $list){ 						 					 
						$arrFriends[] = array(
							'user_given_name' =>$list->user_given_name,
							'user_id' =>$list->user_id,
							'user_profile_name' =>$list->user_profile_name,					 
							'profile_photo' =>$list->profile_photo,	
							'user_fbid' => $list->user_fbid,						 
						);
					}					 
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['arrFriends'] = $arrFriends;			 
		$result = new JsonModel(array(
			'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function uploadhandlerAction(){
		$error = '';
		$filename = '';
		$image_url ='';
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {	
			$identity = $auth->getIdentity();	
			$request   = $this->getRequest();
			if ($request->isPost()){				 
				if(isset($_FILES)&&isset($_FILES['mediaImage']['name'])&&$_FILES['mediaImage']['name']!=''){
					$allowedImages      = array('image/jpg','image/jpeg','image/png','img/gif');
					$minWidth           = 100;
					$minHeight          = 100;
					if(!in_array($_FILES['mediaImage']['type'], $allowedImages)){
						$error =$serviceManager->get('translator')->translate("This file type is not supported. Please upload any image files");
					}
					$image_dimensions = getimagesize($_FILES['mediaImage']['tmp_name']); 
					$image_width = $image_dimensions[0]; // Image width
					$image_height = $image_dimensions[1]; // Image height					
					if(!in_array($_FILES['mediaImage']['type'], $allowedImages)){
						$error = $serviceManager->get('translator')->translate("This file type is not supported. Please upload any image files");
					}
					else if(($image_width < $minWidth) || ($image_height < $minHeight)){
						$error = $serviceManager->get('translator')->translate("Minimum dimension 100x100");
					}
					if($error ==''){
						$config = $this->getServiceLocator()->get('Config');
						$temp_path = $config['pathInfo']['temppath'];
						if(!is_dir($temp_path)){							
							mkdir($temp_path);
						}
						$user_temp_path = $temp_path.$identity->user_id."/";	
						if(!is_dir($user_temp_path)){							
							mkdir($user_temp_path);
						}
						/*$files = glob($user_temp_path); 
						foreach($files as $file){ 
							if(is_file($file))
							@unlink($file);  
						}*/
						$temp = explode(".",$_FILES["mediaImage"]["name"]);
						$newfilename = rand(1,99999) . '.' .end($temp);
						$filename =  time()."_".$newfilename;
						if (move_uploaded_file($_FILES["mediaImage"]["tmp_name"], $user_temp_path .$filename)) {
							$image_url = $config['pathInfo']['temp_absolute_path'].$identity->user_id."/".$filename;
						}else{
							$error =  $serviceManager->get('translator')->translate("Some error occurred. Please try again");
						}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to locate the image");}	
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}		 
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['filename'] = $filename;
		$return_array['image_url'] = $image_url;
		$result = new JsonModel(array(
			'return_array' => $return_array,      
		));		
		return $result;
	}
	public function UpdateNotifications($user_notification_user_id,$msg,$type,$subject,$from,$sender,$reference_id,$processs){
		$UserGroupNotificationData = array();						
		$UserGroupNotificationData['user_notification_user_id'] =$user_notification_user_id;		 
		$UserGroupNotificationData['user_notification_content']  = $msg;
		$UserGroupNotificationData['user_notification_added_timestamp'] = date('Y-m-d H:i:s');			
		$UserGroupNotificationData['user_notification_notification_type_id'] = $type;
		$UserGroupNotificationData['user_notification_status'] = 'unread';
		$UserGroupNotificationData['user_notification_sender_id'] = $sender;
		$UserGroupNotificationData['user_notification_reference_id'] = $reference_id;		
		$UserGroupNotificationData['user_notification_process'] = $processs;
		#lets Save the User Notification
		$UserGroupNotificationSaveObject = new UserNotification();
		$UserGroupNotificationSaveObject->exchangeArray($UserGroupNotificationData);	
		$insertedUserGroupNotificationId ="";	#this will hold the latest inserted id value
		$insertedUserGroupNotificationId = $this->getUserNotificationTable()->saveUserNotification($UserGroupNotificationSaveObject);
		$userData = $this->getUserTable()->getUser($user_notification_user_id); 
		//$this->sendNotificationMail($msg,$subject,$userData->user_email,$from);
	}
	public function sendNotificationMail($msg,$subject,$emailId,$from){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');		
		$body = $this->renderer->render('user/email/emailInvitation.phtml', array('msg'=>$msg));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";
		$textPart = new MimePart($body);
		$textPart->type = "text/plain";
		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));
		$message = new Mail\Message();
		$message->setFrom($from);
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject($subject);
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');
		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}	
	public function getUserTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTable = (!$this->userTable)?$sm->get('User\Model\UserTable'):$this->userTable;    
	} 
	public function getUserTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTagTable = (!$this->userTagTable)?$sm->get('Tag\Model\UserTagTable'):$this->userTagTable;    
	}
	public function getUserProfileTable(){
		$sm = $this->getServiceLocator();
		return  $this->userProfileTable = (!$this->userProfileTable)?$sm->get('User\Model\UserProfileTable'):$this->userProfileTable;    
	}
	public function getTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->tagTable = (!$this->tagTable)?$sm->get('Tag\Model\TagTable'):$this->tagTable;    
	}
	public function getUserFriendTable(){
		$sm = $this->getServiceLocator();
		return  $this->userFriendTable = (!$this->userFriendTable)?$sm->get('User\Model\UserFriendTable'):$this->userFriendTable;    
	}
	public function getUserFriendRequestTable(){
		$sm = $this->getServiceLocator();
		return  $this->userFriendRequestTable = (!$this->userFriendRequestTable)?$sm->get('User\Model\UserFriendRequestTable'):$this->userFriendRequestTable;    
	}
	public function getUserNotificationTable(){         
		$sm = $this->getServiceLocator();
		return  $this->userNotificationTable = (!$this->userNotificationTable)?$sm->get('Notification\Model\UserNotificationTable'):$this->userNotificationTable;    
    }
	public function getUserGroupTable(){
		$sm = $this->getServiceLocator();
		return  $this->userGroupTable = (!$this->userGroupTable)?$sm->get('Groups\Model\UserGroupTable'):$this->userGroupTable;    
    }
	public function getGroupTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupTable = (!$this->groupTable)?$sm->get('Groups\Model\GroupsTable'):$this->groupTable;    
    }
	public function getActivityTable(){
		$sm = $this->getServiceLocator();
		return  $this->activityTable = (!$this->activityTable)?$sm->get('Activity\Model\ActivityTable'):$this->activityTable;    
    }
	public function getDiscussionTable(){
		$sm = $this->getServiceLocator();
		return  $this->discussionTable = (!$this->discussionTable)?$sm->get('Discussion\Model\DiscussionTable'):$this->discussionTable;    
    }
	public function getGroupMediaTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupMediaTable = (!$this->groupMediaTable)?$sm->get('Groups\Model\GroupMediaTable'):$this->groupMediaTable;    
    }
	public function getLikeTable(){
		$sm = $this->getServiceLocator();
		return  $this->likeTable = (!$this->likeTable)?$sm->get('Like\Model\LikeTable'):$this->likeTable; 
	}
	public function getCommentTable(){
		$sm = $this->getServiceLocator();
		return  $this->commentTable = (!$this->commentTable)?$sm->get('Comment\Model\CommentTable'):$this->commentTable;   
	}
	public function getTagCategoryTable(){
		$sm = $this->getServiceLocator();
        return $this->tagCategoryTable = (!$this->tagCategoryTable)?$sm->get('Tag\Model\TagCategoryTable'):$this->tagCategoryTable;      
    }
	public function getTimezoneTable(){
		$sm = $this->getServiceLocator();
		return  $this->timezoneTable = (!$this->timezoneTable)?$sm->get('User\Model\TimezoneTable'):$this->timezoneTable;
    }
	public function getEmailmeTable(){
		$sm = $this->getServiceLocator();
		return  $this->emailmeTable = (!$this->emailmeTable)?$sm->get('User\Model\EmailmeTable'):$this->emailmeTable;
    }
	public function getNotifymeTable(){
		$sm = $this->getServiceLocator();
		return  $this->notifymeTable = (!$this->notifymeTable)?$sm->get('User\Model\NotifymeTable'):$this->notifymeTable;
    }
	public function getUserProfilePhotoTable(){
		$sm = $this->getServiceLocator();
		return  $this->userProfilePhotoTable = (!$this->userProfilePhotoTable)?$sm->get('User\Model\UserProfilePhotoTable'):$this->userProfilePhotoTable;
    }
	public function getActivityRsvpTable(){
		$sm = $this->getServiceLocator();
		return  $this->activityRsvpTable = (!$this->activityRsvpTable)?$sm->get('Activity\Model\ActivityRsvpTable'):$this->activityRsvpTable;
    }
	public function getGroupTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupTagTable = (!$this->groupTagTable)?$sm->get('Tag\Model\GroupTagTable'):$this->groupTagTable;    
    }
	public function getGroupMediaContentTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupMediaContentTable = (!$this->groupMediaContentTable)?$sm->get('Groups\Model\GroupMediaContentTable'):$this->groupMediaContentTable;    
    }
	public function getLocalesTable(){
		$sm = $this->getServiceLocator();
		return  $this->localesTable = (!$this->localesTable)?$sm->get('Admin\Model\LocalesTable'):$this->localesTable;
    }
}
 