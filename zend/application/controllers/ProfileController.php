<?php

class ProfileController extends Zend_Controller_Action
{

	protected $registry;
	protected $authIdentity;
	protected $userModel=null;
	protected $userDetails=null;
	public function init()
    {
        /* Initialize action controller here */
    	
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    		$this->authidentity=$this->auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
    	$this->userModel=new Application_Model_Users($this->registry->DB);
    	$userid=$this->getRequest()->getParam('userid');
    	if(!empty($userid)){
    		 if(isset($this->authIdentity)){
    		 	$this->userDetails=$this->userModel->getUserDetails($this->getRequest()->getParam('userid'),$this->authIdentity);
    		 }
    		 else
    		 	$this->userDetails=$this->userModel->getUserDetails($this->getRequest()->getParam('userid'));
    	}
    }

    public function indexAction()
    {
        // action body
    	$this->view->bodyContent=$this->userDetails;
    	$this->view->mydetails=$this->authIdentity;
       if($this->userDetails['profile_type']=='user')
		$this->renderScript('profile/profile.phtml');
       elseif ($this->userDetails['profile_type']=='page')
       	$this->renderScript('profile/leaf.phtml');
        
    }


}

