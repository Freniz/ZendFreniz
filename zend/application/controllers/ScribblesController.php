<?php

class ScribblesController extends Zend_Controller_Action
{

protected $registry;
	protected $authIdentity;
	
 public function init()
    {
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
	public function scribblesAction(){
		if($this->auth->hasIdentity()){
			$getscribbles=new Application_Model_Post($this->registry['DB']);
			$userid=$this->getRequest()->getParam('userid');
			$from=$this->getRequest()->getParam('from');
			if(empty($from) && !(is_int($from) || ctype_digit($from)))
				$from=0;
			$this->view->results=$getscribbles->Scribbles($userid,$from);
			$this->view->mydetails=$this->authIdentity;
			$this->userModel=new Application_Model_Users($this->registry['DB']);

			
			if(!empty($userid)){

				if(isset($this->authIdentity)){

					$this->view->userDetails=$this->userModel->getUserDetails($userid,$this->authIdentity);

				}

				else

					$this->view->userDetails=$this->userModel->getUserDetails($userid);

			}
			
			if( $this->getRequest()->getParam('format')=='xml')

				$this->_helper->viewRenderer->renderScript('scribbles/getuserscribbles.ajax.phtml');

			
		}
	}
	public function docommentscribblesAction(){
		if($this->auth->hasIdentity()){
			$commentscribbles=new Application_Model_Post($this->registry['DB']);
			$postid=$this->getRequest()->getParam('postid');
			$text=$this->getRequest()->getParam('text');
			$this->view->results=$commentscribbles->doCommentscribbles($postid, $text);
			
		}
	}
	public function addscribblesAction(){
		if($this->auth->hasIdentity()){
			$addscribbles=new Application_Model_Post($this->registry['DB']);
			$userid=$this->getRequest()->getParam('userid');
			$text=$this->getRequest()->getParam('text');
			$pt=$this->getRequest()->getParam('pt');
			$cpt=$this->getRequest()->getParam('cpt');
            $results=$addscribbles->addScribbles($userid, $text, $pt,$cpt);
			echo json_encode($results);
		}
		else 
			echo json_encode(array('status'=>'error'));
	}
	public function deletescribblesAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$deletescribbles=new Application_Model_Post($this->registry['DB']);
			$postid=$this->getRequest()->getParam('postid');
			$deletescribbles->deleteScribbles($postid);
		   echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function deletescribblescommentAction(){
		if($this->auth->hasIdentity()){
			$deletescribblescomment=new Application_Model_Post($this->registry['DB']);
			$commentid=$this->getRequest()->getParam('commentid');
			$this->view->results=$deletescribblescomment->deletescribblesComment($commentid);
			
		}
	}
	public function votescribblesAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		
		if($this->auth->hasIdentity()){
			$votescribbles=new Application_Model_Post($this->registry['DB']);
			$postid=$this->getRequest()->getParam('postid');
			$this->view->results=$votescribbles->votescribbles($postid);
			
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function unvotescribblesAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		
		if($this->auth->hasIdentity()){

			$unvotescribbles=new Application_Model_Post($this->registry['DB']);

			$postid=$this->getRequest()->getParam('postid');

			$unvotescribbles->unVotescribbles($postid);

			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function getcommentsAction()
	{
		$postid=$this->getRequest()->getParam('scribbleid');
		$from=$this->getRequest()->getParam('from');
		if(empty($from) && !(is_int($from) || ctype_digit($from)))
			$from=0;
		$staturemodel=new Application_Model_Post($this->registry->DB);
		$this->view->postid=$postid;
		$this->view->results=$staturemodel->getComments($postid, $from);
	}

}

