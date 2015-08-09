<?php

/**
 * freniz
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class freniz extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'freniz';
	
	public function __construct($db)
	{
		$this->_db=$db;
	}
	protected function createUser($freniz){
		return $this->insert($freniz);
	}
}
