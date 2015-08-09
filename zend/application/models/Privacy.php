<?php

/**
 * Privacy
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Privacy extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'privacy';
	public function __construct($db) {
		$this->_db = $db;
	}
	public function createEntry($userid){
		$a1=array();
		$b='a:0:{}';
		$b1=array('post','image','admire','pin','video');
		foreach($b1 as $c1)
			$a1[$c1]=array();
		$d1= serialize($a1);
		$privacy=array('userid'=>$userid,'postignore'=>$b,'testyignore'=>$b,'postspeci'=>$b,'testyspeci'=>$b,'blogspeci'=>$b,'posthidden'=>$b,'testyhidden'=>$b,'bloghidden'=>$b,'autoacceptusers'=>$d1,'blockactivityusers'=>$d1,'hidestreams'=>$b,'hideusersstream'=>$b);
		return $this->insert($privacy);
	}
	public function getUserPrivacy($user)
	{
		
		
		$select=$this->select()->where('userid=?',$user);
		$results=$this->_db->fetchAssoc($select);
		return $results;
	}
}
