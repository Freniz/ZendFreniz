<?php

class BuildController extends Zend_Controller_Action
{

   protected $registry;
	protected $authIdentity;
	public function init()
	{
		if(!file_exists(APPLICATION_PATH.'/search')){
			mkdir(APPLICATION_PATH.'/search');
			mkdir(APPLICATION_PATH.'/search/forum');
			}
		if(!file_exists(APPLICATION_PATH.'/usersearch')){
			mkdir(APPLICATION_PATH.'/usersearch');
			mkdir(APPLICATION_PATH.'/usersearch/users');
		}
		if(!file_exists(APPLICATION_PATH.'/placesearch')){
			mkdir(APPLICATION_PATH.'/placesearch');
			mkdir(APPLICATION_PATH.'/placesearch/places');
		}
		/* Initialize action controller here */
		 
		$this->auth=Zend_Auth::getInstance();
		if($this->auth->hasIdentity()){
			$this->authIdentity=$this->auth->getIdentity();
		}
		$this->registry=Zend_Registry::getInstance();
	}
	public function buildforumAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$build=new Application_Model_forum($this->registry['DB']);
			$this->view->results=$build->buildforum();
		}
	}
	public function buildusersAction()
	{
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$build=new Application_Model_Users($this->registry['DB']);
			$this->view->results=$build->buildusers();
		}
		
	}
	public function buildplacesAction(){
		$this->_helper->viewRenderer->setNoRender(true);
		if($this->auth->hasIdentity()){
			$build=new Application_Model_places($this->registry['DB']);
			$this->view->results=$build->buildplaces();
		}
	}

}

