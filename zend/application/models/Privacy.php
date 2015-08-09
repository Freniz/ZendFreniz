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
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
						
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
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
	
	
	
	public function updatePrivacy($data){
		if(isset($this->authIdentity)){
			if($this->authIdentity->type=='user' && !empty($data)){
				
				$this->_db->update('privacy',$data, array('userid=?'=>$this->authIdentity->userid));
			}
			elseif($this->authIdentity->type=='page'){
				
			}
		}
	}
	
	public function updateBlock($userid,$action){
		if(isset($this->authIdentity)){
			if($action=='add'){
			$this->_db->insert('block',array('blockedby'=>$this->authIdentity->userid, 'blockusers'=>$userid));
			}
			else if($action=='remove'){
				$where=array('');
				$this->_db->delete('block');
			}
			
		}
	}
	
	public function getUserPrivacy($user,$userspro=false)
	{
		$select=$this->select()->where('userid=?',$user);
		$results=$this->_db->fetchRow($select);
		$result_users=array();
		if($userspro){
			$aau=unserialize($results['autoacceptusers']);
			$bau=unserialize($results['blockactivityusers']);
			$apu=array_merge($aau['post'],$aau['image'],$aau['admire'],$aau['pin'],$aau['video'],$bau['post'],$bau['image'],$bau['admire'],$bau['pin'],$bau['video']);
			
			$users=array_merge(unserialize($results['postignore']),
					unserialize($results['postspeci']),
					unserialize($results['posthidden']),
					unserialize($results['testyignore']),
					unserialize($results['testyspeci']),
					unserialize($results['testyhidden']),
					unserialize($results['blogspeci']),
					unserialize($results['bloghidden']),
					unserialize($results['postspecificpeople']),
					unserialize($results['testyspecificpeople']),
					unserialize($results['videospecificpeople']),
					unserialize($results['staturespecificpeople']),
					unserialize($results['videospeci']),
					unserialize($results['videohidden']),
					unserialize($results['videoignore']),
					unserialize($results['staturespeci']),
					unserialize($results['staturehidden']),
					unserialize($results['statureignore']),
					unserialize($results['albumignore']),
					unserialize($results['albumspecificpeople']),
					unserialize($results['messagespecificpeople']),
					unserialize($results['messageignore']),
					$apu);
			if(!empty($users)){
				$sql=$this->_db->select()->from('user_info',array('userid','fname','lname','url'))->joinLeft('image', 'image.imageid=user_info.propic','url as propic_url')->where('user_info.userid in (?)',$users);
				$result_users=$this->_db->fetchAssoc($sql);
			}
		}
		$results['users_pro']=$result_users;
		return $results;
	}
	
	
	public function updateObjectPrivacy($id,$type,$data){
		if(isset($this->authIdentity)){
			if(isset($data['hiddenlist']))
				$data['hiddenlist']=serialize(explode(',',$data['hiddenlist']));
			if(isset($data['specificlist']))
				$data['specificlist']=serialize(explode(',',$data['specificlist']));
			switch($type){
				case 'stature':
					$this->_db->update('stature', $data,array('userid=?'=>$this->authIdentity->userid,'statureid=?'=>$id));
					break;
				case 'post':
					$this->_db->update('status', $data,array('ruserid=?'=>$this->authIdentity->userid,'statusid=?'=>$id));
					break;
				case 'admire':
					$this->_db->update('testimonial', $data,array('ruserid=?'=>$this->authIdentity->userid,'testyid=?'=>$id));
					break;
				case 'blog':
					$this->_db->update('blog', $data,array('userid=?'=>$this->authIdentity->userid,'blogid=?'=>$id));
					break;
				case 'image':
					$sql='update image left join album on alum.albumid=image.albumid set ';
					foreach($data as $field=>$value)
						$sql.='image.'.$field.'=\''.$value.'\',';
					str_replace($sql, "", -1);
					$sql.=' where album.userid=\''.$this->authIdentity->userid.'\' and image.imageid=\''.$id.'\'';
					$this->_db->query($sql);
					break;
				case 'video':
					$this->_db->update('video', $data,array('ruserid=?'=>$this->authIdentity->userid,'videoid=?'=>$id));
					break;
			}
		}
	}
	
	public function updateinfoprivacy($data){
		if(isset($this->authIdentity)){
			$privacy_data=array('dob'=>$data['dob'],'languages'=>$data['language'],'status'=>$data['status'],'religion'=>$data['religion'],'livingin'=>$data['livingin'],'hometown'=>$data['hometown'],'friendlist'=>$data['friendlist'],'aboutme'=>$data['aboutme'],'education'=>$data['education'],'occupation'=>$data['occupation'],'fav'=>$data['fav']);
			if($this->authIdentity->type=='user' && !empty($data)){
				$this->_db->update('privacy',$privacy_data, array('userid=?'=>$this->authIdentity->userid));
				$this->authIdentity->privacy['dob']=$data['dob'];
				$this->authIdentity->privacy['relationship']=$data['relationship'];
				$this->authIdentity->privacy['languages']=$data['languages'];
				$this->authIdentity->privacy['religion']=$data['religion'];
				$this->authIdentity->privacy['livingin']=$data['livingin'];
				$this->authIdentity->privacy['hometown']=$data['hometown'];
				$this->authIdentity->privacy['friendlist']=$data['friendlist'];
				$this->authIdentity->privacy['aboutme']=$data['aboutme'];
				$this->authIdentity->privacy['education']=$data['education'];
				$this->authIdentity->privacy['occupation']=$data['occupation'];
				$this->authIdentity->privacy['fav']=$data['fav'];
				
			}
			elseif($this->authIdentity->type=='page'){
			
			}
			
		}
	}
	public function hidestream($id){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('privacy','hidestreams')->where('userid=?',$this->authIdentity->userid);
			$result=$this->_db->fetchRow($sql);
			$hidestreams=unserialize($result['hidestreams']);
			array_push($hidestreams, $id);
			$hidestreams=array_unique($hidestreams);
			$data['hidestreams']=serialize($hidestreams);
			$this->_db->update('privacy', $data,array('userid=?'=>$this->authIdentity->userid));
		}
	}
}
