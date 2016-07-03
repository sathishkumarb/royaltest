 <script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<?php //print_r($profile_data); ?>
     <!-- Left Fixed part -->
   <?php echo $this->profileWidget; ?>
    <!-- Left Fixed part Close -->
<div id="group_right_container" ng-app="groupapp" ng-cloak >
	<div ng-controller="groupController">
    <div class="group_banner" style="background:url({{group_picture}}) no-repeat center top;background-size:100%" ng-if="imageEdit ==0" >
    	<img src="<?php echo $this->basePath(); ?>/public/images/banner-shadow.png" alt="" class="group_banner-shadow" />
        
        <!-- Left side Group Brief Starts -->
        <div class="group_brief_outer">
        	<div class="group_details-left">
                <h3>About This Group</h3>
                <div ng-if="editGroupFlag==0"><div ng-show="editGroupFlag==0" id="about_group_container" ng-init="enableGroupScrollbar()" style="">{{group.group_description}}</div></div>
				<p ><textarea name="Edit-group_about" class="edit-group_about border_radius" ng-if="editGroupFlag==1" ng-model="fromData.groupDescription"> </textarea>
				</p>
                <div class="group_categories-outer">
                    <h3>Group Interests
					<a href="javascript:void(0);" ng-if="group.is_admin==1" data-toggle="modal" data-target="#interest_popup" ng-click="loadPopup()">Add / Remove Interests</a><div class="clear"></div>
					</h3>
                    <div class="group_interest-icons">
							<img  alt="" ng-repeat="tagitem in grouptagCategory" ng-if="tagitem.tag_category_icon!=''" src="{{categoryImagePath}}{{tagitem.tag_category_icon}}" alt="{{tagitem.tag_category_title}}" />
                            <img ng-repeat="tagitem in grouptagCategory" ng-if="tagitem.tag_category_icon==''" src="<?php echo $this->basePath(); ?>/public/images/category-icon.png" alt="{{tagitem.tag_category_title}}" /> 
							 
					</div>
                    <div class="interest_list_grouphead">
                        <span ng-repeat="tags in grouptags | limitTo:10" ng-if="checkCommonIntrests(tags.tag_id)" style="text-transform:capitalize;" class="interest_common"> {{tags.tag_title}}<i>.</i></span>
						 <span ng-repeat="tags in grouptags| limitTo:10" ng-if="!checkCommonIntrests(tags.tag_id)" style="text-transform:capitalize;"> {{tags.tag_title}}<i>.</i></span>
						 <a href="javascript:void(0)" ng-if="grouptags.length>10" data-toggle="modal" data-target="#interestlist_popup" ng-click="loadGroupInterestList()">Show more</a>
                    </div>
                </div>
            </div>
            <div class="group_brief_footer">
            	<div class="group_founder">founded <span>{{group.group_added_timestamp}}</span></div>
                <div class="group_brief-location">
                	<div id="location-text">in <span><i class="white-location"></i>{{group.city}}, {{group.country_code}}</span></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <!-- Left side Group Brief ends -->
        
        <div class="group_name-banner">
        	<div class="group_name-banner-inner">
            	<span>
                	<span ng-if="editGroupFlag==0">{{group.group_title}}</span>
					<span ng-if="group.is_admin==1&&editGroupFlag==1">
						<input name="Group_name-edit" class="border_radius group_name-edit" type="text" value="" ng-model="fromData.groupTitle" />
						<span class="group_join"><a href="javascript:void(0);" class="default_butn_grey" ng-click="cancelGroupEdit()">Cancel</a><a href="javascript:void(0);" class="default_butn_blue" data-toggle="modal" data-target="#confirm_popup" ng-click="updateGroup(fromData)">Save Changes</a></span>
						 
						<span class="group_questionary"><a href="javascript:void(0);" data-toggle="modal" data-target="#edit_question" ng-click="loadQuestionnaire(group.group_id)"><i></i>Edt group questionnaire</a></span>
					</span>
					<span class="group_join" ng-if="group.is_admin==1&&editGroupFlag==0"><a href="javascript:void(0)" class="default_butn_yellow" ng-click="enableEditForm()" >Edit Group</a></span>
                    <span class="group_join" ng-if="group.is_member==0&&group.is_admin!=1 &&group.group_status!='pending'&&group.group_type !='private'">
						<a href="javascript:void(0);" class="default_butn_blue" data-toggle="modal" data-target="#join_group" ng-click="selectJoiningGroup(group.group_id,group.group_title,group.group_type,group.group_seo_title)" >Join Group</a></span>
					 
					<span class="group_join" ng-if="group.group_status=='pending'&&group.is_admin!=1"><a href="javascript:void(0);" class="default_butn_grey"   >Pending Approval</a></span>
					<span class="group_join" ng-if="group.is_member==0&&group.is_admin!=1 &&group.group_status!='pending'&&group.is_requested==1"><a href="javascript:void(0);" class="default_butn_grey"   >Already requested</a></span>
					<span class="group_join" ng-if="group.is_member==0&&group.is_admin!=1 &&group.group_status!='pending'&&group.is_requested!=1&&group.is_invited==1&&group.group_type =='private'"><a href="javascript:void(0);" class="default_butn_blue" data-toggle="modal" data-target="#join_group" ng-click="selectJoiningGroup(group.group_id,group.group_title,group.group_type,group.group_seo_title)" >Join Group</a></span>
					<p ng-if="editGroupFlag==0">
					<span class="group_type" ng-if="group.group_type =='open'"><i class="group-open"></i>Open Group</span>
					<span class="group_type" ng-if="group.group_type =='private'"><i class="group-private"></i>Private Group</span>
					<span class="group_type" ng-if="group.group_type =='public'"><i class="group-public"></i>Public Group</span>  
					</p>
					<span class="group_type group_type-edit" ng-if="group.is_admin==1&&editGroupFlag==1">
                     <a href="javascript:void(o);" class="filter_group" data-toggle="dropdown" id="group_type"><i class="group-{{fromData.groupTypeEdit}}"></i>{{fromData.groupTypeEdit}} Group<span class="edit_type_arrow"></span></a>
                            <ul class="dropdown-menu color-trans arrow_box2 group_type_dd" role="menu" aria-labelledby="group_type">
                                <li><a href="javascript:void(0);" ng-class="{'active':fromData.groupTypeEdit =='public'}" ng-click="fromData.groupTypeEdit ='public'"><i class="grouptype_public"></i>Public Group</a></li>
                                <li><a href="javascript:void(0);" ng-class="{'active':fromData.groupTypeEdit =='open'}" ng-click="fromData.groupTypeEdit ='open'"><i class="grouptype_open"></i>Open Group</a></li>
                                <li><a href="javascript:void(0);" ng-class="{'active':fromData.groupTypeEdit =='private'}" ng-click="fromData.groupTypeEdit ='private'"><i class="grouptype_private"></i>Private Group</a></li>
                            </ul>
                    </span>					
                </span>
            </div>
        </div>
		<div class="change-group_banner" ng-if="group.is_admin==1">
        	<a href="javascript:void(0);"  data-toggle="dropdown" >
            	<span>
                    <i class="change_cover"></i><br/>
                    Change
                </span>
            </a>
			<ul class="dropdown-menu banner_dropdown color-trans arrow_box arrow_right" role="menu" aria-labelledby="update_banner">
				<li><a href="javascript:void(0);" ng-click="EditBanner()" id="bannerEditor">Edit Photo</a></li>
                <li><a href="javascript:void(0);" ng-click="EditBanner()" id="bannerUpload">Upload Photo</a></li>
                <li><a href="javascript:void(0);" data-toggle="modal" data-target="#bannerdelete_popup" ng-if="group.group_photo_photo!=null&&group.group_photo_photo!=''" >Remove</a></li>
            </ul>
        </div>
        <div class="change-group_banner group_settings_dropdown" ng-if="group.is_admin!=1">
        	<a href="javascript:void(0);"  data-toggle="dropdown" >
            	<img src="<?php echo $this->basePath(); ?>/public/images/arrow_options.png" />
            </a>
			<ul class="dropdown-menu color-trans arrow_box arrow_right" role="menu" aria-labelledby="update_banner">
				<li ng-if="group.is_member==1">
					<span class="group_join" ng-if="group.is_member==1&&group.is_admin!=1"><a href="javascript:void(0);" class="leave_group_option" data-toggle="modal" data-target="#leaveGroup_popup" ng-click="selectLeavingGroup(group.group_id,group.group_title)" >Leave this group</a></span>
				</li>
				 <li >
					<span class="group_join"><a  data-toggle="modal" data-target="#reportGroup" ng-click="groups_ReportPost('Group',group.group_id)" data-toggle="modal" >Report this group</a></span>
				</li>
            </ul>
        </div>
        <div class="group_members-outer" >
        	<div class="group_members_images"> 
				<div class="comn_profile_img pending_request-link" ng-if="group.group_type!='private'"><span class="vertical_align"></span>
                	<a href="javascript:void(0)" data-toggle="modal" data-target="#invite_group" ng-click="resetInviteForm()"><span class="req_no">+</span></a>
                   
                </div>	
				<div class="comn_profile_img pending_request-link" ng-if="group.group_type=='private'&&group.is_admin==1"><span class="vertical_align"></span>
                	<a href="javascript:void(0)" data-toggle="modal" data-target="#invite_group" ng-click="resetInviteForm()"><span class="req_no">+</span></a>
                    
                </div>
				<div class="comn_profile_img pending_request-link" ng-if="group.is_admin==1&&group.request_count>0"><span class="vertical_align"></span>
                	<a href="<?php echo $this->basePath(); ?>/groups/{{group.group_seo_title}}/members"><span class="req_no">+{{group.request_count}}</span></a>
                    <div class="pending_req_notification border_radius arrow_box-bottom">Pending requests</div>
                </div>				 
            	<div class="comn_profile_img group_member_banner"ng-repeat="members in groupUsers">
					<a href="<?php echo $this->basePath(); ?>/{{members.user_profile_name}}">
					<img alt="" ng-if="members.profile_photo!=null&&members.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{members.user_id}}/{{ members.profile_photo }}">
					<img alt="" ng-if="(members.profile_photo==null||members.profile_photo=='')&&(members.user_fbid!=null&&members.user_fbid!='')" src="https://graph.facebook.com/{{members.user_fbid}}/picture?width=66&&height=66">
					<img alt="" ng-if="(members.profile_photo==null||members.profile_photo=='')&&(members.user_fbid==null||members.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg"></a>
				</div> 			               
            </div>
            <div class="group_member-text">
            	members: <span>{{group.member_count}}</span>    <span>/</span>    friends: <span>{{group.friend_count}}</span>        <a href="<?php echo $this->basePath(); ?>/groups/{{group.group_seo_title}}/members">SEE ALL</a>
            </div>
        </div>
        
    </div><!-- Banner Close --->
   <div   class="group_banner group_banner_edit" ng-if="imageEdit ==1" style="height:auto" > 
		<div class="image-editor-group">
			<input type="file" class="cropit-image-input" style="display:none" id="file_bannerimage_upload">
			<div class="cropit-image-preview"></div>
			
			<div class="range_outer">
			<div class="image-size-label">
				Resize image
			</div>
			<input type="range" class="cropit-image-zoom-input">
			</div>
			<div class="cropButton">
			<a href="javascript:void(0);" class="default_butn_red" ng-click="cancelcropImage()">Cancel</a>			 
			<a href="javascript:void(0);"  class="default_butn_blue" ng-click="cropGroupImage()">Apply</a>
			</div>
		</div>
   </div>
   <div class="clear"></div>
	  <div class="pop_bg modal fade" id="interest_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog interest_popup">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="clear"></div>
				  </div>
				  <div class="modal-body signup_body">
					<div class="step1_outer">
						<h3>Add More Interests</h3>
						<div class="interest_category-outer dropdown_icons-outer" ng-switch on="intrest.switch">
							<ul class="color-trans dropdown_icons border_radius {{drpClass}}" ng-click="dropdownProcess()"  ng-switch-when="1">
								<li   ng-repeat="item in tagCategory" ng-show="isVisible==1 || $index==selectedIndex"   ng-class="{active:$index==selectedIndex}" >
								<a href="javascript:void(0);" ng-click="selectCategory(item.tag_category_id,$index)">
								<img ng-if="item.tag_category_icon!=''" src="{{categoryImagePath}}{{item.tag_category_icon}}" alt="{{item.tag_category_title}}" />
								<img ng-if="item.tag_category_icon==''" src="<?php echo $this->basePath(); ?>/public/images/category-icon.png" alt="{{item.tag_category_title}}" /> 
								{{item.tag_category_title}}</a>
								</li>								 								 						
							</ul>	
							<div ng-switch-when="2" class="loading"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> </div>
						</div>
						<div class="interest_search-outer" >
							<div class="fixed-search interest_search">
								<input type="text" placeholder="Search Interest" name="search" ng-model="tag_search" >
								<a href="javascript:void(0);"></a>
							</div>
						</div>
						<div class="clear"></div>
						<div class="popup_interest-lists" ng-show="showtags"  >
							<a class="interest_add" remove-on-click href="javascript:void(0);"    ng-repeat="items in filtered = (tags | filter:tag_search) | limitTo:tag_limit" ng-click="addToSelected(items.category_id,items.tag_id,items.tag_title);remove()">{{items.tag_title}}<span></span></a>
                            <a ng-show="showmoretags" ng-click="tag_limit = tag_limit + tag_inc_limit" ng-init="tag_limit = tag_inc_limit" ng-hide="filtered.length <= tag_limit">
                                More...
                            </a>
						</div>
						<h3>Your Interests</h3>
						<div class="selected_interest">
						<img  ng-repeat="items in objSelectedCategory"   ng-if="items.category_icon!=''" src="{{categoryImagePath}}{{items.category_icon}}" alt="{{items.category_title}}" />
						<img ng-repeat="items in objSelectedCategory" ng-if="items.category_icon==''" src="<?php echo $this->basePath(); ?>/public/images/category-icon.png" alt="{{items.category_title}}" /> </div>
						<div class="popup_interest-lists">
							<a class="interest_added" href="javascript:void(0);" ng-repeat="items in objSelectedTags" remove-on-click ng-click="remove();removeFromSelected(items.tag_name,items.category)" >{{items.tag_name}}<span></span></a>
							 
						</div>
						
						<div class="login_button">
							<a href="javascript:void(0);" class="default_butn_blue" ng-click="AddToIntrest()">Done Editing</a>
						</div>
					</div>
				 </div>
				  
				</div>
			  </div>
			</div>
		<div class="pop_bg modal fade" id="interestlist_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog interest_popup">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="clear"></div>
				  </div>
				  <div class="modal-body signup_body">
					<div class="step1_outer">					 
						<h3>Your Interests</h3>
						<div class="selected_interest">
							<img  alt="" ng-repeat="tagitem in grouptagCategory" ng-if="tagitem.tag_category_icon!=''" src="{{categoryImagePath}}{{tagitem.tag_category_icon}}" alt="{{tagitem.tag_category_title}}" />
                            <img ng-repeat="tagitem in grouptagCategory" ng-if="tagitem.tag_category_icon==''" src="<?php echo $this->basePath(); ?>/public/images/category-icon.png" alt="{{tagitem.tag_category_title}}" /> 
						</div>
						<div class="popup_interest-lists">
							<span ng-repeat="tags in grouptags" ng-if="checkCommonIntrests(tags.tag_id)" style="text-transform:capitalize;" class="interest_common"> {{tags.tag_title}}<i>.</i></span>
							<span ng-repeat="tags in grouptags" ng-if="!checkCommonIntrests(tags.tag_id)" style="text-transform:capitalize;"> {{tags.tag_title}}<i>.</i></span>						  					 
						</div>					 
					</div>
				 </div>
				  
				</div>
			  </div>
			</div> 			
			 <div class="pop_bg modal fade" id="bannerdelete_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			  <div class="modal-dialog banner_popup">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<div class="clear"></div>
				  </div>
				  <div class="modal-body signup_body">
					<div class="step1_outer">
						<h3>Please Confirm</h3>
						<div class="bannerdelete-outer dropdown_icons-outer">
							 You are about to delete a banner image of <span>{{group.group_title}}</span> group. Banner image is important as it personalizes a group and makes it more recognizable among the users.
								Are you sure you want to do this?
						</div>				 
						
						<div class="login_button">
							<a href="javascript:void(0);" class="default_butn_blue" ng-click="removeGroupBanner()">Yes I'm sure</a>
							<a href="javascript:void(0);" class="default_butn_red" ng-click="cancelRemove()">No</a>
						</div>
					</div>
				 </div>
				  
				</div>
			  </div>
			</div>
			 <div class="pop_bg modal fade" id="edit_question" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog group_popup">
        <div class="modal-content">
          <div class="modal-header">
            
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
           <div ng-if="ajaxloadQuestionnaire==1"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"></div>            
            <div class="step1_outer" ng-if="ajaxloadQuestionnaire==0">                
                <div class="create_group-label"><strong>Edit Questionnaire</strong></div>
				 
				<div ng-repeat="a in range() track by $index">
                    <div class="create_group-label">Question {{$index+1}}</div>
                    <div class="group_select_col-left">
						 <select ng-model="selectedType[$index]" class="styled borderslct" ng-options="item as item for item in QuestionType"   >
							<option value="">Select Question Type</option>
						</select>
                        <input type="hidden" ng-model="questionId[$index]" />
                     </div>
                      <div class="clear"></div>
                        <div class="login-field">
                            <textarea ng-model="question[$index]"  placeholder="Type Your Question"></textarea>
                        </div>
                        <div ng-if="selectedType[$index]=='checkbox'||selectedType[$index]=='radio'">
                            <div class="clear"></div>
                            <div class="group_select_col-left"  >
                                <input name="" type="text" placeholder="Type Your Options"  ng-model="option1[$index]" />
                            </div>
                            <div class="clear"></div>
                            <div class="group_select_col-left">
                                <input name="" type="text" placeholder="Type Your Options" ng-model="option2[$index]" />
                            </div>
                            <div class="clear"></div>
                            <div class="group_select_col-left">
                                <input name="" type="text" placeholder="Type Your Options" ng-model="option3[$index]" />
                            </div>
                        </div>

                     <div class="clear"></div>
                </div>
                 
                
                <div class="signup_button">
                    <div class="signup-link" ng-if="ajaxUpdateQuistionnaire==0">
						<a href="javascript:void(0);" class="default_butn_blue" id="" ng-click="SaveQuestionniare(group.group_id)">Save</a>
						<a href="javascript:void(0);" class="default_butn_grey" id="" ng-click="cancelQuestionniare()">Cancel</a>
					</div>
					<div class="signup-link" ng-if="ajaxUpdateQuistionnaire==1"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"></div>
                    <div class="clear"></div>
                </div>
                
            </div>
            
          </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="leaveGroup_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
			<div id="signup_step1">
				<div class="step1_outer">
					<h3>Please Confirm</h3>
					 <div class="already_member"> You are about to leave the group <span>{{selectedGroupRemoved}}</span>.Are you sure?
					 <div class="leave_group_btn">
					 <a href="javascript:void(0);" class="default_butn_blue" ng-click="leaveGroup()">Yes I'm sure</a>
					 <a href="javascript:void(0);" class="default_butn_red" id="cancel_reply" ng-click="showReply=0;removeLeaveBox()" data-dismiss="modal">No</a>
					 </div>
					</div>
				</div>
			</div>
          </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="join_group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog join_group_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
            <div class="step1_join" id="step_1" ng-if="(profile.user_profile_about_me==''||profile.user_profile_about_me==null||profile.user_profile_about_me=='null')&&joingroupStep==1">
                <h3>Introduce Yourself</h3>
                <div class="already_member signup_po-link">Looks like you haven’t written your bio, why don’t you introduce yourself to the group? *You can edit this later on your profile page<br />&nbsp;</div>
                <div class="login-field introduce_textarea">
                    <textarea name="Introduction" placeholder="Tell something interesting about yourself" ng-model="aboutme"></textarea>
                </div>
                
                <div class="login_button">
                    <a href="javascript:void(0);" class="default_butn_normal" ng-click="profile.user_profile_about_me='';closeJoinWindow();">Cancel</a>
					<div ng-if="aboutmeAjax==1" class="loading"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> </div>
                    <a ng-if="aboutmeAjax==0" href="javascript:void(0);" class="default_butn_blue" id="step_2_join" ng-click="SaveAboutMe(aboutme)">Continue</a>
                </div>
            </div>
            <div class="step1_join" id="step_2" ng-if="joingroupStep==2">
            	<h3>Upload Your Photo</h3>
                <div class="upload_photo-outer">
                	<div class="browse_photo profile_img-edit">
                    	<a href="javascript:void(0);"  data-toggle="modal" data-target="#upload_profile-image" ng-click="loadProfileUpload()">
                            <span>
                                <i class="change_cover"></i><br>
                                Upload
                            </span>
                        </a>
                    </div>
                    <div class="photo_details">Looks like you haven’t uploaded your profile photo. You can do it now (this improves your credibility on Jeera). <br />*You can change this later on your profile page.</div>
                    <div class="clear"></div>
                </div>
                <div class="login_button">                    
                    <a href="javascript:void(0);" class="default_butn_blue" id="step_3_join" ng-click="gotoGroupJoiningStep3()">Continue</a>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="step1_join" id="step_3"  ng-if="joingroupStep==3">
				<h2 class="question_title">How about knowing a bit more about you?</h2>
				<span ng-repeat="items in  joingingquestionnaire">
			   <h3>{{items.question}}</h3>
                <div class="login-field introduce_textarea" ng-if="items.answer_type == 'Textarea'">
                    <textarea name="answer" placeholder="Your Answer" ng-model="md_questionnaire[$index]"></textarea>
                </div>				                 
                <div class="login-field introduce_textarea" ng-if="items.answer_type == 'radio'">
                    <div class="select_group-type join_radio" ng-repeat="option in items.options">
                    	<input type="radio" value="{{option.option_id}}"  name="group_type_{{items.questionnaire_id}}" id="Public-group-{{$parent.$index}}{{$index}}" ng-click="selectedOptionRadio(items.questionnaire_id,option.option_id)">
                        <label for="Public-group-{{$parent.$index}}{{$index}}"><i class="radio-styled"></i> {{option.option}}</label>
                        <div class="clear"></div>
                    </div>
                     
                </div>
				
				<div class="login-field introduce_textarea" ng-if="items.answer_type == 'checkbox'">
                   
                       <div class="checkbox_outer">
						<ul class="notify_list">
							<li ng-repeat="option in items.options"><a href="javascript:void(0)" ng-click="selectedOptionCheckbox(items.questionnaire_id,option.option_id,$index)">
							<i class="check_blue" ng-if="!checkInList(items.questionnaire_id,option.option_id,$index)"></i>
							<i class="check_blue_checked" ng-if="checkInList(items.questionnaire_id,option.option_id,$index)"></i>
							{{option.option}}</a></li>
						</ul>                        
                    </div>  
                        <div class="clear"></div>
                     
                </div>				
			</span>				
                <div class="login_button">
					<div ng-if="aboutmeAjax==1" class="loading"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> </div>
                    <a href="javascript:void(0);" ng-if="aboutmeAjax!=1" class="default_butn_blue" id="step_4_join" ng-click="saveQuestionnaireAnswers()">Continue</a>
                    <div class="clear"></div>
                </div>
            </div>
            
            <div class="step1_join" id="step_4"  ng-if="joingroupStep==4">
               <h3 ng-if="JoiningItemType == 'public'||JoiningItemType == 'private'">Your Request has been sent, and Is Being Considered by <br />The Group Owners.</h3>
				<h3 ng-if="JoiningItemType == 'open'">Thank you for joining this group.</h3>
                
                
                <div class="login_button">
                    <a href="<?php echo $this->basePath(); ?>/explore" class="default_butn_yellow">Show Me Some More Similar Groups</a>
                    <a href="javascript:void(0);" class="default_butn_blue" id="" ng-click="reloadPage()">Continue</a>
                </div>
            </div>
            
            
         </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="upload_profile-image" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog upload-img_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
                <div class="step1_outer">
                    <h3>Position and Size Your Photo</h3>
                    <div class="profile-pic_preview_outer">
                    	<div class="image-editor">
                              <div class="browse-profile_image"><input type="file" class="cropit-image-input"></div>
                              <!-- .cropit-image-preview-container is needed for background image to work -->
                              <div class="cropit-image-preview-container">
                                <div class="cropit-image-preview"></div>
                              </div>
                              <div class="image_zoom-bar">
                              	<input type="range" class="cropit-image-zoom-input">
                              </div>
                              <div class="upload-img_butns">
                              	<a href="javascript:void(0);" class="default_butn_red" ng-click="cancelImageUpload()">Cancel</a>
								<div ng-if="aboutmeAjax==1" class="loading"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> </div>
                                <a href="javascript:void(0);"  ng-if="aboutmeAjax==0" class="default_butn_blue" ng-click="cropImage()">Apply</a>
                              </div>
                              
                        </div>
                    </div>
                    
                </div>
            
          </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="invite_group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
			<div id="signup_step1">
				<div class="step1_outer">
					<h3 >Invite People To Group</h3>
					<div class="invited_names comn_profile_img" ng-repeat="item in objSelectedUsers" ng-click="RemoveFromInvitedUsers(item.user_id)">
							<img alt="" ng-if="item.user_image!=null&&item.user_image!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{item.user_id}}/{{ item.user_image }}">
							<img alt="" ng-if="(item.user_image==null||item.user_image=='')&&(item.user_fbid!=null&&item.user_fbid!='')" src="https://graph.facebook.com/{{item.user_fbid}}/picture?width=66&&height=66">
							<img alt="" ng-if="(item.user_image==null||item.user_image=='')&&(item.user_fbid==null||item.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">{{item.user_name}}<span></span>
						</div>
					<div class="login-field">
						<div class="fixed-search invite_search">
							<input name="name" id="invite_list_friend"  ng-keypress="getAllfriendsNotAMember()" type="text" ng-model="friend_search" placeholder="+ Add more people" ng-focus="enablelist=1"   />
							<a href="javascript:void(0);"></a>
						</div>
						<div class="add-all_friends">
						<a ng-click="selectInviteAll()" href="javascript:void(0)">
						<i class="check_blue " ng-if="inviteAll!=1"></i>
						<i class="check_blue_checked" ng-if="inviteAll==1"></i>
						 Add all my friends
						</a>
						</div>
						<div class="listed_members_container" id="listed_members_container" ng-if="filteredfriends.length>0"  style="display:none">
							<div id="listed_members_container_inner" ng-init="enableScrollbarInvite()">
							<ul id="invite_list_friend_ul">
								<li ng-repeat="item in filteredfriends" class="invite_user_container" remove-on-click ng-click="AddToInvitedUsers(item.user_id,item.user_given_name,item.profile_photo,item.user_fbid);remove();" id="myfriend{{$index}}">
									<div class="profile_imaage comn_profile_img">
										<img alt="" ng-if="item.profile_photo!=null&&item.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{item.user_id}}/{{ item.profile_photo }}">
										<img alt="" ng-if="(item.profile_photo==null||item.profile_photo=='')&&(item.user_fbid!=null&&item.user_fbid!='')" src="https://graph.facebook.com/{{item.user_fbid}}/picture?width=66&&height=66">
										<img alt="" ng-if="(item.profile_photo==null||item.profile_photo=='')&&(item.user_fbid==null||item.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</div>
									<div class="invite_user_name">{{item.user_given_name}}</div>	
								</li>
							</ul>
							</div>							
							<div ng-if="filteredfriends.length==0" class="matching_profile_messages">No matching profiles</div>
						</div>
					<div class="invited_friends">
						 
						<div class="login_button">
						<a ng-click="sendInvite()"   class="default_butn_blue" href="javascript:void(0);">Invite</a>
						</div>
					</div>
					</div>
				</div>
			</div>
          </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="reportGroup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
			<div id="signup_step1" ng-if="groups_ajax_loader_reportpost==1">
				<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> 
			</div>
			<div id="signup_step1" ng-if="groups_ajax_loader_reportpost==0">
				<div class="step1_outer">
					<div class="select_group-type" ng-repeat="items in groups_reasons">			 
                    	<input type="radio" name="spam_type" id="{{items.reason_id}}" value="{{items.reason_id}}" ng-click="groups_selectReason(items.reason_id,items.reason)" ng-model="groups_spam.reason_id">
                        <label for="{{items.reason_id}}"><i class="radio-styled"></i> <i class="group-public_black"></i>{{items.reason}}</label>                       
                        <div class="clear"></div>
                    </div>
					<div class="login-field" ng-if="groups_enableReportReason==1">
					<textarea placeholder="Please type your reason" ng-model="groups_spam.otherReason"></textarea>
					</div>
					<div class="clear"></div>
					<div class="signup_button">                        
                         <a ng-click="groups_sendReport(groups_spam)" id="go_step-2" class="default_butn_blue" href="javascript:void(0);">Send</a></div>
                        <div class="clear"></div>
                    </div>
					<div class="clear"></div>
				</div> 
			</div>
          </div>
          
        </div>
      </div>
   </div> 
