<?php

/**
 * Lists
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_Lists extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'lists';
	protected $registry,$authIdentity,$sunNetwork,$defaultLists,$defaultListIds;
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
		$this->sunNetwork=array('suntv','suryatv','aadhithya','sunmusic','chuttitv');
		$this->defaultLists=$this->registry->defaultLists;
		$this->defaultListIds=$this->registry->defaultListIds;
	
	}
	public function createLists($name){
		if(!in_array(strtolower($name), $this->defaultLists)){
		$listid=$this->insert(array('name'=>$name,'userid'=>$this->authIdentity->userid,'listitems'=>'a:0:{}'));
		if($listid)
			return true;
		else return false;
		}
		else
			return false;
	}
	public function addListusers($id,$users){
		if(!in_array($id, $this->defaultListIds)){
		$users=explode(',',$users);
		$sunList=array_intersect($users, $this->sunNetwork);
		$result=$this->find($id);
		if($result[0]){
			$result=$result[0];
			if($result['userid']==$this->authIdentity->userid){
				if(strcasecmp('sun network', $result['name'])!=0 ){
					$hasusers=unserialize($result['listitems']);
					$newUsersList=array_unique(array_merge($hasusers,$users));
					$this->update(array('listitems'=>serialize($newUsersList)), array('id=?'=>$id));
					return true;
					
				}
				elseif(count($sunList)>0){
					$this->update(array('listitems'=>serialize($sunList)), array('id=?'=>$id));
					return true;
				}
				else return false;
			}
			else return false;
		}
		else return false;
		}
		else return false;
		
	}
	public function deleteListUsers($id,$users){
		if(in_array($id, $this->defaultListIds))
			return false;
		
		$users=explode(',',$users);
		$result=$this->find($id);
		if($result[0]){
			$result=$result[0];
			if($result['userid']==$this->authIdentity->userid){
				if(strcasecmp('sun network', $result['name'])!=0 ){
					$hasusers=unserialize($result['listitems']);
					$newUsersList=array_diff($hasusers,$users);
					$this->update(array('listitems'=>serialize($newUsersList)), array('id=?'=>$id));
					return true;
						
				}
				else{
					$newUsersList=array_diff($hasusers,$users);
					if(count($newUsersList)>0){
					$this->update(array('listitems'=>serialize($newUsersList)), array('id=?'=>$id));
					return true;
					}
					else return false;
				}
			}
			else return false;
		}
		else return false;
	}
	public function getListsDetail($id){
		$result=$this->find($id);
		if($result[0]){
			$result=$result[0]->toArray();
			if($result['userid']==$this->authIdentity->userid){
				$usersList=unserialize($result['listitems']);
				$result['listitems']=$usersList;
				if(!empty($usersList)){
					$users=new Application_Model_Users($this->_db);
					$result['userpro']=$users->getminiprofile($usersList);
				}
				return $result;
			}
			else return false;
		}
		else return false;
	}
	public function getUsersLists(){
		$sql=$this->_db->select()->from($this->_name)->where('userid=?',$this->authIdentity->userid)->orWhere('id in (?)',$this->defaultListIds);
		$result=$this->_db->fetchAssoc($sql);
		return $result;
	}
	public function deleteList($id){
		if(in_array($id, $this->defaultListIds))
			return false;
		if($this->delete(array('userid=?'=>$this->authIdentity->userid,'id=?'=>$id,'name!=?'=>'sun network')))
			return true;
		else return false;
	}
	
}
