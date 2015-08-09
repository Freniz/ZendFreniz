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
	protected $registry;
	public function __construct($db) {
		$this->_db = $db;
	}
	public function createEntry($userid){
		$notification=array('userid'=>$notification,'notifications'=>'a:0:{}');
		$this->insert($notification);
	}
	public function getReviewCount($userid){
		$subsql1=$this->_db->select()->from('status','count(*)')->where('ruserid=? and accepted=\'not\'',$userid);
		$subsql2=$this->_db->select()->from('video','count(*)')->where('ruserid=? and accepted=\'not\'',$userid);
		$subsql3=$this->_db->select()->from('testimonial','count(*)')->where('ruserid=? and accepted=\'not\'',$userid);
		$subsql4=$this->_db->select()->from('image','count(*)')->joinLeft('album', 'album.albumid=image.albumid','')->where('album.userid=? and image.accepted=\'not\'',$userid);
		$subsql5=$this->_db->select()->from('pinme','count(*)')->joinLeft('image', 'image.imageid=pinme.imageid','')->joinLeft('album', 'album.albumid=image.albumid','')->where('album.userid=? and pinme.reviewed=\'false\'',$userid);
		$subsql6=$this->_db->select()->from('pinreq','count(*)')->where('userid=? and reviewed=\'false\'',$userid);
		$subsql7=$this->_db->select()->from('message','count(*)')->where('ruserid=? and read1=0',$userid);
		$subsql8=$this->_db->select()->from('notifications','count(*)')->where('userid=? and read1=0',$userid);
		$sql=$this->_db->select()->from('freniz',array('userid'=>'userid','post-reviews'=>new Zend_Db_Expr('('.$subsql1.')'),'video-reviews'=>new Zend_Db_Expr('('.$subsql2.')'),'admire-reviews'=>new Zend_Db_Expr('('.$subsql3.')'),'image-reviews'=>new Zend_Db_Expr('('.$subsql4.')'),'pinme-reviews'=>new Zend_Db_Expr('('.$subsql5.')'),'pinreq-reviews'=>new Zend_Db_Expr('('.$subsql6.')'),'message'=>new Zend_Db_Expr('('.$subsql7.')'),'notifications'=>new Zend_Db_Expr('('.$subsql8.')')))->joinLeft('friends_vote', 'friends_vote.userid=freniz.userid','incomingrequest')->where('freniz.userid=?',$userid);
		$result=$this->_db->fetchRow($sql);
		$result['incomingrequest']=count(unserialize($result['incomingrequest']));
		$result['reviews']=$result['post-reviews']+$result['video-reviews']+$result['admire-reviews']+$result['image-reviews']+$result['pinme-reviews']+$result['pinreq-reviews']+$result['incomingrequest'];
		$result['alerts']=$result['notifications']+$result['reviews'];
		return $result;
	}
	public function getNotification($userid,$limit,$from=0){
		$sql=$this->_db->select()->from('notifications')->joinLeft('image', 'image.imageid=notifications.userpic','url as userpic_url')->where('notifications.userid=?',$userid)->order('time desc')->limit($limit,$from);
		$result=$this->_db->fetchAssoc($sql);
			$this->_db->update('notifications', array('read1'=>1),array('userid=?'=>$userid));
		return $result;		
	}
	public function readNotification($userid,$notificationids){
		$this->_db->update('notifications', array('read1'=>1),array('userid=?'=>$userid,'notification_id in(?)'=>$notificationids));
	}
}
