<?php

class BlogController extends Zend_Controller_Action
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

public function blogAction(){
		if($this->auth->hasIdentity()){
			$getblogs=new Application_Model_Blog($this->registry['DB']);
		
		$this->userModel=new Application_Model_Users($this->registry->DB);

		 
		$userid=$this->getRequest()->getParam('userid');
		$from=$this->getRequest()->getParam('from');
		if(empty($from) && !(is_int($from) || ctype_digit($from)))
				$from=0;
			if(isset($this->authIdentity)){

				$this->userDetails=$this->userModel->getUserDetails($userid,$this->authIdentity);

			}

			else

				$this->userDetails=$this->userModel->getUserDetails($userid);

		}
		$this->view->bodycontent=$this->userDetails;
		$this->view->blogs=$getblogs->getBlogs($userid,$from);
		
		$this->view->mydetails=$this->authIdentity;

		if( $this->getRequest()->getParam('format')=='xml')

			$this->_helper->viewRenderer->renderScript('blog/blog.ajax.phtml');

		
		}
	public function addblogAction(){
		$this->_helper->viewRenderer->setNoRender(true);
    	if($this->auth->hasIdentity()){
    	$postblog=new Application_Model_Blog($this->registry['DB']);
		$title=$this->getRequest()->getParam('title');
		$text=$this->getRequest()->getParam('text');

		$imageurl=null;
		if(!empty($_REQUEST['qqfile']) || !empty($_FILES['qqfile'])){
			$allowedExtensions = array();
			// max file size in bytes
			$sizeLimit = 2 * 1024 * 1024;
			$uploader=new Image_Upload($allowedExtensions,$sizeLimit);
			$upload_result=$uploader->handleUpload('images/');
			$imageurl=$upload_result['imgurl'];
			
		}
		$userid=$this->getRequest()->getParam('userid');
		if(isset($userid) && ($this->authIdentity->userid==$userid || ($this->authIdentity->type=='user' && in_array($userid, $this->authIdentity->adminpages) )))
			$this->results=$postblog->addBlog($title, $text, $imageurl,$userid);
		else 
			$this->results=$postblog->addBlog($title, $text, $imageurl,$this->authIdentity->userid);
		
		echo json_encode($this->results);
		
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function voteblogAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->auth->hasIdentity()){
			$voteblog=new Application_Model_Blog($this->registry['DB']);
			$blogid=$this->getRequest()->getParam('blogid');
			if($voteblog->voteBlog($blogid))
			echo json_encode(array('status'=>'success'));
			else echo json_encode(array('status'=>'error'));
			}
			else echo json_encode(array('status'=>'error'));
		}
	public function unvoteblogAction(){
		if($this->auth->hasIdentity()){
			$unvoteblog=new Application_Model_Blog($this->registry['DB']);
			$blogid=$this->getRequest()->getParam('blogid');
			$this->view->results=$unvoteblog->unvoteblog($blogid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
		}
	public function deleteblogAction(){
		if($this->auth->hasIdentity()){
			$deleteblog=new Application_Model_Blog($this->registry['DB']);
			$blogid=$this->getRequest()->getParam('blogid');
			$this->view->results=$deleteblog->deleteBlog($blogid);
		}
	}
	
}

