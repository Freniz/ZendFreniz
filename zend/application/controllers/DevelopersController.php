<?php

class DevelopersController extends Zend_Controller_Action
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

public function developersAction(){
			$getblogs=new Application_Model_Blog($this->registry['DB']);
		
			$userid=array('abdulnizam','s.mohamedmeeran');
			$from=$this->getRequest()->getParam('from');
			if(empty($from) && !(is_int($from) || ctype_digit($from)))
				$from=0;
		$this->view->blogs=$getblogs->getdeveloperBlogs($userid, $from);
		
		$this->view->mydetails=$this->authIdentity;
		$string=md5('freniz');
		if(($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml') || ($this->getRequest()->getParam('Accesscode')==$string))
				$this->_helper->viewRenderer->renderScript('blog/blog.ajax.phtml');

		
		}
		public function hireusAction(){
			$this->view->mydetails=$this->authIdentity;
				
		}

}