</div>
 
<div id="feedapp" ng-app="feed_app" ng-cloak>    
	<div ng-controller="feedsController">
	<div ng-if="hideAll==0">
    <div class="feed-filter_outer">
    	<div class="home-container">
        	<div class="row">
            	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                	<div class="feed-refresh"><a href="javascript:void(0);" ng-click="getFeeds(0)"><img src="<?php echo $this->basePath(); ?>/public/images/refresh-icon.png" alt="" /></a></div>
                    <div class="show-sorting">
                    	Show: 
                        <span><a href="javascript:void(0);" ng-class="{'active': feedType=='All'}" ng-click="feedType='All';getFeeds(0);">All</a></span>
                        <span><a href="javascript:void(0);" ng-class="{'active': feedType=='Text'}" ng-click="feedType='Text';getFeeds(0);">Text</a></span>
                        <span><a href="javascript:void(0);" ng-class="{'active': feedType=='Media'}" ng-click="feedType='Media';getFeeds(0);">Media</a></span>
                        <span><a href="javascript:void(0);" ng-class="{'active': feedType=='Event'}" ng-click="feedType='Event';getFeeds(0);">Event</a></span>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                	<div class="feed-filters">
                    	Filter By:                        
                        <div class="filter_group-outer">
                            <a href="javascript:void(0);" class="filter_group" data-toggle="dropdown" id="by_activity">{{ActivityFilter}}<span></span></a>
                            <ul class="dropdown-menu sort-group_dropdown color-trans arrow_box" role="menu" aria-labelledby="by_activity">
                                <li ng-repeat="item in activityFilterList"><a href="javascript:void(0);" ng-class="{'active': ActivityFilterSelected==item.0}" ng-click="selectActivityFilters(item.0,item.1)"><i></i>{{item.1}}</a></li>                                
                            </ul>
                        </div>
                        
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div><!-- Filter Feeds Close --->
    
    <div class="home-container feeds-container" ng-init="getFeeds(0)" scroller>
		<div class="row">
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="feed_row1">
				<div class="item" id="statusbar" ng-if="is_member==1">
					<div class="create_status-outer border_radius">
						<div class="create_status-header">
							<div class="header-profile_image create_status-profile">
								<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
								<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
								<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
							</div> 
							<ul class="color-trans">
								<li><a href="javascript:void(0);" ng-class="{'active': status_selected=='text'}" id="status_header_text" ng-click="status_selected='text'"><i class="text"></i>Text</a></li> 
									  <li><a href="javascript:void(0);"  ng-class="{'active': status_selected=='media'}" id="status_header_media" ng-click="status_selected='media';"><i class="media"></i>Media</a></li> 
									  <li><a href="javascript:void(0);" id="event_tab" ng-class="{'active': status_selected=='event'}" id="status_header_event" ng-click="status_selected='event';"><i class="event"></i>Event</a></li> 
								 </ul> 
						 <div class="clear"></div> 
						  </div> 
						  <div id="status_text" ng-if="status_selected=='text'"> 
						  <div class="create_status-textarea"> 
								<textarea name="status" placeholder="Whats on your mind?" ng-model="mypost.statusText"></textarea> 
							   </div>                      
					  </div> 
						   <div id="status_media" ng-if="status_selected=='media'">
								<div class="status_media-outer">    
									<div ng-if="media_type=='image'"> 
										<div class="drag_drop-media group_drag-drop" id="uplodbtn"> 
											<div class="drag-caption"  > 
												<center><div id="ImageUploadStatus"></div></center> 
												<div id="default_img" onClick="getFile()"> 
													<img src="<?php echo $this->basePath();?>/public/images/drag_drop-img.png" alt=""   /> 
													<br />Choose a photo to upload 
												</div> 
												<div id="uploaded_img" style="display:none"> 
													<img src="<?php echo $this->basePath();?>/public/images/drag_drop-img.png" alt="" id="imgUserImage" /> 
													<a href="javascript:void(0)" id="removeUploaded" class="media_upload_close">Close</a> 
												</div> 
												<input type="file" name="btnUploadImage" id="btnUploadImage"  style="display:none"  >                                 
											</div>						 	
										</div> 
										<div class="status_media-caption"><input name="caption" type="text" placeholder="Add caption..."  ng-model="mypost.caption" /></div> 
									 </div>			 				 
									 <div id="status-media_video"  ng-if="media_type=='video'"> 
										<span ng-if="showVideoImage==0"> 
											<div class="status-video_link"><input name="caption" type="text" placeholder="Enter youtube url"  ng-model="mypost.videourl" /></div> 
											<div class="status-video_butn"><a href="javascript:void(0);" class="default_butn_grey" ng-click="addVideo()">Add</a></div> 
											<div class="clear"></div> 
										</span> 
										<span ng-if="showVideoImage==1"> 
											<img class="youtube-image" src="http://img.youtube.com/vi/{{videoid}}/0.jpg" /> 
											<a href="javascript:void(0)" ng-click="RemoveVideo()" class="media_upload_close">Close</a> 
											<br/> 
										 </span>  
										<div class="status_media-caption"><input name="caption" type="text" placeholder="Add text..." ng-model="mypost.videoCaption" /></div> 
									 </div> 
									 
									 <div class="status_select-filetype"> 
										<a href="javascript:void(0);" class="media_photo" ng-class="{'active': media_type=='image'}" ng-click="changeMediaType('image')"></a>                                
										  <a href="javascript:void(0);" class="media_video" ng-class="{'active': media_type=='video'}" ng-click="changeMediaType('video')"></a> 
										 <div class="clear"></div> 
									 </div> 
								  </div> 
								
							  </div> 
							
							 <div id="status_event" ng-if="status_selected=='event'"> 
								<div class="status_events-outer"> 
									<div class="event_fields-outer"> 
										<input name="event_title" type="text" placeholder="Event Title" ng-model="mypost.event_title" /> 
									 </div> 
									 <div class="event_fields-outer"> 
										<div class="event_date-out border_radius"> 
											<div class="event_date-pick"> 
												  <i class="grey-calender"></i> 
												  <input name="event_date" id="event_date" type="text" value="" ng-model="mypost.event_date" /> 
												 <div class="clear"></div> 
											  </div> 
											  <div class="event_time-pick"> 
												 <input name="event_time" id="event_time" type="text" value="" ng-model="mypost.event_time" placeholder="00:00:AM" /> 
											 </div> 
											 <div class="clear"></div> 
										 </div> 
									 </div> 
									  <div class="event_fields-outer"> 
										<div class="event_date-out border_radius"> 
											<i class="grey-location status_event_location"></i> 
											 <input class="status_event_location-text event_add_location" name="event_location" id="location" type="text" placeholder="Event Location" ng-model="mypost.event_location" /> 
											 <div class="clear"></div> 
										 </div> 
									 </div> 
									  <div class="status_event_pin-map"> 
										<div id="map-canvas"  ></div> 
										<div class="clear"></div> 
									  </div> 
									  <div class="status_event-details"> 
										<textarea name="event_description" placeholder="Event Description" class="border_radius" ng-model="mypost.event_description" ></textarea> 
									  </div> 
								 </div>                      
							 </div> 
							 <div class="create_status-footer"> 
								<div class="postin_group-outer"> 
									 
									<span ng-if="status_selected=='event'"> 
									<span><a href="javascript:void(0);" class="filter_group" data-toggle="dropdown" id="post_members">{{selectedMemberType}}<span></span></a> 
									<ul class="dropdown-menu post-group_dropdown color-trans arrow_box" role="menu" aria-labelledby="post_members"> 
										<li ng-repeat="item in memberTypes"><a href="javascript:void(0);"  ng-click="changeMemberType(item.0,item.1)">{{item.1}}</a></li> 
										 
									</ul></span> 
									
									</span> 
									<div ng-show="inviteSelected" class="invite_common_container" ng-if="status_selected=='event'&&selectedMemberType=='Invite people'"> 
									 <input name="event_invite" id="event_invite" type="text" value="" ng-model="event_search"  ng-focus="enablelist=1" ng-blur="hideList()" /> 
									 <div class="selected_members"> 
										<div ng-repeat="item in objSelectedUsers" class="invite_user_container" ng-click="RemoveFromInvitedUsers(item.user_id)"> 
											<div class="profile_imaage comn_profile_img"> 
												<img alt="" ng-if="item.user_image!=null&&item.user_image!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{item.user_id}}/{{ item.user_image }}"> 
												<img alt="" ng-if="(item.user_image==null||item.user_image=='')&&(item.user_fbid!=null&&item.user_fbid!='')" src="https://graph.facebook.com/{{item.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(item.user_image==null||item.user_image=='')&&(item.user_fbid==null||item.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</div> 
											<div class="invite_user_name">{{item.user_name}}</div> 
										</div> 
									 </div> 
									  <div class="listed_members_container"  ng-mouseover="OnListHover()" ng-mouseleave="OnListOut()"  ng-if="enablelist"> 
										<div ng-repeat="item in allMembers|filter:event_search" class="invite_user_container" remove-on-click ng-click="AddToInvitedUsers(item.user_id,item.user_given_name,item.profile_photo,item.user_fbid);remove();"> 
											<div class="profile_imaage comn_profile_img"> 
												<img alt="" ng-if="item.profile_photo!=null&&item.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{item.user_id}}/{{ item.profile_photo }}"> 
												<img alt="" ng-if="(item.profile_photo==null||item.profile_photo=='')&&(item.user_fbid!=null&&item.user_fbid!='')" src="https://graph.facebook.com/{{item.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(item.profile_photo==null||item.profile_photo=='')&&(item.user_fbid==null||item.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</div> 
											<div class="invite_user_name">{{item.user_given_name}}</div> 
										</div> 
									 </div> 
									 </div> 
								</div> 
								<div class="post-butns" ng-switch on="submit.switch"> 
									<div ng-switch-when="2" class="loading"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> </div> 
									<a href="javascript:void(0);" class="default_butn_violet" ng-click="submitStatus(status_selected)" ng-switch-when="1"> Post</a></div> 
								<div class="clear"></div> 
							</div> 
						  </div> 
					  </div> 
				 
			
			<div class="clear"></div>
			<div class="item" ng-repeat="items in feeds_row1">
					<div ng-switch="items.type" >
						<div class="post-outer border_radius"  ng-switch-when='New Status' ng-init="selectedIndex = $index" >
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
									<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>						 
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Discussion',items.content.group_discussion_id,1,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Discussion',items.content.group_discussion_id,1,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Discussion',items.content.group_discussion_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
									</ul>
								</div>
							</div>
							<div class="post_status-text" >{{items.content.group_discussion_content}}</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Discussion_{{items.content.group_discussion_id}}">
										<a href="javascript:void(0);" ng-if="items.content.is_liked==1" class="post_likes liked" ng-click="funUnLikeAction('Discussion',items.content.group_discussion_id)"><i></i></a>
										<a href="javascript:void(0);" ng-if="items.content.is_liked!=1" class="post_likes" ng-click="funLikeAction('Discussion',items.content.group_discussion_id)"><i></i></a>
										<span class="tootltip_outer">
											<a href="javascript:void(0);" ng-mouseover="loadToolTip('Discussion',items.content.group_discussion_id)" ng-mouseout="unloadToolTip('Discussion',items.content.group_discussion_id)"  class="post_likes_count" data-toggle="tooltip" data-placement="bottom"  ng-click="LoadLikedUsers('Discussion',items.content.group_discussion_id,items.content.like_count,items.content.is_liked)" >{{items.content.like_count}} likes</a>
											<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Discussion_{{items.content.group_discussion_id}}" style="display:none">
												<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
											</div> 							 
										</span>
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Discussion_{{items.content.group_discussion_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,1,$index)"><i></i></a>
									<a href="javascript:void(0);" ng-if="items.content.is_commented!=1" class="post_reply post_comments" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,1,$index)"><i></i></a>
									<a href="javascript:void(0);" id="ButtonComment_Discussion_{{items.content.group_discussion_id}}" class="post_comments" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,1,$index,1)"> {{items.content.comment_counts}} comments</a>							 
								</span>
								<div class="clear"></div>
								
								<div id="comment_Discussion_list_{{items.content.group_discussion_id}}" ng-if="items.viewComment==1">
									<div id="comment_Discussion_{{items.content.group_discussion_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Discussion_{{items.content.group_discussion_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Discussion',items.content.group_discussion_id);"></div>
											<div id="hashingUsers_Discussion_{{items.content.group_discussion_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Discussion',items.content.group_discussion_id,1,$index)">Post</a>
										</div>
									</div>
								</div>
								<div class="clear"></div>
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-if="comments.likes_count==0" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,1,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,1,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,1,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>								
							</div>
						</div>
						<div class="post-outer border_radius" ng-switch-when='New Media' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
									<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Media',items.content.group_media_id,1,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Media',items.content.group_media_id,1,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Media',items.content.group_media_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
									</ul>
								</div>
							</div>
							<div class="post_post-image">
								<a href="javascrip:void(0);" class="media_list"  data-toggle="modal" data-target="#post-img_popup" ng-click="loadMedia(items.content.group_media_id)">
									<img ng-if="items.content.media_type=='image'"  src="<?php echo $this->basePath(); ?>/public/{{image_paths.group}}{{items.content.group_id}}/media/{{items.content.media_content}}" alt=""   />
									<div class="status_youtube_video" ng-if="items.content.media_type=='video'"><span></span><img ng-if="items.content.media_type=='video'" src="http://img.youtube.com/vi/{{items.content.video_id}}/0.jpg" alt="" /></div>
								</a>
							</div>
							<div class="post_status-text" >{{items.content.media_caption}}</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Media_{{items.content.group_media_id}}">
										<a href="javascript:void(0);" ng-if="items.content.is_liked==1" class="post_likes liked" ng-click="funUnLikeAction('Media',items.content.group_media_id)"><i></i></a>
										<a href="javascript:void(0);"  ng-if="items.content.is_liked!=1" class="post_likes" ng-click="funLikeAction('Media',items.content.group_media_id)"><i></i></a>
										<span class="tootltip_outer">
											<a href="javascript:void(0);" class="post_likes_count" ng-mouseover="loadToolTip('Media',items.content.group_media_id)" ng-mouseout="unloadToolTip('Media',items.content.group_media_id)" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Media',items.content.group_media_id,items.content.like_count,items.content.is_liked)"  >{{items.content.like_count}} likes</a>
											<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Media_{{items.content.group_media_id}}" style="display:none">
												<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
											</div> 							 
										</span>	
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Media_{{items.content.group_media_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Media',items.content.group_media_id,1,$index)"><i></i></a>
									<a href="javascript:void(0);" class="post_reply post_comments" ng-click="LoadCommentList('Media',items.content.group_media_id,1,$index)" ng-if="items.content.is_commented!=1"><i></i></a>
									<a href="javascript:void(0);" id="ButtonComment_Media_{{items.content.group_media_id}}" class="post_comments" ng-click="LoadCommentList('Media',items.content.group_media_id,1,$index,1)"> {{items.content.comment_counts}} comments</a>
								</span>
								<div class="clear"></div>
											
								<div id="comment_Media_list_{{items.content.group_media_id}}" ng-if="items.viewComment==1">
									<div id="comment_Media_{{items.content.group_media_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Media_{{items.content.group_media_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Media',items.content.group_media_id);"></div>
											<div id="hashingUsers_Media_{{items.content.group_media_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Media',items.content.group_media_id,1,$index)">Post</a>
										</div>
									</div>
								</div>	
								<div class="clear"></div>	
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-if="comments.likes_count==0" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,1,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,1,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Media',items.content.group_media_id,1,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>
							</div>
						</div>
						<div class="post-outer border_radius" ng-switch-when='New Activity' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
										<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
										<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
										<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>	
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);"  data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Activity',items.content.group_activity_id,1,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Activity',items.content.group_activity_id,1,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Activity',items.content.group_activity_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
										<li ng-if="items.content.allow_join==1&&items.content.is_going==1"><a href="javascript:void(0);" ng-click="QuitRSVP(items.content.group_activity_id,1,$index)">Quit event</a></li>
									</ul>
								</div>
							</div>
							<div class="post_event-map" id="map_Activity_{{items.content.group_activity_id}}">
								<div class="google-map" hello-maps="" latitude="
								{{items.content.group_activity_location_lat}}" longitude="{{items.content.group_activity_location_lng}}"  location="{{items.content.group_activity_location}}" going="{{items.content.is_going}}"></div>
							</div>
							<div class="post_event-header" >
								<i class="event-calender"></i><span>{{items.content.group_activity_start_timestamp}}</span>
								<div ng-if="items.content.allow_join==1">
									<span ng-if="items.content.is_going==1" class="event_butn" id="activityrsvp_{{items.content.group_activity_id}}">I'm Going</span>
									<span class="event_butn" ng-if="items.content.is_going!=1" id="activityrsvp_{{items.content.group_activity_id}}"><a href="javascript:void(0);" class="default_butn_blue" ng-click="JoinRSVP(items.content.group_activity_id,1,$index)" >Join</a></span>
								</div>
								<div class="clear"></div>
							</div>
							<div class="post_status-text" >
								<h5>{{items.content.group_activity_title}}</h5>
								<div class="event_joined-member">
									<div class="event_joined-left">
										<span class="event_going_members" id="activityrsvpcount_{{items.content.group_activity_id}}">{{items.content.rsvp_count}}</span>
										<a href="javascript:void(0)"  ng-click="loadRsvpList(items.content.group_activity_id,'All',1)">GOING </a>   /    <span class="event_going_members">{{items.content.rsvp_friend_count}}</span> <a href="javascript:void(0)" ng-click="loadRsvpList(items.content.group_activity_id,'friends',1)"> friends </a>
									</div>
									<div class="event_member-img">
										<a ng-repeat="attending_users_list in items.content.attending_users" href="<?php echo $this->basePath(); ?>/'+attending_users_list.user_profile_name+'"><span class="event_profile-img_joined comn_profile_img">
											<img alt="" ng-if="attending_users_list.profile_photo!=null&&attending_users_list.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{attending_users_list.user_id}}/{{ attending_users_list.profile_photo }}">
											<img alt="" ng-if="(attending_users_list.profile_photo==null||attending_users_list.profile_photo=='')&&(attending_users_list.user_fbid!=null&&attending_users_list.user_fbid!='')" src="https://graph.facebook.com/{{attending_users_list.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(attending_users_list.profile_photo==null||attending_users_list.profile_photo=='')&&(attending_users_list.user_fbid==null||attending_users_list.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</span></a>							
									</div>
									<div class="clear"></div>
								</div>
								{{items.content.group_activity_content}}
							</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Activity_{{items.content.group_activity_id}}">
										<a ng-if="items.content.is_liked==1" href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Activity',items.content.group_activity_id)" ><i></i></a>
										<a ng-if="items.content.is_liked!=1" href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Activity',items.content.group_activity_id)" ><i></i></a>
										<span class="tootltip_outer"><a href="javascript:void(0);" ng-mouseover="loadToolTip('Activity',items.content.group_activity_id)" ng-mouseout="unloadToolTip('Activity',items.content.group_activity_id)"  class="post_likes_count" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Activity',items.content.group_activity_id,items.content.like_count,items.content.is_liked)">{{items.content.like_count}} likes</a>
										<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Activity_{{items.content.group_activity_id}}" style="display:none">
											<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
										</div>
										</span>
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Activity_{{items.content.group_activity_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Activity',items.content.group_activity_id,1,$index)"><i></i></a>
									<a href="javascript:void(0);" ng-if="items.content.is_commented!=1" class="post_reply post_comments" ng-click="LoadCommentList('Activity',items.content.group_activity_id,1,$index)"><i></i></a>
									<a href="javascript:void(0);" class="post_comments" id="ButtonComment_Activity_{{items.content.group_activity_id}}"  ng-click="LoadCommentList('Activity',items.content.group_activity_id,1,$index,1)">{{items.content.comment_counts}} comments</a>
								</span>
								
								<div class="clear"></div>
								<div id="comment_Activity_list_{{items.content.group_activity_id}}" ng-if="items.viewComment==1">
									<div class="clear"></div>
								<div id="comment_Activity_{{items.content.group_activity_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Activity_{{items.content.group_activity_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Activity',items.content.group_activity_id);"></div>
											<div id="hashingUsers_Activity_{{items.content.group_activity_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Activity',items.content.group_activity_id,1,$index)">Post</a>
										</div>
									</div>
								</div>
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" ng-if="comments.likes_count==0" class="post_likes" data-toggle="tooltip" data-placement="bottom"  ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,1,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,1,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Media',items.content.group_activity_id,1,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="feed_row2">            
				<div class="item" ng-repeat="items in feeds_row2">
					<div ng-switch="items.type">
						<div class="post-outer border_radius"  ng-switch-when='New Status' ng-init="selectedIndex = $index" >
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
									<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>						 
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Discussion',items.content.group_discussion_id,2,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Discussion',items.content.group_discussion_id,2,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Discussion',items.content.group_discussion_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
									</ul>
								</div>
							</div>
							<div class="post_status-text" >{{items.content.group_discussion_content}}</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Discussion_{{items.content.group_discussion_id}}">
										<a href="javascript:void(0);" ng-if="items.content.is_liked==1" class="post_likes liked" ng-click="funUnLikeAction('Discussion',items.content.group_discussion_id)"><i></i></a>
										<a href="javascript:void(0);" ng-if="items.content.is_liked!=1" class="post_likes" ng-click="funLikeAction('Discussion',items.content.group_discussion_id)"><i></i></a>
										<span class="tootltip_outer">
											<a href="javascript:void(0);" ng-mouseover="loadToolTip('Discussion',items.content.group_discussion_id)" ng-mouseout="unloadToolTip('Discussion',items.content.group_discussion_id)"  class="post_likes_count" data-toggle="tooltip" data-placement="bottom"  ng-click="LoadLikedUsers('Discussion',items.content.group_discussion_id,items.content.like_count,items.content.is_liked)" >{{items.content.like_count}} likes</a>
											<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Discussion_{{items.content.group_discussion_id}}" style="display:none">
												<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
											</div> 							 
										</span>
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Discussion_{{items.content.group_discussion_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,2,$index)"><i></i></a>
									<a href="javascript:void(0);" ng-if="items.content.is_commented!=1" class="post_reply post_comments" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,2,$index)"><i></i></a>
									<a href="javascript:void(0);" id="ButtonComment_Discussion_{{items.content.group_discussion_id}}" class="post_comments" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,2,$index,1)"> {{items.content.comment_counts}} comments</a>							 
								</span>
								<div class="clear"></div>
								
								<div id="comment_Discussion_list_{{items.content.group_discussion_id}}" ng-if="items.viewComment==1">
									<div id="comment_Discussion_{{items.content.group_discussion_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Discussion_{{items.content.group_discussion_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Discussion',items.content.group_discussion_id);"></div>
											<div id="hashingUsers_Discussion_{{items.content.group_discussion_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Discussion',items.content.group_discussion_id,2,$index)">Post</a>
										</div>
									</div>
								</div>
								<div class="clear"></div>
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" ng-if="comments.likes_count==0" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,2,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,2,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,2,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>								
							</div>
						</div>
						<div class="post-outer border_radius" ng-switch-when='New Media' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
									<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Media',items.content.group_media_id,2,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Media',items.content.group_media_id,2,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Media',items.content.group_media_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
									</ul>
								</div>
							</div>
							<div class="post_post-image">
								<a href="javascrip:void(0);" class="media_list"  data-toggle="modal" data-target="#post-img_popup" ng-click="loadMedia(items.content.group_media_id)">
									<img ng-if="items.content.media_type=='image'"  src="<?php echo $this->basePath(); ?>/public/{{image_paths.group}}{{items.content.group_id}}/media/{{items.content.media_content}}" alt=""   />
									<div class="status_youtube_video" ng-if="items.content.media_type=='video'"><span></span><img ng-if="items.content.media_type=='video'" src="http://img.youtube.com/vi/{{items.content.video_id}}/0.jpg" alt="" /></div>
								</a>
							</div>
							<div class="post_status-text" >{{items.content.media_caption}}</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Media_{{items.content.group_media_id}}">
										<a href="javascript:void(0);" ng-if="items.content.is_liked==1" class="post_likes liked" ng-click="funUnLikeAction('Media',items.content.group_media_id)"><i></i></a>
										<a href="javascript:void(0);"  ng-if="items.content.is_liked!=1" class="post_likes" ng-click="funLikeAction('Media',items.content.group_media_id)"><i></i></a>
										<span class="tootltip_outer">
											<a href="javascript:void(0);" class="post_likes_count" ng-mouseover="loadToolTip('Media',items.content.group_media_id)" ng-mouseout="unloadToolTip('Media',items.content.group_media_id)" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Media',items.content.group_media_id,items.content.like_count,items.content.is_liked)"  >{{items.content.like_count}} likes</a>
											<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Media_{{items.content.group_media_id}}" style="display:none">
												<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
											</div> 							 
										</span>	
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Media_{{items.content.group_media_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Media',items.content.group_media_id,2,$index)"><i></i></a>
									<a href="javascript:void(0);" class="post_reply post_comments" ng-click="LoadCommentList('Media',items.content.group_media_id,2,$index)" ng-if="items.content.is_commented!=1"><i></i></a>
									<a href="javascript:void(0);" id="ButtonComment_Media_{{items.content.group_media_id}}" class="post_comments" ng-click="LoadCommentList('Media',items.content.group_media_id,2,$index,1)"> {{items.content.comment_counts}} comments</a>
								</span>
								
								<div class="clear"></div>				
								<div id="comment_Media_list_{{items.content.group_media_id}}" ng-if="items.viewComment==1">
									<div class="clear"></div>
								<div id="comment_Media_{{items.content.group_media_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Media_{{items.content.group_media_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Media',items.content.group_media_id);"></div>
											<div id="hashingUsers_Media_{{items.content.group_media_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Media',items.content.group_media_id,2,$index)">Post</a>
										</div>
									</div>
								</div>	
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" ng-if="comments.likes_count==0" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,2,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,2,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Media',items.content.group_media_id,2,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>
							</div>
						</div>
						<div class="post-outer border_radius" ng-switch-when='New Activity' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
										<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
										<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
										<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>	
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);"  data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Activity',items.content.group_activity_id,2,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Activity',items.content.group_activity_id,2,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Activity',items.content.group_activity_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
										<li ng-if="items.content.allow_join==1&&items.content.is_going==1"><a href="javascript:void(0);"  ng-click="QuitRSVP(items.content.group_activity_id,2,$index)">Quit event</a></li>
									</ul>
								</div>
							</div>
							<div class="post_event-map" id="map_Activity_{{items.content.group_activity_id}}">
								<div class="google-map" hello-maps="" latitude="
								{{items.content.group_activity_location_lat}}" longitude="{{items.content.group_activity_location_lng}}"  location="{{items.content.group_activity_location}}" going="{{items.content.is_going}}"></div>
							</div>
							<div class="post_event-header" >
								<i class="event-calender"></i><span>{{items.content.group_activity_start_timestamp}}</span>
								<div ng-if="items.content.allow_join==1">
									<span ng-if="items.content.is_going==1" class="event_butn" id="activityrsvp_{{items.content.group_activity_id}}">I'm Going</span>
									<span class="event_butn" ng-if="items.content.is_going!=1" id="activityrsvp_{{items.content.group_activity_id}}"><a href="javascript:void(0);" class="default_butn_blue" ng-click="JoinRSVP(items.content.group_activity_id,2,$index)" >Join</a></span>
								</div>
								<div class="clear"></div>
							</div>
							<div class="post_status-text" >
								<h5>{{items.content.group_activity_title}}</h5>
								<div class="event_joined-member">
									<div class="event_joined-left">
										<span class="event_going_members" id="activityrsvpcount_{{items.content.group_activity_id}}">{{items.content.rsvp_count}}</span>
										<a href="javascript:void(0)"  ng-click="loadRsvpList(items.content.group_activity_id,'All',1)">GOING </a>   /    <span class="event_going_members">{{items.content.rsvp_friend_count}}</span> <a href="javascript:void(0)" ng-click="loadRsvpList(items.content.group_activity_id,'friends',1)"> friends </a>
									</div>
									<div class="event_member-img">
										<a ng-repeat="attending_users_list in items.content.attending_users" href="<?php echo $this->basePath(); ?>/'+attending_users_list.user_profile_name+'"><span class="event_profile-img_joined comn_profile_img">
											<img alt="" ng-if="attending_users_list.profile_photo!=null&&attending_users_list.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{attending_users_list.user_id}}/{{ attending_users_list.profile_photo }}">
											<img alt="" ng-if="(attending_users_list.profile_photo==null||attending_users_list.profile_photo=='')&&(attending_users_list.user_fbid!=null&&attending_users_list.user_fbid!='')" src="https://graph.facebook.com/{{attending_users_list.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(attending_users_list.profile_photo==null||attending_users_list.profile_photo=='')&&(attending_users_list.user_fbid==null||attending_users_list.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</span></a>							
									</div>
									<div class="clear"></div>
								</div>
								{{items.content.group_activity_content}}
							</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Activity_{{items.content.group_activity_id}}">
										<a ng-if="items.content.is_liked==1" href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Activity',items.content.group_activity_id)" ><i></i></a>
										<a ng-if="items.content.is_liked!=1" href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Activity',items.content.group_activity_id)" ><i></i></a>
										<span class="tootltip_outer"><a href="javascript:void(0);" ng-mouseover="loadToolTip('Activity',items.content.group_activity_id)" ng-mouseout="unloadToolTip('Activity',items.content.group_activity_id)"  class="post_likes_count" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Activity',items.content.group_activity_id,items.content.like_count,items.content.is_liked)">{{items.content.like_count}} likes</a>
										<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Activity_{{items.content.group_activity_id}}" style="display:none">
											<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
										</div>
										</span>
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Activity_{{items.content.group_activity_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Activity',items.content.group_activity_id,2,$index)"><i></i></a>
									<a href="javascript:void(0);" ng-if="items.content.is_commented!=1" class="post_reply post_comments" ng-click="LoadCommentList('Activity',items.content.group_activity_id,2,$index)"><i></i></a>
									<a href="javascript:void(0);" id="ButtonComment_Activity_{{items.content.group_activity_id}}" class="post_comments" ng-click="LoadCommentList('Activity',items.content.group_activity_id,2,$index,1)">{{items.content.comment_counts}} comments</a>
								</span>
								<div class="clear"></div>
								
								<div id="comment_Activity_list_{{items.content.group_activity_id}}" ng-if="items.viewComment==1">
									<div id="comment_Activity_{{items.content.group_activity_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Activity_{{items.content.group_activity_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Activity',items.content.group_activity_id);"></div>
											<div id="hashingUsers_Activity_{{items.content.group_activity_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Activity',items.content.group_activity_id,2,$index)">Post</a>
										</div>
									</div>
								</div>
								<div class="clear"></div>
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" ng-if="comments.likes_count==0" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,2,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,2,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Media',items.content.group_activity_id,2,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" id="feed_row3">            
				<div class="item" ng-repeat="items in feeds_row3">
					<div ng-switch="items.type">
						<div class="post-outer border_radius"  ng-switch-when='New Status' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
									<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>						 
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Discussion',items.content.group_discussion_id,3,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Discussion',items.content.group_discussion_id,3,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Discussion',items.content.group_discussion_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
									</ul>
								</div>
							</div>
							<div class="post_status-text" >{{items.content.group_discussion_content}}</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Discussion_{{items.content.group_discussion_id}}">
										<a href="javascript:void(0);" ng-if="items.content.is_liked==1" class="post_likes liked" ng-click="funUnLikeAction('Discussion',items.content.group_discussion_id)"><i></i></a>
										<a href="javascript:void(0);" ng-if="items.content.is_liked!=1" class="post_likes" ng-click="funLikeAction('Discussion',items.content.group_discussion_id)"><i></i></a>
										<span class="tootltip_outer">
											<a href="javascript:void(0);" ng-mouseover="loadToolTip('Discussion',items.content.group_discussion_id)" ng-mouseout="unloadToolTip('Discussion',items.content.group_discussion_id)"  class="post_likes_count" data-toggle="tooltip" data-placement="bottom"  ng-click="LoadLikedUsers('Discussion',items.content.group_discussion_id,items.content.like_count,items.content.is_liked)" >{{items.content.like_count}} likes</a>
											<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Discussion_{{items.content.group_discussion_id}}" style="display:none">
												<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
											</div> 							 
										</span>
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Discussion_{{items.content.group_discussion_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,3,$index)"><i></i></a>
									<a href="javascript:void(0);" ng-if="items.content.is_commented!=1" class="post_reply post_comments" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,3,$index)"><i></i></a>
									<a href="javascript:void(0);" class="post_comments" id="ButtonComment_Discussion_{{items.content.group_discussion_id}}" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,3,$index,1)"> {{items.content.comment_counts}} comments</a>							 
								</span>
								<div class="clear"></div>
								
								<div id="comment_Discussion_list_{{items.content.group_discussion_id}}" ng-if="items.viewComment==1">
									<div id="comment_Discussion_{{items.content.group_discussion_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Discussion_{{items.content.group_discussion_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Discussion',items.content.group_discussion_id);"></div>
											<div id="hashingUsers_Discussion_{{items.content.group_discussion_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Discussion',items.content.group_discussion_id,3,$index)">Post</a>
										</div>
									</div>
								</div>
								<div class="clear"></div>
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" ng-if="comments.likes_count==0" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,3,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,3,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Discussion',items.content.group_discussion_id,3,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>								
							</div>
						</div>
						<div class="post-outer border_radius" ng-switch-when='New Media' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
									<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
									<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Media',items.content.group_media_id,3,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Media',items.content.group_media_id,3,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0); " ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Media',items.content.group_media_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
									</ul>
								</div>
							</div>
							<div class="post_post-image">
								<a href="javascrip:void(0);" class="media_list"  data-toggle="modal" data-target="#post-img_popup" ng-click="loadMedia(items.content.group_media_id)">
									<img ng-if="items.content.media_type=='image'"  src="<?php echo $this->basePath(); ?>/public/{{image_paths.group}}{{items.content.group_id}}/media/{{items.content.media_content}}" alt=""   />
									<div class="status_youtube_video" ng-if="items.content.media_type=='video'"><span></span><img ng-if="items.content.media_type=='video'" src="http://img.youtube.com/vi/{{items.content.video_id}}/0.jpg" alt="" /></div>
								</a>
							</div>
							<div class="post_status-text" >{{items.content.media_caption}}</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Media_{{items.content.group_media_id}}">
										<a href="javascript:void(0);" ng-if="items.content.is_liked==1" class="post_likes liked" ng-click="funUnLikeAction('Media',items.content.group_media_id)"><i></i></a>
										<a href="javascript:void(0);"  ng-if="items.content.is_liked!=1" class="post_likes" ng-click="funLikeAction('Media',items.content.group_media_id)"><i></i></a>
										<span class="tootltip_outer">
											<a href="javascript:void(0);" class="post_likes_count" ng-mouseover="loadToolTip('Media',items.content.group_media_id)" ng-mouseout="unloadToolTip('Media',items.content.group_media_id)" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Media',items.content.group_media_id,items.content.like_count,items.content.is_liked)"  >{{items.content.like_count}} likes</a>
											<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Media_{{items.content.group_media_id}}" style="display:none">
												<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
											</div> 							 
										</span>	
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Media_{{items.content.group_media_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Media',items.content.group_media_id,3,$index)"><i></i></a>
									<a href="javascript:void(0);" class="post_reply post_comments" ng-click="LoadCommentList('Media',items.content.group_media_id,3,$index)" ng-if="items.content.is_commented!=1"><i></i></a>
									<a href="javascript:void(0);" id="ButtonComment_Media_{{items.content.group_media_id}}" class="post_comments" ng-click="LoadCommentList('Media',items.content.group_media_id,3,$index,1)"> {{items.content.comment_counts}} comments</a>
								</span>
								<div class="clear"></div>
								 				
								<div id="comment_Media_list_{{items.content.group_media_id}}" ng-if="items.viewComment==1">
									<div id="comment_Media_{{items.content.group_media_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Media_{{items.content.group_media_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Media',items.content.group_media_id);"></div>
											<div id="hashingUsers_Media_{{items.content.group_media_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Media',items.content.group_media_id,3,$index)">Post</a>
										</div>
									</div>
								</div>	
								<div class="clear"></div>
									<div class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img">
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" ng-if="comments.likes_count==0" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,3,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,3,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Media',items.content.group_media_id,3,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>
							</div>
						</div>
						<div class="post-outer border_radius" ng-switch-when='New Activity' ng-init="selectedIndex = $index">
							<div class="post_header">
								<span class="vertical_align"></span>
								<div class="header-profile_image">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">
										<img alt="" ng-if="items.content.profile_photo!=null&&items.content.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.content.user_id}}/{{ items.content.profile_photo }}">
										<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid!=null&&items.content.user_fbid!='')" src="https://graph.facebook.com/{{items.content.user_fbid}}/picture?width=66&&height=66">
										<img alt="" ng-if="(items.content.profile_photo==null||items.content.profile_photo=='')&&(items.content.user_fbid==null||items.content.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
									</a>
									<span class="owner-badge" ng-if="items.content.is_admin"></span>
								</div>
								<div class="post_profile-name">
									<a href="<?php echo $this->basePath(); ?>/{{items.content.user_profile_name}}">{{items.content.user_given_name}}</a>
									<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{items.content.group_seo_title}}">{{items.content.group_title}}</a></span>
								</div>	
								<div class="post_time noti_time"><i></i>{{items.time}}</div>
								<div class="clear"></div>
								<div class="post_option-outer">
									<a href="javascript:void(0);" data-toggle="dropdown" id="post_option"><i></i></a>
									<ul class="dropdown-menu post-option_dropdown color-trans" role="menu" aria-labelledby="post_in_group">
										<li><a href="javascript:void(0);"  data-toggle="modal" data-target="#feed_edit" ng-if="items.content.user_id==profile.user_id"  ng-click="editFeed('Activity',items.content.group_activity_id,3,$index)">Edit this post</a></li>
										<li><a href="javascript:void(0);" data-toggle="modal" data-target="#feed_delete" ng-if="items.content.is_logged_user_admin==1||items.content.user_id==profile.user_id"  ng-click="deleteFeed('Activity',items.content.group_activity_id,3,$index)">Delete this post</a></li>
										<li><a href="javascript:void(0);" ng-if="items.content.user_id!=profile.user_id" ng-click="ReportPost('Activity',items.content.group_activity_id)" data-toggle="modal" data-target="#report_post">Report this post</a></li>
										<li ng-if="items.content.allow_join==1&&items.content.is_going==1"><a href="javascript:void(0);"  ng-click="QuitRSVP(items.content.group_activity_id,3,$index)">Quit event</a></li>
									</ul>
								</div>
							</div>
							<div class="post_event-map" id="map_Activity_{{items.content.group_activity_id}}">
								<div class="google-map" hello-maps="" latitude="
								{{items.content.group_activity_location_lat}}" longitude="{{items.content.group_activity_location_lng}}"  location="{{items.content.group_activity_location}}" going="{{items.content.is_going}}"></div>
							</div>
							<div class="post_event-header" >
								<i class="event-calender"></i><span>{{items.content.group_activity_start_timestamp}}</span>
								<div ng-if="items.content.allow_join==1">
									<span ng-if="items.content.is_going==1" class="event_butn" id="activityrsvp_{{items.content.group_activity_id}}">I'm Going</span> 
									<span class="event_butn" ng-if="items.content.is_going!=1" id="activityrsvp_{{items.content.group_activity_id}}"><a href="javascript:void(0);" class="default_butn_blue" ng-click="JoinRSVP(items.content.group_activity_id,3,$index)" >Join</a></span>
								</div>
								<div class="clear"></div>
							</div>
							<div class="post_status-text" >
								<h5>{{items.content.group_activity_title}}</h5>
								<div class="event_joined-member">
									<div class="event_joined-left">
										<span class="event_going_members" id="activityrsvpcount_{{items.content.group_activity_id}}">{{items.content.rsvp_count}}</span>
										<a href="javascript:void(0)"  ng-click="loadRsvpList(items.content.group_activity_id,'All',1)">GOING </a>   /    <span class="event_going_members">{{items.content.rsvp_friend_count}}</span> <a href="javascript:void(0)" ng-click="loadRsvpList(items.content.group_activity_id,'friends',1)"> friends </a>
									</div>
									<div class="event_member-img">
										<a ng-repeat="attending_users_list in items.content.attending_users" href="<?php echo $this->basePath(); ?>/'+attending_users_list.user_profile_name+'"><span class="event_profile-img_joined comn_profile_img">
											<img alt="" ng-if="attending_users_list.profile_photo!=null&&attending_users_list.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{attending_users_list.user_id}}/{{ attending_users_list.profile_photo }}">
											<img alt="" ng-if="(attending_users_list.profile_photo==null||attending_users_list.profile_photo=='')&&(attending_users_list.user_fbid!=null&&attending_users_list.user_fbid!='')" src="https://graph.facebook.com/{{attending_users_list.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(attending_users_list.profile_photo==null||attending_users_list.profile_photo=='')&&(attending_users_list.user_fbid==null||attending_users_list.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</span></a>							
									</div>
									<div class="clear"></div>
								</div>
								{{items.content.group_activity_content}}
							</div>
							<div class="post_status-footer">
								<span class="like_comment_outer">
									<span class="Activity_{{items.content.group_activity_id}}">
										<a ng-if="items.content.is_liked==1" href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Activity',items.content.group_activity_id)" ><i></i></a>
										<a ng-if="items.content.is_liked!=1" href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Activity',items.content.group_activity_id)" ><i></i></a>
										<span class="tootltip_outer"><a href="javascript:void(0);" ng-mouseover="loadToolTip('Activity',items.content.group_activity_id)" ng-mouseout="unloadToolTip('Activity',items.content.group_activity_id)"  class="post_likes_count" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Activity',items.content.group_activity_id,items.content.like_count,items.content.is_liked)">{{items.content.like_count}} likes</a>
										<div ng-if="items.content.like_count>0" class="like_tooltip" id="tooltip_Activity_{{items.content.group_activity_id}}" style="display:none">
											<span ng-repeat="likedusers in items.content.liked_users">{{likedusers}}</span>						 
										</div>
										</span>
									</span>
								</span>
								<span class="post_comment-right" id="CntComment_Activity_{{items.content.group_activity_id}}">
									<a href="javascript:void(0);" ng-if="items.content.is_commented==1" class="post_reply post_comments comented" ng-click="LoadCommentList('Activity',items.content.group_activity_id,3,$index)"><i></i></a>
									<a href="javascript:void(0);" ng-if="items.content.is_commented!=1" class="post_reply post_comments" ng-click="LoadCommentList('Activity',items.content.group_activity_id,3,$index)"><i></i></a>
									<a href="javascript:void(0);" class="post_comments" id="ButtonComment_Activity_{{items.content.group_activity_id}}" ng-click="LoadCommentList('Activity',items.content.group_activity_id,3,$index,1)">{{items.content.comment_counts}} comments</a>
								</span>
								<div class="clear"></div>
								 
								<div id="comment_Activity_list_{{items.content.group_activity_id}}" ng-if="items.viewComment==1">
									<div id="comment_Activity_{{items.content.group_activity_id}}">
									<div class="post_comment-outer" id="post_reply">
										<div class="header-profile_image comment_profile-img">
											<img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
											<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
										</div>
										<div class="post_comment_text post_comment_reply">
											<div class="commentTextArea" id="commentText_Activity_{{items.content.group_activity_id}}"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,'Activity',items.content.group_activity_id);"></div>
											<div id="hashingUsers_Activity_{{items.content.group_activity_id}}"></div>
										</div>
										<div class="clear"></div>
										<div class="post_comment_reply-butns">
											<a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment('Activity',items.content.group_activity_id,3,$index)">Post</a>
										</div>
									</div>
								</div>
								<div class="clear"></div>
									<div  class="post_comment-outer" ng-repeat="comments in items.content.comments" id="comment_list_{{comments.comment_id}}">
										<div class="header-profile_image comment_profile-img" >
											<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
												<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
												<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
											</a>
										</div>
										<div class="post_comment_text" ng-if="!ifexistinEditList(comments.comment_id)">
											<span class="profile_name-comment">
												<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name}}</a>											
											</span>
											<div   id="comment_content_text_{{comments.comment_id}}">{{showCommentText(comments.comment_content,comments.comment_id)}}</div>
											<div class="comment_likes">
												<span class="Comment_{{comments.comment_id}}">
												<div ng-if="comments.islike==1">
													<span class="tootltip_outer">
														<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
												</div>
												<div ng-if="comments.islike!=1">
													<span class="tootltip_outer" ng-if="comments.likes_count>0">
														<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a>
														<a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadToolTip('Comment',comments.comment_id)" ng-mouseout="unloadToolTip('Comment',comments.comment_id)" >{{comments.likes_count}} likes</a>
														<div class="like_tooltip" id="tooltip_Comment_{{comments.comment_id}}" style="display:none">
															<span ng-repeat="likedusers in comments.liked_users">{{likedusers}}</span>
														</div>
													</span>
													<a href="javascript:void(0);"  ng-if="comments.likes_count==0" class="post_likes" ng-click="funLikeAction('Comment',comments.comment_id)" ><i></i></a><a href="javascript:void(0);" class="post_likes" ng-if="comments.likes_count==0" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" >0 likes</a>
												</div>
												</span>
												<div class="post_time noti_time comment_time"><i></i>{{comments.comment_time}}</div>
												<div class="clear"></div>
											</div>
										</div>
										<div ng-if="comments.allowedit==1">
											<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
												<div class="post_comment_text post_comment_reply">
													<textarea placeholder="Start typing your comment here.." name="" id="editcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>													
												</div>
												<div class="clear"></div>
												<div class="post_comment_reply-butns">												 
													<span>
														<!-- <a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a> -->
														<a ng-click="SendEditComment(comments.comment_id,3,selectedIndex)" class="default_butn_violet" href="javascript:void(0);">Post</a>
													</span>
												</div>
											</div>
											<div class="clear"></div>
											<div class="comment_actions">
												<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
												<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteComment(comments.comment_id,3,selectedIndex)"></a>
												<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
											</div>
											<div class="clear"></div>
										</div>
										
									</div>
									<div><a class="loadmore-comments" href="javascript:void(0)" ng-if="items.content.comments.length>=(items.content.comment_page*1)*10" ng-click="LoadCommentList('Media',items.content.group_activity_id,3,$index,(items.content.comment_page*1)+1)">More comments</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
