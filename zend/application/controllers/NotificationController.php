<?php

class NotificationController extends Zend_Controller_Action
{
	protected $registry,$authIdentity;
    public function init()
    {
        /* Initialize action controller here */
    	$auth=Zend_Auth::getInstance();
    	$this->registry=Zend_Registry::getInstance();
    	if($auth->hasIdentity()){
    		$this->authIdentity=$auth->getIdentity();
    	}
    	
    	
    }

    public function indexAction()
    {
    	// action body
        if(isset($this->authIdentity)){
        	$NotificationModel=new Application_Model_Notification($this->registry->DB);
        	$limit=$this->getRequest()->getParam('limit');
        	$from=$this->getRequest()->getParam('from');
        	if(empty($from) && !(is_int($from) || ctype_digit($from)))
        		$from=0;
        	
        	$this->view->results=$NotificationModel->getNotification($this->authIdentity->userid, $limit,$from);
        }
    }

		
    public function notificationAction(){
    	if(isset($this->authIdentity)){
    		$this->view->mydetails=$this->authIdentity;
    	}
    }
    
}

