<?php

class VideosController extends Zend_Controller_Action
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
		public function videosAction(){
				if($this->auth->hasIdentity()){
					$getvideos=new Application_Model_Videos($this->registry['DB']);
					$userid=$this->getRequest()->getParam('userid');
					$from=$this->getRequest()->getParam('from');
					if(empty($from) && !(is_int($from) || ctype_digit($from)))
						$from=0;
					$this->userModel=new Application_Model_Users($this->registry['DB']);
						
					if(!empty($userid)){
						if(isset($this->authIdentity)){
							$this->view->userDetails=$this->userModel->getUserDetails($userid,$this->authIdentity);
						}
						else
							$this->view->userDetails=$this->userModel->getUserDetails($userid);
					}
					$this->view->mydetails=$this->authIdentity;
						
					$this->view->userid=$userid;
					$this->view->results=$getvideos->videos($userid,$from);
					if( $this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')
		
						$this->_helper->viewRenderer->renderScript('videos/getuservideos.phtml');
		
					
				}
				
			}
	public function getvideosAction(){
		if($this->auth->hasIdentity()){
			$getvideos=new Application_Model_Videos($this->registry['DB']);
			$videoid=$this->getRequest()->getParam('videoid');
			$this->view->results=$getvideos->getVideos($videoid);
			$this->view->videoid=$videoid;
			$this->view->mydetails=$this->authIdentity;
				
			if($this->_request->isXmlHttpRequest() && $this->getRequest()->getParam('format')=='xml')
				$this->_helper->viewRenderer->renderScript('videos/getvideos.ajax.phtml');
				
			
		}
	}
	public function docommentvideoAction(){
		if($this->auth->hasIdentity()){
			$docomment=new Application_Model_Videos($this->registry['DB']);
			$videoid=$this->getRequest()->getParam('videoid');
			$text=$this->getRequest()->getParam('text');
			
			$this->view->results=$docomment->doCommentvideo($videoid, $text);
			
		}
	}
	public function votevideoAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$votevideos=new Application_Model_Videos($this->registry['DB']);
			$videoid=$this->getRequest()->getParam('videoid');
			$this->view->results=$votevideos->votevideo($videoid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
		}
	public function unvotevideoAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$votevideos=new Application_Model_Videos($this->registry['DB']);
			$videoid=$this->getRequest()->getParam('videoid');
			$this->view->results=$votevideos->unVotevideo($videoid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
		}
	public function deletevideoAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$deletevideos=new Application_Model_Videos($this->registry['DB']);
			$videoid=$this->getRequest()->getParam('videoid');
			$deletevid=$deletevideos->deleteVideo($videoid);
			echo $deletevid;
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function deletevideocommentAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$deletevideocomment=new Application_Model_Videos($this->registry['DB']);
			$commentid=$this->getRequest()->getParam('commentid');
			$deletevideocomment->deleteVideoComment($commentid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));	
	}
	
	public function addvideoAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$addvideo=new Application_Model_Videos($this->registry['DB']);
			$title=$this->getRequest()->getParam('title');
			$embeddcode=$this->getRequest()->getParam('embeddcode');
			$ruserid=$this->getRequest()->getParam('userid');
			$addvideo->addVideos($title, $embeddcode, $ruserid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));	}
			
			
			public function getcommentsAction()
			{
				$videoid=$this->getRequest()->getParam('videoid');
				$from=$this->getRequest()->getParam('from');
				if(empty($from) && !(is_int($from) || ctype_digit($from)))
					$from=0;
				$videomodel=new Application_Model_Videos($this->registry->DB);
				$this->view->videoid=$videoid;
				$this->view->results=$videomodel->getComments($videoid, $from);
			}
}

