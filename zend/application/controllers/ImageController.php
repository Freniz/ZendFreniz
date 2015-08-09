<?php

class ImageController extends Zend_Controller_Action
{
	protected $auth;
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

			$this->view->userid=$this->authIdentity->password;

			$this->view->onlineusers=$this->authIdentity;

		}

		else {

			$this->_redirect('login');

		}

	}
	public function getimagesAction(){
			if($this->auth->hasIdentity()){
				$getimages=new Application_Model_Images($this->registry['DB']);
				$albumid=$this->getRequest()->getParam('albumid');
				$from=$this->getRequest()->getParam('from');
				$userid=$this->getRequest()->getParam('userid');
				if(empty($from) && !(is_int($from) || ctype_digit($from)))
					$from=0;
					
				$this->view->results=$getimages->getImages($albumid,$from,'album',$userid);
				$this->view->myid=$this->authIdentity->userid;
				$this->view->mydetails=$this->authIdentity;
				if( $this->getRequest()->getParam('format')=='xml')
					$this->_helper->viewRenderer->renderScript('image/getimages.ajax.phtml');
			}
		}
	
	public function uploadimageAction(){
		if($this->auth->hasIdentity()){
			$uploadimage=new Application_Model_Images($this->registry['DB']);
			$albumid=$this->getRequest()->getParam('album');
			$description=$this->getRequest()->getParam('text');
			$this->view->results=$uploadimage->uploadImage($albumid,$description);
		
			
		}
	}
	
	
	public function imageAction(){
		if($this->auth->hasIdentity()){
			$getphoto=new Application_Model_Images($this->registry['DB']);
			$albumid=$this->getRequest()->getParam('albumid');
			$userid=$this->getRequest()->getParam('userid');
			$this->view->results=$getphoto->getImages($albumid,null,'album',$userid);
			$this->view->mydetails=$this->authIdentity;
			
		}
	}
	public function cropAction(){
		if($this->auth->hasIdentity()){
			
		$crop=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$type=$this->getRequest()->getParam('type');
			$this->view->type=$type;
			if($type=='propic'){
				$this->view->init='1/1';
			}else{
			$this->view->init='2/1';}
			$this->view->results=$crop->crop($imageid);
			$this->view->mydetails=$this->authIdentity;
		}
	}
	public function setprofilepictureAction(){
		$this->_helper->viewRenderer->setNoRender();
		if(isset($this->authIdentity)){
			$setpropic=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$deletesrc=$this->getRequest()->getParam('deletesrc');
			$x=$this->getRequest()->getParam('x');
			$y=$this->getRequest()->getParam('y');
			$width=$this->getRequest()->getParam('width');
			$height=$this->getRequest()->getParam('height');
			$results=$setpropic->SetProfilePicture($imageid, $deletesrc, $x, $y, $width, $height);
		echo json_encode($results);
			}
			else echo json_encode(array('status'=>'error'));
	}
