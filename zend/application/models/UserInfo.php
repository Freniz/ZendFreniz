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
		$this->auth=Zend_Auth::getInstance();

		if($this->auth->hasIdentity()){

			$this->authIdentity=$this->auth->getIdentity();
						
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				

		}
	}
	public function initUserSession($id)
	{
		$sql=$this->_db->select()->from('freniz',array('freniz_userid'=>'userid','freniz_url'=>'url','freniz_adminpages'=>'adminpages','username','type'))
					->joinLeft($this->_name, 'freniz.userid=user_info.userid')
					->joinLeft('image as propic', 'propic.imageid=user_info.propic',array('propic_url'=>'url'))
					->joinLeft('friends_vote', 'freniz.userid=friends_vote.userid')
					->joinLeft('places', 'places.id=user_info.hometown',array('name as ht_name','id as ht_id','vote as ht_vote','placepic as ht_pic','infoid as ht_info'))
					->joinLeft('image as htpic', 'htpic.imageid=places.placepic','url as htpic_url')
					->joinLeft('apps', 'freniz.userid=apps.userid','apps.diary')
					->joinLeft('places as ccplaces', 'ccplaces.id=user_info.currentcity',array('name as cc_name','id as cc_id','vote as cc_vote','placepic as cc_pic','infoid as cc_info'))
					->joinLeft('image as ccpic', 'ccpic.imageid=ccplaces.placepic','url as ccpic_url')
					->where('freniz.userid=?',$id);
		$results=$this->_db->fetchRow($sql,null,Zend_Db::FETCH_ASSOC);
		$results['userid']=$results['freniz_userid'];
		unset($results['freniz_userid']);
		$results['url']=$results['freniz_url'];
		unset($results['freniz_url']);
		$results['adminpages']=unserialize($results['freniz_adminpages']);
		unset($results['freniz_adminpages']);
		if($results['type']=='user'){
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
		$results['blocklistmerged']=array_merge($results['blocklist'],$results['blockedby']);
		$results['reviews']=unserialize($results['reviews']);
		$results['reqfrmme']=unserialize($results['reqfrmme']);
		$results['personalinfo']=unserialize($results['personalinfo']);
		$results['friends']=unserialize($results['friendlist']);
		unset($results['friendlist']);
		$results['incomingrequest']=unserialize($results['incomingrequest']);
		$results['sentrequest']=unserialize($results['sentrequest']);
		$results['vote']=unserialize($results['vote']);
		$results['voted']=unserialize($results['voted']);
		$results['diary']=unserialize($results['diary']);
		$privacyModel=new Application_Model_Privacy($this->_db);
		$privacy=$privacyModel->getUserPrivacy($id);
		$results['privacy']=$privacy;
		}
		$adminpages=array_merge(array($id),$results['adminpages']);
		$sql1=$this->_db->select()->from('freniz',array('userid','username','url'))->joinLeft('image', 'freniz.propic=image.imageid','url as propic_url')->where('freniz.userid in (?)',$adminpages);
		$results['adminpages_details']=$this->_db->fetchAssoc($sql1);
		$results['latime']=time();
		$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$id));
		
		return (object) $results;
		
	}
	public function createEntry($data){
		return $this->insert($data);
	}
	

	public function updateBasicInfo($fname,$lname,$bdy,$bdm,$bdd,$sex,$religious,$rstatus,$skills){

		if(isset($this->authIdentity) && $this->authIdentity->type=='user'){
			$dob=$bdy."-".$bdm."-".$bdd;
			$update_basic=array('fname'=>$fname,'lname'=>$lname,'sex'=>$sex,'rstatus'=>$rstatus,'religion'=>$religious,'dob'=>$dob,'skills'=>$skills);
			$this->authIdentity->skills=$skills;
			$skills=str_ireplace(',', ' ', $skills);
			$search_data=array('username'=>$fname.' '.$lname,'skills'=>$skills);
			$userid=$this->authIdentity->userid;
			$this->update($update_basic,"userid='$userid'");
			$this->_db->update('searchtable',$search_data,array('userid=?'=>$userid));
			
			$this->authIdentity->username=$fname.' '.$lname;
			$this->authIdentity->fname=$fname;
			$this->authIdentity->lname=$lname;
			$this->authIdentity->sex=$sex;
			$this->authIdentity->rstatus=$rstatus;
			$this->authIdentity->religion=$religious;
			$this->authIdentity->dob=$bdy.'-'.$bdm.'-'.$bdd;
			
			$activityModel=new Application_Model_Activity($this->_db);

			$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'basic info','contenturl=?'=>'basicinfo.php'));

			$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update basic info','contenttype'=>'basic info','contenturl'=>'basicinfo.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'basicinfo');

			$activityModel->insert($activity_data);
			$tags=explode(',', $skills);
			foreach($tags as $val){
				$val=trim($val);
				$query='insert into skills(skill) values(\''.$val.'\') on duplicate key update skill=\''.$val.'\'';
		    	$this->_db->query($query);
			}
			
			return true;

		}
		return false;

	

	}

	

	public  function updatepersonalInfo($body,$look,$smoke,$drink,$pets,$passion,$ethnicity,$humor,$sexual){

	

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
			$this->authIdentity->personalinfo=$personalinfo;
			$this->update(array('personalinfo'=>serialize($personalinfo)),array('userid=?'=>$userid));

			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'personal info','contenturl=?'=>'personalinfo.php'));
			$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update personal info','contenttype'=>'personal info','contenturl'=>'personalinfo.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'basicinfo');
			$activityModel->insert($activity_data);
			return true;
		}
		return false;

	

	}

	

	public function updateMood($mood,$description){

		if(isset($this->authIdentity)){

	
			$userid=$this->authIdentity->userid;

			
			$this->update(array('mood'=>$mood.','.$description),array('userid=?'=>$userid));
			$this->authIdentity->mood=$mood.','.$description;
	

			$activityModel=new Application_Model_Activity($this->_db);

	

			$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'mood'));

	

			$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update mood','contenttype'=>'mood','contenturl'=>'mood.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'mood');

	

			$activityModel->insert($activity_data);
			
			return array('id'=>$userid,"time"=>date('c'),'mood'=>$mood,'description'=>$description,"status"=>'success');
			
		}

	}

	
	
	
	public function updatecity($city,$type){
		if(isset($this->authIdentity)){
			$this->update(array($type=>$city), array('userid=?'=>$this->authIdentity->userid));
			$userid=$this->authIdentity->userid;
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->delete(array('userid=?'=>$userid,'ruserid=?'=>$userid,'contenttype=?'=>'city'));
			$activity_data=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'update city','contenttype'=>'city','contenturl'=>'city.php','date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'city');
			$activityModel->insert($activity_data);
			return true;
		}
		return false;
	}

	

	
	
	
	
	
	
	
	public function getFavourites($pageids){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('pages')->joinLeft('image','image.imageid=pages.pagepic','url as pagepic_url')->where('pageid in(?)',$pageids);
			return $this->_db->fetchAssoc($sql);
			
		}
	}
	
	
	
	
	public function UpdateToFavorites($pageid,$category,$action){

	
$category=strtolower($category);
		if(isset($this->authIdentity)){
		$pages=$this->authIdentity->$category;
		
			if($action=='add'){
				
				$pages=array_unique(array_merge($pages,$pageid));
				
				
				$this->UpdateFavorites($pages, $pageid,$category, $action);

			//return $this->authIdentity->$category;

				$this->authIdentity->$category=$pages;

	

			

			}

	

			elseif ($action=='remove') {
				$pages=array_diff($pages,$pageid);
				
	

				$this->UpdateFavorites($pages, $pageid,$category, $action);

	

				$this->authIdentity->$category=$pages;
					
	

			}

			elseif ($action=='update'){
				$this->UpdateFavorites($pageid, $pageid,$category, $action);
				
				
				
				$this->authIdentity->$category=$pageid;
				
			}	
			return true;

		}

	return false;

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

				$notify_data=array('userid'=>$pageid,'contenturl'=>$userid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> voted on your leaf','userpic'=>$this->authIdentity->propic);
				$this->_db->insert('notifications', $notify_data);
				

	

			}

	

	

			$this->update(array($category=>serialize($pages)),array('userid=?'=>$userid));

	
			$sql=$this->_db->select()->from('pages')->where('pageid=?',$pageid);
			$result=$this->_db->fetchRow($sql);

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
		
			$result=$this->_db->fetchRow($sql);
			$votes=unserialize($result['vote']);
				
		if(!in_array($this->authIdentity->userid, $votes))

			{

				array_push($votes, $this->authIdentity->userid);

					array_push($this->authIdentity->voted, $userid);
				$this->_db->update('friends_vote',array('vote'=>serialize($votes)),array('userid=?'=>$userid));
		       $suserid=$this->authIdentity->userid;

				$svoted=$this->authIdentity->voted;

				$this->_db->update('friends_vote',array('voted'=>serialize($svoted)),array('userid=?'=>$suserid));
				
				$activityModel=new Application_Model_Activity($this->_db);

				$suserid=$this->authIdentity->userid;

	

				$activity_data=array('userid'=>$suserid,'ruserid'=>$userid,'contentid'=>$userid,'title'=>'voted on','contenttype'=>'user','contenturl'=>'profile.php?userid='.$userid,'date'=>new Zend_Db_Expr('NOW()'),'alternate_contentid'=>'user_'.$userid);

	

				$activityModel->insert($activity_data);

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
						$notificationtext.=" your profile";
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

	

	

	

	

	public function userUnvote($userid){

		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('friends_vote')->where('userid=?',$userid);
			
			$result=$this->_db->fetchRow($sql);
				
			$votes=unserialize($result['vote']);

			if(in_array($this->authIdentity->userid, $votes))

			{
				$votes=array_diff($votes, array($this->authIdentity->userid));

				$svote=array_diff($this->authIdentity->vote, array($userid));
				$suserid=$this->authIdentity->userid;
				
					$this->_db->update('friends_vote',array('vote'=>serialize($votes)),array('userid=?'=>$userid));
					$this->_db->update('friends_vote',array('voted'=>serialize($svote)),array('userid=?'=>$suserid));
						
				$activityModel=new Application_Model_Activity($this->_db);

				$suserid=$this->authIdentity->userid;

	

				$activityModel->delete(array('userid=?'=>$suserid,'contentid=?'=>$userid,'contenttype=?'=>'user','title=?'=>'voted on'));

	

			}

	

		}

	}
	public function getskills($skills){
		$sql=$this->_db->select()->from('skills')->where('skill like (?)',$skills.'%');
		return $this->_db->fetchAssoc($sql);
	}
	public function totalviews(){
		$query='insert into admin(views) values(now()) on duplicate key update views=views+1';
		$this->_db->query($query);
		
	}
	public function changepassword($old,$new,$conf){
		if($new==$conf){
				$count=$this->_db->update('userstable',array("pass"=>$new),array('userid=?'=>$this->authIdentity->userid,'pass=?'=>$old));
				if($count>0)
				return json_encode(array("status"=>"Password changed"));
				else 
					return json_encode(array("status"=>"Current Password doesnot match"));
		
		}else {
			return json_encode(array("status"=>"new password doesnot match"));
		}
	}
	
}
