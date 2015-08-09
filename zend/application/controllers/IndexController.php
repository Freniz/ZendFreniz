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
        	$this->view->userid=$this->authidentity->password;
        	$this->view->onlineusers=$this->authidentity;
        }
        else {
        	$this->_redirect('login');
        }
    }
	public function loginAction()
    {
    	if(!$this->auth->hasIdentity()){
    	if($this->getRequest()->isPost()){
                $userid = $this->getRequest()->getParam('username');
                $password=$this->getRequest()->getParam('password');
                $db=$this->registry->DB;
                $users = new Application_Model_Users($db);
                $authAdapter = new MyAuth_Adapter($db,'userstable');
                $authAdapter->setIdentityColumn('userid')
                            ->setCredentialColumn('pass');
                $authAdapter->setIdentity($userid)
                            ->setCredential($password);
                $result = $this->auth->authenticate($authAdapter);
                if($result->isValid()){
                	$auth_results=$authAdapter->getResultRowObject();
                    //$this->auth->getStorage()->write($auth_results);
                    $userinfo=new Application_Model_UserInfo($this->registry['DB']);
                    $info_results=$userinfo->initUserSession($auth_results->userid);
                    $info_results->password=$auth_results->pass;
                    $this->auth->getStorage()->write($info_results);
                    
                   	$this->_redirect('');
                } else {
                    $this->view->errorMessage = "Invalid username or password. Please try again.";
                }         
            }
       }
    	else $this->_redirect('');
    }
    
public function signupAction()
    {
        $form = new Application_Form_RegistrationForm();
        $this->view->form=$form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                if($data['password'] != $data['confirmPassword']){
                    $this->view->errorMessage = "Password and confirm password don't match.";
                    return;
                }
                $this->view->results=$data;
                 $users = new Application_Model_Users($this->registry->DB);
                $createaccresult=$users->CreateUserAccount($data['username'], $data['password'], $data['firstname'], $data['lastname'], $data['email'], $data['gender'], $data['dob']);
               $this->view->errorMessage =$createaccresult['message']; 
                // $this->_redirect('auth/login');
            }
        }
    }
    
    
    public function logoutAction(){
    		$this->_helper->viewRenderer->setNoRender(true);
    		$this->auth->clearIdentity();
    		$this->_redirect('login');
    	
    }
    public function placesAction(){
    	$places=new Application_Model_Invites($this->registry['DB']);
    	$this->view->results= $places->getInvites('nisar');
    }
	
}

