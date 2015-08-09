<?php

class PostController extends Zend_Controller_Action
{
	protected $request;
	protected $auth;
	protected $authidentity=null;
	protected $registry;
	public function init()
    {
        /* Initialize action controller here */
    	$this->request=$this->getRequest();
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    		$this->authidentity=$this->auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
    }

    public function indexAction()
    {
        // action body
        $this->_redirect('');
    }
    public function addstatureAction(){
    	$this->_helper->viewRenderer->setNoRender(true);
    	if($this->auth->hasIdentity()){
    		$text=trim($this->request->getParam('text'));
    		if(count_chars($text)>0){
    			$stature=new Application_Model_Stature($this->registry['DB']);
    			$stature->addStature($text);
    		}
    	}
    	$this->_redirect('');
    }


}

