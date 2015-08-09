<?php

/**
 * ListController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
class ListController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
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
	public function indexAction() {
		// TODO Auto-generated ListController::indexAction() default action
		if($this->authIdentity){
			$listModel=new Application_Model_Lists($this->registry->DB);
			$this->view->result=$listModel->getUsersLists();
		}
	}
	public function createAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity){
			$name=$this->getRequest()->getParam('name');
			if(!empty($name)){
				$listModel=new Application_Model_Lists($this->registry->DB);
				if($listModel->createLists($name))
					echo json_encode(array('status'=>'success'));
				else echo json_encode(array('status'=>'Error Occured'));
			}	
		}
	}
	public function addusersAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity){
			$id=$this->getRequest()->getParam('id');
			$users=$this->getRequest()->getParam('users');
			if(!empty($users) && !empty($id)){
				$listModel=new Application_Model_Lists($this->registry->DB);
				if($listModel->addListusers($id, $users))
					echo json_encode(array('status'=>'success'));
				else echo json_encode(array('status'=>'Error Occured'));
			}
		}
	}
	public function removeusersAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity){
			$id=$this->getRequest()->getParam('id');
			$users=$this->getRequest()->getParam('users');
			if(!empty($users) && !empty($id)){
				$listModel=new Application_Model_Lists($this->registry->DB);
				if($listModel->deleteListUsers($id, $users))
					echo json_encode(array('status'=>'success'));
				else echo json_encode(array('status'=>'Error Occured'));
			}
		}
	}
	public function displayAction(){
		if($this->authIdentity){
			$id=$this->getRequest()->getParam('id');
			if(!empty($id)){
				$listModel= new Application_Model_Lists($this->registry->DB);
				$this->view->result=$listModel->getListsDetail($id);
				$this->view->lists=$listModel->getUsersLists();
				$this->view->mydetails=$this->authIdentity;
			}
		}
	}
	public function removeAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->authIdentity){
			$id=$this->getRequest()->getParam('id');
			$listModel=new Application_Model_Lists($this->registry->DB);
			if($listModel->deleteList($id))
				echo json_encode(array('status'=>'success'));
			else echo json_encode(array('status'=>'Error Occured'));
		}
	}
}
