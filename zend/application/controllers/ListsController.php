<?php

class ListsController extends Zend_Controller_Action
{

	protected $authIdentity,$registry;
    public function init()
    {
    	$auth=Zend_Auth::getInstance();
    	if($auth->hasIdentity()){
    		$this->authIdentity=$auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $this->_helper->viewRenderer->setNoRender();
        if(isset($this->authIdentity)){
        	echo json_encode($this->authIdentity->interestedlists);
        }
    }
    public function displayAction(){
    	if(isset($this->authIdentity)){
    		$id=$this->getRequest()->getParam('name');
    		if(!empty($this->authIdentity->intrestedlists[$id])){
    			$userids=explode(',',$this->authIdentity->interestedlists[$id]);
    			$activityModel=new Application_Model_Activity($this->registry->DB);
    			$this->view->results=$activityModel->getStreams($userids);
    			$userModel=new Application_Model_Users($this->registry->DB);
    			$this->view->users=$userModel->getminiprofile($userids);
    			
    			
    		}
    	}
    }
    public function updateAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if(isset($this->authIdentity)){
    		$id=$this->getRequest()->getParam('name');
    		$userid=$this->getRequest()->getParam('userid');
    		$action=$this->getRequest()->getParam('type');
    		if(isset($this->authIdentity->interestedlists[$id]) && !empty($action)){
    			if(!empty($this->authIdentity->interestedlists[$id]))
    			$userids=explode(',',$this->authIdentity->interestedlists[$id]);
    			else
    				$userids=array();
    			if($action=='add'){
    			if(!in_array($userid,$userids)){
    				array_push($userids,$userid);
    			}
    			}
    			elseif($action=='remove'){
    			if(in_array($userid,$userids)){
    				$userids=array_diff($userids,array($userid));
    			}
    			}
    				$this->authIdentity->interestedlists[$id]=implode(',', $userids);
    				$userModel=new Application_Model_UserInfo($this->registry->DB);
    				$userModel->update(array('interestedlists'=>serialize($this->authIdentity->interestedlists)), array('userid=?'=>$this->authIdentity->userid));
    			
    		}
    	}
    }
    public function createAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if(isset($this->authIdentity)){
    		$id=$this->getRequest()->getParam('name');
    		if(!isset($this->authIdentity->interestedlists[$id])){
    			$this->authIdentity->interestedlists[$id]='';
    			$userModel=new Application_Model_UserInfo($this->registry->DB);
    			$userModel->update(array('interestedlists'=>serialize($this->authIdentity->interestedlists)), array('userid=?'=>$this->authIdentity->userid));
    			 
    		}
    	}
    }
    public function deleteAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	if(isset($this->authIdentity)){
    		$id=$this->getRequest()->getParam('name');
    		if(array_key_exists($id, $this->authIdentity->interestedlists)){
    			unset($this->authIdentity->interestedlists[$id]);
    			$userModel=new Application_Model_UserInfo($this->registry->DB);
    			$userModel->update(array('interestedlists'=>serialize($this->authIdentity->interestedlists)), array('userid=?'=>$this->authIdentity->userid));
    			 
    		}
    	}
    }


}

