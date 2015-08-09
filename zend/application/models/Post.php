<?php

/**
 * Post
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Post extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'status';
	protected $authIdentity;
	protected $sql;
	protected $suserinfo=array('fname as sfname','lname as slname','propic as spropic','url as suser_url');
	protected $ruserinfo=array('fname as rfname','lname as rlname','propic as rpropic','url as ruser_url');
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
		$this->sql=$db->select()->from($this->_name)
		->joinLeft('user_info as suser_info','status.suserid=suser_info.userid',$this->suserinfo)
		->joinLeft('friends_vote as sfriends_vote', 'status.suserid=sfriends_vote.userid','friendlist as sfriendlist')
		->joinLeft('image as simage','suser_info.propic=simage.imageid','url as spropic_url')
		->joinLeft('user_info as ruser_info','status.ruserid=ruser_info.userid',$this->ruserinfo)
		->joinLeft('friends_vote as rfriends_vote', 'status.ruserid=rfriends_vote.userid','friendlist as rfriendlist')
		->joinLeft('image as rimage','ruser_info.propic=rimage.imageid','url as rpropic_url');
		
	}
	public function addPost($ruserid,$text)
	{
		if(isset($this->authIdentity)){
			$insert_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'status'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('now()'),'vote'=>'a:0:{}','notifyusers'=>'a:0:{}');
			$isvalid=false;$message='';$canupdateavtivity=false;
			if($this->authIdentity->userid==$ruserid){
				$isvalid=true;
				$canupdateavtivity=true;
				if($this->authIdentity->type=='user'){
					$privacy=$this->authIdentity->privacy;
					$insert_data=array_merge($insert_data,array('pt'=>$privacy['pt'],'specificlist'=>$privacy['postspeci'],'hiddenlist' => $privacy['posthidden'],'accepted'=>'yes'));
					
				}
				else{
					$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','accepted'=>'yes'));
				}
			}
			else{
				$privacy_data=array('post','postignore','postvisi','postspeci','posthidden','advancedprivacypost','autoacceptusers','blockactivityusers');
				$sql=$this->_db->select()->from('freniz')->joinLeft('privacy','privacy.userid=freniz.userid',$privacy_data)
						->joinLeft('pages','freniz.userid=pages.pageid',array('canpost as page_canpost','admins as page_admins','vote as page_vote','bannedusers as page_bannedusers'))
						->joinLeft('groups', 'freniz.userid=groups.groupid',array('canpost as group_canpost','admins as group_admins','members as group_members','bannedusers as group_bannedusers'))
						->where(' freniz.userid=?',$ruserid);
				
				$result=$this->_db->fetchRow($sql);
				if($result['type']=='user' && $this->authIdentity->type=='user'){
					$postignore=unserialize($result['postignore']);
					$autoacceptusers=unserialize($result['autoacceptusers']);
					$blockusersactivity=unserialize($result['blockactivityusers']);
					if(($result['post']=='friends' && !in_array($ruserid, $this->authIdentity->blocklistmerged)&&in_array($ruserid, $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $postignore))){
						if($result['advancedprivacypost']=='on' && !in_array($this->authIdentity->userid, $blockusersactivity['post'])){
							if(in_array($this->authIdentity->userid, $autoacceptusers['post'])){
								$isvalid=true;
								$insert_data=array_merge($insert_data,array('pt'=>$result['postvisi'],'specificlist'=>$result['postspeci'],'hiddenlist' => $result['posthidden'],'accepted'=>'yes'));
								$canupdateavtivity=true;
							}
							else{
								$isvalid=true;
								$insert_data=array_merge($insert_data,array('pt'=>$result['postvisi'],'specificlist'=>$result['postspeci'],'hiddenlist' => $result['posthidden']));
							}
						}
						else {
							$isvalid=true;
							$insert_data=array_merge($insert_data,array('pt'=>$result['postvisi'],'specificlist'=>$result['postspeci'],'hiddenlist' => $result['posthidden'],'accepted'=>'yes'));
							$canupdateavtivity=true;
						}
					}
					else
						return array("status"=>"you do not have permission to comment");
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
					$activitydata=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'posted on','contenttype'=>'post','contenturl'=>'post.php?postid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'post_'.$uptdid);
					$this->_db->insert('activity', $activitydata);
					return array("status"=>"your comment sucessfully posted");
				}
			}
		}
		else
			return array("status","please give the valid information");
	}
	
	public function doComment($postid,$text){
		if(isset($this->authIdentity)){
			$a=array();
			$select=$this->_db->select()->from($this->_name,array('suserid','ruserid','notifyusers'))->joinLeft('freniz','freniz.userid=status.ruserid',array('type as rtype','username as rusername'))
			->joinLeft('pages','pages.pageid=status.ruserid',array('admins','canpost','vote','bannedusers'))
			->joinLeft('privacy','privacy.userid=status.ruserid',array('post','postignore'))->where('statusid=?',$postid);
			$result=$this->_db->fetchRow($select);
			
			if(isset($result)){
				$commentdata=array('statusid'=>$postid,'userid'=>$this->authIdentity->userid,'comment'=>mysql_real_escape_string(trim($text)),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
				if($this->authIdentity->type=='user' && $result['rtype']=='user'){
					$ignorelist=unserialize($result['postignore']);
					if(($result['post']=='friends' && !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && in_array($result['ruserid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid=$result['ruserid'] || $this->authIdentity->userid==$result['susersid']){
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$postid,'title'=>'commented on','contenttype'=>'post','contenturl'=>'post.php?postid='.$postid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'post_'.$postid);
						$this->_db->insert('comment', $commentdata);
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
                                        $notificationtext.=" your post";
                                    }
                                    else if($user==$result['suserid']){
                                        $notificationtext.=" your post of <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s chart";
                                    }
                                    else if($user==$result['ruserid'])
                                    {
                                        $notificationtext.=" your post";
                                    }
                                    else
									{
										$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s post";
									}
									$notifications["post.php?postid=".$postid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
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
						$this->update($update_staturedata, "statusid='$postid'");
						return array("status"=>"your comment sucessfully posted");
					}
					else
					return array("status"=>"you do not have permission to comment");
				}
				else if($result['type']=='page'){
					$votes=unserialize($result['votes']);
					$bannedusers=unserialize($result['bannedusers']);
					$admins=unserialize($result['admins']);
					if((($result['canpost']=='public' ||($result['canpost']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['ruserid']   )){
						$this->_db->insert('comment', $commentdata);
							
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
                                        $notificationtext.=" your post";
                                    }
                                    else if($user==$result['suserid']){
                                        $notificationtext.=" your post of <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s chart";
                                    }
                                    else if($user==$result['ruserid'])
                                    {
                                        $notificationtext.=" your post";
                                    }
                                    else
									{
										$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s post";
									}
									$notifications["post.php?postid=".$postid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
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
						$this->update($update_data, "statusid='$postid'");
						//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
						return array("status"=>"your comment sucessfully posted");
					}
					else
						return array("status"=>"you do not have permission to comment");
		
				}
				 
			}
		
		}
		else
			return array("status","please give the valid information");
	}
	
	public function vote($postid){
		if(isset($this->authIdentity)){
			$result=$this->find($postid);
			$result=$result[0];
			$vote=unserialize($result['vote']);
			 
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "statusid='$postid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$postid,'title'=>'voted on','contenttype'=>'post','contenturl'=>'post.php?postid='.$postid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'post_'.$postid);
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
						$notificationtext.=" your post";
					}
					else
					{
						$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s post";
					}
					$notifications["post.php?postid=".$postid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
					$update_notification[$user]=$notifications;
					//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
				}
				$notification_case=' case userid ';
				foreach($update_notification as $user=>$notification){
					$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
				}
				$notification_case.=' end';
				 
				$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));
				return array("status"=>"you have voted to this post");
			}
		}
		else
			return array("status","please give the valid information");
	}
	
	public function deletePost($postid){
		if(isset($this->authIdentity)){
			$result=$this->find($postid);
			if($result){
				$result=$result[0];
				if($this->authIdentity->userid==$result['suserid'] || $this->authIdentity->userid==$result['ruserid']){
					$this->delete(array('statusid=?'=>$postid));
					$activityModel=new Application_Model_Activity($this->_db);
					$activityModel->delete(array('contenttype=?'=>'post','contentid=?'=>$postid));
					return array("status"=>"you have deleted this post");
				}
			}
		}
		else
			return array("status","please give the valid information");
	}
	
	public function deleteComment($commentid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('comment')->joinLeft('status', 'comment.statusid=status.statusid',array('suserid','ruserid','commentcount'))->where('commentid=?',$commentid);
			$result=$this->_db->fetchRow($sql);
			$userid=$this->authIdentity->userid;
			if($userid==$result['userid'] || $userid==$result['suserid'] || $userid==$result['ruserid']){
				$this->_db->delete('comment',array('commentid=?'=>$commentid));
				$this->_db->delete('activity',array('contentid=?'=>$result['statusid'],'contenttype=?'=>'post','title=?'=>'commented on','userid=?'=>$userid));
				$updatedata=array('commentcount'=>new Zend_Db_Expr('commentcount-1'));
				$this->update($updatedata,array('statusid=?'=>$result['statusid']));
				return array("status"=>"you have deleted this post");
			}
			else
				return array("status"=>"you dont have permission to delete this comment");
		}
		else
			return array("status","please give the valid information");
	}
	public function unVote($postid)
	{
		if(isset($this->authIdentity)){
			$result=$this->find($postid);
			if($result){
				$result=$result[0];
				
				$vote=unserialize($result['vote']);
				if(in_array($this->authIdentity->userid, $vote)){
					$vote=array_diff($vote, array($this->authIdentity->userid));
					$updatedata=array('vote'=>serialize($vote));
					$this->update($updatedata, array('statusid=?'=>$postid));
					return array("status"=>"you have withdrawn vote from this post");
				}
				else
					return array("status"=> "you havent voted to this post");
			}
		}
		else
			return array("status","please give the valid information");
	}
	
	
	public function getPosts($postids){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;	
			$sql=$sql->where("statusid in(?) and status.accepted='yes'",$postids);
			
			$results=$this->processResults($this->_db->fetchAssoc($sql));
			
			return $results;
		}
	}
	public function getUserPosts($userid){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;
			$sql=$sql->where('status.ruserid=? and status.accepted=? ');
			$results=$this->processResults($this->_db->fetchAssoc($sql,array($userid,'yes')));
				
			return $results;
		}
	}
	public function processResults($results){
		$userid=$this->authIdentity->userid;
		$myfriends=$this->authIdentity->friends;
		$blocklist=$this->authIdentity->blocklistmerged;
		$postids=array_keys($results);
		$comment_sql=$this->_db->select()->from('comment')->joinLeft('user_info', 'user_info.userid=comment.userid',$this->userinfo)->joinLeft('image', 'user_info.propic=image.imageid','image.url as commentpic_url')->where('statusid in (?)',$postids);
		$comment_result=$this->_db->fetchAssoc($comment_sql);
		
		$myfriends=$this->authIdentity->friends;
		foreach ($results as $postid => $values){
			$friends=unserialize($values['rfriendlist']);
			
			$privacy=$values['pt'];
			$specific=unserialize($values['specificlist']);
			$hidden=unserialize($values['hiddenlist']);
			
			if((($privacy=='public'||($privacy=='friends' && in_array($userid,$friends)) || ($privacy=='fof' && (count(array_intersect($friends, $myfriends))>=1) || in_array($userid, $friends)) || ($privacy=='specific' && in_array($userid, $specific))) && !in_array($values['userid'], array_merge($blocklist,$hidden))) || $userid==$values['userid']){
			$results[$postid]['comments']=$this->filter_by_value($comment_result, 'statusid', $postid);	
			}
			else{
				unset($results[$postid]);
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
