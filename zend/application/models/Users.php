<?php

/**
 * Users
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Registry.php';
class Application_Model_Users extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'freniz';
	protected $authIdentity;
	public function __construct($db){
		$this->_db=$db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$this->authIdentity=$auth->getIdentity();
		}
	}
	public function checkUniqueUserid($userid,$email){
				$select = $this->_db->select()->from('freniz')->join('userstable', 'freniz.userid=userstable.userid')->where('freniz.userid=:userid or userstable.email=:email');
		$result=$this->_db->fetchOne($select,array('userid'=>$userid,'email'=>$email));
		if($result){
			return false;
		}
		return true;
	}
	
	
	
	
	public function CreateUserAccount($username,$password,$fname,$lname,$email,$sex,$dob){
        
     if($this->checkUniqueUserid($username,$email)){
     	$ip2c=new Application_Model_ip2c($this->_db);
     	$a=array();
     	$b=serialize($a);
     	$freniz=array('userid'=>$username,'type'=>'user','url'=>'profile.php?userid='.$username,'adminpages'=>$b,'username'=>$fname.' '.$lname,'createdipadd'=>$ip2c->getIpAdd());
     	$userstable=array('userid'=>$username,'pass'=>$password,'email'=>$email);
     	$user_info=array('userid'=>$username,'fname'=>$fname,'lname'=>$lname,'dob'=>$dob,'sex'=>$sex,'email'=>$email,'date'=>new Zend_Db_Expr('NOW()'),'musics'=>$b,'books'=>$b,'movies'=>$b,'games'=>$b,'celebrities'=>$b,'other'=>$b,'pinnedpic'=>$b,'sports'=>$b,'playlist'=>$b,'school'=>$b,'college'=>$b,'language'=>$b,'adminpages'=>$b,'employer'=>$b,'url'=>'profile.php?userid='.$username,'blocklist'=>$b,'blockedby'=>$b,'reviews'=>$b,'reqfrmme'=>$b,'style'=>'blue-world.css','groups'=>$b);
     	$apps=array('userid'=>$username,'slambook'=>$b,'diary'=>$b,'inivitation'=>$b);
     	$a1=array();
     	$b1=array('post','image','admire','pin','video');
     	foreach($b1 as $c1)
     		$a1[$c1]=array();
     	$d1= serialize($a1);
     	$friends_vote=array('userid'=>$username,'friendlist'=>$b,'incomingrequest'=>$b,'sentrequest'=>$b,'vote'=>$b,'voted'=>$b);
     	$privacy=array('userid'=>$username,'postignore'=>$b,'testyignore'=>$b,'postspeci'=>$b,'testyspeci'=>$b,'blogspeci'=>$b,'posthidden'=>$b,'testyhidden'=>$b,'bloghidden'=>$b,'autoacceptusers'=>$d1,'blockactivityusers'=>$d1,'hidestreams'=>$b,'hideusersstream'=>$b);
     	$notification=array('userid'=>$username,'notifications'=>'a:0:{}');
     	
     	
     	$db=$this->_db;
     	$this->insert($freniz);
     	$userstableModel=new Application_Model_Userstable($db);
     	$userstableModel->insert($userstable);
     	$user_infoModel=new Application_Model_UserInfo($db);
     	$user_infoModel->insert($user_info);
     	$appsModel=new Application_Model_Apps($db);
     	$appsModel->insert($apps);
     	$friends_voteModel=new Application_Model_Friendsvote($db);
     	$friends_voteModel->insert($friends_vote);
     	$privacyModel=new Application_Model_Privacy($db);
     	$privacyModel->insert($privacy);
     	$albumModel=new Application_Model_Album($db);
     	$profilepicalbum=$this->createAlbumData($username, 'Profile Pics');
     	$propicid=$albumModel->insert($profilepicalbum);
     	$secpicalbum=$this->createAlbumData($username, 'Secondary Pics');
     	$secpicid=$albumModel->insert($secpicalbum);
     	$chartpicalbum=$this->createAlbumData($username,'Chart Pics',true);
     	$chartpicid=$albumModel->insert($chartpicalbum);
     	$notificationModel=new Application_Model_Notification($db);
     	$notificationModel->insert($notification);
     	$albumids=array('propicalbum'=>$propicid,'secondarypicalbum'=>$secpicalbum);
     	$where="userid='$username'";
     	$user_infoModel->update($albumids,$where);
     	return array('status'=>true,'message'=>'');
     }
     else {
     	return array('status'=>false,'message'=>"Name already taken. Please choose another one.") ;
     }
     	
     	
     	
          
	}    
    private function createAlbumData($userid,$albumname,$canupload=false){
    	$album = array('userid'=>$userid,'name'=>$albumname,'date'=>new Zend_Db_Expr('Now()'),'specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','ignorelist'=>'a:0:{}');
    	if($canupload)
    	{
    		$album=array_merge($album,array('canupload'=>'friends'));
    	}
    	return $album;
    }
	
    public function getNotifiation(){
    	if(isset($this->authIdentity)){
    		$userid=$this->authIdentity->userid;
    		$sql=$this->_db->select()->from('notification',array('userid','notifications'))
    						->where("userid='$userid'");
    		$result=$this->_db->fetchRow($sql);
    		$showcontent=false;
    			$notifications=unserialize($result['notifications']);
    			$ordered_notifications;
    			foreach ($notifications as $key => &$entry) {
    				$ordered_notifications[$entry['time']][$key] = $entry;
    			}
    			krsort($ordered_notifications);
    			$notifications=array_values($ordered_notifications);
    			$ordered_notifications=array();
    			foreach ($notifications as $key => &$entry) {
    				foreach($entry as $key1=>&$entry1)
    					$ordered_notifications[$entry1['read']][$key1] = $entry1;
    			}
    			ksort($ordered_notifications);
    			$ordered_notifications=  array_values($ordered_notifications);
    			foreach ($ordered_notifications as $notification){
    				foreach($notification as $key => $value){
    					
    		      // <userid><?php echo $_SESSION['userid']; </userid>
    		       //<contenturl><?php echo $key; </contenturl>
    		       //<text><?php echo $value['notification']; </text>
    		       //<read><?php echo $value["read"]; </read>
    		      //<date><?php echo date(DATE_RSS,$value['time']); </date>
    		       return $value["notification"];
    		   //</notification>
    		       
    		    }
    		    }
    		
    	}
    }
    public function getNotificationCount(){
    	if (isset($this->authIdentity)){
    		$userid=$this->authIdentity->userid;
    		$sql=$this->_db->select()->from('notification','notifications')->where("userid='$userid'");
    		$result=$this->_db->fetchRow($sql);
    		$notifi=unserialize($result['notifications']);
    		foreach ($notifi as $value)
    		
    		return $value['read'];
    		return array("count"=>$result['count']);
    		
    	}
    }
    
    
    public function getUserDetails($userid,$myauthIdentity=null){
    	$sql=$this->_db->select()->from('freniz',array('userid','user_url'=>'url','profile_type'=>'type','username'))
    	->where('freniz.userid=?',$userid);
    	$result=$this->_db->fetchRow($sql);
    	if($result){
    		if(isset($myauthIdentity))
    			$myfriends=$myauthIdentity->friends;
    		else $myfriends=array();
    
    		if($result['profile_type']=='user'){
    			$sql=$this->_db->select()->from('user_info', array('dob','sex','school','college','email','hometown','currentcity','language','rstatus','employer','religion','myphilosophy','musics','books','movies','games','celebrities','other','propic','pinnedpic','sports','playlist','mood','secondarypic1','secondarypic2','personalinfo','style','date'))
    			->joinLeft('friends_vote','user_info.userid=friends_vote.userid',array('friends'=>'friendlist','user_vote'=>'vote'))
    			->joinLeft('privacy','user_info.userid=privacy.userid',array('privacy_contactdetails'=>'contactdetails','privacy_religion'=>'religion','privacy_dob'=>'dob','privacy_aboutme'=>'aboutme','privacy_relationship'=>'relationship','privacy_livingin'=>'livingin','privacy_hometown'=>'hometown','privacy_languages'=>'languages','privacy_education'=>'education','privacy_occupation'=>'occupation','privacy_friendlist'=>'friendlist','privacy_status'=>'status','privacy_fav'=>'fav','privacy_message'=>'message','privacy_request'=>'request','privacy_invite'=>'invite','privacy_post'=>'post','privacy_postignore'=>'postignore','privacy_testy'=>'testy','privacy_testyignore'=>'testyignore','privacy_video'=>'video','privacy_videoignore'=>'videoignore'))
    			->joinLeft('image as propic', 'propic.imageid=user_info.propic',array('propic_url'=>'url'))
    			->joinLeft('image as secondarypic1','secondarypic1.imageid=user_info.secondarypic1',array('secondarypic1_url'=>'url'))
    			->joinLeft('image as secondarypic2','secondarypic2.imageid=user_info.secondarypic2',array('secondarypic2_url'=>'url'))
    			->joinLeft('places as hometown', 'hometown.id=user_info.hometown',array('hometown_name'=>'name'))
    			->joinLeft('places as currentcity', 'currentcity.id=user_info.currentcity',array('currentcity_name'=>'name'))
    			->where('user_info.userid=?',$result['userid']);
    			$result1=$this->_db->fetchRow($sql);
    			//return $result1;
    			$friends=unserialize($result1['friends']);
    			if(isset($myauthIdentity))
    				$myfriends=$myauthIdentity->friends;
    			else $myfriends=array();
    			$mutual_friends=array_intersect($myfriends, $friends);
    			if(in_array($result['userid'],$myfriends))
    				$relationtype='friends';
    			elseif (count($mutual_friends))
    			$relationtype='fof';
    			else $relationtype='public';
    			if($result1['privacy_religion']=='public' || ($result1['privacy_religion']=='friends' && $relationtype=='friends') || ($result1['privacy_religion']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['religion']=$result1['religion'];
    			if($result1['privacy_dob']=='public' || ($result1['privacy_dob']=='friends' && $relationtype=='friends') || ($result1['privacy_dob']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['dob']=$result1['dob'];
    			if($result1['privacy_education']=='public' || ($result1['privacy_education']=='friends' && $relationtype=='friends') || ($result1['privacy_education']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))){
    				$result['school']=unserialize($result1['school']);
    				$result['college']=unserialize($result1['college']);
    			}
    			if($result1['privacy_occupation']=='public' || ($result1['privacy_occupation']=='friends' && $relationtype=='friends') || ($result1['privacy_occupation']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['employer']=unserialize($result1['employer']);
    			if($result1['privacy_contactdetails']=='public' || ($result1['privacy_contactdetails']=='friends' && $relationtype=='friends') || ($result1['privacy_contactdetails']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['email']=$result1['email'];
    			if($result1['privacy_hometown']=='public' || ($result1['privacy_hometown']=='friends' && $relationtype=='friends') || ($result1['privacy_hometown']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))){
    				$result['hometown']=$result1['hometown'];
    				$result['hometown_name']=$result1['hometown_name'];
    			}
    			if($result1['privacy_livingin']=='public' || ($result1['privacy_livingin']=='friends' && $relationtype=='friends') || ($result1['privacy_livingin']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))){
    				$result['currentcity']=$result1['currentcity'];
    				$result['currentcity_name']=$result1['currentcity_name'];
    			}
    			if($result1['privacy_religion']=='public' || ($result1['privacy_religion']=='friends' && $relationtype=='friends') || ($result1['privacy_religion']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['religion']=$result1['religion'];
    			if($result1['privacy_languages']=='public' || ($result1['privacy_languages']=='friends' && $relationtype=='friends') || ($result1['privacy_languages']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['laguage']=$result1['language'];
    			if($result1['privacy_relationship']=='public' || ($result1['privacy_relationship']=='friends' && $relationtype=='friends') || ($result1['privacy_relationship']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['rstatus']=$result1['rstatus'];
    			if($result1['privacy_aboutme']=='public' || ($result1['privacy_aboutme']=='friends' && $relationtype=='friends') || ($result1['privacy_aboutme']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['religion']=$result1['religion'];
    			if($result1['privacy_fav']=='public' || ($result1['privacy_fav']=='friends' && $relationtype=='friends') || ($result1['privacy_fav']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))){
    				$result['musics']=unserialize($result1['musics']);
    				$result['books']=unserialize($result1['books']);
    				$result['movies']=unserialize($result1['movies']);
    				$result['games']=unserialize($result1['games']);
    				$result['sports']=unserialize($result1['sports']);
    				$result['celebrities']=unserialize($result1['celebrities']);
    				$result['other']=unserialize($result1['other']);
    			}
    			if($result1['privacy_status']=='public' || ($result1['privacy_status']=='friends' && $relationtype=='friends') || ($result1['privacy_status']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))){
    				$stature_sql=$this->_db->select()->from('stature','stature')->where("userid='$userid'")->order('date desc')->limit(1);
    				$stature=$this->_db->fetchRow($stature_sql);
    				if($stature)
    					$result['stature']=$stature['stature'];
    			}
    			if($result1['privacy_friendlist']=='public' || ($result1['privacy_friendlist']=='friends' && $relationtype=='friends') || ($result1['privacy_friendlist']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))
    				$result['friends']=unserialize($result1['friends']);
    			$result['propic_id']=$result1['propic'];
    			$result['propic_url']=$result1['propic_url'];
    			$result['secondarypic1_id']=$result1['secondarypic1'];
    			$result['secondarypic2_id']=$result1['secondarypic2'];
    			$result['secondarypic1_url']=$result1['secondarypic1_url'];
    			$result['secondarypic2_url']=$result1['secondarypic2_url'];
    			$result['style']=$result1['style'];
    			$result['sex']=$result1['sex'];
    			 
    			$result['mutual_friends']=$mutual_friends;
    			$result['vote']=unserialize($result1['user_vote']);
    			$friends_profile_sql=$this->_db->select()->from('user_info',array('userid','fname','lname','propic'))
    			->joinLeft('friends_vote', 'friends_vote.userid=user_info.userid',array('friendlist','vote'))->joinLeft('image', 'image.imageid=user_info.propic',array('imageurl'=>'url'));
    			if(!empty($result['friends'])){
    				$friends_profile_sql=$friends_profile_sql->where('user_info.userid in (?)',$result['friends']);
    				$result['friends_profiles']=$this->_db->fetchAssoc($friends_profile_sql);
    			}
    			elseif (!empty($result['mutual_friends'])){
    				$friends_profile_sql=$friends_profile_sql->where('user_info.userid in (?)',$result['mutual_friends']);
    				$result['friends_profiles']=$this->_db->fetchAssoc($friends_profile_sql);
    			}
    			 
    			$pages=array_unique(array_merge($result['school'],$result['college'],$result['musics'],$result['books'],$result['movies'],$result['celebrities'],$result['games'],$result['sports'],$result['other']));
    			if(count($pages)>0){
    				$fav_sql=$this->_db->select()->from('pages',array('pagename','pagepic','page_url'=>'url'))->joinLeft('image','image.imageid=pages.pagepic',array('imageurl'=>'url'))->where('pageid in (?)',$pages);
    				$fav_result=$this->_db->fetchAssoc($fav_sql);
    				$result['fav_pages']=$fav_result;
    			}
    			else
    				$result['fav_pages']=array();
    			$result['privacy_fav']=$result1['privacy_fav'];
    			$result['post']=$result1['privacy_post'];
    			$result['postignore']=unserialize($result1['privacy_postignore']);
    			$result['video']=$result1['privacy_video'];
    			$result['videoignore']=unserialize($result1['privacy_videoignore']);
    			if($result1['privacy_request']=='public' ||($result1['privacy_request']=='friends' && $relationtype['friends']) || ($result1['privacy_request']=='fof' &&($relationtype=='fof'||$relationtype=='friends')))
    				$result['privacy_request']=true;
    			else $result['privacy_request']=false;
    			if($result1['privacy_message']=='public' ||($result1['privacy_message']=='friends' && $relationtype['friends']) || ($result1['privacy_message']=='fof' &&($relationtype=='fof'||$relationtype=='friends')))
    				$result['privacy_message']=true;
    			else $result['privacy_message']=false;
    			return $result;
    		}
    		else {
    			$sql=$this->_db->select()->from('pages', array('pagepic','page_vote'=>'vote','website','views','page_type'=>'type','category','subcategory','bannedusers','canpost','bids','place'))
    			->joinLeft('pages_info','pages_info.pageid=pages.pageid',array('page_info'=>'info','page_tabs'=>'tabs','page_songurl'=>'songurl','page_ratings'=>'ratings'))
    			->joinLeft('image', 'image.imageid=pages.pagepic',array('page_pageicurl'=>'url'))
    			->where('pages.pageid=?',$result['userid']);
    			$result1=$this->_db->fetchRow($sql);
    			if(isset($result1['page_info']))
    				$result1['page_info']=unserialize($result1['page_info']);
    			else $result1['page_info']=array();
    			if(isset($result1['page_tabs']))
    				$result1['page_tabs']=unserialize($result1['page_tabs']);
    			else $result1['page_tabs']=array();
    			if(isset($result1['page_vote']))
    				$result1['page_vote']=unserialize($result1['page_vote']);
    			else $result1['page_vote']=array();
    			if(isset($result1['page_ratings']))
    				$result1['page_ratings']=unserialize($result1['page_ratings']);
    			else $result1['page_ratings']=array();
    			$result1['bannedusers']=unserialize($result1['bannedusers']);
    			if(isset($myauthIdentity)){
    				if(!in_array($myauthIdentity->userid, $result1['bannedusers'])){
    					$result=array_merge($result,$result1);
    
    				}
    				else
    				{
    					$result=array();
    				}
    			}
    			else $result=array_merge($result,$result1);
    			return $result;
    		}
    	}
    	return $result;
    }
    
    
}
