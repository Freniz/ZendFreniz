<?php
class IndexController extends Zend_Controller_Action
{
	protected $request;
	protected $auth;
	protected $authidentity=null;
	protected $registry;
    public function init()
    {
        /* Initialize action controller here */
    	
    	$this->request=$this->getRequest();
    	$this->auth=Zend_Auth::getInstance();
    	if($this->auth->hasIdentity()){
    	$this->authidentity=$this->auth->getIdentity();
    	}
    	$this->registry=Zend_Registry::getInstance();
    	
    }

    public function indexAction()
    {
        // action body
        if($this->auth->hasIdentity()){
        	// Streams logic has to be here
        	$this->view->mydetails=$this->authidentity;
        	$this->view->defaultListIds=$this->registry->defaultListIds;
        	$listModel=new Application_Model_Lists($this->registry->DB);
        	$this->view->lists=$listModel->getUsersLists();
        	if($this->authidentity->type=='none'){
        	 $this->renderScript('profile/index.phtml');
			}
        }
        else {
        	$this->_redirect('login');
        }
    }
	public function loginAction()
    {
    	if(!$this->auth->hasIdentity()){
			$facebook=new FbPlugin_Facebook(array(
				'appId'  => '216305671841281',
				'secret' => '041be5ec9d9cb6aea7cd4bb3541e9c8c',
			));
			$this->view->FbAppId=$facebook->getAppID();
		if($this->getRequest()->isGet()){
    		$this->_helper->viewRenderer->setNoRender(true);
    		$data=$this->getRequest()->getParams();
            $this->validateUser($data);
        }
        }
    	else $this->_redirect('');
    	
    }
    public function validateUser($data){
    	if(isset($data['username']) && isset($data['password'])){
    		$db=$this->registry->DB;
    		$users = new Application_Model_Users($db);
    		$authAdapter = new MyAuth_Adapter($db,'userstable');
    		$authAdapter->setIdentityColumn('userid')
    		->setCredentialColumn('pass');
    		$authAdapter->setIdentity($data['username'])
    		->setCredential($data['password']);
    		$result = $this->auth->authenticate($authAdapter);
    		if($result->isValid()){
    			$auth_results=$authAdapter->getResultRowObject();
    			//$this->auth->getStorage()->write($auth_results);
    			$userinfo=new Application_Model_UserInfo($this->registry['DB']);
    			$info_results=$userinfo->initUserSession($auth_results->userid);
    			$info_results->password=$auth_results->pass;
    			$this->auth->getStorage()->write($info_results);
    			$this->getResponse()->setBody(json_encode(array('userid'=>$auth_results->userid,'status'=>'true','redirect'=>$data['redir'])));
    		} else {
    			$this->getResponse()->setBody(json_encode(array('userid'=>$data['username'],'status'=>'false','redirect'=>$data['redir'])));
    		}
    	}
    	else 
    		$this->getResponse()->setBody(json_encode(array('userid'=>$data['username'],'status'=>'false')));
    }
    
    public function fbauthAction(){
		if(empty($this->authidentity)){
			$facebook=new FbPlugin_Facebook(array(
				'appId'  => '216305671841281',
				'secret' => '041be5ec9d9cb6aea7cd4bb3541e9c8c',
			));
			$fbuser=$facebook->getUser();
			if($fbuser)
			{
				$fbprofile=$facebook->api('/me?fields=id,username,email,first_name,last_name,birthday,gender');
				$this->view->fbprofile=$fbprofile;
				$users=new Application_Model_Users($this->registry['DB']);
				$result=$users->getProfileFromFb($fbprofile);
				if($result){
					$this->view->result=$result;
					$this->renderScript('index/Fblogin.phtml');
				}
				else
				$this->renderScript('index/FbSignup.phtml');
			}
			else
			 $this->_redirect('http://www.freniz.com');
		}
		else
		 $this->_redirect('http://www.freniz.com');
	}
    
