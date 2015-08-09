<?php

/**
 * Admiration
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Admiration extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'testimonial';
	protected $authIdentity;
	
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$this->authIdentity=$auth->getIdentity();
						
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
		$this->registry=Zend_Registry::getInstance();
				
	}
	
	
public function addAdmiration($ruserid,$text)

	{

		if(isset($this->authIdentity)){

			$insert_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>$text,'date'=>new Zend_Db_Expr('now()'),'vote'=>'a:0:{}');

			$isvalid=false;$message='';$canupdateavtivity=false;
			$insert_data['dontnotify']='a:0:{}';

			if($this->authIdentity->userid!=$ruserid){

				$privacy_data=array('testy','testyignore','testyvisi','testyspeci','testyhidden','advancedprivacyadmire','autoacceptusers','blockactivityusers','testyspecificpeople');

				$sql=$this->_db->select()->from('freniz')->joinLeft('privacy','privacy.userid=freniz.userid',$privacy_data)

				->joinLeft('pages','freniz.userid=pages.pageid',array('canpost as page_canpost','admins as page_admins','vote as page_vote','bannedusers as page_bannedusers'))

				->joinLeft('groups', 'freniz.userid=groups.groupid',array('canpost as group_canpost','admins as group_admins','members as group_members','bannedusers as group_bannedusers'))

				->where(' freniz.userid=?',$ruserid);

				$result=$this->_db->fetchRow($sql);

				if($result['type']=='user' && $this->authIdentity->type=='user'){

					$testyignore=unserialize($result['testyignore']);

					$autoacceptusers=unserialize($result['autoacceptusers']);

					$blockusersactivity=unserialize($result['blockactivityusers']);

					if(($result['testy']=='public' || ($result['testy']=='friends' && in_array($ruserid, $this->authIdentity->friends)) ||($result['testy']=='fof' && ((count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends))>=1) || in_array($ruserid, $this->authIdentity->friends))) || ($result['testy']=='specific' && in_array($this->authIdentity->userid, unserialize($result['testyspecificpeople'])) )) && !in_array($this->authIdentity->userid, $testyignore) && !in_array($ruserid, $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, $blockusersactivity['admire'])  ){
						if($result['advancedprivacyadmire']=='on' ){

							if(in_array($this->authIdentity->userid, $autoacceptusers['post'])){

								$isvalid=true;

								$insert_data=array_merge($insert_data,array('pt'=>$result['testyvisi'],'specificlist'=>$result['testyspeci'],'hiddenlist' => $result['testyhidden'],'accepted'=>'yes'));

								$canupdateavtivity=true;

							}

							else{

								$isvalid=true;

								$insert_data=array_merge($insert_data,array('pt'=>$result['testyvisi'],'specificlist'=>$result['testyspeci'],'hiddenlist' => $result['testyhidden']));

							}

						}

						else {

							$isvalid=true;

							$insert_data=array_merge($insert_data,array('pt'=>$result['testyvisi'],'specificlist'=>$result['testyspeci'],'hiddenlist' => $result['testyhidden'],'accepted'=>'yes'));

							$canupdateavtivity=true;

						}

					}

				}

				

	

			}

			if($isvalid){

				$uptdid=$this->insert($insert_data);

				if($canupdateavtivity){
						$notify_data=array('userid'=>$ruserid,'contenturl'=>'admire/'.$uptdid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> added admiration on your chart','userpic'=>$this->authIdentity->propic);
						$this->_db->insert('notifications', $notify_data);
					$activitydata=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'wrote on','contenttype'=>'admire','contenturl'=>'admire.php?testyid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'admire_'.$uptdid);

					$this->_db->insert('activity', $activitydata);

				}
				return array("admireid"=>$uptdid,"time"=>date('c'),'content'=>$text,"susername"=>$this->authIdentity->username,"status"=>success);

			}

		}

	}
	/*
	
	public function addAdmiration($text,$ruserid)
	{
		if(isset($this->authIdentity)){
			
			$privacy_data=$this->_db->select()->from('privacy',array('testy','testyignore','testyvisi','testyspeci','testyhidden','advancedprivacyadmire','autoacceptusers','blockactivityusers'))->where('userid=?',$ruserid);
			$privacy=$this->_db->fetchRow($privacy_data);
				$ignore=unserialize($privacy['testyignore']);
			$autoacceptusers=unserialize($privacy['autoacceptusers']);
			$blockusersactivity=unserialize($privacy['blockactivityusers']);
			if(($privacy['testy']=='friends' && !in_array($ruserid, $this->authIdentity->blocklistmerged)&&in_array($ruserid, $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignore))&&($ruserid!=$this->authIdentity->userid)){
				$a=array();
				if($privacy['advancedprivacyadmire']=='on' && !in_array($this->authIdentity->userid, $blockusersactivity['admire'])){
					if(in_array($this->authIdentity->userid, $autoacceptusers['admire'])){
						
						
						$admiration_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['testyvisi'],'specificlist'=>$privacy['testyspeci'],'hiddenlist'=>$privacy['testyhidden']);
						$uptdid=$this->insert($admiration_data);
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'write an admire on','contenttype'=>'admire','contenturl'=>'admire.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'admire_'.$uptdid);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
					}
					else{
						
						$admiration_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['testyvisi'],'specificlist'=>$privacy['testyspeci'],'hiddenlist'=>$privacy['testyhidden']);
						$uptdid=$this->insert($admiration_data);
						
						$result1=mysql_query("select reviews from user_info where userid='".$ruserid."'");
						$reviews;
						$postreviews=array();
						while($row1= mysql_fetch_assoc($result1))
						{
							$reviews=unserialize($row1['reviews']);
							if(isset($reviews['admire']))
							{
								array_push($reviews['admire'], $updtdid);
							}
							else
							{
								$reviews['admire']=array($updtdid);
							}
							mysql_query("update user_info set reviews='".serialize($reviews)."' where userid='".$ruserid."'");
							 
						}
						if(isset($_SESSION['reqfrmme']['admire']))
							array_push($_SESSION['reqfrmme']['admire'], $updtdid);
						else
							$_SESSION['reqfrmme']['admire']=array($updtdid);
						mysql_query("update user_info set reqfrmme='".serialize($_SESSION['reqfrmme'])."' where userid='".$_SESSION['userid']."'");
						return array("status"=>"your admire will be posted after ".$ruserid." has reviewed");
						}
						
						
					}
					
					else if(!in_array($this->authidentity->userid, $blockusersactivity['admire'])) {

						$admiration_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['blogvisi'],'specificlist'=>$privacy['blogspeci'],'hiddenlist'=>$privacy['bloghidden']);
						$uptdid=$this->insert($admiration_data);
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'write an admire on','contenttype'=>'admire','contenturl'=>'admire.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'admire_'.$uptdid);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
							
					}
					else
						return array("status"=>"you do not have permission to post");
					
					}
					else{
						return array("status"=>"you do not have permission to post");
					}
					
				
			}
			else
				return array("status","please give the valid information");
		 
	
		}
	*/
	
		public function deleteAdmire($admireid){
			if(isset($this->authIdentity)){
			    $userid=$this->authIdentity->userid;
				$affected_rows=$this->delete("testyid='$admireid' and (suserid='$userid' or ruserid='$userid')");
				if($affected_rows==1){
					$activityModel=new Application_Model_Activity($this->_db);
					$activityModel->delete("userid='$userid' and contentid='$admireid' and contenttype='admire' and title='write an admire on'");
					return array("status"=>"Admire removed");
				}
					else 
					return array("status"=>"you dont have permission to delete this Admire");
				}
			else
				return array("status","please give the valid information");
		
		}	
		
		
