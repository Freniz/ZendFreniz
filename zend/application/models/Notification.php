<?php

/**
 * Notification
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Notification extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'notification';
	public function __construct($db) {
		$this->_db = $db;
	}
	public function createEntry($userid){
		$notification=array('userid'=>$notification,'notifications'=>'a:0:{}');
		$this->insert($notification);
	}
}
