<?php

/**
 * PlacesController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
class PlacesController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	protected $registry,$authIdentity;
	public function init(){
		$this->auth=Zend_Auth::getInstance();
		if($this->auth->hasIdentity()){
			$this->authIdentity=$this->auth->getIdentity();
		}
		$this->registry=Zend_Registry::getInstance();
		 
	}
	public function indexAction() {
		// TODO Auto-generated PlacesController::indexAction() default action
		$this->_helper->viewRenderer->setNoRender();
		$placeid=$this->getRequest()->getParam('placeid');
		if(!empty($placeid)){
			$places=new Application_Model_places($this->registry->DB);
			print_r($places->getProfile($placeid));
		}
		
	}
}
