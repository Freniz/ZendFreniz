<?php

/**
 * Admiration
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Admiration extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'testimonial';
	protected $authIdentity;
	
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$this->authIdentity=$auth->getIdentity();
		}
				
	}
	
	public function addAdmiration($text,$ruserid)
	{
		if(isset($this->authIdentity)){
			
			$privacy_data=$this->_db->select()->from('privacy',array('testy','testyignore','testyvisi','testyspeci','testyhidden','advancedprivacyadmire','autoacceptusers','blockactivityusers'))->where('userid=?',$ruserid);
			$privacy=$this->_db->fetchRow($privacy_data);
				$ignore=unserialize($privacy['testyignore']);
			$autoacceptusers=unserialize($privacy['autoacceptusers']);
			$blockusersactivity=unserialize($privacy['blockactivityusers']);
			if(($privacy['testy']=='friends' && !in_array($ruserid, $this->authIdentity->blocklistmerged)&&in_array($ruserid, $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignore))&&($ruserid!=$this->authIdentity->userid)){
				$a=array();
				if($privacy['advancedprivacyadmire']=='on' && !in_array($this->authIdentity->userid, $blockusersactivity['admire'])){
					if(in_array($this->authIdentity->userid, $autoacceptusers['admire'])){
						
						
						$admiration_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['testyvisi'],'specificlist'=>$privacy['testyspeci'],'hiddenlist'=>$privacy['testyhidden']);
						$uptdid=$this->insert($admiration_data);
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'write an admire on','contenttype'=>'admire','contenturl'=>'admire.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'admire_'.$uptdid);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
					}
					else{
						
						$admiration_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['testyvisi'],'specificlist'=>$privacy['testyspeci'],'hiddenlist'=>$privacy['testyhidden']);
						$uptdid=$this->insert($admiration_data);
						
						$result1=mysql_query("select reviews from user_info where userid='".$ruserid."'");
						$reviews;
						$postreviews=array();
						while($row1= mysql_fetch_assoc($result1))
						{
							$reviews=unserialize($row1['reviews']);
							if(isset($reviews['admire']))
							{
								array_push($reviews['admire'], $updtdid);
							}
							else
							{
								$reviews['admire']=array($updtdid);
							}
							mysql_query("update user_info set reviews='".serialize($reviews)."' where userid='".$ruserid."'");
							 
						}
						if(isset($_SESSION['reqfrmme']['admire']))
							array_push($_SESSION['reqfrmme']['admire'], $updtdid);
						else
							$_SESSION['reqfrmme']['admire']=array($updtdid);
						mysql_query("update user_info set reqfrmme='".serialize($_SESSION['reqfrmme'])."' where userid='".$_SESSION['userid']."'");
						return array("status"=>"your admire will be posted after ".$ruserid." has reviewed");
						}
						
						
					}
					
					else if(!in_array($this->authidentity->userid, $blockusersactivity['admire'])) {

						$admiration_data=array('suserid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'message'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['blogvisi'],'specificlist'=>$privacy['blogspeci'],'hiddenlist'=>$privacy['bloghidden']);
						$uptdid=$this->insert($admiration_data);
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$ruserid,'contentid'=>$uptdid,'title'=>'write an admire on','contenttype'=>'admire','contenturl'=>'admire.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'admire_'.$uptdid);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
							
					}
					else
						return array("status"=>"you do not have permission to post");
					
					}
					else{
						return array("status"=>"you do not have permission to post");
					}
					
				
			}
			else
				return array("status","please give the valid information");
		 
	
		}
	
	
		public function deleteAdmiration($admireid){
			if(isset($this->authIdentity)){
			    $userid=$this->authIdentity->userid;
				$affected_rows=$this->delete("testyid='$admireid' and (suserid='$userid' or ruserid='$userid')");
				if($affected_rows==1){
					$activityModel=new Application_Model_Activity($this->_db);
					$activityModel->delete("userid='$userid' and contentid='$admireid' and contenttype='admire' and title='write an admire on'");
					return array("status"=>"Admire removed");
				}
					else 
					return array("status"=>"you dont have permission to delete this Admire");
				}
			else
				return array("status","please give the valid information");
		
		}	
	public function getAdmiration($ruserid,$from){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name,array('testyid','suserid','ruserid','message','vote','date','pt','specificlist','hiddenlist'))
			->joinLeft('friends_vote','friends_vote.userid=testimonial.ruserid','friendlist')
			->joinLeft('user_info','user_info.userid=testimonial.suserid', array('fname','lname','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->where("(suserid='".$ruserid."'or ruserid='".$ruserid."') and testimonial.accepted='yes'")
			->order('date desc')->limit('500',$from);
			 $results=$this->_db->fetchAssoc($sql);
			 foreach ($results as $id=> $result){
			 $privacy=$result['pt'];
			 $specific=  unserialize($result['specificlist']);
			 $hiddenlist=  unserialize($result['hiddenlist']);
			 $rusrfrnds=$result['friendlist'];
			 if((($privacy=='public'||($privacy=='friends' && in_array($result['ruserid'],$this->authIdentity->friends))||($privacy=='fof' && count(array_intersect($rusrfrnds, $this->authIdentity->friends)>=1) )||($privacy=='specific' && in_array($this->authIdentity->userid, $specific)))&& !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, $hiddenlist))|| $this->authIdentity->userid==$result['ruserid']){
            
			 }
			 else 
			 	unset($results[$id]);
			 }
			 return $results;
			
		}
		else
			return array("status","please give the valid information");
	}	

}
