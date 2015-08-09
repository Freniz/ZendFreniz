<?php

class ReviewController extends Zend_Controller_Action
{

	protected $request;
	protected $auth;
	protected $authidentity=null;
	protected $registry,$posts,$images,$videos,$admires;
	public function init()
    {
        /* Initialize action controller here */
    	$this->request=$this->getRequest()->getParams();
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    		$this->authidentity=$this->auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
    	$db=$this->registry['DB'];
	    $this->posts=new Application_Model_Post($db);
    	$this->admires=new Application_Model_Admiration($db);
    	$this->images=new Application_Model_Images($db);
    	$this->videos=new Application_Model_Videos($db);
    	 
    }
	
    public function indexAction()
    {
        // action body
        if(isset($this->authidentity)){
	        $request=$this->request;
	        $from=$request['from'];
	        if(empty($from) && !(is_int($from) || ctype_digit($from)))
	        	$from=0;
	        $this->view->mydetails=$this->authIdentity;
	        switch ($request['type']){
	        	case 'posts':
	        		$this->view->results=$this->posts->tobeReviewed($from);
	        		$this->_helper->viewRenderer->renderScript('scribbles/getuserscribbles.ajax.phtml');
	        		break;
	        	case 'admires':
	        		$this->view->admire=$this->admires->tobeReviewed($from);
	        		$this->_helper->viewRenderer->renderScript('admire/admire.ajax.phtml');
	        		break;
	        	case 'videos':
	        		$this->view->results=$this->videos->tobeReviewed($from);
	        		$this->_helper->viewRenderer->renderScript('videos/getvideos.ajax.phtml');
	        		break;
	        	case 'images':
	        		$this->view->results=$this->images->tobeReviewed($from);
	        		//print_r($this->view->results);
	        		$this->_helper->viewRenderer->renderScript('image/getimages.ajax.phtml');
	        		break;
	        	case 'pinreq':
	        		$this->view->results=$this->images->getPinReq($from);
	        		$this->_helper->viewRenderer->renderScript('image/getimages.ajax.phtml');
	        	case 'pinmereq':
	        		$this->view->results=$this->images->getPinMeReviews($from);
	        		
	        	/*default:
	        		$results['posts']=$this->posts->tobeReviewed();
	        		$results['admires']=$$this->admires->tobeReviewed();
	        		$results['videos']=$this->videos->tobeReviewed();
	        		$results['images']=$this->images->tobeReviewed();
	        		break; */
	        }
	        
        }
    }
    public function approveAction(){
    $this->_helper->viewRenderer->setNoRender();
    	if(isset($this->authidentity)){
    		$request=$this->request;
    		switch ($request['type']){
    			case 'posts':
					if(isset($request['ids'])){
						$postids=explode(',', $request['ids']);
						$this->posts->reviewPosts($postids);
					}			
					else{
						$this->posts->reviewPosts();
					}
    				break;
    			case 'admires':
    				if(isset($request['ids'])){
    					$postids=explode(',', $request['ids']);
    					$this->admires->reviewAdmires($postids);
    				}
    				else{
    					$this->admires->reviewAdmires();
    				}
    				break;
    			case 'videos':
    				if(isset($request['ids'])){
    					$postids=explode(',', $request['ids']);
    					$this->videos->reviewVideos($postids);
    				}
    				else{
    					$this->videos->reviewVideos();
    				}
    				break;
    			case 'images':
    				if(isset($request['ids'])){
    					$postids=explode(',', $request['ids']);
    					$this->images->reviewImages($postids);
    				}
    				else{
    					$this->images->reviewImages();
    				}
    				break;
    			default:
    				break;
    		}
    	echo json_encode(array('status'=>'success'));
    	}
    	else echo json_encode(array('status'=>'error'));
    }
	
    public function denyAction(){
    $this->_helper->viewRenderer->setNoRender();
    	if(isset($this->authidentity)){
    		$request=$this->request;
    	switch ($request['type']){
    			case 'posts':
					if(isset($request['ids'])){
						$postids=explode(',', $request['ids']);
						$this->posts->reviewPosts($postids,false);
					}			
					else{
						$this->posts->reviewPosts(null,false);
					}
    				break;
    			case 'admires':
    				if(isset($request['ids'])){
    					$postids=explode(',', $request['ids']);
    					$this->admires->reviewAdmires($postids,false);
    				}
    				else{
    					$this->admires->reviewAdmires(null,false);
    				}
    				break;
    			case 'videos':
    				if(isset($request['ids'])){
    					$postids=explode(',', $request['ids']);
    					$this->videos->reviewVideos($postids,false);
    				}
    				else{
    					$this->videos->reviewVideos(null,false);
    				}
    				break;
    			case 'images':
    				if(isset($request['ids'])){
    					$postids=explode(',', $request['ids']);
    					$this->images->reviewImages($postids,false);
    				}
    				else{
    					$this->images->reviewImages(null,false);
    				}
    				break;
    			default:
    				break;
    		}
    	echo json_encode(array('status'=>'success'));
    	}
    	else echo json_encode(array('status'=>'error'));
    }
    
    public function getcountAction()
    {
    	if(isset($this->authidentity)){
    		$notificationModel=new Application_Model_Notification($this->registry->DB);
    		echo json_encode($notificationModel->getReviewCount($this->authidentity->userid));
    	}
    	
    }
	public function reviewsAction(){
		if(isset($this->authidentity)){
			$this->view->mydetails=$this->authidentity;
		}
	}
	public function reviewpinmereqAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authidentity){
			$imageid=$this->getRequest()->getParam('imageid');
			$userid=$this->getRequest()->getParam('userid');
			$accept=$this->getRequest()->getParam('accept');
			if($accept=='true')
			{
				$accept=true;
			}
			else $accept=false;
			$ImageModel=new Application_Model_Images($this->registry->DB);
			$this->view->result=$ImageModel->reviewPinMeReq($imageid, $userid,$accept);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	public function reviewpinreqAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authidentity->userid){
			$imageid=$this->getRequest()->getParam('imageid');
			$accept=$this->getRequest()->getParam('accept');
			if($accept=='true')
			{
				$accept=true;
			}
			else $accept=false;
			$ImageModel=new Application_Model_Images($this->registry->DB);
			$this->view->result=$ImageModel->reviewPinReq($imageid,$accept);
			echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
	}
	
}

