<?php

class LeafController extends Zend_Controller_Action
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
    public function createleafaccountAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	//$username=$this->getRequest()->getParam('username');
    	$password=$this->getRequest()->getParam('password');
    	$email=$this->getRequest()->getParam('email');
    	$dd=$this->getRequest()->getParam('dd');
    	$dm=$this->getRequest()->getParam('dm');
    	$dy=$this->getRequest()->getParam('dy');
    	$dob=$dy.'-'.$dm.'-'.$dd;
    	$create=new Application_Model_Users($this->registry['DB']);
    	$finis=$create->CreateUserAccount(null,$password,null,null,$email,null,$dob,'none');
    	echo json_encode($finis);
    }
    public function createAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	$types=array('default','basic','songs','video');
    	$fav=array('school','college','work','books','musics','movies','celebrities','games','sports');
    	$data=$this->getRequest()->getParams();
    	if(isset($this->authIdentity) && isset($data['leafname']) && isset($data['category']) && isset($data['subcategory']) && in_array($data['type'], $types)){
    		$leaf=new Application_Model_Leaf($this->registry->DB);
    		$leafid=$leaf->createLeaf($data['leafname'], $data['type'], $data['category'], $data['subcategory']);
    		if($leafid){
    			if(isset($data['fav']) && in_array(strtolower($data['fav']),$fav)){
    				$UserInfoModel=new Application_Model_UserInfo($this->registry->DB);
    				$UserInfoModel->UpdateToFavorites($leafid, $data['fav'], 'add');
    			}
    			$this->getResponse()->setBody(json_encode(array('status'=>'success','leafid'=>$leafid,'name'=>$data['leafname'])));
    		}
    		else 
    			$this->getResponse()->setBody(json_encode(array('status'=>'error occured')));
    	}
    }

public function createleafAction(){
	if(isset($this->authIdentity)){
		$this->view->mydetails=$this->authIdentity;
	}else 
		$this->_helper->viewRenderer->renderScript('leaf/createaccountleaf.phtml');
}
public function leafeditAction(){
	if(isset($this->authIdentity)){
		$this->userModel=new Application_Model_Users($this->registry->DB);
		$userid=$this->getRequest()->getParam('leafid');
		if(!empty($userid)){
			if(isset($this->authIdentity)){
				$this->leafdetails=$this->userModel->getUserDetails($userid,$this->authIdentity);
			}
			else
				$this->leafdetails=$this->userModel->getUserDetails($userid);
		}
		$this->view->leafdetails=$this->leafdetails;
		$this->view->mydetails=$this->authIdentity;
	}
}
public function newleafAction(){
	$this->_helper->viewRenderer->setNoRender();
	if(isset($this->authIdentity)){
		$createleaf=new Application_Model_Leaf($this->registry['DB']);
		$pagename=$this->getRequest()->getParam('leafname');
		$type=$this->getRequest()->getParam('type');
		$category=$this->getRequest()->getParam('category');
		$subcategory=$this->getRequest()->getParam('subcategory');
		$songurl=$this->getRequest()->getParam('songurl');
		if($songurl)
			$createleaf->createLeaf($pagename, $type, $category, $subcategory,$songurl);
		else
		   $createleaf->createLeaf($pagename, $type, $category, $subcategory);
	echo json_encode(array('status'=>'success'));
			}
			else echo json_encode(array('status'=>'error'));
}
public function leafupdateAction(){
	$this->_helper->viewRenderer->setNoRender();
	if(isset($this->authIdentity)){
		$update_leaf=new Application_Model_Leaf($this->registry['DB']);
		$leafid=$this->getRequest()->getParam('leafid');
		$pagename=$this->getRequest()->getParam('leafname');
		$category=$this->getRequest()->getParam('category');
		$subcategory=$this->getRequest()->getParam('subcategory');
		$place=$this->getRequest()->getParam('leafplace');
		$contact=$this->getRequest()->getParam('leafcontact');
		$website=$this->getRequest()->getParam('leafsite');
		$url=$this->getRequest()->getParam('leafurl');
		$update_leaf->leafUpdate($leafid, $pagename, $category, $subcategory, $place, $website,$contact,$url);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
}

public function leafinfoupdateAction(){
	$this->_helper->viewRenderer->setNoRender();
	if(isset($this->authIdentity)){
		$update_data=new Application_Model_Leaf($this->registry['DB']);
		$data=$this->getRequest()->getParams();
		$update_data->leafinfoUpdate($data);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));	
}
public function addtagsAction(){
	$this->_helper->viewRenderer->setNoRender();
	if(isset($this->authIdentity)){
		$leafModel=new Application_Model_Leaf($this->registry->DB);
		$tagid=$this->getRequest()->getParam('tagid');
		$leafModel->addTags($tagid);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
}
public function accepttagsAction(){
	$this->_helper->viewRenderer->setNoRender();
	if(isset($this->authIdentity)){
		$leafModel=new Application_Model_Leaf($this->registry->DB);
		$tagid=$this->getRequest()->getParam('tagid');
		$leafModel->accepttag($tagid);
		echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
}
public function getleaftaglistAction(){
	if(isset($this->authIdentity)){
		$leafModel=new Application_Model_Leaf($this->registry->DB);
		$leafid=$this->getRequest()->getParam('leafid');
		$this->view->results=$leafModel->gettaglist($leafid);
		$this->view->mydetails=$this->authIdentity;
	}
}
public function searchtagsAction(){
	if(isset($this->authIdentity)){
		$leafModel=new Application_Model_Leaf($this->registry->DB);
		$key=$this->getRequest()->getParam('key');
		$leafid=$this->getRequest()->getParam('leafid');
		$this->view->results=$leafModel->searchtags($key, $leafid);
	}
}
public function getrequesttagAction(){
	if(isset($this->authIdentity)){
		$leafModel=new Application_Model_Leaf($this->registry->DB);
		$this->view->results=$leafModel->getrequesttag();
	}
}
public function addimagesAction(){
	$this->_helper->viewRenderer->setNoRender(true);
	if(isset($this->authIdentity) && $this->authIdentity->type='leaf'){
		$name=$this->getRequest()->getParam('img-name');
	$imageurl=null;
	if(!empty($_REQUEST['qqfile']) || !empty($_FILES['qqfile'])){
		$allowedExtensions = array();
		// max file size in bytes
		$sizeLimit = 2 * 1024 * 1024;
		$uploader=new Image_Upload($allowedExtensions,$sizeLimit);
		$upload_result=$uploader->handleUpload('images/');
		$imageurl=$upload_result['imgurl'];

	  }
	  $addimages=new Application_Model_Leaf($this->registry['DB']);
	  $results=$addimages->addimages($imageurl,$name);
	echo json_encode($results);
		}
		else echo json_encode(array('status'=>'error'));
	
}
public function changealbumnameAction(){
	if(isset($this->authIdentity) && $this->authIdentity->type='leaf'){
		$changename=new Application_Model_Leaf($this->registry['DB']);
		$name=$this->getRequest()->getParam('name');
		$changename->changealbumname($name);
		
	}
}
public function getleafalbumAction(){
	if(isset($this->authIdentity)){
		$getalbum=new Application_Model_Leaf($this->registry['DB']);
		$leafid=$this->getRequest()->getParam('leafid');
		$this->view->results=$getalbum->getalbum($leafid);
	}
}
public function getwallphotosAction(){
	if(isset($this->authIdentity)){
		$get=new Application_Model_Images($this->registry['DB']);
		$id=$this->getRequest()->getParam('id');
		$this->view->results=$get->getbanners($id);
	}
}

}

