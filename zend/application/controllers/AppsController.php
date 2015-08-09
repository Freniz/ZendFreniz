<?php

class AppsController extends Zend_Controller_Action
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
	public function updatediaryAction(){
		 
		if($this->auth->hasIdentity()){
		$updatediary=new Application_Model_Apps($this->registry['DB']);
		$date=$this->getRequest()->getParam('date');
		$notes=$this->getRequest()->getParam('notes');
		$this->view->results=$updatediary->UpdateDiary($date, $notes);
		}
	}
	public function updateslambookAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		 
		if($this->auth->hasIdentity()){
			$updateslambook=new Application_Model_Apps($this->registry['DB']);
			$request=$this->getRequest()->getParams();
			$userid=$request['userid'];
			unset($request['userid']);
			unset($request['controller']);
			unset($request['action']);
			if($updateslambook->UpdateSlambook($userid, $request))
			echo json_encode(array('status'=>'success'));
			else echo json_encode(array('status'=>'error'));
		}
		
	}
	public function diaryAction(){
		if($this->auth->hasIdentity()){
			$getdiary=new Application_Model_Apps($this->registry['DB']);
			$date=$this->getRequest()->getParam('date');
			$this->view->diary=$getdiary->diary($date);
			$this->view->mydetails=$this->authIdentity;
			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')
				$this->_helper->viewRenderer->renderScript('apps/getdiary.phtml');
			
			
		}
	}
	public function getslambookAction(){
		if($this->auth->hasIdentity()){
			$getslambook=new Application_Model_Apps($this->registry['DB']);
			$this->view->slambook=$getslambook->slambook();
		}
	}
	public function slambookAction(){
		if($this->auth->hasIdentity()){
			$this->view->mydetails=$this->authIdentity;
			$getslambook=new Application_Model_Apps($this->registry['DB']);
			$this->view->slambook=$getslambook->slambook();
		}
	}
	public function addslambookAction(){
		if($this->auth->hasIdentity()){
			$userid=$this->getRequest()->getParam('userid');
			$this->view->mydetails=$this->authIdentity;
			
			$this->userModel=new Application_Model_Users($this->registry['DB']);
				$friendslist=$this->authIdentity->friends;
				if (in_array($userid, $friendslist)){
			if(!empty($userid)){
				if(isset($this->authIdentity)){
					$this->view->userdetails=$this->userModel->getUserDetails($userid,$this->authIdentity);
				}
				else
					$this->view->userdetails=$this->userModel->getUserDetails($userid);
			}
				}
				
						
		}
	}
}

