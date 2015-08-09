<?php

/**
 * Videos
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Videos extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'video';
	protected $authIdentity;
	protected $sql;
	protected $suserinfo=array('fname as sfname','lname as slname','propic as spropic','url as suser_url');
	protected $ruserinfo=array('fname as rfname','lname as rlname','propic as rpropic','url as ruser_url');
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$this->authIdentity=$auth->getIdentity();
		}
		$this->sql=$db->select()->from($this->_name)
		->joinLeft('user_info as suser_info','video.suserid=suser_info.userid',$this->suserinfo)
		->joinLeft('friends_vote as sfriends_vote', 'video.suserid=sfriends_vote.userid','friendlist as sfriendlist')
		->joinLeft('image as simage','suser_info.propic=simage.imageid','url as spropic_url')
		->joinLeft('user_info as ruser_info','video.ruserid=ruser_info.userid',$this->ruserinfo)
		->joinLeft('friends_vote as rfriends_vote', 'video.ruserid=rfriends_vote.userid','friendlist as rfriendlist')
		->joinLeft('image as rimage','ruser_info.propic=rimage.imageid','url as rpropic_url');
		
	}
	
	public function addVideos($title,$embeddcode,$ruserid){
		if(isset($this->authIdentity)){
			$insert_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'title'=>$title,'embeddcode'=>$embeddcode,'date'=>new Zend_Db_Expr('now()'),'vote'=>'a:0:{}','notifyusers'=>'a:0:{}');
			$isvalid=false;$message='';$canupdateavtivity=false;
			if($this->authIdentity->userid==$ruserid){
				$isvalid=true;
				$canupdateavtivity=true;
				if($this->authIdentity->type=='user'){
					$privacy=$this->authIdentity->privacy;
					$insert_data=array_merge($insert_data,array('pt'=>$privacy['videovisi'],'specificlist'=>$privacy['postspeci'],'hiddenlist' => $privacy['posthidden'],'accepted'=>'yes'));
						
				}
				else{
					$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','accepted'=>'yes'));
				}
			}
			else{
				$privacy_data=array('video','videoignore','videovisi','videospeci','videohidden','advancedprivacyvideo','autoacceptusers','blockactivityusers');
				$sql=$this->_db->select()->from('freniz')->joinLeft('privacy','privacy.userid=freniz.userid',$privacy_data)
				->joinLeft('pages','freniz.userid=pages.pageid',array('canpost as page_canpost','admins as page_admins','vote as page_vote','bannedusers as page_bannedusers'))
				->joinLeft('groups', 'freniz.userid=groups.groupid',array('canpost as group_canpost','admins as group_admins','members as group_members','bannedusers as group_bannedusers'))
				->where(' freniz.userid=?',$ruserid);
		
				$result=$this->_db->fetchRow($sql);
				if($result['type']=='user' && $this->authIdentity->type=='user'){
					$videoignore=unserialize($result['videoignore']);
					$autoacceptusers=unserialize($result['autoacceptusers']);
					$blockusersactivity=unserialize($result['blockactivityusers']);
					if(($result['video']=='friends' && !in_array($ruserid, $this->authIdentity->blocklistmerged)&&in_array($ruserid, $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $videoignore))){
						if($result['advancedprivacyvideo']=='on' && !in_array($this->authIdentity->userid, $blockusersactivity['video'])){
							if(in_array($this->authIdentity->userid, $autoacceptusers['video'])){
								$isvalid=true;
								$insert_data=array_merge($insert_data,array('pt'=>$result['videovisi'],'specificlist'=>$result['videospeci'],'hiddenlist' => $result['videohidden'],'accepted'=>'yes'));
								$canupdateavtivity=true;
							}
							else{
								$isvalid=true;
								$insert_data=array_merge($insert_data,array('pt'=>$result['videovisi'],'specificlist'=>$result['videospeci'],'hiddenlist' => $result['videohidden']));
							}
						}
						else {
							$isvalid=true;
							$insert_data=array_merge($insert_data,array('pt'=>$result['videovisi'],'specificlist'=>$result['videospeci'],'hiddenlist' => $result['videohidden'],'accepted'=>'yes'));
							$canupdateavtivity=true;
						}
					}
				}
				else if($result['type']=='page'){
					if((($result['page_canpost']=='public' || ($result['page_canpost']=='votedusers' && in_array($this->authIdentity->userid, unserialize($result['page_vote']))))&&!in_array($this->authIdentity->userid, unserialize($result['page_bannedusers'])) ) ){
						$isvalid=true;
						$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist' => 'a:0:{}','accepted'=>'yes'));
						$canupdateavtivity=true;
					}
				}
				else if($result['type']=='group' && $this->authIdentity->type=='user'){
					if((( ($result['group_canpost']=='members' && in_array($this->authIdentity->userid, unserialize($result['group_members']))) || ($result['group_canpost']=='admins' && in_array($this->authIdentity->userid, unserialize($result['group_admins']))))&&!in_array($this->authIdentity->userid, unserialize($result['group_bannedusers']) ) )){
						$isvalid=true;
						$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist' => 'a:0:{}','accepted'=>'yes'));
						$canupdateavtivity=true;
					}
				}
		
		
			}
			if($isvalid){
				$uptdid=$this->insert($insert_data);
				if($canupdateavtivity){
					$activitydata=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'post a video on','contenttype'=>'video','contenturl'=>'video.php?videoid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'video_'.$uptdid);
					$this->_db->insert('activity', $activitydata);
				}
			}
		}
	}
	public function doComment($videoid,$text){
		if(isset($this->authIdentity)){
			$a=array();
			$select=$this->_db->select()->from($this->_name,array('suserid','ruserid','notifyusers'))->joinLeft('freniz','freniz.userid=video.ruserid',array('type as rtype','username as rusername'))
			->joinLeft('pages','pages.pageid=video.ruserid',array('admins','canpost','vote','bannedusers'))
			->joinLeft('privacy','privacy.userid=video.ruserid',array('video','videoignore'))->where('videoid=?',$videoid);
			$result=$this->_db->fetchRow($select);
				
			if(isset($result)){
				$commentdata=array('videoid'=>$videoid,'userid'=>$this->authIdentity->userid,'comment'=>mysql_real_escape_string(trim($text)),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
						if($this->authIdentity->type=='user' && $result['rtype']=='user'){
					$ignorelist=unserialize($result['videoignore']);
					if(($result['video']=='friends' && !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && in_array($result['ruserid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid==$result['ruserid'] || $this->authIdentity->userid==$result['susersid']){
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$videoid,'title'=>'commented on','contenttype'=>'video','contenturl'=>'video.php?videoid='.$videoid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'video_'.$videoid);
						$this->_db->insert('video_comments', $commentdata);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
						 
						 
						$notifyusers=unserialize($result['notifyusers']);
						
						$notifyusers=array_diff($notifyusers, array($this->authIdentity->userid));
						$notifyusers1=$notifyusers;
						
						array_push($notifyusers1, $result['ruserid']);
						array_push($notifyusers1, $result['suserid']);
						$notifyusers1=array_diff($notifyusers1, array($this->authIdentity->userid));
						$notifyusers1=array_unique($notifyusers1);
						if(!empty($notifyusers1)){
							$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
							$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
							$update_notification=array();
							foreach($result_notifyusers as $user => $notifications)
							{
								$notifications=unserialize($notifications['notifications']);
								if(sizeof($notifyusers)>1)
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($notifyusers)-1)." other commented on";
								}
								else
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  commented on";
								}
								if($user==$result['suserid'] && $user==$result['ruserid'])
								{
									$notificationtext.=" your video";
								}
								else if($user==$result['suserid']){
									$notificationtext.=" your video of <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s chart";
								}
								else if($user==$result['ruserid'])
								{
									$notificationtext.=" your video";
								}
								else
								{
									$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s video";
								}
								$notifications["video.php?videoid=".$videoid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
								$update_notification[$user]=$notifications;
								//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
							}
							$notification_case=' case userid ';
							foreach($update_notification as $user=>$notification){
								$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
							}
							$notification_case.=' end';
							$where_clause=array(' userid in (?)'=>array_keys($update_notification));
							$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),$where_clause);
							array_push($notifyusers,$this->authIdentity->userid);
						}
						$update_staturedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_staturedata, "videoid='$videoid'");
	
					}
				}
				else if($result['type']=='page'){
					$votes=unserialize($result['votes']);
					$bannedusers=unserialize($result['bannedusers']);
					$admins=unserialize($result['admins']);
					if((($result['canpost']=='public' ||($result['canpost']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['ruserid']   )){
						$this->_db->insert('video_comments', $commentdata);
							
						$notifyusers=unserialize($result['notifyusers']);
						$notifyusers=array_diff($notifyusers, array($_SESSION['userid']));
						$notifyusers1=$notifyusers;
						$notifyusers1=array_merge($notifyusers, $result['admins'],array($result['ruserid'],$result['suserid']));
						$notifyusers1=array_diff($notifyusers1, array($_SESSION['userid']));
						$notifyusers1=array_unique($notifyusers1);
						if(!empty($notifyusers1)){
							$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
							$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
							$update_notification=array();
							foreach($result_notifyusers as $user => $notifications)
							{
								$notifications=unserialize($notifications['notifications']);
								if(sizeof($notifyusers)>1)
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($notifyusers)-1)." other commented on";
								}
								else
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  commented on";
								}
								if($user==$result['suserid'] && $user==$result['ruserid'])
								{
									$notificationtext.=" your video";
								}
								else if($user==$result['suserid']){
									$notificationtext.=" your video of <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s chart";
								}
								else if($user==$result['ruserid'])
								{
									$notificationtext.=" your video";
								}
								else
								{
									$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s video";
								}
								$notifications["video.php?videoid=".$videoid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
								$update_notification[$user]=$notifications;
								//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
							}
							$notification_case=' case userid ';
							foreach($update_notification as $user=>$notification){
								$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
							}
							$notification_case.=' end';
	
							$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));
	
							array_push($notifyusers,$this->authIdentity->userid);
						}
						$update_data=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_data, "videoid='$videoid'");
						//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
							
					}
	
				}
					
			}
	
		}
	}
	
	public function deleteComment($commentid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('video_comments')->joinLeft('video', 'video_comments.videoid=video.videoid',array('suserid','ruserid','commentcount'))->where('commentid=?',$commentid);
			$result=$this->_db->fetchRow($sql);
			$userid=$this->authIdentity->userid;
			if($userid==$result['userid'] || $userid==$result['suserid'] || $userid==$result['ruserid']){
				$this->_db->delete('video_comments',array('commentid=?'=>$commentid));
				$this->_db->delete('activity',array('contentid=?'=>$result['videoid'],'contenttype=?'=>'video','title=?'=>'commented on','userid=?'=>$userid));
				$updatedata=array('commentcount'=>new Zend_Db_Expr('commentcount-1'));
				$this->update($updatedata,array('videoid=?'=>$result['videoid']));
			}
		}
	}
	public function deleteVideo($videoid){
		if(isset($this->authIdentity)){
			$result=$this->find($videoid);
			if($result){
				$result=$result[0];
				if($this->authIdentity->userid==$result['suserid'] || $this->authIdentity->userid==$result['ruserid']){
					$this->delete(array('videoid=?'=>$videoid));
					$activityModel=new Application_Model_Activity($this->_db);
					$activityModel->delete(array('contenttype=?'=>'post','contentid=?'=>$videoid));
				}
			}
		}
	}
	
	
	public function deleteVideoComment($commentid){
	
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('video_comment')->joinLeft($this->_name, 'video_comment.videoid=video.videoid','userid as video_userid')->where('commentid=?',$commentid);
			$result=$this->_db->fetchRow($sql);
			$userid=$this->authIdentity->userid;
			if($result['userid']==$userid || $result['stature_userid']==$userid){
				$this->_db->delete('video_comment',"commentid='$commentid'");
				$userid=$this->authIdentity->userid;
				$videoid=$result['$videoid'];
				$activityModel=new Application_Model_Activity($this->_db);
				$activityModel->delete("userid='$userid' and contentid='$videoid' and contenttype='video' and title='commented on'");
				$update_data=array('commentcount=?'=>new Zend_Db_Expr('commentcount-1'));
				$this->update($update_data, "videoid='$videoid'");
			}
		}
	
	}
public function vote($videoid){
		if(isset($this->authIdentity)){
			$result=$this->find($videoid);
			$result=$result[0];
			$vote=unserialize($result['vote']);
			 
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "videoid='$videoid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$videoid,'title'=>'voted on','contenttype'=>'video','contenturl'=>'video.php?videoid='.$videoid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'video_'.$videoid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			$notifyusers=unserialize($result['notifyusers']);
			$notifyusers=array_merge($notifyusers, array($result['suserid'],$result['ruserid']),$vote);
			$notifyusers1=$notifyusers;
			//array_push($notifyusers1, $result['userid']);
			$notifyusers1=array_diff($notifyusers1, array($this->authIdentity->userid));
			$notifyusers1=array_unique($notifyusers1);
			$votes1=array_diff($vote, array($this->authIdentity->userid));
			if(!empty($notifyusers1)){
				$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
				$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
				$update_notification=array();
				foreach($result_notifyusers as $user => $notifications)
				{
					$notifications=unserialize($notifications['notifications']);
					if(sizeof($vote)>1)
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($votes1)-1)." other voted on";
					}
					else
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  voted on";
					}
					if($user==$result['ruserid'] )
					{
						$notificationtext.=" your video";
					}
					else
					{
						$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s video";
					}
					$notifications["video.php?videoid=".$videoid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
					$update_notification[$user]=$notifications;
					//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
				}
				$notification_case=' case userid ';
				foreach($update_notification as $user=>$notification){
					$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
				}
				$notification_case.=' end';
				 
				$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));
			}
		}
	}
      public function unVote($videoid)
			{
				if(isset($this->authIdentity)){
				$result=$this->find($videoid);
				if($result){
				$result=$result[0];
				
				$vote=unserialize($result['vote']);
				if(in_array($this->authIdentity->userid, $vote)){
					$vote=array_diff($vote, array($this->authIdentity->userid));
					$updatedata=array('vote'=>serialize($vote));
					$this->update($updatedata, array('videoid=?'=>$videoid));
				}
			}
		}
	}
	public function getVideos($videoids){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;
			$sql=$sql->where("videoid in(?) and video.accepted='yes'",$videoids);
				
			$results=$this->processResults($this->_db->fetchAssoc($sql));
				
			return $results;
		}
	}
	public function getUserVideos($userid){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;
			$sql=$sql->where('video.ruserid=? and video.accepted=? ');
			
			$results=$this->processResults($this->_db->fetchAssoc($sql,array($userid,'yes')));
	
			return $results;
		}
	}
	public function processResults($results){
		$userid=$this->authIdentity->userid;
		$myfriends=$this->authIdentity->friends;
		$blocklist=$this->authIdentity->blocklistmerged;
		$videoids=array_keys($results);
		$comment_sql=$this->_db->select()->from('video_comments')->joinLeft('user_info', 'user_info.userid=video_comments.userid',$this->userinfo)->joinLeft('image', 'user_info.propic=image.imageid','image.url as commentpic_url')->where('videoid in (?)',$videoids);
		$comment_result=$this->_db->fetchAssoc($comment_sql);
	
		$myfriends=$this->authIdentity->friends;
		foreach ($results as $videoid => $values){
			$friends=unserialize($values['rfriendlist']);
				
			$privacy=$values['pt'];
			$specific=unserialize($values['specificlist']);
			$hidden=unserialize($values['hiddenlist']);
				
			if((($privacy=='public'||($privacy=='friends' && in_array($userid,$friends)) || ($privacy=='fof' && (count(array_intersect($friends, $myfriends))>=1) || in_array($userid, $friends)) || ($privacy=='specific' && in_array($userid, $specific))) && !in_array($values['userid'], array_merge($blocklist,$hidden))) || $userid==$values['userid']){
				$results[$videoid]['comments']=$this->filter_by_value($comment_result, 'videoid', $videoid);
			}
			else{
				unset($results[videoid]);
			}
		}
		return $results;
	}
	
	private function filter_by_value ($array, $index, $value){
		if(is_array($array) && count($array)>0)
		{
			foreach(array_keys($array) as $key){
				$temp[$key] = $array[$key][$index];
	
				if ($temp[$key] == $value){
					$newarray[$key] = $array[$key];
				}
			}
		}
		return $newarray;
	}
	
}
