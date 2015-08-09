<?php

/**
 * apps
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_Apps extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'apps';
    protected $authIdentity,$auth;
	
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$this->auth=Zend_Auth::getInstance();
		if($this->auth->hasIdentity())
		{
			$this->authIdentity=$this->auth->getIdentity();
			
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
				
	}
	public function createEntry($data){
		$this->insert($data);
	}
	
	public function UpdateDiary($date,$notes){
	
		if(isset($this->authIdentity)){
		$diary=$this->authIdentity->diary;
		$content=array();
		$content['notes']=htmlspecialchars($notes);
		$diary[$date]=$content;
		$myid=$this->authIdentity->userid;
		$this->update(array('diary'=>serialize($diary)), "userid='$myid'");
		 
		$this->authIdentity->diary=$diary;
		$this->auth->getStorage()->write($this->authIdentity);
		return array("status"=> "diary is updated");
		}
	}
	
	public function UpdateSlambook($userid,$request){
		if(isset($this->authIdentity)){
		
		$slambook=array();
		$myid=$this->authIdentity->userid;
		
		$sql=$this->select()->from($this->_name,'slambook')->where("userid='$userid'");
		$result=$this->_db->fetchRow($sql);
			$slambook=unserialize($result['slambook']);
		$slambook[$myid]=$request;
		$update_data=array('slambook'=>serialize($slambook));
		$this->update($update_data, "userid='$userid'");
		
		
			return array("status"=> "slambook updated successfully");
		
	
	}
	
}
	public function diary($date){
		if(isset($this->authIdentity)){
			$session_diary=$this->auth->getIdentity()->diary;
			$notes=$session_diary[$date]['notes'];
			return $notes;
			
			
		}
		
	}
	public function slambook(){
		if(isset($this->authIdentity)){
			$userid=$this->authIdentity->userid;
			$sql=$this->select()->from($this->_name,'slambook')->where("userid='$userid'");
			$result=$this->_db->fetchRow($sql);
			$slambook=unserialize($result['slambook']);
			if (count($slambook)>0){
			$sql1=$this->_db->select()->from('user_info as s',array('userid','propic'))
			                     ->joinLeft('image','image.imageid=s.propic',array('url'))
			                   ->where("s.userid in (?)",array_keys($slambook));
			$result1=$this->_db->fetchAssoc($sql1);
				
			foreach ($result1 as $key=>$value){
				$slambook[$key]['propic_url']=$value['url'];
			}
			}
				return $slambook;
			
			
			
		}
	}

}
