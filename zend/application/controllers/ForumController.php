<?php
require_once 'MySearch/MyLucene.php';
class ForumController extends Zend_Controller_Action
{
	
	protected $registry;
	protected $authIdentity;
	public function init()
	{
		if(!file_exists(APPLICATION_PATH.'/search')){
			mkdir(APPLICATION_PATH.'/search');
			mkdir(APPLICATION_PATH.'/search/forum');
		}
		/* Initialize action controller here */
		 
		$this->auth=Zend_Auth::getInstance();
		if($this->auth->hasIdentity()){
			$this->authIdentity=$this->auth->getIdentity();
		}
		$this->registry=Zend_Registry::getInstance();
	}
	
	public function indexAction()
	{
		// action body
		if($this->auth->hasIdentity()){
			// Streams logic has to be here
			$this->view->userid=$this->authidentity->password;
			$this->view->onlineusers=$this->authidentity;
		}
		else {
			$this->_redirect('login');
		}
	}
	
	public function createforumAction(){
		if($this->auth->hasIdentity()){
			$createforum=new Application_Model_forum($this->registry['DB']);
			$name=$this->getRequest()->getParam('name');
			$category=$this->getRequest()->getParam('category');
			$subcategory=$this->getRequest()->getParam('subcategory');
			$this->view->results=$createforum->createForum($name, $category, $subcategory);
			
		}
	
	}
	public function askquestionAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$askquestion=new Application_Model_forum($this->registry['DB']);
			$tags=$this->getRequest()->getParam('tags');
			$tags=explode(',', $tags);
			$question=$this->getRequest()->getParam('question');
			$description=$this->getRequest()->getParam('description');
			$this->view->results=$askquestion->askQuestion($tags, $question,$description);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function ansquestionAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$ansquestion=new Application_Model_forum($this->registry['DB']);
			$questionid=$this->getRequest()->getParam('questionid');
			$answer=$this->getRequest()->getParam('answer');
			$date=$this->getRequest()->getParam('date');
			$this->view->results=$ansquestion->ansQuestion($questionid, $answer, $date);
		}
	}
	public function commentanswerAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$commentans=new Application_Model_forum($this->registry['DB']);
			$answerid=$this->getRequest()->getParam('answerid');
			$comment=$this->getRequest()->getParam('comment');
			$this->view->results=$commentans->commentAnswer($answerid, $comment);
		}
	}
	public function editquestionAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$editquestion=new Application_Model_forum($this->registry['DB']);
			$questionid=$this->getRequest()->getParam('questionid');
			$description=$this->getRequest()->getParam('description');
			$this->view->results=$editquestion->editQuestion($questionid, $description);
			
		}
		
	}
	public function editanswerAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$editanswer=new Application_Model_forum($this->registry['DB']);
			$answerid=$this->getRequest()->getParam('answerid');
			$answer=$this->getRequest()->getParam('answer');
			$this->view->results=$editanswer->editAnswer($answerid, $answer);
				
		}	
	}
	public function editcommentAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$editcomment=new Application_Model_forum($this->registry['DB']);
			$commentid=$this->getRequest()->getParam('commentid');
			$comment=$this->getRequest()->getParam('comment');
			$this->view->results=$editcomment->editComment($commentid, $comment);
	
		}
	}
	public function votequestionAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$votequestion=new Application_Model_forum($this->registry['DB']);
			$questionid=$this->getRequest()->getParam('questionid');
			$this->view->results=$votequestion->voteQuestion($questionid);
		}
	}
	public function voteanswerAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$voteanswer=new Application_Model_forum($this->registry['DB']);
			$id=$this->getRequest()->getParam('answerid');
			$this->view->results=$voteanswer->voteAnswer($id);
		}
	}
	public function getforumAction(){
		
			$getforum=new Application_Model_forum($this->registry['DB']);
			$category=$this->getRequest()->getParam('category');
			$subcategory=$this->getRequest()->getParam('subcategory');
			$this->view->results=$getforum->getForums($category, $subcategory);
		
	}
	public function topicAction(){
	
			$topic=new Application_Model_forum($this->registry['DB']);
			$forumid=$this->getRequest()->getParam('forumid');
			$order=$this->getRequest()->getParam('order');
			$this->view->results=$topic->getTopics($forumid, $order);
		
	}
	public function questionAction(){
		
			$getquestion=new Application_Model_forum($this->registry['DB']);
			$questionid=$this->getRequest()->getParam('questionid');
			$this->view->results=$getquestion->question($questionid);
			$this->view->mydetails=$this->authIdentity;
		
	}
	public function answersAction(){
		if($this->auth->hasIdentity()){
			$getanswer=new Application_Model_forum($this->registry['DB']);
			$questionid=$this->getRequest()->getParam('questionid');
			$this->view->results=$getanswer->answers($questionid);
		}
	}
		public function forumAction(){
		
			$this->view->userdetails=$this->authIdentity;
			$suggesttag=new Application_Model_forum($this->registry['DB']);
			$this->view->suggesttags=$suggesttag->suggesttags();
		
	}
	public function viewsAction(){
		if($this->auth->hasIdentity()){
			$adviews=new Application_Model_forum($this->registry['DB']);
			$this->view->results=$adviews->getdetail();
			
		}
	}
	public function searchAction(){
		$key=$this->getRequest()->getParam('key');
		$type=$this->getRequest()->getParam('type');
		$this->view->type=$type;
		$order=$this->getRequest()->getParam('order');
		$from=$this->getRequest()->getParam('from');
		if(empty($from) && !(is_int($from) || ctype_digit($from)))
			$from=0;
		$forumModel=new Application_Model_forum($this->registry->DB);
		$this->view->result=$forumModel->searchQuestions($key, $type, $order,$from);
		$this->view->mydetails=$this->authIdentity;
	}
	public function gettagsAction(){
		$key=$this->getRequest()->getParam('key');
		if(strlen($key)>1){
			$forumModel=new Application_Model_forum($this->registry->DB);
			$this->view->result=$forumModel->gettags($key);
		}
	}
	
}

