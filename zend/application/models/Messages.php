<?php

/**
 * Messages
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Messages extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'message';
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
		
	}
	
	public function sendMessages($ruserid,$message){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('privacy','message')
			->joinLeft('friends_vote', 'friends_vote.userid=privacy.userid','friendlist')
			->where('privacy.userid=?',$ruserid);
		$result=$this->_db->fetchRow($sql);
		if(($result['message']=='public' || ($result['message']=='friends' &&in_array($ruserid, $this->authIdentity->friends) || ($result['message']=='fof' && (in_array($ruserid, $this->authIdentity->friends)|| count(array_intersect($result['friendlist'], $this->authIdentity->friends)>=1))) )&& !in_array($ruserid, $this->authIdentity->blocklistmerged))&&($ruserid!=$this->authIdentity->userid)){
			$userid=$this->authIdentity->userid;
			$message_data=array('suserid'=>$userid,'ruserid'=>$ruserid,'message'=>$message,'date'=>new Zend_Db_Expr('Now()'),'read1'=>'0');
            $this->insert($message_data);
		  }
			
		}
	}
	
	public function unReadMessages(){
		if(isset($this->authIdentity)){
			$count=new Zend_Db_Expr('count(messageid)');
		$sql=$this->select()->from($this->_name,array('count'=>$count))->where("read1='0' and ruserid=?",$this->authIdentity->userid);
        $result=$this->fetchRow($sql);
        return $result->toArray();
	 	
	   }
	
	}
	public function deleteAllMessages($ruserid){
		if(isset($this->authIdentity)){
		$myuserid=$this->authIdentity->userid;
		$sql=$this->select()->from($this->_name,array('messageid','suserid','ruserid','suservisi','ruservisi'))->where("(suserid='$ruserid' and ruserid='$myuserid') or (ruserid='$ruserid' and suserid='$myuserid')");	
	    $result=$this->_db->fetchAssoc($sql);
	    $deletemessages=array();$hiddensuser=array();$hiddenruser=array();
	    foreach($result as $messageid => $message){
	       	if($message['suserid']==$this->authIdentity->userid){
	       		if($message['ruservisi']=='hidden'){
	       			array_push($deletemessages, $messageid);
	       			//mysql_query("delete from message where messageid='".$row['messageid']."'");
	       			//return array("status"=>"message removed");
	       		}
	       		else
	       		{
	       			array_push($hiddensuser,$messageid);
	       			//mysql_query("update message set suservisi='hidden' where messageid='".$row['messageid']."'");
	       			//return array("status"=>"message removed");
	       		}
	       	}
	       	else if($message['ruserid']==$this->authIdentity->userid){
	       		if($message['suservisi']=='hidden'){
	       			array_push($deletemessages, $messageid);
	       			 
	       			//mysql_query("delete from message where messageid='".$row['messageid']."'");
	       			//return array("status"=>"message removed");
	       		}
	       		else
	       		{
	       			array_push($hiddenruser, $messageid);
	       			 
	       			//mysql_query("update message set ruservisi='hidden' where messageid='".$row['messageid']."'");
	       			//return array("status"=>"message removed");
	       		}
	       	}
	       	if(!empty($deletemessages))
	       	{
	       		$this->delete(array('messageid in (?)'=>$deletemessages));
	       	}
	       	if(!empty($hiddensuser)){
	       		$this->update(array('suservisi'=>'hidden'), array('messageid in (?)'=>$hiddensuser));
	       	}
	       	if(!empty($hiddenruser)){
	       		$this->update(array('ruservisi'=>'hidden'), array('messageid in (?)'=>$hiddenruser));
	       	}
         }
    	
	   }
	}
	
	public function getMessages(){
		if(isset($this->authIdentity)){
		$sql=$this->_db->select()->from("{$this->_name} as t",array('messageid','suserid','message','date','read1'))->joinLeft('message as r', 'r.date=t.date and t.suserid=r.suserid and t.messageid < r.messageid ')->where("t.ruserid='".$this->authIdentity->userid."' and t.ruservisi='visible' ")->order('t.date desc');
		return $sql;
			
		}
		
	}
	public function getUserMessages($ruserid){
		if(isset($this->authIdentity)){
			$suserid=$this->authIdentity->userid;
			$sql=$this->_db->select()
			->from($this->_name,array('messageid','suserid','ruserid','message','date','read1','suservisi','ruservisi'))
			->joinLeft('user_info','user_info.userid=message.suserid', array('fname','lname','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->where("(suserid='".$ruserid."' and ruserid='".$suserid."') or (ruserid='".$ruserid."' and suserid='".$suserid."')")->order('date asc');
			$result=$this->_db->fetchAssoc($sql);
			return $result;
			$this->update('read1=1',"ruserid='$suserid' and suserid='$ruserid'");
				
		}
		
	}
	
	public function getConvocation($ruserid){
		if(isset($this->authIdentity)){
			$suserid=$this->authIdentity->userid;
			$this->update('read=1',"suserid='$ruserid' and ruserid='$suserid'");
			$suserid=$this->authIdentity->userid;
			$sql=$this->_db->select()->from($this->_name,array('suserid','messageid','message','date'))
							->joinLeft('user_info','user_info.userid=message.suserid', array('fname','lname','propic'))
							->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
							->where("(suserid='".$ruserid."' and ruserid='".$suserid."' or ruserid='".$ruserid."' and suserid='".$suserid."')")
							->order('date desc');
			$result=$this->_db->fetchAssoc($sql);
			return $result;
		}
		
	}
}
