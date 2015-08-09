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
						
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
		$this->registry=Zend_Registry::getInstance();
		$this->sql=$db->select()->from($this->_name)
		->joinLeft('user_info','stature.userid=user_info.userid',$this->userinfo)
		->joinLeft('friends_vote', 'stature.userid=friends_vote.userid','friendlist')
		->joinLeft('image','user_info.propic=image.imageid','url as propic_url');
			
	}
public function addStatures($text,$visi,$cpt)
	{
		if(isset($this->authIdentity)){
			$privacy=$this->authIdentity->privacy;
			$stature_data=array('userid'=>$this->authIdentity->userid,'stature'=>$text,'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$visi,'specificlist'=>$privacy['staturespeci'],'hiddenlist'=>$privacy['staturehidden'],'notifyusers'=>'a:0:{}','cpt'=>$cpt,'ciu'=>$privacy['statureignore'],'csu'=>$privacy['staturespecificpeople']);
			$stature_data['dontnotify']='a:0:{}';
			$uptdid=$this->insert($stature_data);
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$uptdid,'title'=>'update stature','contenttype'=>'stature','contenturl'=>'stature.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'stature_'.$uptdid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			return array('id'=>$uptdid,'userid'=>$this->authIdentity->userid,'propic_url'=>$this->authIdentity->propic_url,'username'=>$this->authIdentity->username,'stature'=>trim($text),'date'=>date('c'),'status'=>'success');
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
	public function getUserStatures($userid,$from){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;
			$sql=$sql->where('stature.userid=?',$userid)->order('statureid desc')->limit($this->registry->limit,$from);
			
			$results=$this->processResults($this->_db->fetchAssoc($sql));
				
			return $results;
		}
	}
	public function processResults($results){
		if(empty($results))
			return array();
		if(count($results)==$this->registry->limit)
			$final_results['loadmore']=true;
		else
			$final_results['loadmore']=false;
		$userid=$this->authIdentity->userid;
		$myfriends=$this->authIdentity->friends;
		$blocklist=array_merge($this->authIdentity->blocklist,$this->authIdentity->blockedby);
		$statureids=array_keys($results);
		$comment_sql=$this->_db->select()->from('stature_comment as s')
				->joinLeft('stature_comment as t','s.statureid=t.statureid and s.commentid<t.commentid','')
				->joinLeft('freniz', 'freniz.userid=s.userid',array('username','url'))
				->joinLeft('image', 'freniz.propic=image.imageid','image.url as commentpic_url')
				->where('s.statureid in (?)',$statureids)
				->group('s.commentid')->having('count(*) < ?',$this->registry->commentDefaultLimit);
		$comment_result=$this->_db->fetchAssoc($comment_sql);
		foreach ($results as $statureid => $values){
			$friends=unserialize($values['friendlist']);
			
			$privacy=$values['pt'];
			$specific=unserialize($values['specificlist']);
			$hidden=unserialize($values['hiddenlist']);
			$cprivacy=$values['cpt'];
			$results[$statureid]['iscommentable']=false;
			if((($cprivacy=='public'||($cprivacy=='friends' && in_array($userid,$friends)) || ($cprivacy=='fof' && (count(array_intersect($friends, $myfriends))>=1) || in_array($userid, $friends)) || ($cprivacy=='specific' && in_array($userid, unserialize($values['csu'])))) && !in_array($values['userid'],$blocklist) && !in_array($this->authIdentity->userid, unserialize($values['ciu']))) || $userid==$values['userid']){
				$results[$statureid]['iscommentable']=true;
			}
			if((($privacy=='public'||($privacy=='friends' && in_array($userid,$friends)) || ($privacy=='fof' && (count(array_intersect($friends, $myfriends))>=1) || in_array($userid, $friends)) || ($privacy=='specific' && in_array($userid, $specific))) && !in_array($values['userid'],$blocklist) && !in_array($this->authIdentity->userid, $hidden)) || $userid==$values['userid']){
			$results[$statureid]['comments']=$this->filter_by_value($comment_result, 'statureid', $statureid);
			
			if($results[$statureid]['commentcount']>count($results[$statureid]['comments'])){
				$results[$statureid]['loadprevcomments']=true;
			}
			else
				$results[$statureid]['loadprevcomments']=false;
			}
			else{
				unset($results[$statureid]);
			}
		}
		
		$sql=$this->_db->select()->from('commentactivity','max(id) as maxid');
		$maxid=$this->_db->fetchRow($sql);
		$final_results['maxcommentid']=$maxid['maxid'];
			$final_results['results']=$results;
			return $final_results;
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
    
 public function dostatureComment($statureid,$text){
    	if(isset($this->authIdentity)){
    		$a=array();
    		$select=$this->_db->select()->from($this->_name,array('userid','notifyusers','dontnotify','vote','cpt','ciu','csu'))->joinLeft('freniz','freniz.userid=stature.userid',array('type','username'))
    				->joinLeft('friends_vote', 'friends_vote.userid=freniz.userid','friendlist')
					->joinLeft('pages','pages.pageid=stature.userid',array('admins','page_vote'=>'vote','bannedusers'))
    				->where('statureid=?',$statureid);
    		$result=$this->_db->fetchRow($select);
    		if(isset($result)){
    			$stature_commentdata=array('statureid'=>$statureid,'userid'=>$this->authIdentity->userid,'comment'=>trim($text),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
    			print_r($this->authIdentity->type);
    			if($this->authIdentity->type !='leaf' && $result['type']=='user'){
	    			$ignorelist=unserialize($result['ciu']);
	    			if((($result['cpt']=='public' || ($result['cpt']=='friends' && in_array($result['userid'], $this->authIdentity->friends)) ||($result['cpt']=='fof' && ((count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends))>=1) || in_array($result['userid'], $this->authIdentity->userid))) || ($result['cpt']=='specific' && in_array($this->authIdentity->userid, unserialize($result['csu'])) )) && !in_array($this->authIdentity->userid, $ignorelist) && !in_array($result['userid'], $this->authIdentity->blocklistmerged) ) || $this->authIdentity->userid==$result['userid'] ){
	    					
	    			//if(($result['post']=='friends' && !in_array($result['userid'], $this->authIdentity->blocklistmerged) && in_array($result['userid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid=$result['userid']){
	    				$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['userid'],'contentid'=>$statureid,'title'=>'commented on','contenttype'=>'stature','contenturl'=>'stature.php?statureid='.$statureid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'stature_'.$statureid);
	    				$statureCommentsModel=new Application_Model_StatureComments($this->_db);
	    				$commentid=$statureCommentsModel->insert($stature_commentdata);
	    				$this->_db->insert('commentactivity', array('commentid'=>$commentid,'objid'=>$statureid,'type'=>'stature','comment'=>trim($text),'userid'=>$this->authIdentity->userid));
	    				
	    				$activityModel=new Application_Model_Activity($this->_db);
	    				$activityModel->insert($activity);
	    				
	    				$notifyusers=unserialize($result['notifyusers']);
	    				if(!in_array($this->authIdentity->userid, $notifyusers))
	    					array_push($notifyusers, $this->authIdentity->userid);
	    				$votes=unserialize($result['vote']);
	    				$dontnotify=unserialize($result['dontnotify']);
	    				$notifyusers1=array_unique(array_diff(array_merge($notifyusers,array($result['userid']),$votes),$dontnotify,array($this->authIdentity->userid)));
	    				//print_r($notifyusers1);
	    				if(!empty($notifyusers1)){
	    					$query='insert into notifications(userid,contenturl,notification,userpic) values ';
	    					$userpic=$this->authIdentity->propic;
	    					foreach ($notifyusers1 as $user){
	    						if(sizeof($notifyusers1)>1)
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
	    						$query.=' ('.$this->_db->quote(array($user,'stature/'.$statureid,$notificationtext,$userpic)).'),';
	    					}
	    					$query=substr($query, 0,-1);
	    					$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
	    					$this->_db->query($query);
	    				}
	    				
	    				$update_staturedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
	    				$this->update($update_staturedata, "statureid='$statureid'");
	    					
	    			}
    			}
    			else if($result['type']=='leaf'){
    				$votes=unserialize($result['page_vote']);
    				$bannedusers=unserialize($result['bannedusers']);
    				$admins=unserialize($result['admins']);
    				if((($result['cpt']=='public' ||($result['cpt']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['userid']   )){
    					$this->_db->insert('stature_comment', $stature_commentdata);
    					$commentid=$this->_db->lastInsertId('stature_comment');
    					$this->_db->insert('commentactivity', array('commentid'=>$commentid,'objid'=>$statureid,'type'=>'stature','comment'=>trim($text),'userid'=>$this->authIdentity->userid)); 
    					
    					$notify_data=array('userid'=>$result['userid'],'contenturl'=>'stature/'.$statureid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->userid.'</a> commented on your stature','userpic'=>$this->authIdentity->propic);
    					$this->_db->insert('notifications', $notify_data);
    					
    					$update_staturedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'));
   						$this->update($update_staturedata, "statureid='$statureid'");
    					//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
    					
    				}
    				
    			}
    			
    		}
    		return array('statureid'=>$statureid,'commentid'=>$commentid,'userpic'=>$this->authIdentity->propic_url,'userid'=>$this->authIdentity->userid,'username'=>$this->authIdentity->username,'comment'=>trim($text),'date'=>date('c'),'status'=>'success');
    	}
    }
    
    
    public function deleteStature($statureid){
    	if(isset($this->authIdentity)){
    		$userid=$this->authIdentity->userid;
    		$this->delete("statureid='$statureid' and userid='$userid'");
    		$this->_db->delete('activity',"contenttype='stature' and contentid='$statureid'");
    	}
    }
    public function deletestatureComment($commentid){
    	if(isset($this->authIdentity)){
    		$sql=$this->_db->select()->from('stature_comment')->joinLeft($this->_name, 'stature_comment.statureid=stature.statureid','userid as stature_userid')->where('commentid=?',$commentid);
    		$result=$this->_db->fetchRow($sql);
    		$userid=$this->authIdentity->userid;
    		if($result['userid']==$userid || $result['stature_userid']==$userid){
    			$this->_db->delete('stature_comment',"commentid='$commentid'");
    			$this->update(array('commentcount'=>new Zend_Db_Expr('commentcount-1')),array('statureid=?'=>$result['statureid']));
    		}
    	}
    }
public function votestature($statureid){
    	 if(isset($this->authIdentity)){
    	 	$sql=$this->_db->select()->from('stature',array('userid','notifyusers','vote','dontnotify'))->joinLeft('freniz','stature.userid=freniz.userid',array('username','propic'))->where('statureid=?',$statureid);
    	 	$result=$this->_db->fetchRow($sql);
    	 	if(!empty($result)){
    	 	$vote=unserialize($result['vote']);
    	 	
    	 	array_push($vote, $this->authIdentity->userid);
    	 	$vote=array_unique($vote);
    	 	$update_data=array('vote'=>serialize($vote));
    	 	$this->update($update_data, "statureid='$statureid'");
    	 	$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['userid'],'contentid'=>$statureid,'title'=>'voted on','contenttype'=>'stature','contenturl'=>'stature.php?statureid='.$statureid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'stature_'.$statureid);
    	 	$activityModel=new Application_Model_Activity($this->_db);
    	 	$activityModel->insert($activity);
    	 	
    	 	
    	 	$notifyusers=unserialize($result['notifyusers']);
    	 	$vote1=array_diff($vote,array($this->authIdentity->userid));

    	 	
    	 	$dontnotify=unserialize($result['dontnotify']);
    	 	$notifyusers=array_unique(array_diff(array_merge($notifyusers,array($result['userid']),$vote1),$dontnotify,array($this->authIdentity->userid)));
    	 	if(!empty($notifyusers)){
    	 		$query='insert into notifications(userid,contenturl,notification,userpic) values ';
    	 		$userpic=$this->authIdentity->propic;
    	 		foreach($notifyusers as $user){
    	 			if(sizeof($vote1)>1)
    	 			{
    	 				$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($vote1)-1)." other voted on";
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
    	 					$query.=' ('.$this->_db->quote(array($user,'stature/'.$statureid,$notificationtext,$userpic)).'),';
    			}
    			$query=substr($query, 0,-1);
    			$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
    			$this->_db->query($query);
    	 	}
    	 	
    	 	}
    	 }
    }
    public function unVotestature($statureid){
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
    public function getComments($statureid,$from){
    	//if(isset($this->authIdentity)){
    	$sql=$this->_db->select()->from('stature_comment')->joinLeft('freniz', 'stature_comment.userid=freniz.userid',array('username','url'))
    	->joinLeft('image', 'image.imageid=freniz.propic','url as imageurl')
    	->where('statureid=?',$statureid)->order('commentid desc')->limit($this->registry->commentlimit,$from);
    	$result=$this->_db->fetchAssoc($sql);
    	return $result;
    
    	//}
    }
    
}


