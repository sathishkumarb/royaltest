<?php 
namespace Product\Controller;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;	//Return model 
use Zend\View\Model\JsonModel;
use Groups\Model\Groups; 
use User\Model\User; 

use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Session\Container; 
class ProductController extends AbstractActionController
{
	protected $userTable;
	protected $userFriendTable;
	protected $groupTable;
	protected $userGroupTable;

	public function creategroupAction(){
		$error = '';
		$auth = new AuthenticationService();
		$seotitle = '';
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($myinfo)&&$myinfo->user_id){
				$request   = $this->getRequest();
				if ($request->isPost()){					 
					$objGroup = new Groups();
					$objGroup->strGroupName = trim($this->getRequest()->getPost('strGroupName'));
					$objGroup->intCountryId = trim($this->getRequest()->getPost('intCountryId'));
					$objGroup->intCityId = trim($this->getRequest()->getPost('intCityId'));
					$objGroup->strDesp = trim($this->getRequest()->getPost('strDesp'));
					$intGroupType = trim($this->getRequest()->getPost('intGroupType')); 
					 switch ($intGroupType){
					  case 1:
					  $objGroup->intGroupType = 'open';
					  break;
					  case 2:
					  $objGroup->intGroupType = 'public';
					  break;
					  case 3:
					  $objGroup->intGroupType = 'private';
					  break;
					  default:
					   $objGroup->intGroupType = 'open';
					 }
					
					$arrgrouptags 		= explode(',',$this->getRequest()->getPost('addedtags'));
					$arrQuestions       = explode(',',$this->getRequest()->getPost('QuestionDetails'));
                    $arrAddedFriends    = explode(',',$this->getRequest()->getPost('addedFriends'));
					$que = 1;
                   // echo count($arrQuestions);
                   // print_r($arrQuestions);
				   $erro_count = 0;
                    if($arrQuestions[0] != ''){
                        foreach($arrQuestions as $question){
                              $arrQuestionDetail  = explode('##',$question);
                              if($arrQuestionDetail[1] == ''){
                                  $erro_count++;
                                  $error = $serviceManager->get('translator')->translate("Please enter question").$que;
                                  break;
                              }else{
								if($arrQuestionDetail[0] != 'Textarea'){
									$options = array();
									for($i=2;$i<count($arrQuestionDetail);$i++){
										$options[] = $arrQuestionDetail[$i];
									}									 
                                    if(count($options)<2){
                                        $erro_count++;
                                        $error = $serviceManager->get('translator')->translate("Please enter atleast two options for question").$que."";
                                        break;
                                    }                                     
                                }// else
                              } // if
                              $que++;
                         } // foreach
                    }// if					
					
					$erro_count =($objGroup->strGroupName == '')?$erro_count++:$erro_count;
					$erro_count =($objGroup->strDesp == '')?$erro_count++:$erro_count;
					$erro_count =(count($arrgrouptags)<=0)?$erro_count++:$erro_count;
					if(count($arrgrouptags)<=0){
						$erro_count++;
						$error = $serviceManager->get('translator')->translate("Please select atleast one interest");
					}
					if($objGroup->strDesp == ''){
						$erro_count++;
						$error = $serviceManager->get('translator')->translate("Group description is required");
					}
					if($objGroup->strGroupName == ''){
						$erro_count++;
						$error = $serviceManager->get('translator')->translate("Group title is required");
					}					
					$group_info = $this->getGroupTable()->getGroupByName($objGroup->strGroupName);
					if(!empty($group_info)&& $group_info->group_id!=''){
						$erro_count++;
						$error = $serviceManager->get('translator')->translate("This group name is already taken");
					}
					if($erro_count == 0){
						$objGroup->group_seo_title = $this->creatSeotitle($objGroup->strGroupName);
						$seotitle = $objGroup->group_seo_title;
						$intGroupId = $this->getGroupTable()->saveGroupBasicDetails($objGroup, '');
						$config = $this->getServiceLocator()->get('Config');
						$temp_path = $config['pathInfo']['temppath'];
						$user_temp_path = $temp_path.$identity->user_id."/";	
						$files = glob($user_temp_path); 
						foreach($files as $file){ 
							if(is_file($file))
							@unlink($file);  
						}
						if($intGroupId){
							$userGroup = new UserGroup();
							$userGroup->user_group_user_id              = $identity->user_id;
							$userGroup->user_group_group_id             = $intGroupId;
							$userGroup->user_group_added_timestamp      = '';
							$userGroup->user_group_added_ip_address     = $_SERVER["SERVER_ADDR"];
							$userGroup->user_group_status               = 'available';
							$userGroup->user_group_is_owner             = 1;
							$intU_GroupId                               = $this->getUserGroupTable()->saveUserGroup($userGroup);
							if(isset($_FILES)&&isset($_FILES['groupImage']['name'])&&$_FILES['groupImage']['name']!=''){ 
								$config = $this->getServiceLocator()->get('Config');
								$options['script_url']          = $config['pathInfo']['base_url'];
								$options['upload_dir']          = $config['pathInfo']['group_img_path'].$intGroupId."/";
								$options['upload_url']          = $config['pathInfo']['group_img_path_absolute_path'].$intGroupId."/";
								$options['param_name']          = 'groupImage';
								$options['min_width']           = 100;
								$options['min_height']          = 100;
								
								// object of file upload plug in which is used for simple upload as well as drag and drop upload functionality
								$upload_handler = new UploadHandler($options); 
								if(isset($upload_handler->image_objects['filename'])&&$upload_handler->image_objects['filename']!=''){
									$groupphoto  = new GroupPhoto();
									$groupphoto->group_photo_group_id  = $intGroupId;
									$groupphoto->group_photo_photo = $upload_handler->image_objects['filename'];
									$groupphoto->group_photo_orginal = $upload_handler->image_objects['filename'];
									$intGroupPhotoId  = $this->getGroupPhotoTable()->savePhoto($groupphoto);
								}
							}
							$grouptag   = new GroupTag();
							foreach($arrgrouptags as $group_tag){
								$grouptag->group_tag_group_id           = $intGroupId;
								$grouptag->group_tag_added_ip_address   = $_SERVER["SERVER_ADDR"];
								$grouptag->group_tag_tag_id             = $group_tag;
								$intGrouptagId                          = $this->getGroupTagTable()->saveGroupTag($grouptag);
							}
							$UserGroupJoiningInvitation                 = new UserGroupJoiningInvitation();
                            if($arrAddedFriends[0] != ''){
                                foreach($arrAddedFriends as $group_invt){
                                    $UserGroupJoiningInvitation->user_group_joining_invitation_sender_user_id           = $identity->user_id;
                                    $UserGroupJoiningInvitation->user_group_joining_invitation_receiver_id              = $group_invt;
                                    $UserGroupJoiningInvitation->user_group_joining_invitation_status                   = "active";
                                    $UserGroupJoiningInvitation->user_group_joining_invitation_ip_address               = $_SERVER["SERVER_ADDR"];
                                    $UserGroupJoiningInvitation->user_group_joining_invitation_group_id                 = $intGroupId;
                                    $intUserGroupJoiningInvitation   = $this->getGroupJoiningInvitationTable()->saveUserGroupJoiningInvite($UserGroupJoiningInvitation);
									if( $intUserGroupJoiningInvitation){
										$config = $this->getServiceLocator()->get('Config');
										$base_url = $config['pathInfo']['base_url'];
										$msg = $identity->user_given_name." invited you to join the group ".$objGroup->strGroupName;
										$subject = 'Group joining invitation';
										$from = 'admin@jeera.me';
										$process = 'Invite';
										$this->UpdateNotifications($group_invt,$msg,3,$subject,$from,$identity->user_id,$intGroupId,$process);
									}
                                }
                            }

                           // adding the question
                           if($arrQuestions[0] != ''){
                                foreach($arrQuestions as $question){
									$arrQuestionDetail = array();
                                    $arrQuestionDetail  = explode('##',$question);
									$answer_type = 'Textarea';
									$answer_type =($arrQuestionDetail[0] == 'CheckBox')?'checkbox':$answer_type;
									$answer_type =($arrQuestionDetail[0] == 'Radiobutton')?'radio':$answer_type;
                                    $addedQuestion      = array(
                                            'group_id'            => $intGroupId,
                                            'question'            => $arrQuestionDetail[1],
                                            'question_status'     => 'active',
                                            'added_ip'            => $_SERVER["SERVER_ADDR"],
                                            'added_user_id'       => $identity->user_id,
                                            'answer_type'         => $answer_type
                                       );
                                    // save question
                                    $intQuestionId                      = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);

                                    for($o=2; $o<count($arrQuestionDetail); $o++){
                                        if($arrQuestionDetail[$o]!= ''){
                                            //getGroupQuestionnaireOptionsTable
                                            $addedOption    = array(
                                                'question_id'   => $intQuestionId,
                                                'option'        => $arrQuestionDetail[$o]
                                            );
                                            $intQOptionId                      = $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
                                        }
                                    }// for
                                }// foreach
                           }//if
						}else{$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again");}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['seotitle'] = $seotitle;			
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	
	public function getAllActiveMembersExceptMeAction(){
		$error = '';
		$auth = new AuthenticationService();
		$allActiveMembers = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$myinfo = $this->getUserTable()->getUser($identity->user_id);
			if(!empty($myinfo)&&$myinfo->user_id){
				$request   = $this->getRequest();
				if ($request->isPost()){
					$post = $request->getPost();
					$error =($post['group_id']=='')? $serviceManager->get('translator')->translate("Select one group"):$error;	 
					$group  = $this->getGroupTable()->getPlanetinfo($post['group_id']);
					$error =(empty($group)||$group->group_id=='')?$serviceManager->get('translator')->translate("Given group not exist in this system"):$error;	
					$allActiveMembers = $this->getUserGroupTable()->getAllActiveMembersExceptMeAction($group->group_id,$identity->user_id);
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{	$error = $serviceManager->get('translator')->translate("User not exist in the system"); }
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['members'] = $allActiveMembers;		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getUserFriendsAction(){

        $error          = '';
		$auth           = new AuthenticationService();
		$identity       = $auth->getIdentity();

        $friendsList    = $this->getUserFriendTable()->fetchAllUserFriend($identity->user_id);
        //print_r($friendsList->toArray());
        $result = new JsonModel(array( 'friendsList' => $friendsList->toArray()));
		return $result;
    }
	public function getMediaAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arr_group_media = array();
		$allActiveMembers = array();
		$comments = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();				 
				$item_id =$post['item_id']; 
				if($item_id){
					$group_media = $this->getGroupMediaTable()->getMediaFromContent($item_id);
					$group_oneMedia = $this->getGroupMediaTable()->getOneMedia($group_media->group_media_id);
					$is_admin = 0;
					if($this->getUserGroupTable()->checkOwner($group_media->media_added_group_id,$group_media->media_added_user_id)){
						$is_admin = 1;
					}
					$logged_user_ismember = 0;
					if($this->getUserGroupTable()->is_member($identity->user_id,$group_media->media_added_group_id)){
						$logged_user_ismember = 1;
					}
					$arr_group_media = array();
					if(!empty($group_media)&&$group_media->group_media_id!=''){
						$SystemTypeData = $this->getGroupTable()->fetchSystemType("Image");
						$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$item_id,$identity->user_id);
						$like_count = $like_details->likes_counts;		
						$arr_likedUsers = array();						
						if(!empty($like_details)){  
							$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$item_id,$identity->user_id,2,0);
							$arr_likedUsers = array();
							if($like_details['is_liked']==1){
								$arr_likedUsers[] = $serviceManager->get('translator')->translate("you");
							}
							if($like_details['likes_counts']>0&&!empty($liked_users)){
								foreach($liked_users as $likeuser){
									$arr_likedUsers[] = $likeuser['user_given_name'];
								}
							}							 
						}
						$commet_users = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$item_id,$identity->user_id);
						$media_file = $this->getGroupMediaContentTable()->getMediafile($item_id);
						$event_name = '';
						$event_id = '';
						$allMediafiles = array();
						$is_album_creator = 0;
						if($group_media->media_album_id!=''){
							$event_details  = $this->getGroupEventAlbumTable()->getAlbumEvents($group_media->media_album_id);
							if(!empty($event_details)){
								$event_name = $event_details->group_activity_title;
								$event_id = $event_details->group_activity_id;
							}
							$allAlbum_files = $this->getGroupMediaTable()->getAllAlbumFiles($group_media->media_album_id);
							foreach($allAlbum_files as $files){
								$media_ids = json_decode($files['media_content']);
								$allMediafiles = array_merge($allMediafiles,$media_ids );
							}
						}else{
							$all_files = $this->getGroupMediaTable()->getGroupFiles($group_media->media_added_group_id);
							foreach($all_files as $files){
								$media_ids = json_decode($files['media_content']);
								$allMediafiles = array_merge($allMediafiles,$media_ids );
							}
						}
						
						$arr_group_media = array(
										'media_type' =>$media_file->media_type,
										'group_media_id' => $group_media->group_media_id,
										'media_content' => json_decode($group_media->media_content),
										'media_caption' => $group_media->media_caption,
										'added_time' =>$this->timeAgo($group_media->media_added_date),
										'group_id' => $group_oneMedia->group_id,
										'group_title' => $group_oneMedia->group_title,
										'group_seo_title' => $group_oneMedia->group_seo_title,
										'user_id' => $group_oneMedia->user_id,
										'user_given_name' => $group_oneMedia->user_given_name,
										'user_first_name' => $group_oneMedia->user_first_name,
										'user_last_name' => $group_oneMedia->user_last_name,
										'user_profile_name' => $group_oneMedia->user_profile_name,
										'user_fbid' => $group_oneMedia->user_fbid,
										'profile_photo' => $group_oneMedia->profile_photo,
										'likedUsers' => $arr_likedUsers,
										'likes_counts' =>$like_details['likes_counts'],
										'is_liked' =>$like_details['is_liked'],
										'comment_count' =>$commet_users['comment_counts'],
										'is_commented' =>$commet_users['is_commented'],
										'is_admin'=>$is_admin,
										'logged_user_ismember' =>$logged_user_ismember,
										'album_name'=>$group_oneMedia->album_title,
										'album_id'=>$group_oneMedia->album_id,
										'media_file_id'=>$media_file->media_content_id,
										'media_file'=>$media_file->content,
										'event_name'=>$event_name,
										'event_id'=>$event_id,
										'allMediafiles'=>$allMediafiles,
										'is_added'=>($group_media->media_added_user_id==$identity->user_id)?1:0,
										'is_album_creator'=>($group_oneMedia->creator_id ==$identity->user_id)?1:0 ,
										);
						$commentSystemTYpe =$this->getGroupTable()->fetchSystemType('Comment'); 
						$comments_details = $this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$commentSystemTYpe->system_type_id,$item_id,$identity->user_id,10,0);
						if(!empty($comments_details)){
							foreach($comments_details as $list){
								 
								$arr_likedUsers = array();
								$like_details = $this->getLikeTable()->fetchLikesCountByReference($commentSystemTYpe->system_type_id,$list->comment_id,$identity->user_id);
								if(!empty($like_details)){  
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($commentSystemTYpe->system_type_id,$list->comment_id,$identity->user_id,2,0);
									$arr_likedUsers = array();
									if($like_details['is_liked']==1){
										$arr_likedUsers[] = $serviceManager->get('translator')->translate("you");
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}
								$allowedit = 0;
									if($list->user_id == $identity->user_id){
										$allowedit = 1;
									}
									switch($list->system_type_title){
										case 'Activity':
											$activity_deatils =  $this->getActivityTable()->getActivity($list->comment_refer_id);
											if($activity_deatils->group_activity_owner_user_id == $identity->user_id){
												$allowedit = 1;
											}
											if($this->getUserGroupTable()->checkOwner($activity_deatils->group_activity_group_id,$identity->user_id)){
												$allowedit = 1;
											}
										break;
										case 'Discussion':
											$discussion_deatils =  $this->getDiscussionTable()->getDiscussion($list->comment_refer_id);
											if($discussion_deatils->group_discussion_owner_user_id == $identity->user_id){
												$allowedit = 1;
											}
											if($this->getUserGroupTable()->checkOwner($discussion_deatils->group_discussion_group_id,$identity->user_id)){
												$allowedit = 1;
											}
										break;
										case 'Media':
											$media_deatils =  $this->getGroupMediaTable()->getMedia($list->comment_refer_id);
											if($media_deatils->media_added_user_id == $identity->user_id){
												$allowedit = 1;
											}
											if($this->getUserGroupTable()->checkOwner($media_deatils->media_added_group_id,$identity->user_id)){
												$allowedit = 1;
											}
										break;							 
									}
								$comments[] = array(
												'likes_count'=>$like_details['likes_counts'],
												'islike'=>$list->islike,
												'comment_content'=>$list->comment_content,
												'comment_id'=>$list->comment_id,
												'comment_time'=>$this->timeAgo($list->comment_added_timestamp),
												'user_given_name'=>$list->user_given_name,
												'user_id'=>$list->user_id,
												'user_register_type'=>$list->user_register_type,
												'user_fbid'=>$list->user_fbid,
												'profile_photo'=>$list->profile_photo,
												'likedUsers'=>$arr_likedUsers,
												'user_profile_name'=>$list->user_profile_name,
												'allowedit' =>$allowedit,
											);
							}
						}
					}						
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['group_media'] = $arr_group_media;
		$return_array['comments'] = $comments;		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getAlbumMediaAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arr_group_media = array();
		$allActiveMembers = array();
		$comments = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();				 
				$album_id =$post['album_id']; 
				$group_id = $post['group_id']; 
				$page = (isset($post['page'])&&$post['page']!=null&&$post['page']!=''&&$post['page']!='undefined')?$post['page']:1;
				
				$arr_group_list = '';
				$limit =5;
				$page =($page>0)?$page-1:0;
				$offset = $page*$limit;
				if($album_id){
					if($album_id=='unsorted'){
						$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
						$unsorted_media = $this->getGroupMediaTable()->getAllUnsortedMedia($group_id);	
						if(!empty($unsorted_media)){
							$media_content = array();
							foreach($unsorted_media as $unsorted){
								$unsortedmedia_ids = json_decode($unsorted['media_content']);
								$media_content = array_merge($media_content,$unsortedmedia_ids );
							}
							$logged_user_ismember = 0;
							if($this->getUserGroupTable()->is_member($identity->user_id,$group_id)){
								$logged_user_ismember = 1;
							}
							
							if(!empty($media_content)){
								$media_files = $this->getGroupMediaContentTable()->getMediaContents($media_content);
								$arr_media_files = array();
								foreach($media_files as $files){
									if($files['media_type'] == 'youtube'){
										$arr_media_files[] = array(
												'id'=>$files['media_content_id'],
												'files'=>$files['content'],
												'video_id'=>$this->get_youtube_id_from_url($files['content']),
												'media_type'=>$files['media_type'],
												);
									}else{
										$arr_media_files[] = array(
														'id'=>$files['media_content_id'],
														'files'=>$files['content'],
														'media_type'=>$files['media_type'],
														);
									}
								}
								 
								$arr_group_media = array(
													'album_id'=>'unsorted',
													'album_title'=>'Post Images/Unsorted',				 
													'media_files'=>$arr_media_files,
													'logged_user_ismember'=>$logged_user_ismember,
													 
													'group_title'=>$group_info->group_title,
													'group_seo_title'=>$group_info->group_seo_title,
													'group_id'=>$group_info->group_id,
													);
								
							}
						}
					}else{				
						$album_details = $this->getGroupAlbumTable()->getAlbum($album_id);
						if(!empty($album_details)){
							$media_content = [];
							$media_details = $this->getGroupMediaTable()->getAllAlbumMedia($album_id,$offset,$limit);
							$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	 
							if(!empty($media_details)){
								foreach($media_details as $details){
									$media_ids = json_decode($details['media_content']);
									$media_content = array_merge($media_content,$media_ids );
								}
								
								if(!empty($media_content)){
									$media_files = $this->getGroupMediaContentTable()->getMediaContents($media_content);
									$arr_media_files = array();
									foreach($media_files as $files){
										if($files['media_type'] == 'youtube'){
											$arr_media_files[] = array(
													'id'=>$files['media_content_id'],
													'files'=>$files['content'],
													'video_id'=>$this->get_youtube_id_from_url($files['content']),
													'media_type'=>$files['media_type'],
													);
										}else{
											$arr_media_files[] = array(
															'id'=>$files['media_content_id'],
															'files'=>$files['content'],
															'media_type'=>$files['media_type'],
															);
										}
									}
									$arr_files = array();
									$is_admin = 0;
									if($this->getUserGroupTable()->checkOwner($group_id,$album_details->creator_id)){
										$is_admin = 1;
									}
									$logged_user_ismember = 0;
									if($this->getUserGroupTable()->is_member($identity->user_id,$group_id)){
										$logged_user_ismember = 1;
									}
									$is_logged_user_admin = 0;
									if($this->getUserGroupTable()->checkOwner($group_id,$identity->user_id)){
										$is_logged_user_admin = 1;
									}
									$albumCreator = $this->getUserTable()->getProfileDetails($album_details->creator_id);
									$SystemTypeData = $this->getGroupTable()->fetchSystemType("Album");
									$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_id,$identity->user_id);
									$like_count = $like_details->likes_counts;		
									$arr_likedUsers = array();						
									if(!empty($like_details)){  
										$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$album_id,$identity->user_id,2,0);
										$arr_likedUsers = array();
										if($like_details['is_liked']==1){
											$arr_likedUsers[] = $serviceManager->get('translator')->translate("you");
										}
										if($like_details['likes_counts']>0&&!empty($liked_users)){
											foreach($liked_users as $likeuser){
												$arr_likedUsers[] = $likeuser['user_given_name'];
											}
										}							 
									}
									$commet_users = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$album_id,$identity->user_id);								 
									$event_name = '';
									$event_id = '';								 
									$event_details  = $this->getGroupEventAlbumTable()->getAlbumEvents($album_id);
									$is_event_creator = 0;
									if(!empty($event_details)){
										$event_name = $event_details->group_activity_title;
										$event_id = $event_details->group_activity_id;
										if( $event_details->group_activity_owner_user_id == $identity->user_id){
											$is_event_creator = 1;
										}
									}
									 
									$arr_group_media = array(
														'album_name'=>$album_details->album_title,
														'album_id'=>$album_details->album_id,
														'album_description'=>$album_details->album_description,
														'media_files'=>$arr_media_files,
														'logged_user_ismember'=>$logged_user_ismember,
														'is_admin'=>$is_admin,
														'creator_id' => $albumCreator->user_id,
														'user_id' => $albumCreator->user_id,
														'user_given_name' => $albumCreator->user_given_name,
														'user_first_name' => $albumCreator->user_first_name,
														'user_last_name' => $albumCreator->user_last_name,
														'user_profile_name' => $albumCreator->user_profile_name,
														'user_fbid' => $albumCreator->user_fbid,
														'profile_photo' => $albumCreator->profile_photo,
														'likedUsers' => $arr_likedUsers,
														'likes_counts' =>$like_details['likes_counts'],
														'is_liked' =>$like_details['is_liked'],
														'comment_count' =>$commet_users['comment_counts'],
														'is_commented' =>$commet_users['is_commented'],
														'event_name'=>$event_name,
														'event_id'=>$event_id,
														'group_title'=>$group_info->group_title,
														'group_seo_title'=>$group_info->group_seo_title,
														'group_id'=>$group_info->group_id,
														'is_logged_user_admin'=>$is_logged_user_admin,
														'is_album_creator'=>($albumCreator->user_id==$identity->user_id)?1:0,
														'is_event_creator'=>$is_event_creator,
														);
									$commentSystemTYpe =$this->getGroupTable()->fetchSystemType('Comment'); 
									$comments_details = $this->getCommentTable()->getAllCommentsWithLike($SystemTypeData->system_type_id,$commentSystemTYpe->system_type_id,$album_id,$identity->user_id,10,0);
									if(!empty($comments_details)){
										foreach($comments_details as $list){								 
											$arr_likedUsers = array();
											$like_details = $this->getLikeTable()->fetchLikesCountByReference($commentSystemTYpe->system_type_id,$list->comment_id,$identity->user_id);
											if(!empty($like_details)){  
												$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($commentSystemTYpe->system_type_id,$list->comment_id,$identity->user_id,2,0);
												$arr_likedUsers = array();
												if($like_details['is_liked']==1){
													$arr_likedUsers[] = $serviceManager->get('translator')->translate("you");
												}
												if($like_details['likes_counts']>0&&!empty($liked_users)){
													foreach($liked_users as $likeuser){
														$arr_likedUsers[] = $likeuser['user_given_name'];
													}
												}								 
											}
											$allowedit = 0;
											if($list->user_id == $identity->user_id){
												$allowedit = 1;
											}
											switch($list->system_type_title){
												case 'Activity':
													$activity_deatils =  $this->getActivityTable()->getActivity($list->comment_refer_id);
													if($activity_deatils->group_activity_owner_user_id == $identity->user_id){
														$allowedit = 1;
													}
													if($this->getUserGroupTable()->checkOwner($activity_deatils->group_activity_group_id,$identity->user_id)){
														$allowedit = 1;
													}
												break;
												case 'Discussion':
													$discussion_deatils =  $this->getDiscussionTable()->getDiscussion($list->comment_refer_id);
													if($discussion_deatils->group_discussion_owner_user_id == $identity->user_id){
														$allowedit = 1;
													}
													if($this->getUserGroupTable()->checkOwner($discussion_deatils->group_discussion_group_id,$identity->user_id)){
														$allowedit = 1;
													}
												break;
												case 'Media':
													$media_deatils =  $this->getGroupMediaTable()->getMedia($list->comment_refer_id);
													if($media_deatils->media_added_user_id == $identity->user_id){
														$allowedit = 1;
													}
													if($this->getUserGroupTable()->checkOwner($media_deatils->media_added_group_id,$identity->user_id)){
														$allowedit = 1;
													}
												break;							 
											}
											$comments[] = array(
														'likes_count'=>$like_details['likes_counts'],
														'islike'=>$list->islike,
														'comment_content'=>$list->comment_content,
														'comment_id'=>$list->comment_id,
														'comment_time'=>$this->timeAgo($list->comment_added_timestamp),
														'user_given_name'=>$list->user_given_name,
														'user_id'=>$list->user_id,
														'user_register_type'=>$list->user_register_type,
														'user_fbid'=>$list->user_fbid,
														'profile_photo'=>$list->profile_photo,
														'likedUsers'=>$arr_likedUsers,
														'user_profile_name'=>$list->user_profile_name,
														'allowedit' =>$allowedit,
													);
										}
									}
								 
								}
							}
						}else{$error = $serviceManager->get('translator')->translate("Album not exist");}	
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['group_album'] = $arr_group_media;
		$return_array['comments'] = $comments;		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
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
	public function matchedgroupsWithInterestsAction(){
		$error = '';
		$auth = new AuthenticationService();	
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();
				$city = (isset($post['city'])&&$post['city']!=null&&$post['city']!=''&&$post['city']!='undefined')?$post['city']:'';
				$country = (isset($post['country'])&&$post['country']!=null&&$post['country']!=''&&$post['country']!='undefined')?$post['country']:'';	
				$category = (isset($post['categories'])&&$post['categories']!=null&&$post['categories']!=''&&$post['categories']!='undefined')?$post['categories']:'';
				$page = (isset($post['page'])&&$post['page']!=null&&$post['page']!=''&&$post['page']!='undefined')?$post['page']:1;
				$option = (isset($post['option'])&&$post['option']!=null&&$post['option']!=''&&$post['option']!='undefined')?$post['option']:'All';
				$arr_group_list = '';
				$limit =10;
				$page =($page>0)?$page-1:0;
				$offset = $page*$limit;
				if($option=='All'){
					$groups = $this->getUserGroupTable()->getmatchGroupsByuser($identity->user_id,$city,$country,$category,$limit,$offset);					
				}else{
					$groups = $this->getUserGroupTable()->getmatchGroupsByuserTags($identity->user_id,$city,$country,$category,$limit,$offset);
				}			
				if(!empty($groups)){
					foreach($groups as $list){
						$tag_category = $this->getGroupTagTable()->getAllGroupTagCategiry($list['group_id']);
						$tags = $this->getGroupTagTable()->fetchAllGroupTags($list['group_id']);
						$arr_tags = array();
						if(!empty($tags)){
							foreach($tags as $tag_list){
								$arr_tags[] = array('group_tag_id'=>$tag_list['group_tag_id'],
											'tag_title'=>$tag_list['tag_title'],
											'tag_id'=>$tag_list['tag_id'],
											'tag_title_locale'=>$serviceManager->get('translator')->translate($tag_list['tag_title']),									
										);
							}
						}
						$is_requested = 0;
						$requestedHystory = $this->getUserGroupJoiningRequestTable()->checkActiveRequestExist($list['group_id'],$identity->user_id);
						if(!empty($requestedHystory)&&$requestedHystory->user_group_joining_request_id!=''){
							$is_requested = 1;
						}
						$is_invited = 0;
						$invitedHystory = $this->getGroupJoiningInvitationTable()->checkInvited($identity->user_id,$list['group_id']);
						if(!empty($invitedHystory)&&$invitedHystory->user_group_joining_invitation_id!=''){
							$is_invited = 1;
						}
						$is_member = 0;
						if($this->getUserGroupTable()->is_member($identity->user_id,$list['group_id'])){
							$is_member = 1;
						}
						$arr_friends = array();
						if($list['friend_count']>0){
							$type = 'Friends';
							$arr_friends = $this->getUserGroupTable()->getMembers($list['group_id'],$identity->user_id,$type,0,2);
						}
						$arr_group_list[] = array(
										'group_id' =>$list['group_id'],
										'group_title' =>$list['group_title'],
										'group_seo_title' =>$list['group_seo_title'],
										'group_type' =>$list['group_type'],
										'group_photo_photo' =>$list['group_photo_photo'],										 
										'country_title' =>$list['country_title'],
										'country_code' =>$list['country_code'],
										'country_locale' =>$serviceManager->get('translator')->translate($list['country_code']),
										'member_count' =>$list['member_count'],
										'friend_count' =>$list['friend_count'],
										'city' =>$list['city'],	
										'city_locale' =>$serviceManager->get('translator')->translate($list['city']),
										'tag_category_count' =>count($tag_category),
										'tag_category' =>$tag_category,
										'tags' =>$arr_tags,
										'is_requested'=>$is_requested,
										'is_invited'=>$is_invited,
										'friends' => $arr_friends,
										'is_member' => $is_member,
										);
					}
				}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error =  $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['groups'] = $arr_group_list;
		 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function grouplistAction(){
        $error = '';
		$auth = new AuthenticationService();	
		$arr_group_list = '';
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if($request->isPost()){ 
                $profilename            = $this->getRequest()->getPost('profilename');
                $strType                = $this->getRequest()->getPost('type');
                $page                  = $this->getRequest()->getPost('page');
                $limit =10;
				$page =($page>0)?$page-1:0;
				$offset = $page*$limit;
				$userinfo = $this->getUserTable()->getUserByProfilename($profilename);
				$myinfo               = $this->getUserTable()->getUser($identity->user_id);
				if(!empty($userinfo)&&$userinfo->user_id&&!empty($myinfo)&&$myinfo->user_id){
					$profile_type='mine' ;
					if($userinfo->user_id!=$identity->user_id){
						$profile_type='others';
					}
					$intTotalGroups     = $this->getUserGroupTable()->fetchAllUserGroupCount($userinfo->user_id,$identity->user_id,$strType,$profile_type);
					if($intTotalGroups['group_count'] > 0){                            
						$arrGroups      = $this->getUserGroupTable()->fetchUserGroupList($userinfo->user_id,$identity->user_id,$strType,$profile_type,$limit,$offset );
						$group_list = array();
						if(!empty($arrGroups)){
							foreach($arrGroups as $list){
								$tag_category = $this->getGroupTagTable()->getAllGroupTagCategiry($list['group_id']);
								$tags = $this->getGroupTagTable()->fetchAllGroupTags($list['group_id']);
								$arr_tags = array();
								if(!empty($tags)){
									foreach($tags as $tag_list){
										$arr_tags[] = array('group_tag_id'=>$tag_list['group_tag_id'],
													'tag_title'=>$tag_list['tag_title'],
													'tag_id'=>$tag_list['tag_id'],
													'tag_title_locale'=>$serviceManager->get('translator')->translate($tag_list['tag_title']),									
												);
									}
								}								
								$request_count =0;
								if($list['is_admin']){
									$request_count = $this->getUserGroupJoiningRequestTable()->countGroupMemberRequests($list['group_id'])->memberCount;
								}
								$is_requested = 0;
								$requestedHystory = $this->getUserGroupJoiningRequestTable()->checkActiveRequestExist($list['group_id'],$identity->user_id);
								if(!empty($requestedHystory)&&$requestedHystory->user_group_joining_request_id!=''){
									$is_requested = 1;
								}
								$is_invited = 0;
								$invitedHystory = $this->getGroupJoiningInvitationTable()->checkInvited($identity->user_id,$list['group_id']);
								if(!empty($invitedHystory)&&$invitedHystory->user_group_joining_invitation_id!=''){
									$is_invited = 1;
								}
								$arr_friends = array();
								if($list['friend_count']>0){
									$type = 'Friends';
									$arr_friends = $this->getUserGroupTable()->getMembers($list['group_id'],$identity->user_id,$type,0,2);
								}
								$arr_group_list[] = array(
									'group_id' =>$list['group_id'],
									'group_title' =>$list['group_title'],
									'group_seo_title' =>$list['group_seo_title'],
									'group_type' =>$list['group_type'],
									'group_status' =>$list['group_status'],
									'group_photo_photo' =>$list['group_photo_photo'],										 
									'country_title' =>$list['country_title'],
									'country_code' =>$list['country_code'],
									'country_locale' =>$serviceManager->get('translator')->translate($list['country_code']),
									'member_count' =>$list['member_count'],
									'friend_count' =>$list['friend_count'],
									'city' =>$list['city'],	
									'city_locale' =>$serviceManager->get('translator')->translate($list['city']),	
									'is_admin' =>$list['is_admin'],	
									'is_member' =>$list['is_member'],	
									'tag_category_count' =>count($tag_category),
									'tag_category' =>$tag_category,
									'request_count' =>$request_count,
									'is_requested'=>$is_requested,
									'is_invited'=>$is_invited,
									'tags' =>$arr_tags,
									'friends'=>$arr_friends,
								);
							}
						}
						                               
					}else{
						$error      = $serviceManager->get('translator')->translate("No Record Found");							 
					}         
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['groups'] = $arr_group_list;
		 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
    }
	public function leavegroupAction(){
		$error = '';
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$request   = $this->getRequest();
			$identity  = $auth->getIdentity();
            if ($request->isPost()){                   
                 $intGroupId = $this->getRequest()->getPost('groupId'); 
                 $userinfo   = $this->getUserTable()->getUser($identity->user_id);
				 if(!empty($userinfo)&&$userinfo->user_id){					 
					$arrUserGroup      = $this->getUserGroupTable()->getUserGroup($identity->user_id, $intGroupId);
					if(count($arrUserGroup) > 0){					    
					    if($this->getUserGroupTable()->deleteOneUserGroup($intGroupId, $identity->user_id)){
						   $this->getGroupQuestionnaireAnswersTable()->deleteUserAnswersOfGroup($intGroupId,$identity->user_id);
						}else{	$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again"); }			   
				   }else{$error = $serviceManager->get('translator')->translate("Group not exist in the system");}
				}else{$error = $serviceManager->get('translator')->translate("User not exist in the system");}              
            }else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;		 		 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getQuestionnaireAction(){
        $error = '';
		$auth = new AuthenticationService();		 
		$questionnaire = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if($request->isPost()){                 
                $group_id                  = $this->getRequest()->getPost('group_id');
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);
				
                if(!empty($group_info)&&$group_info->group_id!=''){
					$questionnaire_list = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaire($group_id);
					if(!empty($questionnaire_list)){
						
						foreach($questionnaire_list as $list){
							$options = array();
							if($list->answer_type == 'radio'||$list->answer_type == 'checkbox'){
								$options =$this->getGroupQuestionnaireOptionsTable()->getoptionOfOneQuestion($list->questionnaire_id);
							}
							$questionnaire[] = array(
									'questionnaire_id'=>$list->questionnaire_id,
									'question'=>$list->question,
									'answer_type'=>$list->answer_type,
									'options'=>$options,
								);
						}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['questionnaire'] = $questionnaire;
		 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
    }
	public function saveUserQuestionnaireAction(){
		$error = '';
		$auth = new AuthenticationService();		 
		$questionnaire = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if($request->isPost()){
				$questionanswers = $this->getRequest()->getPost('questionanswers'); 
                $group_id  = $this->getRequest()->getPost('group_id');
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);				
                if(!empty($group_info)&&$group_info->group_id!=''){					 
					if(!empty($questionanswers)){						
						foreach($questionanswers as $list){
							$question = $this->getGroupJoiningQuestionnaireTable()->getQuestionFromQuestionId($list['question_id']);
							if(!empty($question)){ 
								if($question->answer_type == 'radio'||$question->answer_type == 'checkbox'){
									$data['group_id'] = $group_id;
									$data['question_id'] = $list['question_id'];
									$data['selected_options'] = $list['answer'];
									$data['added_user_id'] = $identity->user_id;
								}else{
									$data['group_id'] = $group_id;
									$data['question_id'] = $list['question_id'];
									$data['answer'] = $list['answer'];
									$data['added_user_id'] = $identity->user_id;
								}
								$this->getGroupQuestionnaireAnswersTable()->AddAnswer($data);
							}
						}
					}
					if($group_info->group_type == 'open'){
						$usergroup = $this->getUserGroupTable()->getUserGroup($identity->user_id,$group_id);						
						if(empty($usergroup)){
							$user_data['user_group_user_id'] = $identity->user_id;
							$user_data['user_group_group_id'] = $group_id;
							$user_data['user_group_status'] = "available";							 
							$this->getUserGroupTable()->AddMembersTOGroup($user_data);
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$msg = $identity->user_given_name." Joined in the group ".$group_info->group_title;
							$subject = 'Group joining Request';
							$from = 'admin@jeera.me';
							$process = 'Joined';
							$admin_users = $this->getUserGroupTable()->getAllAdminUsers($group_id);
							foreach($admin_users as $admins){
								if($identity->user_id!=$admins->user_group_user_id){
								$this->UpdateNotifications($admins->user_group_user_id,$msg,4,$subject,$from,$identity->user_id,$group_id,$process);
								}
							}
						}else{$error = $serviceManager->get('translator')->translate("You are already a member of this group");}
					}
					if($group_info->group_type == 'public'){ 
						$usergroup = $this->getUserGroupTable()->getUserGroup($identity->user_id,$group_id);
						$usergroup_requested = $this->getUserGroupJoiningRequestTable()->checkActiveRequestExist($group_id,$identity->user_id);
						if(empty($usergroup)){
							if(empty($usergroup_requested)){
								$user_data['user_group_joining_request_user_id'] = $identity->user_id;
								$user_data['user_group_joining_request_group_id'] = $group_id;
								$user_data['user_group_joining_request_status'] = "active"; 
								$this->getUserGroupJoiningRequestTable()->AddRequestTOGroup($user_data);
								$config = $this->getServiceLocator()->get('Config');
								$base_url = $config['pathInfo']['base_url'];
								$msg = $identity->user_given_name." requested to join in the group ".$group_info->group_title;
								$subject = 'Group joining Request';
								$from = 'admin@jeera.me';
								$process = 'Requested';
								$admin_users = $this->getUserGroupTable()->getAllAdminUsers($group_id);
								 
								foreach($admin_users as $admins){ 
									if($identity->user_id!=$admins->user_group_user_id){
									$this->UpdateNotifications($admins->user_group_user_id,$msg,4,$subject,$from,$identity->user_id,$group_id,$process);
									}
								} 
							}else{$error = $serviceManager->get('translator')->translate("You are already requested");}
						}else{$error = $serviceManager->get('translator')->translate("You are already a member of this group");}
					}
					if($group_info->group_type == 'private'){ 
						$invitedHystory = $this->getGroupJoiningInvitationTable()->checkInvited($identity->user_id,$group_id);	
						$usergroup = $this->getUserGroupTable()->getUserGroup($identity->user_id,$group_id);
						if(empty($usergroup)){ 
							if(!empty($invitedHystory)&&$invitedHystory->user_group_joining_invitation_id!=''){
								$user_data['user_group_user_id'] = $identity->user_id;
								$user_data['user_group_group_id'] = $group_id;
								$user_data['user_group_status'] = "available";							 				 
								if($this->getUserGroupTable()->AddMembersTOGroup($user_data)){
									$this->getGroupJoiningInvitationTable()->ChangeStatusTOProcessed($invitedHystory->user_group_joining_invitation_id);
									$config = $this->getServiceLocator()->get('Config');
									$base_url = $config['pathInfo']['base_url'];
									$msg = $identity->user_given_name." Joined in the group ".$group_info->group_title;
									$subject = 'Group joining Request';
									$from = 'admin@jeera.me';
									$process = 'Joined';
									$admin_users = $this->getUserGroupTable()->getAllAdminUsers($group_id);
									foreach($admin_users as $admins){
										if($identity->user_id!=$admins->user_group_user_id){
										$this->UpdateNotifications($admins->user_group_user_id,$msg,4,$subject,$from,$identity->user_id,$group_id,$process);
										}
									}
								}
							}else{$error = $serviceManager->get('translator')->translate("You need invitation from group admin to join this group");}
														
						}else{$error = $serviceManager->get('translator')->translate("You are already a member of this group");}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['questionnaire'] = $questionnaire;
		 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}	
	public function joinGroupAction(){
		$error = '';
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();		
		$questionnaire = array();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if($request->isPost()){				 
                $group_id  = $this->getRequest()->getPost('group_id');
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	 
                if(!empty($group_info)&&$group_info->group_id!=''){				 
					if($group_info->group_type == 'open'){
						$usergroup = $this->getUserGroupTable()->getUserGroup($identity->user_id,$group_id);
						if(empty($usergroup)){
							$user_data['user_group_user_id'] = $identity->user_id;
							$user_data['user_group_group_id'] = $group_id;
							$user_data['user_group_status'] = "available";							 
							$this->getUserGroupTable()->AddMembersTOGroup($user_data);
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$msg = $identity->user_given_name." Joined in the group ".$group_info->group_title;
							$subject = 'Group joining Request';
							$from = 'admin@jeera.me';
							$process = 'Joined';
							$admin_users = $this->getUserGroupTable()->getAllAdminUsers($group_id);
							foreach($admin_users as $admins){
								if($identity->user_id!=$admins->user_group_user_id){
								$this->UpdateNotifications($admins->user_group_user_id,$msg,4,$subject,$from,$identity->user_id,$group_id,$process);
								}
							}
						}else{$error = $serviceManager->get('translator')->translate("You are already a member of this group");}
					}
					if($group_info->group_type == 'public'){ 
						$usergroup_requested = $this->getUserGroupJoiningRequestTable()->checkActiveRequestExist($group_id,$identity->user_id);
						$usergroup = $this->getUserGroupTable()->getUserGroup($identity->user_id,$group_id);
						if(empty($usergroup)){
							if(empty($usergroup_requested)){
								$user_data['user_group_joining_request_user_id'] = $identity->user_id;
								$user_data['user_group_joining_request_group_id'] = $group_id;
								$user_data['user_group_joining_request_status'] = "active";							 
								$this->getUserGroupJoiningRequestTable()->AddRequestTOGroup($user_data);
								$config = $this->getServiceLocator()->get('Config');
								$base_url = $config['pathInfo']['base_url'];
								$msg = $identity->user_given_name." requested to join in the group ".$group_info->group_title;
								$subject = 'Group joining Request';
								$from = 'admin@jeera.me';
								$process = 'Requested';
								$admin_users = $this->getUserGroupTable()->getAllAdminUsers($group_id);
								//print_r($admin_users);die();
								foreach($admin_users as $admins){
									if($identity->user_id!=$admins->user_group_user_id){
									$this->UpdateNotifications($admins->user_group_user_id,$msg,4,$subject,$from,$identity->user_id,$group_id,$process);
									}
								}
							}else{$error = $serviceManager->get('translator')->translate("You are already requested");}
						}else{$error = $serviceManager->get('translator')->translate("You are already a member of this group");}
					}
					if($group_info->group_type == 'private'){
						$invitedHystory = $this->getGroupJoiningInvitationTable()->checkInvited($identity->user_id,$group_id);	
						$usergroup = $this->getUserGroupTable()->getUserGroup($identity->user_id,$group_id);
						if(empty($usergroup)){ 
							if(!empty($invitedHystory)&&$invitedHystory->user_group_joining_invitation_id!=''){
								$user_data['user_group_user_id'] = $identity->user_id;
								$user_data['user_group_group_id'] = $group_id;
								$user_data['user_group_status'] = "available";							 				 
								if($this->getUserGroupTable()->AddMembersTOGroup($user_data)){
									$this->getGroupJoiningInvitationTable()->ChangeStatusTOProcessed($invitedHystory->user_group_joining_invitation_id);
									$config = $this->getServiceLocator()->get('Config');
									$base_url = $config['pathInfo']['base_url'];
									$msg = $identity->user_given_name." Joined in the group ".$group_info->group_title;
									$subject = 'Group joining Request';
									$from = 'admin@jeera.me';
									$process = 'Joined';
									$admin_users = $this->getUserGroupTable()->getAllAdminUsers($group_id);
									foreach($admin_users as $admins){
										if($identity->user_id!=$admins->user_group_user_id){
										$this->UpdateNotifications($admins->user_group_user_id,$msg,4,$subject,$from,$identity->user_id,$group_id,$process);
										}
									}
								}
							}else{$error =$serviceManager->get('translator')->translate("You need invitation from group admin to join this group") ;}						
						}else{$error = $serviceManager->get('translator')->translate("You are already a member of this group");}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['questionnaire'] = $questionnaire;
		 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getAllMediaAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arr_group_media = array();
		$allActiveMembers = array();
		$comments = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();
				$group_id = $post['group_id'];
				$page = $this->getRequest()->getPost('page');
                $limit =10;
				$page =($page>0)?$page-1:0;
				$offset = $page*$limit;				 
				if($group_id){
					$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
					if(!empty($group_info)&&$group_info->group_id!=''){	
						 $groupmedia = $this->getGroupMediaTable()->getAllMedia($group_id,$limit,$offset);
						 $media_content = array();
						 if(!empty($groupmedia)){
							 foreach($groupmedia as $details){
								$media_ids = json_decode($details['media_content']);
								$media_content = array_merge($media_content,$media_ids );
							}							
							if(!empty($media_content)){
								$media_files = $this->getGroupMediaContentTable()->getMediaContents($media_content);							
								foreach($media_files as $media){
									$video_id ='';
									if($media['media_type']=='youtube'){
										$video_id = $this->getYoutubeIdFromUrl($media['content']);
									}
									$arr_group_media[] = array(
										 
										'media_type'=>$media['media_type'],										 
										'id' => $media['media_content_id'],
										'files' => $media['content'],
										'video_id'=>$video_id,
										'group_title'=>$group_info->group_title,
										'group_seo_title'=>$group_info->group_seo_title,
										'group_id'=>$group_info->group_id,
									);
								}
							}
						 }						 
					}else{$error = $serviceManager->get('translator')->translate("Group is not existing");}			
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['group_media'] = $arr_group_media;
		$return_array['comments'] = $comments;		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getAllGroupAlbumsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$album_content = array();
		$allActiveMembers = array();
		$comments = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();
				$group_id = $post['group_id'];
				$page = $this->getRequest()->getPost('page');
                $limit =10;
				$page =($page>0)?$page-1:0;
				$offset = $page*$limit;		
				$config = $this->getServiceLocator()->get('Config');
				if($group_id){
					$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
					if(!empty($group_info)&&$group_info->group_id!=''){	
						 $groupalbums = $this->getGroupAlbumTable()->getAllGroupAlbums($group_id,$limit,$offset);					 
						 $album_content = array();
						 $album_icon_url='';
						 $unsorted_media = $this->getGroupMediaTable()->getAllUnsortedMedia($group_id);	
						 if(!empty($unsorted_media)){
							$media_content = array();
							foreach($unsorted_media as $unsorted){
								$unsortedmedia_ids = json_decode($unsorted['media_content']);
								$media_content = array_merge($media_content,$unsortedmedia_ids );
							}
							$album_icon = $this->getGroupMediaContentTable()->getMediafile($media_content[0]);
								if(!empty($album_icon)){
									if($album_icon->media_type=='image'){
										 $album_icon_url=$config['pathInfo']['group_img_path_absolute_path'].$group_id.'/media/medium/'.$album_icon->content;
									}
									if($album_icon->media_type=='youtube'){
										$video_id = $this->getYoutubeIdFromUrl($album_icon->content);
										$album_icon_url='http://img.youtube.com/vi/'.$video_id.'/0.jpg';
									}
								}else{
									$album_icon_url=$config['pathInfo']['base_url']."/public/images/album-thumb.png";
								}
							$album_content[] = array(
													'album_id'=>'unsorted',
													'album_title'=>'Post Images/Unsorted',
													'album_icon_url'=>$album_icon_url,
													'albumImage_count'=>count($media_content),
													'group_id'=>$group_id,
													'event_id'=>0,
													'created_date'=>'',
													);
						 }
						 if(!empty($groupalbums)){
							foreach($groupalbums as $details){
								$media_details = $this->getGroupMediaTable()->getAllAlbumFiles($details['album_id']);
								$albumImage_count = 0; 
								$media_content = array();
								if(!empty($media_details)){
									foreach($media_details as $contents){
										$media_ids = json_decode($contents['media_content']);
										$media_content = array_merge($media_content,$media_ids );
									}
								}
								$albumImage_count = count($media_content); 
								$album_icon = $this->getGroupMediaContentTable()->getAlbumIcon($details['album_id']);
								if(!empty($album_icon)){
									if($album_icon->media_type=='image'){
										 $album_icon_url=$config['pathInfo']['group_img_path_absolute_path'].$group_id.'/media/medium/'.$album_icon->content;
									}
									if($album_icon->media_type=='youtube'){
										$video_id = $this->getYoutubeIdFromUrl($album_icon->content);
										$album_icon_url='http://img.youtube.com/vi/'.$video_id.'/0.jpg';
									}
								}else{
									$album_icon_url=$config['pathInfo']['base_url']."/public/images/album-thumb.png";
								}
								$album_content[] = array(
													'album_id'=>$details['album_id'],
													'album_title'=>$details['album_title'],
													'album_icon_url'=>$album_icon_url,
													'albumImage_count'=>$albumImage_count,
													'group_id'=>$group_id,
													'event_id'=>$details['event_id'],
													'created_date'=>date("M t,Y",strtotime($details['created_date'])),
													);
							}						 
						 }						 
					}else{$error = $serviceManager->get('translator')->translate("Group is not existing");}			
				}else{$error =  $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error =  $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['album_content'] = $album_content;
		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getAllEventAlbumsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$album_content = array();
		$allActiveMembers = array();
		$comments = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();
				$group_id = $post['group_id'];
				$page = $this->getRequest()->getPost('page');
                $limit =10;
				$page =($page>0)?$page-1:0;
				$offset = $page*$limit;		
				$config = $this->getServiceLocator()->get('Config');
				if($group_id){
					$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
					if(!empty($group_info)&&$group_info->group_id!=''){	
						 $groupalbums = $this->getGroupAlbumTable()->getAllEventAlbums($group_id,$limit,$offset);					 
						 $album_content = array();
						 $album_icon_url='';
						 if(!empty($groupalbums)){
							foreach($groupalbums as $details){
								$media_details = $this->getGroupMediaTable()->getAllAlbumFiles($details['album_id']);
								$albumImage_count = 0; 
								$media_content = array();
								if(!empty($media_details)){
									foreach($media_details as $contents){
										$media_ids = json_decode($contents['media_content']);
										$media_content = array_merge($media_content,$media_ids );
									}
								}
								$albumImage_count = count($media_content); 
								$album_icon = $this->getGroupMediaContentTable()->getAlbumIcon($details['album_id']);
								if(!empty($album_icon)){
									if($album_icon->media_type=='image'){
										 $album_icon_url=$config['pathInfo']['group_img_path_absolute_path'].$group_id.'/media/medium/'.$album_icon->content;
									}
									if($album_icon->media_type=='youtube'){
										$video_id = $this->getYoutubeIdFromUrl($album_icon->content);
										$album_icon_url='http://img.youtube.com/vi/'.$video_id.'/0.jpg';
									}
								}else{
									$album_icon_url=$config['pathInfo']['base_url']."/public/images/album-thumb.png";
								}
								$album_content[] = array(
													'album_id'=>$details['album_id'],
													'album_title'=>$details['album_title'],
													'album_icon_url'=>$album_icon_url,
													'albumImage_count'=>$albumImage_count,
													'group_id'=>$group_id,
													'event_id'=>$details['event_id'],
													'group_activity_title'=>$details['group_activity_title'],
													'created_date'=>date("M t,Y",strtotime($details['created_date'])),
													);
							}						 
						 }						 
					}else{$error = $serviceManager->get('translator')->translate("Group is not existing");}			
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['album_content'] = $album_content;
		 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function getYoutubeIdFromUrl($url){
		preg_match("#([\/|\?|&]vi?[\/|=]|youtu\.be\/|embed\/)(\w+)#", $url, $matches);
		return(end($matches));
	}
	public function groupdetailsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel->setVariable('image_folders',$config['image_folders']);
		$serviceManager = $this->getServiceLocator();
		//$request   = $this->getRequest();
		$edit = $this->params()->fromQuery('edit');
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$session = New Container('language');
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
            $group_seo      = $this->params('group_seo');
			$profilepic = $this->getUserTable()->getUserProfilePic($identity->user_id);
			$pic = '';
			if(!empty($profilepic)&&$profilepic->biopic!='')
			$pic = $profilepic->biopic;
			$identity->profile_pic = $pic;			 
			$this->layout()->identity = $identity;
			$userinfo = $this->getUserTable()->getUser($identity->user_id);
            $viewModel->setVariable( 'current_Profile', $userinfo->user_profile_name);
			if(!empty($userinfo)&&$userinfo->user_id){
				$user_profileData = $this->getUserTable()->getProfileDetails($identity->user_id);
				$viewModel->setVariable( 'profilename', $userinfo->user_profile_name);
				$viewModel->setVariable( 'userinfo', $user_profileData);
				$arrGroup           = $this->getGroupTable()->getGroupBySeoTitle($group_seo);
                if(!empty($arrGroup)){  
					  $group_info = $this->getGroupTable()->getGroupDetails($arrGroup->group_id,$identity->user_id);
					  $arr_group_info = array();
					  if(!empty($group_info)){
						$tag_category = $this->getGroupTagTable()->getAllGroupTagCategiry($group_info->group_id);
						$arr_tags = $this->getGroupTagTable()->fetchAllGroupTags($group_info->group_id); 
						$tags = array();
						if(!empty($arr_tags)){
							foreach($arr_tags as $list){
								$tags[] =array(
									'group_tag_id'=>$list['group_tag_id'],
									'group_tag_group_id'=>$list['group_tag_group_id'],
									'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
									'tag_id'=>$list['tag_id'],									 
								);
							}
						}	
						$viewModel->setVariable( 'enableEdit',0);
						$request_count = 0;
						if($group_info->is_admin){
							$request_count = $this->getUserGroupJoiningRequestTable()->countGroupMemberRequests($group_info->group_id)->memberCount;
							if(isset($edit)&&$edit==1){
								$viewModel->setVariable( 'enableEdit',1);
							}
						}
						$is_invited = 0;
								$invitedHystory = $this->getGroupJoiningInvitationTable()->checkInvited($identity->user_id,$group_info->group_id);
								if(!empty($invitedHystory)&&$invitedHystory->user_group_joining_invitation_id!=''){
									$is_invited = 1;
								}
						 $arr_group_info = array(
							'group_id'=>$group_info->group_id,
							'group_title'=>$group_info->group_title,
							'group_seo_title'=>$group_info->group_seo_title,
							'group_description'=>$group_info->group_description,
							'group_added_timestamp'=>date("F d, Y",strtotime($group_info->group_added_timestamp)),
							'group_type'=>$group_info->group_type,
							'is_admin'=>$group_info->is_admin,
							'is_member'=>$group_info->is_member,
							'is_requested'=>$group_info->is_requested,
							'is_invited'=>$is_invited,
							'member_count'=>$group_info->member_count,
							'friend_count'=>$group_info->friend_count,
							'country_title'=>$group_info->country_title,
							'country_locale'=>$serviceManager->get('translator')->translate($group_info->country_code),
							'country_code'=>$group_info->country_code,
							'group_photo_photo'=>$group_info->group_photo_photo,
							'group_photo_orginal'=>$group_info->group_photo_orginal,
							'city'=>$group_info->city,		
							'city_locale'=>$serviceManager->get('translator')->translate($group_info->city),
							'request_count'=>$request_count,
						 );
					  }
					  $groupUsers = $this->getUserGroupTable()->fetchAllUserListForGroup($group_info->group_id,$identity->user_id,0,3)->toArray();
					  $viewModel->setVariable('tag_category',$tag_category);
					  $viewModel->setVariable('tags',$tags);
					  $viewModel->setVariable('group_info',$arr_group_info);
					  $viewModel->setVariable('groupUsers',$groupUsers);
					  $profile_data = $this->getUserTable()->getProfileDetails($identity->user_id);				 
					  $viewModel->setVariable( 'profile_data' , $profile_data);
					  $myIntrests = $this->getUserTagTable()->getAllUserTags($identity->user_id);
					  $viewModel->setVariable( 'myIntrests' , $myIntrests);
					  return $viewModel;
				}else{
					$error = $serviceManager->get('translator')->translate("Group not exist");
					$result = new ViewModel(array('error'=>$error));
					return $result;
				}
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
	}
	public function getAllGroupTagsAction(){
		$error = '';
		$auth = new AuthenticationService();
		$arr_group_tags = array();
		$tag_category = array();
		$comments = array();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){ 
				$post = $request->getPost();
				$group_id = $post['group'];				 				 
				if($group_id){
					$tag_category = $this->getGroupTagTable()->getAllGroupTagCategiry($group_id);
					$arr_group_tags = $this->getGroupTagTable()->fetchAllGroupTags($group_id);
					$tags = array();
					if(!empty($arr_group_tags)){
						foreach($arr_group_tags as $list){
							$tags[] =array(
								'group_tag_id'=>$list['group_tag_id'],
								'group_tag_group_id'=>$list['group_tag_group_id'],
								'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
								'tag_id'=>$list['tag_id'],									 
							);
						}
					}	
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	
		$return_array['group_tags'] = $tags;	
		$return_array['tag_category'] = $tag_category;	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function updateTagAction(){
		$error = '';
		$tag_category = array();
		$tags = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group'];	
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					if(!empty($post['tags'])){
						foreach($post['tags'] as $tags){
							$data_grouptags = array();
							$tag_hystory = $this->getTagTable()->getTag($tags);
							$tag_exist =  $this->getGroupTagTable()->checkGroupTag($group_id,$tags); 
							if(!empty($tag_hystory)&&$tag_hystory->tag_id!=''&&empty($tag_exist)){
								$data_grouptags['group_tag_group_id'] =$group_id;
								$data_grouptags['group_tag_tag_id'] = $tags;
								 
								$objGroupTag = new GroupTag();
								$objGroupTag->exchangeArray($data_grouptags);
								$this->getGroupTagTable()->saveGroupTag($objGroupTag);
							}							
						}
						$this->getGroupTagTable()->deleteAllGroupTags($group_id,$post['tags']);
						$tag_category = $this->getGroupTagTable()->getAllGroupTagCategiry($group_info->group_id);						 
						$arr_tags = $this->getGroupTagTable()->fetchAllGroupTags($group_info->group_id); 
						$tags = array();
						if(!empty($arr_tags)){
							foreach($arr_tags as $list){
								$tags[] =array(
									'group_tag_id'=>$list['group_tag_id'],
									'group_tag_group_id'=>$list['group_tag_group_id'],
									'tag_title'=>$serviceManager->get('translator')->translate($list['tag_title']),
									'tag_id'=>$list['tag_id'],									 
								);
							}
						}
					}else{	
						$this->getGroupTagTable()->deleteGroupTag($group_id);
					}						
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
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
	public function updateGroupAction(){
		$error = '';		
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group'];
				$group_title = $post['group_title'];
				$group_description = $post['group_description'];
				$group_type = $post['group_type'];
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					if($this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id)){
						$group_details = $this->getGroupTable()->getGroupByName($group_title);
						if(!empty($group_details)&& $group_details->group_id!=''&&$group_details->group_id!=$group_info->group_id){							 
							$error = $serviceManager->get('translator')->translate("Group name already exist");
						}else{
							$group_data['group_title'] = $group_title;
							$group_data['group_description'] = $group_description;
							$group_data['group_type'] = $group_type;
							if($this->getGroupTable()->updateGroup($group_data,$group_info->group_id)){
								;					
							}else{
								$error = $serviceManager->get('translator')->translate("Some error occurred. Please try again");
							}
						}
					}else{$error = $serviceManager->get('translator')->translate("You don't have the permissions to do this operation");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	 	 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function groupmembersAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel->setVariable('image_folders',$config['image_folders']);
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$session = New Container('language');
			$serviceManager = $this->getServiceLocator();
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$profile_data = $this->getUserTable()->getProfileDetails($identity->user_id);
			$viewModel->setVariable( 'profile_data' , $profile_data);	
			$group_seo = $this->params('group_seo');
			$this->layout()->identity = $identity;       
			$groupinfo =  $this->getGroupTable()->getGroupBySeoTitle($group_seo);		
			if(!empty($groupinfo)&&$identity->user_id){			    
				$group_details = $this->getGroupTable()->getGroupDetails($groupinfo->group_id,$identity->user_id);
				$friendsCount      = $this->getUserGroupTable()->getFriendsCount($groupinfo->group_id,$identity->user_id);
                $arrownerCount		  = $this->getUserGroupTable()->getOwnersCount($groupinfo->group_id);
				$owner = $this->getUserGroupTable()->checkOwner($groupinfo->group_id,$identity->user_id);
				$pending_reguest_count = 0;
				if($owner){
					$viewModel->setVariable( 'is_owner' , 1);
					$pending_reguest_count = $this->getUserGroupJoiningRequestTable()->countGroupMemberRequests($groupinfo->group_id)->memberCount;
				}else{$viewModel->setVariable( 'is_owner' , 0);}
				if(!empty($arrownerCount)){
					$ownerCount = $arrownerCount->group_owner_count;
				}else{$ownerCount=0;}
				$friends_count = (isset($friendsCount->friend_count))?$friendsCount->friend_count:0;
                $viewModel->setVariable( 'friend_count' , $friends_count);
				$viewModel->setVariable( 'group_details' , $group_details);
				$viewModel->setVariable( 'ownerCount' , $ownerCount);
				$viewModel->setVariable( 'pending_reguest_count' , $pending_reguest_count);
				$questionnaire = $this->getGroupJoiningQuestionnaireTable()->getQuestionnaireArray($groupinfo->group_id);
				$viewModel->setVariable( 'questionnaire' , $questionnaire);
				return $viewModel;
			}else{
				$error = $serviceManager->get('translator')->translate("User not exist in the system");
				$result = new ViewModel(array('error'=>$error));
				return $result;
			}
		}else{return $this->redirect()->toRoute('home', array('action' => 'index'));}
	}
	public function getMembersAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group_id'];	
				$page = $post['page'];
				$offset = ($page)?($page-1)*10:0;
				$type = $post['type'];
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){
					if($type == 'pending'){
						$members_list = $this->getUserGroupJoiningRequestTable()->getRequestMembers($group_info->group_id,$offset,10);
					}else{
						$members_list = $this->getUserGroupTable()->getMembers($group_info->group_id,$identity->user_id,$type,$offset,10);
					}
					 if(!empty($members_list)){
						foreach($members_list as $list){
							$tag_category = $this->getUserTagTable()->getAllUserTagCategiry($list['user_id']);
							$objcreated_group_count = $this->getUserGroupTable()->getCreatedGroupCount($list['user_id']);
							if(!empty($objcreated_group_count)){
							$created_group_count = $objcreated_group_count->created_group_count;
							}else{$created_group_count =0;}
							$is_friend = ($this->getUserFriendTable()->isFriend($list['user_id'],$identity->user_id))?1:0;
							$is_requested = ($this->getUserFriendTable()->isRequested($list['user_id'],$identity->user_id))?1:0;
							$isPending = ($this->getUserFriendTable()->isPending($list['user_id'],$identity->user_id))?1:0;
							$arr_questionnaire = array();
							if($type == 'pending'){
								$questionnaire = $this->getGroupQuestionnaireAnswersTable()->getAllQuestionswithanswers($group_info->group_id,$list['user_id']);
								foreach($questionnaire as $questions){
									$options = $this->getGroupQuestionnaireOptionsTable()->getAnswerOptions(array($questions['selected_options']));
									$arr_questionnaire[] = array(	
															'question'=>$questions['question'],
															'answer_type'=>$questions['answer_type'],
															'answer'=>$questions['answer'],
															'options'=>$options,
															);
								}
							}
							$arrMembers[] = array(
											'user_id'=>$list['user_id'],
											'user_given_name'=>$list['user_given_name'],
											'user_profile_name'=>$list['user_profile_name'],
											'country_title'=>$list['country_title'],
											'country_title_locale'=>$serviceManager->get('translator')->translate($list['country_code']),
											'country_code'=>$list['country_code'],
											'user_fbid'=>$list['user_fbid'],
											'user_register_type'=>$list['user_register_type'],
											'city'=>$list['city'],
											'city_locale'=>$serviceManager->get('translator')->translate($list['city']),
											'profile_photo'=>$list['profile_icon'],
											'tag_count' =>count($tag_category),
											'tag_category' =>$tag_category,
											'group_count'=>$list['group_count'],
											'created_group_count'=>$created_group_count,
											'is_admin'=>($type == 'pending')?0:$list['is_admin'],
											'user_group_is_owner'=>($type == 'pending')?0:$list['user_group_is_owner'],
											'user_group_role'=>($type == 'pending')?'':$list['user_group_role'],
											'is_friend'=>$is_friend,
											'is_requested'=>$is_requested,
											'isPending'=>$isPending,
											'questionnaire' =>$arr_questionnaire,
											);
						}
					 }
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error;	 
		$return_array['members'] = $arrMembers;	 		
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function ignoreJoinRequestAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost();
				$group_id = $post['group_id'];
				$user_id = $post['user_id']; 
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					$owner = $this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id);
					if($owner){
						$this->getUserGroupJoiningRequestTable()->RemoveRequest($user_id,$group_id);
						$this->getGroupQuestionnaireAnswersTable()->deleteUserAnswersOfGroup($group_id,$user_id);
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function acceptJoinRequestAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost();
				$group_id = $post['group_id'];
				$user_id = $post['user_id']; 
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					$owner = $this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id);
					if($owner){						
						$usergroup = $this->getUserGroupTable()->getUserGroup($user_id,$group_id);
						if(empty($usergroup)){
							$user_data['user_group_user_id'] = $user_id;
							$user_data['user_group_group_id'] = $group_id;
							$user_data['user_group_status'] = "available";							 
							$this->getUserGroupTable()->AddMembersTOGroup($user_data);
							$this->getUserGroupJoiningRequestTable()->ChangeStatusTOProcessed($group_id,$user_id);
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$msg = $identity->user_given_name." Accept the group joining request to the group ".$group_info->group_title;
							$subject = 'Group joining Request';
							$from = 'admin@jeera.me';
							$process = 'Accepted';
							$this->UpdateNotifications($user_id,$msg,5,$subject,$from,$identity->user_id,$group_id,$process);					 
						}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function removeuserAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group_id'];	
				$user_id = $post['user_id'];	 
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					$owner = $this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id);
					if($owner){
						if($this->getUserGroupTable()->deleteOneUserGroup($group_info->group_id,$user_id)){
						 $this->getGroupQuestionnaireAnswersTable()->deleteUserAnswersOfGroup($group_info->group_id,$user_id);
						}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function promoteadminAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group_id'];	
				$user_id = $post['user_id'];	 
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					$owner = $this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id);
					if($owner){
						if($this->getUserGroupTable()->updateUserRoles($user_id,$group_info->group_id,1)){
							$config = $this->getServiceLocator()->get('Config');
							$base_url = $config['pathInfo']['base_url'];
							$msg = $identity->user_given_name."promoted you as an admin to the group ".$group_info->group_title;
							$subject = 'Group admin Promoted';
							$from = 'admin@jeera.me';
							$process = 'Promoted';
							$this->UpdateNotifications($user_id,$msg,9,$subject,$from,$identity->user_id,$group_id,$process);
								 
						}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function revokeadminAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group_id'];	
				$user_id = $post['user_id'];	 
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){	
					$owner = $this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id);
					if($owner){
						if($this->getUserGroupTable()->updateUserRoles($user_id,$group_info->group_id,0)){
						;
						}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}
					}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	public function updateQuestionnaireAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();			
			if ($request->isPost()){
				$post = $request->getPost(); 
				$group_id = $post['group_id'];					
				$group_info = $this->getGroupTable()->getPlanetinfo($group_id);	
				if(!empty($group_info)&&$group_info->group_id!=''){					
					$owner = $this->getUserGroupTable()->checkOwner($group_info->group_id,$identity->user_id);
					if($owner){
						 $type = $post['type'];	 
						 $question = $post['question'];	
						 $option1	 = $post['option1'];
						 $option2	 = $post['option2'];
						 $option3	 = $post['option3'];
						 $questionId = $post['questionId'];
						 $ndex = 0;
						 $arr_questionnaire = array();
						 foreach($type  as $questype){  
							switch($questype){
								case 'Textarea':   
									if($question[$ndex]==''||$question[$ndex]=='undefined'){
										$error = $serviceManager->get('translator')->translate("Add questions");
									}else{
										$arr_questionnaire[] = array('answer_type'=>'Textarea',
																	'group_id'=>$group_id,
																	'question'=>$question[$ndex],
																	'question_status'=>'active',
																	'added_user_id'=>$identity->user_id,
																	'questionId'=>(isset($questionId[$ndex]))?$questionId[$ndex]:'',
																);
									}
								break;
								case 'checkbox':
									$option = array();
									if($ndex==0){
										$option = $option1;
									}
									if($ndex==1){
										$option = $option2;
									}
									if($ndex==2){
										$option = $option3;
									}
									if($question[$ndex]==''||$question[$ndex]=='undefined'){
										$error = $serviceManager->get('translator')->translate("Add questions");
									}else if(count($option)<2){
										$error = $serviceManager->get('translator')->translate("Add options");
									}else{
										
										$arr_questionnaire[] = array('answer_type'=>'checkbox',
																	'group_id'=>$group_id,
																	'question'=>$question[$ndex],
																	'question_status'=>'active',
																	'added_user_id'=>$identity->user_id,
																	'questionId'=>$questionId[$ndex],
																	'option'=>array_filter($option),
																	 
																);
									}
								break;
								case 'radio':
									$option = array();
									if($ndex==0){
										$option = $option1;
									}
									if($ndex==1){
										$option = $option2;
									}
									if($ndex==2){
										$option = $option3;
									}
									if($question[$ndex]==''||$question[$ndex]=='undefined'){
										$error = $serviceManager->get('translator')->translate("Add questions");
									}else if(count($option)<2){
										$error = $serviceManager->get('translator')->translate("Add options");
									}else{										
										$arr_questionnaire[] = array('answer_type'=>'radio',
																	'group_id'=>$group_id,
																	'question'=>$question[$ndex],
																	'question_status'=>'active',
																	'added_user_id'=>$identity->user_id,
																	'questionId'=>$questionId[$ndex],
																	'option'=>array_filter($option),									 
										);
									}
								break;
								case 'Select Question Type':
									if($questionId[$ndex]!=''){
										$this->getGroupJoiningQuestionnaireTable()->DeleteQuestions($questionId[$ndex]);
										$this->getGroupQuestionnaireOptionsTable()->DeleteOptions($questionId[$ndex]);
									}
								break;
							}
							$ndex++;
						 }
						 if($error == ""){
							foreach($arr_questionnaire as $list){
								if($list['questionId']!=''&&$list['questionId']!='undefined'&&$list['questionId']>0){
									$question = $this->getGroupJoiningQuestionnaireTable()->getQuestionFromQuestionId($list['questionId']);
									if(!empty($question)){
										switch($question->answer_type){
											case 'Textarea': 
												$qdata['question'] = $list['question'];
												$qdata['answer_type'] = $list['answer_type'];
												$this->getGroupJoiningQuestionnaireTable()->updateQuestion($qdata,$question->questionnaire_id);
												if($list['answer_type']!='Textarea'){	
													foreach( $list['option'] as $options){
														$addedOption = array();
														$addedOption    = array(
															'question_id'   => $question->questionnaire_id,
															'option'        => $options,
														);
														$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
													}													
												}												 
											break;
											case 'radio':
												$qdata['question'] = $list['question'];
												$qdata['answer_type'] = $list['answer_type'];
												$this->getGroupJoiningQuestionnaireTable()->updateQuestion($qdata,$question->questionnaire_id);
												if($list['answer_type']=='Textarea'){	
													$this->getGroupQuestionnaireOptionsTable()->DeleteOptions($question->questionnaire_id);
												}else{
													$options = $this->getGroupQuestionnaireOptionsTable()->getoptionOfOneQuestion($question->questionnaire_id);
													$i=0;
													foreach( $list['option'] as $opt){
														if(count($options)>$i){
															$data['option'] = $opt;
															$this->getGroupQuestionnaireOptionsTable()->UpdateOptions($opt,$options[$i]['option_id']);
														}else{
															$addedOption = array();
															$addedOption    = array(
																'question_id'   => $question->questionnaire_id,
																'option'        => $opt,
															);
															$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
														}
														$i++;
													}
													if(count($options)>$i){
														for($i;$i<count($options);$i++){
															$this->getGroupQuestionnaireOptionsTable()->DeleteSingleOptions($options[$i]['option_id']);
														}
													}
												}												
											break;
											case 'checkbox':
												$qdata['question'] = $list['question'];
												$qdata['answer_type'] = $list['answer_type'];
												$this->getGroupJoiningQuestionnaireTable()->updateQuestion($qdata,$question->questionnaire_id);
												if($list['answer_type']=='Textarea'){	
													$this->getGroupQuestionnaireOptionsTable()->DeleteOptions($question->questionnaire_id);
												}else{
													$options = $this->getGroupQuestionnaireOptionsTable()->getoptionOfOneQuestion($question->questionnaire_id);
													$i=0; 
													foreach( $list['option'] as $opt){  
														if(count($options)>$i){ 
															$data['option'] = $opt;
															$this->getGroupQuestionnaireOptionsTable()->UpdateOptions($opt,$options[$i]['option_id']);
														}else{ 
															$addedOption = array();
															$addedOption    = array(
																'question_id'   => $question->questionnaire_id,
																'option'        => $opt,
															);
															$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
														}
														$i++;
													}													 
													if(count($options)>$i){
														for($i;$i<count($options);$i++){
															$this->getGroupQuestionnaireOptionsTable()->DeleteSingleOptions($options[$i]['option_id']);
														}
													}
												}
											break;
										}
									}else{
										switch($list['answer_type']){
											case 'Textarea': 
												$addedQuestion      = array(
													'group_id'            => $list['group_id'],
													'question'            => $list['question'],
													'question_status'     => 'active',
													'added_ip'            => $_SERVER["SERVER_ADDR"],
													'added_user_id'       => $identity->user_id,
													'answer_type'         => $list['answer_type'],
											   );
											   $intQuestionId = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);
											break;
											case 'radio':
												$addedQuestion      = array(
													'group_id'            => $list['group_id'],
													'question'            => $list['question'],
													'question_status'     => 'active',
													'added_ip'            => $_SERVER["SERVER_ADDR"],
													'added_user_id'       => $identity->user_id,
													'answer_type'         => $list['answer_type'],
												);
												$intQuestionId = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);
												foreach( $list['option'] as $opt){													 
													$addedOption = array();
													$addedOption    = array(
														'question_id'   => $question->questionnaire_id,
														'option'        => $opt,
													);
													$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
												}												
											break;
											case 'checkbox':
												$addedQuestion      = array(
													'group_id'            => $list['group_id'],
													'question'            => $list['question'],
													'question_status'     => 'active',
													'added_ip'            => $_SERVER["SERVER_ADDR"],
													'added_user_id'       => $identity->user_id,
													'answer_type'         => $list['answer_type'],
												);
												$intQuestionId = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);
												foreach( $list['option'] as $opt){													 
													$addedOption = array();
													$addedOption    = array(
														'question_id'   => $question->questionnaire_id,
														'option'        => $opt,
													);
													$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
												}
											break;
										}
									}
								}else{
									switch($list['answer_type']){
										case 'Textarea': 
											$addedQuestion      = array(
												'group_id'            => $list['group_id'],
												'question'            => $list['question'],
												'question_status'     => 'active',
												'added_ip'            => $_SERVER["SERVER_ADDR"],
												'added_user_id'       => $identity->user_id,
												'answer_type'         => $list['answer_type'],
										   );
										   $intQuestionId = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);
										break;
										case 'radio':
											$addedQuestion      = array(
												'group_id'            => $list['group_id'],
												'question'            => $list['question'],
												'question_status'     => 'active',
												'added_ip'            => $_SERVER["SERVER_ADDR"],
												'added_user_id'       => $identity->user_id,
												'answer_type'         => $list['answer_type'],
											);
											$intQuestionId = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);
											foreach( $list['option'] as $opt){													 
												$addedOption = array();
												$addedOption    = array(
													'question_id'   => $question->questionnaire_id,
													'option'        => $opt,
												);
												$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
											}												
										break;
										case 'checkbox':
											$addedQuestion      = array(
												'group_id'            => $list['group_id'],
												'question'            => $list['question'],
												'question_status'     => 'active',
												'added_ip'            => $_SERVER["SERVER_ADDR"],
												'added_user_id'       => $identity->user_id,
												'answer_type'         => $list['answer_type'],
											);
											$intQuestionId = $this->getGroupJoiningQuestionnaireTable()->AddQuestion($addedQuestion);
											foreach( $list['option'] as $opt){													 
												$addedOption = array();
												$addedOption    = array(
													'question_id'   => $question->questionnaire_id,
													'option'        => $opt,
												);
												$intQOptionId	= $this->getGroupQuestionnaireOptionsTable()->AddOptions($addedOption);
											}
										break;
									}
								}
							}
						}
					}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
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
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost(); 
				if(!empty($post)){						 
					$img =$post['imageData'];  
					if($img!=''){
						$group_id = $post['group_id'];
						$group  = $this->getGroupTable()->getPlanetinfo($post['group_id']);
						if(!empty($group)){
							
							$img = str_replace('data:image/png;base64,', '', $img);
							$img = str_replace(' ', '+', $img);
							$data = base64_decode($img);
							$config = $this->getServiceLocator()->get('Config');
							$temp_path = $config['pathInfo']['temppath'];
							$user_temp_path = $temp_path.$identity->user_id."/";	
							$files = glob($user_temp_path); 
							foreach($files as $file){ 
								if(is_file($file))
								@unlink($file);  
							}
							$imagePath_dir = $config['pathInfo']['ROOTPATH'].'/public/datagd/group/'.$group->group_id.'/';
							$mediumimagePath_dir = $config['pathInfo']['ROOTPATH'].'/public/datagd/group/'.$group->group_id.'/medium';
							$avtarimagePath_dir = $config['pathInfo']['ROOTPATH'].'/public/datagd/group/'.$group->group_id.'/avtar/';
							$filename = 'group_'.$group->group_id.''.time().'.png';	
							$imagePath = $config['pathInfo']['ROOTPATH'].'/public/datagd/group/'.$group->group_id.'/'.$filename;
							$mediumimagePath = $config['pathInfo']['ROOTPATH'].'/public/datagd/group/'.$group->group_id.'/medium/'.$filename;
							if(!is_dir($imagePath_dir)){							
								mkdir($imagePath_dir);
							} 
							if(!is_dir($mediumimagePath_dir)){							
								mkdir($mediumimagePath_dir);
							}	
							if(!is_dir($avtarimagePath_dir)){							
								mkdir($avtarimagePath_dir);
							}
							$avtarimage_name = '';
							if(file_put_contents($imagePath, $data)){
								if($post['group_banner_new']==1&&isset($_FILES['orginalImage'])&&$_FILES['orginalImage']['name']!=''){
									$avtarimage_name = time().$_FILES['orginalImage']['name'];
									@move_uploaded_file($_FILES["orginalImage"]["tmp_name"], $avtarimagePath_dir .$avtarimage_name);
								}
								$resize = new ResizeImage($imagePath);
								$resize->resizeTo(380, 214, 'maxWidth');								
								$resize->saveImage($mediumimagePath);
								
								$group_photo =  $this->getGroupPhotoTable()->getGalexyPhoto($group->group_id);
								$groupphoto  = new GroupPhoto();
								$previous_image = '';
								if(!empty($group_photo)&&$group_photo->group_photo_id!=''){									
									$groupphoto->group_photo_id = $group_photo->group_photo_id;
									$previous_image = $group_photo->group_photo_photo;
									$previous_avatar = $group_photo->group_photo_orginal;
								}
								$groupphoto->group_photo_group_id  = $group->group_id;
								$groupphoto->group_photo_photo = $filename;
								
								if($avtarimage_name != ''){
									$groupphoto->group_photo_orginal = $avtarimage_name;
								}else{
									$groupphoto->group_photo_orginal = $group_photo->group_photo_orginal;
								}							
								$intGroupPhotoId  = $this->getGroupPhotoTable()->savePhoto($groupphoto);
								if($intGroupPhotoId){
									if($previous_image!=''){	
										@unlink($config['pathInfo']['ROOTPATH'].'/public/datagd/groups/'.$group->group_id.'/'.$groupphoto->group_photo_photo);
										@unlink($config['pathInfo']['ROOTPATH'].'/public/datagd/groups/'.$group->group_id.'/medium/'.$groupphoto->group_photo_photo);
									}
									if($avtarimage_name != ''){
										@unlink($config['pathInfo']['ROOTPATH'].'/public/datagd/groups/'.$group->group_id.'/avatar/'.$previous_avatar);
									}
								}else{$error = $serviceManager->get('translator')->translate("Some error occurred.Please try again");}	
							}							 
						}else{$error = $serviceManager->get('translator')->translate("Group not available");}												 
					}else{$error = $serviceManager->get('translator')->translate("Image not available");}
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;	
	}
	
	public function mediaviewAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel->setVariable('image_folders',$config['image_folders']);
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$session = New Container('language');			 
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$identity = $auth->getIdentity();
			$profile_data = $this->getUserTable()->getProfileDetails($identity->user_id);
			$viewModel->setVariable( 'profile_data' , $profile_data);	
			$group_seo = $this->params('group_seo');
			$this->layout()->identity = $identity;       
			$groupinfo =  $this->getGroupTable()->getGroupBySeoTitle($group_seo);		
			if(!empty($groupinfo)&&$groupinfo->group_id){
				$group_details = $this->getGroupTable()->getGroupDetails($groupinfo->group_id,$identity->user_id);
				$viewModel->setVariable( 'group_details' , $group_details);
				$media_id =  $this->params('id');
				$arr_group_media = array();
				if(!empty($media_id)){
					$group_media = $this->getGroupMediaTable()->getOneMedia($media_id);
					$is_admin = 0;
					if($this->getUserGroupTable()->checkOwner($group_media->group_id,$group_media->user_id)){
						$is_admin = 1;
					}
					$is_logged_user_admin = 0;
					if($this->getUserGroupTable()->checkOwner($group_media->group_id,$identity->user_id)){
						$is_logged_user_admin = 1;
					}
					if(!empty($group_media)){
						if($group_media->media_added_group_id == $groupinfo->group_id){
							$media_contents = $this->getGroupMediaContentTable()->getMediaContents(json_decode($group_media->media_content));
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
							$SystemTypeData = $this->getGroupTable()->fetchSystemType("Media");
							$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$media_id,$identity->user_id);
							$like_count = $like_details->likes_counts;		
							$arr_likedUsers = array();						
							if(!empty($like_details)){  
								$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$media_id,$identity->user_id,2,0);
								$arr_likedUsers = array();
								if($like_details['is_liked']==1){
									$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
								}
								if($like_details['likes_counts']>0&&!empty($liked_users)){
									foreach($liked_users as $likeuser){
										$arr_likedUsers[] = $likeuser['user_given_name'];
									}
								}
								 
							}	
							$commet_users = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$media_id,$identity->user_id);
							 
							$arr_group_media = array(
											'media_files' => $media_files,
											'group_media_id' => $group_media->group_media_id,
											'media_content' => $group_media->media_content,
											'media_caption' => $group_media->media_caption,
											'added_time' =>$this->timeAgo($group_media->media_added_date),
											'group_id' => $group_media->group_id,
											'group_title' => $group_media->group_title,
											'group_seo_title' => $group_media->group_seo_title,
											'user_id' => $group_media->user_id,
											'user_given_name' => $group_media->user_given_name,
											'user_first_name' => $group_media->user_first_name,
											'user_last_name' => $group_media->user_last_name,
											'user_profile_name' => $group_media->user_profile_name,
											'user_fbid' => $group_media->user_fbid,
											'profile_photo' => $group_media->profile_photo,
											'arr_likedUsers' => $arr_likedUsers,
											'like_count' =>$like_details['likes_counts'],
											'is_liked' =>$like_details['is_liked'],
											'comment_count' =>$commet_users['comment_counts'],
											'is_commented' =>$commet_users['is_commented'],
											'is_admin'=>$is_admin,
											'time'=>$this->timeAgo($group_media->media_added_date),
											'is_logged_user_admin'=>$is_logged_user_admin,
											);
								
							$viewModel->setVariable( 'arr_group_media' , $arr_group_media);
							return $viewModel;
						}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }								
					}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }							  	
				}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
			}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
		}else{return $this->redirect()->toRoute('home', array('action' => 'nopage'));}
	}
	public function albumviewAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel->setVariable('image_folders',$config['image_folders']);
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$session = New Container('language');			 
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$profile_data = $this->getUserTable()->getProfileDetails($identity->user_id);
			$viewModel->setVariable( 'profile_data' , $profile_data);	
			$group_seo = $this->params('group_seo');
			$this->layout()->identity = $identity;       
			$groupinfo =  $this->getGroupTable()->getGroupBySeoTitle($group_seo);		
			if(!empty($groupinfo)&&$groupinfo->group_id){
				$group_details = $this->getGroupTable()->getGroupDetails($groupinfo->group_id,$identity->user_id);
				$viewModel->setVariable( 'group_details' , $group_details);
				$album_id =  $this->params('id');
				$arr_group_media = array();
				if(!empty($album_id)){
					$album_data = $this->getGroupAlbumTable()->getAlbum($album_id);
					if(!empty($album_data)){
						$event_name = '';
						$event_id = '';								 
						$event_details  = $this->getGroupEventAlbumTable()->getAlbumEvents($album_id);
						$is_event_creator = 0;
						if(!empty($event_details)){
							$event_name = $event_details->group_activity_title;
							$event_id = $event_details->group_activity_id;
							if( $event_details->group_activity_owner_user_id == $identity->user_id){
								$is_event_creator = 1;
							}
						}
						$EventAlbumDetails = array('event_name'=>$event_name,
													'event_id'=>$event_id,
													'is_event_creator'=>$is_event_creator
											);
						$viewModel->setVariable( 'album_data' , $album_data);
						$viewModel->setVariable( 'EventAlbumDetails' , $EventAlbumDetails);						
						$album_creator_info = $this->getUserTable()->getProfileDetails($album_data->creator_id);
						$group_media = $this->getGroupMediaTable()->getAllAlbumFiles($album_id);
						$media_content  = array();
						if(!empty($group_media)){
							foreach($group_media as $media){
								$media_ids = json_decode($media['media_content']);
								$media_content = array_merge($media_content,$media_ids );
							}
						}						
						$is_admin = 0;
						if($this->getUserGroupTable()->checkOwner($groupinfo->group_id,$album_data->creator_id)){
							$is_admin = 1;
						}
						$is_logged_user_admin = 0;
						if($this->getUserGroupTable()->checkOwner($groupinfo->group_id,$identity->user_id)){
							$is_logged_user_admin = 1;
						}
						if(!empty($media_content)){						 
							$media_contents = $this->getGroupMediaContentTable()->getMediaContents($media_content);
							$media_files = [];
							$media_count = 0;
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
								$media_count++;
								if($media_count>5){
									break;
								}
							}
							$SystemTypeData = $this->getGroupTable()->fetchSystemType("Album");
							$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$album_id,$identity->user_id);
							$like_count = $like_details->likes_counts;		
							$arr_likedUsers = array();						
							if(!empty($like_details)){  
								$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$album_id,$identity->user_id,2,0);
								$arr_likedUsers = array();
								if($like_details['is_liked']==1){
									$arr_likedUsers[] = 'you';
								}
								if($like_details['likes_counts']>0&&!empty($liked_users)){
									foreach($liked_users as $likeuser){
										$arr_likedUsers[] = $likeuser['user_given_name'];
									}
								}
								 
							}	
							$commet_users = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$album_id,$identity->user_id);							 
							$arr_group_media = array(
											'media_files' => $media_files,
											'group_album_id' => $album_data->album_id,
											'album_title' => $album_data->album_title,
											'media_contents' => $media_content,
											'album_description' => $album_data->album_description,
											'added_time' =>$this->timeAgo($album_data->created_date),
											'group_id' => $groupinfo->group_id,
											'group_title' => $groupinfo->group_title,
											'group_seo_title' => $groupinfo->group_seo_title,
											'user_id' => $album_creator_info->user_id,
											'user_given_name' => $album_creator_info->user_given_name,
											'user_first_name' => $album_creator_info->user_first_name,
											'user_last_name' => $album_creator_info->user_last_name,
											'user_profile_name' => $album_creator_info->user_profile_name,
											'user_fbid' => $album_creator_info->user_fbid,
											'profile_photo' => $album_creator_info->profile_photo,
											'arr_likedUsers' => $arr_likedUsers,
											'like_count' =>$like_details['likes_counts'],
											'is_liked' =>$like_details['is_liked'],
											'comment_count' =>$commet_users['comment_counts'],
											'is_commented' =>$commet_users['is_commented'],
											'is_admin'=>$is_admin,
											'time'=>$this->timeAgo($album_data->created_date),
											'is_logged_user_admin'=>$is_logged_user_admin,
											);
								
							$viewModel->setVariable( 'arr_group_media' , $arr_group_media);
							return $viewModel;										
						}else{ $viewModel->setVariable( 'arr_group_media' , $arr_group_media);}
					}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
				}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
			}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
		}else{return $this->redirect()->toRoute('home', array('action' => 'nopage'));}
	}
	public function imageviewAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$serviceManager = $this->getServiceLocator();
		$viewModel->setVariable('image_folders',$config['image_folders']);
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$profile_data = $this->getUserTable()->getProfileDetails($identity->user_id);
			$viewModel->setVariable( 'profile_data' , $profile_data);	
			$group_seo = $this->params('group_seo');
			$this->layout()->identity = $identity;   
			$session = New Container('language');			 
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$groupinfo =  $this->getGroupTable()->getGroupBySeoTitle($group_seo);		
			if(!empty($groupinfo)&&$groupinfo->group_id){
				$group_details = $this->getGroupTable()->getGroupDetails($groupinfo->group_id,$identity->user_id);
				$viewModel->setVariable( 'group_details' , $group_details);
				$media_id =  $this->params('media_id');
				$image_id =  $this->params('image_id');
				$arr_group_media = array();
				if(!empty($media_id)&&!empty($image_id)){
					$group_media = $this->getGroupMediaTable()->getOneMedia($media_id);
					$is_admin = 0;
					if($this->getUserGroupTable()->checkOwner($group_media->group_id,$group_media->user_id)){
						$is_admin = 1;
					}
					$is_logged_user_admin = 0;
					if($this->getUserGroupTable()->checkOwner($group_media->group_id,$identity->user_id)){
						$is_logged_user_admin = 1;
					}
					if(!empty($group_media)){
						if($group_media->media_added_group_id == $groupinfo->group_id){
							$media_items  = json_decode($group_media->media_content);
							if(in_array($image_id, $media_items)){
								$media_contents = $this->getGroupMediaContentTable()->getMediafile($image_id);
								 
								$media_files = [];								 
								if($media_contents->media_type == 'youtube'){
									$media_files[] = array(
											'id'=>$media_contents->media_content_id,
											'files'=>$media_contents->content,
											'video_id'=>$this->get_youtube_id_from_url($media_contents->content),
											'media_type'=>$media_contents->media_type,
											);
								}else{
									$media_files[] = array(
													'id'=>$media_contents->media_content_id,
													'files'=>$media_contents->content,
													'media_type'=>$media_contents->media_type,
													);
								}
								$SystemTypeData = $this->getGroupTable()->fetchSystemType("Image");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$image_id,$identity->user_id);
								$like_count = $like_details->likes_counts;		
								$arr_likedUsers = array();						
								if(!empty($like_details)){  
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$image_id,$identity->user_id,2,0);
									$arr_likedUsers = array();
									if($like_details['is_liked']==1){
										$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}	
								$commet_users = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$image_id,$identity->user_id);
								if(count($media_items)==1){
									$next_item ='';
									$prev_item = '';
								}else{
									$i=0;
									foreach($media_items as $items){
										if($items == $image_id){
											if($i==0){
												$next_item =$media_items[$i+1];
												$prev_item = '';
											}else if($i==count($media_items)-1){
												$next_item ='';
												$prev_item = $media_items[$i-1];
											}else{
												$next_item =$media_items[$i+1];
												$prev_item = $media_items[$i-1];
											}
										}
										$i++;
									}
								}
								 
								$arr_group_media = array(
												'media_files' => $media_files,
												'group_media_id' => $group_media->group_media_id,
												'media_content' => $group_media->media_content,
												'media_caption' => $group_media->media_caption,
												'added_time' =>$this->timeAgo($group_media->media_added_date),
												'group_id' => $group_media->group_id,
												'group_title' => $group_media->group_title,
												'group_seo_title' => $group_media->group_seo_title,
												'user_id' => $group_media->user_id,
												'user_given_name' => $group_media->user_given_name,
												'user_first_name' => $group_media->user_first_name,
												'user_last_name' => $group_media->user_last_name,
												'user_profile_name' => $group_media->user_profile_name,
												'user_fbid' => $group_media->user_fbid,
												'profile_photo' => $group_media->profile_photo,
												'arr_likedUsers' => $arr_likedUsers,
												'like_count' =>$like_details['likes_counts'],
												'is_liked' =>$like_details['is_liked'],
												'comment_count' =>$commet_users['comment_counts'],
												'is_commented' =>$commet_users['is_commented'],
												'next_id' =>$next_item,
												'prev_id' =>$prev_item,
												'is_admin'=>$is_admin,
												'time'=>$this->timeAgo($group_media->media_added_date),
												'is_logged_user_admin'=>$is_logged_user_admin,
												'image_id'=>$image_id,
												);
									
								$viewModel->setVariable( 'arr_group_media' , $arr_group_media);
							return $viewModel;
							}else{
								return $this->redirect()->toRoute('home/404', array('action' => 'nopage'));
							}							 
						}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }								
					}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }							  	
				}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
			}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
		}else{return $this->redirect()->toRoute('home', array('action' => 'nopage'));}
	}
	public function albumimageviewAction(){
		$error = '';
		$auth = new AuthenticationService();
		$viewModel = new ViewModel();
		$config = $this->getServiceLocator()->get('Config');
		$viewModel->setVariable('image_folders',$config['image_folders']);
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$this->layout('layout/layout_user');
			$identity = $auth->getIdentity();
			$session = New Container('language');			 
			$locales_selected = (isset($session->language))?$session->language:''; 
			$viewModel->setVariable('locales_selected', $locales_selected);	
			$this->layout()->locales_selected = $locales_selected;
			$profile_data = $this->getUserTable()->getProfileDetails($identity->user_id);
			$viewModel->setVariable( 'profile_data' , $profile_data);	
			$group_seo = $this->params('group_seo');
			$this->layout()->identity = $identity;       
			$groupinfo =  $this->getGroupTable()->getGroupBySeoTitle($group_seo);		
			if(!empty($groupinfo)&&$groupinfo->group_id){
				$group_details = $this->getGroupTable()->getGroupDetails($groupinfo->group_id,$identity->user_id);
				$viewModel->setVariable( 'group_details' , $group_details);
				$album_id =  $this->params('album_id');
				$image_id =  $this->params('image_id');
				$arr_group_media = array();
				if(!empty($album_id)&&!empty($image_id)){
					$album_data = $this->getGroupAlbumTable()->getAlbum($album_id);
					
					if(!empty($album_data)){
						
						$group_media = $this->getGroupMediaTable()->getAllAlbumFiles($album_id);
						$media_content  = array();
						if(!empty($group_media)){
							foreach($group_media as $media){
								$media_ids = json_decode($media['media_content']);
								$media_content = array_merge($media_content,$media_ids );
							}
						}
						if(in_array($image_id, $media_content)){
							$media_contents = $this->getGroupMediaContentTable()->getMediafile($image_id);
							$media_data =  $this->getGroupMediaTable()->getMediaFromContent($image_id);
							$group_media = $this->getGroupMediaTable()->getOneMedia($media_data->group_media_id);
							$is_admin = 0;
							if($this->getUserGroupTable()->checkOwner($groupinfo->group_id,$group_media->user_id)){
								$is_admin = 1;
							}
							$is_logged_user_admin = 0;
							if($this->getUserGroupTable()->checkOwner($groupinfo->group_id,$identity->user_id)){
								$is_logged_user_admin = 1;
							}
								$media_files = [];								 
								if($media_contents->media_type == 'youtube'){
									$media_files[] = array(
											'id'=>$media_contents->media_content_id,
											'files'=>$media_contents->content,
											'video_id'=>$this->get_youtube_id_from_url($media_contents->content),
											'media_type'=>$media_contents->media_type,
											);
								}else{
									$media_files[] = array(
													'id'=>$media_contents->media_content_id,
													'files'=>$media_contents->content,
													'media_type'=>$media_contents->media_type,
													);
								}
								$SystemTypeData = $this->getGroupTable()->fetchSystemType("Image");
								$like_details  = $this->getLikeTable()->fetchLikesCountByReference($SystemTypeData->system_type_id,$image_id,$identity->user_id);
								$like_count = $like_details->likes_counts;		
								$arr_likedUsers = array();						
								if(!empty($like_details)){  
									$liked_users = $this->getLikeTable()->likedUsersWithoutLoggedOneWithFriendshipStatus($SystemTypeData->system_type_id,$image_id,$identity->user_id,2,0);
									$arr_likedUsers = array();
									if($like_details['is_liked']==1){
										$arr_likedUsers[] = $serviceManager->get('translator')->translate('you');
									}
									if($like_details['likes_counts']>0&&!empty($liked_users)){
										foreach($liked_users as $likeuser){
											$arr_likedUsers[] = $likeuser['user_given_name'];
										}
									}
									 
								}	
								$commet_users = $this->getCommentTable()->fetchCommentCountByReference($SystemTypeData->system_type_id,$image_id,$identity->user_id);
								if(count($media_content)==1){
									$next_item ='';
									$prev_item = '';
								}else{
									$i=0;
									foreach($media_content as $items){
										if($items == $image_id){
											if($i==0){
												$next_item =$media_content[$i+1];
												$prev_item = '';
											}else if($i==count($media_content)-1){
												$next_item ='';
												$prev_item = $media_content[$i-1];
											}else{
												$next_item =$media_content[$i+1];
												$prev_item = $media_content[$i-1];
											}
										}
										$i++;
									}
								}
								 
								$arr_group_media = array(
												'media_files' => $media_files,
												'album_id' => $album_id,
												'media_content' => $media_content,
												'media_caption' => '',
												'added_time' =>$this->timeAgo($album_data->created_date),
												'group_id' => $groupinfo->group_id,
												'group_title' => $groupinfo->group_title,
												'group_seo_title' => $groupinfo->group_seo_title,
												'user_id' => $group_media->user_id,
												'user_given_name' => $group_media->user_given_name,
												'user_first_name' => $group_media->user_first_name,
												'user_last_name' => $group_media->user_last_name,
												'user_profile_name' => $group_media->user_profile_name,
												'user_fbid' => $group_media->user_fbid,
												'profile_photo' => $group_media->profile_photo,
												'arr_likedUsers' => $arr_likedUsers,
												'like_count' =>$like_details['likes_counts'],
												'is_liked' =>$like_details['is_liked'],
												'comment_count' =>$commet_users['comment_counts'],
												'is_commented' =>$commet_users['is_commented'],
												'next_id' =>$next_item,
												'prev_id' =>$prev_item,
												'is_admin'=>$is_admin,
												'time'=>$this->timeAgo($group_media->media_added_date),
												'is_logged_user_admin'=>$is_logged_user_admin,
												'image_id'=>$image_id,
												);
									
								$viewModel->setVariable( 'arr_group_media' , $arr_group_media);
							return $viewModel;
						}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage'));}	
						 								
					}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }							  	
				}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
			}else{ return $this->redirect()->toRoute('home/404', array('action' => 'nopage')); }
		}else{return $this->redirect()->toRoute('home', array('action' => 'nopage'));}
	}
	
	public function getFriendsNotMemberOfGroupListAction(){
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost(); 
				if(!empty($post)){				 
					$group_id = $post['group_id'];
					$group  = $this->getGroupTable()->getPlanetinfo($group_id);
					if(!empty($group)){						 
						$search_string = $post['search_str'];
						$arrMembers = $this->getUserGroupTable()->getFriendsNotMemberOfGroup($group_id,$identity->user_id,$search_string,0,250);
					}else{$error = $serviceManager->get('translator')->translate("Group not available");}				 
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$return_array['members'] = $arrMembers; 	
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	public function inviteMembersAction(){		
		$error = '';
		$arrMembers = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost(); 
				if(!empty($post)){				 
					$group_id = $post['group_id'];
					$group  = $this->getGroupTable()->getPlanetinfo($group_id);
					$friends = $post['users'];
					if(!empty($group)){	
						if($group->group_type=='private'){
							$owner = $this->getUserGroupTable()->checkOwner($group->group_id,$identity->user_id);
							if($owner){
								if($friends == 'All'){
									$all_members = $this->getUserGroupTable()->getFriendsNotMemberOfGroup($group_id,$identity->user_id,'');
									foreach($all_members as $items){
										$arrMembers[] = $items['user_id'];
									}
								}else{								 
									foreach($friends as $items){
										$arrMembers[] = $items['user_id'];
									}
								}
								$UserGroupJoiningInvitation                 = new UserGroupJoiningInvitation();
								foreach($arrMembers as $group_invt){
									$invite =  $this->getGroupJoiningInvitationTable()->checkInvited($group_invt, $group_id);
									if(empty($invite)){
										$UserGroupJoiningInvitation->user_group_joining_invitation_sender_user_id           = $identity->user_id;
										$UserGroupJoiningInvitation->user_group_joining_invitation_receiver_id              = $group_invt;
										$UserGroupJoiningInvitation->user_group_joining_invitation_status                   = "active";
										$UserGroupJoiningInvitation->user_group_joining_invitation_ip_address               = $_SERVER["SERVER_ADDR"];
										$UserGroupJoiningInvitation->user_group_joining_invitation_group_id                 = $group_id;
										$intUserGroupJoiningInvitation   = $this->getGroupJoiningInvitationTable()->saveUserGroupJoiningInvite($UserGroupJoiningInvitation);
										if( $intUserGroupJoiningInvitation){
											$config = $this->getServiceLocator()->get('Config');
											$base_url = $config['pathInfo']['base_url'];
											$msg = $identity->user_given_name." invited you to join the group ".$group->group_title;
											$subject = 'Group joining invitation';
											$from = 'admin@jeera.me';
											$process = 'Invite';
											$this->UpdateNotifications($group_invt,$msg,3,$subject,$from,$identity->user_id,$group_id,$process);
										}
									}
								}
							}else{
								$error =$serviceManager->get('translator')->translate("Sorry, You need to be a member of the group to interact with the posts");
							}
						}else{
							if($this->getUserGroupTable()->is_member($identity->user_id,$group_id)){
							
								if($friends == 'All'){
									$all_members = $this->getUserGroupTable()->getFriendsNotMemberOfGroup($group_id,$identity->user_id,'');
									foreach($arrMembers as $items){
										$arrMembers[] = $items->user_id;
									}
								}else{								 
									foreach($friends as $items){
										$arrMembers[] = $items['user_id'];
									}
								}
								$UserGroupJoiningInvitation                 = new UserGroupJoiningInvitation();
								foreach($arrMembers as $group_invt){
									$invite =  $this->getGroupJoiningInvitationTable()->checkInvited($group_invt, $group_id);
									if(empty($invite)){
										$UserGroupJoiningInvitation->user_group_joining_invitation_sender_user_id           = $identity->user_id;
										$UserGroupJoiningInvitation->user_group_joining_invitation_receiver_id              = $group_invt;
										$UserGroupJoiningInvitation->user_group_joining_invitation_status                   = "active";
										$UserGroupJoiningInvitation->user_group_joining_invitation_ip_address               = $_SERVER["SERVER_ADDR"];
										$UserGroupJoiningInvitation->user_group_joining_invitation_group_id                 = $group_id;
										$intUserGroupJoiningInvitation   = $this->getGroupJoiningInvitationTable()->saveUserGroupJoiningInvite($UserGroupJoiningInvitation);
										if( $intUserGroupJoiningInvitation){
											$config = $this->getServiceLocator()->get('Config');
											$base_url = $config['pathInfo']['base_url'];
											$msg = $identity->user_given_name." invited you to join the group ".$group->group_title;
											$subject = 'Group joining invitation';
											$from = 'admin@jeera.me';
											$process = 'Invite';
											$this->UpdateNotifications($group_invt,$msg,3,$subject,$from,$identity->user_id,$group_id,$process);
										}
									}
								}
							}else{$error =  $serviceManager->get('translator')->translate("Sorry, You need to be a member of the group to interact with the posts");}
						}						 
					}else{$error = $serviceManager->get('translator')->translate("Group not available");}				 
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		 
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
		$error = '';	
		$friends = array();
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost(); 
				if(!empty($post)){				 
					$group_id = $post['group_id'];					 
					$page = (isset($post['page'])&&$post['page']!=null&&$post['page']!=''&&$post['page']!='undefined')?$post['page']:1;
					$limit =10;
					$page =($page>0)?$page-1:0;
					$offset = $page*$limit;
					$group_info = $this->getGroupTable()->getPlanetinfo($group_id);				
					if(!empty($group_info)&&$group_info->group_id!=''){						 
						$type = 'Friends';
						$friends = $this->getUserGroupTable()->getMembers($group_id,$identity->user_id,$type,$offset,$limit); 
					}else{$error = $serviceManager->get('translator')->translate("Unable to locate the group");}	 			 
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");} 
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 	
		$return_array['friends'] = $friends; 			 			
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
	
	public function deleteGroupAction(){
		$error = '';		
		$auth = new AuthenticationService();
		$members = 0;
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();			  
			$request   = $this->getRequest();
			if ($request->isPost()){
				$post = $request->getPost(); 
				if(!empty($post)){				 
					$group_id = $post['group_id'];							
					if($group_id!=''){ 
						$group  = $this->getGroupTable()->getPlanetinfo($group_id);						
						if(!empty($group)){							
							if($this->getUserGroupTable()->checkOwner($group_id,$identity->user_id)){
								$members_count = $this->getUserGroupTable()->countGroupMembers($group_id);
								if(!empty($members_count))
								$members = $members_count->memberCount;
								if($members<25){
									$data['group_status'] = 'deleterequest';
									$this->getGroupTable()->updateGroup($data,$group_id);
								}else{
									$system_type= $this->getAdminAlertSystemsTable()->getAlertSystem("Group");
									$request_hystory = $this->getAdminAlertTable()->checkGroupDeleteRequestExistOrNot($system_type->system_id,$group_id,$identity->user_id);
									if(empty($request_hystory)){
										$alertdata = array();
										$alertdata['system_id'] = $system_type->system_id;
										$alertdata['refrence_id'] = $group_id; 
										$alertdata['user_id'] = $identity->user_id;
										$alertdata['alert_msg'] = $identity->user_given_name." requested to delete his group ".$group->group_title ;
										$alertdata['alert_status'] ='unread';
										$alertdata['alert_process'] ='group delete';
										$Adminalerts = new Adminalerts();
										$Adminalerts->exchangeArray($alertdata);
										$this->getAdminAlertTable()->saveAdminalerts($Adminalerts);
									}else{
										$error = $serviceManager->get('translator')->translate("You are already requested");
									}									
								}
							}else{$error = $serviceManager->get('translator')->translate("You don't have the permissions to do this");}						 
						}else{$error = $serviceManager->get('translator')->translate("Given group not existing in this system");}
					}else{$error = $serviceManager->get('translator')->translate("Forms are incomplete. Some values are missing");}	  			 
				}else{$error = $serviceManager->get('translator')->translate("Unable to process");}
			}else{$error = $serviceManager->get('translator')->translate("Unable to process");} 
		}else{$error = $serviceManager->get('translator')->translate("Your session expired, please log in again to continue");}
		$return_array= array();		 
		$return_array['process_status'] = (empty($error))?'success':'failed';
		$return_array['process_info'] = $error; 			
		$result = new JsonModel(array(
		'return_array' => $return_array,      
		));		
		return $result;
	}
		
	public function get_youtube_id_from_url($url){
		if (stristr($url,'youtu.be/'))
			{preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID); return isset($final_ID[4])?$final_ID[4]:''; }
		else 
			{@preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|channel\/)([a-z_A-Z0-9\-]{11})/i', $url, $IDD); return isset($IDD[5])?$IDD[5]:''; }
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
	public function getGroupTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupTable = (!$this->groupTable)?$sm->get('Groups\Model\GroupsTable'):$this->groupTable;    
    }
	public function getUserGroupTable(){
		$sm = $this->getServiceLocator();
		return  $this->userGroupTable = (!$this->userGroupTable)?$sm->get('Groups\Model\UserGroupTable'):$this->userGroupTable;  
	}
	public function getGroupPhotoTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupPhotoTable = (!$this->groupPhotoTable)?$sm->get('Groups\Model\GroupPhotoTable'):$this->groupPhotoTable;  
	}
	public function getUserTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTable = (!$this->userTable)?$sm->get('User\Model\UserTable'):$this->userTable;    
	}
	public function getUserFriendTable(){
		$sm = $this->getServiceLocator();
		return  $this->userFriendTable = (!$this->userFriendTable)?$sm->get('User\Model\UserFriendTable'):$this->userFriendTable;    
	}	
	public function getGroupTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupTagTable = (!$this->groupTagTable)?$sm->get('Tag\Model\GroupTagTable'):$this->groupTagTable;    
    }
	public function getGroupMediaTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupMediaTable = (!$this->groupMediaTable)?$sm->get('Groups\Model\GroupMediaTable'):$this->groupMediaTable;    
    }
	public function getUserNotificationTable(){         
		$sm = $this->getServiceLocator();
		return  $this->userNotificationTable = (!$this->userNotificationTable)?$sm->get('Notification\Model\UserNotificationTable'):$this->userNotificationTable;    
    }	 
    public function getGroupJoiningInvitationTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupJoiningInvitationTable = (!$this->groupJoiningInvitationTable)?$sm->get('Groups\Model\UserGroupJoiningInvitationTable'):$this->groupJoiningInvitationTable;
    }
    public function getGroupJoiningQuestionnaireTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupJoiningQuestionnaire = (!$this->groupJoiningQuestionnaire)?$sm->get('Groups\Model\GroupJoiningQuestionnaireTable'):$this->groupJoiningQuestionnaire;
    }
    public function getGroupQuestionnaireOptionsTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupQuestionnaireOptions = (!$this->groupQuestionnaireOptions)?$sm->get('Groups\Model\GroupQuestionnaireOptionsTable'):$this->groupQuestionnaireOptions;
    }
	public function getCommentTable(){
		$sm = $this->getServiceLocator();
		return  $this->commentTable = (!$this->commentTable)?$sm->get('Comment\Model\CommentTable'):$this->commentTable;   
	}
	public function getLikeTable(){         
		$sm = $this->getServiceLocator();
        return  $this->likeTable = (!$this->likeTable)?$sm->get('Like\Model\LikeTable'):$this->likeTable;       
    }
	public function getUserGroupJoiningRequestTable(){         
		$sm = $this->getServiceLocator();
        return  $this->userGroupJoiningRequestTable = (!$this->userGroupJoiningRequestTable)?$sm->get('Groups\Model\UserGroupJoiningRequestTable'):$this->userGroupJoiningRequestTable;       
    }
	public function getGroupQuestionnaireAnswersTable(){
		$sm = $this->getServiceLocator();
        return  $this->groupQuestionnaireAnswersTable = (!$this->groupQuestionnaireAnswersTable)?$sm->get('Groups\Model\GroupQuestionnaireAnswersTable'):$this->groupQuestionnaireAnswersTable; 
	}
	public function getTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->tagTable = (!$this->tagTable)?$sm->get('Tag\Model\TagTable'):$this->tagTable;    
	}
	public function getUserTagTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTagTable = (!$this->userTagTable)?$sm->get('Tag\Model\UserTagTable'):$this->userTagTable;    
	}
	public function getActivityTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupActivityTable = (!$this->groupActivityTable)?$sm->get('Activity\Model\ActivityTable'):$this->groupActivityTable;    
    }
	public function getDiscussionTable(){
		$sm = $this->getServiceLocator();
		return  $this->discussionTable = (!$this->discussionTable)?$sm->get('Discussion\Model\DiscussionTable'):$this->discussionTable;   
    }
	public function getActivityRsvpTable(){
		$sm = $this->getServiceLocator();
		return  $this->activityRsvpTable = (!$this->activityRsvpTable)?$sm->get('Activity\Model\ActivityRsvpTable'):$this->activityRsvpTable;
    }
	public function getGroupMediaContentTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupMediaContentTable = (!$this->groupMediaContentTable)?$sm->get('Groups\Model\GroupMediaContentTable'):$this->groupMediaContentTable;    
    }
	public function getGroupAlbumTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupAlbumTable = (!$this->groupAlbumTable)?$sm->get('Album\Model\GroupAlbumTable'):$this->groupAlbumTable;    
    }
	public function getGroupEventAlbumTable(){
		$sm = $this->getServiceLocator();
		return  $this->groupEventAlbumTable = (!$this->groupEventAlbumTable)?$sm->get('Album\Model\GroupEventAlbumTable'):$this->groupEventAlbumTable;    
    }	 
	public function getAdminAlertSystemsTable(){
		$sm = $this->getServiceLocator();
		return  $this->adminAlertSystemsTable = (!$this->adminAlertSystemsTable)?$sm->get('Admin\Model\AdminalertSystemTable'):$this->adminAlertSystemsTable; 
	}
	public function getAdminAlertTable(){
		$sm = $this->getServiceLocator();
		return  $this->adminAlertTable = (!$this->adminAlertTable)?$sm->get('Admin\Model\AdminalertsTable'):$this->adminAlertTable; 
	}
}