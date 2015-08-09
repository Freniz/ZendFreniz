<?php

class StreamsController extends Zend_Controller_Action
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
	public function streamsAction(){
		if($this->auth->hasIdentity()){
			$activity=new Application_Model_Activity($this->registry['DB']);
			$userids=$this->getRequest()->getParam('userids');
			$maxId=$this->getRequest()->getParam('maxid');
			$minId=$this->getRequest()->getParam('minid');
			$criteria=$this->getRequest()->getParam('criteria');
			$activitylist=$this->getRequest()->getParam('activitylist');
			$this->view->mydetails=$this->authIdentity;
			$this->view->results=$activity->getStreams($userids,$criteria,$maxId,$minId,$activitylist);
		}
	}
	public function mystreamsAction(){
		$activity=new Application_Model_Activity($this->registry['DB']);
		$userid=$this->getRequest()->getParam('userid');
		$this->view->results=$activity->myStreams($userid);
		$this->view->mydetails=$this->authIdentity;
		//$this->_helper->viewRenderer->setNoRender();
	}

}

