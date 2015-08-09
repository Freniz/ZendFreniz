<?php

/**
 * Activity
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Activity extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'activity';
	protected $authIdentity=null;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}
	public function getStreams($userids,$criteria=null,$maxId=0,$minId=0,$activitylist=null){
		if(isset($this->authIdentity) && $this->authIdentity->type=='user'){
			if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
			/*$as=new Zend_Session_Namespace('activity');
			if(!isset($as->activity_upper)){
				$as->activity_upper=0;
			}
			if(!isset($as->activity_lower)){
				$as->activity_lower=0;
			}
			if(!isset($as->activitylist)){
				$as->activitylist=array();
			}
			if(isset($reset) && $reset==1){
				$as->activity_upper=0;
				$as->activity_lower=0;
				$as->activitylist=array();
			}
			//return array();

			*/
			if(empty($userids)){
				$userids=array_merge($this->authIdentity->friends,array_keys($this->authIdentity->school),array_keys($this->authIdentity->college),array_keys($this->authIdentity->employer),
							$this->authIdentity->musics,$this->authIdentity->movies,$this->authIdentity->books,$this->authIdentity->celebrities,$this->authIdentity->games,$this->authIdentity->sports,$this->authIdentity->other);
			}
			else
				$userids=explode(',', $userids);
			$sub_sql=$this->_db->select()->from('activity as t',array('t.activityid','t.userid','t.ruserid','t.contentid','t.title','t.contenttype','t.contenturl','t.alternate_contentid'))->joinLeft('activity as r', "t.alternate_contentid=r.alternate_contentid and r.userid in (".$this->_db->quote($userids).") and t.activityid < r.activityid",'')->where("t.ruserid!='{$this->authIdentity->userid}' and t.userid in (?)",$userids);
			$sql=$this->_db->select()->from('activity as p',array('p.activityid','p.userid','p.ruserid','p.contentid','p.title','p.contenttype','p.contenturl','p.alternate_contentid','p.date'))->joinLeft(array('q'=>new Zend_Db_Expr('('.$sub_sql.')')), 'q.alternate_contentid=p.alternate_contentid and p.activityid < q.activityid','')
					->joinLeft('friends_vote', 'p.userid=friends_vote.userid','friendlist as sfriends')
					->where('q.activityid is null and p.ruserid!=?',$this->authIdentity->userid);
			if(isset($criteria)){
				if($criteria=='higher' && !empty($maxId)){
					$sql=$sql->where('p.activityid > ?',$maxId);
				}
				else if($criteria=='lower' && !empty($minId)){
					$sql=$sql->where('p.activityid < ?',$minId);
				}
				if(!empty($activitylist)){
					$activitylist=explode(',', $activitylist);
					$sql=$sql->where('p.alternate_contentid not in (?)',$activitylist);
				}
			}
			 $sql=$sql->where('p.userid in (?)',$userids);
			$sql=$sql->order('p.date desc')->limit(20);
			$results=$this->_db->fetchAssoc($sql);
			$activity_mapper=array();
			$posts=array();
			$activity_users=array();
			$activity_results=array();
			$userignore=array();
			$db_expr=new Zend_Db_Expr('date_sub(now(),interval 5 day)');
			/*if(count($results)>=1){
				$as->activity_lower=min(array_keys($results));
				$as->activity_higher=max(array_keys($results));
			}*/
			foreach($results as $activityid => $values){
				//array_push($as->activitylist, $values['alternate_contentid']);
				array_push($activity_users, $values['userid']);
				array_push($activity_users, $values['ruserid']);
				if($values['contenttype']=='post'){
					$activity_mapper['posts'][$activityid]=$values['contentid'];
				}
				else if($values['contenttype']=='stature'){
					$activity_mapper['statures'][$activityid]=$values['contentid'];
				}
				else if($values['contenttype']=='blog'){
					$activity_mapper['blogs'][$activityid]=$values['contentid'];
				}
				else if($values['contenttype']=='admire'){
					$activity_mapper['admires'][$activityid]=$values['contentid'];
				}
				else if($values['contenttype']=='image'){
					$activity_mapper['images'][$activityid]=$values['contentid'];
				}
				else if($values['contenttype']=='video'){
					$activity_mapper['videos'][$activityid]=$values['contentid'];
				}
				else if($values['contenttype']=='forum'){
					$activity_mapper['forums'][$activityid]=$values['contentid'];	
				}
				else if($values['contenttype']=='user'){
					if(!in_array($values['ruserid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $userignore) && !in_array($values['userid'], $userignore)){
						array_push($userignore, $values['userid']);
						array_push($userignore, $values['ruserid']);
						$activity_mapper['users'][$activityid]['recendusers']=array($values['ruserid']);
						if($values['contentid'] > 0){
							
							$suserfriends=unserialize($values['sfriends']);
							$activity_results['user'][$activityid]['recendusers']=array_slice($suserfriends, -($values['contentid']+1));
							foreach($activity_results['user'][$activityid]['recendusers'] as $user){
								array_push($activity_users, $user);
							}
						}
						else 
							$activity_results['user'][$activityid]['recendusers']=array();
					}
					else $activity_results['user'][$activityid]['recendusers']=array();
				}
				else if($values['contenttype']=='propic'){
					//$activity_mapper['propics'][$activityid]=$values['contentid'];
					$friends=array_diff($this->authIdentity->friends, array($values['userid']));
					if(!empty($friends)){
					$sql=$this->select()->from('activity',array('activityid','userid'))->where('userid in(?) and contenttype=\'propic\' and alternate_contentid=\'propic\'',$friends)->where('date > ?',$db_expr);
					$propic_results=$this->fetchAll($sql)->toArray();
					if(!empty($propic_results)){
						$activity_results['propic'][$activityid]['recendusers']=$propic_results;
						foreach ($propic_results as $user){
							array_push($activity_users, $user);
						}
					}
					else
						$activity_results['propic'][$activityid]['recendusers']=array();
					}
					else
						$activity_results['propic'][$activityid]['recendusers']=array();
				}
				else if($values['contenttype']=='basic info'){
					$friends=array_diff($this->authIdentity->friends, array($values['userid']));
					if(!empty($friends)){
					$sql=$this->select()->from('activity',array('activityid','userid'))->where('userid in(?) and contenttype=\'basic info\' and alternate_contentid=\'basicinfo\'',$friends)->where('date > ?',$db_expr);
					$propic_results=$this->fetchAll($sql)->toArray();
					if(!empty($propic_results)){
						$activity_results['basic info'][$activityid]['recendusers']=$propic_results;
						foreach ($propic_results as $user){
							array_push($activity_users, $user);
						}
					}
					else
						$activity_results['basic info'][$activityid]['recendusers']=array();
					}
					else
						$activity_results['basic info'][$activityid]['recendusers']=array();
				}
				else if($values['contenttype']=='personal info'){
					$friends=array_diff($this->authIdentity->friends, array($values['userid']));
					if(!empty($friends)){
					$sql=$this->select()->from('activity',array('activityid','userid'))->where('userid in(?) and contenttype=\'personal info\' and alternate_contentid=\'personalinfo\'',$friends)->where('date > ?',$db_expr);
					$propic_results=$this->fetchAll($sql)->toArray();
					if(!empty($propic_results)){
						$activity_results['personal info'][$activityid]['recendusers']=$propic_results;
						foreach ($propic_results as $user){
							array_push($activity_users, $user);
						}
					}
					else
						$activity_results['personal info'][$activityid]['recendusers']=array();
					}
					else
						$activity_results['personal info'][$activityid]['recendusers']=array();
				}
				else if($values['contenttype']=='mood'){
				$friends=array_diff($this->authIdentity->friends, array($values['userid']));
					if(!empty($friends)){
					$sql=$this->_db->select()->from('activity',array('activityid','userid'))->joinLeft('user_info', 'user_info.userid=activity.userid','mood')->where('activity.userid in(?) and contenttype=\'mood\' and alternate_contentid=\'mood\'',$friends)->where('activity.date > ?',$db_expr);
					$propic_results=$this->_db->fetchAll($sql);
					if(!empty($propic_results)){
						$activity_results['mood'][$activityid]['recendusers']=$propic_results;
						foreach ($propic_results as $user){
							array_push($activity_users, $user);
						}
					}
					else
						$activity_results['mood'][$activityid]['recendusers']=array();
					}
					else
						$activity_results['mood'][$activityid]['recendusers']=array();
				}
				else if($values['contenttype']=='education info'){
				$friends=array_diff($this->authIdentity->friends, array($values['userid']));
				if(!empty($friends)){	
				$sql=$this->select()->from('activity',array('activityid','userid'))->where('userid in(?) and contenttype=\'education info\' and alternate_contentid=\'edcationinfo\'',$friends)->where('date > ?',$db_expr);
					$propic_results=$this->fetchAll($sql)->toArray();
					if(!empty($propic_results)){
						$activity_results['education info'][$activityid]['recendusers']=$propic_results;
						foreach ($propic_results as $user){
							array_push($activity_users, $user);
						}
					}
					else
						$activity_results['education info'][$activityid]['recendusers']=array();
					}
					else
						$activity_results['education info'][$activityid]['recendusers']=array();
				
				}
				
				
			}
			if(!empty($activity_mapper['posts'])){
				$posts=new Application_Model_Post($this->_db);
				$activity_results['post']=$posts->getPosts($activity_mapper['posts']);
			}
			if(!empty($activity_mapper['statures'])){
				$posts=new Application_Model_Stature($this->_db);
				$activity_results['stature']=$posts->getStatures($activity_mapper['statures']);
			}
			if(!empty($activity_mapper['blogs'])){
				$blogs=new Application_Model_Blog($this->_db);
				$activity_results['blog']=$blogs->getBlogsArray($activity_mapper['blogs']);
			}
			if(!empty($activity_mapper['admires'])){
				$admires=new Application_Model_Admiration($this->_db);
				$activity_results['admire']=$admires->getAdmirationArray($activity_mapper['admires']);
			}
			if(!empty($activity_mapper['images'])){
				$images=new Application_Model_Images($this->_db);
				$activity_results['image']=$images->getArrayOfImages($activity_mapper['images']);
			}
			if(!empty($activity_mapper['videos'])){
				$videos=new Application_Model_Videos($this->_db);
				$activity_results['video']=$videos->getVideos($activity_mapper['videos']);
			}
			if(!empty($activity_mapper['forums'])){
				$forums=new Application_Model_forum($this->_db);
				$activity_results['forum']=$forums->getTopics($activity_mapper['forums']);
			}
			$activity_users=array_unique($activity_users);
			if(!empty($activity_users)){
			$sql=$this->_db->select()->from('freniz',array('userid','username','user_type'=>'type','user_url'=>'url'))->joinLeft('user_info', 'user_info.userid=freniz.userid',array('sex','mood'))->joinLeft('image as user_image','user_info.propic=user_image.imageid','url as user_imageurl')->joinLeft('pages', 'pages.pageid=freniz.userid','')->joinLeft('image as page_image','pages.pagepic=page_image.imageid','url as page_imageurl')->where('freniz.userid in (?)',$activity_users);
			$activity_users_minipro=$this->_db->fetchAssoc($sql);
			}
			else
				$activity_users_minipro=array();
			$final_results['activity']=$results;
			$final_results['results']=$activity_results;
			$final_results['users']=$activity_users_minipro;
			$sql=$this->_db->select()->from('commentactivity','max(id) as maxcomment');
			$result=$this->_db->fetchRow($sql);
			$final_results['maxcomment']=$result['maxcomment'];
			return $final_results;
		}
		
	}
	
	public function myStreams($ruserid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('activity',array('activityid','userid','ruserid','contentid','title','contenttype','contenturl','alternate_contentid','date'))
						->joinLeft('freniz', 'freniz.userid=activity.ruserid',array('ruserid'=>'userid','rusername'=>'username','ruserurl'=>'url'))
						->joinLeft('image','image.imageid=freniz.propic','image.url as ruserimageurl')
						->where('ruserid=?',$ruserid)->where('(contenttype=\'post\' and activity.title=\'posted on\') or (contenttype=\'video\' and activity.title=\'post a video on\') or (contenttype=\'image\' and activity.title=\'post image\' and activity.userid!=?) or (contenttype in (\'propic\',\'basicinfo\',\'personalinfo\',\'mood\',\'city\'))',$ruserid);
			$results=$this->_db->fetchAssoc($sql);
			$myStreamsMapper=array();
			foreach($results as $id => $values){
				switch($values['contenttype']){
					case 'post':
						$myStreamsMapper['posts'][$id]=$values['contentid'];
						break;
					case 'image':
						$myStreamsMapper['images'][$id]=$values['contentid'];
						break;
					case 'video':
						$myStreamsMapper['videos'][$id]=$values['contentid'];
						break;
				}
			}
			if(!empty($myStreamsMapper['posts'])){
				$posts=new Application_Model_Post($this->_db);
				$myStream_results['post']=$posts->getPosts($myStreamsMapper['posts']);
				
			}
			if(!empty($myStreamsMapper['images'])){
				$images=new Application_Model_Images($this->_db);
				$myStream_results['image']=$images->getArrayOfImages($myStreamsMapper['images']);
				
			}
			if(!empty($myStreamsMapper['videos'])){
				$videos=new Application_Model_Videos($this->_db);
				$myStream_results['video']=$videos->getVideos($myStreamsMapper['videos']);
			}
			$final_results['mystream']=$results;
			$final_results['results']=$myStream_results;
			$sql=$this->_db->select()->from('commentactivity','max(id) as maxcomment');
			$result=$this->_db->fetchRow($sql);
			$final_results['maxcomment']=$result['maxcomment'];
			return $final_results;
		}
	}
	
}
