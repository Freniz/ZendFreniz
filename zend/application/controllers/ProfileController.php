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
    		$this->authIdentity=$this->auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
    	$this->userModel=new Application_Model_Users($this->registry->DB);
		$this->tab=$this->getRequest()->getParam('tab');
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
        $this->view->tab=$this->tab;
    	$this->view->bodyContent=$this->userDetails;
    	$this->view->mydetails=$this->authIdentity;
    	
       if($this->userDetails['profile_type']=='user')
		$this->renderScript('profile/profile.phtml');
       elseif ($this->userDetails['profile_type']=='page')
       	$this->renderScript('profile/leaf.phtml');
       	else
       	$this->_redirect('');
        
    }
    public function addfriendsAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if($this->auth->hasIdentity()){
    		$addfrd=new Application_Model_Friend($this->registry['DB']);
    		$ruserid=$this->getRequest()->getParam('userid');
    		$this->view->results=$addfrd->sendFriendsRequest($ruserid);
    		echo json_encode(array('status'=>'success'));
    	}
    	else echo json_encode(array('status'=>'error'));
    }
     public function cancelfriendsAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if($this->auth->hasIdentity()){
    		$cancelfrd=new Application_Model_Friend($this->registry['DB']);
    		$ruserid=$this->getRequest()->getParam('userid');
    		$this->view->results=$cancelfrd->cancelFriendsRequest($ruserid);
    		echo json_encode(array('status'=>'success'));
    	}
    	else echo json_encode(array('status'=>'error'));
    }
    public function acceptfriendsAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if($this->auth->hasIdentity()){
    		$acceptfrd=new Application_Model_Friend($this->registry['DB']);
    		$ruserid=$this->getRequest()->getParam('userid');
    		$this->view->results=$acceptfrd->addFriends($ruserid);
    		echo json_encode(array('status'=>'success'));
    	}
    	else echo json_encode(array('status'=>'error'));
    }
    public function denyfriendsAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if($this->auth->hasIdentity()){
    		$denyfrd=new Application_Model_Friend($this->registry['DB']);
    		$ruserid=$this->getRequest()->getParam('userid');
    		$this->view->results=$denyfrd->IgnorefriendsRequest($ruserid);
    		echo json_encode(array('status'=>'success'));
    	}
    	else echo json_encode(array('status'=>'error'));
    }
	public function removefriendsAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->auth->hasIdentity()){
			$removfrd=new Application_Model_Friend($this->registry['DB']);
			$ruserid=$this->getRequest()->getParam('userid');
			$this->view->results=$removfrd->removeFriends($ruserid);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function uservoteAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->auth->hasIdentity()){
		$voteuser=new Application_Model_UserInfo($this->registry['DB']);
		$userid=$this->getRequest()->getParam('userid');
		$this->view->result=$voteuser->userVote($userid);
			
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function userunvoteAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->auth->hasIdentity()){
			$voteunuser=new Application_Model_UserInfo($this->registry['DB']);
			$userid=$this->getRequest()->getParam('userid');
			$this->view->result=$voteunuser->userUnvote($userid);
				
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function getminiprofileAction(){
		if($this->auth->hasIdentity()){
			$getmini=new Application_Model_Users($this->registry['DB']);
			$ids=$this->getRequest()->getParam('ids');
			$this->view->results=$getmini->getminiprofile($ids);
		}
	}
	public function getfriendsrequestAction(){
		if($this->auth->hasIdentity()){
			$getreq=new Application_Model_Friend($this->registry['DB']);
			$this->view->results=$getreq->getfriendsrequest();
		}
	}
	public function getfriendslistAction(){
		if($this->auth->hasIdentity()){
			$frd=new Application_Model_Friend($this->registry['DB']);
			$userid=$this->getRequest()->getParam('userid');
			$this->view->results=$frd->getfriends($userid);
		}
	}

}

