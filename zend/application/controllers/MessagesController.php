<?php

class MessagesController extends Zend_Controller_Action
{

	protected $registry;
	protected $authIdentity;
	protected $getmessages=null;
	public function init()
    {
        /* Initialize action controller here */
    	
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    		$this->authIdentity=$this->auth->getIdentity();
    	}else 
    		$this->_redirect('http://www.freniz.com');
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
    
    public function sendmessagesAction(){
    	if($this->auth->hasIdentity()){
    		$this->_helper->viewRenderer->setNoRender(true);
    		
    		$sendmsgusr=new Application_Model_Messages($this->registry['DB']);
    			
    		$suserid=$this->getRequest()->getParam('userid');
    		$message=$this->getRequest()->getParam('message');
    		echo json_encode($sendmsgusr->sendMessages($suserid, $message));
		
		}
		else echo json_encode(array('status'=>'error'));
        }
        public function deletemessageAction(){
        	if($this->auth->hasIdentity()){
        		$this->_helper->viewRenderer->setNoRender(true);
        		$deletemess=new Application_Model_Messages($this->registry['DB']);
        		$ruserid=$this->getRequest()->getParam('userid');
        		$deletemess->deleteAllMessages($ruserid);
        		echo json_encode(array('status'=>'success'));
        		
        		}
        		else echo json_encode(array('status'=>'error'));    
            }
 public function messagesAction(){
 	if($this->auth->hasIdentity()){
 		$this->view->mydetails=$this->authIdentity;
 		$ruserid=$this->getRequest()->getParam('userid');
 		if(!empty($ruserid)){
 			$this->userModel=new Application_Model_Users($this->registry['DB']);
 			if(isset($this->authIdentity)){
 				$this->view->userDetails=$this->userModel->getUserDetails($ruserid,$this->authIdentity);
 			}
 			else
 				$this->view->userDetails=$this->userModel->getUserDetails($ruserid);
 			
 			$getusermess=new Application_Model_Messages($this->registry['DB']);
 			$this->view->results=$getusermess->getUserMessages($ruserid);
 			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')
 				$this->_helper->viewRenderer->renderScript('messages/getusermessages.ajax.phtml');
 		}else{
 			$getmessages=new Application_Model_Messages($this->registry['DB']);
 			$this->view->results= $getmessages->getMessages();
 			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')
 				$this->_helper->viewRenderer->renderScript('messages/messages.ajax.phtml');
 		}
 		 
 		
    	
    	//$this->_helper->viewRenderer->setNoRender(true);
 		//$this->_helper->viewRenderer->setNoRender(true);
    	//$this->getResponse()->setBody(json_encode($places->uploadImage($album)));
    	//$this->view->results= $places->getUserDetailts('leaf_1164721637_31408804');
    	//$this->view->results= $places->doComment(1, 'ifadfja');
 	}
 	
    }
  public function getusermessagesAction(){
  	if($this->auth->hasIdentity()){
  	//$this->renderScript('messages/getusermessages.phtml');
      	
  		
  	}
  	
  	
  }
  
  public function sentmessagesAction(){
  	if($this->auth->hasIdentity()){
  	$sentmess=new Application_Model_Messages($this->registry['DB']);
  	$this->view->mydetails=$this->authIdentity;
  	
  	$this->view->results=$sentmess->sentMessages();
  		
  	
  	
  	}
  	
  }

}

