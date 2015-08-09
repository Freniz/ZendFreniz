<?php

class StaturesController extends Zend_Controller_Action
{
	protected $registry;
	protected $authIdentity;
	protected $auth;
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
public function getuserstaturesAction(){
		if($this->auth->hasIdentity()){
			$getuserstature=new Application_Model_Stature($this->registry['DB']);
			$userid=$this->getRequest()->getParam('userid');
			$from=$this->getRequest()->getParam('from');
			if(empty($from) && !(is_int($from) || ctype_digit($from)))
				$from=0;
			$this->view->mydetails=$this->authIdentity;
			$this->view->results=$getuserstature->getUserStatures($userid,$from);

			
			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')

				$this->_helper->viewRenderer->renderScript('statures/getusersatures.ajax.phtml');

				
			
		}
	}
	public function addstaturesAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		
		if($this->auth->hasIdentity()){
			$addstatures=new Application_Model_Stature($this->registry['DB']);
			$text=$this->getRequest()->getParam('text');
			$visi=$this->getRequest()->getParam('visi');
			$cpt=$this->getRequest()->getParam('cpt');
			echo json_encode($addstatures->addStatures($text, $visi, $cpt));
			}
			else echo json_encode(array('status'=>'error'));
		
	}
	public function deletestatureAction(){
		if($this->auth->hasIdentity()){
			$deletestatures=new Application_Model_Stature($this->registry['DB']);
			$statureid=$this->getRequest()->getParam('statureid');
			$this->view->results=$deletestatures->deleteStature($statureid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
		}
	public function dostaturecommentAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$staturecomment=new Application_Model_Stature($this->registry['DB']);
			$statureid=$this->getRequest()->getParam('statureid');
			$text=$this->getRequest()->getParam('text');
			echo json_encode($staturecomment->dostatureComment($statureid, $text));
			}
			else echo json_encode(array('status'=>'error'));	
	}
	public function deletestaturecommentAction(){
		if($this->auth->hasIdentity()){
			$deletestaturecomment=new Application_Model_Stature($this->registry['DB']);
			$commentid=$this->getRequest()->getParam('commentid');
			$this->view->results=$deletestaturecomment->deletestatureComment($commentid);
			
		}
	}
	public function votestatureAction(){
		if($this->auth->hasIdentity()){
			$votestature=new Application_Model_Stature($this->registry['DB']);
			$statureid=$this->getRequest()->getParam('statureid');
			$this->view->results=$votestature->votestature($statureid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function unvotestatureAction(){
		if($this->auth->hasIdentity()){
			$unvotestature=new Application_Model_Stature($this->registry['DB']);
			$statureid=$this->getRequest()->getParam('statureid');
			$this->view->results=$unvotestature->unVotestature($statureid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function getcommentsAction()
	{
		$statureid=$this->getRequest()->getParam('statureid');
		$from=$this->getRequest()->getParam('from');
		if(empty($from) && !(is_int($from) || ctype_digit($from)))
			$from=0;
		$staturemodel=new Application_Model_Stature($this->registry->DB);
		$this->view->statureid=$statureid;
		$this->view->results=$staturemodel->getComments($statureid, $from);
	}
}

