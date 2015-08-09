<?php

class InvitesController extends Zend_Controller_Action
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
	public function getinvitesAction(){
		if($this->auth->hasIdentity()){
		$getallinvites=new Application_Model_Invites($this->registry['DB']);
		$this->view->mydetails=$this->authIdentity;
		
		$this->view->results=$getallinvites->getInvites();
			
		}
	}

}

