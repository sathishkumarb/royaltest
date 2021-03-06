<?php  
namespace User\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\View\Renderer\PhpRenderer;
use \Exception;
use Zend\Crypt\BlockCipher;
use Zend\Crypt\Password\Bcrypt;	
use User\Auth\BcryptDbAdapter as AuthAdapter;
use Zend\Session\Container;     
use Zend\Authentication\AuthenticationService;
use Zend\Mail;
use User\Model\User;
use User\Model\UserProfile;
use User\Model\Recoveryemails;
use User\Form\Login;       
use User\Form\LoginFilter; 
use User\Form\ResetPassword;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Authentication\Storage\Session;
use Facebook\Controller\Facebook;
use Facebook\Controller\FacebookApiException;
class UserController extends AbstractActionController
{
	public $form_error ;
	protected $userTable;
	protected $userProfileTable;
	protected $RecoveryemailsTable;
	protected $cityTable;
	protected $tagCategoryTable;
	protected $userTagTable;
	protected $localesTable;
	public function  __construct() {

        $this->facebook = new Facebook(array(
            'appId'  => '1603985636524692',
            'secret' => 'e33f99f0cbeb430ad28d25179e304605'
        ));

    }
   	public function indexAction()
    {	
		 	
    }
	public function fbloginAction(){
		$user = null;
        $user = $this->facebook->getUser();
        $user_profile = null;
        $logoutUrl = null;
        $statusUrl = null;
        $config = $this->getServiceLocator()->get('Config');
        if ($user) {
            $logoutUrl = $this->facebook->getLogoutUrl();
            $this->facebook->setExtendedAccessToken();
            $access_token = $this->facebook->getAccessToken();
            $user_profile = $this->facebook->api('/me?access_token='.$access_token);
        }         
		return $this->redirect()->toUrl($this->facebook->getLoginUrl(array('redirect_uri' => $config['pathInfo']['fbredirect'], 'scope' => 'public_profile,email,user_friends,offline_access')));
	}
	public function fbredirectAction(){ 
		$user = $this->facebook->getUser();  
		$sm = $this->getServiceLocator();	
		$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');		
        if ($user) { 
			 try {
            // Proceed knowing you have a logged in user who's authenticated.
                $access_token = $this->facebook->getAccessToken(); 
                $user_profile = $this->facebook->api('/me?access_token='.$access_token);	
                $this->userTable = $sm->get('User\Model\UserTable');
                $this->userProfileTable = $sm->get('User\Model\UserProfileTable');
				$UserData = $this->userTable->getUserByFbid($user_profile['id']);
				if(!empty($UserData)&&$UserData->user_id){
					$checkFbUserData = $UserData;
				}else if(isset($user_profile['email'])&&$user_profile['email']!=''){				 
					$checkFbUserData = $this->userTable->getUserByEmail($user_profile['email']);
				}else{
					$checkFbUserData = $this->userTable->getUserByFbid($user_profile['id']);
				}
                if (!empty($checkFbUserData->user_id)) {
					if($checkFbUserData->user_status == 'not activated'){
						$data = array('user_status'=>"live");
						$this->getUserTable()->updateUser($data,$checkFbUserData->user_id);	
					}
					if($checkFbUserData->user_fbid == ''){
						$data = array('user_fbid'=>$user_profile['id']);
						$this->getUserTable()->updateUser($data,$checkFbUserData->user_id);	
					}
					$container = new Container('fbUser');
					$container->user_id =  $checkFbUserData->user_id;					
					return $this->redirect()->toRoute('user/fbprocess');					
                }else{					 
					$user_data['user_given_name'] =  $user_profile['first_name'].' '.$user_profile['last_name'];
					$user_data['user_first_name'] =  $user_profile['first_name'];
					$user_data['user_last_name'] =  $user_profile['last_name'];
					$user_data['user_profile_name'] = $this->make_url_friendly($user_profile['first_name']."".$user_profile['last_name']);
					$user_data['user_status'] = "live";
					$user_data['user_email'] = (isset($user_profile['email']))?$user_profile['email']:NULL;
					$user_data['user_gender'] = (isset($user_profile['gender']))?$user_profile['gender']:'';
					$user_data['user_register_type'] = 'facebook';
					$user_data['user_fbid'] = $user_profile['id']; 
					$user = new User();
					$user->exchangeArray($user_data);
					$insertedUserId = $this->getUserTable()->saveUser($user);
					if($insertedUserId){
						$user_profile_data['user_profile_user_id'] = $insertedUserId;
						$user_profile_data['user_profile_emailme_id'] = '';
						$user_profile_data['user_profile_notifyme_id'] = '';
						$userProfile = new UserProfile();
						$userProfile->exchangeArray($user_profile_data);
						$insertedUserProfileId = $this->getUserProfileTable()->saveUserProfile($userProfile);
					}
					$container = new Container('fbUser');
					$container->user_id =  $insertedUserId;					 
					return $this->redirect()->toRoute('user/fbprocess');
				}
            }
            catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
		}else{
		return $this->redirect()->toRoute('home', array('action' => 'index'));
		}
	}
	public function fbprocessAction(){		 
		$auth = new AuthenticationService();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			 return $this->redirect()->toUrl('/register');
		}else{			 
			$container = new Container('fbUser');
			$user = $container->user_id;
			if($user!=''){
				$user_details = $this->getUserTable()->getProfileDetails($user);
				if(!empty($user_details)&&isset($user_details->user_id)&&$user_details->user_id!=''){
					if($user_details->user_given_name!=''&&$user_details->user_profile_city_id!=''&&$user_details->user_profile_country_id!=''){
						$checkFbUserData =  $this->getUserTable()->getUser($user_details->user_id);
						$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');					 
						$auth = new AuthenticationService();
						$storage = $auth->getStorage();
						$storage->write($checkFbUserData);					 
						$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						$authNamespace->getManager()->rememberMe(200000);
						return $this->redirect()->toUrl('/register');
					}else{
						$result = new ViewModel(array(
								'user_details' => $user_details,								 
						));		
						return $result;
					}
					 		
				}else{
					return $this->redirect()->toRoute('home', array('action' => 'index'));
				}				
			}else{
				return $this->redirect()->toRoute('home', array('action' => 'index'));
			}
		} 
	}
	public function fbauthAction(){
		$error ='';
		$msg = '';
		$request = $this->getRequest();		 
		$serviceManager = $this->getServiceLocator();
		if ($request->isPost()) {
			$post = $request->getPost();
			$user_given_name = $post['user_given_name']; 
			$user_country_id = $post['user_country_id'];
			$user_city_id = $post['user_city_id'];
			if ($user_given_name=='') {$error = 'Enter your name';}
			$container = new Container('fbUser');
			$user = $container->user_id;
			if($user!=''){
				$user_details = $this->getUserTable()->getProfileDetails($user);
				if(empty($user_details)){	$error = 'Access denied';}
				if($error==''){
					$data['user_given_name'] = $user_given_name;
					$this->getUserTable()->updateUser($data,$user_details->user_id);
					$profile_data['user_profile_country_id'] = $user_country_id;
					$profile_data['user_profile_city_id'] = $user_city_id;
					$this->getUserProfileTable()->updateUserProfile($profile_data,$user_details->user_id);
					$checkFbUserData =  $this->getUserTable()->getUser($user_details->user_id);
					$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');					 
					$auth = new AuthenticationService();
					$storage = $auth->getStorage();
					$storage->write($checkFbUserData);					 
					$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
					$authNamespace->getManager()->rememberMe(200000);
					$container->getManager()->getStorage()->clear('fbUser');
					$headCookie = $this->getRequest()->getHeaders()->get('Cookie');

						if(array_key_exists('cc_data', get_object_vars($headCookie))){
								//$user_id = $headCookie->cc_data;
								$user_id = $user_details->user_id;
						//set cookie
								$cookie = new  \Zend\Http\Header\SetCookie('cc_data',$user_id,time() + 365 * 60 * 60 * 24,'/');
								$this->getResponse()->getHeaders()->addHeader($cookie);
							}else{
								$user_id = $user_details->user_id;
						//set cookie
								$cookie = new  \Zend\Http\Header\SetCookie('cc_data',$user_id,time() + 365 * 60 * 60 * 24,'/');
								$this->getResponse()->getHeaders()->addHeader($cookie);
						} 
					//return $this->redirect()->toRoute('memberprofile', array('member_profile' => $checkFbUserData->user_profile_name));
				}
			}else{
				$error = $serviceManager->get('translator')->translate('Access denied');
			}			 			 	
		}else{$error =$serviceManager->get('translator')->translate('Unable to process');}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = (empty($error))?$msg:$error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function registerAction(){
		$error ='';		
		$domain = '';		 
		$request = $this->getRequest();	 
		$profilename ='';
		$user_data = array();
		$serviceManager = $this->getServiceLocator();
		if ($request->isPost()) {
			$post = $request->getPost();
			if($this->registrationFromValidator($post)){
				$bcrypt = new Bcrypt();
				$password = strip_tags($post['user_password']);
				$password = trim($password);
				$email = strip_tags($post['user_email']);
				$email = trim($email);
				$name = strip_tags($post['user_given_name']);
				$name = trim($name);
				$data['user_password'] = $bcrypt->create($password);
				$user_verification_key = md5('enckey'.rand().time());
				$data['user_verification_key'] = $user_verification_key;
				$data['user_profile_name'] = $this->make_url_friendly($post['user_given_name']);
				$data['user_email'] = $email;
				$data['user_given_name'] = $name;
				$data['user_status'] = "not activated";
				$session = New Container('language');				 
				if(isset($session->language)&&$session->language!=''){
					$db_localeData = $this->getLocalesTable()->checkLocaleBycode($session->language);
					if(!empty($db_localeData)&&$db_localeData->locales_id!=''){
						$data['user_locale'] = $db_localeData->locales_id;
					}
				}
				$profilename =$data['user_profile_name'];
				$user = new User();
				$user->exchangeArray($data);
				$insertedUserId = $this->getUserTable()->saveUser($user);
				if($insertedUserId){
					$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
					$authAdapter = new AuthAdapter($dbAdapter);
					$authAdapter
						->setTableName('y2m_user')
						->setIdentityColumn('user_email')
						->setCredentialColumn('user_password');					
					$authAdapter
						->setIdentity(addslashes($data['user_email']))
						->setCredential($post['user_password']);			
					$result = $authAdapter->authenticate();
					if ($result->isValid()) {					
						$auth = new AuthenticationService();
						$storage = $auth->getStorage();
						$storage->write($authAdapter->getResultRowObject(null,'user_password'));						 
						$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
						$authNamespace->getManager()->rememberMe(200000);
						$user_data = $this->getUserTable()->getProfileDetails($insertedUserId);
						$headCookie = $this->getRequest()->getHeaders()->get('Cookie');

						if(array_key_exists('cc_data', get_object_vars($headCookie))){
								//$user_id = $headCookie->cc_data;
								$user_id = $insertedUserId;
						//set cookie
								$cookie = new  \Zend\Http\Header\SetCookie('cc_data',$insertedUserId,time() + 365 * 60 * 60 * 24,'/');
								$this->getResponse()->getHeaders()->addHeader($cookie);
							}else{
								$user_id = $insertedUserId;
						//set cookie
								$cookie = new  \Zend\Http\Header\SetCookie('cc_data',$insertedUserId,time() + 365 * 60 * 60 * 24,'/');
								$this->getResponse()->getHeaders()->addHeader($cookie);
						} 
					}					
					$profile_data['user_profile_user_id'] = $insertedUserId;
					$profile_data['user_profile_country_id'] = strip_tags($post['user_country_id']);
					$profile_data['user_profile_city_id'] = strip_tags($post['user_city_id']);
					$profile_data['user_profile_status'] = "available";					 
					$profile_data['user_profile_notifyme_id'] = "";
					$profile_data['user_profile_emailme_id'] = "";					 
					$userProfile = new UserProfile();
					$userProfile->exchangeArray($profile_data);
					$insertedUserProfileId = $this->getUserProfileTable()->saveUserProfile($userProfile);					 
					$allowed = array('yahoomail.com', 'gmail.com', 'hotmail.com');
					$domain_ar1 = explode('@', $email);
					$domain_ar = array_pop($domain_ar1);
					if (in_array($domain, $allowed))
					{
						$domain =  $domain_ar;
					}
					//$this->sendVerificationEmail($user_verification_key,$insertedUserId,$data['user_email'],$post['user_given_name']);
				}else{
					$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again");
				}
			}else{
				$error = $this->form_error;
			}
		}else{
				$error = $serviceManager->get('translator')->translate("Unable to process");
		}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['mail_domain'] = $domain;	
		$return_array['profilename'] = $profilename;
		$return_array['profile_data'] = $user_data;		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;		
	}
	public function registrationFromValidator($form){
		$user_given_name = strip_tags($form['user_given_name']);
		$user_email  = strip_tags($form['user_email']);
		$user_password = strip_tags($form['user_password']);
		$serviceManager = $this->getServiceLocator();
		if(empty($user_given_name)||empty($user_email)||empty($user_password)){
			$this->form_error = 'Name email and password are required';
			return false;
		}
		if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
			$this->form_error = $serviceManager->get('translator')->translate('Please enter a valid email address');
			return false;
		}
		if (strlen($user_password)<5) {
			$this->form_error = $serviceManager->get('translator')->translate('Password must contain more than  5 characters');
			return false;
		}
		$user_data =  $this->getUserTable()->getUserFromEmail($user_email);
		if(!empty($user_data)){
			$this->form_error = $serviceManager->get('translator')->translate('Email you are entered is already exist');
			return false;
		}
		return true;
	}
	public function make_url_friendly($string)
	{
		$string = trim($string); 
		$string = preg_replace('/(\W\B)/', '',  $string); 
		$string = preg_replace('/[\W]+/',  '_', $string); 
		$string = str_replace('-', '_', $string);
		if(!$this->checkProfileNameExist($string)){
			return $string; 
		}
		$length = 5;
		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$string = strtolower($string).'_'.$randomString;
		if(!$this->checkProfileNameExist($string)){
			return $string; 
		} 
		$string = strtolower($string).'_'.time(); 
		return $string; 
	}
	public function checkProfileNameExist($string){
		if($this->getUserTable()->checkProfileNameExist($string)){
			return true;
		}else{
			return false;
		}
	}
	public function sendVerificationEmail($user_verification_key,$insertedUserId,$emailId,$name){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');	 
		$user_insertedUserId = md5(md5('userId~'.$insertedUserId));
		$body = $this->renderer->render('user/email/emailVarification.phtml', array('user_verification_key'=>$user_verification_key,'user_insertedUserId'=>$user_insertedUserId,'name'=>$name));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom('admin@jeera.me');
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject("Registration confirmation");
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function ajaxLoginAction(){
		$error ='';	
		$form = new Login();
		$request = $this->getRequest();
		$activate_user = '';
		$serviceManager = $this->getServiceLocator();
		if ($request->isPost()) {
			$form->setInputFilter(new LoginFilter());			
			$form->setData($request->getPost());
			if ($form->isValid()) { 
				$data = $request->getPost();				
				$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
				$authAdapter = new AuthAdapter($dbAdapter);
				$authAdapter
					->setTableName('y2m_user')
					->setIdentityColumn('user_email')
					->setCredentialColumn('user_password');					
				$authAdapter
					->setIdentity(addslashes($data['user_email']))
					->setCredential($data['user_password']);			
				$result = $authAdapter->authenticate(); 
				if (!$result->isValid()) {						 
					$error = $serviceManager->get('translator')->translate('Invalid Email or Password');					 
				} else {
					$user_details = $user_data= $this->getUserTable()->getUserFromEmail($data['user_email']);
					if($user_details->user_status == 'suspend'){
						$error = $serviceManager->get('translator')->translate('Your account has been suspended. Please contact Jeera administrator to activate it');
					}else if($user_details->user_status == 'block'){
						$error = $serviceManager->get('translator')->translate('Your account has been blocked. Please contact Jeera administrator  to activate it');
					}
					else if($user_details->user_status == 'delete'){
						$error = $serviceManager->get('translator')->translate('Account does not exist');
					}else{
						$auth = new AuthenticationService();
						$storage = $auth->getStorage();
						$storage->write($authAdapter->getResultRowObject(null,'user_password'));
						//get cookie
						$headCookie = $this->getRequest()->getHeaders()->get('Cookie');

						if(array_key_exists('cc_data', get_object_vars($headCookie))){
								//$user_id = $headCookie->cc_data;
								$user_id = $user_details->user_id;
						//set cookie
								$cookie = new  \Zend\Http\Header\SetCookie('cc_data',$user_id,time() + 365 * 60 * 60 * 24,'/');
								$this->getResponse()->getHeaders()->addHeader($cookie);
							}else{
								$user_id = $user_details->user_id;
						//set cookie
								$cookie = new  \Zend\Http\Header\SetCookie('cc_data',$user_id,time() + 365 * 60 * 60 * 24,'/');
								$this->getResponse()->getHeaders()->addHeader($cookie);
						}
						if(!empty($user_details->user_locale)){
							$localedata = $this->getLocalesTable()->getLocales($user_details->user_locale);
							$session = New Container('language');							
							$translator = $serviceManager->get('translator');
							if(!empty($localedata)&&$localedata->locales_code!=''){
								$session->language =  $localedata->locales_code;
							}						
							$translator
								->setLocale($session->language)
								->setFallbackLocale('en_US');
						}
						if($this->params()->fromPost('rememberme')){ 
							$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
							$authNamespace->getManager()->rememberMe(200000);
						} else{
							$authNamespace = new Container(Session::NAMESPACE_DEFAULT);
							$authNamespace->getManager()->rememberMe(20000);
						}	
					}
				}				
			}else{
				$validation_msg = $form->getMessages();
				if(isset($validation_msg['user_email']['isEmpty'])&&$validation_msg['user_email']['isEmpty']!=''){
					$error = $validation_msg['user_email']['isEmpty'];
				}
				else if(isset($validation_msg['user_password']['isEmpty'])&&$validation_msg['user_password']['isEmpty']!=''){
					$error = $validation_msg['user_password']['isEmpty'];
				}
				else if(isset($validation_msg['user_email']['emailAddressInvalidHostname'])&&$validation_msg['user_email']['emailAddressInvalidHostname']!=''){
					$error = $validation_msg['user_email']['emailAddressInvalidHostname'];
				}
				else if(isset($validation_msg['user_email']['hostnameUnknownTld'])&&$validation_msg['user_email']['hostnameUnknownTld']!=''){
					$error = $validation_msg['user_email']['hostnameUnknownTld'];
				}
				else if(isset($validation_msg['user_email']['hostnameLocalNameNotAllowed'])&&$validation_msg['user_email']['hostnameLocalNameNotAllowed']!=''){
					$error = $validation_msg['user_email']['hostnameLocalNameNotAllowed'];
				}
				else{
					$error =  $serviceManager->get('translator')->translate("Error occurred. PLease try again");
				}
			}
		}else{
			$error =$serviceManager->get('translator')->translate('Unable to process');	
		}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;
		$return_array['process_user_status'] = $activate_user; 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function resendverificationAction(){
		$error ='';
		$msg = '';
		$request = $this->getRequest();
		$serviceManager = $this->getServiceLocator();
		if ($request->isPost()) {
			$user_email = $this->params()->fromPost('user_email');
			if(!empty($user_email)){
				$user_details = $this->getUserTable()->getUserFromEmail($this->params()->fromPost('user_email'));
				if(!empty($user_details)){
					if($user_details->user_status!='not activated'){ $error =$serviceManager->get('translator')->translate('This account is activated already');}else{
						$user_verification_key = md5('enckey'.rand().time());						 
						$data['user_verification_key'] = $user_verification_key;
						$this->getUserTable()->updateUser($data,$user_details->user_id);						
						$this->sendVerificationEmail($user_verification_key,$user_details->user_id,$user_details->user_email,$user_details->user_given_name);
						$msg = $serviceManager->get('translator')->translate('Verification code is sent to your email. Please check your email and follow the instructions');
					}
				}else{$error =$serviceManager->get('translator')->translate('There are no records in this system with e-mail ID given'); }
			}else{$error =$serviceManager->get('translator')->translate('There are no records in this system with e-mail ID given'); }
		}else{ $error =$serviceManager->get('translator')->translate('Unable to process');}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = (empty($error))?$msg:$error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function checkUserStatusAction(){
		$error ='';
		$request = $this->getRequest();
		$user_status = "active";
		$msg ='';
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($request->isPost()) {
			if($auth->hasIdentity()) {
				$identity = $auth->getIdentity();
				$myinfo = $this->getUserTable()->getUser($identity->user_id);
				if($myinfo->user_status=="not activated"){
					$user_status = "not active";
				}
			}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}				
		}else{ $error =$serviceManager->get('translator')->translate('Unable to process');}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['user_status'] = $user_status;
		$return_array['process_info'] = (empty($error))?$msg:$error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function varifyemailAction(){
		$error = '';
		$email = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$key = $this->getEvent()->getRouteMatch()->getParam('key');  
		$id = $this->getEvent()->getRouteMatch()->getParam('id');	 
		if($key!=''&&$id!=''){		
			$user_id = $this->getUserTable()->checkUserVarification($key,$id);
			if($user_id){
				$data = array('user_status'=>"live");
				$this->getUserTable()->updateUser($data,$user_id);
				if ($auth->hasIdentity()) {
					$userinfo = $this->getUserTable()->getUser($user_id);
					$storage = $auth->getStorage();
					$storage->write($userinfo);
				}
				$this->flashMessenger()->addMessage('Your account is verified! Enjoy exploring Jeera!');
				return $this->redirect()->toRoute('home', array('action' => 'index'));	
			}else{$error = $serviceManager->get('translator')->translate('Verification code that you are entered is not valid');}			
		}else{$error =$serviceManager->get('translator')->translate('Unable to process');} 		
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
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
			$email = $identity->user_email;
		}
		$viewModel->setVariable('email',$email);
		$viewModel->setVariable( 'message', $error);
		return $viewModel;
	}
	public function forgotPasswordAction(){
		$error ='';
		$msg = '';
		$request = $this->getRequest();	
		$serviceManager = $this->getServiceLocator();
		if ($request->isPost()) {
			$post = $request->getPost();
			$user_email = $post['user_email']; 
			if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {$error = $serviceManager->get('translator')->translate('Please enter a valid email address');}
			$user_data =  $this->getUserTable()->getUserFromEmail($user_email); 
			if(empty($user_data)){	$error = $serviceManager->get('translator')->translate('The email you entered does not exist in our system, kindly register as a new user');}
			if($error==''){
				$data['user_id'] = $user_data->user_id;
				$secret_code = time().rand();
				$data['secret_code'] = $secret_code;
				$data['user_email'] = $user_data->user_email;
				$data['status'] = 0;
				$recoveremails = new Recoveryemails();
				$recoveremails->exchangeArray($data);
				$this->getRecoveremailsTable()->ResetAllActiveRequests($user_data->user_id);
				$insertedRecoveryId = $this->getRecoveremailsTable()->saveRecovery($recoveremails);
				if($insertedRecoveryId){
					$this->sendPasswordResetMail($secret_code,$insertedRecoveryId,$user_data->user_email);
					$msg = $serviceManager->get('translator')->translate("The password reset steps has been sent to your mail, please follow the instructions to reset it");
				}
			}		
		}else{$error =$serviceManager->get('translator')->translate('Unable to process');}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = (empty($error))?$msg:$error;		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;		 
	}
	public function resetpasswordAction(){		   
		$key = $this->getEvent()->getRouteMatch()->getParam('key');  
		$id = $this->getEvent()->getRouteMatch()->getParam('id');		 
		$vm = new ViewModel();
		if($key!=''&&$id!=''){	
			$request_record = new Recoveryemails();
			$request_record = $this->getRecoveremailsTable()->checkResetRequest($key,$id);			 
			if($request_record){
				 if($request_record->status){ $this->flashmessenger()->addMessage("This option is expired for you");}
				 else if(md5(md5('recoverid~'.$request_record->id))!=$id){$this->flashmessenger()->addMessage("Varification code that you are entered is not valid"); }
				 else{
					$current_date = strtotime(date("Y-m-d"));
					$expiry_date = strtotime(date("Y-m-d", strtotime($request_record->senddate)) . " +1 day");
					if($current_date>$expiry_date){
						$data['status'] = 1;
						$request_record = $this->getRecoveremailsTable()->updateRecovery($data,$request_record->id);						 
						$this->flashmessenger()->addMessage("This option is expired for you");						 
					}else{
						$user_id = $request_record->user_id;
						$form = new ResetPassword();
						$request = $this->getRequest();
						$recoveremails = new Recoveryemails();
						if ($request->isPost()) {
							$form->setInputFilter($recoveremails->getResetPasswordFilter());
							$form->setData($request->getPost());
							if($form->isValid()){
								$userdata  = $form->getData();
								$bcrypt = new Bcrypt();
								$data['user_password'] = $bcrypt->create($userdata['user_password']);
								if($this->getUserTable()->updateUser($data,$user_id)){
									$this->flashmessenger()->addMessage("Successfully reset your password");
									$data_expry['status'] = 1;
									$request_record = $this->getRecoveremailsTable()->updateRecovery($data_expry,$request_record->id);
									return $this->redirect()->toRoute('home', array('action' => 'index'));	
								}else{$this->flashmessenger()->addMessage("Some error occurred.Please try again");}
							}							
						}						 
						$vm->setVariable('key', $key);
						$vm->setVariable('id', $id);						 
						$vm->setVariable('form', $form);						
					}
				 }
			}else{$this->flashmessenger()->addMessage("Verification code that you have entered is not valid");}			
		}else{$this->flashmessenger()->addMessage("Unauthorized access");	}		 
		$vm->setVariable('flashMessages', $this->flashMessenger()->getMessages());
		return $vm; 
	}
	public function registerstepAction(){
		$vm = new ViewModel();
		$auth = new AuthenticationService();
		$auth = new AuthenticationService();
		$this->layout('layout/layout_register');
		$session = New Container('language');
		$serviceManager = $this->getServiceLocator();
		$locales_selected = (isset($session->language))?$session->language:''; 
		$vm->setVariable('locales_selected', $locales_selected);	
		$this->layout()->locales_selected = $locales_selected;
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$userTags = $this->getUserTagTable()->getAllUserTagCategiry($identity->user_id);
			if(!empty($userTags)){
				return $this->redirect()->toRoute('memberprofile', array('member_profile' => $identity->user_profile_name));
			}else{
				$cities = $this->getCityTable()->selectAllCityWithCountry();
				foreach($cities as $index => $citylist){
					$tempcites = explode(",", $citylist['city_name']);
					$arr_cities[0] = array();
					foreach($tempcites as $indexes => $splitlist){
						$arr_cities = array();
						$arr_cities = explode("|", $splitlist);
						$objarr_city[] = array('city_id'=>$arr_cities[0],'city_name'=>$arr_cities[1],'city_locale'=>$serviceManager->get('translator')->translate($arr_cities[1]));
					
					}
					$loadcitiescountries[] = array(
						'country_id' =>$citylist['country_id'],
						'country_title' =>$citylist['country_title'],
						'country_code' =>$citylist['country_code'],
						'country_locale' =>$serviceManager->get('translator')->translate($citylist['country_title']),
						'cities' =>$objarr_city,
						);
					unset($objarr_city);
				}
				$vm->setVariable('country', $loadcitiescountries);
				$categories = $this->getTagCategoryTable()->getActiveCategories();				
				$arr_categories = array();
				foreach($categories  as $list){
					$arr_categories[]= array(
											'tag_category_id'=>$list['tag_category_id'],
											'tag_category_title'=>$list['tag_category_title'],
											'tag_category_locale'=>$serviceManager->get('translator')->translate($list['tag_category_title']),
											'tag_category_icon'=>$list['tag_category_icon'],
											'tag_category_desc'=>$list['tag_category_desc'],
											'tag_category_status'=>$list['tag_category_status'],
										);
				}
				$vm->setVariable('categories', $arr_categories);
				$config = $this->getServiceLocator()->get('Config');
				$vm->setVariable('image_folders',$config['image_folders']);
				$vm->setVariable('register_step', 4);
				$user_data = $this->getUserTable()->getProfileDetails($identity->user_id);
				$vm->setVariable('profile_data', $user_data);				
			}			
		}else{
			$cities = $this->getCityTable()->selectAllCityWithCountry();
			foreach($cities as $index => $citylist){
				$tempcites = explode(",", $citylist['city_name']);
				$arr_cities[0] = array();
				foreach($tempcites as $indexes => $splitlist){
					$arr_cities = array();
					$arr_cities = explode("|", $splitlist);
					$objarr_city[] = array('city_id'=>$arr_cities[0],'city_name'=>$arr_cities[1],'city_locale'=>$serviceManager->get('translator')->translate($arr_cities[1]));
				
				}
				$loadcitiescountries[] = array(
					'country_id' =>$citylist['country_id'],
					'country_title' =>$citylist['country_title'],
					'country_code' =>$citylist['country_code'],
					'country_locale' =>$serviceManager->get('translator')->translate($citylist['country_title']),
					'cities' =>$objarr_city,
					);
				unset($objarr_city);
			}
			$vm->setVariable('country', $loadcitiescountries);
			$categories = $this->getTagCategoryTable()->getActiveCategories();
			$arr_categories = array();
			foreach($categories  as $list){
				$arr_categories[]= array(
										'tag_category_id'=>$list['tag_category_id'],
										'tag_category_title'=>$list['tag_category_title'],
										'tag_category_locale'=>$serviceManager->get('translator')->translate($list['tag_category_title']),
										'tag_category_icon'=>$list['tag_category_icon'],
										'tag_category_desc'=>$list['tag_category_desc'],
										'tag_category_status'=>$list['tag_category_status'],
									);
			}
			$vm->setVariable('categories', $arr_categories);
			$config = $this->getServiceLocator()->get('Config');
			$vm->setVariable('image_folders',$config['image_folders']);
			$vm->setVariable('register_step', 1);
			$user_data = array();
			$vm->setVariable('profile_data', $user_data);	
			
		}
		return $vm; 
	}
	public function emailExistAction(){
		$request   = $this->getRequest();
		$serviceManager = $this->getServiceLocator();
		$error ='';
		if ($request->isPost()){
			$post = $request->getPost();
			$email = $post['email'];
			if($email==''){
				$error = $serviceManager->get('translator')->translate("Enter email address");
			}else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$error = $serviceManager->get('translator')->translate("Enter a valid email address");
			}else{
				$user_data =  $this->getUserTable()->getUserFromEmail($email);
				if(!empty($user_data)){
					$error = $serviceManager->get('translator')->translate('Email you have entered already exists');
				}
			}
		}else{	$error = $serviceManager->get('translator')->translate("Unable to process");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;			 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));	
		return $result;
	}
	public function sendPasswordResetMail($user_verification_key,$insertedRecoveryid,$emailId){
		$this->renderer = $this->getServiceLocator()->get('ViewRenderer');	 
		$user_recoverId = md5(md5('recoverid~'.$insertedRecoveryid));
		$body = $this->renderer->render('user/email/emailResetPassword.phtml', array('user_verification_key'=>$user_verification_key,'user_recoverId'=>$user_recoverId));
		$htmlPart = new MimePart($body);
		$htmlPart->type = "text/html";

		$textPart = new MimePart($body);
		$textPart->type = "text/plain";

		$body = new MimeMessage();
		$body->setParts(array($textPart, $htmlPart));

		$message = new Mail\Message();
		$message->setFrom('admin@jeera.me');
		$message->addTo($emailId);
		//$message->addReplyTo($reply);							 
		$message->setSender("Jeera");
		$message->setSubject("Reset password request");
		$message->setEncoding("UTF-8");
		$message->setBody($body);
		$message->getHeaders()->get('content-type')->setType('multipart/alternative');

		$transport = new Mail\Transport\Sendmail();
		$transport->send($message);
		return true;
	}
	public function logoutAction(){
		//$user = $this->facebook->getUser();     
		$auth = new AuthenticationService();	
	  	$auth->clearIdentity();
		unset($_SESSION);	  
		$this->flashmessenger()->addMessage("You've been logged out");
		
		$cookie= $this->getRequest()->getCookie();
		if ($cookie->offsetExists('cc_data')) { 
		$cookie = new  \Zend\Http\Header\SetCookie('cc_data','',time() - 365 * 60 * 60 * 24,'/');
		$this->getResponse()->getHeaders()->addHeader($cookie);
		//$new_cookie=  new  \Zend\Http\Header\SetCookie('cc_data', '');//<---empty value and the same 'name'
		//$new_cookie->setExpires(-(time() + 365 * 60 * 60 * 24));
		//$this->getResponse()->getHeaders()->addHeader($new_cookie);
		}
		//if($user){
         // $logoutUrl = $this->facebook->getLogoutUrl();
        //  $this->redirect()->toUrl($logoutUrl);
       //}
	    setcookie("cc_data", "", time()-3600);
		return $this->redirect()->toRoute('home', array('action' => 'index'));			
    }
	public function checkUserActive($email){
		$user_data= $this->getUserTable()->getUserFromEmail($email);
		if(empty($user_data)){
			return "not exist";
		}elseif(!empty($user_data)&&$user_data->user_status !="live"){
			return "not activated";
		}else{return "active";}	
	}
	public function getUserTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTable = (!$this->userTable)?$sm->get('User\Model\UserTable'):$this->userTable;    
	}
	public function getUserProfileTable(){
		$sm = $this->getServiceLocator();
		return  $this->userProfileTable = (!$this->userProfileTable)?$sm->get('User\Model\UserProfileTable'):$this->userProfileTable;    
	}
	public function getRecoveremailsTable(){
		$sm = $this->getServiceLocator();
		return $this->RecoveryemailsTable =(!$this->RecoveryemailsTable)?$sm->get('User\Model\RecoveryemailsTable'):$this->RecoveryemailsTable;
	}
	public function getCityTable()
    {
		$sm = $this->getServiceLocator();       
		return $this->cityTable =(!$this->cityTable)? $this->cityTable = $sm->get('City\Model\CityTable'):$this->cityTable; 
    }
	public function getTagCategoryTable()
    {
		$sm = $this->getServiceLocator();       
		return $this->tagCategoryTable =(!$this->tagCategoryTable)? $this->tagCategoryTable = $sm->get('Tag\Model\TagCategoryTable'):$this->tagCategoryTable; 
    }
	public function getUserTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTagTable = (!$this->userTagTable)?$sm->get('Tag\Model\UserTagTable'):$this->userTagTable;    
	}
	public function getLocalesTable(){
		$sm = $this->getServiceLocator();
		return  $this->localesTable = (!$this->localesTable)?$sm->get('Admin\Model\LocalesTable'):$this->localesTable;
    }
}