public function setsecondarypictureAction(){

		if($this->auth->hasIdentity()){

			$setsecpic=new Application_Model_Images($this->registry['DB']);

			$imageid=$this->getRequest()->getParam('imageid');

			$deletesrc=$this->getRequest()->getParam('deletesrc');

			$x=$this->getRequest()->getParam('x');

			$y=$this->getRequest()->getParam('y');

			$width=$this->getRequest()->getParam('width');

			$height=$this->getRequest()->getParam('height');
			$secpicno=$this->getRequest()->getParam('secpicno');
			$top=$this->getRequest()->getParam('top');

			$this->view->results=$setsecpic->setSecondarypicture($imageid, $deletesrc, $x, $y, $width, $height, $secpicno,$top);

		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));

	}
	public function addpinAction(){
		$this->_helper->viewRenderer->setNoRender(true);

		if($this->auth->hasIdentity()){

			$addpin=new Application_Model_Images($this->registry['DB']);

			$imageid=$this->getRequest()->getParam('imageid');

			$userids=explode(',',$this->getRequest()->getParam('userids'));
			$this->view->results=$addpin->addpin($imageid, $userids);

		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));

	}
	public function pinmereqAction(){
		$this->_helper->viewRenderer->setNoRender(true);

		if($this->auth->hasIdentity()){
			$pinmereq=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$this->view->results=$pinmereq->pinmereq($imageid);
			
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function unpinAction(){
		$this->_helper->viewRenderer->setNoRender(true);
	
		if($this->auth->hasIdentity()){
			$unpin=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$userid=$this->getRequest()->getParam('userid');
			$this->view->results=$unpin->unpin($imageid, $userid);
				
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	
	public function getpinmereqAction(){

		if($this->auth->hasIdentity()){

			$getpinmereq=new Application_Model_Images($this->registry['DB']);

			$imageid=$this->getRequest()->getParam('imageid');

			$this->view->results=$getpinmereq->getPinMeReq($imageid);

		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));

	}
	

	public function getpinreqAction(){

		if($this->auth->hasIdentity()){

			$getpinreq=new Application_Model_Images($this->registry['DB']);

			$this->view->results=$getpinreq->getPinReq();

		}

	}
	
	public function uploadpropicAction(){
		$this->_helper->viewRenderer->setNoRender();
		if(isset($this->authIdentity))
		{
			if(!empty($_FILES['qqfile'])){
				$request=$this->getRequest()->getParams();
				$allowedExtensions = array();
				// max file size in bytes
				$sizeLimit = 2 * 1024 * 1024;
				$uploader=new Image_Upload($allowedExtensions,$sizeLimit);
				$uploader_result=$uploader->handleUpload('images/',false);
				if(!isset($uploader_result['error']))
				{
					$ImageModel=new Application_Model_Images($this->registry->DB);
					$imageurl=$uploader_result['imgurl'];
					$ImageModel->uploadPropic($imageurl,$request);
					echo json_encode(array('status'=>'success'));
				}
				else 
					echo json_encode(array('status'=>$uploader_result['error']));
			}
			else
				echo json_encode(array('status'=>'No files were uploaded'));
		}
		else 
			echo json_encode(array('status'=>'Please Login'));
	}
	
	public function commentAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$imageid=$this->getRequest()->getParam('imageid');
			$text=$this->getRequest()->getParam('text');
			if(!empty($text)){
				$ImageModel=new Application_Model_Images($this->registry->DB);
				$result=$ImageModel->doComment($imageid, $text);
			}
			echo json_encode($result);
			}
			else echo json_encode(array('status'=>'error'));
	}
	
	public function getcommentsAction(){
		if($this->authIdentity->userid){
			$imageid=$this->getRequest()->getParam('imageid');
			$from=$this->getRequest()->getParam('from');
			$ImageModel=new Application_Model_Images($this->registry->DB);
			if(empty($from) && !(is_int($from) || ctype_digit($from)))
					$from=0;
			$results=$ImageModel->getComments($imageid, $from);
			$this->view->imageid=$imageid;
			$this->view->results=$results['result'];
			$this->view->maxcomment=$results['maxcomment'];
		}
	}
	
	public function getpinpeopleAction(){
		if($this->authIdentity->userid){
			$imageid=$this->getRequest()->getParam('imageid');
			$ImageModel=new Application_Model_Images($this->registry->DB);
			$this->view->results=$ImageModel->getPinnedPeople($imageid);
		}
	}
	
	
	
	
	public function getpinnedpicsAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$userid=$this->getRequest()->getParam('userid');
			$from=$this->getRequest()->getParam('from');
			if(empty($from) && !(is_int($from) || ctype_digit($from)))
				$from=0;
			$ImageModel=new Application_Model_Images($this->registry->DB);
			$ImageModel->getPinnedPics($userid, $from);
			
		}
	}
	public function imagecommentAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$imageid=$this->getRequest()->getParam('imageid');
			$text=$this->getRequest()->getParam('text');
			if(!empty($text)){
				$ImageModel=new Application_Model_Images($this->registry->DB);
				$result=$ImageModel->doComment($imageid, $text);
			}
		}
	}
	public function adddescriptionAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$desc=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$text=$this->getRequest()->getParam('text');
			$desc->adddescription($imageid, $text);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function voteimageAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$vote=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$vote->voteimage($imageid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function unvoteimageAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$unvote=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$unvote->unVoteimage($imageid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function deleteimagesAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$deleteimage=new Application_Model_Images($this->registry['DB']);
			$imageid=$this->getRequest()->getParam('imageid');
			$deleteimage->deleteImages($imageid);
			echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
	public function deleteimagecommentAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity->userid){
			$deleteimagecomment=new Application_Model_Images($this->registry['DB']);
			$commentid=$this->getRequest()->getParam('commentid');
			$deleteimagecomment->deleteimageComment($commentid);
		echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
	}
}

