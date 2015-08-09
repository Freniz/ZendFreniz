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
	protected $suserinfo=array('username as susername','propic as spropic','url as suser_url');
	protected $ruserinfo=array('username as rusername','propic as rpropic','url as ruser_url');
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
		->joinLeft('freniz as sfreniz','video.suserid=sfreniz.userid',$this->suserinfo)
		->joinLeft('friends_vote as sfriends_vote', 'video.suserid=sfriends_vote.userid','friendlist as sfriendlist')
		->joinLeft('image as simage','sfreniz.propic=simage.imageid','url as spropic_url')
		->joinLeft('freniz as rfreniz','video.ruserid=rfreniz.userid',$this->ruserinfo)
		->joinLeft('friends_vote as rfriends_vote', 'video.ruserid=rfriends_vote.userid','friendlist as rfriendlist')
		->joinLeft('image as rimage','rfreniz.propic=rimage.imageid','url as rpropic_url');
		
	}
	function removeFromEnd($string, $stringToRemove) {
		$stringToRemoveLen = strlen($stringToRemove);
		$stringLen = strlen($string);
	
		$pos = $stringLen - $stringToRemoveLen;
	
		$out = substr($string, 0, $pos);
	
		return $out;
	}
	
public function addVideos($title,$embeddcode,$ruserid){
		if(isset($this->authIdentity)){
			$embeddcode=substr($embeddcode, 0,stripos($embeddcode, '</iframe>')+9);
			$insert_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'title'=>$title,'embeddcode'=>$embeddcode,'date'=>new Zend_Db_Expr('now()'),'vote'=>'a:0:{}','notifyusers'=>'a:0:{}');
			$insert_data['dontnotify']='a:0:{}';

			$isvalid=false;$message='';$canupdateavtivity=false;

			if($this->authIdentity->userid==$ruserid){

				$isvalid=true;

				$canupdateavtivity=true;

				if($this->authIdentity->type=='user'){

					$privacy=$this->authIdentity->privacy;

					$insert_data=array_merge($insert_data,array('pt'=>$privacy['videovisi'],'specificlist'=>$privacy['videospeci'],'hiddenlist' => $privacy['videohidden'],'accepted'=>'yes','cpt'=>$privacy['video'],'ciu'=>$privacy['videoignore'],'csu'=>$privacy['videospecificpeople']));

				}

				else{

					$insert_data=array_merge($insert_data,array('pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','accepted'=>'yes','cpt'=>$privacy['canpost'],'ciu'=>'a:0:{}','csu'=>'a:0:{}'));

				}

			}

			else{

				$privacy_data=array('video','videoignore','videovisi','videospeci','videohidden','advancedprivacyvideo','autoacceptusers','blockactivityusers','videospecificpeople');

				$sql=$this->_db->select()->from('freniz')->joinLeft('friends_vote', 'friends_vote.userid=freniz.userid','friendlist')
				->joinLeft('privacy','privacy.userid=freniz.userid',$privacy_data)

				->joinLeft('pages','freniz.userid=pages.pageid',array('canpost as page_canpost','admins as page_admins','vote as page_vote','bannedusers as page_bannedusers'))

				->joinLeft('groups', 'freniz.userid=groups.groupid',array('canpost as group_canpost','admins as group_admins','members as group_members','bannedusers as group_bannedusers'))

				->where(' freniz.userid=?',$ruserid);

		

				$result=$this->_db->fetchRow($sql);

				if($result['type']=='user' && $this->authIdentity->type=='user'){

					$videoignore=unserialize($result['videoignore']);

					$autoacceptusers=unserialize($result['autoacceptusers']);

					$blockusersactivity=unserialize($result['blockactivityusers']);

					if(($result['video']=='public' || ($result['video']=='friends' && in_array($ruserid, $this->authIdentity)) ||($result['video']=='fof' && ((count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends))>=1) || in_array($ruserid, $this->authIdentity->userid))) || ($result['video']=='specific' && in_array($this->authIdentity->userid, unserialize($result['videospecificpeople'])) )) && !in_array($this->authIdentity->userid, $videoignore) && !in_array($ruserid, $this->authIdentity->blocklistmerged)  ){
						$insert_data=array_merge($insert_data,array('cpt'=>$result['video'],'ciu'=>$result['videoignore'],'csu'=>$result['videospecificpeople']));
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
					if($ruserid!=$this->authIdentity->userid && $result['rtype']!='group'){
					$notify_data=array('userid'=>$ruserid,'contenturl'=>'video/'.$uptdid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> added video on your chart','userpic'=>$this->authIdentity->propic);
					$this->_db->insert('notifications', $notify_data);
					}

					$activitydata=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'post a video on','contenttype'=>'video','contenturl'=>'video.php?videoid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'video_'.$uptdid);

					$this->_db->insert('activity', $activitydata);

				}

			}

		}
	}