public function voteadmire($testyid){

			if(isset($this->authIdentity)){

				$sql=$this->_db->select()->from('testimonial',array('suserid','ruserid','vote','dontnotify'))->joinLeft('freniz', 'testimonial.ruserid=freniz.userid',array('rusername'=>'username'))->where('testyid=?',$testyid);
				$result=$this->_db->fetchRow($sql);
				if(!empty($result)){

				//$result=$result[0];

				$vote=unserialize($result['vote']);

				if(!in_array($this->authIdentity->userid,$vote)){

				array_push($vote, $this->authIdentity->userid);

				$vote=array_unique($vote);

				$update_data=array('vote'=>serialize($vote));

				$this->update($update_data, "testyid='$testyid'");

				$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$testyid,'title'=>'voted on','contenttype'=>'admire','contenturl'=>'admire.php?admireid='.$testyid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'admire_'.$testyid);

				$activityModel=new Application_Model_Activity($this->_db);

				$activityModel->insert($activity);
				

				$dontnotify=unserialize($result['dontnotify']);

				
				$votes1=array_diff($vote, array($this->authIdentity->userid));
				$notifyusers=array_diff(array_merge($votes1,array($result['suserid'],$result['ruserid'])),$dontnotify,array($this->authIdentity->userid));
				$query='insert into notifications(userid,contenturl,notification,userpic) values ';
				$userpic=$this->authIdentity->propic;
				if(!empty($notifyusers)){
				
				
				foreach($notifyusers as $user){
					if(sizeof($votes1)>1)
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($votes1)-1)." other voted on";
					}
					else
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  voted on";
					}
					if($user==$result['ruserid'] )
					{
						$notificationtext.=" your admire";
					}
					else
					{
						$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s admire";
					}
					$query.=' ('.$this->_db->quote(array($user,'admire/'.$testyid,$notificationtext,$userpic)).'),';
				}
				$query=substr($query, 0,-1);
				$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0,time=now()';
				$this->_db->query($query);
				}
				/*

				if(!empty($notifyusers1)){

					$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);

					$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);

					$update_notification=array();

					foreach($result_notifyusers as $user => $notifications)

					{

						$notifications=unserialize($notifications['notifications']);

						$notifications["admire.php?admireid=".$testyid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());

						$update_notification[$user]=$notifications;

						//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");

					}

					$notification_case=' case userid ';

					foreach($update_notification as $user=>$notification){

						$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";

					}

					$notification_case.=' end';

						

					$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));

				}*/

			}
				}
			}

		}
		public function unVoteadmire($testyid)
		{
			if(isset($this->authIdentity)){
				$result=$this->find($testyid);
				if($result){
					$result=$result[0];
		
					$vote=unserialize($result['vote']);
					if(in_array($this->authIdentity->userid, $vote)){
						$vote=array_diff($vote, array($this->authIdentity->userid));
						$updatedata=array('vote'=>serialize($vote));
						$this->update($updatedata, array('testyid=?'=>$testyid));
					}
				}
			}
		}
		
