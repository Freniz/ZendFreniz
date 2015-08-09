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
	protected $authIdentity,$registry;
	protected $sql;
	protected $suserinfo=array('username as susername','propic as spropic','url as suser_url');
	protected $ruserinfo=array('username as rusername','propic as rpropic','url as ruser_url');
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
						
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
		$this->registry=Zend_Registry::getInstance();
		$this->sql=$db->select()->from($this->_name)
		->joinLeft('freniz as suser_info','status.suserid=suser_info.userid',$this->suserinfo)
		->joinLeft('friends_vote as sfriends_vote', 'status.suserid=sfriends_vote.userid','friendlist as sfriendlist')
		->joinLeft('image as simage','suser_info.propic=simage.imageid','url as spropic_url')
		->joinLeft('freniz as ruser_info','status.ruserid=ruser_info.userid',$this->ruserinfo)
		->joinLeft('friends_vote as rfriends_vote', 'status.ruserid=rfriends_vote.userid','friendlist as rfriendlist')
		->joinLeft('image as rimage','ruser_info.propic=rimage.imageid','url as rpropic_url');
		
	}
public function addScribbles($ruserid,$text,$pt,$cpt)
	{
		if(isset($this->authIdentity)){
			$insert_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'status'=>trim($text),'date'=>new Zend_Db_Expr('now()'),'vote'=>'a:0:{}','notifyusers'=>'a:0:{}');
			$insert_data['dontnotify']='a:0:{}';
			$isvalid=false;$message='';$canupdateavtivity=false;
			if($this->authIdentity->userid==$ruserid){
				$isvalid=true;
				$canupdateavtivity=true;
				if($this->authIdentity->type=='user'){
					$privacy=$this->authIdentity->privacy;
					$insert_data=array_merge($insert_data,array('pt'=>$pt,'specificlist'=>$privacy['postspeci'],'hiddenlist' => $privacy['posthidden'],'accepted'=>'yes','cpt'=>$cpt,'ciu'=>$privacy['postignore'],'csu'=>$privacy['postspecificpeople']));
					
				}
				else{
					$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','accepted'=>'yes','cpt'=>$privacy['canpost'],'ciu'=>'a:0:{}','csu'=>'a:0:{}'));
				}
			}
			else{
				$privacy_data=array('post','postignore','postvisi','postspeci','posthidden','advancedprivacypost','autoacceptusers','blockactivityusers','postspecificpeople');
				$sql=$this->_db->select()->from('freniz')->joinLeft('friends_vote', 'friends_vote.userid=freniz.userid','friendlist')
						->joinLeft('privacy','privacy.userid=freniz.userid',$privacy_data)
						->joinLeft('pages','freniz.userid=pages.pageid',array('canpost as page_canpost','admins as page_admins','vote as page_vote','bannedusers as page_bannedusers'))
						->joinLeft('groups', 'freniz.userid=groups.groupid',array('canpost as group_canpost','admins as group_admins','members as group_members','bannedusers as group_bannedusers'))
						->where(' freniz.userid=?',$ruserid);
				
				$result=$this->_db->fetchRow($sql);
				if($result['type']=='user' && $this->authIdentity->type=='user'){
					$postignore=unserialize($result['postignore']);
					$autoacceptusers=unserialize($result['autoacceptusers']);
					$blockusersactivity=unserialize($result['blockactivityusers']);
					if(($result['post']=='public' || ($result['post']=='friends' && in_array($ruserid, $this->authIdentity->friends)) ||($result['post']=='fof' && ((count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends))>=1) || in_array($ruserid, $this->authIdentity->userid))) || ($result['post']=='specific' && in_array($this->authIdentity->userid, unserialize($result['postspecificpeople'])) )) && !in_array($this->authIdentity->userid, $postignore) && !in_array($ruserid, $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, $blockusersactivity['post']) ){
					//if((($result['post']=='friends' &&in_array($ruserid, $this->authIdentity->friends) )))( !in_array($ruserid, $this->authIdentity->blocklistmerged)&& !in_array($this->authIdentity->userid, $postignore))){
						if($result['advancedprivacypost']=='on' ){
							if(in_array($this->authIdentity->userid, $autoacceptusers['post'])){
								$isvalid=true;
								$insert_data=array_merge($insert_data,array('pt'=>$result['postvisi'],'specificlist'=>$result['postspeci'],'hiddenlist' => $result['posthidden'],'accepted'=>'yes','cpt'=>$result['post'],'ciu'=>$result['postignore'],'csu'=>$result['postspecificpeople']));
								$canupdateavtivity=true;
							}
							else{
								$isvalid=true;
								$insert_data=array_merge($insert_data,array('pt'=>$result['postvisi'],'specificlist'=>$result['postspeci'],'hiddenlist' => $result['posthidden'],'cpt'=>$result['post'],'ciu'=>$result['postignore'],'csu'=>$result['postspecificpeople']));
							}
						}
						else {
							$isvalid=true;
							$insert_data=array_merge($insert_data,array('pt'=>$result['postvisi'],'specificlist'=>$result['postspeci'],'hiddenlist' => $result['posthidden'],'accepted'=>'yes','cpt'=>$result['post'],'ciu'=>$result['postignore'],'csu'=>$result['postspecificpeople']));
							$canupdateavtivity=true;
						}
					}
				}
				else if($result['type']=='page'){
					if((($result['page_canpost']=='public' || ($result['page_canpost']=='votedusers' && in_array($this->authIdentity->userid, unserialize($result['page_vote']))))&&!in_array($this->authIdentity->userid, unserialize($result['page_bannedusers'])) ) ){
						$isvalid=true;
						$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist' => 'a:0:{}','accepted'=>'yes','cpt'=>$result['page_canpost'],'ciu'=>'a:0:{}','csu'=>'a:0:{}'));
						$canupdateavtivity=true;
					}
				}
				else if($result['type']=='group' && $this->authIdentity->type=='user'){
					if((( ($result['group_canpost']=='members' && in_array($this->authIdentity->userid, unserialize($result['group_members']))) || ($result['group_canpost']=='admins' && in_array($this->authIdentity->userid, unserialize($result['group_admins']))))&&!in_array($this->authIdentity->userid, unserialize($result['group_bannedusers']) ) )){
						$isvalid=true;
						$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist' => 'a:0:{}','accepted'=>'yes','cpt'=>$result['group_canpost'],'ciu'=>'a:0:{}','csu'=>'a:0:{}'));
						$canupdateavtivity=true;
					}
				}
				
						
			}
			if($isvalid){
				$uptdid=$this->insert($insert_data);
				if($canupdateavtivity){
					if($this->authIdentity->userid!=$ruserid && $result['rtype']!='group')
					{
						$notify_data=array('userid'=>$ruserid,'contenturl'=>'scribbles/'.$uptdid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> added a post on your chart','userpic'=>$this->authIdentity->propic);
						$this->_db->insert('notifications', $notify_data);
					}
					$activitydata=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'posted on','contenttype'=>'post','contenturl'=>'post.php?postid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'post_'.$uptdid);
					$this->_db->insert('activity', $activitydata);
				}
				return array('id'=>$uptdid,'userid'=>$this->authIdentity->userid,'propic_url'=>$this->authIdentity->propic_url,'username'=>$this->authIdentity->username,'post'=>trim($text),'date'=>date('c'),'pt'=>$pt,'status'=>'success');
			
			}
			else return false;
		}
		else return false;
	}
	public function doCommentscribbles($postid,$text){
		if(isset($this->authIdentity)){
			$a=array();
			$select=$this->_db->select()->from($this->_name,array('suserid','ruserid','dontnotify','vote','notifyusers','cpt','ciu','csu'))->joinLeft('freniz','freniz.userid=status.ruserid',array('type as rtype','username as rusername'))
			->joinLeft('pages','pages.pageid=status.ruserid',array('admins','page_vote'=>'vote','bannedusers'))
			->joinLeft('friends_vote', 'friends_vote.userid=freniz.userid','friendlist')->where('status.statusid=?',$postid);
			$result=$this->_db->fetchRow($select);
			$rfriends=unserialize($result['friendlist']);
			if(isset($result)){
				$commentdata=array('statusid'=>$postid,'userid'=>$this->authIdentity->userid,'comment'=>trim(trim($text)),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
				if($this->authIdentity->type=='user' && $result['rtype']=='user'){
					$ignorelist=unserialize($result['ciu']);
					$specificlist=unserialize($result['csu']);
					if((($result['cpt']=='public' || ($result['cpt']=='fof' &&((count(array_intersect($this->authIdentity->friends, $rfriends))>=1)|| in_array($result['ruserid'], $this->authIdentity->friends)))||($result['cpt']=='friends'  && in_array($result['ruserid'], $this->authIdentity->friends)) || ($result['cpt']=='specific' && in_array($this->authIdentity>userid, $specificlist))) && !in_array($this->authIdentity->userid, $ignorelist)&& !in_array($result['ruserid'], $this->authIdentity->blocklistmerged)) || $this->authIdentity->userid==$result['ruserid'] || $this->authIdentity->userid==$result['susersid']){
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$postid,'title'=>'commented on','contenttype'=>'post','contenturl'=>'post.php?postid='.$postid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'post_'.$postid);
						$this->_db->insert('comment', $commentdata);
						$commentid=$this->_db->lastInsertId('comment');
						$this->_db->insert('commentactivity',array('commentid'=>$commentid,'objid'=>$postid,'type'=>'scribble','userid'=>$this->authIdentity->userid,'comment'=>trim(trim($text))));
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
						 
	
	
	
	
	
	
						$notifyusers=unserialize($result['notifyusers']);
						if(!in_array($this->authIdentity->userid, $notifyusers))
							array_push($notifyusers, $this->authIdentity->userid);
						$votes=unserialize($result['vote']);
						$dontnotify=unserialize($result['dontnotify']);
						$notifyusers1=array_unique(array_diff(array_merge($notifyusers,array($result['suserid'],$result['ruserid']),$votes),$dontnotify,array($this->authIdentity->userid)));
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
								$query.=' ('.$this->_db->quote(array($user,'scribbles/'.$postid,$notificationtext,$userpic)).'),';
							}
							$query=substr($query, 0,-1);
							$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
							$this->_db->query($query);
						}
	
	
						$update_staturedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_staturedata, "statusid='$postid'");
	
					}
				}
				else if($result['rtype']=='page'){
					$votes=unserialize($result['page_vote']);
					$bannedusers=unserialize($result['bannedusers']);
					$admins=unserialize($result['admins']);
					if((($result['cpt']=='public' ||($result['cpt']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['ruserid'] || $this->authIdentity->userid==$result['suserid']  )){
						$this->_db->insert('comment', $commentdata);
						$commentid=$this->_db->lastInsertId('comment');
						$this->_db->insert('commentactivity',array('commentid'=>$commentid,'objid'=>$postid,'type'=>'scribble','userid'=>$this->authIdentity->userid,'comment'=>trim(trim($text))));
	
						$notify_data=array('userid'=>$result['ruserid'],'contenturl'=>'scribbles/'.$postid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> commented on your post','userpic'=>$this->authIdentity->propic);
						$this->_db->insert('notifications', $notify_data);
	
	
						$update_data=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_data, "statusid='$postid'");
						//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
							
					}
	
				}
					
			}
	
		}
	}
	
