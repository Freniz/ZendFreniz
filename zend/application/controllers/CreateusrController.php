<?php

/**
 * CreateUsrController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
class CreateusrController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	protected $registry;
	
	public function init()
	{
		/* Initialize action controller here */
		 
		$this->registry=Zend_Registry::getInstance();
	}
	public function indexAction() {
		// TODO Auto-generated CreateUsrController::indexAction() default action
	}
	public function checkuserAction(){
		$this->_helper->viewRenderer->setNoRender();
		
		$userid=$this->getRequest()->getParam('username');
		$user=new Application_Model_Users($this->registry['DB']);
		if($user->checkUser($userid))
			$this->getResponse()->setBody(json_encode(array('status'=>'true')));
		else
			$this->getResponse()->setBody(json_encode(array('status'=>'false')));
	}
	public function checkmailAction(){
		$this->_helper->viewRenderer->setNoRender();
	
		$email=$this->getRequest()->getParam('email');
		$user=new Application_Model_Users($this->registry['DB']);
		if($user->checkmail($email))
			$this->getResponse()->setBody(json_encode(array('status'=>'true')));
		else
			$this->getResponse()->setBody(json_encode(array('status'=>'false')));
	}
	
}
