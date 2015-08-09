<?php

/**
 * Friend
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Friend extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'friends_vote';
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}
	
	public function sendFriendsRequest($ruserid,$text,$imageurl,$songurl){
		if(isset($this->authIdentity)){
		$myid=$this->authIdentity->userid;
		if(in_array($ruserid,$this->authIdentity->friends))
		{
			return array("status"=> "already friend");
		}
		else if(in_array($ruserid, $this->authIdentity->sentrequest))
		{
			return array("status"=>"requsted already");
		}
		else if(in_array($ruserid, $this->authIdentity->bendingrequest))
		{
			return array("status"=>"request got already");
		}
		else
		{
			array_push($this->authIdentity->sentrequest, $ruserid);
			$this->update(array('sentrequest'=>serialize($this->authIdentity->sentrequest)),array('userid=?'=>$myid));
			$sql=$this->select()->from($this->_name,'incomingrequest')->where("userid='$ruserid'");
			$result=$this->fetchRow($sql)->toArray();
				$bu=unserialize($result['incomingrequest']);
				array_push($bu, $myid);
				$this->update(array('incomingrequest'=>serialize($bu)), array('userid=?'=>$ruserid));
			$invites_data=array('suserid'=>$myid,'ruserid'=>$ruserid,'text'=>$text,'songurl'=>$songurl,'imageurl'=>$imageurl);
			$this->_db->insert('invites', $invites_data);	
			return array("status"=>"sucess");
		 }
		}
	}
	public function addFriends($ruserid){
		if(isset($this->authIdentity)){
			$myid=$this->authIdentity->userid;
			
		$frnds=$this->authIdentity->friends;
		$bendingrequest=$this->authIdentity->incomingrequest;
		//$this->_db->delete('invites',"(ruserid='".$myid."' and suserid='".$ruserid."')");
		$sql=$this->select()->from($this->_name,array('userid','friendlist','sentrequest'))->where("userid='$ruserid'");
		$result=$this->_db->fetchRow($sql);

			$frnds1=unserialize($result['friendlist']);
			$sentrequest=unserialize($result['sentrequest']);
		
		if(in_array($ruserid,$bendingrequest))
		{
			array_push($frnds,$ruserid);
			$frnds=array_unique($frnds);
			$bendingrequest=array_diff($bendingrequest,array($ruserid));
			$this->authIdentify->bendingrequest=$bendingrequest;
			$this->authIdentity->friends=$frnds;
			$this->update(array('friendlist'=>serialize($frnds),'incomingrequest'=>serialize($bendingrequest)), array('userid=?'=>$myid));
			array_push($frnds1,$myid);
			$sentrequest=array_diff($sentrequest, array($myid));
			//return array($frnds,$sentrequest);
			$this->update(array('friendlist'=>serialize($frnds1),'sentrequest'=>serialize($sentrequest)), array('userid=?'=>$ruserid));
			$count=new Zend_Db_Expr('count(userid)');
			$sql2=$this->_db->select()->from('activity',array('userid','ruserid','count'=>$count))->where("(userid='".$myid."' or userid='$ruserid') and title='now friends' and date>date_sub(now(),interval 1 day)")->group('userid');
			$result2=$this->_db->fetchAssoc($sql2);
			//return $result2;
		foreach($result2 as $userid=>$values)
			{
				$activity_data=array('userid'=>$userid,'ruserid'=>$values['ruserid'],'contentid'=>0,'title'=>'now friends','contenttype'=>'user','contenturl'=>$values['userid'],'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'user_'.$values['userid']);
				if($values['count']!=0){
					$activity_data['contentid']=$values['count'];		 
				}
				$this->_db->insert('activity',$activity_data);
			}
			if($result2){
				if(count($result2)!=2){
					
				if(!key_exists($ruserid, $result2)){
					$activity_data['userid']=$ruserid;
					$activity_data['ruserid']=$myid;
					$activity_data['contenturl']=$ruserid;
					$activity_data['alternate_contentid']='user_'.$ruserid;
					$activity_data['contentid']=0;
					
				}
				else
				{
					$activity_data['userid']=$myid;
					$activity_data['ruserid']=$ruserid;
					$activity_data['contenturl']=$myid;
					$activity_data['alternate_contentid']='user_'.$myid;
					$activity_data['contentid']=0;
				}
				$this->_db->insert('activity', $activity_data);
				}
				
				
			}
			else
			{
				$activity_data=array('userid'=>$myid,'ruserid'=>$ruserid,'contentid'=>0,'title'=>'now friends','contenttype'=>'user','contenturl'=>$myid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'user_'.$myid);
				$this->_db->insert('activity',$activity_data);
				$activity_data['userid']=$ruserid;
				$activity_data['ruserid']=$myid;
				$activity_data['contenturl']=$ruserid;
				$activity_data['alternate_contentid']='user_'.$ruserid;
				$this->_db->insert('activity', $activity_data);
			}
			}
			
		 }
		 
		}
	
		public function BendingRequestCount(){
			if(isset($this->authIdentity)){
			$myid=$this->authIdentity->useid;
			$sql=$this->select()->from($this->_name,array('incomingrequest','friendlist','sentrequest'))->where("userid='$myid'");	 
			$bendingrequest;$frnds;$sentrequest;
			$result=$this->_db->fetchAssoc($sql);
			$bendingrequest=unserialize($result['incomingrequest']);
				$frnds=unserialize($result['friendlist']);
				$sentrequest=unserialize($result['sentrequest']);
			$this->authIdentity->bendingrequest=$bendingrequest;
			$this->authIdentity->friendlist=$frnds;
			$this->authIdentity->sentrequest=$sentrequest;
			return array("reqcount"=> sizeof($bendingrequest));
			}
		
		}
		public function cancelFriendsRequest($ruserid){
			if(isset($this->authIdentity)){
				$myid=$this->authIdentity->userid;
				$sentrequest=$this->authIdentity->sentrequest;
				if(in_array($ruserid, $sentrequest)){
				$this->_db->delete('invites',array('suserid=?'=>$myid,'ruserid=?'=>$ruserid));
				$sql=$this->select()->from($this->_name,'incomingrequest')->where("userid='$ruserid'");
				$result=$this->_db->fetchAssoc($sql);
				$bendingrequest;
					$bendrequest=unserialize($result['incomingrequest']);
					$sentrequest=array_diff($sentrequest,array($ruserid));
				$bendingrequest=array_diff($bendrequest,array($myid));
				if(!isset($bendingrequest)){
					$bendingrequest='a:0:{}';
				}else{
					$bendingrequest=serialize($bendingrequest);
				}
				$this->authIdentity->sentrequest=$sentrequest;
				$this->update(array('sentrequest'=>serialize($sentrequest)),array('userid=?'=>$myid));
				$this->update(array('incomingrequest'=>$bendingrequest),array('userid=?'=>$ruserid));
				return array("status"=>"friend request cancelled");
			 }
			}
		}
		
		public function removeFriends($ruserid){
			if(isset($this->authIdentity)){
				$myid=$this->authIdentity->userid;	
			    $frnds= $this->authIdentity->friends;
			   $sql=$this->select()->from($this->_name,'friendlist')->where("userid='$ruserid'");
				$result=$this->fetchRow($sql)->toArray();
				$frnds1;
			       $frnds1=unserialize($result['friendlist']);
			     if(in_array($ruserid,$frnds))
			             {
			        $newfrnds=array_diff($frnds,array($ruserid));
			       $newfrnds=array_unique($newfrnds);
					 $this->authIdentity->friendlist=$newfrnds;
					$this->update(array('friendlist'=>serialize($newfrnds)), array('userid=?'=>$myid));
					$newfrnds1=array_diff($frnds1,array($myid));
					$this->update(array('friendlist'=>serialize($newfrnds1)),array('userid=?'=>$ruserid));
					$this->_db->delete('activity',"(userid='$myid' and ruserid='$ruserid' and title='now friends') or (userid='$ruserid' and ruserid='$myid' and title='now friends')");
				  return array("status"=>"success");
			     }
			 	}	
			}
  
			public function IgnorefriendsRequest($ruserid){
				if(isset($this->authIdentity)){
					$myid=$this->authIdentity->userid;
				$bendingrequest=$this->authIdentity->bendingrequest;
				$this->_db->delete('invites',array('ruserid=?'=>$myid,'suserid=?'=>$ruserid));
				$sql=$this->select()->from($this->_name,'sentrequest')->where("userid='$ruserid'");
				$result=$this->fetchRow($sql)->toArray();
				$sentrequest;
				$sentrequest=unserialize($result['sentrequest']);
				$bendingrequest=array_diff($bendingrequest, array($ruserid));
				$this->authIdentity->bendingrequest=$bendingrequest;
				$sentrequest=array_diff($sentrequest, array($myid));
					if(!isset($bendingrequest)){
					$bendingrequest='a:0:{}';
					
				}else{
					$bendingrequest=serialize($bendingrequest);
				}
				if(!isset($sentrequest)){
					
					$sentrequest='a:0:{}';
				}else{
					$sentrequest=serialize($sentrequest);
				}
				$this->update(array('incomingrequest'=>$bendingrequest),array('userid=?'=>$myid));
				$this->update(array('sentrequest'=>$sentrequest),array('userid=?'=>$ruserid));
				return array("status"=> "success");
				} 
			}
			
}
