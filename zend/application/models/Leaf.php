<?php

/**
 * Leaf
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Leaf extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'pages';
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}

	public function createLeaf($pagename,$type,$category,$subcategory,$songurl,$validurl){
		if(isset($this->authIdentity)){
			$myid=$this->authIdentity->userid;
			if ($type=='default' || $type=='normal' ){
						$rand=mt_rand()."_".mt_rand();
						if(strlen($rand)>25)
							$rand= $substr ($rand, 0, 25);
						$admins=array($myid);
						$creator=$myid;
						if($type=='default')
						{
							$admins=array('default');
							$creator='default';
						}
						$ip2c=new Application_Model_ip2c($this->_db);
						$a=array($this->authIdentity->userid);
						$freniz_data=array('userid'=>'leaf_'.$rand,'type'=>'page','url'=>'leaf.php?leafid=leaf_'.$rand,'adminpages'=>'a:0:{}','username'=>$pagename,'createdipadd'=>$ip2c->getIpAdd());
						$pages_data=array('pageid'=>'leaf_'.$rand,'pagename'=>$pagename,'type'=>$type,'category'=>$category,'subcategory'=>$subcategory,'creator'=>$creator,'admins'=> serialize($admins),'vote'=> serialize($a),'date'=>new Zend_Db_Expr('now()'),'url'=>'leaf.php?leafid=leaf_'.$rand,'bannedusers'=>'a:0:{}');
						$pagesinfo_data=array('pageid'=>'leaf_'.$rand,'info'=>'a:0:{}');
						$songurl_data=array('songurl'=>$songurl);
						if($category!='songs'){
							 $this->_db->insert('freniz',$freniz_data );
							   $this->_db->insert('pages', $pages_data);
							    $this->_db->insert('pages_info', $pagesinfo_data);
								$sql=$this->_db->select()->from('user_info','adminpages')->where("userid='$myid'");
								$result=$this->_db->fetchRow($sql);
							   			$admins1=unserialize($result['adminpages']);
											array_push($admins1, "leaf_".$rand);
											if($type=='normal')
											{
												
												$this->_db->update('user_info', array('adminpages'=>serialize($admins1)),array('userid=?'=>$myid));
												$this->_db->update('freniz', array('adminpages'=>serialize($admins1)),array('userid=?'=>$myid));
												$album_data=array('userid'=>'leaf_'.$rand,'specificlist'=>'a:0:{}','hiddenlist'=>'a:o:{}','name'=>'pagepics','date'=>new Zend_Db_Expr('now()'));
												$this->_db->insert('album', $album_data);
											}
										
							}
						else if($category=='songs' && isset($songurl))
						{
							$this->_db->insert('freniz',$freniz_data );
							$this->_db->insert('pages', $pages_data);
							$pagesinfo_data=array_merge($pagesinfo_data,$songurl);
							$this->_db->insert('pages_info',$pagesinfo_data);
							$sql=$this->_db->select()->from('user_info','adminpages')->where("userid='$this->authIdentity->userid'");
							$result=$this->_db->fetchRow($sql);
							$admins1=unserialize($result['adminpages']);
							array_push($admins1, "leaf_".$rand);
							if($type=='normal')
							{
								
								$this->_db->update('user_info', array('adminpages'=>serialize($admins1)),array('userid=?'=>$myid));
								$this->_db->update('freniz', array('adminpages'=>serialize($admins1)),array('userid=?'=>$myid));
								$album_data=array('userid'=>'leaf_'.$rand,'specificlist'=>'a:0:{}','hiddenlist'=>'a:o:{}','name'=>'pagepics','date'=>new Zend_Db_Expr('now()'));
								$this->_db->insert('album', $album_data);
							}
							
						}
											
			       }
			
		}
	}
	public function vote($leafid){
		if(isset($this->authIdentity)){
		
		$sql=$this->select()->from($this->_name,'vote')->where("pageid='$leafid'");
		$result=$this->fetchRow($sql);
			$votes=unserialize($result['vote']);
			$myid=$this->authIdentity->userid;
			if(!in_array($myid, $votes))
			{
				array_push($votes, $myid);
				array_push($this->authIdentity->voted, $leafid);
				$this->update(array('vote'=>serialize($votes)),array('pageid=?'=>$leafid));
				$this->_db->update('friends_vote',array('voted'=>serialize($this->authIdentity->voted)),array('userid=?'=>$myid));
				$activity_data=array('userid'=>$myid,'ruserid'=>$leafid,'contentid'=>$leafid,'title'=>'voted on','contenttype'=>'leaf','contenturl'=>'leaf.php?leafid='.$leafid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'leaf_'.$leafid);
				$this->_db->insert('activity', $activity_data);
				
			}
		}
	}
	public function unVote($leafid){
		if(isset($this->authIdentity)){
			$sql=$this->select()->from($this->_name,'vote')->where("pageid='$leafid'");
			$result=$this->_db->fetchRow($sql);
			$votes=unserialize($result['vote']);
			$myid=$this->authIdentity->userid;
			if(!in_array(array($myid), $votes))
			{
				$votes=array_diff($votes, array($myid));
				$this->authIdentity->voted=array_diff($this->authIdentity->voted, array($leafid));
				
				$this->update(array('vote'=>serialize($votes)),array('pageid=?'=>$leafid));
				$this->_db->update('friends_vote',array('voted'=>serialize($this->authIdentity->voted)),array('userid=?'=>$myid));
				$this->_db->delete('activity',"(userid='".$myid."' and contentid='".$leafid."' and contenttype='leaf' and title='voted on')");
			
			}
			
		}
	}
	
	function CreateGovernmentLeaf($leafname,$type,$admin,$leafurl){
		if(isset($this->authIdentity)){
		
						$pagesurl=explode(",", $leafurl);
						$tabs['Related Sites']['type']='links';
						$tabs['Related Sites']['urls']=$pagesurl;
						$rand=$leafname;
						$admins=array($admin);
						$creator=$admin;
						$myid=$this->authIdentity->userid;
						$ip2c=new Application_Model_ip2c($this->_db);
						$a=array($myid);
						$freniz_data=array('userid'=>$rand,'type'=>'page','url'=>'leaf.php?leafid='.$rand,'adminpages'=>'a:0:{}','username'=>$leafname,'createdipadd'=>$ip2c->getIpAdd());
						$pages_data=array('pageid'=>$rand,'pagename'=>$leafname,'type'=>'govt','category'=>'govt','subcategory'=>'govt','creator'=>$creator,'admins'=> serialize($admins),'vote'=> serialize($a),'date'=>new Zend_Db_Expr('now()'),'url'=>'leaf.php?leafid='.$rand,'bannedusers'=>'a:0:{}');
						$pagesinfo_data=array('pageid'=>'leaf_'.$rand,'info'=>'a:0:{}','tabs'=>serialize($tabs));
						$this->_db->insert('freniz',$freniz_data );
						$this->_db->insert('pages', $pages_data);
						$this->_db->insert('pages_info', $pagesinfo_data);
						$sql=$this->_db->select()->from('user_info','adminpages')->where("userid='$admin'");
						$result=$this->_db->fetchRow($sql);
						
						$admins1=unserialize($result['adminpages']);
						array_push($admins1,$rand);
						if($type=='normal')
						{
							
							$this->_db->update('user_info', array('adminpages'=>serialize($admins1)),array('userid=?'=>$admin));
							$this->_db->update('freniz', array('adminpages'=>serialize($admins1)),array('userid=?'=>$admin));
							$album_data=array('userid'=>$rand,'specificlist'=>'a:0:{}','hiddenlist'=>'a:o:{}','name'=>'pagepics','date'=>new Zend_Db_Expr('now()'));
							$this->_db->insert('album', $album_data);
						}
						
		       }
		}
	
	      
	      public function addLeafInfo($leafid,$category,$subcategory,$request){
	      	if(isset($this->authIdentity)){
	      	
	      	$info=array();
	      	foreach($request as $key=>$value)
	      	{
	      		if($key!='category' && $key!='subcategory' && $key!='pageid' && strlen(trim($value))>0){
	      			$info[$key]=$value;
	      		}
	      	}
	      	$sql=$this->select()->from($this->_name,array('admins','creator'))->where("pageid='$leafid'");
	      	$result=$this->_db->fetchRow($sql);
	      	$admins=unserialize($result['admins']);
	      	$myid=$this->authIdentity->userid;
	      		if(in_array($myid, $admins) || $myid==$result['creator'] || $myid==$leafid){
	      		$this->update(array('category'=>$category,'subcategory'=>$subcategory),array('pageid=?'=>$leafid));
	      		$this->_db->update('pages_info',array('info'=>mysql_real_escape_string(serialize($info))),array('pageid=?'=>$leafid));	
	      		
	      	}
	      	}
	      }
}
