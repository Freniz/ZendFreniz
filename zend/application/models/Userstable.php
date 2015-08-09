<?php

/**
 * userstable
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_Userstable extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'userstable';
	public function __construct($db)
	{
		$this->_db=$db;
	}
	
	public function createEntry($username){
		$a='a:0:{}';
		$data=array('userid'=>$username,'slambook'=>$a,'diary'=>$a,'inivitaion'=>$a);
		return $this->insert($data);
	}
}
