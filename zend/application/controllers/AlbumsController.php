<?php

class AlbumsController extends Zend_Controller_Action
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
	public function albumsAction(){
		if($this->auth->hasIdentity()){
			$getalbums=new Application_Model_Album($this->registry['DB']);
			$userid=$this->getRequest()->getParam('userid');
			$this->view->mydetails=$this->authIdentity;
			$this->view->myid=$userid;
			$this->view->results=$getalbums->albums($userid);
			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')
				$this->_helper->viewRenderer->renderScript('albums/getalbums.phtml');
			
		}
	}
	public function createalbumAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$create=new Application_Model_Album($this->registry['DB']);
			$albumname=$this->getRequest()->getParam('name');
			$create->createAlbum($albumname);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
		}

}

