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
			
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
		$this->registry=Zend_Registry::getInstance();
		
	}
	public function addBlog($title,$text,$imageurl,$userid)
	{
		if(isset($this->authIdentity)){
			$privacy=$this->authIdentity->privacy;
			if(($this->authIdentity->userid !=$userid)){
					$privacy['blogvisi']='public';
					$privacy['blogspeci']='a:0:{}';
					$privacy['bloghidden']='a:0:{}';
			}
			else if($this->authIdentity->type=='page')
			{
				$privacy['blogvisi']='public';
				$privacy['blogspeci']='a:0:{}';
				$privacy['bloghidden']='a:0:{}';
			}

			$blog_data=array('userid'=>$userid,'blog'=>$text,'date'=>new Zend_Db_Expr('Now()'),'vote'=>'a:0:{}','pt'=>$privacy['blogvisi'],'specificlist'=>$privacy['blogspeci'],'hiddenlist'=>$privacy['bloghidden'],'title'=>$title);
			$blog_data['dontnotify']='a:0:{}';

			if(isset($imageurl)){
			$blog_data=array_merge($blog_data,array('imgurl'=>$imageurl));
			}
			$uptdid=$this->insert($blog_data);

			$activity=array('userid'=>$userid,'ruserid'=>$userid,'contentid'=>$uptdid,'title'=>'write blog','contenttype'=>'blog','contenturl'=>'blog.php?statureid='.$uptdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'blog_'.$uptdid);

			$activityModel=new Application_Model_Activity($this->_db);

			$activityModel->insert($activity);
			if($imageurl){
			return array("blogid"=>$uptdid,"time"=>date('c'),'content'=>$text,"title"=>$title,"status"=>'success','imageurl'=>$imageurl);
			}else 
				return array("blogid"=>$uptdid,"time"=>date('c'),'content'=>$text,"title"=>$title,"status"=>'success');
					
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
			$result=$this->find($blogid)->toArray();
			$result=$result[0];
			$vote=unserialize($result['vote']);
	
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "blogid='$blogid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['userid'],'contentid'=>$blogid,'title'=>'voted on','contenttype'=>'blog','contenturl'=>'blog.php?blogid='.$blogid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'blog_'.$blogid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			
			return true;
			
		}
	}
	public function unVoteblog($blogid)
	{
		if(isset($this->authIdentity)){
			$result=$this->find($blogid);
			if($result){
				$result=$result[0];
	
				$vote=unserialize($result['vote']);
				if(in_array($this->authIdentity->userid, $vote)){
					$vote=array_diff($vote, array($this->authIdentity->userid));
					$updatedata=array('vote'=>serialize($vote));
					$this->update($updatedata, array('blogid=?'=>$blogid));
				}
			}
		}
	}
	public function getdeveloperBlogs($ruserid,$from){
			$sql=$this->_db->select()->from($this->_name,array('blogid','title','imgurl','userid','blog','vote','date','pt','specificlist','hiddenlist'))
	
			->joinLeft('friends_vote','friends_vote.userid=blog.userid','friendlist')
	
			->joinLeft('freniz','freniz.userid=blog.userid', array('username'))
	
			->joinLeft('image','image.imageid=freniz.propic','url as imageurl')
	
			->where("blog.userid in (?)",$ruserid)
	
			->order('date desc')->limit($this->registry->limit,$from);
			$results=$this->_db->fetchAssoc($sql);
			if(count($results)==$this->registry->limit)
				$final_results['loadmore']=true;
			else
				$final_results['loadmore']=false;
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
			$final_results['results']=$results;
			return $final_results;
		
	}
	
	
public function getBlogs($ruserid,$from){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name,array('blogid','title','imgurl','userid','blog','vote','date','pt','specificlist','hiddenlist'))

			->joinLeft('friends_vote','friends_vote.userid=blog.userid','friendlist')

			->joinLeft('freniz','freniz.userid=blog.userid', array('username'))

			->joinLeft('image','image.imageid=freniz.propic','url as imageurl')

			->where("blog.userid='".$ruserid."'")

			->order('date desc')->limit($this->registry->limit,$from);
			$results=$this->_db->fetchAssoc($sql);
			if(count($results)==$this->registry->limit)
				$final_results['loadmore']=true;
			else
				$final_results['loadmore']=false;
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
		$final_results['results']=$results;
		return $final_results;
		}
		else

			return array("status","please give the valid information");
	}

	public function getBlogsArray($blogids){
		$sql=$this->_db->select()->from($this->_name,array('blogid','title','imgurl','userid','blog','vote','date','pt','specificlist','hiddenlist'))
		->joinLeft('friends_vote','friends_vote.userid=blog.userid','friendlist')
		->joinLeft('freniz','freniz.userid=blog.userid', array('username'))
		->joinLeft('image','image.imageid=freniz.propic','url as imageurl')
		->where('blog.blogid in (?) and accepted=\'yes\'',$blogids);
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
}
