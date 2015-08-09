<?php

/**
 * Stature
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Stature extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'stature';
	protected $authIdentity=null;
	protected $sql;
	protected $userinfo=array('fname','lname','propic','url as user_url');
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$this->authIdentity=$auth->getIdentity();
		}
		$this->sql=$db->select()->from($this->_name)
		->joinLeft('user_info','stature.userid=user_info.userid',$this->userinfo)
		->joinLeft('friends_vote', 'stature.userid=friends_vote.userid','friendlist')
		->joinLeft('image','user_info.propic=image.imageid','url as propic_url');
			
	}
	public function addStature($text)
	{
		if(isset($this->authIdentity)){
			$privacy=$this->authIdentity->privacy;
			$stature_data=array('userid'=>$this->authIdentity->userid,'stature'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['postvisi'],'specificlist'=>$privacy['postspeci'],'hiddenlist'=>$privacy['posthidden'],'notifyusers'=>'a:0:{}');
			$uptdid=$this->insert($stature_data);
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$uptdid,'title'=>'update stature','contenttype'=>'stature','contenturl'=>'stature.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'stature_'.$uptdid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			//return $activity;
		}
	}
	public function getStatures($statureids){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;	
			$sql=$sql->where("statureid in(?)",$statureids);
			
			$results=$this->processResults($this->_db->fetchAssoc($sql));
			
			return $results;
		}
	}
	public function getUserStatures($userid){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;
			$sql=$sql->where('stature.userid=?',$userid);
				
			$results=$this->processResults($this->_db->fetchAssoc($sql));
				
			return $results;
		}
	}
	public function processResults($results){
		
		$userid=$this->authIdentity->userid;
		$myfriends=$this->authIdentity->friends;
		$blocklist=array_merge($this->authIdentity->blocklist,$this->authIdentity->blockedby);
		$statureids=array_keys($results);
		$comment_sql=$this->_db->select()->from('stature_comment')->joinLeft('user_info', 'user_info.userid=stature_comment.userid',$this->userinfo)->joinLeft('image', 'user_info.propic=image.imageid','image.url as commentpic_url')->where('statureid in (?)',$statureids);
		$comment_result=$this->_db->fetchAssoc($comment_sql);
		foreach ($results as $statureid => $values){
			$friends=unserialize($values['friendlist']);
			
			$privacy=$values['pt'];
			$specific=unserialize($values['specificlist']);
			$hidden=unserialize($values['hiddenlist']);
			
			if((($privacy=='public'||($privacy=='friends' && in_array($userid,$friends)) || ($privacy=='fof' && (count(array_intersect($friends, $myfriends))>=1) || in_array($userid, $friends)) || ($privacy=='specific' && in_array($userid, $specific))) && !in_array($values['userid'], array_merge($blocklist,$hidden))) || $userid==$values['userid']){
			$results[$statureid]['comments']=$this->filter_by_value($comment_result, 'statureid', $statureid);	
			}
			else{
				unset($results[$statureid]);
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
    
    public function doComment($statureid,$text){
    	if(isset($this->authIdentity)){
    		$a=array();
    		$select=$this->_db->select()->from($this->_name,array('userid','notifyusers'))->joinLeft('freniz','freniz.userid=stature.userid',array('type','username'))
    				->joinLeft('pages','pages.pageid=stature.userid',array('admins','canpost','vote','bannedusers'))
    				->joinLeft('privacy','privacy.userid=stature.userid',array('post','postignore'))->where('statureid=?',$statureid);
    		$result=$this->_db->fetchRow($select);
    		if(isset($result)){
    			$stature_commentdata=array('statureid'=>$statureid,'userid'=>$this->authIdentity->userid,'comment'=>mysql_real_escape_string(trim($text)),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
    			if($this->authIdentity->type!='leaf' && $result['type']=='user'){
	    			$ignorelist=unserialize($result['postignore']);
	    			if(($result['post']=='friends' && !in_array($result['userid'], $this->authIdentity->blocklistmerged) && in_array($result['userid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid=$result['userid']){
	    				$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['userid'],'contentid'=>$statureid,'title'=>'commented on','contenttype'=>'stature','contenturl'=>'stature.php?statureid='.$statureid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'stature_'.$statureid);
	    				$statureCommentsModel=new Application_Model_StatureComments($this->_db);
	    				$statureCommentsModel->insert($stature_commentdata);
	    				$activityModel=new Application_Model_Activity($this->_db);
	    				$activityModel->insert($activity);
	    				
	    				
	    				$notifyusers=unserialize($result['notifyusers']);
	    				$notifyusers=array_diff($notifyusers, array($this->authIdentity->userid));
	    				$notifyusers1=$notifyusers;
	    				array_push($notifyusers1, $result['userid']);
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
	    						if($user==$result['userid'] )
	    						{
	    							$notificationtext.=" your stature";
	    						}
	    						else
	    						{
	    							$notificationtext.=" <a href='".$result['userid']."'>".$result['username']."</a>'s post";
	    						}
	    						$notifications["stature.php?statureid=".$statureid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
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
	    				$this->update($update_staturedata, "statureid='$statureid'");
	    					
	    			}
    			}
    			else if($result['type']=='leaf'){
    				$votes=unserialize($result['votes']);
    				$bannedusers=unserialize($result['bannedusers']);
    				$admins=unserialize($result['admins']);
    				if((($result['canpost']=='public' ||($result['canpost']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['userid']   )){
    					$this->_db->insert('stature_comment', $stature_commentdata);
    					
    					$notifyusers=unserialize($result['notifyusers']);
    					$notifyusers=array_diff($notifyusers, array($_SESSION['userid']));
    					$notifyusers1=$notifyusers;
    					array_push($notifyusers1, $result['userid']);
    					$notifyusers=array_merge($notifyusers, $result['admins']);
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
    						if($user==$result['userid'] )
    						{
    							$notificationtext.=" your stature";
    						}
    						else
    						{
    							$notificationtext.=" <a href='".$result['userid']."'>".$result['username']."</a>'s post";
    						}
    						$notifications["stature.php?statureid=".$statureid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
    						$update_notification[$user]=$notifications;
    						//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
    					}
    					$notification_case=' case userid ';
    					foreach($update_notification as $user=>$notification){
    						$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
    					}
    					$notification_case.=' end';
    					 
    					$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));
    					
    					array_push($notifyusers,$_SESSION['userid']);
    					}
    					$update_staturedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
   						$this->update($update_staturedata, "statureid='$statureid'");
    					//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
    					
    				}
    				
    			}
    			
    		}
    		
    	}
    }
    
    public function deleteStature($statureid){
    	if(isset($this->authIdentity)){
    		$userid=$this->authIdentity->userid;
    		$this->delete("statureid='$statureid' and userid='$userid'");
    		$this->_db->delete('activity',"contenttype='stature' and contentid='$statureid'");
    		$update_data=array('commentcount=?'=>new Zend_Db_Expr('commentcount-1'));
    		$this->update($update_data, "statureid='$statureid'");
	    	}
    }
    public function deleteComment($commentid){
    	if(isset($this->authIdentity)){
    		$sql=$this->_db->select()->from('stature_comment')->joinLeft($this->_name, 'stature_comment.statureid=stature.statureid','userid as stature_userid')->where('commentid=?',$commentid);
    		$result=$this->_db->fetchRow($sql);
    		$userid=$this->authIdentity->userid;
    		if($result['userid']==$userid || $result['stature_userid']==$userid){
    			$this->_db->delete('stature_comment',"commentid='$commentid'");
    			$userid=$this->authIdentity->userid;
    			$statureid=$result['statureid'];
    			$activityModel=new Application_Model_Activity($this->_db);
    			$activityModel->delete("userid='$userid' and contentid='$statureid' and contenttype='stature' and title='commented on'");
    			$update_data=array('commentcount=?'=>new Zend_Db_Expr('commentcount-1'));
    			$this->update($update_data, "statureid='$statureid'");
    			
    		}
    	}
    }
    public function vote($statureid){
    	 if(isset($this->authIdentity)){
    	 	$result=$this->find($statureid);
    	 	$result=$result[0];
    	 	$vote=unserialize($result['vote']);
    	 	
    	 	array_push($vote, $this->authIdentity->userid);
    	 	$vote=array_unique($vote);
    	 	$update_data=array('vote'=>serialize($vote));
    	 	$this->update($update_data, "statureid='$statureid'");
    	 	$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$statureid,'title'=>'voted on','contenttype'=>'stature','contenturl'=>'stature.php?statureid='.$statureid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'stature_'.$statureid);
    	 	$activityModel=new Application_Model_Activity($this->_db);
    	 	$activityModel->insert($activity);
    	 	$notifyusers=unserialize($result['notifyusers']);
    	 	$notifyusers=array_diff($notifyusers, array($this->authIdentity->userid));
    	 	$notifyusers1=$notifyusers;
    	 	array_push($notifyusers1, $result['userid']);
    	 	$notifyusers1=array_diff($notifyusers1, array($this->authIdentity->userid));
    	 	$notifyusers1=array_unique($notifyusers1);
    	 	if(!empty($notifyusers1)){
    	 	$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
    	 	return $select_notifyusers;
    	 	$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
    	 	$update_notification=array();
    	 	foreach($result_notifyusers as $user => $notifications)
    	 	{
    	 		$notifications=unserialize($notifications['notifications']);
    	 		if(sizeof($notifyusers)>1)
    	 		{
    	 			$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($notifyusers)-1)." other voted on";
    	 		}
    	 		else
    	 		{
    	 			$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  voted on";
    	 		}
    	 		if($user==$result['userid'] )
    	 		{
    	 			$notificationtext.=" your stature";
    	 		}
    	 		else
    	 		{
    	 			$notificationtext.=" <a href='".$result['userid']."'>".$result['username']."</a>'s stature";
    	 		}
    	 		$notifications["stature.php?statureid=".$statureid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
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
    public function unVote($statureid){
    	if(isset($this->authIdentity)){
    		$result=$this->find($statureid);
    		$result=$result[0];
    		$vote=unserialize($result['vote']);
    		 
    		$vote=array_diff($vote, array($this->authIdentity->userid));
    		$vote=array_unique($vote);
    		$update_data=array('vote'=>serialize($vote));
    		$this->update($update_data, "statureid='$statureid'");
    		$activityModel=new Application_Model_Activity($this->_db);
    		$activityModel->delete(array('userid=?'=>$this->authIdentity->userid,'contenttype=?'=>'stature','title=?'=>'voted on','contentid=?'=>$statureid));
    	}
    }
    
}


