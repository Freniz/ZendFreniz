<?php

/**
 * forum
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';

class Application_Model_forum extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_indexPath='../application/search/forum/';
	protected $_name = 'forum_data';
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
		
	}
	public function createForum($name,$category,$subcategory){
		if(isset($this->authIdentity)){
			$insert_data=array('creator'=>$this->authIdentity->userid,'category'=>$category,'subcategory'=>$subcategory,'name'=>mysql_real_escape_string($name),'vote'=>'a:0:{}');
			$forumid=$this->insert($insert_data);
		}
		
	}
	public function askQuestion($tags,$question,$description){
		if (isset($this->authIdentity->userid)){
			$insert_data=array('question'=>$question,'description'=>$description,'tags'=>implode(' ',$tags),'askedby'=>$this->authIdentity->userid,'vote'=>'a:0:{}','time'=>time());
			$this->_db->insert('forum_questions',$insert_data);
			$id=$this->_db->lastInsertId('forum_questions');
			$insert_search=array('id'=>$id,'question'=>$question,'tags'=>implode(' ',$tags));
			$this->_db->insert('forum_search',$insert_search);	
			$activity_data=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'title'=>'Asked a question in forum','contentid'=>$id,'contenttype'=>'forum','contenturl'=>'forum/'.$id,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'forum_'.$id);
			$this->_db->insert('activity', $activity_data);
			foreach($tags as $val){
				$val=trim($val);
		    	$query='insert into forum_data(tag) values(\''.$val.'\') on duplicate key update noq=noq+1';
		    	$this->_db->query($query);
		    }
		    	
		}
	}
	public function ansQuestion($questionid,$answer,$date){
		if(isset($this->authIdentity->userid)){
			$insert_data=array('answer'=>$answer,'ansby'=>$this->authIdentity->userid,'questionid'=>$questionid,'vote'=>'a:0:{}');
			$this->_db->insert('forum_answer', $insert_data);
			$activity_data=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'title'=>'Answered a question in forum','contentid'=>$questionid,'contenttype'=>'forum','contenturl'=>'forum/'.$questionid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'forum_'.$questionid);
			$this->_db->insert('activity', $activity_data);
			$datepopularity=time()-strtotime($date);
			$popularity=10000/$datepopularity;
			$this->_db->update('forum_questions', array('anscount'=>new Zend_Db_Expr('anscount+1'),'popularity'=>new Zend_Db_Expr("popularity+$popularity")),array('id=?'=>$questionid));
		}
	}
	public function commentAnswer($answerid,$comment){
		if(isset($this->authIdentity)){
			$insert_data=array('comment'=>$comment,'userid'=>$this->authIdentity->userid,'ansid'=>$answerid,'vote'=>'a:0:{}');

			$this->_db->insert('forum_comment', $insert_data);
		}
	}
	public function editQuestion($questionid,$description){
		if(isset($this->authIdentity)){
			$this->_db->update('forum_questions', array('description'=>$description,'modified'=>new Zend_Db_Expr('now()')),array('id=?'=>$questionid,'askedby=?'=>$this->authIdentity->userid));
		}
	}
	public function editAnswer($answerid,$answer){
		if(isset($this->authIdentity)){
			$this->_db->update('forum_answer', array('answer'=>$answer,'modified'=>new Zend_Db_Expr('now()')),array('id=?'=>$answerid,'ansby=?'=>$this->authIdentity->userid));
		}
	}
	public function editComment($commentid,$comment){
		if(isset($this->authIdentity)){
			$this->_db->update('forum_comment', array('comment'=>$comment,'modified'=>new Zend_Db_Expr('now()')),array('id=?'=>$commentid,'userid=?'=>$this->authIdentity->userid));
		}
	}
	public function voteQuestion($questionid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('forum_questions',array('id','vote','date','votecount'))->where('id=?',$questionid);
			$result=$this->_db->fetchAssoc($sql);
			$result=$result[$questionid];
			$vote=unserialize($result['vote']);
			$votecount=$result['votecount']+1;
			if(!in_array($this->authIdentity->userid, $vote)){
				array_push($vote, $this->authIdentity->userid);
				$popularity=20000/(time()-strtotime($result['date']));
				$this->_db->update('forum_questions',array('vote'=>serialize($vote),'votecount'=>$votecount,'popularity'=>new Zend_Db_Expr("(popularity+$popularity)")),array('id=?'=>$questionid));
			}
		}
	}
	public function voteAnswer($id){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('forum_answer',array('id','vote','date','votecount','questionid'))->joinLeft('forum_questions', 'forum_answer.questionid=forum_questions.id',array('qdate'=>'date'))->where('forum_answer.id=?',$id);
			$result=$this->_db->fetchAssoc($sql);
			$result=$result[$id];
			$vote=unserialize($result['vote']);
			$votecount=$result['votecount']+1;
			if(!in_array($this->authIdentity->userid ,$vote)){
				array_push($vote, $this->authIdentity->userid);
				$popularity=10000/(time()-strtotime($result['qdate']));
				$this->_db->update('forum_answer', array('vote'=>serialize($vote),'votecount'=>$votecount),array('id=?'=>$id));
				$this->_db->update('forum_questions',array('popularity'=>new Zend_Db_Expr("popularity+$popularity")),array('id=?'=>$result['questionid']));
			}
		}
		
	}
	public function question($questionid){
		
			$sql=$this->_db->select()->from('forum_questions')
			->joinLeft('user_info','user_info.userid=forum_questions.askedby',array('fname','lname','url as user_url','propic'))
            ->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')	
			->where('id=?',$questionid);
			$result=$this->_db->fetchAssoc($sql);
			$views=1+$result[$questionid]['views'];
			$result[$questionid]['views']=$result[$questionid]['views']+1;
			$this->_db->update('forum_questions',array('views'=>$views),array('id=?'=>$questionid));
			return $result; 
		
	}
	public function getQuestions($questionids){
		
			$sql=$this->_db->select()->from('forum_questions')->where('id in(?)',$questionids);
			$result=$this->_db->fetchAssoc($sql);
			return $result;
		
	}
	public function answers($questionid){
		
			$sql=$this->_db->select()->from('forum_answer')
			->joinLeft('user_info','user_info.userid=forum_answer.ansby',array('fname','lname','url as user_url','propic'))
			->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
			->where('forum_answer.questionid=?',$questionid);
			return $this->processResults($this->_db->fetchAssoc($sql));
		
	}
	
	public function processResults($results){
		
		$postids=array_keys($results);
			$comment_sql=$this->_db->select()->from('forum_comment')->where('ansid in (?)',$postids);
			$comment_result=$this->_db->fetchAssoc($comment_sql);
			foreach ($results as $postid => $values){
			$results[$postid]['comments']=$this->filter_by_value($comment_result, 'ansid', $postid);
			
		}
		return $results;
	}
	
	private function filter_by_value ($array, $index, $value){
		if(is_array($array) && count($array)>0)
		{
			foreach(array_keys($array) as $key){
				$temp[$key] = $array[$key][$index];
	
				if ($temp[$key] == $value){
					$newarray[$key] = $array[$key];
				}
			}
		}
		return $newarray;
	}
	
	
	public function getForums($category,$subcategory){
	$sql=$this->select()->where('category=?',$category)->where('subcategory=?',$subcategory)->order('votecount desc');
	$result=$this->_db->fetchAssoc($sql);
	echo($sql);
	return $result;	
	}
	public function searchQuestions($key,$searchType,$order,$from=0){
	$key=str_replace(',', ' ', $key);
		$sql=$this->_db->select()->from('forum_search','')->joinLeft('forum_questions', 'forum_questions.id=forum_search.id')->joinLeft('user_info', 'user_info.userid=forum_questions.askedby',array('fname','lname','url','propic'))
		->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
		->where(new Zend_Db_Expr('match(forum_search.'.$searchType.') against (\''.$key.'\' in boolean mode)'));
		switch($order){
			case 'popular':
				$popularity=new Zend_Db_Expr('popularity/time');
				$sql=$sql->order($popularity);
				break;
			case 'resolved':
				$sql=$sql->where('bestanswer is Not NULL')->order('date desc');
				break;
			case 'unresolved':
				$sql=$sql->where('bestanswer is NULL')->order('date desc');
				break;
			case 'vote':
				$sql=$sql->order('votecount desc');
			default :
				$sql=$sql->order('date desc');
		}
		$sql=$sql->limit($this->registry->limit,$from);
		return $this->_db->fetchAssoc($sql);
	
	}
	public function getTopics($questionids,$order,$from=0){
		$sql=$this->_db->select()->from('forum_questions')->joinLeft('user_info', 'user_info.userid=forum_questions.askedby',array('fname','lname','url','propic'))
					->joinLeft('image','image.imageid=user_info.propic','url as user_imageurl')
					->where('forum_questions.id in (?)',$questionids);
		switch($order){
			case 'popular':
				$popularity=new Zend_Db_Expr('popularity/time');
				$sql=$sql->order();
				break;
			case 'resolved':
				$sql=$sql->where('bestanswer is Not NULL')->order('date desc');
				break;
			case 'unresolved':
				$sql=$sql->where('bestanswer is NULL')->order('date desc');
				break;
			case 'vote':
				$sql=$sql->order('votecount desc');
			default :
				$sql=$sql->order('date desc');
		}
		$sql=$sql->limit(50,$from);
		return $this->_db->fetchAssoc($sql);
	}
	public function getdetail(){
		$sql=$this->_db->select()->from('places',array('name'))->limit(1000);
		$result=$this->_db->fetchAssoc($sql);
		print_r($result);
		//foreach ($result as $val){
			$this->addviews('sssssssssssssssssss');
		//}
		
	}
	public function addviews($val){
		$sql=$this->_db->select()->from('forum_questions','vote')->where('id=?','5');
			$result=$this->_db->fetchRow($sql);
			$views=unserialize($result['vote']);
			array_push($views, $val);
			//array_merge($views,$views);
			$this->_db->update('forum_questions', array('vote'=>serialize($views)),array('id=?'=>'5'));
			
	}
	public function buildforum()
	{
		ini_set('memory_limit', '1000M');
		set_time_limit(0);
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
		/**
		 * Create index
		 */
		$index = Zend_Search_Lucene::create($this->_indexPath);
	/**
		 * Get all users
		 */
		$sql=$this->_db->select()->from('forum_questions',array('tags','question','id'));
		$result =$this->_db->fetchAssoc($sql);
		
		/**
		 * Create a document for each user and add it to the index
		 */
		/*foreach ($users as $user) {
		 $doc = new Zend_Search_Lucene_Document();
	
		/**
		* Fill document with data
		*/
		/*  $doc->addField(Zend_Search_Lucene_Field::unIndexed('title', $user->id));
		 $doc->addField(Zend_Search_Lucene_Field::text('contents', $user->name));
		//$doc->addField(Zend_Search_Lucene_Field::unIndexed('birthday', $user['dob'], 'UTF-8'));
	
		/**
		* Add document
		*/
		/*$index->addDocument($doc);
		 }
	
		$index->optimize();
		$index->commit();*/
		foreach ($result as $values){
			$doc = new Zend_Search_Lucene_Document();
			$doc->addField(
					Zend_Search_Lucene_Field::keyword('questionid', $values['id']) );
			$doc->addField(
					Zend_Search_Lucene_Field::unStored('questions', $values['question']) );
			$tag=explode(',', $values['tags']);
			$tags=implode(' ', $tag);
			$doc->addField(
					Zend_Search_Lucene_Field::text('tags', $tags) );
			
			$index->addDocument($doc);
		}
		
		
		
		$index->commit();
		
	}
	public function gettags($tags){
		$sql=$this->_db->select()->from('forum_data')->where('tag like (?)',$tags.'%');
		return $this->_db->fetchAssoc($sql);
	}
	public function suggesttags(){
		$sql=$this->_db->select()->from('forum_data')->order(new Zend_Db_Expr('rand()'))->limit('4');
		$results=$this->_db->fetchAssoc($sql);
		return $results;
		
		
	}
	
}
