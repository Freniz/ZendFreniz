<?php

/**
 * UserInfo
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_UserInfo extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'user_info';
	protected $authIdentity;
	public function __construct($db){
		$this->_db=$db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}
	public function initUserSession($id)
	{
		$sql=$this->_db->select()->from('freniz')
					->joinLeft($this->_name, 'freniz.userid=user_info.userid')
					->joinLeft('friends_vote', 'freniz.userid=friends_vote.userid')
					->joinLeft('places', 'places.id=user_info.hometown','places.name as ht_name')
					->joinLeft('apps', 'freniz.userid=apps.userid','apps.diary')
					->joinLeft('places as ccplaces', 'ccplaces.id=user_info.currentcity','ccplaces.name as cc_name')
					->where('freniz.userid=?',$id);
		$results=$this->_db->fetchRow($sql,null,Zend_Db::FETCH_ASSOC);
		$results['adminpages']=unserialize($results['adminpages']);
		$results['school']=unserialize($results['school']);
		$results['college']=unserialize($results['college']);
		$results['language']=unserialize($results['language']);
		$results['employer']=unserialize($results['employer']);
		$results['musics']=unserialize($results['musics']);
		$results['books']=unserialize($results['books']);
		$results['celebrities']=unserialize($results['celebrities']);
		$results['movies']=unserialize($results['movies']);
		$results['games']=unserialize($results['games']);
		$results['other']=unserialize($results['other']);
		$results['pinnedpic']=unserialize($results['pinnedpic']);
		$results['sports']=unserialize($results['sports']);
		$results['playlist']=unserialize($results['playlist']);
		$results['blocklist']=unserialize($results['blocklist']);
		$results['blockedby']=unserialize($results['blockedby']);
		$results['blocklistmerged']=array_merge($results['blocklist'],$results['blockedlist']);
		$results['reviews']=unserialize($results['reviews']);
		$results['reqfrmme']=unserialize($results['reqfrmme']);
		$results['personalinfo']=unserialize($results['personalinfo']);
		$results['friends']=unserialize($results['friendlist']);
		//unset($results['friendlist']);
		$results['incomingrequest']=unserialize($results['incomingrequest']);
		$results['sentrequest']=unserialize($results['sentrequest']);
		$results['vote']=unserialize($results['vote']);
		$results['voted']=unserialize($results['voted']);
		$results['diary']=unserialize($results['diary']);
		
		$privacyModel=new Application_Model_Privacy($this->_db);
		$privacy=$privacyModel->getUserPrivacy($id);
		$results['privacy']=$privacy[$id];
		
		return (object) $results;
	}
	public function createEntry($data){
		return $this->insert($data);
	}
	
	public function updateBasicInfo($fname,$lname,$dob,$sex,$religious,$rstatus,$ccity,$htown){
		if(isset($this->authIdentity)){
		$update_basic=array('hometown'=>$htown,'currentcity'=>$ccity,'fname'=>$fname,'lname'=>$lname,'sex'=>$sex,'rstatus'=>$rstatus,'religion'=>$religious,'dob'=>$dob);
		$userid=$this->authIdentity->userid;
		$this->update($update_basic,"userid='$userid'");
		$activityModel=new Application_Model_Activity($this->_db);
		$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'basic info','contenturl=?'=>'basicinfo.php'));
		$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update basic info','contenttype'=>'basic info','contenturl'=>'basicinfo.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'basicinfo');
		$activityModel->insert($activity_data);
		}
	}
	public  function personalInfo($body,$look,$smoke,$drink,$pets,$passion,$ethnicity,$humor,$sexual){
		if(isset($this->authIdentity)){
		$result=$this->find($this->authIdentity->userid);
		$result=$result[0];
		$personalinfo=array();
			$personalinfo=unserialize($result['personalinfo']);
		if(isset($body)){
			$personalinfo['body']=$body;
		}
		if(isset($look)){
			$personalinfo['look']=$look;
		}
		if(isset($smoke)){
			$personalinfo['smoke']=$smoke;
		}
		if(isset($drink)){
			$personalinfo['drink']=$drink;
		}
		if(isset($pets)){
			$personalinfo['pets']=$pets;
		}
		if(isset($passion)){
			$personalinfo['passion']=$passion;
		}
		if(isset($ethnicity)){
			$personalinfo['ethnicity']=$ethnicity;
		}
		if(isset($humor)){
			$personalinfo['humor']=$humor;
		}
		if(isset($sexual)){
			$personalinfo['sexual']=$sexual;
		}
		
		$userid=$this->authIdentity->userid;
		$this->update(array('personalinfo'=>serialize($personalinfo)),array('userid=?'=>$userid));
		$activityModel=new Application_Model_Activity($this->_db);
		$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'personal info','contenturl=?'=>'personalinfo.php'));
		$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update personal info','contenttype'=>'personal info','contenturl'=>'personalinfo.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'basicinfo');
		$activityModel->insert($activity_data);
		}
	}
	public function updateMood($mood){
		if(isset($this->authIdentity)){
		
		$userid=$this->authIdentity->userid;
		$this->update(array('mood'=>$mood),array('userid=?'=>$userid));
		$activityModel=new Application_Model_Activity($this->_db);
		$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'mood'));
		$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update mood','contenttype'=>'mood','contenturl'=>'mood.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'mood');
		$activityModel->insert($activity_data);
		}
	}
	
	public function UpdateToFavorites($pageid,$category,$action){
		if(isset($this->authIdentity)){
		
		$pages=$this->authIdentity->$category;
		if(!in_array($pageid, $pages) && $action=='add'){
			array_push ($pages, $pageid);
			$this->UpdateFavorites($pages, $pageid,$category, $action);
			$this->authIdentity->$category=$pages;
			
		}
		elseif (in_array($pageid, $pages) && $action=='remove') {
			$pages=array_diff($pages,array($pageid));
			$this->UpdateFavorites($pages, $pageid,$category, $action);
			$this->authIdentity->$category=$pages;
		}
		}
	}
	
	public function updateFavorites($pages,$pageid,$category,$action){
		if(isset($this->authIdentity)){
		
		$userid=$this->authIdentity->userid;
		if($category=='school' || $category=='college' || $category=='employer'){
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'education info','contenturl=?'=>'educationinfo.php'));
			$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update education info','contenttype'=>'education info','contenturl'=>'educationinfo.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'education info');
			$activityModel->insert($activity_data);
				 
		}
		elseif($category=='language'){
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'language info','contenturl=?'=>'languageinfo.php'));
			$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update education info','contenttype'=>'language info','contenturl'=>'languageinfo.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'language info');
			$activityModel->insert($activity_data);
			 
		}
		
		$this->update(array($category=>serialize($pages)),array('userid=?'=>$userid));
		$result=$this->_db->select()->from('pages')->where('pageid=?',$pageid);
			$votes=unserialize($result['vote']);
			if($action=='add'){
				array_push($votes,$userid);
				$activityModel=new Application_Model_Activity($this->_db);
				$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$pageid,'contenttype=?'=>'pages','contenturl=?'=>'pages.php'));
				$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'added a leaf','contenttype'=>'pages','contenturl'=>'pages.php?pageid='.$pageid,'date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'pages_'.$pageid);
				$activityModel->insert($activity_data);
			}
			elseif($action=='remove')
			
			$votes=array_diff ($votes, array($userid));
			$votes=array_unique($votes);
			$this->_db->update('pages',array('vote'=>serialize($votes)),array('pageid=?'=>$pageid));
		}	
	}
	
	public function userVote($userid){
		if(isset($this->authIdentity)){
		
		$sql=$this->_db->select()->from('friends_vote')->where('userid=?',$userid);
		return $sql;
		$result=$this->_db->fetchRow($sql);
		$votes=unserialize($result['vote']);
		return $votes;
			if(in_array($this->authIdentity->userid, $votes))
			{
				array_push($votes, $this->authIdentity->userid);
				array_push($this->authIdentity->voted, $userid);
				$votes_seri=serialize($votes);
				return $votes_seri;
				$voted_seri=serialize($this->authIdentity->voted);
				$this->_db->update('friends_vote',array('vote'=>serialize($votes)),array('userid=?'=>$userid));
				$suserid=$this->authIdentity->userid;
				$svoted=$this->authIdentity->voted;
				$this->_db->update('friends_vote',array('voted'=>serialize($svoted)),array('userid=?'=>$suserid));
				$activityModel=new Application_Model_Activity($this->_db);
				$suserid=$this->authIdentity->userid;
				$activity_data=array('userid'=>$suserid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'voted on','contenttype'=>'user','contenturl'=>'profile.php?userid='.$userid,'date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'user_'.$userid);
				$activityModel->insert($activity_data);
				
			}
	
		}
	}
	
	
	public function userUnvote($userid){
		if(isset($this->authIdentity)){
		
		$result=$this->_db->select()->from('friends_vote')->where('userid=?',$userid);
			$votes=unserialize($result['vote']);
			
			if(in_array($this->authIdentity->userid, $votes))
			{
			
				$votes=array_diff($votes, array($this->authIdentity->userid));
				$this->_db->update('friends_vote',array('vote'=>serialize($votes)),array('userid=?'=>$userid));
				$activityModel=new Application_Model_Activity($this->_db);
				$suserid=$this->authIdentity->userid;
				$activityModel->delete(array('userid=?'=>$suserid,'contentid=?'=>$userid,'contenttype=?'=>'user','title=?'=>'voted on'));
			}
		  }
		}
	
	
}