public function getAdmiration($ruserid,$from){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name,array('testyid','suserid','ruserid','message','vote','date','pt','specificlist','hiddenlist','accepted'))
			->joinLeft('friends_vote','friends_vote.userid=testimonial.ruserid','friendlist')
			->joinLeft('user_info','user_info.userid=testimonial.suserid', array('fname','lname','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->where("(ruserid='".$ruserid."') and (suserid='{$this->authIdentity->userid}' or testimonial.accepted='yes') ")
			->order('date desc')->limit($this->registry->limit,$from);
			 $results=$this->_db->fetchAssoc($sql);
			 if(count($results)==$this->registry->limit)
			 	$final_results['loadmore']=true;
			 else $final_results['loadmore']=false;
			 
			 foreach ($results as $id=> $result){
			 $privacy=$result['pt'];

			 $specific=  unserialize($result['specificlist']);

			 $hiddenlist=  unserialize($result['hiddenlist']);

			 $rusrfrnds=$result['friendlist'];
			 if((($privacy=='public'||($privacy=='friends' && in_array($result['ruserid'],$this->authIdentity->friends))||($privacy=='fof' && count(array_intersect($rusrfrnds, $this->authIdentity->friends)>=1) )||($privacy=='specific' && in_array($this->authIdentity->userid, $specific)))&& !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, $hiddenlist))|| $this->authIdentity->userid==$result['ruserid']){

            
			 }
			 else 
			 	unset($results[$id]);
			 }
			 $final_results['results']=$results;
			 return $final_results;
			
		}
		else

			return array("status","please give the valid information");
	}	
	public function tobeReviewed($from){
		if(isset($this->authIdentity->userid)){
			$sql=$this->_db->select()->from($this->_name,array('testyid','suserid','ruserid','message','vote','date','pt','specificlist','hiddenlist','accepted'))
			->joinLeft('friends_vote','friends_vote.userid=testimonial.ruserid','friendlist')
			->joinLeft('user_info','user_info.userid=testimonial.suserid', array('fname','lname','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->where("(ruserid='{$this->authIdentity->userid}' and testimonial.accepted='not') ")
			->order('date desc')->limit($this->registry->limit,$from);
			$results=$this->_db->fetchAssoc($sql);
			if(count($results)==$this->registry->limit)
				$final_results['loadmore']=true;
			else $final_results['loadmore']=false;
			$final_results['results']=$results;
			return $final_results;
		}
	}
	public function getAdmirationArray($admireids){
		$sql=$this->_db->select()->from($this->_name,array('testyid','suserid','ruserid','message','vote','date','pt','specificlist','hiddenlist','accepted'))
		->joinLeft('friends_vote','friends_vote.userid=testimonial.ruserid','friendlist')
		->joinLeft('user_info','user_info.userid=testimonial.suserid', array('fname','lname','propic'))
		->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
		->where('testimonial.testyid in (?) and  testimonial.accepted=\'yes\'',$admireids);
		$results=$this->_db->fetchAssoc($sql);
		foreach ($results as $id=> $result){
			$privacy=$result['pt'];
			$specific=  unserialize($result['specificlist']);
			$hiddenlist=  unserialize($result['hiddenlist']);
			$rusrfrnds=$result['friendlist'];
			if((($privacy=='public'||($privacy=='friends' && in_array($result['ruserid'],$this->authIdentity->friends))||($privacy=='fof' && count(array_intersect($rusrfrnds, $this->authIdentity->friends)>=1) )||($privacy=='specific' && in_array($this->authIdentity->userid, $specific)))&& !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, $hiddenlist))|| $this->authIdentity->userid==$result['ruserid']){
	
			}
			else
				unset($results[$id]);
		}
		return $results;
	
	
	}
	
	
	public function reviewAdmires($ids=null,$accept=true){
		$where=array('ruserid=?'=>$this->authIdentity->userid);
		if(!empty($ids))
			$where['testyid in (?)']=$ids;
		if($accept)
			$this->update(array('accepted'=>'yes'),$where);
		else
			$this->delete($where);
	
	}
		
}
		