<!-- Post Images Pop Up -->
<div class="pop_bg modal fade" id="post-img_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog postimg_popup">
    <div class="modal-content postimg_content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <div class="clear"></div>
      </div>
      <div class="modal-body postimg_body">
		<span ng-if="mediaLoader ==1"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"></span>
		<span ng-if="mediaLoader ==0">
			<div class="post-img_preview-outer" ng-if="medialist==1">
				<div id="media_list" style="height:350px;overflow:hidden" ng-init="enableScrollbarMedia()">
				<h3>All Media of {{media.group_title}}</h3>
				<ul>
					<li ng-repeat="items in groupAllMediaContent"><a href="javascript:void(0)" ng-click="loadMedia(items.group_media_id)" >
					<img  ng-if="items.media_type=='image'" src="<?php echo $this->basePath(); ?>/public/datagd/group/{{items.media_added_group_id}}/media/{{items.media_content}}">
					<img ng-if="items.media_type=='video'" src="http://img.youtube.com/vi/{{items.video_id}}/0.jpg" />
					</a></li>
					<a href="javascript:void(0)" ng-click="LoadmoreMedia(media.group_id);" ng-if="showLoadmore==1">Loadmore</a>
				</ul>
				</div>
			</div>
			<div class="post-img_preview-outer" ng-if="medialist==0">
				<div class="post-img_preview">
					<div class="post-img_preview_nav">
						<div class="image_preview-previous" ng-if="media.prev_id!=''">
							<a href="javascript:void(0);" ng-click="loadMedia(media.prev_id)"><i></i></a>
						</div>
						<div class="image_show-all">
							<a href="javascript:void(0);" ng-click="loadAllMedia(media.group_id)"><i></i></a>
						</div>
						<div class="image_preview-next" ng-if="media.next_id!=''">
							<a href="javascript:void(0);" ng-click="loadMedia(media.next_id)"><i></i></a>
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="pop_image-preview" ng-if="media.media_type=='image'">
					<img ng-if="media.media_type=='image'" src="<?php echo $this->basePath(); ?>/public/datagd/group/{{media.group_id}}/media/{{media.media_content}}" alt="{{media.media_content}}" />				 
					</div>
					<div id="video_content">
					 
					 </div>				
					<div class="pop_img-footer">in group: <a href="<?php echo $this->basePath(); ?>/groups/{{media.group_seo_title}}">{{media.group_title}}</a></div>					
				</div>
			</div>			
			<!-- Popup Right Side -->
			<div class="image_postdetails" ng-if="medialist==0">
				<div class="post_header">
					<span class="vertical_align"></span>
					<div class="header-profile_image">
						<img alt="" ng-if="media.profile_photo!=null&&media.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{media.user_id}}/{{ media.profile_photo }}">
						<img alt="" ng-if="(media.profile_photo==null||media.profile_photo=='')&&(media.user_fbid!=null&&media.user_fbid!='')" src="https://graph.facebook.com/{{media.user_fbid}}/picture?width=66&&height=66">
						<img alt="" ng-if="(media.profile_photo==null||media.profile_photo=='')&&(media.user_fbid==null||media.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
						<span class="owner-badge" ng-if="media.is_admin==1"></span>
					</div>
					<div class="post_profile-name">
						<a href="<?php echo $this->basePath(); ?>/{{media.user_profile_name}}">{{media.user_given_name}}</a>
						<span>posted in <a href="<?php echo $this->basePath(); ?>/groups/{{media.group_seo_title}}">{{media.group_title}}</a></span>
					</div>
					<div class="post_time noti_time"><i></i>{{media.added_time}}</div>
					<div class="clear"></div>
				</div>
				<div class="post_status-text">
					{{media.media_caption}}
				</div>
				<div class="post_status-footer">
					<span class="like_comment_outer">
						<span id="Media_modelwindow">
							<span ng-if="medialike_ajaxloader == 1">
								<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif">
							</span>
							<span class="tootltip_outer" ng-if="medialike_ajaxloader == 0">
						 
								<a class="post_likes" ng-class="{'liked': media.is_liked==1}" ng-if="media.is_liked ==1&&is_member==1" href="javascript:void(0);" ng-click="funUnLikeAction('Media',media.group_media_id)"  ><i></i></a>
								<a class="post_likes" ng-if="media.is_liked ==0&&is_member==1" href="javascript:void(0);" ng-click="funLikeAction('Media',media.group_media_id)" ><i></i></a>
								<span class="post_likes" ng-if="is_member!=1" ><i></i></span>
								<a href="javascript:void(0)"   ng-click="LoadLikedUsers('Media',media.group_media_id,media.likes_counts,media.is_liked)" ng-mouseover="loadPopupToolTip('Media',media.group_media_id)"  ng-mouseleave="unloadPopupToolTip('Media',media.group_media_id)"  >{{media.likes_counts}} likes</a>							 
								<div class="like_tooltip" ng-if="media.likes_counts>0" id="tooltip_popup_Media_{{media.group_media_id}}" style="display:none">	
									<span ng-repeat="items in media.likedUsers">{{items}}</span> 
								</div>
							</span>						
						</span>
					</span>
					<span  class="post_comment-right">
						<a class="post_comments" ng-if="is_member==1" ng-class="{'comented': media.is_commented==1}" href="javascript:void(0)" ng-click="enableCommentReplyMedia()"><i></i></a>
						<span class="post_comments" ng-if="is_member!=1" ><i></i></span>
						<a class="post_comments" href="javascript:void(0);"><span>{{media.comment_count}} comments</span></a>
					</span>	
					<div class="clear"></div>
					<div class="post_comment-outer" id="post_reply-media" ng-if="showReply==1">
						<div class="header-profile_image comment_profile-img">
							 <img alt="" ng-if="profile.profile_photo!=null&&profile.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{profile.user_id}}/{{ profile.profile_photo }}">
							 <img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid!=null&&profile.user_fbid!='')" src="https://graph.facebook.com/{{profile.user_fbid}}/picture?width=66&&height=66">
							<img alt="" ng-if="(profile.profile_photo==null||profile.profile_photo=='')&&(profile.user_fbid==null||profile.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
							
						</div>
						<div class="post_comment_text post_comment_reply">
							<div class="commentTextArea" contenteditable="true" ng-keydown="keyPressMedia($event,'Media',media.group_media_id);" data-text="Start typing your comment here.." id="txtMediaComment"></div>
							 <div id="txtMediaCommenthashed"></div>
						</div>
						<div class="clear"></div>
						<div class="post_comment_reply-butns">
							<a href="javascript:void(0);" class="default_butn_grey" id="cancel_reply" ng-click="showReply=0;removeCommentBox()">Cancel</a><a href="javascript:void(0);" class="default_butn_violet" ng-click="postComment(media.group_media_id)">Post</a>
						</div>
					</div>
				</div>
				 
				<div id="comment_container_outer" ng-init="enableScrollbar()">
					<div class="post_comment-outer" ng-repeat="comments in media_comments">
						<div class="header-profile_image comment_profile-img">
							<a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">
							<img alt="" ng-if="comments.profile_photo!=null&&comments.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{comments.user_id}}/{{ comments.profile_photo }}">
							<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid!=null&&comments.user_fbid!='')" src="https://graph.facebook.com/{{comments.user_fbid}}/picture?width=66&&height=66">
							<img alt="" ng-if="(comments.profile_photo==null||comments.profile_photo=='')&&(comments.user_fbid==null||comments.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
							 </a>
						</div>
						<div class="post_comment_text"  ng-if="!ifexistinEditList(comments.comment_id)">
							<span class="profile_name-comment"><a href="<?php echo $this->basePath(); ?>/{{comments.user_profile_name}}">{{comments.user_given_name }}</a></span><span id="comment_text_{{$index}}">{{loadCommmentText($index,comments.comment_content)}}</span>
							<div class="comment_likes">								 
								<span class="Comment_{{comments.comment_id}}">
								<span class="tootltip_outer">
								<a href="javascript:void(0);" class="post_likes liked" ng-if="comments.islike==1&&is_member==1" ng-click="funUnLikeAction('Comment',comments.comment_id)"><i></i></a>
								<span class="post_likes liked" ng-if="comments.islike==1&&is_member!=1" ><i></i></span>
								<a href="javascript:void(0)" ng-if="comments.islike == 1" ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadPopupToolTip('Comment',comments.comment_id)" ng-mouseleave="unloadPopupToolTip('Comment',comments.comment_id)">{{comments.likes_count}} likes</a>
								<a href="javascript:void(0);" class="post_likes" ng-if="comments.islike==0&&is_member==1" ng-click="funLikeAction('Comment',comments.comment_id)"><i></i></a>
								<span class="post_likes" ng-if="comments.islike==0&&is_member!=1"|><i></i></span>
								<a href="javascript:void(0)" ng-if="comments.islike==0"  ng-click="LoadLikedUsers('Comment',comments.comment_id,comments.likes_count,comments.islike)" ng-mouseover="loadPopupToolTip('Comment',comments.comment_id)" ng-mouseleave="unloadPopupToolTip('Comment',comments.comment_id)">{{comments.likes_count}} likes</a>
								
								<div class="like_tooltip" ng-if="comments.likes_count>0" id="tooltip_popup_Comment_{{comments.comment_id}}" style="display:none">	
									<span ng-repeat="items in comments.likedUsers">{{items}}</span> 
								</div>
								</span>
								</span>
								<div class="post_time noti_time comment_time "><i></i>{{comments.comment_time}}</div>
								<div class="clear"></div>
							</div>
						</div>
						<div ng-if="comments.allowedit==1">
							<div class="comment-edit" ng-if="ifexistinEditList(comments.comment_id)">
								 
								 
								<div class="post_comment_text post_comment_reply">
									<textarea placeholder="Start typing your comment here.." name="" id="Mediaeditcomment_text_{{comments.comment_id}}" >{{comments.comment_content}}</textarea>
								</div>
								<div class="clear"></div>
								<div class="post_comment_reply-butns">
									 
										<a ng-click="RemoveCommentEditWindow(comments.comment_id)" id="cancel_reply" class="default_butn_grey" href="javascript:void(0);">Cancel</a>
										<a ng-click="SendMediaEditComment(comments.comment_id,media.group_media_id)" class="default_butn_violet" href="javascript:void(0);">Post</a>
									 
								</div>
							</div>
						</div>
						<div class="clear"></div>
						<div ng-if="comments.allowedit==1">
							<div class="comment_actions">
								<a class="comment_edit" href="javascript:void(0);" title="Edit" ng-click="enableCommentEdit(comments.comment_id)"></a>
								<a class="comment_delete" href="javascript:void(0);" title="Delete" ng-click="DeleteCommentMedia(comments.comment_id,media.group_media_id)"></a>
								<a class="comment_report" href="javascript:void(0);" title="Report"ng-if="comments.user_id!=profile.user_id" ng-click="ReportPost('Comment',comments.comment_id)" data-toggle="modal" data-target="#report_post" ></a>
							</div>
						</div>
						<div class="clear"></div>
						
					</div>
					<div ng-if= "media.comment_count>10&&hideloadmore==0" ng-click="loadmoreCommentFromPopup(media.group_media_id)"> <a href="javascript:void(0)" class="loadmore-comments">More comments</a></div>  	  
				</div>
			</div>
			<div class="clear"></div>
		</span>
      </div>      
    </div>
  </div>