public function doCommentvideo($videoid,$text){
		if(isset($this->authIdentity)){

			$a=array();

			$select=$this->_db->select()->from($this->_name,array('suserid','ruserid','dontnotify','vote','notifyusers','cpt','ciu','csu'))->joinLeft('freniz','freniz.userid=video.ruserid',array('type as rtype','username as rusername'))

			->joinLeft('friends_vote', 'friends_vote.userid=freniz.userid','friendlist')
			->joinLeft('pages','pages.pageid=video.ruserid',array('admins','page_vote'=>'vote','bannedusers'))
			->where('videoid=?',$videoid);

			$result=$this->_db->fetchRow($select);

				

			if(isset($result)){
				$commentdata=array('videoid'=>$videoid,'userid'=>$this->authIdentity->userid,'comment'=>(trim($text)),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));

						if($this->authIdentity->type=='user' && $result['rtype']=='user'){

					$ignorelist=unserialize($result['ciu']);
					if((($result['cpt']=='public' || ($result['cpt']=='friends' && in_array($result['ruserid'], $this->authIdentity->friends)) ||($result['cpt']=='fof' && ((count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends))>=1) || in_array($result['ruserid'], $this->authIdentity->friends))) || ($result['cpt']=='specific' && in_array($this->authIdentity->userid, unserialize($result['csu'])) )) && !in_array($this->authIdentity->userid, $ignorelist) && !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) ) || $this->authIdentity->userid==$result['suserid'] || $this->authIdentity->userid==$result['ruserid'] ){
							

					//if(($result['video']=='friends' && !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && in_array($result['ruserid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid==$result['ruserid'] || $this->authIdentity->userid==$result['susersid']){

						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$videoid,'title'=>'commented on','contenttype'=>'video','contenturl'=>'video.php?videoid='.$videoid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'video_'.$videoid);

						$this->_db->insert('video_comments', $commentdata);

						$commentid=$this->_db->lastInsertId('video_comments');
						$this->_db->insert('commentactivity',array('commentid'=>$commentid,'objid'=>$videoid,'type'=>'video','userid'=>$this->authIdentity->userid,'comment'=>(trim($text))));
						
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
								$query.=' ('.$this->_db->quote(array($user,'video/'.$videoid,$notificationtext,$userpic)).'),';
							}
							$query=substr($query, 0,-1);
							$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
							$this->_db->query($query);
						}

						$update_staturedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));

						$this->update($update_staturedata, "videoid='$videoid'");

	

					}

				}

				else if($result['rtype']=='page'){

					$votes=unserialize($result['page_vote']);

					$bannedusers=unserialize($result['bannedusers']);

					$admins=unserialize($result['admins']);

					if((($result['cpt']=='public' ||($result['cpt']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['ruserid'] || $this->authIdentity->userid=$result['suserid']   )){

						$this->_db->insert('video_comments', $commentdata);

						$commentid=$this->_db->lastInsertId('video_comments');
						$this->_db->insert('commentactivity',array('commentid'=>$commentid,'objid'=>$videoid,'type'=>'video','userid'=>$this->authIdentity->userid,'comment'=>(trim($text))));
						
						$notify_data=array('userid'=>$result['ruserid'],'contenturl'=>'video/'.$videoid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> commented on your video','userpic'=>$this->authIdentity->propic);
						$this->_db->insert('notifications', $notify_data);
						
						

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
			return json_encode(array('status'=>'success','userid'=>$this->authIdentity->userid));
		}
	}
	
	
	public function deleteVideoComment($commentid){
	
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('video_comments')->joinLeft($this->_name, 'video_comments.videoid=video.videoid','ruserid as video_userid')->where('commentid=?',$commentid);
			$result=$this->_db->fetchRow($sql);
			$userid=$this->authIdentity->userid;
			if($result['userid']==$userid || $result['video_userid']==$userid){
				$this->_db->delete('video_comments',"commentid='$commentid'");
				$userid=$this->authIdentity->userid;
				$videoid=$result['videoid'];
				$activityModel=new Application_Model_Activity($this->_db);
				$activityModel->delete("userid='$userid' and contentid='$videoid' and contenttype='video' and title='commented on'");
				$update_data=array('commentcount'=>new Zend_Db_Expr('commentcount-1'));
				$this->update($update_data, array('videoid=?'=>$videoid));
			}
		}
	
	}
public function votevideo($videoid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('video',array('suserid','ruserid','vote','notifyusers','dontnotify'))->joinLeft('freniz', 'video.ruserid=freniz.userid',array('rusername'=>'username'))->where('videoid=?',$videoid);
			$result=$this->_db->fetchRow($sql);
			//$result=$result[0];
			if(!empty($result)){
				$vote=unserialize($result['vote']);
			if(!in_array($this->authIdentity->userid,$vote)){
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "videoid='$videoid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$videoid,'title'=>'voted on','contenttype'=>'video','contenturl'=>'video.php?videoid='.$videoid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'video_'.$videoid);
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
						$notificationtext.=" your video";
					}
					else
					{
						$notificationtext.=" <a href='".$result['userid']."'>".$result['username']."</a>'s video";
					}
					$query.=' ('.$this->_db->quote(array($user,'video/'.$videoid,$notificationtext,$userpic)).'),';
				}
				$query=substr($query, 0,-1);
				$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
				$this->_db->query($query);
			}
			}
			}
		}
	}
      public function unVotevideo($videoid)
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
public function videos($userid,$from){

		if(isset($this->authIdentity)){

			$sql=clone $this->sql;

			$sql=$sql->where('video.ruserid=? and video.accepted=\'yes\'',$userid)->limit($this->registry->limit,$from);

			
			$results=$this->_db->fetchAssoc($sql);
			$final_result=$this->processResults($results,false);
			if(count($results)==$this->registry->limit)
				$final_result['loadmore']=true;
			else
				$final_result['loadmore']=false;
			return $final_result;

		}

	}

	public function processResults($results,$withComment=true){

		$userid=$this->authIdentity->userid;

		$myfriends=$this->authIdentity->friends;

		$blocklist=$this->authIdentity->blocklistmerged;

		if($withComment){
		$videoids=array_keys($results);
		$comment_sql=$this->_db->select()->from('video_comments as s')
						->joinLeft('video_comments as t', 's.videoid=t.videoid and s.commentid < t.commentid','')
						->joinLeft('freniz', 'freniz.userid=s.userid',array('username','url as user_url'))
						->joinLeft('image', 'freniz.propic=image.imageid','image.url as commentpic_url')
						->where('s.videoid in (?)',$videoids)
						->group('s.commentid')->having('count(*) < ?',$this->registry->commentDefaultLimit);
		$comment_result=$this->_db->fetchAssoc($comment_sql);

		}

		$myfriends=$this->authIdentity->friends;

		foreach ($results as $videoid => $values){

			$friends=unserialize($values['rfriendlist']);

				

			$privacy=$values['pt'];

			$specific=unserialize($values['specificlist']);

			$hidden=unserialize($values['hiddenlist']);
			
			$ignorelist=unserialize($values['ciu']);
			$results[$videoid]['iscommentable']=false;

			if((($values['cpt']=='public' || ($values['cpt']=='friends' && in_array($values['ruserid'], $this->authIdentity->friends)) ||($values['cpt']=='fof' && ((count(array_intersect($friends, $this->authIdentity->friends))>=1) || in_array($values['ruserid'], $this->authIdentity->friends))) || ($values['cpt']=='specific' && in_array($this->authIdentity->userid, unserialize($values['csu'])) )) && !in_array($this->authIdentity->userid, $ignorelist) && !in_array($values['ruserid'], $this->authIdentity->blocklistmerged) ) || $this->authIdentity->userid==$values['suserid'] || $this->authIdentity->userid==$values['ruserid'] ){
				$results[$videoid]['iscommentable']=true;
			}

			if((($privacy=='public'||($privacy=='friends' && in_array($userid,$friends)) || ($privacy=='fof' && (count(array_intersect($friends, $myfriends))>=1) || in_array($userid, $friends)) || ($privacy=='specific' && in_array($userid, $specific))) && !in_array($values['suserid'], array_merge($blocklist,$hidden))) || $userid==$values['suserid']){

				if($withComment){
					$results[$videoid]['comments']=$this->filter_by_value($comment_result, 'videoid', $videoid);
					
					if($results[$videoid]['commentcount']>count($results[$videoid]['comments'])){
						$results[$videoid]['loadprevcomments']=true;
					}
					else
						$results[$videoid]['loadprevcomments']=false;
					
				}

			}

			else{

				unset($results[videoid]);

			}

		}
		if($withComment){

			$sql=$this->_db->select()->from('commentactivity','max(id) as maxid');
			$maxid=$this->_db->fetchRow($sql);
			$final_results['maxcommentid']=$maxid['maxid'];
			
		}
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
	public function getComments($videoid,$from){
		//if(isset($this->authIdentity)){
		$sql=$this->_db->select()->from('video_comments')->joinLeft('freniz', 'video_comments.userid=freniz.userid',array('username','url'))
		->joinLeft('image', 'image.imageid=freniz.propic','url as imageurl')
		->where('videoid=?',$videoid)->order('commentid desc')->limit($this->registry->commentlimit,$from);
		$result=$this->_db->fetchAssoc($sql);
		return $result;
	
		//}
	}
	public function tobeReviewed($from){
		//$sql=$this->_db->select()->from('video')->joinLeft('freniz','freniz.userid=video.suserid',array('username','url'))->joinLeft('image','image.imageid=freniz.propic','url as user_imageurl')->where('status.accepted=\'no\' and status.ruserid=?',$this->authIdentity->userid)->order('date desc')->limit($this->registry->limit,$from);
		$sql= clone $this->sql;
		$sql=$sql->where('video.accepted=\'not\' and video.ruserid=?',$this->authIdentity->userid)->order('date desc')->limit($this->registry->limit,$from);
		$results=$this->_db->fetchAssoc($sql);
		if(count($results)==$this->registry->limit)
			$final_results['loadmore']=true;
		else $final_results['loadmore']=false;
		$final_results['results']=$results;
		return $final_results;
	}
	public function reviewVideos($ids=null,$accept=true){
	if(!empty($ids)){
    		$where['statusid in (?)']=$ids;
    		$sql=$this->_db->select()->from('video',array('videoid','suserid','ruserid','date'))->where('videoid in (?)',$ids)->where('ruserid=? and accepted=\'not\'',$this->authIdentity->userid);
    		$posts=$this->_db->fetchAssoc($sql);
    	}
    	if($accept){
    		$this->update(array('accepted'=>'yes'),$where);
    		foreach($posts as $id=>$values){
    			$this->_db->insert('activity',array('userid'=>$values['suserid'],'ruserid'=>$values['ruserid'],'contentid'=>$id,'title'=>'post a video on','contenttype'=>'video','date'=>$values['date'],'alternate_contentid'=>'video_'.$id,'contenturl'=>'video.php?videoid='.$id));
    		}
    	}else
			$this->delete($where);
	
	}
	
	
	
}
