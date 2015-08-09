<?php

/**
 * SearchController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
class SearchController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
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
    public function indexAction(){
    	
    }
public function searchAction(){
    	$searchModel=new Application_Model_Search($this->registry->DB);
    	$request=$this->getRequest()->getParams();
    	
    	$this->view->results=$searchModel->search($request);
    	
    }
    public function suggestionAction(){
    	$searchModel=new Application_Model_Search($this->registry->DB);
    	$from=$this->getRequest()->getParam('from');
    	if(empty($from) && !(is_int($from) || ctype_digit($from)))
			$from=0;
		$limit=$this->getRequest()->getParam('limit');
    	if(empty($limit) && !(is_int($limit) || ctype_digit($limit)))
			$limit=20;
		$this->view->results=$searchModel->frndSuggestion($from,$limit);
    }
    public function frenizsearchAction(){
    	$log=$this->getRequest()->getParams();
    	$searchModel=new Application_Model_Search($this->registry->DB);
    	if($log['ccity']){
    		$id=$log['ccity'];
    		$this->view->placename=$searchModel->placename($id);
    	}elseif ($log['htown']){
    		$id=$log['htown'];
    		$this->view->placename=$searchModel->placename($id);
    	}
    }
}
