<?php

/**
 * Users
 *  
 * @author abdulnizam
 * @version 
 */
require_once 'MySearch/MyLucene.php';
require_once 'Zend/Db/Table/Abstract.php';
require_once 'Zend/Registry.php';
class Application_Model_Users extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_indexPath='../application/usersearch/users/';
	protected $_name = 'freniz';
	protected $authIdentity,$registry;
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
	public function checkUniqueUserid($userid,$email){
		$select = $this->_db->select()->from('freniz')->join('userstable', 'freniz.userid=userstable.userid')->where('freniz.userid=:userid or userstable.email=:email');
		$result=$this->_db->fetchOne($select,array('userid'=>$userid,'email'=>$email));
		if($result){
			return false;
		}
		return true;
	}
	
	
	public function getProfileFromFb($fbprofile){
		$select=$this->_db->select()->from('userstable',array('userid','email','facebook'))->joinLeft('freniz','freniz.userid=userstable.userid','username')->joinLeft('image','freniz.propic=image.imageid','url as propic')->where('email=?',$fbprofile['email']);
		$result=$this->_db->fetchRow($select);
		if($result && empty($result['facebook'])){
			$this->_db->update('userstable',array('facebook'=>$fbprofile['id']),array('email=?'=>$fbprofile['email']));
		}
		return $result;
	}
	
	
	