	public function signupAction()
    {
       $this->_helper->viewRenderer->setNoRender();
        if($this->getRequest()->isPost()){
           
                $data = $this->getRequest()->getParams();
                if($data['password'] != $data['confirmpassword']){
                    $this->view->errorMessage = "Password and confirm password don't match.";
                    return;
                }
                $facebook=new FbPlugin_Facebook(array(
                		'appId'  => '216305671841281',
                		'secret' => '041be5ec9d9cb6aea7cd4bb3541e9c8c',
                ));
                $fbuser=$facebook->getUser();
                if($fbuser){
                	$fbprofile=$facebook->api('/me?fields=id,email');
                	if(strcasecmp($fbprofile['email'], $data['email'])===0){
                		$fbuserid=$fbprofile['id'];
                	}
                }
                $users = new Application_Model_Users($this->registry->DB);
                $createaccresult=$users->CreateUserAccount($data['username'], $data['password'], $data['firstname'], $data['lastname'], $data['email'], $data['gender'], $data['dob'],'user',$fbuserid);
                if($createaccresult['status']=='true' && $fbuserid){
                	$facebook->api('/me/feed', 'post', array(
                			'message'=>'Now Joined in Freniz Network',
                			'link'=>"http://www.freniz.com/".$data['username'],
                			'picture'=>"http://images.freniz.com/freniz.png",
                			'name'=> $data['firstname'].' '.$data['lastname'],
                			"description"=> 'description'
                	));
                }
                echo json_encode($createaccresult);
                // $this->_redirect('auth/login');
           
        }
    }
    
    
    public function logoutAction(){
    		$this->_helper->viewRenderer->setNoRender(true);
    		$this->auth->clearIdentity();
    		$this->_redirect('login');
    	
    }
    public function loginattemptAction(){
    	if($this->auth->hasIdentity()){
    		// Streams logic has to be here
    		$this->_redirect('login');
    	}
    	else {
    		$this->view->results='error message';
    	}
    }
    public function resetpasswordAction(){
    	$this->view->errorMessage = "Password and confirm password don't match.";
    }
    public function signupaccountAction(){
    	$this->view->errorMessage = "Password and confirm password don't match.";
    }
    public function placesAction(){
    	$getimage=new Application_Model_Images($this->registry['DB']);
		//$imageid=$this->getRequest()->getParam('imageid');
		
		//$this->view->results=$this->authIdentity;
		$this->view->results=$getimage->getImages('10','image');
		//$this->_helper->viewRenderer->setNoRender(true);
    	//$this->getResponse()->setBody(json_encode($places->uploadImage($album)));
    	//$this->view->results= $places->getUserDetailts('leaf_1164721637_31408804');
    	//$this->view->results= $places->doComment(1, 'ifadfja');
    	
    }
    
    public function personalinfoAction(){
    	if(isset($this->authidentity)){

    		$this->view->personalinfo=$this->authidentity->personalinfo;
    		$this->view->myDetails=$this->authidentity;
    	}
    		
    }
    public function otherinfoAction(){
    	if(isset($this->authidentity)){
    			$this->view->myDetails=$this->authidentity;
    	}
    }
    public function favouritesAction(){
    	if(isset($this->authidentity)){
    	$this->view->myDetails=$this->authidentity;
    	}
    }
    public function changedpAction(){
    	if(isset($this->authidentity)){
    		$this->view->myDetails=$this->authidentity;
    	}
    }
    public function basicinfoAction(){
    	if(isset($this->authidentity)){
    		$this->view->myDetails=$this->authidentity;
    	}
    }
    public function privacysettingsAction(){
    	if(isset($this->authidentity)){
    		$privacy=new Application_Model_Privacy($this->registry->DB);
    		$this->view->privacy=$privacy->getUserPrivacy($this->authidentity->userid,true);
    		$this->view->myDetails=$this->authidentity;
    	}
    }
    public function advancedsettingsAction(){
    	if(isset($this->authidentity)){
    		$this->view->myDetails=$this->authidentity;
    	}
    }
      public function changepasswordAction(){
    	if(isset($this->authidentity)){
    		$this->view->myDetails=$this->authidentity;
    	}
    }
     public function changepassAction(){
    	$this->_helper->viewRenderer->setNoRender(true);
    	$change=new Application_Model_UserInfo($this->registry['DB']);
    	$old=$this->getRequest()->getParam('old');
    	$new=$this->getRequest()->getParam('new');
    	$conf=$this->getRequest()->getParam('conf');
    	echo $change->changepassword($old, $new, $conf);
    }
    public function switchAction(){
    	$this->_helper->viewRenderer->setNoRender();
    	$id=trim($this->getRequest()->getParam('id'));
    	if(isset($this->authidentity) && $this->authidentity->userid!=$id ){
    		$userModel= new Application_Model_Users($this->registry->DB);
    		$userModel->switchUser($id);
    	echo json_encode(array('status'=>'success'));
		}
		else echo json_encode(array('status'=>'error'));
    }
    public function getminiprofileAction(){
    		$userid=$this->getRequest()->getParam('id');
    		$this->userModel=new Application_Model_Users($this->registry->DB);
    		if(!empty($userid)){
    			if(isset($this->authIdentity)){
    				$miniprofile=$this->userModel->getmini($userid,$this->authIdentity);
    			}
    			else
    				$miniprofile=$this->userModel->getmini($userid);
    		}
    		$this->view->miniprofile=$miniprofile;
    		$this->view->mydetails=$this->authidentity;
    }
    
}

