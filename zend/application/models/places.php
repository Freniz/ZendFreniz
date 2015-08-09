<?php

/**
 * places
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_places extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'places';
	public function __construct($db){
	$this->_db=$db;
	}
	public function getPlacesInfo($placeid)
	{
		$select=$this->_db->select()->from($this->_name)->joinRight('placesinfo', 'places.infoid=placesinfo.id')->limit(100);
			return $this->_db->fetchAssoc($select);
	}
}