public function CreateUserAccount($username=null,$password,$fname=null,$lname=null,$email,$sex=null,$dob,$type='user',$fbuser){
     if(!isset($username)){
     	$username='user_'.mt_rand()."_".mt_rand();
     }
     if(($this->checkUniqueUserid($username,$email)) && (strlen($username)>=6) ){
     	$ip2c=new Application_Model_ip2c($this->_db);
     	$a=array();
     	$b=serialize($a);
     	if($sex=='male')
     		$propic=1;
     	else $propic=2;
     	if($type=='user'){
     		$freniz=array('userid'=>$username,'type'=>$type,'url'=>$username,'adminpages'=>$b,'username'=>$fname.' '.$lname,'createdipadd'=>$ip2c->getIpAdd(),'propic'=>$propic);
     		$search=array('userid'=>$username,'username'=>$fname.' '.$lname,'type'=>'user');
     		$user_info=array('userid'=>$username,'fname'=>$fname,'lname'=>$lname,'dob'=>$dob,'sex'=>$sex,'email'=>$email,'date'=>new Zend_Db_Expr('NOW()'),'musics'=>$b,'books'=>$b,'movies'=>$b,'games'=>$b,'celebrities'=>$b,'other'=>$b,'pinnedpic'=>$b,'sports'=>$b,'playlist'=>$b,'school'=>$b,'college'=>$b,'language'=>$b,'adminpages'=>$b,'employer'=>$b,'url'=>$username,'blocklist'=>$b,'blockedby'=>$b,'reviews'=>$b,'reqfrmme'=>$b,'style'=>'blue-world.css','groups'=>$b,'propic'=>$propic);
     		$apps=array('userid'=>$username,'slambook'=>$b,'diary'=>$b,'inivitation'=>$b);
     		$a1=array();
     		$b1=array('post','image','admire','pin','video');
     		foreach($b1 as $c1)
     			$a1[$c1]=array();
     		$d1= serialize($a1);
     		$friends_vote=array('userid'=>$username,'friendlist'=>$b,'incomingrequest'=>$b,'sentrequest'=>$b,'vote'=>$b,'voted'=>$b);
     		$privacy=array('userid'=>$username,'postignore'=>$b,'testyignore'=>$b,'postspeci'=>$b,'testyspeci'=>$b,'blogspeci'=>$b,'posthidden'=>$b,'testyhidden'=>$b,'bloghidden'=>$b,'autoacceptusers'=>$d1,'blockactivityusers'=>$d1,'hidestreams'=>$b,'hideusersstream'=>$b,'staturespeci'=>$b,'staturehidden'=>$b,'postspecificpeople'=>$b,'testyspecificpeople'=>$b,'videospecificpeople'=>$b,'staturespecificpeople'=>$b,'statureignore'=>$b,'albumignore'=>$b,'albumspecificpeople'=>$b,'albumspeci'=>$b,'albumhidden'=>$b,'messagespecificpeople'=>$b,'messageignore'=>$b);
     		
     	}
     	else
     		$freniz=array('userid'=>$username,'type'=>$type,'url'=>$username,'adminpages'=>$b,'username'=>'','createdipadd'=>$ip2c->getIpAdd(),'propic'=>$propic);
     	$userstable=array('userid'=>$username,'pass'=>$password,'email'=>$email);
     	if($fbuser){
     		$userstable['facebook']=$fbuser;
     	}
     	$notification=array('userid'=>$username,'notifications'=>'a:0:{}');
     	
     	
     	$db=$this->_db;
     	$this->insert($freniz);
     	$userstableModel=new Application_Model_Userstable($db);
     	$userstableModel->insert($userstable);
     	if($type=='user'){
     	$user_infoModel=new Application_Model_UserInfo($db);
     	$user_infoModel->insert($user_info);
     	$appsModel=new Application_Model_Apps($db);
     	$appsModel->insert($apps);
     	$friends_voteModel=new Application_Model_Friendsvote($db);
     	$friends_voteModel->insert($friends_vote);
     	$privacyModel=new Application_Model_Privacy($db);
     	$privacyModel->insert($privacy);
     	$albumModel=new Application_Model_Album($db);
     	$profilepicalbum=$this->createAlbumData($username, 'Profile photos');
     	$propicid=$albumModel->insert($profilepicalbum);
     	$secpicalbum=$this->createAlbumData($username, 'Wallpapers');
     	$secpicid=$albumModel->insert($secpicalbum);
     	$chartpicalbum=$this->createAlbumData($username,'Chart photos',true);
     	$chartpicid=$albumModel->insert($chartpicalbum);
     	$albumids=array('propicalbum'=>$propicid,'secondarypicalbum'=>$secpicid);
     	$where="userid='$username'";
     	$user_infoModel->update($albumids,$where);
     	$this->_db->insert('searchtable', $search);
     	}
     	$notificationModel=new Application_Model_Notification($db);
     	$notificationModel->insert($notification);
     	return array('status'=>"true",'message'=>'','un'=>$username,'pass'=>$password);
     }
     else {
     	return array('status'=>"false",'message'=>"Name already taken. Please choose another one.") ;
     }
     	
	}    
    public function createAlbumData($userid,$albumname,$canupload=false){
    	$album = array('userid'=>$userid,'name'=>$albumname,'date'=>new Zend_Db_Expr('Now()'),'specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','ignorelist'=>'a:0:{}','ciu'=>'a:0:{}','csu'=>'a:0:{}');
    	if($canupload)
    	{
    		$album=array_merge($album,array('canupload'=>'friends'));
    	}
    	return $album;
    }
    
    public function getUserDetails($userid,$myauthIdentity=null){
    	$suserid=$this->authIdentity->userid;
    	$sql=$this->_db->select()->from('freniz',array('userid','user_url'=>'url','profile_type'=>'type','username','propic as displaypic'))
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
				    	->joinLeft('album', 'album.userid=user_info.userid and album.name=\'Chart Pics\'','albumid as chartpic')
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
    			if(($result1['privacy_religion']=='public' || ($result1['privacy_religion']=='friends' && $relationtype=='friends') || ($result1['privacy_religion']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))) or $suserid==$userid)
    				$result['religion']=$result1['religion'];
    			if(($result1['privacy_dob']=='public' || ($result1['privacy_dob']=='friends' && $relationtype=='friends') || ($result1['privacy_dob']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid)
    				$result['dob']=$result1['dob'];
    			if(($result1['privacy_education']=='public' || ($result1['privacy_education']=='friends' && $relationtype=='friends') || ($result1['privacy_education']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid){
    				$school=unserialize($result1['school']);
    				$skul=array();
    				foreach ($school as $id=>$val){
    				array_push($skul, $id);	
    				}
    				$result['school']=$skul;
    				$college=unserialize($result1['college']);
    				$col=array();
    				foreach ($college as $id=>$val){
    					array_push($col, $id);
    				}
    				$result['college']=$col;
    			}
    			if(($result1['privacy_occupation']=='public' || ($result1['privacy_occupation']=='friends' && $relationtype=='friends') || ($result1['privacy_occupation']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid)
    				$result['employer']=unserialize($result1['employer']);
    			if(($result1['privacy_contactdetails']=='public' || ($result1['privacy_contactdetails']=='friends' && $relationtype=='friends') || ($result1['privacy_contactdetails']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid)
    				$result['email']=$result1['email'];
    			if(($suserid==$userid || $result1['privacy_hometown']=='public' || ($result1['privacy_hometown']=='friends' && $relationtype=='friends') || ($result1['privacy_hometown']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid){
    				$result['hometown']=$result1['hometown'];
    				$result['hometown_name']=$result1['hometown_name'];
    			}
    			if(($result1['privacy_livingin']=='public' || ($result1['privacy_livingin']=='friends' && $relationtype=='friends') || ($result1['privacy_livingin']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))) or $suserid==$userid){
    				$result['currentcity']=$result1['currentcity'];
    				$result['currentcity_name']=$result1['currentcity_name'];
    			}
    			if(($result1['privacy_religion']=='public' || ($result1['privacy_religion']=='friends' && $relationtype=='friends') || ($result1['privacy_religion']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid)
    				$result['religion']=$result1['religion'];
    			if(($result1['privacy_languages']=='public' || ($result1['privacy_languages']=='friends' && $relationtype=='friends') || ($result1['privacy_languages']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid)
    				$result['language']=$result1['language'];
    			if(($result1['privacy_relationship']=='public' || ($result1['privacy_relationship']=='friends' && $relationtype=='friends') || ($result1['privacy_relationship']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))) or $suserid==$userid)
    				$result['rstatus']=$result1['rstatus'];
    			if(($result1['privacy_aboutme']=='public' || ($result1['privacy_aboutme']=='friends' && $relationtype=='friends') || ($result1['privacy_aboutme']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))) or $suserid==$userid)
    				$result['religion']=$result1['religion'];
    			if(($result1['privacy_fav']=='public' || ($result1['privacy_fav']=='friends' && $relationtype=='friends') || ($result1['privacy_fav']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid){
    				$result['musics']=unserialize($result1['musics']);
    				$result['books']=unserialize($result1['books']);
    				$result['movies']=unserialize($result1['movies']);
    				$result['games']=unserialize($result1['games']);
    				$result['sports']=unserialize($result1['sports']);
    				$result['celebrities']=unserialize($result1['celebrities']);
    				$result['other']=unserialize($result1['other']);
    			}
    			if(($result1['privacy_fav']=='public' || ($result1['privacy_fav']=='friends' && $relationtype=='friends') || ($result1['privacy_fav']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid){
    				 $result['personalinfo']=unserialize($result1['personalinfo']);
    			}
    			if(($result1['privacy_status']=='public' || ($result1['privacy_status']=='friends' && $relationtype=='friends') || ($result1['privacy_status']=='fof' && ($relationtype=='fof' || $relationtype=='friends'))) or $suserid==$userid){
    				$stature_sql=$this->_db->select()->from('stature','stature')->where("userid='$userid'")->order('date desc')->limit(1);
    				$stature=$this->_db->fetchRow($stature_sql);
    				if($stature)
    				$result['stature']=$stature['stature'];	
    			}
    			if(($result1['privacy_friendlist']=='public' || ($result1['privacy_friendlist']=='friends' && $relationtype=='friends') || ($result1['privacy_friendlist']=='fof' && ($relationtype=='fof' || $relationtype=='friends')))or $suserid==$userid)
    				$result['friends']=unserialize($result1['friends']);
    			$result['propic_id']=$result1['propic'];
    			$result['propic_url']=$result1['propic_url'];
    			$result['secondarypic1_id']=$result1['secondarypic1'];
    			$result['secondarypic2_id']=$result1['secondarypic2'];
    			$result['secondarypic1_url']=$result1['secondarypic1_url'];
    			$result['secondarypic2_url']=$result1['secondarypic2_url'];
    			$result['style']=$result1['style'];
    			$result['sex']=$result1['sex'];
    			$result['mood']=$result1['mood'];
    			$result['chartpic']=$result1['chartpic'];
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
    			
    			$pages=array_unique(array_merge($result['school'],$result['employer'],$result['college'],$result['musics'],$result['books'],$result['movies'],$result['celebrities'],$result['games'],$result['sports'],$result['other']));
    			if(count($pages)>0){
    				$fav_sql=$this->_db->select()->from('pages',array('pageid','pagename','pagepic','page_url'=>'url'))->joinLeft('image','image.imageid=pages.pagepic',array('imageurl'=>'url'))->where('pageid in (?)',$pages);
    				$fav_result=$this->_db->fetchAssoc($fav_sql);
    				$result['fav_pages']=$fav_result;
    				
    				$result['fav_pages_merged']=array_diff($pages, $result['school'],$result['college'],$result['employer']);
    			}
    			else
    				$result['fav_pages']=array();
    			$result['privacy_fav']=$result1['privacy_fav'];
    			$result['privacy_post']=$result1['privacy_post'];
    			$result['postignore']=unserialize($result1['privacy_postignore']);
    			$result['privacy_video']=$result1['privacy_video'];
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
    			$sql=$this->_db->select()->from('pages', array('pagepic','page_vote'=>'vote','website','views','page_type'=>'type','category','subcategory','bannedusers','canpost','bids','place','contact','url'))
    					->joinLeft('places', 'places.id=pages.place',array('place_name'=>'name'))
				    	->joinLeft('pages_info','pages_info.pageid=pages.pageid',array('page_info'=>'info','page_tabs'=>'tabs','page_songurl'=>'songurl','page_ratings'=>'ratings','page_tags'=>'tags'))
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
    			$result1['page_tags']=unserialize($result1['page_tags']);
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
    			 
    			 $this->_db->update('pages',array('views'=>($result1['views']+1)),array('pageid=?'=>$result['userid']));
    			 return $result;
    		}
    	}
    	return $result;
    }
   
    public function buildusers($users=null){
    	ini_set('memory_limit', '1000M');
    	set_time_limit(0);
    	Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
    	
    	/**
    	 * Create index
    	 */
    	if(isset($users))
    		$index = Search_MyLucene::open($this->_indexPath);
    	else{
    		$index = Search_MyLucene::create($this->_indexPath);
    	
    	/**
    	 * Get all users
    	 */
    	$sql=$this->_db->select()
    	->from('freniz',array('userid','username','type','user_url'=>'url'))
    	->joinLeft('user_info','freniz.userid=user_info.userid',array('school','college','employer','hometown','currentcity','mood','skills'))
    	->joinLeft('friends_vote','friends_vote.userid=freniz.userid',array('friends'=>'friendlist','user_vote'=>'vote'))
    	->joinLeft('image','image.imageid=user_info.propic',array('propic_url'=>'url'))
    	->joinLeft('pages', 'pages.pageid=freniz.userid',array('page_vote'=>'vote','category','subcategory','bids'))->joinLeft('image','image.imageid=pages.pagepic',array('pagepic_url'=>'url'))->where("freniz.userid!='default'");
    	$users=$this->_db->fetchAssoc($sql);
    	//$this->view->results=$users;
    	}
    	/**
    	 * Create a document for each user and add it to the index
    	 */
    	foreach ($users as $user) {
    		$doc = new Zend_Search_Lucene_Document();
    	
    		/**
    		 * Fill document with data
    		 */
    	
    		if($user['type']=='user')
    			$index->addDocument($this->insertUserDocument($user, $doc));
    		else if($user['type']=='page')
    			$index->addDocument($this->insertPageDocument($user, $doc));
    		
    		/**
    		 * Add document
    		 */
    	}
    	
    	$index->optimize();
    	$index->commit();
    	
    	
    }
    private function insertUserDocument($user,$doc){
    	$doc->addField(Zend_Search_Lucene_Field::keyword('userid', $user['userid']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('type', $user['type']));
    	$doc->addField(Zend_Search_Lucene_Field::text('username', $user['username']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('user_url', $user['user_url']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('propic', $user['propic_url']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('friends', $user['friends']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('vote', $user['user_vote']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('mood', $user['mood']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('school', implode(',',unserialize($user['school']))));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('college', implode(',',unserialize($user['college']))));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('employer', implode(',',unserialize($user['employer']))));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('livingin', $user['currentcity']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('hometown', $user['hometown']));
    	$skill=explode(',', $user['skills']);
    	$skills=implode(' ', $skill);
    	$doc->addField(Zend_Search_Lucene_Field::text('skills', $skills));
    	
    	return $doc;
    	 
    }
    private function insertPageDocument($user,$doc){
    	$doc->addField(Zend_Search_Lucene_Field::keyword('userid', $user['userid']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('type', $user['type']));
    	$doc->addField(Zend_Search_Lucene_Field::text('username', $user['username']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('user_url', $user['user_url']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('propic', $user['pagepic_url']));
    	$doc->addField(Zend_Search_Lucene_Field::unIndexed('vote', $user['page_vote']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('category', $user['category']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('subcategory', $user['subcategory']));
    	$doc->addField(Zend_Search_Lucene_Field::keyword('bids', $user['bids']));
    	return $doc;
    }
   public function getminiprofile($ids){
   	$sql=$this->_db->select()->from('freniz',array('userid','user_url'=>'url','profile_type'=>'type','username'))
   	->joinLeft('user_info', 'freniz.userid=user_info.userid',array('sex','school','email','hometown','currentcity','myphilosophy','propic','mood'))
   	->joinLeft('friends_vote','user_info.userid=friends_vote.userid',array('friends'=>'friendlist','user_vote'=>'vote'))
   	->joinLeft('privacy','user_info.userid=privacy.userid',array('privacy_contactdetails'=>'contactdetails','privacy_religion'=>'religion','privacy_dob'=>'dob','privacy_aboutme'=>'aboutme','privacy_relationship'=>'relationship','privacy_livingin'=>'livingin','privacy_hometown'=>'hometown','privacy_languages'=>'languages','privacy_education'=>'education','privacy_occupation'=>'occupation','privacy_friendlist'=>'friendlist','privacy_status'=>'status','privacy_fav'=>'fav','privacy_message'=>'message','privacy_request'=>'request','privacy_invite'=>'invite','privacy_post'=>'post','privacy_postignore'=>'postignore','privacy_testy'=>'testy','privacy_testyignore'=>'testyignore','privacy_video'=>'video','privacy_videoignore'=>'videoignore'))
   	->joinLeft('image as propic', 'propic.imageid=user_info.propic',array('propic_url'=>'url'))
   	->joinLeft('image as secondarypic1','secondarypic1.imageid=user_info.secondarypic1',array('secondarypic1_url'=>'url'))
   	->joinLeft('image as secondarypic2','secondarypic2.imageid=user_info.secondarypic2',array('secondarypic2_url'=>'url'))
   	->joinLeft('places as hometown', 'hometown.id=user_info.hometown',array('hometown_name'=>'name'))
   	->joinLeft('places as currentcity', 'currentcity.id=user_info.currentcity',array('currentcity_name'=>'name'))
   	->joinLeft('pages', 'freniz.userid=pages.pageid',array('pagepic','page_vote'=>'vote','website','views','page_type'=>'type','category','subcategory','bannedusers','canpost','bids','place'))
   	->joinLeft('pages_info','pages_info.pageid=pages.pageid',array('page_info'=>'info','page_tabs'=>'tabs','page_songurl'=>'songurl','page_ratings'=>'ratings'))
   	->joinLeft('image', 'image.imageid=pages.pagepic',array('page_pagepicurl'=>'url'))
   	->where('freniz.userid in(?)',$ids);
   	$result=$this->_db->fetchAssoc($sql);
   	
   	
   	return $result;
    
   }
  
   
   
   
   public function checkUser($username){
   	$result=$this->find($username);
   	if(isset($result[0]))
        return false;
    else
    	return true;
   }
   
   public function checkmail($email){
   	$sql=$this->_db->select()->from('userstable','userid')->where('email=?',$email);
   	$result=$this->_db->fetchRow($sql);
   	if($result){
   		return false;
   	}
   	else return true;
   }
   public function switchUser($id){
   	if(in_array($id, $this->authIdentity->adminpages)){
   		$sql=$this->_db->select()->from('freniz')->joinLeft('friends_vote','friends_vote.userid=freniz.userid','friendlist')
   		->joinLeft('pages', 'pages.pageid=freniz.userid',array('canpost','bannedusers'))->joinLeft('image', 'image.imageid=freniz.propic','url as propic_url')->where('freniz.userid = ?',$id);
   		$result=$this->_db->fetchRow($sql);
   		if($result['type']=='user'){
   			$this->authIdentity->userid=$result['userid'];
   			$this->authIdentity->username=$result['username'];
   			$this->authIdentity->type='user';
   			$this->authIdentity->url=$result['url'];
   			$this->authIdentity->friends=unserialize($result['friendlist']);
   			$this->authIdentity->blocklistmerged=array_merge($this->authIdentity->blocklist,$this->authIdentity->blockedby);
   			$this->authIdentity->adminpages=unserialize($result['adminpages']);
   			$this->authIdentity->propic_url=$result['propic_url'];
   			unset($this->authIdentity->privacy['canpost']);
   		}
   		else if($result['type']=='page'){
   			$userid=$this->authIdentity->userid;
   			$adminpages=array_merge(array($userid),array_diff($this->authIdentity->adminpages, array($result['userid'])));
   			$this->authIdentity->userid=$result['userid'];
   			$this->authIdentity->username=$result['username'];
   			$this->authIdentity->type='leaf';
   			$this->authIdentity->url=$result['url'];
   			$this->authIdentity->friends=array();
   			$this->authIdentity->blocklistmerged=unserialize($result['bannedusers']);
   			$this->authIdentity->adminpages=$adminpages;
   			$this->authIdentity->propic_url=$result['propic_url'];
   			$this->authIdentity->privacy['canpost']=$result['canpost'];
   		}
   	}
   }
   public function getmini($userid,$myauthIdentity=null){
   	$sql=$this->_db->select()->from('freniz',array('userid','user_url'=>'url','profile_type'=>'type','username'))
   	->where('freniz.userid=?',$userid);
   	$result=$this->_db->fetchRow($sql);
   	if($result){
   		if(isset($myauthIdentity))
   			$myfriends=$myauthIdentity->friends;
   		else $myfriends=array();
   
   		if($result['profile_type']=='user'){
   			$sql=$this->_db->select()->from('user_info', array('propic','mood','secondarypic1'))
   			->joinLeft('friends_vote','user_info.userid=friends_vote.userid',array('friends'=>'friendlist','user_vote'=>'vote','incomingrequest','sentrequest'))
   			->joinLeft('image as propic', 'propic.imageid=user_info.propic',array('propic_url'=>'url'))
   			->joinLeft('image as secondarypic1','secondarypic1.imageid=user_info.secondarypic1',array('secondarypic1_url'=>'url'))
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
   			if(isset($this->authIdentity))
   				$result['friends']=unserialize($result1['friends']);
   			$result['propic_id']=$result1['propic'];
   			$result['propic_url']=$result1['propic_url'];
   			$result['secondarypic1_id']=$result1['secondarypic1'];
   			$result['secondarypic1_url']=$result1['secondarypic1_url'];
   			$result['mood']=$result1['mood'];
   			$result['sentrequest']=unserialize($result1['sentrequest']);
   			$result['incomingrequest']=unserialize($result1['incomingrequest']);
   			$result['mutual_friends']=$mutual_friends;
   			$result['vote']=unserialize($result1['user_vote']);
   			$friends_profile_sql=$this->_db->select()->from('user_info',array('userid','fname','lname','propic','url as friendurl'))
   			->joinLeft('friends_vote', 'friends_vote.userid=user_info.userid',array('friendlist','vote'))->joinLeft('image', 'image.imageid=user_info.propic',array('imageurl'=>'url'));
   			if(!empty($result['friends'])){
   				$friends_profile_sql=$friends_profile_sql->where('user_info.userid in (?)',$result['friends']);
   				$result['friends_profiles']=$this->_db->fetchAssoc($friends_profile_sql);
   			}
   			elseif (!empty($result['mutual_friends'])){
   				$friends_profile_sql=$friends_profile_sql->where('user_info.userid in (?)',$result['mutual_friends']);
   				$result['friends_profiles']=$this->_db->fetchAssoc($friends_profile_sql);
   			}
   			 
   			return $result;
   		}
   		else {
   			$sql=$this->_db->select()->from('pages', array('pagepic','page_vote'=>'vote','website','views','page_type'=>'type','category','subcategory','bids','place','url as user_url','bannedusers'))
   			->joinLeft('image', 'image.imageid=pages.pagepic',array('propic_url'=>'url'))
   			->where('pages.pageid=?',$result['userid']);
   			$result1=$this->_db->fetchRow($sql);
   			if(isset($result1['page_vote']))
   				$result1['page_vote']=unserialize($result1['page_vote']);
   			else $result1['page_vote']=array();
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
