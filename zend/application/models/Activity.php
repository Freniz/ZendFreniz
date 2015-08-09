<?php

/**
 * Activity
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Activity extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'activity';
	protected $authIdentity=null;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}
}
