<?php

/**
 * Invites
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Invites extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'invites';
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}
	
	public function getAllInvites(){
		if (isset($this->authIdentity)){
			$myid=$this->authIdentity->userid;
			$sql=$this->_db->select()->from($this->_name,array('inviteid','suserid','text','songurl','imageurl'))
									->joinLeft('user_info','user_info.userid=invites.suserid',array('fname','lname','propic'))
									->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
									->joinLeft('friends_vote','friends_vote.userid=invites.suserid','friendlist')
									->where("ruserid='$myid'");
			$result=$this->_db->fetchAssoc($sql);
		
			foreach($result as $inviteid=>$invites){
			$result[$inviteid]['count']=count(array_intersect(unserialize($invites['friendlist']), $this->authIdentity->friends));
			}
			
			return $result;
		}
	}
	
	public function getInvites($ruserid){
		if (isset($this->authIdentity)){
			$myid=$this->authIdentity->userid;
			$sql=$this->_db->select()->from($this->_name,array('inviteid','suserid','text','songurl','imageurl'))
			->joinLeft('user_info','user_info.userid=invites.suserid',array('fname','lname','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->joinLeft('friends_vote','friends_vote.userid=invites.suserid','friendlist')
			->where("ruserid='$myid'" and "suserid='$ruserid'");
			$result=$this->_db->fetchRow($sql);
	
				$result['count']=count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends));
				
			return $result;
		}
	}
}
