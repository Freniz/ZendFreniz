<?php

/**
 * Blog
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_Blog extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'blog';
	protected $authIdentity;
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$this->authIdentity=$auth->getIdentity();
		}
		
	}
	
	public function addBlog($title,$text,$imageurl)
	{
		if(isset($this->authIdentity)){
			$privacy=$this->authIdentity->privacy;
			$blog_data=array('userid'=>$this->authIdentity->userid,'blog'=>mysql_real_escape_string($text),'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['blogvisi'],'specificlist'=>$privacy['blogspeci'],'hiddenlist'=>$privacy['bloghidden'],'title'=>mysql_real_escape_string($title));
			if(isset($imageurl)){
			$blog_data=array_merge($blog_data,array('imgurl'=>$imageurl));
			}
			$uptdid=$this->insert($blog_data);
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$uptdid,'title'=>'write blog','contenttype'=>'blog','contenturl'=>'blog.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'blog_'.$uptdid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			return array("status"=>"your blog sucessfully updated");
		   // return $activity;
		}
		else
			return array("status","please give the valid information");
	}
	public function deleteBlog($blogid){
		if(isset($this->authIdentity)){
			$userid=$this->authIdentity->userid;
			$affected_rows=$this->delete("blogid='$blogid' and userid='$userid'");
			if($affected_rows==1){
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->delete("userid='$userid' and contentid='$blogid' and contenttype='blog' and title='write blog'");
			return array("status"=>"Admire removed");
			}
			else
				return array("status"=>"you dont have permission to delete this Admire");
				
		}
		else
			return array("status","please give the valid information");
	}
	
	public function voteBlog($blogid){
		if(isset($this->authIdentity)){
			$result=$this->_db->se;
			 if(!in_array($_SESSION['userid'], $votes)){
			$votes=unserialize($result['vote']);

			 }
			 
			 else
			 	return array("status"=> "you are already voted to this blog");
		}
		else
			return array("status","please give the valid information");
	}
	public function getBlogs($ruserid,$from){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name,array('blogid','title','imgurl','userid','blog','vote','date','pt','specificlist','hiddenlist'))
			->joinLeft('friends_vote','friends_vote.userid=blog.userid','friendlist')
			->joinLeft('user_info','user_info.userid=blog.userid', array('fname','lname','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->where("blog.userid='".$ruserid."'")
			->order('date desc')->limit('500',$from);
			$results=$this->_db->fetchAssoc($sql);
			foreach ($results as $id=>$result){
			    $rusrid=$result['userid'];
			    $privacy=$result['pt'];
			    $specific=  unserialize($result['specificlist']);
			    $hiddenlist=  unserialize($result['hiddenlist']);
			   $rusrfrnds=$result['friendlist'];
			    if((($privacy=='public'||($privacy=='friends' && in_array($rusrid,$this->authIdentity->friends))||($privacy=='fof' && count(array_intersect($rusrfrnds, $this->authIdentity->friends)>=1) )||($privacy=='specific' && in_array($this->authIdentity->userid, $specific)))&& !in_array($rusrid, $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, $hiddenlist))|| $this->authIdentity->userid==$rusrid ){

    		}
    		else unset($results[$id]);
		}
		return $results;
		}
		else
			return array("status","please give the valid information");
	}

}
