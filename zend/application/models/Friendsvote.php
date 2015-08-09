<?php

/**
 * friendsvote
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Friendsvote extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'friends_vote';
	public function __construct($db) {
		$this->_db = $db;
	}
	public function createEntry($userid){
		$b='a:0:{}';
		$friends_vote=array('userid'=>$userid,'friendlist'=>$b,'incomingrequest'=>$b,'sentrequest'=>$b,'vote'=>$b,'voted'=>$b);
		return $this->insert($friends_vote);
	}
}
