<?php

/**
 * commentactivity
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_CommentActivity extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'commentactivity';
	protected $authIdentity,$registry;
	public function __construct() {
		$this->registry=Zend_Registry::getInstance();
		$this->_db = $this->registry->DB;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
			
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
	}
	
	public function getNewComments($maxid,$statureids,$scribbleids,$imageids,$videoids){
		//if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('commentactivity')->joinLeft('freniz', 'commentactivity.userid=freniz.userid',array('username','url'))
    				->joinLeft('image', 'image.imageid=freniz.propic','url as imageurl')
    				->where('(('.$this->_db->quoteInto('objid in (?) and commentactivity.type=\'stature\'',$statureids).') or ('.$this->_db->quoteInto('objid in (?) and commentactivity.type=\'scribble\'',$scribbleids).') or ('.$this->_db->quoteInto('objid in (?) and commentactivity.type=\'image\'',$imageids).') or ('.$this->_db->quoteInto('objid in (?) and commentactivity.type=\'video\'',$videoids).')) and ('.$this->_db->quoteInto('id > ?',$maxid).')');
			//return $sql;
			return $this->_db->fetchAssoc($sql);
    		
		//}
	}
	
}
