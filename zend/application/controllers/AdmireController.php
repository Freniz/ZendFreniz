<?php

class AdmireController extends Zend_Controller_Action
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
public function admireAction(){
		if($this->auth->hasIdentity() && $this->authIdentity->type=='user'){
			$getadmire=new Application_Model_Admiration($this->registry['DB']);
			$this->userModel=new Application_Model_Users($this->registry->DB);

				

			$userid=$this->getRequest()->getParam('userid');
			$from=$this->getRequest()->getParam('from');

			if(empty($from) && !(is_int($from) || ctype_digit($from)))
				$from=0;

			if(!empty($userid)){

				if(isset($this->authIdentity)){

					$this->userDetails=$this->userModel->getUserDetails($userid,$this->authIdentity);

				}

				else

					$this->userDetails=$this->userModel->getUserDetails($userid);

			}

			$this->view->bodycontent=$this->userDetails;
			$this->view->admire=$getadmire->getAdmiration($userid, $from);
			$this->view->mydetails=$this->authIdentity;
			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')

				$this->_helper->viewRenderer->renderScript('admire/admire.ajax.phtml');
		}
	}
    public function addadmirationAction(){
    		$this->_helper->viewRenderer->setNoRender(true);
    		if($this->auth->hasIdentity()){
    			$postadmire=new Application_Model_Admiration($this->registry['DB']);
    			$userid=$this->getRequest()->getParam('userid');
    			$text=$this->getRequest()->getParam('text');
    				echo json_encode($postadmire->addAdmiration($userid, $text));
    			}
    			else echo json_encode(array('status'=>'error'));
    	
    }
	public function voteadmireAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$voteadmire=new Application_Model_Admiration($this->registry['DB']);
			$testyid=$this->getRequest()->getParam('admireid');
			$voteadmire->voteadmire($testyid);
		   echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
		
	}
	public function unvoteadmireAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$unvoteadmire=new Application_Model_Admiration($this->registry['DB']);
			$testyid=$this->getRequest()->getParam('admireid');
			$unvoteadmire->unVoteadmire($testyid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	
	}
	public function deleteadmireAction(){
		if($this->auth->hasIdentity()){
			$deleteadmire=new Application_Model_Admiration($this->registry['DB']);
			$admireid=$this->getRequest()->getParam('admireid');
			$this->view->results=$deleteadmire->deleteAdmire($admireid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
}

