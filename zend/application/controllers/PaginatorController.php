<?php

/**
 * PaginatorController
 * 
 * @author
 * @version 
 */

require_once 'Zend/Controller/Action.php';
class PaginatorController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	protected $registry,$model,$request;
    public function init()
    {
        /* Initialize action controller here */
    	$this->registry=Zend_Registry::getInstance();
    	$this->request=$this->getRequest();
    }
    public function indexAction(){
    	$adapter=new Zend_Paginator_Adapter_DbSelect($this->registry->DB->select()->from('places'));
    	$adapter->setRowCount(14034475);
    	$paginator=new Zend_Paginator($adapter);
    	$paginator::setDefaultItemCountPerPage(20);
    	$paginator->setCurrentPageNumber($this->_getParam('page'));
    	$this->view->paginator=$paginator;
    }
}
