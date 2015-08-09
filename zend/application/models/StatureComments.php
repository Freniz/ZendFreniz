<?php

/**
 * StatureComments
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_StatureComments extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'stature_comment';
	
	public function __construct(Zend_Db_Adapter_Abstract $db) {
		$this->_db = $db;
	}
	public function getComments($statureid){
		
	}
}