</div>
<div class="pop_bg modal fade" id="likes_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
				<div ng-if="loadpeople==1"><img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"></div>
          		<div class="likes_pop-outer" ng-if="loadpeople==0">
                	<div class="like_profile-outer" ng-repeat="items in likedpeoples">
                    	<div class="like_profile-img comn_profile_img">
							<img alt="" ng-if="items.profile_photo!=null&&items.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.user_id}}/{{ items.profile_photo }}">
							<img alt="" ng-if="(items.profile_photo==null||items.profile_photo=='')&&(items.user_fbid!=null&&items.user_fbid!='')" src="https://graph.facebook.com/{{items.user_fbid}}/picture?width=66&&height=66">
							<img alt="" ng-if="(items.profile_photo==null||items.profile_photo=='')&&(items.user_fbid==null||items.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
						</div>
                        <div class="like_profile-name"><a href="javascript:void(0);">{{items.user_given_name}}</a></div>
                        <a href="javascript:void(0);" class="button_small_blue like_add-friend" ng-if="items.is_friend">Friends</a>
						<a href="javascript:void(0);" class="button_small_blue friend_accept" ng-if="items.get_request" ng-click="acceptFriendRequest(items.user_profile_name);remove()" title="Accept request"></a>
						<a href="javascript:void(0);" remove-on-click ng-if="items.get_request" class="friend_ignore" ng-click="rejectFriendRequest(items.user_profile_name);remove()" title="Ignore Request"></a>
						<a ng-if="items.is_requested" href="javascript:void(0);" class="default_butn_grey">Pending</a>
						<a href="javascript:void(0);" remove-on-click ng-if="items.is_requested!= 1 && items.get_request!= 1 && items.is_friend!= 1" class="default_butn_blue" ng-click="sentFriendRequest(items.user_profile_name);remove()">Add Friend</a>		 
                    </div>                  
                </div>
          </div>          
        </div>
      </div>
    </div>	
	<div class="pop_bg modal fade" id="rsvp_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
				 
          		<div class="likes_pop-outer" id="rsvplist_outer" >
                	<div class="like_profile-outer" ng-repeat="items in rsvplist">
                    	<div class="like_profile-img comn_profile_img">
							<img alt="" ng-if="items.profile_photo!=null&&items.profile_photo!=''" src="<?php echo $this->basePath(); ?>/public/datagd/profile/{{items.user_id}}/{{ items.profile_photo }}">
							<img alt="" ng-if="(items.profile_photo==null||items.profile_photo=='')&&(items.user_fbid!=null&&items.user_fbid!='')" src="https://graph.facebook.com/{{items.user_fbid}}/picture?width=66&&height=66">
							<img alt="" ng-if="(items.profile_photo==null||items.profile_photo=='')&&(items.user_fbid==null||items.user_fbid=='')" src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">
						 
						</div>
                        <div class="like_profile-name"><a href="javascript:void(0);">{{items.user_given_name}}</a></div>
                        <a href="javascript:void(0);" class="button_small_blue like_add-friend" ng-if="items.is_friend">Friends</a>
						<a href="javascript:void(0);" class="button_small_blue friend_accept" ng-if="items.get_request" ng-click="acceptFriendRequest(items.user_profile_name);remove()" title="Accept request"></a>
						<a href="javascript:void(0);" remove-on-click ng-if="items.get_request" class="friend_ignore" ng-click="rejectFriendRequest(items.user_profile_name);remove()" title="Ignore Request"></a>
						<a ng-if="items.is_requested" href="javascript:void(0);" class="default_butn_grey">Pending</a>
						<a href="javascript:void(0);" remove-on-click ng-if="items.is_requested!= 1 && items.get_request!= 1 && items.is_friend!= 1 &&profile.user_id!=items.user_id" class="default_butn_blue" ng-click="sentFriendRequest(items.user_profile_name);remove()">Add Friend</a>		
                    </div> 
					<div><a href="javascript:void(0)"  class="loadmore-comments" ng-if="rsvplist.length<=0" >No members joined</a></div>
					<div><a href="javascript:void(0)" class="loadmore-comments" ng-click="loadRsvpList(selectedActivity,filter_by,'')" ng-if="loadmore==1">load more</a>
					 
					</div>
                </div>
          </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="feed_edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
			<div id="signup_step1" ng-if="ajax_loader_feededit==1">
				<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> 
			</div>
			<div id="signup_step1" ng-if="ajax_loader_feededit==0">
				<div class="step1_outer">
					<div ng-if="feedEditSystemType == 'Discussion'">
						<div class="create_status-textarea"><textarea id="editDiscussion">{{EditFeedDiscussionContent.group_discussion_content}}</textarea></div>
						<div  class="post-butns"> 
							 <a ng-click="UpdateStatus('Discussion',EditFeedDiscussionContent.group_discussion_id,EditFeedrow_count,EditFeedrow_index)" class="default_butn_violet ng-scope" href="javascript:void(0);">Update Post</a>
						</div>
						<div class="clear"></div>
					</div>
					<div ng-if="feedEditSystemType == 'Media'">
						<div>
							<img ng-if="EditFeedMediaContent.media_type=='image'"  src="<?php echo $this->basePath(); ?>/public/{{image_paths.group}}{{EditFeedMediaContent.media_added_group_id}}/media/{{EditFeedMediaContent.media_content}}" alt=""   />
							<img ng-if="EditFeedMediaContent.media_type=='video'" src="http://img.youtube.com/vi/{{getYouTubeIdFromURL(EditFeedMediaContent.media_content)}}/0.jpg" alt="" />
						</div>
						<div class="create_status-textarea edit_media_textarea">
						<input type="text"  placeholder="Add caption..." name="feed_edit_caption" id="feed_edit_caption" value="{{EditFeedMediaContent.media_caption}}" />
						</div>
						<div  class="post-butns"> 
							 <a ng-click="UpdateStatus('Media',EditFeedMediaContent.group_media_id,EditFeedrow_count,EditFeedrow_index)" class="default_butn_violet ng-scope" href="javascript:void(0);">Update Post</a>
						</div>
						<div class="clear"></div>
					</div>
					<div ng-if="feedEditSystemType == 'Activity'">
						<div class="edit_event_outer"> 
							<a href="javascript:void(0)" id="enableDatepiker" style="display:block;height:0;width:0">&nbsp</a>
							<div class="event_fields-outer"> 
								<input   id="event_title_edit" type="text" placeholder="Event Title" value="{{EditFeedActivityContent.group_activity_title}}" /> 
							 </div> 
							 <div class="event_fields-outer"> 
								<div class="event_date-out border_radius"> 
									<div class="event_date-pick"> 
										  <i class="grey-calender"></i> 
										  <input name="event_date_edit" id="event_date_edit" type="text" value="{{EditFeedActivityContent.group_activity_start_date}}" ng-init="loadDatePicker()"  /> 
										 <div class="clear"></div> 
									  </div> 
									  <div class="event_time-pick"> 
										 <input name="event_time" id="event_time_edit" type="text" value="{{EditFeedActivityContent.group_activity_start_time}}" placeholder="00:00:AM" ng-model="mypost.event_time" /> 
									 </div> 
									 <div class="clear"></div> 
								 </div> 
							 </div> 
							  <div class="event_fields-outer"> 
								<div class="event_date-out border_radius"> 
									<i class="grey-location status_event_location"></i> 
									 <input class="status_event_location-text edit_location" name="event_location" id="edit_location" type="text" placeholder="Event Location" value="{{EditFeedActivityContent.group_activity_location}}"  /> 
									 <div class="clear"></div> 
								 </div> 
							 </div> 
							  <div class="status_event_pin-map"> 
								<div id="map-canvasedit"  ></div> 
								<div class="clear"></div> 
							  </div> 
							  <div class="status_event-details"> 
								<textarea name="event_description_edit" id="event_description_edit" placeholder="Event Description" class="border_radius" >{{EditFeedActivityContent.group_activity_content}}</textarea> 
							  </div> 
						 </div>  
						 <div  class="post-butns"> 
							 <a ng-click="UpdateStatus('Activity',EditFeedActivityContent.group_activity_id,EditFeedrow_count,EditFeedrow_index)" class="default_butn_violet ng-scope" href="javascript:void(0);">Update Post</a>
						</div>
						<div class="clear"></div>
					</div>
				</div>
			</div>
          </div>
          
        </div>
      </div>
    </div>
</div>
	<div class="pop_bg modal fade" id="feed_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
			<div id="signup_step1" ng-if="ajax_loader_feeddelte==1">
				<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> 
			</div>
			<div id="signup_step1" ng-if="ajax_loader_feeddelte==0">
				<div class="step1_outer">
					<div ng-if="feedDeleteSystemType == 'Discussion'">
						Are you sure you want to remove this status?
					</div>
					<div ng-if="feedDeleteSystemType == 'Media'">
						Are you sure you want to remove this media?
					</div>
					<div ng-if="feedDeleteSystemType == 'Activity'">
						Are you sure you want to remove this activity?
					</div>
					<div  class="post-butns"> 
					<a ng-click="RemovePost(feedDeleteSystemType,DeleteFeedrow_content_id,DeleteFeedrow_count,DeleteFeedrow_index)" class="default_butn_blue" href="javascript:void(0);">Yes</a>
					<a ng-click="CancelRemovePost()" class="default_butn_red" href="javascript:void(0);">No</a>
					</div>
					<div class="clear"></div>
				</div> 
			</div>
          </div>
          
        </div>
      </div>
    </div>
	<div class="pop_bg modal fade" id="report_post" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog login_popup">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <div class="clear"></div>
          </div>
          <div class="modal-body signup_body">
			<div id="signup_step1" ng-if="ajax_loader_reportpost==1">
				<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> 
			</div>
			<div id="signup_step1" ng-if="ajax_loader_reportpost==0">
				<div class="step1_outer">
					<div class="select_group-type" ng-repeat="items in reasons">			 
                    	<input type="radio" name="spam_type" id="{{items.reason_id}}" value="{{items.reason_id}}" ng-click="selectReason(items.reason_id,items.reason)" ng-model="spam.reason_id">
                        <label for="{{items.reason_id}}"><i class="radio-styled"></i> <i class="group-public_black"></i>{{items.reason}}</label>                       
                        <div class="clear"></div>
                    </div>
					<div class="login-field" ng-if="enableReportReason==1">
					<textarea placeholder="Please type your reason" ng-model="spam.otherReason"></textarea>
					</div>
					<div class="clear"></div>
					<div class="signup_button">                        
                         <a ng-click="sendReport(spam)" id="go_step-2" class="default_butn_blue" href="javascript:void(0);">Send</a></div>
                        <div class="clear"></div>
                    </div>
					<div class="clear"></div>
				</div> 
			</div>
          </div>
          
        </div>
      </div>
</div>
<div ng-if="hideAll==1">
	
