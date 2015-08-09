<?php

/**
 * apps
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_Apps extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'apps';
	public function __construct($db){
		$this->_db=$db;
	}
	public function createEntry($data){
		$this->insert($data);
	}
}