public function votescribbles($postid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('status',array('suserid','ruserid','vote','notifyusers','dontnotify'))->joinLeft('freniz', 'status.ruserid=freniz.userid',array('rusername'=>'username'))->where('statusid=?',$postid);
			$result=$this->_db->fetchRow($sql);
			//$result=$result[0];
			if(!empty($result)){
			$vote=unserialize($result['vote']);
			if(!in_array($this->authIdentity->userid,$vote)){
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "statusid='$postid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$postid,'title'=>'voted on','contenttype'=>'post','contenturl'=>'post.php?postid='.$postid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'post_'.$postid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			
			$notifyusers=unserialize($result['notifyusers']);
			$vote1=array_diff($vote,array($this->authIdentity->userid));
			
			 
			$dontnotify=unserialize($result['dontnotify']);
			$notifyusers=array_unique(array_diff(array_merge($notifyusers,array($result['suserid'],$result['ruserid']),$vote1),$dontnotify,array($this->authIdentity->userid)));
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
					if($user==$result['ruserid'] )
					{
						$notificationtext.=" your post";
					}
					else
					{
						$notificationtext.=" <a href='".$result['userid']."'>".$result['username']."</a>'s post";
					}
					$query.=' ('.$this->_db->quote(array($user,'scribbles/'.$postid,$notificationtext,$userpic)).'),';
				}
				$query=substr($query, 0,-1);
				$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
				$this->_db->query($query);
			}
		
		}
		}
		}
	}
	
	public function deleteScribbles($postid){
		if(isset($this->authIdentity)){
			$result=$this->find($postid);
			if($result->count()>0){
				$result=$result[0];
				if($this->authIdentity->userid==$result['suserid'] || $this->authIdentity->userid==$result['ruserid']){
					$this->delete(array('statusid=?'=>$postid));
					$activityModel=new Application_Model_Activity($this->_db);
					$activityModel->delete(array('contenttype=?'=>'post','contentid=?'=>$postid));
				}
			}
			
			}
			
	}
	
	public function deletescribblesComment($commentid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('comment')->joinLeft('status', 'comment.statusid=status.statusid',array('suserid','ruserid','commentcount'))->where('commentid=?',$commentid);
			$result=$this->_db->fetchRow($sql);
			$userid=$this->authIdentity->userid;
			if($userid==$result['userid'] || $userid==$result['suserid'] || $userid==$result['ruserid']){
				$this->_db->delete('comment',array('commentid=?'=>$commentid));
				$this->_db->delete('activity',array('contentid=?'=>$result['statusid'],'contenttype=?'=>'post','title=?'=>'commented on','userid=?'=>$userid));
				$updatedata=array('commentcount'=>new Zend_Db_Expr('commentcount-1'));
				$this->update($updatedata,array('statusid=?'=>$result['statusid']));
			}
		}
	}
	public function unVotescribbles($postid)
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
				}
			}
		}
	}
	
	
	public function getPosts($postids){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;	
			$sql=$sql->where("statusid in(?) and status.accepted='yes'",$postids);
			
			$results=$this->processResults($this->_db->fetchAssoc($sql));
			
			return $results;
		}
	}
	public function Scribbles($userid,$from){
		if(isset($this->authIdentity)){
			$sql=clone $this->sql;
			$sql=$sql->where('status.ruserid=? and status.accepted=\'yes\' ',array($userid))->order('date desc')->limit($this->registry->limit,$from);
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
		$blocklist=$this->authIdentity->blocklistmerged;
		$postids=array_keys($results);
		$comment_sql=$this->_db->select()->from('comment as s')
						->joinLeft('comment as t', 's.statusid=t.statusid and s.commentid < t.commentid','')
						->joinLeft('freniz', 'freniz.userid=s.userid',array('username','freniz.url as user_url'))
						->joinLeft('image', 'freniz.propic=image.imageid','image.url as commentpic_url')
						->where('s.statusid in (?)',$postids)
						->group('s.commentid')->having('count(*)<?',$this->registry->commentDefaultLimit);
			$comment_result=$this->_db->fetchAssoc($comment_sql);
		foreach ($results as $postid => $values){
			$friends=unserialize($values['rfriendlist']);
			$privacy=$values['pt'];
			$specific=unserialize($values['specificlist']);
			$hidden=unserialize($values['hiddenlist']);
			$cpt=$values['cpt'];
			$csu=unserialize($values['csu']);
			$ciu=unserialize($values['ciu']);
			$results[$postid]['iscommentable']=false;
			if((($cpt=='public'||($cpt=='friends' && in_array($userid,$friends)) || ($cpt=='fof' && (count(array_intersect($friends, $myfriends))>=1 || in_array($userid, $friends))) || ($cpt=='specific' && in_array($userid, $csu))) && !in_array($values['ruserid'], array_merge($blocklist,$ciu))) || $userid==$values['suserid'] || $userid==$values['ruserid']){
				$results[$postid]['iscommentable']=true;
			}
			if((($privacy=='public'||($privacy=='friends' && in_array($userid,$friends)) || ($privacy=='fof' && (count(array_intersect($friends, $myfriends))>=1 || in_array($userid, $friends))) || ($privacy=='specific' && in_array($userid, $specific))) && !in_array($values['ruserid'], array_merge($blocklist,$hidden))) || $userid==$values['suserid'] || $userid==$values['ruserid']){
			$results[$postid]['comments']=$this->filter_by_value($comment_result, 'statusid', $postid);

			if($results[$postid]['commentcount']>count($results[$postid]['comments'])){
				$results[$postid]['loadprevcomments']=true;
			}
			else
				$results[$postid]['loadprevcomments']=false;
			
			}
			else{
					
				unset($results[$postid]);
			}
		}
			
			$sql=$this->_db->select()->from('commentactivity','max(id) as maxid');
			$maxid=$this->_db->fetchRow($sql);
			$final_results['maxcommentid']=$maxid['maxid'];
		
			$final_results['results']=$results;
			return $final_results;
	}
	
	private function filter_by_value ($array, $index, $value){
		$newarray=array();
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
    
    public function getComments($postid,$from){
    	//if(isset($this->authIdentity)){
    	$sql=$this->_db->select()->from('comment')->joinLeft('freniz', 'comment.userid=freniz.userid',array('username','url'))
    	->joinLeft('image', 'image.imageid=freniz.propic','url as imageurl')
    	->where('statusid=?',$postid)->order('statusid desc')->limit($this->registry->commentlimit,$from);
    	$result=$this->_db->fetchAssoc($sql);
    	return $result;
    
    	//}
    }
    public function tobeReviewed($from){
    	//$sql=$this->_db->select()->from('status')->joinLeft('freniz','freniz.userid=status.suserid',array('susername'=>'username','url'))->joinLeft('image','image.imageid=freniz.propic','url as user_imageurl')->where('status.accepted=\'no\' and status.ruserid=?',$this->authIdentity->userid)->order('date desc')->limit($this->registry->limit,$from);
    	$sql= clone $this->sql;
    	$sql=$sql->where('status.accepted=\'not\' and status.ruserid=?',$this->authIdentity->userid)->order('date desc')->limit($this->registry->limit,$from);
    	$results=$this->_db->fetchAssoc($sql);
    	if(count($results)==$this->registry->limit)
    		$final_results['loadmore']=true;
    	else $final_results['loadmore']=false;
    	$final_results['results']=$results;
    	return $final_results;
    }
    public function reviewPosts($ids=null,$accept=true){
    	$where=array('ruserid=?'=>$this->authIdentity->userid);
    	if(!empty($ids)){
    		$where['statusid in (?)']=$ids;
    		$sql=$this->_db->select()->from('status',array('statusid','suserid','ruserid','date'))->where('statusid in (?)',$ids)->where('ruserid=? and accepted=\'not\'',$this->authIdentity->userid);
    		$posts=$this->_db->fetchAssoc($sql);
    	}
    	if($accept){
    		$this->update(array('accepted'=>'yes'),$where);
    		foreach($posts as $id=>$values){
    			$this->_db->insert('activity',array('userid'=>$values['suserid'],'ruserid'=>$values['ruserid'],'contentid'=>$id,'title'=>'posted on','contenttype'=>'post','date'=>$values['date'],'alternate_contentid'=>'post_'.$id,'contenturl'=>'post.php?postid='.$id));
    		}
    	}
    	else
    		$this->delete($where);
    
    }
	
}