</div>
</div>
</div>
<script type="text/javascript" src="<?php echo $this->basePath(); ?>/public/js/jquery.cropit.js"></script> 
<script src="<?php echo $this->basePath(); ?>/public/js/jquery.plugin.js"></script>
<script src="<?php echo $this->basePath(); ?>/public/js/jquery.timeentry.js"></script>
<link href="<?php echo $this->basePath(); ?>/public/css/prettify.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo $this->basePath(); ?>/public/js/prettify.js"></script>
<script type="text/javascript" src="<?php echo $this->basePath(); ?>/public/js/jquery.slimscroll.js"></script>
<script>  
	var current_lat = 25.271139000000000000;
	var current_lng =55.307485000000040000;	
	var image_paths = <?php echo json_encode($image_folders); ?>;
	var enableHashing = 0;
	var str_search = ''; 
	var hashedUser = [];
	var rsvppage=1;
	var group = <?php echo json_encode($group_info); ?>;
    var groupapp = angular.module('groupapp',[]);
    groupapp.config(function ($httpProvider) {
		$httpProvider.defaults.transformRequest = function(data){
			if (data === undefined) {
				return data;
			}
			return $.param(data);
		}
	}); 	 
    groupapp.controller('groupController',function($scope, $http){
		$scope.tag_limit = 25;
        $scope.tag_inc_limit = 25;
		$scope.group = <?php echo json_encode($group_info); ?>;
        $scope.group_picture = '<?php echo $this->basePath(); ?>/public/images/group-img_def.jpg';
		if($scope.group.group_photo_photo!=''&&$scope.group.group_photo_photo!=null){
			$scope.group_picture = '<?php echo $this->basePath(); ?>/public/datagd/group/'+$scope.group.group_id+'/'+$scope.group.group_photo_photo;
		}
		$scope.categoryImagePath=(image_paths.tag_category!='')?baseurl+"/public/"+image_paths.tag_category:baseurl+'/public/images/';
		$scope.intrest = {};
		$scope.imageEdit = 0;
		$scope.intrest.switch = 2; 
		$scope.tagCategory=[];
		$scope.selectedCategory = '';
		$scope.selectedIndex = 0;
		$scope.grouptags =  <?php echo json_encode($tags); ?>;
		$scope.grouptagCategory =  <?php echo json_encode($tag_category); ?>;
		<?php if($enableEdit == 1){ ?>
			$scope.editGroupFlag =1;
		<?php }else{ ?>
			$scope.editGroupFlag =0;
		<?php } ?>
		
		$scope.fromData = {},
		$scope.fromData.groupTitle = $scope.group.group_title;
		$scope.fromData.groupDescription = $scope.group.group_description;
		$scope.groupUsers = <?php echo json_encode($groupUsers); ?>;
		$scope.QuestionType        = new Array("Textarea","checkbox","radio");
        $scope.TextType            = [];
		$scope.TextType[0]            = "Select Question Type";
		$scope.TextType[1]            = "Select Question Type";
		$scope.TextType[2]            = "Select Question Type";
		$scope.fromData.groupTypeEdit  = $scope.group.group_type;
		$scope.myIntrest = <?php echo json_encode($myIntrests); ?>;
		$scope.objSelectedCategory =  new Array();		 
		$scope.objSelectedTags = new Array();	
		$scope.inviteAll = 0;
		$scope.checkCommonIntrests = function(tag_id){
			var is_exist = -1;
			for(i=0;i<$scope.myIntrest.length;i++){
				if($scope.myIntrest[i].tag_id == tag_id){	
					is_exist =i;
				}
			}
			if(is_exist!=-1){return true;}else{return false;}
		}
		 $scope.enableGroupScrollbar = function(){
			 $('#about_group_container').slimScroll({				   
				  height: '140px'
			  });
		}
		$scope.enableScrollbarInvite= function(){
			$('#listed_members_container_inner').slimScroll({
				height: '140px'
			});
		}
		$scope.loadPopup = function(){ 
			$scope.intrest.switch = 2;
			 $scope.tag_limit = 25;
			url = baseurl+'/tag/getAllActiveCategories';
			$http({
			  method: 'POST',
			  url: url
			}).success(function(data, status, headers, config) {
			   $scope.tagCategory=data.tag_category;
			   console.log($scope.tagCategory);
			   $scope.selectedCategory = data.tag_category[0].tag_category_id;
			   $scope.getTags(data.tag_category[0].tag_category_id);
			   $scope.intrest.switch = 1;
			   $scope.selectedIndex = 0;
			}).error(function(data, status, headers, config) {
				 //alert("Error occured. Please try again");
			});
			url = baseurl+'/groups/getAllGroupTags';
			$http.post(url,{group:$scope.group.group_id}, {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
			   if(data.return_array.process_info==''){
				$scope.objSelectedTags = [];
				$scope.objSelectedCategory =  [];
				angular.forEach(data.return_array.tag_category, function(dataset){ 
					$scope.objSelectedCategory.push({
						category:dataset.tag_category_id,category_title:dataset.tag_category_title,category_icon:dataset.tag_category_icon
					});										 
				});
				angular.forEach(data.return_array.group_tags, function(dataset){ 
					$scope.objSelectedTags.push({
						category:dataset.category_id,tag_id:dataset.tag_id,tag_name:dataset.tag_title
					});										 
				});
			   }else{alert(data.return_array.process_info);}
			}).error(function(data, status, headers, config) {
				 //alert("Error occured. Please try again");
			});
		};
		$scope.isVisible = 0;
		$scope.drpClass = "dropdown_close";
		$scope.selectedIndex = 0;	
		$scope.tags = {};
		$scope.showtags = 0;
		$scope.showmoretags = 0;			
		$scope.selectedCategory = '';
		$scope.selectCategory = function(category,selected){  
			if($scope.isVisible){
				$scope.isVisible = 0;
				$scope.selectedIndex = selected;
				$scope.drpClass = "dropdown_close";
				$scope.selectedCategory = category;
				$scope.showtags = 0;	
				$scope.showmoretags = 0;
				$scope.tag_limit = 25;
				$scope.getTags(category);					 
			}else{ 
				$scope.isVisible = 1;
				$scope.drpClass = "dropdown_open";
			}
		};
		$scope.getTags = function(category){
			$scope.showtags = 0;
			url  = baseurl+'/tag/getAllTagsOfSelectedCategory';				
			$http.post(url,{category_id:category,search:$scope.tag_search}, {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) { 
				if(data.tags.length>0){
				$scope.tags = data.tags; 	
				$scope.showtags = 1;
				}else{$scope.showtags = 0;}
				if(data.tags.length > $scope.tag_limit) {
				$scope.showmoretags=1; } else { $scope.showmoretags =0; }
			}).error(function(data, status, headers, config) {
			// alert("Error occured. Please try again");
			});
		};
		$scope.objSelectedTags = new Array();
		$scope.addToSelected = function(category,tag_id,tag_name){ 
			var incexist = 0;
			angular.forEach($scope.objSelectedTags, function(dataset){ 
				if(dataset.tag_name == tag_name){incexist++;}
			});				
			if(incexist == 0){
				$scope.objSelectedTags.push({
					category:category,tag_id:tag_id,tag_name:tag_name
				});
			}
			angular.forEach($scope.tagCategory, function(dataset){ 
				if(dataset.tag_category_id == category){
					var incexist = 0;
					angular.forEach($scope.objSelectedCategory, function(datasets){ 
						if(datasets.category_icon == dataset.tag_category_icon){incexist++;}
						 
					});	
					if(incexist ==0){
						$scope.objSelectedCategory.push({
							category:dataset.tag_category_id,category_title:dataset.tag_category_title,category_icon:dataset.tag_category_icon
						});	
					}
				}
			   });			 
		};
		$scope.removeFromSelected = function(tag_name,category){ 			 
			var array_index = $scope.getArrayIndex($scope.objSelectedTags,'tag_name',tag_name);
			$scope.objSelectedTags.splice( array_index, 1 );
			var incexist = 0;
			angular.forEach($scope.objSelectedTags, function(dataset){ 
				if(dataset.category == category){incexist++;}
			});				
			if(incexist == 0){
				var array_index = $scope.getArrayIndex($scope.objSelectedCategory,'category',category);
				$scope.objSelectedCategory.splice( array_index, 1 );
			}
		};
		$scope.getArrayIndex = function(arr_elemnt,key,item){ 
			var incexist = -1;
			for(i=0;i<arr_elemnt.length;i++) {
				if(arr_elemnt[i][key] == item){incexist = i;}
			}
			return incexist;
		};
		$scope.AddToIntrest = function(){
			var newtags = new Array();
			var incexist = 0;
			angular.forEach($scope.objSelectedTags, function(dataset){ 
				newtags[incexist] = dataset.tag_id;
				incexist++;
			});
			 url  = baseurl+'/groups/updateTag';			
			$http.post(url,{tags:newtags,group:$scope.group.group_id}, {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) { 
				$scope.grouptags=data.return_array.tags;
				$scope.grouptagCategory=data.return_array.tag_category;
				$('#interest_popup').modal('hide');
			}).error(function(data, status, headers, config) {
			 //alert("Error occured. Please try again");
			});
		};
		$scope.enableEditForm = function(){
			$scope.editGroupFlag = 1;
		}
		$scope.cancelGroupEdit = function(){
			$scope.editGroupFlag = 0;
			$scope.groupTitle = $scope.group.title;
			$scope.groupDescription = $scope.group.group_description;
		}
		$scope.updateGroup = function(fromData){
			var error = 0;  
			if(fromData.groupTitle==''){ 
				alert("Group Title Required");
				error++;
			}
			if(fromData.groupDescription==''){
				alert("Group Description Required");
				error++;
			}
			if(error==0){
				 url  = baseurl+'/groups/updateGroup';			
				$http.post(url,
				{
					group_title:fromData.groupTitle,
					group_description:fromData.groupDescription,
					group:$scope.group.group_id,
					group_type:$scope.fromData.groupTypeEdit,
				}
				, {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) { 
					if(data.return_array.process_status == 'success'){
						$scope.group.group_title = fromData.groupTitle;
						$scope.group.group_description = fromData.groupDescription;
						$scope.fromData.groupTitle = fromData.groupTitle;
						$scope.fromData.group_description = fromData.groupDescription;
						$scope.group.group_type = $scope.fromData.groupTypeEdit;
						$scope.editGroupFlag = 0;
					}else{
						alert(data.return_array.process_info);
					}
				}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
				});
			}
		}
		$scope.ajaxloadQuestionnaire = 0;
		$scope.questionnaire = [];
		$scope.selectedType=[];
		$scope.question = [];
		$scope.option1 = [];
		$scope.option2 = [];
		$scope.option3= [];
		$scope.questionId = [];
		$scope.ajaxUpdateQuistionnaire = 0;
		$scope.loadQuestionnaire = function(group_id){ 
			$scope.ajaxloadQuestionnaire = 1;
			url  = baseurl+'/groups/getQuestionnaire';
			$http.post(url,
				{	
					group_id:group_id,					 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					$scope.ajaxloadQuestionnaire = 0;
					if(data.return_array.process_status == 'success'){		
						$scope.questionnaire = data.return_array.questionnaire;
						var index = 0;
						if($scope.questionnaire.length>0){
							for(i=0;i<$scope.questionnaire.length;i++){
								$scope.selectedType[i]=$scope.questionnaire[i].answer_type;
								$scope.question[i]=$scope.questionnaire[i].question;
								$scope.questionId[i]=$scope.questionnaire[i].questionnaire_id;
								if($scope.questionnaire[i].answer_type=='radio'||$scope.questionnaire[i].answer_type=='checkbox'){
									$scope.option1[i]=$scope.questionnaire[i].options[0].option;
									$scope.option2[i]=$scope.questionnaire[i].options[1].option;
									$scope.option3[i]=$scope.questionnaire[i].options[2].option;
								}
							}
						}
					}else{
						alert(data.return_array.process_info);						 
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
		}
		$scope.range = function() {			 
			return new Array(3);
	    }	  
		$scope.cancelQuestionniare = function(){
			$("#edit_question").modal("hide");
		}
	    $scope.SaveQuestionniare = function(group_id){
			var index = 0;
			var error = 0;
			angular.forEach($scope.selectedType, function(dataset){ 
				 switch(dataset){
					case 'Textarea':
						if($scope.question[index]==null||$scope.question[index]==undefined||$scope.question[index]==''){
							alert("Add question for quesiton"+((index*0)+1));
							error++;
							return false;
						}
					break;
					case 'checkbox':
						if($scope.question[index]==null||$scope.question[index]==undefined||$scope.question[index]==''){
							alert("Add question for quesiton"+((index*0)+1));
							error++;
							return false;
						}
						if($scope.option1[index]==null||$scope.option1[index]==undefined||$scope.option1[index]==''||$scope.option2[index]==null||$scope.option2[index]==undefined||$scope.option2[index]==''){
							alert("Add atleast two options for quesiton"+((index*0)+1));
							error++;
							return false;
						}
						 
					break;
					case 'radio':
						if($scope.question[index]==null||$scope.question[index]==undefined||$scope.question[index]==''){
							alert("Add question for quesiton"+((index*0)+1));
							error++;
							return false;
						}
						if($scope.option1[index]==null||$scope.option1[index]==undefined||$scope.option1[index]==''||$scope.option2[index]==null||$scope.option2[index]==undefined||$scope.option2[index]==''){
							alert("Add atleast two options for quesiton"+((index*0)+1));
							error++;
							return false;
						}
						 
					break;
				 }
				 index++;
			});
			
			if(error == 0){
				$scope.ajaxUpdateQuistionnaire = 1;
				url  = baseurl+'/groups/updateQuestionnaire';
				$http.post(url,
				{	
					group_id:group_id,
					type:$scope.selectedType,
					question:$scope.question,
					option1:$scope.option1,
					option2:$scope.option2,
					option3:$scope.option3,
					questionId:$scope.questionId,
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					$scope.ajaxloadQuestionnaire = 0;
					if(data.return_array.process_status == 'success'){		
						alert("The questionnaire is successfully updated");
						$scope.ajaxUpdateQuistionnaire = 0;						
					}else{
						alert(data.return_array.process_info);						 
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
			}
	   }
	   $scope.EditBanner = function(){
		  $scope.imageEdit = 1;
	   }
	   $scope.cancelcropImage = function(){
		  $scope.imageEdit = 0;
	   }
	   $scope.cropGroupImage = function(){
			var imageData = $('.image-editor-group').cropit('export');	
			var formData = new FormData();	
			formData.append("imageData",imageData);			
			formData.append("group_id",$scope.group.group_id);			
			url = baseurl + '/groups/updatProfilePic';
			$http.post(url,formData,{withCredentials: true, headers: {'Content-Type': undefined }, transformRequest: angular.identity}).success(function(data, status, headers, config) {
				 if(data.return_array.process_status == 'success'){
					  
					 $scope.aboutmeAjax =0;		
					window.location.reload();
					
				 }else{
					 alert(data.return_array.process_info);
					 $scope.aboutmeAjax =0;		
				 }

			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			}); 
	   }
	   $scope.removeGroupBanner = function(){
		   url = baseurl + '/groups/removeBanner';
			$http.post(url,{ group:$scope.group.group_id},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
			).success(function(data, status, headers, config) {
				 if(data.return_array.process_status == 'success'){
					 window.location.reload();						
				 }else{
					 alert(data.return_array.process_info);
				 }

			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});		   
	   }
	   $scope.cancelRemove=function(){
		   $('#bannerdelete_popup').modal('hide');
	   }
	   $scope.selectedGroupRemoved = '';
	   $scope.selectedGroupIdRemoved = '';
	   $scope.selectLeavingGroup = function(item_id,item_title){
			$scope.selectedGroupRemoved = item_title;
			$scope.selectedGroupIdRemoved = item_id;
	   }
	   $scope.removeLeaveBox= function(){
			$scope.selectedGroupRemoved = '';
			$scope.selectedGroupIdRemoved = '';
	   }
	   $scope.leaveGroup = function(){
			url = baseurl + '/groups/leavegroup';
			$http.post(url,{groupId:$scope.selectedGroupIdRemoved},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
			).success(function(data, status, headers, config) {
				 if(data.return_array.process_status == 'success'){
					 window.location.reload();						
				 }else{
					 alert(data.return_array.process_info);
				 }

			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.aboutmeAjax = 0;
		$scope.profile ={};
		$scope.USERprofile = <?php echo json_encode($profile_data); ?>;
		$scope.profile.profile_photo = '<?php echo $profile_data->profile_photo; ?>';
		$scope.profile.user_id = '<?php echo $profile_data->user_id; ?>';
		$scope.profile.user_fbid = '<?php echo $profile_data->user_fbid; ?>';
		$scope.profile.user_profile_about_me = $scope.USERprofile.user_profile_about_me;
			$scope.aboutme = '';
			$scope.JoiningItem = '';
			$scope.JoiningItemTitle = '';
			$scope.JoiningItemType = '';
			$scope.JoiningItemseoTitile = '';
			$scope.joingingquestionnaire = [];
			$scope.md_questionnaire = [];
			$scope.md_option = [];
			$scope.md_checkbox0 = [];
			$scope.md_checkbox1 = [];
			$scope.md_checkbox2 = [];
			$scope.cancelImageUpload = function(){
				$('#upload_profile-image').modal('hide');
				$('#join_group').modal('show');
				$scope.gotoGroupJoiningStep2();
				$scope.aboutmeAjax =0;
			}
			$scope.selectJoiningGroup= function(item_id,item_title,type,seoTitile){
				$scope.JoiningItem =item_id;
				$scope.JoiningItemTitle = item_title;
				$scope.JoiningItemType = type;
				$scope.JoiningItemseoTitile = seoTitile;
				if($scope.profile.user_profile_about_me!=''&&$scope.profile.user_profile_about_me!=null&&$scope.profile.user_profile_about_me!='null'&&$scope.profile.user_profile_about_me!=undefined){
					$scope.gotoGroupJoiningStep2();
				}else{ 	
					$scope.joingroupStep =1;
				}
			}
			$scope.gotoGroupJoiningStep2 = function(){
				if(($scope.profile.profile_photo!=''&&$scope.profile.profile_photo!=null&&$scope.profile.profile_photo!='null'&&$scope.profile.profile_photo!=undefined)||($scope.profile.user_fbid!=''&&$scope.profile.user_fbid!=null&&$scope.profile.user_fbid!='null'&&$scope.profile.user_fbid!=undefined)){
					$scope.gotoGroupJoiningStep3();
				}else{
					$scope.joingroupStep =2;
				}
			}
			$scope.gotoGroupJoiningStep3 = function(){
				url = baseurl + '/groups/getQuestionnaire';
				$http.post(url,{group_id:$scope.JoiningItem},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						if(data.return_array.questionnaire.length>0){
							$scope.joingingquestionnaire = data.return_array.questionnaire;
							$scope.joingroupStep =3;
						}else{
							$scope.gotoGroupJoiningStep4();
						}
					}else{
						alert(data.return_array.process_info);						 
					}
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});						 
			}
			$scope.gotoGroupJoiningStep4 = function(){
				url = baseurl + '/groups/joinGroup';
				$http.post(url,
				{										 
					group_id:$scope.JoiningItem,
				},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						if($scope.group.group_type == 'public'){
							$scope.group.is_requested = 1;
						}else{
							$scope.group.is_member = 1;
						} 
						$scope.joingroupStep =4;						
					}else{
						alert(data.return_array.process_info);								
					}
				}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
				});	
			}
			$scope.closeJoinWindow = function(){
				$("#join_group").modal("hide");
			}
			$scope.SaveAboutMe = function(content){
				$scope.aboutme = content;
				if($scope.aboutme!=''&&$scope.aboutme!=null&&$scope.aboutme!='null'&&$scope.aboutme!=undefined){
					$scope.aboutmeAjax =1;
					url = baseurl + '/user/updateBio';
					$http.post(url,{bio:$scope.aboutme},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
					).success(function(data, status, headers, config) {
						if(data.return_array.process_status == 'success'){
							$scope.gotoGroupJoiningStep2(); 						 
							$scope.aboutmeAjax =0;							 
						}else{
							alert(data.return_array.process_info);
							$scope.aboutmeAjax =0;		
						}

					}).error(function(data, status, headers, config) {
						// alert("Error occured. Please try again");
					});
				}else{
					$scope.gotoGroupJoiningStep2();
				}
			}
			$scope.loadProfileUpload = function(){
				$('#join_group').modal('hide')
			}
			$scope.aboutmeAjax =0;
			$scope.cropImage = function(){				
				var imageData = $('.image-editor').cropit('export');
				if(imageData!=''){
					$scope.aboutmeAjax =1;	
					url = baseurl + '/user/updatProfilePic';
					$http.post(url,{imageData:imageData},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
					).success(function(data, status, headers, config) {
						if(data.return_array.process_status == 'success'){
							$('#upload_profile-image').modal('hide');								 
							$('#join_group').modal('show');	
							$scope.gotoGroupJoiningStep3();
							$scope.aboutmeAjax =0;												 
						}else{
							alert(data.return_array.process_info);
							$scope.aboutmeAjax =0;		
						}
					}).error(function(data, status, headers, config) {
					//	alert("Error occured. Please try again");
					}); 
				}else{
					$scope.gotoGroupJoiningStep3();
				}
			}
			$scope.checkInList = function(question_id,option_id,index){ 
				if(index==0){
					if($scope.md_checkbox0[question_id]&&$scope.md_checkbox0[question_id]!=''&&$scope.md_checkbox0[question_id]==option_id) {return true;}
					else{return false;}
				}
				if(index==1){
					if($scope.md_checkbox1[question_id]&&$scope.md_checkbox1[question_id]!=''&&$scope.md_checkbox1[question_id]==option_id) {return true;}
					else{return false;}
				}
				if(index==2){
					if($scope.md_checkbox2[question_id]&&$scope.md_checkbox2[question_id]!=''&&$scope.md_checkbox2[question_id]==option_id) {return true;}
					else{return false;}
				}
				return false;				
			}
			$scope.checkInList = function(question_id,option_id,index){ 
				if(index==0){
					if($scope.md_checkbox0[question_id]&&$scope.md_checkbox0[question_id]!=''&&$scope.md_checkbox0[question_id]==option_id) {return true;}
					else{return false;}
				}
				if(index==1){
					if($scope.md_checkbox1[question_id]&&$scope.md_checkbox1[question_id]!=''&&$scope.md_checkbox1[question_id]==option_id) {return true;}
					else{return false;}
				}
				if(index==2){
					if($scope.md_checkbox2[question_id]&&$scope.md_checkbox2[question_id]!=''&&$scope.md_checkbox2[question_id]==option_id) {return true;}
					else{return false;}
				}
				return false;				
			}
			$scope.selectedOptionRadio = function(question_id,option_id){ 
				$selectedOption = option_id;
				$scope.md_option[question_id] =  option_id
			}
			$scope.selectedOptionCheckbox = function(question_id,option_id,index){ 
				if(index==0){
					if($scope.md_checkbox0[question_id]&&$scope.md_checkbox0[question_id]!='') {$scope.md_checkbox0[question_id]='';}
					else{$scope.md_checkbox0[question_id] = option_id;}
				}
				if(index==1){
					if($scope.md_checkbox1[question_id]&&$scope.md_checkbox1[question_id]!='') {$scope.md_checkbox1[question_id]='';}
					else{$scope.md_checkbox1[question_id] = option_id;}
				}
				if(index==2){
					if($scope.md_checkbox2[question_id]&&$scope.md_checkbox2[question_id]!='') {$scope.md_checkbox2[question_id]='';}
					else{$scope.md_checkbox2[question_id] = option_id;}
				}				  
			}
			$scope.saveQuestionnaireAnswers = function(){
				ince = 0;
				var questionanswers = [];
				angular.forEach($scope.joingingquestionnaire, function(dataset){					
					 if(dataset.answer_type == 'Textarea'){
						questionanswers.push({'question_id':dataset.questionnaire_id,'answer':$scope.md_questionnaire[ince]});						
					 }
					 if(dataset.answer_type == 'checkbox'){
						 var chked_option = [];
						 if($scope.md_checkbox0[dataset.questionnaire_id])chked_option.push($scope.md_checkbox0[dataset.questionnaire_id]);
						 if($scope.md_checkbox1[dataset.questionnaire_id])chked_option.push($scope.md_checkbox1[dataset.questionnaire_id]);
						 if($scope.md_checkbox2[dataset.questionnaire_id])chked_option.push($scope.md_checkbox2[dataset.questionnaire_id]);
						questionanswers.push({'question_id':dataset.questionnaire_id,'answer':chked_option.join()});	
					 }
					  if(dataset.answer_type == 'radio'){
						questionanswers.push({'question_id':dataset.questionnaire_id,'answer':$scope.md_option[dataset.questionnaire_id]});				  
					 }
					 ince++;
				});
				url = baseurl + '/groups/saveUserQuestionnaire';
					$http.post(url,
					{
						questionanswers:questionanswers,
						group_id:$scope.JoiningItem,
					},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
					).success(function(data, status, headers, config) {
						 if(data.return_array.process_status == 'success'){
							$scope.joingroupStep = 4;	
							$scope.aboutmeAjax =0;	
							if($scope.group.group_type == 'public'){
								$scope.group.is_requested = 1;
							}else{
								$scope.group.is_member = 1;
							} 	
						 }else{
							 alert(data.return_array.process_info);
							 $scope.aboutmeAjax =0;		
						 }

					}).error(function(data, status, headers, config) {
					//	 alert("Error occured. Please try again");
					});					
			}
			$scope.reloadPage = function(){	
				window.location.reload();
			}
			$scope.selectInviteAll = function(){
				if($scope.inviteAll==0){
					$scope.inviteAll =1;
				}else{
					$scope.inviteAll =0;
				}
			}
			$scope.objSelectedUsers = [];
			$scope.getAllfriendsNotAMember = function(){
				$('.listed_members_container').show();
				var group_id = $scope.group.group_id;
				url = baseurl + '/groups/getFriendsNotMemberOfGroupList';
				var search_str = $("#invite_list_friend").val();
				$http.post(url,
				{
					search_str:search_str,
					group_id:group_id,
				},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
				).success(function(data, status, headers, config) {
					 if(data.return_array.process_status == 'success'){
						$scope.filteredfriends = data.return_array.members;
					 }else{
						 alert(data.return_array.process_info);
						 $scope.aboutmeAjax =0;		
					 }

				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
			}
			$scope.AddToInvitedUsers = function(user_id,user_name,user_image,user_fbid){ 
			var incexist = 0;
			angular.forEach($scope.objSelectedUsers, function(dataset){ 
				if(dataset.user_id == user_id){incexist++;}
			});				
			if(incexist == 0){
				$scope.objSelectedUsers.push({
					user_id:user_id,user_name:user_name,user_image:user_image,user_fbid:user_fbid
				});
			}				
			}
			$scope.RemoveFromInvitedUsers = function(user_id){
				var array_index = $scope.getArrayIndex($scope.objSelectedUsers,'user_id',user_id);
				$scope.objSelectedUsers.splice( array_index, 1 );			 
			}
			$scope.sendInvite =function(){
				var users = '';
				if($scope.inviteAll==1){
					users = 'All';
				}else{
					if($scope.objSelectedUsers.length>0){
						users = $scope.objSelectedUsers;
					}else{
						alert("Select one to invite");
					}
				}
				if(users!=''){
					var group_id = $scope.group.group_id;
					url = baseurl + '/groups/inviteMembers';					 
					$http.post(url,
					{
						users:users,
						group_id:group_id,
					},{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}
					).success(function(data, status, headers, config) {
						 if(data.return_array.process_status == 'success'){
							 alert("Invitation send to users");
							$scope.filteredfriends =[];
							$scope.objSelectedUsers=[];
							$("#listed_members_container").hide();
							$("#invite_group").modal("hide");
						 }else{
							 alert(data.return_array.process_info);
							 $scope.aboutmeAjax =0;		
						 }

					}).error(function(data, status, headers, config) {
						// alert("Error occured. Please try again");
					});
				}
			}
			$scope.enableScrollbar = function(){
			 $('#comment_container_outer').slimScroll({
				  color: '#00f'
			  });
		}
		$scope.resetInviteForm = function(){
			$scope.filteredfriends =[];
			$scope.objSelectedUsers=[];
			$("#listed_members_container").hide();
			$("#invite_group").modal("hide");
			$("#invite_list_friend").val("");
		}
		$scope.groups_reasons= [];
		$scope.groups_ajax_loader_reportpost = 1;
		$scope.groups_reportSelectedContentId = '';
		$scope.groups_reportSelectedContentType = '';
		$scope.groups_selectedReasonId='';
		$scope.groups_selectedReasonType='';
		$scope.groups_enableReportReason=0;;
		$scope.groups_spam={};
		$scope.groups_ReportPost = function(type,content_id){
			$scope.groups_reasons= [];
			$scope.groups_reportSelectedContentId = content_id;
			$scope.groups_reportSelectedContentType = type;
			url  = baseurl+'/spam/getreasons';
			$http.post(url,
			{	
				type:type,content_id:content_id
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.groups_reasons=data.return_array.reasons;
					$scope.groups_ajax_loader_reportpost = 0;
				}else{
					alert(data.return_array.process_info);	
					$("#reportGroup").modal("hide");
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});	
		}
		$scope.groups_selectReason = function(reason_id,reasontype){
			$scope.groups_selectedReasonId=reason_id;
			$scope.groups_selectedReasonType=reasontype;
			if(reasontype == 'Other'){
				$scope.groups_enableReportReason=1;
			}else{
				$scope.groups_enableReportReason=0;
			}
		}
		$scope.groups_sendReport = function(frmspam){
			if($scope.groups_selectedReasonType == 'Other'&&frmspam.otherReason=='' ){
				alert("Please enter the reason");
			}else{
				url  = baseurl+'/spam/sentreport';
				$http.post(url,
				{	
					type:$scope.groups_reportSelectedContentType,content_id:$scope.groups_reportSelectedContentId,reason_id:frmspam.reason_id,otherReason:frmspam.otherReason
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						alert("You have successfully reported about the post");
						$scope.groups_spam={};
						$("#reportGroup").modal("hide");
					}else{
						alert(data.return_array.process_info);	
						$("#reportGroup").modal("hide");
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});	
			}
		}
     }); 
	groupapp.directive('removeOnClick', function() {
		return {
			link: function(scope, elt, attrs) {
				scope.remove = function() {
					elt.remove();
				};
			}
		}
	}); 
	angular.element(document).ready(function() {   
		angular.bootstrap(document.getElementById("group_right_container"), ["groupapp"]);
	});
	var upladedFile = [];
	var geo_latitude = ''; 
	var geo_longitude ='';
	var media_page =1;
	var feed_page =1;
	row_count = 2;
	var feedapp = angular.module('feed_app',[]);
	feedapp.config(function ($httpProvider) {
		$httpProvider.defaults.transformRequest = function(data){
			if (data === undefined) {return data;}
			return $.param(data);
		}
	});
	feedapp.directive('scroller', function ($window) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
		  var windowEl = angular.element($window);
		 
			var handler = function() { scope.handler();  }
			windowEl.on('scroll', scope.$apply.bind(scope, handler));
		  
		}
		};
	});
	feedapp.directive('helloMaps', function ($timeout) {
      return function (scope, elem, attrs) {
		   $timeout(function(){
             var mapOptions,
          latitude = attrs.latitude,
          longitude = attrs.longitude,
		  location = attrs.location,
		  going = attrs.going,
          map;		  
        latitude = latitude && parseFloat(latitude, 10) || 43.074688;
        longitude = longitude && parseFloat(longitude, 10) || -89.384294;
        mapOptions = {
          zoom: 8,
          center: new google.maps.LatLng(latitude, longitude)
        };
		
        map = new google.maps.Map(elem[0], mapOptions);
		if(going==1){
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(latitude, longitude),
				icon: baseurl+"/public/images/map_pin-blue.png",
				title:location
			});
		}else{
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(latitude, longitude),
				icon: baseurl+"/public/images/map_pin.png",
				title:location
			});
		}
		// To add the marker to the map, call setMap();
		marker.setMap(map);
		google.maps.event.trigger(map, 'resize'); 
         }, 3000);
        
      };
    }); 
	//feeds controller .......
	feedapp.controller('feedsController',function($scope, $http,$compile){	
		$scope.hideAll = 0; 
		if(group.group_type == 'private'&&group.is_member==0){$scope.hideAll = 1;}
		$scope.is_member = group.is_member; 
		$scope.profile = {};
		$scope.profile.profile_name = '<?php echo $userinfo->user_profile_name; ?>';
		$scope.profile.user_given_name = '<?php echo $userinfo->user_given_name; ?>';
		$scope.profile.profile_photo = '<?php echo $userinfo->profile_photo; ?>';
		$scope.profile.user_id = '<?php echo $userinfo->user_id; ?>';
		$scope.profile.user_fbid = '<?php echo $userinfo->user_fbid; ?>';
		$scope.image_paths = <?php echo json_encode($image_folders); ?>;
		$scope.status_selected = 'text';
		$scope.mygroups = '';
		$scope.mypost = {};
		$scope.mypost.statusText = '';
		$scope.mypost.group_id = '';
		$scope.selectedGroup = 'Choose Group';
		$scope.submit = {};
		$scope.submit.switch = 1;
		$scope.media_type = 'image';
		$scope.showVideoImage = 0;
		$scope.mypost.videourl = '';
		$scope.mypost.videoCaption = '';		 
		$scope.mypost.event_date = '<?php echo date("d-m-Y"); ?>';
		$scope.selectedMemberType = "All members";
		$scope.memberTypes = [];
		$scope.memberTypes =[["all","All members"],["friends","Friends"],["invite","Invite people"]];
		$scope.inviteSelected = 0;
		$scope.allMembers = [];
		$scope.event_search = '';
		$scope.objSelectedUsers = [];
		$scope.enablelist = 0;
		$scope.onlist = 0;
		$scope.media = '';
		$scope.medialike_ajaxloader = 0;
		$scope.group = <?php echo json_encode($group_info); ?>;
		$scope.feeds_row1 = [];
		$scope.feeds_row2 = [];
		$scope.feeds_row3 = [];
		$scope.changeGroup = function(group_id,groupTitle){
			$scope.selectedGroup = groupTitle;
			$scope.mypost.group_id =group_id;
			if($scope.selectedMemberType=='Invite people'){
				$scope.listMembers();	
			}
		}
		$scope.changeMediaType = function(status){
			$scope.media_type = status;
		}
		$scope.video = [];
		$scope.addVideo = function(){  
			 $scope.videoid = $scope.getYouTubeIdFromURL($scope.mypost.videourl);
			 if($scope.videoid!=false){$scope.showVideoImage = 1;}else{alert("Invalid video url")}
        }
		$scope.RemoveVideo = function(){
			$scope.mypost.videourl = '';
			$scope.showVideoImage = 0;
			$scope.videoid = '';
		}
		$scope.getYouTubeIdFromURL = function(url){ 
			var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]{11,11}).*/;
			var match = url.match(regExp);
			if (match) if (match.length >= 2) return match[2];
			return false;
		}
		$scope.submitStatus = function(slected_status){ 
			$scope.status_selected = slected_status;
			switch($scope.status_selected){
				case 'text':
					$scope.SubmitText();
				break;
				case 'media':
					$scope.SubmitMedia();
				break;
				case 'event':
					$scope.SubmitEvent(); 
				break;
			}
		}
		$scope.SubmitText = function(){  
			if($scope.mypost.statusText==''||$scope.mypost.statusText==undefined){
				alert("Status is empty.. Please add something before you submit");
				return false;
			}			
			url  = baseurl+'/discussion/ajaxNewDiscussion';
			$scope.submit.switch =2;
			$http.post(url,
			{	
				group_id:$scope.group.group_id,
				statusText:$scope.mypost.statusText
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.submit.switch =1;
					$scope.mypost.statusText= ''; 
					$scope.mypost.group_id= '';
					$scope.selectedGroup = 'Choose Group';
					 
					$scope.getFeeds(2);
				}else{
					alert(data.return_array.process_info);
					$scope.submit.switch = 1;
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.SubmitMedia = function(){  
			switch($scope.media_type){
				case 'image':					 
					if(upladedFile.length<=0){
						alert("Select one file to upload");
						return false;
					}
					$scope.addMedia();					
				break;
				case 'video':					 
					if($scope.mypost.videourl==''||$scope.mypost.videourl==undefined){
						alert("Select one video to upload");
						return false;
					}
					$scope.addMedia(); 
				break;
			}
		}
		$scope.addMedia =function(){
			url  = baseurl+'/groups/ajaxAddMedia';
			$scope.submit.switch =2;
			var formData = new FormData();	
			if($scope.media_type == 'image'){
				formData.append("mediaImage",upladedFile);
				formData.append("imageCaption",$scope.mypost.caption);
			}
			if($scope.media_type == 'video'){
				formData.append("mediaVideo",$scope.mypost.videourl);
				formData.append("videoCaption",$scope.mypost.videoCaption);
			}
			formData.append("group_id",$scope.group.group_id);
			formData.append("media_type",$scope.media_type);
			$http.post(url,formData,{withCredentials: true, headers: {'Content-Type': undefined }, transformRequest: angular.identity})
			.success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.submit.switch =1;
					$scope.mypost.videoCaption= ''; 
					$scope.mypost.caption= ''; 
					$scope.mypost.group_id= '';
					$scope.showVideoImage = 0;
					$scope.mypost.videourl = ''; 
					upladedFile = [];
					$scope.selectedGroup = 'Choose Group';
					$("#uploaded_img").hide();
					$("#default_img").show();
					$scope.getFeeds(0);
				}else{
					alert(data.return_array.process_info);
					$scope.submit.switch = 1;
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.changeMemberType =  function(id,type){
			$scope.selectedMemberType = type;
			if(id == 'invite'){
				if($scope.group.group_id!=''){
					$scope.listMembers();	
				}else{
					alert("Choose one group");
				}				
			}
		}
		$scope.listMembers =  function(){
			$scope.inviteSelected = 1;
			url  = baseurl+'/groups/getAllActiveMembersExceptMe';				 
			$http.post(url,
			{	
				group_id:$scope.group.group_id					 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.allMembers =data.return_array.members
				}else{
					alert(data.return_array.process_info);						  
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.AddToInvitedUsers = function(user_id,user_name,user_image,user_fbid){
			var incexist = 0;
			angular.forEach($scope.objSelectedUsers, function(dataset){ 
				if(dataset.user_id == user_id){incexist++;}
			});				
			if(incexist == 0){
				$scope.objSelectedUsers.push({
					user_id:user_id,user_name:user_name,user_image:user_image,user_fbid:user_fbid
				});
			}				
		}
		$scope.RemoveFromInvitedUsers = function(user_id){
			var array_index = $scope.getArrayIndex($scope.objSelectedUsers,'user_id',user_id);
			$scope.objSelectedUsers.splice( array_index, 1 );			 
		}
		$scope.getArrayIndex = function(arr_elemnt,key,item){ 
			var incexist = -1;
			for(i=0;i<arr_elemnt.length;i++) {
				if(arr_elemnt[i][key] == item){incexist = i;}
			}
			return incexist;
		}
		$scope.hideList = function(){  
			if(!$scope.onlist)$scope.enablelist = 0;
		}
		$scope.OnListHover = function(){ 
			$scope.onlist = 1;
		}
		$scope.OnListOut =  function(){ 
			$scope.onlist = 0;
			$scope.enablelist = 0;
		}
		$scope.SubmitEvent = function(){
			$scope.mypost.event_location = $(".event_add_location").val(); 
			if($scope.mypost.event_title==''||$scope.mypost.event_title==undefined){
				alert("Event title required");
				return false;
			}
			if($scope.mypost.event_date==''||$scope.mypost.event_date==undefined){
				alert("Event date required");
				return false;
			}
			
			if($scope.mypost.event_location==''||$scope.mypost.event_location==undefined){
				alert("Event location required");
				return false;
			}
			if($scope.mypost.event_description==''||$scope.mypost.event_description==undefined){
				alert("Event description required");
				return false;
			}			 
			$scope.submit.switch = 2;
			url  = baseurl+'/activity/ajaxAddActivity';	
			var invite_members = [];
			angular.forEach($scope.objSelectedUsers, function(dataset){ 
				 invite_members.push(dataset.user_id);
			});
			
			$http.post(url,
			{	
				group_id:$scope.group.group_id,
				event_title:$scope.mypost.event_title,
				event_date:$scope.mypost.event_date,
				event_location:$scope.mypost.event_location,
				event_description:$scope.mypost.event_description,
				event_time:$scope.mypost.event_time,
				selectedMemberType:$scope.selectedMemberType,
				invite_members:invite_members,
				event_location_lat:geo_latitude,
				event_location_lng:geo_longitude
				
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.submit.switch =1;
					$scope.mypost.event_title=''
					$scope.mypost.event_date = '<?php echo date("d-m-Y"); ?>';
					$scope.mypost.event_location ='';
					$scope.mypost.group_id= '';
					$scope.mypost.event_description = '';
					$scope.mypost.event_time = '';
					$scope.selectedMemberType = "All members";
					$scope.objSelectedUsers = [];
					$scope.inviteSelected = 0;					 
					$scope.event_search = '';					 
					$scope.enablelist = 0;
					$scope.onlist = 0;
					$scope.selectedGroup = 'Choose Group';
					$scope.getFeeds(0);
				}else{
					alert(data.return_array.process_info);
					$scope.submit.switch = 1;
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		/*feeds*/
		$scope.feedType = "All";
		$scope.groupFilter = "All Groups";
		$scope.groupFilterSelected = "All";
		$scope.feedGroups ='';  
		$scope.activityFilterList = [];
		$scope.ActivityFilter = "All Activity";
		$scope.ActivityFilterSelected = "all";
		$scope.activityFilterList =[["all","All Activity"],["Interactions","Interactions"],["friends_post","Posts By Friends"],["goingto","Events Im Going To"]];
		$scope.feeds = [];	 
		$scope.selectActivityFilters = function(identity,name){
			$scope.ActivityFilter = name;
			$scope.ActivityFilterSelected = identity;
			$scope.getFeeds(0);
		}
		$scope.feedAjax = 0;
		$scope.getFeeds = function(scrolling){ 
			if($scope.feedAjax ==0){  
				$scope.feedAjax =1;
				if(scrolling ==1){feed_page = feed_page+1;}else{
					feed_page = 1; console.log(feed_page);
					if($scope.is_member==1){row_count = 2;}else{row_count = 1;}					
					$scope.feeds_row1 = [];
					$scope.feeds_row2 = [];
					$scope.feeds_row3 = [];				 
				}
				url  = baseurl+'/groups/getGroupFeed';
				
				$http.post(url,
				{	
					page:feed_page,
					type:$scope.feedType,
					group:$scope.group.group_seo_title,
					activity:$scope.ActivityFilterSelected,
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						if(data.return_array.feeds.length>0){
							$scope.feedsAppend(data.return_array.feeds);
						}
					}else{
						alert(data.return_array.process_info);						 
					}	
					$scope.feedAjax =0;					
				}).error(function(data, status, headers, config) {  
				//	 alert("Error occured. Please try again");
					 $scope.feedAjax =0;
				});
			}
		}
		$scope.feedsAppend = function(feeds){  
			var testINcrement = 0;
			var screenwidth = screen.width;
			var availablerows = 1;
			if(screenwidth<=450){
				availablerows = 1;
			}else if(screenwidth<=768){
				availablerows = 2;
			}else{
				availablerows = 3;
			}
			angular.forEach(feeds, function(dataset){ 
				if(availablerows==1){
					$scope.feeds_row1.push(dataset);
					row_count=1;
				}else if(availablerows==2){
					if(row_count==1){
						$scope.feeds_row1.push(dataset);
					}
					if(row_count==2){
						$scope.feeds_row2.push(dataset);
					}					 
					row_count++;
					if(row_count>2){row_count=1;}
				}else{
					if(row_count==1){
						$scope.feeds_row1.push(dataset);
					}
					if(row_count==2){
						$scope.feeds_row2.push(dataset);
					}
					if(row_count==3){
						$scope.feeds_row3.push(dataset);
					}
					row_count++;
					if(row_count>3){row_count=1;}
				} 	 
			});
			 
		}
		$scope.funLikeAction = function(system_type,content_id){
			var holder_content = $("."+system_type+"_"+content_id).html();
			$("."+system_type+"_"+content_id).html('<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> ');
			if(system_type == 'Media'){
				//$("#"+system_type+"_modelwindow").html('<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> ');
				$scope.medialike_ajaxloader = 1;
			}
			url  = baseurl+'/like/likes';				 
			$http.post(url,
			{	
				system_type:system_type,content_id:content_id,		 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					if(data.return_array.like_count>0){
						var likecontent = '';
						likecontent += '<span class="tootltip_outer">';
						likecontent += '<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction(\''+system_type+'\','+content_id+')" ><i></i></a><a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers(\''+system_type+'\','+content_id+','+data.return_array.like_count+',1)" ng-mouseover="loadToolTip(\''+system_type+'\','+content_id+')" ng-mouseout="unloadToolTip(\''+system_type+'\','+content_id+')">'+data.return_array.like_count+' likes</a>';
						likecontent += '<div class="like_tooltip" id="tooltip_'+system_type+'_'+content_id+'" style="display:none">'
							for(i=0;i<data.return_array.liked_users.length;i++){
								likecontent += '<span>'+data.return_array.liked_users[i]+'</span>';
							}
						likecontent += '</div></span>';
						$("."+system_type+"_"+content_id).html($compile(likecontent)($scope)) ;
						
					}else{
						$("."+system_type+"_"+content_id).html($compile('<a href="javascript:void(0);" class="post_likes liked" ng-click="funUnLikeAction(\''+system_type+'\','+content_id+')" ><i></i></a><a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers(\''+system_type+'\','+content_id+','+data.return_array.like_count+',1)" title="'+data.return_array.liked_users+'"> likes</a>')($scope)) ;
					}
					if(system_type == 'Media'){
						$scope.medialike_ajaxloader = 0;
						$scope.media.likes_counts = data.return_array.like_count;
						$scope.media.is_liked = 1;
						$scope.media.likedUsers = data.return_array.liked_users;
					}
					
				}else{
					alert(data.return_array.process_info);	
				$("."+system_type+"_"+content_id).html($compile(holder_content)($scope));					
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
			$('[data-toggle="tooltip"]').tooltip();
		}
		$scope.funUnLikeAction = function(system_type,content_id){
			var holder_content = $("#"+system_type+"_"+content_id).html();
			$("."+system_type+"_"+content_id).html('<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif"> ');
			if(system_type == 'Media'){
				$scope.medialike_ajaxloader = 1;
			}
			url  = baseurl+'/like/unlikes';				 
			$http.post(url,
			{	
				system_type:system_type,content_id:content_id,		 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					if(data.return_array.like_count>0){
						var likecontent = '';
						likecontent += '<span class="tootltip_outer">';
						likecontent += '<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction(\''+system_type+'\','+content_id+')" ><i></i></a><a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers(\''+system_type+'\','+content_id+','+data.return_array.like_count+',1)" ng-mouseover="loadToolTip(\''+system_type+'\','+content_id+')" ng-mouseout="unloadToolTip(\''+system_type+'\','+content_id+')">'+data.return_array.like_count+' likes</a>';
						likecontent += '<div class="like_tooltip" id="tooltip_'+system_type+'_'+content_id+'" style="display:none">'
							for(i=0;i<data.return_array.liked_users.length;i++){
								likecontent += '<span>'+data.return_array.liked_users[i]+'</span>';
							}
						likecontent += '</div></span>';
						$("."+system_type+"_"+content_id).html($compile(likecontent)($scope)) ;
					}else{
						$("."+system_type+"_"+content_id).html($compile('<a href="javascript:void(0);" class="post_likes" ng-click="funLikeAction(\''+system_type+'\','+content_id+')" ><i></i></a><a href="javascript:void(0);" class="post_likes" data-toggle="tooltip" data-placement="bottom" ng-click="LoadLikedUsers(\''+system_type+'\','+content_id+','+data.return_array.like_count+',1)" title="'+data.return_array.liked_users+'"> likes</a>')($scope)) ;
					}
					if(system_type == 'Media'){
						$scope.medialike_ajaxloader = 0;
						$scope.media.likes_counts = data.return_array.like_count;
						$scope.media.is_liked =0;
						$scope.media.likedUsers = data.return_array.liked_users;	
					}
				}else{
					alert(data.return_array.process_info);	
				$("."+system_type+"_"+content_id).html($compile(holder_content)($scope));					
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
			$('[data-toggle="tooltip"]').tooltip();
		}
		$scope.loadpeople = 0;
		$scope.likedpeoples = [];
		$scope.LoadLikedUsers = function(system_type,content_id,like_count,is_liked){
			 $scope.likedpeoples = [];
			 if(like_count ==1&&is_liked==1){
				return true;
			 }
			 if(like_count > 0){
				$scope.loadpeople = 1;
				$('#likes_popup').modal();				 
				url  = baseurl+'/like/userslist';				 
				$http.post(url,
				{	
					system_type:system_type,content_id:content_id,		 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						$scope.loadpeople = 0;
						$scope.likedpeoples = data.return_array.liked_users;
					}else{
						alert(data.return_array.process_info);	
					$("#"+system_type+"_"+content_id).html($compile(holder_content)($scope));					
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
			 }
		}		 	
		$scope.sentFriendRequest  = function(user){
				 
			url  = baseurl+'/user/sentFriendRequest/'+user;
			$http({
			  method: 'POST',
			  url: url
			}).success(function(data, status, headers, config) {		 
				if(data.return_array.process_info==''){
				 ;
				}else{
					alert(data.return_array.process_info);
				}
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.rejectFriendRequest  = function(user){			 
			url  = baseurl+'/user/rejectFriendRequest/'+user;
			$http({
			  method: 'POST',
			  url: url
			}).success(function(data, status, headers, config) {
				 
				if(data.return_array.process_info==''){
					;
				}else{
					alert(data.return_array.process_info);
				}
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.acceptFriendRequest  = function(user){			 
			url  = baseurl+'/user/acceptFriendRequest/'+user;
			$http({
			  method: 'POST',
			  url: url
			}).success(function(data, status, headers, config) {
				 
				if(data.return_array.process_info==''){
					 ;
				}else{
					alert(data.return_array.process_info);
				}
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.commentText='';
		$scope.LoadCommentWindow = function(system_type,content_id,row_id,row_index){
			var commentForm = '';
			commentForm += '<div class="post_comment-outer" id="post_reply">';
            commentForm += '<div class="header-profile_image comment_profile-img">';
			if($scope.profile.profile_photo!=''&&$scope.profile.profile_photo!=null){
               commentForm += '<img  src="<?php echo $this->basePath(); ?>/public/datagd/profile/'+$scope.profile.user_id+'/'+$scope.profile.profile_photo+'">';
			}else if(($scope.profile.profile_photo==''||$scope.profile.profile_photo==null)&&($scope.profile.user_fbid!=''&&$scope.profile.user_fbid!=null)){
				 commentForm += '<img alt="" src="https://graph.facebook.com/'+$scope.profile.user_fbid+'/picture?width=66&&height=66">';
			}else{ commentForm += '<img  src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">';}
            commentForm += '</div>';
            commentForm += '<div class="post_comment_text post_comment_reply">';
            commentForm += '<div class="commentTextArea" id="commentText_'+system_type+'_'+content_id+'"  contenteditable="true" data-text="Start typing your comment here.." ng-keydown="keyPress($event,\''+system_type+'\','+content_id+');"></div><div id="hashingUsers_'+system_type+'_'+content_id+'"></div>';
            commentForm += '</div>';
            commentForm += '<div class="clear"></div>';
            commentForm += '<div class="post_comment_reply-butns">';
            commentForm += '<a href="javascript:void(0);" class="default_butn_grey" id="cancel_reply" ng-click="RemoveCommentWindow(\''+system_type+'\','+content_id+')">Cancel</a><a href="javascript:void(0);" class="default_butn_violet" ng-click="SendComment(\''+system_type+'\','+content_id+','+row_id+','+row_index+')">Post</a>';
            commentForm += '</div>';
            commentForm += '</div>';
			$("#comment_"+system_type+'_'+content_id).html($compile(commentForm)($scope));
		}
		$scope.keyPress = function(event,system_type,content_id){
			
			if(event.which == 50){
				if(event.shiftKey) {
					
					enableHashing = 1;
					str_search ='';
				}
			}
			if(enableHashing){ 
				if(event.which != 50){
					str_search = str_search+String.fromCharCode(event.keyCode);
				}
				$scope.getFriends(str_search,system_type,content_id);
			}
		}
		$scope.getFriends =function(str_search,system_type,content_id){
			url  = baseurl+'/user/getFriends';
			$http.post(url,
				{	
					str_search:str_search,		 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						if(data.return_array.arrFriends.length>0){
							var hasusers = '<ul class="hashUserList">';
							angular.forEach(data.return_array.arrFriends, function(dataset){				
								hasusers+= '<li><a href="javascript:void(0)" ng-click="addTOhashed(\''+system_type+'\','+content_id+',\''+dataset.user_profile_name+'\','+dataset.user_id+',\''+dataset.user_given_name+'\')">';
								if(dataset.profile_photo!=''&&dataset.profile_photo!=null){
								   hasusers += '<img  src="<?php echo $this->basePath(); ?>/public/datagd/profile/'+dataset.user_id+'/'+dataset.profile_photo+'">';
								 }else if((dataset.profile_photo==''||dataset.profile_photo==null)&&(dataset.user_fbid!=''&&dataset.user_fbid!=null)){
										hasusers += '<img alt="" src="https://graph.facebook.com/'+dataset.user_fbid+'/picture?width=66&&height=66">';
								 }else{ hasusers += '<img  src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">';}
								hasusers += dataset.user_given_name;
								hasusers+= '</a>';
								hasusers+= '</li>';
							});
							hasusers+= '</ul>';
							$("#hashingUsers_"+system_type+"_"+content_id).html($compile(hasusers)($scope));
						}else{
							$("#hashingUsers_"+system_type+"_"+content_id).html('');
							str_search = '';
							enableHashing=0;
						}						
					}
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
		}
		$scope.addTOhashed = function(system_type,content_id,profilename,user_id,user_given_name){ 
			commentText=$("#commentText_"+system_type+'_'+content_id).html();
			str_search = str_search.toLowerCase();
			text = commentText.replace('@'+str_search,'<a class="hashed_user" href="<?php echo $this->basePath(); ?>/'+profilename+'">'+user_given_name+'</a>&nbsp;'); 
			$("#commentText_"+system_type+'_'+content_id).html(text);
			$("#hashingUsers_"+system_type+"_"+content_id).html('');
			hashedUser.push(user_id);
			str_search = '';
			enableHashing=0;
		}
		$scope.RemoveCommentWindow = function(system_type,content_id){
			$("#comment_"+system_type+'_'+content_id).html('');
			hashedUser=[];
			str_search = '';
			enableHashing=0;
		}
		$scope.SendComment = function(system_type,content_id,row_id,row_index){
			var buttn_content =  $("#comment_"+system_type+'_'+content_id+" .post_comment_reply-butns").html();
			$("#comment_"+system_type+'_'+content_id+" .post_comment_reply-butns").html('<img src="<?php echo $this->basePath(); ?>/public/images/ajax_loader.gif">');
			$scope.commentText=$("#commentText_"+system_type+'_'+content_id).html();
			if($scope.commentText!=''){
				url  = baseurl+'/comment/comments';
				$http.post(url,
				{	
					system_type:system_type,content_id:content_id,txt_comment:$scope.commentText,hashedUser:hashedUser		 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						$("#commentText_"+system_type+'_'+content_id).html('');
						//$("#comment_"+system_type+'_'+content_id).html('');		
						$("#comment_"+system_type+'_'+content_id+" .post_comment_reply-butns").html($compile(buttn_content)($scope)); 
						//$scope.LoadCommentList(system_type,content_id,row_id,row_index,1);

						var newcomment = [];
						newcomment['comment_content'] = $scope.commentText;
						newcomment['comment_id'] = data.return_array.comment_id;
						newcomment['comment_time'] = '0m';
						newcomment['user_given_name'] = $scope.profile.user_given_name;
						newcomment['user_id'] = $scope.profile.user_id;
						newcomment['user_register_type'] = '';
						newcomment['user_fbid'] = $scope.profile.user_fbid;
						newcomment['profile_photo'] = $scope.profile.profile_photo;
						newcomment['liked_users'] = 0;
						newcomment['likes_count'] = 0;
						newcomment['islike'] = 0;						
						newcomment['user_profile_name'] = $scope.profile.user_profile_name;
						newcomment['allowedit'] = 1;
						
						if(row_id==1){
							$scope.feeds_row1[row_index].content.is_commented = 1;							
							$scope.feeds_row1[row_index].content.comment_counts = data.return_array.comment_count;						
							$scope.feeds_row1[row_index].content.comments.unshift(newcomment);
						}
						if(row_id==2){
							$scope.feeds_row2[row_index].content.is_commented = 1;
							$scope.feeds_row2[row_index].content.comment_counts = data.return_array.comment_count;
							$scope.feeds_row2[row_index].content.comments.unshift(newcomment);							 
						}
						if(row_id==3){
							$scope.feeds_row3[row_index].content.is_commented = 1;
							$scope.feeds_row3[row_index].content.comment_counts = data.return_array.comment_count;
							$scope.feeds_row3[row_index].content.comments.unshift(newcomment);							 
						}
						hashedUser=[];
					}else{
						alert(data.return_array.process_info);		
						$("#comment_"+system_type+'_'+content_id+" .post_comment_reply-butns").html($compile(buttn_content)($scope));
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
			}else{
				alert("Type your comments before submit");
			}
		}
		$scope.LoadCommentList = function(system_type,content_id,rowid,rowindex,page){	
			hashedUser=[];
			 if(page=1){
				 if(rowid==1){
					 if($scope.feeds_row1[rowindex].viewComment==1){
						 $scope.feeds_row1[rowindex].viewComment = 0;
					 }else{
						 $scope.feeds_row1[rowindex].viewComment = 1;
					 }					 
				 }
				 if(rowid==2){
					 if($scope.feeds_row2[rowindex].viewComment==1){
						 $scope.feeds_row2[rowindex].viewComment = 0;
					 }else{
						 $scope.feeds_row2[rowindex].viewComment = 1;
					 }					 
				 }
				 if(rowid==3){
					 if($scope.feeds_row3[rowindex].viewComment==1){
						 $scope.feeds_row3[rowindex].viewComment = 0;
					 }else{
						 $scope.feeds_row3[rowindex].viewComment = 1;
					 }					 
				 }
			 }
			url  = baseurl+'/comment/getComments';
			 $http.post(url,
			 {	
				system_type:system_type,content_id:content_id,page:page,		 
			 },
			 {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			 ).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$("#comment_"+system_type+"_list"+content_id).addClass("open");
					if(rowid==1){
						if(page==1){
							$scope.feeds_row1[rowindex].content.comments = data.return_array.comments;
							$scope.feeds_row1[rowindex].content.comment_page= page;
						}else{
							 
							angular.forEach(data.return_array.comments, function(dataset){	
								$scope.feeds_row1[rowindex].content.comments.push(dataset);
							});
							 
							$scope.feeds_row1[rowindex].content.comment_page= page;
						}					
					}
					if(rowid==2){
						if(page==1){
							$scope.feeds_row2[rowindex].content.comments = data.return_array.comments;
							$scope.feeds_row2[rowindex].content.comment_page= page;
						}else{
							angular.forEach(data.return_array.comments, function(dataset){	
								$scope.feeds_row2[rowindex].content.comments.push(dataset);
							});
							$scope.feeds_row2[rowindex].content.comment_page= page;
						}					
					}
					if(rowid==3){
						if(page==1){
							$scope.feeds_row3[rowindex].content.comments = data.return_array.comments;
							$scope.feeds_row3[rowindex].content.comment_page= page;
						}else{
							angular.forEach(data.return_array.comments, function(dataset){	
								$scope.feeds_row3[rowindex].content.comments.push(dataset);
							});
							$scope.feeds_row3[rowindex].content.comment_page= page;
						}					
					}
				}else{
					alert(data.return_array.process_info);					 				
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.showCommentText = function(content,id){
			$("#comment_content_text_"+id).html(content);
		}
		$scope.mediaLoader = 0;		
		$scope.media_comments = [];
		$scope.loadMedia = function(media_id){ 
			$scope.medialist = 0;
			url  = baseurl+'/groups/getMedia';
			 $http.post(url,
			 {	
				media_id:media_id,		 
			 },
			 {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			 ).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.mediaLoader = 0;
					$scope.media_comments = data.return_array.comments;
					$scope.media =  data.return_array.group_media;
					if($scope.media.media_type=='video'){
						$("#video_content").show();
						$scope.loadVideoContent($scope.media.media_content);						
					}else{
						$("#video_content").html('');
						$("#video_content").hide();
					}
				}else{
					alert(data.return_array.process_info);					 				
				}				     
			 }).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			 });
			 
		}
		$scope.loadYoutubeVideo = function(video_id){
			 $("#video_content").html('<iframe style="overflow:hidden;height:100%;width:100%" width="100%" height="100%" src="http://www.youtube.com/embed/'+video_id+'" frameborder="0" allowfullscreen></iframe>');
		}
		$scope.loadVideoContent = function(videoContent){ 
			var video_id = $scope.getYouTubeIdFromURL(videoContent);
			if(video_id!=''){ 
				$scope.loadYoutubeVideo(video_id);
			}
		}
		$scope.enableScrollbar = function(){
			$('#comment_container_outer').slimScroll({
				color: '#00f'
			});
		}
		$scope.commentPage =2;
		$scope.hideloadmore =0;
		$scope.showReply = 1;
		$scope.enableCommentReplyMedia= function(){
			if($scope.showReply ==1){
				$scope.showReply = 0;
			}else{
				$scope.showReply = 1;
			}
		}
		$scope.txtMediaComment = '';
		$scope.loadmoreCommentFromPopup = function(media_id){
			url  = baseurl+'/comment/getComments';
			$http.post(url,
			 {	
				system_type:'Media',content_id:media_id,page:$scope.commentPage,		 
			 },
			 {headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			 ).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						$scope.media_comments = $scope.media_comments.concat(data.return_array.comments);
						$scope.commentPage = $scope.commentPage+1;
						if(data.return_array.comments.length<=0){
							$scope.hideloadmore =1;
						}
				}else{
					alert(data.return_array.process_info);					 				
				}				     
			 }).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			 });
		}
		$scope.removeCommentBox = function(){  
			$scope.showReply = 0;
				$("#txtMediaCommenthashed").html('');
			hashedUser=[];
			str_search = '';
			enableHashing=0;
			$("#txtMediaComment").html('');
			//$("#post_reply-media").hide();
		}
		$scope.keyPressMedia = function(event,system_type,content_id){
			
			if(event.which ==50){
				if(event.shiftKey) {
					
					enableHashing = 1;
					str_search ='';
				}
			}
			if(enableHashing){ 
				if(event.which != 50){
					str_search = str_search+String.fromCharCode(event.keyCode);
				}
				$scope.getFriendsMedia(str_search,system_type,content_id);
			}
		}
		$scope.getFriendsMedia =function(str_search,system_type,content_id){
			url  = baseurl+'/user/getFriends';
			$http.post(url,
				{	
					str_search:str_search,		 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						if(data.return_array.arrFriends.length>0){
							var hasusers = '<ul class="hashUserList">';
							angular.forEach(data.return_array.arrFriends, function(dataset){				
								hasusers+= '<li><a href="javascript:void(0)" ng-click="addTOhashedMedia(\''+system_type+'\','+content_id+',\''+dataset.user_profile_name+'\','+dataset.user_id+',\''+dataset.user_given_name+'\')">';
								if(dataset.profile_photo!=''&&dataset.profile_photo!=null){
								   hasusers += '<img  src="<?php echo $this->basePath(); ?>/public/datagd/profile/'+dataset.user_id+'/'+dataset.profile_photo+'">';
								 }else if((dataset.profile_photo==''||dataset.profile_photo==null)&&(dataset.user_fbid!=''&&dataset.user_fbid!=null)){
										hasusers += '<img alt="" src="https://graph.facebook.com/'+dataset.user_fbid+'/picture?width=66&&height=66">';
								 }else{ hasusers += '<img  src="<?php echo $this->basePath(); ?>/public/images/noimg.jpg">';}
								hasusers += dataset.user_given_name;
								hasusers+= '</a>';
								hasusers+= '</li>';
							});
							hasusers+= '</ul>';
							$("#txtMediaCommenthashed").html($compile(hasusers)($scope));
						}else{
							$("#txtMediaCommenthashed").html('');
							str_search = '';
							enableHashing=0;
						}						
					}
				}).error(function(data, status, headers, config) {
				//	 alert("Error occured. Please try again");
				});
		}
		$scope.addTOhashedMedia = function(system_type,content_id,profilename,user_id,user_given_name){ 
			commentText=$("#txtMediaComment").html();
			str_search = str_search.toLowerCase();
			text = commentText.replace('@'+str_search,'<a class="hashed_user" href="<?php echo $this->basePath(); ?>/'+profilename+'">'+user_given_name+'</a>&nbsp;'); 
			$("#txtMediaComment").html(text);
			$("#txtMediaCommenthashed").html('');
			hashedUser.push(user_id);
			str_search = '';
			enableHashing=0;
		}
		$scope.loadCommmentText = function(arrayIndex,commenttext){
			 $('#comment_text_'+arrayIndex).html(commenttext);
		}
		$scope.postComment = function(media_id){
			$scope.txtMediaComment = $("#txtMediaComment").val();
			if($scope.txtMediaComment!=''){
			url  = baseurl+'/comment/comments';
				$http.post(url,
				{	
					system_type:"Media",content_id:media_id,txt_comment:$scope.txtMediaComment,hashedUser:hashedUser,	 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){	
						hashedUser=[];
						$scope.media.is_commented = 1;
						$scope.media.comment_count = $scope.media.comment_count;
						var newcomment = [];
						newcomment['likes_count'] = 0;
						newcomment['islike'] = 0;
						newcomment['comment_content'] = $scope.txtMediaComment;						
						newcomment['comment_id'] = data.return_array.comment_id;
						newcomment['comment_time'] = 'just now';
						newcomment['user_given_name'] = $scope.profile.user_given_name;
						newcomment['user_id'] = $scope.profile.user_id;
						newcomment['user_register_type'] = '';
						newcomment['user_fbid'] = $scope.profile.user_fbid;
						newcomment['profile_photo'] = $scope.profile.profile_photo;
						newcomment['likedUsers'] = [];
						newcomment['user_profile_name'] = $scope.profile.profile_name;
						newcomment['allowedit'] = 1;						 
						$scope.media_comments.unshift(newcomment);	
						 $scope.txtMediaComment = '';	
						 $scope.showReply = 0;	
						 $("#txtMediaComment").val('');
						   
						 var comment_content	='';
						 comment_content += '<a href="javascript:void(0);" class="post_reply post_comments comented" ng-click="LoadCommentWindow(\'Media\','+media_id+')"><i></i></a>';
						 comment_content += '<a href="javascript:void(0);" class="post_comments" ng-click="LoadCommentList(\'Media\','+media_id+','+$scope.media.comment_count+',1)"> '+$scope.media.comment_count+' comments</a>';
						 $("#CntComment_Media_"+media_id).html($compile(comment_content)($scope));
					}else{
						alert(data.return_array.process_info);		
						 
					}				     
				}).error(function(data, status, headers, config) {
				//	 alert("Error occured. Please try again");
				});
			}else{alert("Type your comments before submit");}
		}
		$scope.groupAllMediaContent = [];
		$scope.medialist = 0;
		$scope.showLoadmore = 1;
		$scope.loadAllMedia = function(group_id){
			url  = baseurl+'/groups/getAllMedia';
			$http.post(url,
			{	
				page:media_page,
				group_id:group_id 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){		
					if(data.return_array.group_media.length>0){
						if(data.return_array.group_media.length<10)$scope.showLoadmore = 0;
						if(media_page>1){
							$scope.groupAllMediaContent= $scope.groupAllMediaContent.concat(data.return_array.group_media);
						}else{							
							$scope.groupAllMediaContent=data.return_array.group_media;
						}
						$scope.medialist = 1;
						  
					}							
				}else{
					alert(data.return_array.process_info);		
					 
				}				     
			}).error(function(data, status, headers, config) {
			//	 alert("Error occured. Please try again");
			});
		}
		$scope.enableScrollbarMedia = function(){
			 $('#media_list').slimScroll({
				color: '#00f'
			});
		}
		$scope.LoadmoreMedia = function(group_id){
			media_page = media_page+1;
			$scope.loadAllMedia(group_id);
		}
		$scope.QuitRSVP = function(activity_id,row_id,row_count){
			url  = baseurl+'/activity/quitactivity';
			$http.post(url,
			{	
				activity_id:activity_id,					 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){		
					if(row_id == 1){
						$scope.feeds_row1[row_count].content.is_going = 0;
					}
					if(row_id == 2){
						$scope.feeds_row2[row_count].content.is_going = 0;
					}
					if(row_id == 3){
						$scope.feeds_row3[row_count].content.is_going = 0;
					}
					var count_rsvp = $("#activityrsvpcount_"+activity_id).text();
					count_rsvp = (count_rsvp*1)-1;
					$("#map_Activity_"+activity_id).html($compile('<div class="google-map" hello-maps="" latitude="'+data.return_array.activity.group_activity_location_lat+'" longitude="'+data.return_array.activity.group_activity_location_lng+'"  location="'+data.return_array.activity.group_activity_location+'" going="'+data.return_array.activity.is_going+'"></div>')($scope));
					$("#activityrsvpcount_"+activity_id).text(count_rsvp);
				}else{
					alert(data.return_array.process_info);						 
				}				     
			}).error(function(data, status, headers, config) {
			//	 alert("Error occured. Please try again");
			});
		}
		$scope.JoinRSVP = function(activity_id,row_id,row_count){
			url  = baseurl+'/activity/joinactivity';
			$http.post(url,
				{	
					activity_id:activity_id,					 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){		
						if(row_id == 1){
							$scope.feeds_row1[row_count].content.is_going = 1;
						}
						if(row_id == 2){
							$scope.feeds_row2[row_count].content.is_going = 1;
						}
						if(row_id == 3){
							$scope.feeds_row3[row_count].content.is_going = 1;
						}	
						var count_rsvp = $("#activityrsvpcount_"+activity_id).text();
						count_rsvp = (count_rsvp*1)+1;
						$("#map_Activity_"+activity_id).html($compile('<div class="google-map" hello-maps="" latitude="'+data.return_array.activity.group_activity_location_lat+'" longitude="'+data.return_array.activity.group_activity_location_lng+'"  location="'+data.return_array.activity.group_activity_location+'" going="'+data.return_array.activity.is_going+'"></div>')($scope));
						$("#activityrsvpcount_"+activity_id).text(count_rsvp);						
					}else{
						alert(data.return_array.process_info);						 
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
		}
		var lastScrollPosition = 0;
		$scope.handler = function() {	 
			 
		  if($(window).scrollTop()>lastScrollPosition+50){	
			lastScrollPosition = $(window).scrollTop()
			$scope.getFeeds(1);
		  }
		}
		$scope.loadPopupToolTip = function(type,id){
			$("#tooltip_popup_"+type+"_"+id).show();
		}
		$scope.unloadPopupToolTip = function(type,id){
			$("#tooltip_popup_"+type+"_"+id).hide();
		}
		$scope.rsvplist = [];
		$scope.rsvppage =1;
		$scope.selectedActivity = '';
		$scope.loadmore = 1;
		$scope.commenteditEnableList = [];
		$scope.loadRsvpList = function(activity_id,filter_by,pageselected){
			$scope.selectedActivity = activity_id;
			$('#rsvplist_outer').slimScroll({
				height: '350px',color: '#00f'
			});
			
			if(pageselected==1){
				rsvppage = 1;
			}
			$scope.filter_by = filter_by;
			url  = baseurl+'/activity/rsvplist';
			var system_type = 'Activity';
			var content_id = activity_id;
			$http.post(url,
			{	
				content_id:content_id,page:rsvppage,filter_by:filter_by
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					if(data.return_array.activityrsvp_info.length>0){
						$('#rsvp_popup').modal();
						$scope.loadmore = 1;
					}else{$scope.loadmore = 0;}
					if(rsvppage==1){
						$scope.rsvplist =  data.return_array.activityrsvp_info;
					}else{						 
						$scope.rsvplist = $.merge($scope.rsvplist, data.return_array.activityrsvp_info);						 			 
					}
					rsvppage++;	
					$scope.rsvppage =rsvppage;		 
				}else{
					alert(data.return_array.process_info);					 				
				}				     
			}).error(function(data, status, headers, config) {
				//alert("Error occured. Please try again");
			});
		}
		
		$scope.enableCommentEdit = function(comment_index){
			$scope.commenteditEnableList.push(comment_index);
		}
		$scope.ifexistinEditList = function(comment_index){
			var exist_flag= 0;
			angular.forEach($scope.commenteditEnableList, function(dataset){
				if(dataset == comment_index){exist_flag++;}
			});
			if(exist_flag == 0){return false;}else{return true;}
		}
		$scope.RemoveCommentEditWindow = function(comment_index){
			var array_index = $scope.getArrayIndexEdit($scope.commenteditEnableList,comment_index);
			$scope.commenteditEnableList.splice( array_index, 1 );		
		}		 
		$scope.getArrayIndexEdit = function(arr_elemnt,item){ 
			var incexist = -1;
			for(i=0;i<arr_elemnt.length;i++) {
				if(arr_elemnt[i] == item){incexist = i;}
			}
			return incexist;
		}
		$scope.getArrayIndex = function(arr_elemnt,key,item){ 
			var incexist = -1;
			for(i=0;i<arr_elemnt.length;i++) {
				if(arr_elemnt[i][key] == item){incexist = i;}
			}
			return incexist;
		}
		$scope.SendEditComment = function(comment_id,row_count,row_index){ 
			var edited_comment = $("#editcomment_text_"+comment_id).val();
			if(edited_comment==''){
				alert("Type your comments to submit");
			}else{
				url  = baseurl+'/comment/editComment';
				$http.post(url,
				{	
					edited_comment:edited_comment,comment_id:comment_id,		 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){  
						$scope.commentText[comment_id] = edited_comment;						 
						$scope.RemoveCommentEditWindow(comment_id);
						if(row_count==1){
							var array_index = $scope.getArrayIndex($scope.feeds_row1[row_index].content.comments,'comment_id',comment_id);
							$scope.feeds_row1[row_index].content.comments[array_index].comment_content = edited_comment;	
						 
						}
						if(row_count==2){
							var array_index = $scope.getArrayIndex($scope.feeds_row2[row_index].content.comments,'comment_id',comment_id);
							$scope.feeds_row2[row_index].content.comments[array_index].comment_content = edited_comment;	
						}
						if(row_count==3){
							var array_index = $scope.getArrayIndex($scope.feeds_row3[row_index].content.comments,'comment_id',comment_id);
							$scope.feeds_row3[row_index].content.comments[array_index].comment_content = edited_comment;	
						}
					}else{
						alert(data.return_array.process_info);				 					
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
				
			}
		}
		$scope.SendMediaEditComment = function(comment_id,media_id){ 
			var edited_comment = $("#Mediaeditcomment_text_"+comment_id).val();
			if(edited_comment==''){
				alert("Type your comments to submit");
			}else{
				url  = baseurl+'/comment/editComment';
				$http.post(url,
				{	
					edited_comment:edited_comment,comment_id:comment_id,		 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						$scope.commentText[comment_id] = edited_comment;	
						var array_index = $scope.getArrayIndex($scope.media_comments,'comment_id',comment_id);
						$scope.media_comments[array_index].comment_content = edited_comment;						 
						$scope.RemoveCommentEditWindow(comment_id);
						$scope.showCommentText(edited_comment,comment_id);
						var row_index = 0;	
						angular.forEach($scope.feeds_row1, function(dataset){
							if(dataset.type == 'New Media' && dataset.content.group_media_id == media_id){
								i=0;
								angular.forEach($scope.feeds_row1[row_index].content.comments, function(commentdataset){
									if(commentdataset.comment_id == comment_id) {
										$scope.feeds_row1[row_index].content.comments[i].comment_content = edited_comment;
									}
									i++;
								});
							}
							row_index++;
						});
						var row_index = 0;	
						angular.forEach($scope.feeds_row2, function(dataset){
							if(dataset.type == 'New Media' && dataset.content.group_media_id == media_id){
								i=0;
								angular.forEach($scope.feeds_row2[row_index].content.comments, function(commentdataset){
									if(commentdataset.comment_id == comment_id) {
										$scope.feeds_row2[row_index].content.comments[i].comment_content = edited_comment;
									}
									i++;
								});
							}
							row_index++;
						});
						var row_index = 0;	
						angular.forEach($scope.feeds_row3, function(dataset){
							if(dataset.type == 'New Media' && dataset.content.group_media_id == media_id){
								i=0;
								angular.forEach($scope.feeds_row3[row_index].content.comments, function(commentdataset){
									if(commentdataset.comment_id == comment_id) {
										$scope.feeds_row3[row_index].content.comments[i].comment_content = edited_comment;
									}
									i++;
								});
							}
							row_index++;
						});
						 
					}else{
						alert(data.return_array.process_info);				 					
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});
				
			}
		}
		
		$scope.DeleteComment = function(comment_id,row_count,row_index){
			 
			url  = baseurl+'/comment/deleteComment';
			$http.post(url,
			{	
				comment_id:comment_id,		 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					
					$scope.RemoveCommentEditWindow(comment_id);
					$("#comment_list_"+comment_id).remove();
					if(row_count==1){
						var array_index = $scope.getArrayIndex($scope.feeds_row1[row_index].content.comments,'comment_id',comment_id);
						$scope.feeds_row1[row_index].content.comments.splice( array_index, 1 );	
						$scope.feeds_row1[row_index].content.comment_counts = ($scope.feeds_row1[row_index].content.comment_counts*1)-1;
					 
					}
					if(row_count==2){
						var array_index = $scope.getArrayIndex($scope.feeds_row2[row_index].content.comments,'comment_id',comment_id);
						$scope.feeds_row2[row_index].content.comments.splice( array_index, 1 );	
						$scope.feeds_row2[row_index].content.comment_counts = ($scope.feeds_row2[row_index].content.comment_counts*1)-1;
					}
					if(row_count==3){
						var array_index = $scope.getArrayIndex($scope.feeds_row3[row_index].content.comments,'comment_id',comment_id);
						$scope.feeds_row3[row_index].content.comments.splice( array_index, 1 );	
						$scope.feeds_row3[row_index].content.comment_counts = ($scope.feeds_row3[row_index].content.comment_counts*1)-1;
					} 
					 
				}else{
					alert(data.return_array.process_info);				 					
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
				
			 
		}
		$scope.DeleteCommentMedia = function(comment_id,media_id){
			 
			url  = baseurl+'/comment/deleteComment';
			$http.post(url,
			{	
				comment_id:comment_id,		 
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					
					$scope.RemoveCommentEditWindow(comment_id);
					$("#comment_list_"+comment_id).remove();
					var row_index = 0;	
					var array_index = $scope.getArrayIndex($scope.media_comments,'comment_id',comment_id);
					$scope.media_comments.splice( array_index, 1 );
					angular.forEach($scope.feeds_row1, function(dataset){
						if(dataset.type == 'New Media' && dataset.content.group_media_id == media_id){
							i=0;
							angular.forEach($scope.feeds_row1[row_index].content.comments, function(commentdataset){
								if(commentdataset.comment_id == comment_id) {
									$scope.feeds_row1[row_index].content.comments.splice( i, 1 );										 
									$scope.feeds_row1[row_index].content.comment_counts = ($scope.feeds_row1[row_index].content.comment_counts*1)-1;
								}
								i++;
							});
						}
						row_index++;
					});
					var row_index = 0;	
					angular.forEach($scope.feeds_row2, function(dataset){
						if(dataset.type == 'New Media' && dataset.content.group_media_id == media_id){
							i=0;
							angular.forEach($scope.feeds_row2[row_index].content.comments, function(commentdataset){
								if(commentdataset.comment_id == comment_id) {
									$scope.feeds_row2[row_index].content.comments.splice( i, 1 );										 
									$scope.feeds_row2[row_index].content.comment_counts = ($scope.feeds_row2[row_index].content.comment_counts*1)-1;
								}
								i++;
							});
						}
						row_index++;
					});
					var row_index = 0;	
					angular.forEach($scope.feeds_row3, function(dataset){
						if(dataset.type == 'New Media' && dataset.content.group_media_id == media_id){
							i=0;
							angular.forEach($scope.feeds_row3[row_index].content.comments, function(commentdataset){
								if(commentdataset.comment_id == comment_id) {
									$scope.feeds_row3[row_index].content.comments.splice( i, 1 );										 
									$scope.feeds_row3[row_index].content.comment_counts = ($scope.feeds_row2[row_index].content.comment_counts*1)-1;
								}
								i++;
							});
						}
						row_index++;
					});					 
				}else{
					alert(data.return_array.process_info);				 					
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
				
			 
		}
		$scope.EditFeedActivityContent = [];
		$scope.EditFeedDiscussionContent = [];
		$scope.EditFeedMediaContent = [];
		$scope.feedEditSystemType='';
		$scope.ajax_loader_feededit = 1;
		$scope.EditFeedrow_count = '';
		$scope.EditFeedrow_index = '';
		$scope.editLocation ='';
		$scope.editFeed =function(system_type,content_id,row_count,row_index){
			$scope.ajax_loader_feededit = 1
			url  = baseurl+'/groups/getoneFeed';
			$scope.feedEditSystemType=system_type;
			$scope.EditFeedrow_count = row_count;
			$scope.EditFeedrow_index = row_index;
			$http.post(url,
			{	
				system_type:system_type,content_id:content_id
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.ajax_loader_feededit =0;
					if(system_type == 'Activity'){
						$scope.EditFeedActivityContent = data.return_array.feed_details;
						current_lat =$scope.EditFeedActivityContent.group_activity_location_lat;
						current_lng =$scope.EditFeedActivityContent.group_activity_location_lng;
						$scope.editLocation = $scope.EditFeedActivityContent.group_activity_location;
						geo_latitude = current_lat;
						geo_longitude = current_lng;
					}
					if(system_type == 'Discussion'){
						$scope.EditFeedDiscussionContent = data.return_array.feed_details;
					}
					if(system_type == 'Media'){
						$scope.EditFeedMediaContent = data.return_array.feed_details;
					}
				}else{
					alert(data.return_array.process_info);				 					
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});
		}
		$scope.UpdateStatus = function(system_type,content_id,row_count,row_index){
			if(system_type == 'Discussion'){
				var Discussion_data = $("#editDiscussion").val();
				if(Discussion_data==''){
					alert("Enter status to update")
				}else{
					url  = baseurl+'/discussion/edit';
					 
					$http.post(url,
					{	
						system_type:system_type,content_id:content_id,content:Discussion_data
					},
					{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
					).success(function(data, status, headers, config) {
						if(data.return_array.process_status == 'success'){
							alert("Sucessfully Updated your post")
							if(row_count == 1){
								$scope.feeds_row1[row_index].content.group_discussion_content = Discussion_data;
							}
							if(row_count == 2){
								$scope.feeds_row2[row_index].content.group_discussion_content = Discussion_data;
							}
							if(row_count == 3){
								$scope.feeds_row3[row_index].content.group_discussion_content = Discussion_data;
							}
							$("#feed_edit").modal("hide");
						}else{
							alert(data.return_array.process_info);				 					
						}				     
					}).error(function(data, status, headers, config) {
						// alert("Error occured. Please try again");
					});			
				}
			}else if(system_type == 'Media'){
				var caption = $("#feed_edit_caption").val();
				url  = baseurl+'/groups/editMedia';					 
				$http.post(url,
				{	
					system_type:system_type,content_id:content_id,content:caption
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						alert("Sucessfully Updated your post")
						if(row_count == 1){
							$scope.feeds_row1[row_index].content.media_caption = caption;
						}
						if(row_count == 2){
							$scope.feeds_row2[row_index].content.media_caption = caption;
						}
						if(row_count == 3){
							$scope.feeds_row3[row_index].content.media_caption = caption;
						}
						$("#feed_edit").modal("hide");
					}else{
						alert(data.return_array.process_info);				 					
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});		
			}else if(system_type == 'Activity'){
				var title = $("#event_title_edit").val();
				var event_date = $("#event_date_edit").val();
				var event_time = $("#event_time_edit").val();
				var event_location = $(".edit_location").val();
				var event_description = $("#event_description_edit").val();
				var event_location_lat= geo_latitude;
				var event_location_lng= geo_longitude;
				var edit_error = 0;
				if(title==''){
					alert("Enter event title");
					edit_error++;
				}
				if(event_date==''){
					alert("Enter event date");
					edit_error++;
				}
				if(event_location==''){
					alert("Enter event location");
					edit_error++;
				}
				url  = baseurl+'/activity/editActivity';					 
				$http.post(url,
				{	
					system_type:system_type,
					content_id:content_id,
					title:title,
					event_date:event_date,
					event_time:event_time,
					event_location:event_location,
					event_description:event_description,
					event_location_lat:event_location_lat,
					event_location_lng:event_location_lng,
					 
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						alert("Sucessfully Updated your post")
						if(row_count == 1){
							$scope.feeds_row1[row_index].content.group_activity_title = title;
							$scope.feeds_row1[row_index].content.group_activity_location = event_location;
							$scope.feeds_row1[row_index].content.group_activity_location_lat = event_location_lat;
							$scope.feeds_row1[row_index].content.group_activity_location_lng = event_location_lng;
							$scope.feeds_row1[row_index].content.group_activity_content = event_description;
							$scope.feeds_row1[row_index].content.group_activity_start_timestamp = data.return_array.group_activity_start_timestamp;
						}
						if(row_count == 2){
							$scope.feeds_row2[row_index].content.group_activity_title = title;
							$scope.feeds_row2[row_index].content.group_activity_location = event_location;
							$scope.feeds_row2[row_index].content.group_activity_location_lat = event_location_lat;
							$scope.feeds_row2[row_index].content.group_activity_location_lng = event_location_lng;
							$scope.feeds_row2[row_index].content.group_activity_content = event_description;
							$scope.feeds_row2[row_index].content.group_activity_start_timestamp = data.return_array.group_activity_start_timestamp;
						}
						if(row_count == 3){
							$scope.feeds_row3[row_index].content.group_activity_title = title;
							$scope.feeds_row3[row_index].content.group_activity_location = event_location;
							$scope.feeds_row3[row_index].content.group_activity_location_lat = event_location_lat;
							$scope.feeds_row3[row_index].content.group_activity_location_lng = event_location_lng;
							$scope.feeds_row3[row_index].content.group_activity_content = event_description;
							$scope.feeds_row3[row_index].content.group_activity_start_timestamp = data.return_array.group_activity_start_timestamp;
						}
						$("#feed_edit").modal("hide");
					}else{
						alert(data.return_array.process_info);				 					
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});		
			}
		}
		$scope.loadDatePicker = function(){
			enableDatepicker();
			addLocationValue($scope.editLocation);
		}		 
		$scope.feedDeleteSystemType='';
		$scope.ajax_loader_feeddelte = 1;
		$scope.DeleteFeedrow_count = '';
		$scope.DeleteFeedrow_index = '';		 
		$scope.deleteFeed =function(system_type,content_id,row_count,row_index){
			$scope.ajax_loader_feeddelte = 0;			 
			$scope.feedDeleteSystemType=system_type;
			$scope.DeleteFeedrow_count = row_count;
			$scope.DeleteFeedrow_index = row_index;
			$scope.DeleteFeedrow_content_id = content_id;
			  
		}
		$scope.CancelRemovePost = function(){
			$scope.feedDeleteSystemType='';
			$scope.ajax_loader_feeddelte = 1;
			$scope.DeleteFeedrow_count = '';
			$scope.DeleteFeedrow_index = '';
			$("#feed_delete").modal("hide");
		}
		$scope.RemovePost = function(system_type,content_id,row_count,row_index){
			url  = '';
			if(system_type == 'Discussion'){
				url  = baseurl+'/discussion/delete';	
			}
			if(system_type == 'Media'){
				url  = baseurl+'/groups/deleteMedia';		
			}
			if(system_type == 'Activity'){
				url  = baseurl+'/activity/deleteActivity';	
			}
			if(url != ''){				 				 
				$http.post(url,
				{	
					system_type:system_type,content_id:content_id
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						alert("Sucessfully removed your post")
						if(row_count == 1){
							$scope.feeds_row1.splice( row_index, 1 );;
						}
						if(row_count == 2){
							$scope.feeds_row2.splice( row_index, 1 );
						}
						if(row_count == 3){
							$scope.feeds_row3.splice( row_index, 1 );
						}
						$("#feed_delete").modal("hide");
					}else{
						alert(data.return_array.process_info);				 					
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});	
			}
		}
		$scope.reasons= [];
		$scope.ajax_loader_reportpost = 1;
		$scope.reportSelectedContentId = '';
		$scope.reportSelectedContentType = '';
		$scope.selectedReasonId='';
		$scope.selectedReasonType='';
		$scope.enableReportReason=0;;
		$scope.spam={};
		$scope.ReportPost = function(type,content_id){
			$scope.ajax_loader_reportpost = 1;
			$scope.reasons= [];
			$scope.reportSelectedContentId = content_id;
			$scope.reportSelectedContentType = type;
			url  = baseurl+'/spam/getreasons';
			$http.post(url,
			{	
				type:type,content_id:content_id
			},
			{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
			).success(function(data, status, headers, config) {
				if(data.return_array.process_status == 'success'){
					$scope.reasons=data.return_array.reasons;
					$scope.ajax_loader_reportpost = 0;
				}else{
					alert(data.return_array.process_info);	
					$("#report_post").modal("hide");
				}				     
			}).error(function(data, status, headers, config) {
				// alert("Error occured. Please try again");
			});	
		}
		$scope.selectReason = function(reason_id,reasontype){
			$scope.selectedReasonId=reason_id;
			$scope.selectedReasonType=reasontype;
			if(reasontype == 'Other'){
				$scope.enableReportReason=1;
			}else{
				$scope.enableReportReason=0;
			}
		}
		$scope.sendReport = function(frmspam){
			if($scope.selectedReasonType == 'Other'&&frmspam.otherReason=='' ){
				alert("Please enter the reason");
			}else{
				url  = baseurl+'/spam/sentreport';
				$http.post(url,
				{	
					type:$scope.reportSelectedContentType,content_id:$scope.reportSelectedContentId,reason_id:frmspam.reason_id,otherReason:frmspam.otherReason
				},
				{headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'}	}	
				).success(function(data, status, headers, config) {
					if(data.return_array.process_status == 'success'){
						alert("You have successfully reported about the post");
						$scope.spam={};
						$("#report_post").modal("hide");
					}else{
						alert(data.return_array.process_info);	
						$("#report_post").modal("hide");
					}				     
				}).error(function(data, status, headers, config) {
					// alert("Error occured. Please try again");
				});	
			}
		}
		$scope.loadmediacommentwindow = function(){
			$scope.showReply = 1;
		}
		$scope.loadToolTip =function(type,id){
			$("#tooltip_"+type+"_"+id).show();
		}
		$scope.unloadToolTip =function(type,id){
			$("#tooltip_"+type+"_"+id).hide();
		}
	});
	feedapp.directive('removeOnClick', function() {
		return {
			link: function(scope, elt, attrs) {
				scope.remove = function() {
					elt.remove();
				};
			}
		}
	});
	feedapp.directive('ngFocus', ['$parse', function($parse) {
	  return function(scope, element, attr) {
		var fn = $parse(attr['ngFocus']);
		element.bind('focus', function(event) {
		  scope.$apply(function() {
			fn(scope, {$event:event});
		  });
		});
	  }
	}]); 
	feedapp.directive('ngBlur', ['$parse', function($parse) {
	  return function(scope, element, attr) {
		var fn = $parse(attr['ngBlur']);
		element.bind('blur', function(event) {
		  scope.$apply(function() {
			fn(scope, {$event:event});
		  });
		});
	  }
	}]);
	angular.element(document).ready(function() {	
		angular.bootstrap(document.getElementById("feedapp"), ["feed_app"]);
	}); 
function getFile(){
    document.getElementById("btnUploadImage").click();
}
$(document).on("click","#status_header_media",function(){
	var dropbox = document.getElementById("uplodbtn");
	// init event handlers
	dropbox.addEventListener("dragenter", dragEnter, false);
	dropbox.addEventListener("dragexit", dragExit, false);
	dropbox.addEventListener("dragover", dragOver, false);
	dropbox.addEventListener("drop", drop, false);
 
});
function dragEnter(evt) {
	evt.stopPropagation();
	evt.preventDefault();
}
function dragExit(evt) {
	evt.stopPropagation();
	evt.preventDefault();
}
function dragOver(evt) {
	evt.stopPropagation();
	evt.preventDefault();
}
function drop(evt) {  
	evt.stopPropagation();
	evt.preventDefault();

	var files = evt.dataTransfer.files;
	var count = files.length;

	// Only call the handler if 1 or more files was dropped.
	if (count > 0)
		handleFiles(files);
}
function handleFiles(files) {
	var file = files[0];
	upladedFile = files[0];
	var reader = new FileReader();	 
	reader.onloadend = handleReaderLoadEnd; 
	reader.readAsDataURL(file);
}
function handleReaderProgress(evt) {
	if (evt.lengthComputable) {
		var loaded = (evt.loaded / evt.total);
		$("#progressbar").progressbar({ value: loaded * 100 });
	}
}
function handleReaderLoadEnd(evt) {
	var img = document.getElementById("imgUserImage");
	img.src = evt.target.result;
	$("#default_img").hide();
	$("#uploaded_img").show();	 
}
$(document).on("click","#removeUploaded",function(){
	$("#uploaded_img").hide();
	$("#default_img").show();
	upladedFile = [];
});
$(document).on('change','#btnUploadImage',function(e){
	fileInput = e.target.files;
	upladedFile = fileInput[0];
	image_name = upladedFile.name;
	var extension = image_name.split('.').pop().toUpperCase();
	if (extension!="PNG" && extension!="JPG" && extension!="GIF" && extension!="JPEG"){
		upladedFile = [];
		alert("Sorry! You can only upload image files");		
	}else{
		var fimg = document.getElementById("imgUserImage");
		var _URL = window.URL;
		var  img;
		if ((element = upladedFile)) {
			img = new Image();
			img.onload = function () {
				if(this.width<50 || this.height<50){	
					alert("Please upload files with width more than 200 and height more than 100");
					upladedFile = [];
					return false;
				}              
			};
			img.src = _URL.createObjectURL(element);
		}	 
		var reader = new FileReader();
		reader.readAsDataURL(fileInput[0]);  
		reader.onload = function(_file) {
			fimg.src    = _file.target.result; 
		};
		fimg.src = e.target.result;
		$("#default_img").hide();
		$("#uploaded_img").show();
	}
});
 function enableDatepicker(){
	 
	 $("#enableDatepiker").trigger("click");
 }
 function addLocationValue(location){
	 $('.edit_location').val(location);
 }
$(document).on("click","#event_tab",function(){	
	$( "#event_date" ).datepicker({ changeYear: true , dateFormat: "dd-mm-yy",minDate: new Date(<?php echo date("Y");?>, <?php echo date("m");?> - 1, <?php echo date("d");?>) });
	$('#event_time').timeEntry();
	//initialize();
	autocomplete_init();gmaps_init();
}); 
 $(document).on("click","#enableDatepiker",function(){	
	
	$( "#event_date_edit" ).datepicker({ changeYear: true , dateFormat: "dd-mm-yy",minDate: new Date(<?php echo date("Y");?>, <?php echo date("m");?> - 1, <?php echo date("d");?>) });
	$('#event_time_edit').timeEntry();
	mapselected = 'map2';
					gmaps_init();
					
					autocomplete_init();
					
});
</script>
<style>
[contentEditable=true]:empty:not(:focus):before{
        content:attr(data-text)
    }
#map-canvasedit, #map-canvas, #activity_map{
		height: 344px;
		width:340px;
		border: 1px solid #999;
		-moz-box-shadow:    0px 0px 5px #ccc;
		-webkit-box-shadow: 0px 0px 5px #ccc;
		box-shadow:         0px 0px 5px #ccc;
}
 .google-map{width:100%; height:100%;}
</style>
<script> 
 
var geocoder;
var map;
var marker;
var mapselected = 'map1';
// initialise the google maps objects, and add listeners
function gmaps_init(){
  // center of the universe
  var latlng = new google.maps.LatLng(current_lat,current_lng);

  var options = {
    zoom: 5,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP,

  };
  // create our map object   
  
	if(mapselected=='map2'){
	   map = new google.maps.Map(document.getElementById("map-canvasedit"), options);  
	}else{
	   map = new google.maps.Map(document.getElementById("map-canvas"), options);  
	} 
  
  // the geocoder object allows us to do latlng lookup based on address
  geocoder = new google.maps.Geocoder();	 
  // the marker shows us the position of the latest address
  marker = new google.maps.Marker({
    map: map,
	icon: baseurl+"/public/images/map_pin.png",
    draggable: true
  });
marker.setPosition(latlng);
  // event triggered when marker is dragged and dropped
  google.maps.event.addListener(marker, 'dragend', function() {
    geocode_lookup( 'latLng', marker.getPosition() );
  });
  // event triggered when map is clicked
  google.maps.event.addListener(map, 'click', function(event) {
    marker.setPosition(event.latLng)
    geocode_lookup( 'latLng', event.latLng  );
  });
  $('#gmaps-error').hide();
}
// move the marker to a new position, and center the map on it
function update_map( geometry ) {
  map.fitBounds( geometry.viewport )
  marker.setPosition( geometry.location )
}
// fill in the UI elements with new position data
function update_ui( address, latLng ) { 
if(mapselected=='map2'){
	$('#edit_location').autocomplete("close");
  $('#edit_location').val(address); 
}else{
  $('#location').autocomplete("close");
  $('#location').val(address);   
}
  geo_latitude = latLng.lat(); 
  geo_longitude = latLng.lng(); 
  //$('#gmaps-output-latitude').html(latLng.lat());
  //$('#gmaps-output-longitude').html(latLng.lng());
}

// Query the Google geocode object
//
// type: 'address' for search by address
//       'latLng'  for search by latLng (reverse lookup)
//
// value: search query
//
// update: should we update the map (center map and position marker)?
function geocode_lookup( type, value, update ) {
  // default value: update = false
  update = typeof update !== 'undefined' ? update : false;
  request = {};
  request[type] = value;
  geocoder.geocode(request, function(results, status) {
    $('#gmaps-error').html('');
    $('#gmaps-error').hide();
    if (status == google.maps.GeocoderStatus.OK) {
      // Google geocoding has succeeded!
      if (results[0]) {
        // Always update the UI elements with new location data
        update_ui( results[0].formatted_address,
                   results[0].geometry.location )

        // Only update the map (position marker and center map) if requested
        if( update ) { update_map( results[0].geometry ) }
      } else {
        // Geocoder status ok but no results!?
        $('#gmaps-error').html("Sorry, something went wrong. Try again!");
        $('#gmaps-error').show();
      }
    } else {
      // Google Geocoding has failed. Two common reasons:
      //   * Address not recognised (e.g. search for 'zxxzcxczxcx')
      //   * Location doesn't map to address (e.g. click in middle of Atlantic)

      if( type == 'address' ) {
        // User has typed in an address which we can't geocode to a location
        $('#gmaps-error').html("Sorry! We couldn't find " + value + ". Try a different search term, or click the map." );
        $('#gmaps-error').show();
      } else {
        // User has clicked or dragged marker to somewhere that Google can't do a reverse lookup for
        // In this case we display a warning, clear the address box, but fill in LatLng
        $('#gmaps-error').html("Woah... that's pretty remote! You're going to have to manually enter a place name." );
        $('#gmaps-error').show();
        update_ui('', value)
      }
    };
  });
};
// initialise the jqueryUI autocomplete element
function autocomplete_init() {	 
  $("#location").autocomplete({
    // source is the list of input options shown in the autocomplete dropdown.
    // see documentation: http://jqueryui.com/demos/autocomplete/
    source: function(request,response) {
      // the geocode method takes an address or LatLng to search for
      // and a callback function which should process the results into
      // a format accepted by jqueryUI autocomplete
      geocoder.geocode( {'address': request.term }, function(results, status) {
        response($.map(results, function(item) {
          return {
            label: item.formatted_address, // appears in dropdown box
            value: item.formatted_address, // inserted into input element when selected
            geocode: item                  // all geocode data: used in select callback event
          }
        }));
      })
    },
    // event triggered when drop-down option selected
    select: function(event,ui){
      update_ui(  ui.item.value, ui.item.geocode.geometry.location )
      update_map( ui.item.geocode.geometry )
    }
  }); 
  $("#edit_location").autocomplete({
    // source is the list of input options shown in the autocomplete dropdown.
    // see documentation: http://jqueryui.com/demos/autocomplete/
    source: function(request,response) {
      // the geocode method takes an address or LatLng to search for
      // and a callback function which should process the results into
      // a format accepted by jqueryUI autocomplete
      geocoder.geocode( {'address': request.term }, function(results, status) {
        response($.map(results, function(item) {
          return {
            label: item.formatted_address, // appears in dropdown box
            value: item.formatted_address, // inserted into input element when selected
            geocode: item                  // all geocode data: used in select callback event
          }
        }));
      })
    },
    // event triggered when drop-down option selected
    select: function(event,ui){
      update_ui(  ui.item.value, ui.item.geocode.geometry.location )
      update_map( ui.item.geocode.geometry )
    }
  }); 
  // triggered when user presses a key in the address box
  $("#location").bind('keydown', function(event) {
    if(event.keyCode == 13) {
      geocode_lookup( 'address', $('#location').val(), true );
      // ensures dropdown disappears when enter is pressed
      $('#location').autocomplete("disable")
    } else {
      // re-enable if previously disabled above
      $('#location').autocomplete("enable")
    }
  }); 
$("#edit_location").bind('keydown', function(event) {
    if(event.keyCode == 13) {
      geocode_lookup( 'address', $('#edit_location').val(), true );
      // ensures dropdown disappears when enter is pressed
      $('#edit_location').autocomplete("disable")
    } else {
      // re-enable if previously disabled above
      $('#edit_location').autocomplete("enable")
    }
  });   
};  	
</script> 						
<script>  
	mapselected = 'map1';
	gmaps_init();				
	autocomplete_init();					 
</script>
<script>
     
	$(document).on('click','#bannerEditor',function(e){
		if(group.group_photo_photo!=''&&group.group_photo_photo!=null){
			$('.image-editor-group').cropit({
			 exportZoom:1.25,
			  imageState: {
				src: '<?php echo $this->basePath(); ?>/public/datagd/group/'+group.group_id+'/'+group.group_photo_photo
			  }
			});
			}else{
				$('.image-editor-group').cropit({
					exportZoom:1.25,
					imageState: {
						src: '<?php echo $this->basePath(); ?>/public/images/group-img_def.jpg'
					}
				});
			}
	});
	$(document).on('click','#bannerUpload',function(e){
		if(group.group_photo_photo!=''&&group.group_photo_photo!=null){
		$('.image-editor-group').cropit({
		 exportZoom:1.25,
		  imageState: {
			src: '<?php echo $this->basePath(); ?>/public/datagd/group/'+group.group_id+'/'+group.group_photo_photo
		  }
		});
		}else{
			$('.image-editor-group').cropit({
				exportZoom:1.25,
				imageState: {
					src: '<?php echo $this->basePath(); ?>/public/images/group-img_def.jpg'
				}
			});
		}
		$("#file_bannerimage_upload").click();
	});
	function loadToolTip(type,id){
		$("#tooltip_"+type+"_"+id).show();
	}
	function unloadToolTip(type,id){
		$("#tooltip_"+type+"_"+id).hide();
	}
	var invite_listenabled = 0;
	 $(document).on('click','#invite_group',function(e){
		 if(invite_listenabled==0){
			 $("#listed_members_container").hide();
		 }
	 });
	 $(document).on('mouseenter','#invite_list_friend',function(e){
		 invite_listenabled = 1;
	 });
	 $(document).on('mouseleave','#invite_list_friend',function(e){
		 invite_listenabled = 0;
	 });
	  $(document).on('mouseenter','#listed_members_container',function(e){
		 invite_listenabled = 1;
	 });
	 $(document).on('mouseleave','#listed_members_container',function(e){
		 invite_listenabled = 0;
	 });
    </script>