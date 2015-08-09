<?php

/**
 * Album
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Album extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'album';
	protected $auth;
	public function __construct($db) {
		$this->_db = $db;
		$this->auth=Zend_Auth::getInstance();
	}
	public function createAlbum($albumname,$userid,$privacy=null){
		if(is_array($privacy))
		{
			if(is_null($privacy['pt']))
				$privacy['pt']='public';
			if(is_null($privacy['specificlist']))
				$privacy['specificlist']='a:0:{}';
			if(is_null($privacy['hiddenlist']))
				$privacy['hiddenlist']='a:0:{}';
			if(is_null($privacy['ignorelist']))
				$privacy['ignorelist']='a:0:{}';
		}
		$album=array('userid'=>$userid,'name'=>$albumname,'date'=>new Zend_Db_Expr('Now()'),'pt'=>$privacy['pt'],'specificlist'=>$privacy['specificlist'],'hiddenlist'=>$privacy['hiddenlist'],'ignorelist'=>$privacy['ignorelist']);
		return $this->insert($album);
	}
}
