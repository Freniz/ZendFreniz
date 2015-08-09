<?php

/**
 * ip2c
 *  
 * @author abdulnizam
 * @version 
 */

require_once 'Zend/Db/Table/Abstract.php';
class Application_Model_ip2c extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'ip2city_list';
	private $ip='';private $ip1='';
	
	public function __construct($db)
	{
		$this->_db=$db;
		$this->ip=(!empty($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR'] :((!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR']: @getenv('REMOTE_ADDR'));
		if(isset($_SERVER['HTTP_CLIENT_IP']))
			$this->ip1=$_SERVER['HTTP_CLIENT_IP'];
		
	}
	function getIpAdd()
	{
		if(!empty($this->ip1))
			return $this->ip1."@".$this->ip;
		else
			return $this->ip;
	}
	
	function getCountrycode()
	{
		$sql=$this->select()->where('locationid=(select locid from ip2city where ? between beginip and endip)',ip2long($this->ip));
		$results=$this->_db->fetchRow($sql,null,Zend_Db::FETCH_ASSOC);
		return $results;
	}
}
