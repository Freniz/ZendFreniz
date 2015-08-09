<?php

/**
 * Album
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Album extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'album';
	
	protected $authIdentitiy,$registry;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentitiy=$auth->getIdentity();
			
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
		$this->registry=Zend_Registry::getInstance();
	}
	public function createAlbum($albumname,$userid=null,$privacy=null){
			if(isset($this->authIdentitiy)){
			if($this->authIdentity->type=='user'){
				$privacy['pt']=$this->authIdentitiy->privacy['albumvisi'];
				$privacy['specificlist']=$this->authIdentity->privacy['albumspeci'];
				$privacy['hiddenlist']=$this->authIdentitiy->privacy['albumhidden'];
				$privacy['cpt']=$this->authIdentitiy->privacy['album'];
				$privacy['ciu']=$this->authIdentitiy->privacy['albumignore'];
				$privacy['csu']=$this->authIdentitiy->privacy['albumspecificpeople'];
			}
			else
				$privacy=array('pt'=>'public','ignorelist'=>'a:0:{}','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','cpt'=>'friends','ciu'=>'a:0:{}','csu'=>'a:0:{}');
				$myid=$this->authIdentitiy->userid;
			$album=array('userid'=>$myid,'name'=>$albumname,'date'=>new Zend_Db_Expr('Now()'),'pt'=>$privacy['pt'],'specificlist'=>$privacy['specificlist'],'hiddenlist'=>$privacy['hiddenlist'],'ignorelist'=>$privacy['ignorelist']);
			return $this->insert($album);
		}
		
	}
	protected function getAlbumInfo($id)
	{
		if(isset($this->authIdentitiy)){
				$sql=$this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)->joinLeft('user_info', 'user_info.userid=freniz.userid',array('fname','lname','block'));
		}
	}
	public function albums($userid){
		if(isset($this->authIdentitiy)){
			$select_album=array('albumid','userid','name','date','coverimage','pt','specificlist','hiddenlist');
			$sql=$this->_db->select()->from($this->_name,$select_album)			
									->joinLeft('user_info', 'user_info.userid=album.userid','propic')
									->joinLeft('image', 'album.coverimage=image.imageid','url as coverimage_url')
									->joinLeft('image as simage','user_info.propic=simage.imageid',array('surl'=>'url'))
									->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist')
									->where("album.userid='$userid'");
			$results=$this->_db->fetchAssoc($sql);
			foreach ($results as $albumid=>$result){
				$rusrid=$userid;
				$privacy=$result['pt'];
				$specific=  unserialize($result['specificlist']);
				$hiddenlist=  unserialize($result['hiddenlist']);
				$session['friends']=$this->authIdentitiy->friends;
				$rusrfrnds=$result['friendlist'];
				$myid=$this->authIdentitiy->userid;
				$session['blocklistmerged']=$this->authIdentitiy->blocklistmerged;
				if((($privacy=='public'||($privacy=='friends' && in_array($rusrid,$session['friends']))||($privacy=='fof' && count(array_intersect($rusrfrnds, $session['friends'])>=1) )||($privacy=='specific' && in_array($myid, $specific)))&& !in_array($rusrid, $session['blocklistmerged']) && !in_array($myid, $hiddenlist))|| $myid==$rusrid ){
				
				}
				else 
					unset($results[$albumid]);	
			}
			//print_r($results);
			return $results;
		}
	}
	
	
}
