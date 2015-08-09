<?php

/**
 * SearchModel
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Search extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'searchtable';
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$this->auth=Zend_Auth::getInstance();
		if($this->auth->hasIdentity()){
			$this->authIdentity=$this->auth->getIdentity();
						
				if(round((time()-$this->authIdentity->latime)/60)>25){
				$this->authIdentity->latime=time();
				$this->_db->update('user_info', array('latime'=>new Zend_Db_Expr('now()')),array('userid=?'=>$this->authIdentity->userid));
			}
				
				
		}
	}
	
	
	public function search($data){
		$key=$data['key'];
		$relevance=new Zend_Db_Expr('match(searchtable.username) against(\''.$key.'\')');
		$match=new Zend_Db_Expr('match(searchtable.username) against(\''.$key.'*\' in boolean mode)');
		$sql=$this->_db->select()->from('searchtable',array('userid','username','type','relevance'=>$relevance));
		switch($data['type']){
			case 'user':
				$sql=$sql->joinLeft('user_info', 'user_info.userid=searchtable.userid',array('currentcity','hometown','skills','propic','college','school','url as user_url'))
				->joinLeft('places', 'places.id=user_info.hometown',array('name as ht_name','id as ht_id'))
				->joinLeft('places as cplaces', 'cplaces.id=user_info.currentcity',array('name as cc_name','id as cc_id'))
				->joinLeft('friends_vote','friends_vote.userid=user_info.userid',array('friends'=>'friendlist','vote'))->joinLeft('image', 'image.imageid=user_info.propic','url as propic_url');
				if(isset($data['school'])){
					$sql=$sql->where(new Zend_Db_Expr('match(searchtable.school) against(\''.$data['school'].'\' in boolean mode)'));
				}
				if(isset($data['college'])){
					$sql=$sql->where(new Zend_Db_Expr('match(searchtable.college) against(\''.$data['college'].'\' in boolean mode)'));
				}
				if(isset($data['employer'])){
					$sql=$sql->where(new Zend_Db_Expr('match(searchtable.employer) against(\''.$data['employer'].'\' in boolean mode)'));
				}
				if(isset($data['skills'])){
					$sql=$sql->where(new Zend_Db_Expr('match(searchtable.skills) against(\''.$data['skills'].'\' in boolean mode)'));
				}
				if(isset($data['htown'])){
					$sql=$sql->where('hometown=?',$data['htown']);
				}
				if(isset($data['ccity'])){
					$sql=$sql->where('currentcity=?',$data['ccity']);
				}
				
				break;
			case 'page':
				$sql=$sql->joinLeft('pages','pages.pageid=searchtable.userid',array('category','subcategory','vote'))->joinLeft('image','image.imageid=pages.pagepic','url as pagepic_url');
				
				if(isset($data['category']))
					$sql=$sql->where('pages.category=?',$data['category']);
				if(isset($data['subcategory']))
					$sql=$sql->where('pages.subcategory=?',$data['subcategory']);
				break;
			case 'friends':
				$sql=$sql->joinLeft('user_info', 'user_info.userid=searchtable.userid',array('currentcity','hometown','skills','propic','college','school','url as user_url'))->joinLeft('friends_vote','friends_vote.userid=user_info.userid',array('friends'=>'friendlist','vote'))->joinLeft('image', 'image.imageid=user_info.propic','url as propic_url');
				if(!empty($this->authIdentity->userid))
					$sql=$sql->where('user_info.userid in(?)',$this->authIdentity->friends);
				break;
			case 'places':
				return $this->searchPlaces($key, 5);
			default:
				$sql=$sql->joinLeft('freniz','freniz.userid=searchtable.userid','url as user_url')->joinLeft('user_info', 'user_info.userid=searchtable.userid',array('currentcity','hometown','skills','propic'))->joinLeft('friends_vote','friends_vote.userid=user_info.userid','vote as user_vote')->joinLeft('image', 'image.imageid=user_info.propic','url as propic_url')
						->joinLeft('pages','pages.pageid=searchtable.userid',array('category','subcategory','page_vote'=>'vote'))->joinLeft('image as pageimage','pageimage.imageid=pages.pagepic','url as pagepic');
						if(isset($data['category']))
							$sql=$sql->where('pages.category=?',$data['category']);
						if(isset($data['subcategory']))
							$sql=$sql->where('pages.subcategory=?',$data['subcategory']);
						if(isset($data['school'])){
							$sql=$sql->where(new Zend_Db_Expr('match(searchtable.school) against(\''.$data['school'].'\' in boolean mode)'));
						}
						if(isset($data['college'])){
							$sql=$sql->where(new Zend_Db_Expr('match(searchtable.college) against(\''.$data['college'].'\' in boolean mode)'));
						}
						if(isset($data['employer'])){
							$sql=$sql->where(new Zend_Db_Expr('match(searchtable.employer) against(\''.$data['employer'].'\' in boolean mode)'));
						}
						if(isset($data['skills'])){
							$sql=$sql->where(new Zend_Db_Expr('match(searchtable.skills) against(\''.$data['skills'].'\' in boolean mode)'));
						}
						if(isset($data['htown'])){
							$sql=$sql->where('hometown=?',$data['htown']);
						}
						if(isset($data['ccity'])){
							$sql=$sql->where('currentcity=?',$data['ccity']);
						}
						break;
		}
		if(!empty($key)){
			$sql=$sql->where($match." ");
			if($data['type']=='page')
					$sql=$sql->order('(relevance+(2*bids)) desc');
			else
				$sql=$sql->order('(relevance) desc');
		}
		else  if($data['type']=='page')
			$sql=$sql->order('bids desc');
		
		if(isset($data['from']))
			$from=$data['from'];
		if(empty($from) && !(is_int($from) || ctype_digit($from)))
			$from=0;
		if(isset($data['limit']))
			$limit=$data['limit'];
		if(empty($limit) && !(is_int($limit) || ctype_digit($limit)))
			$limit=7;
		$sql=$sql->limit($limit,$from);
		$result=$this->_db->fetchAssoc($sql);
		//print_r($result);
		return $result;
	}
	
	public function searchPlaces($key,$limit){
		//$match=new Zend_Db_Expr('match(place.name) against(\''.$key.'*\' in boolean mode)');
		//$sql=$this->_db->select()->from('place')->joinLeft('placesinfo','place.id=placesinfo.id','')->joinLeft('country','country.code=placesinfo.country','name as country')->joinLeft('province', 'province.provinceid=concat(placesinfo.country,".",placesinfo.province)','provincename as province')->where($match)->group('place.infoid')->limit($limit);
		$sql=$this->_db->select()->from('places')
		->joinLeft('image','image.imageid=places.placepic','url as placepic_url')
		->joinLeft('placesinfo','places.infoid=placesinfo.id',array('province_code'=>'province','country_code'=>'country'))
		->joinLeft('country','country.code=placesinfo.country','name as country')
		->where('places.name like ?',$key.'%')->group('places.infoid')->limit($limit);
		
		$result=$this->_db->fetchAssoc($sql);
		foreach($result as $key=>$values){
			$province_mapper[$key]=trim($values['country_code']).'.'.trim($values['province_code']);	
		}
		if(!empty($province_mapper)){
			$sql=$this->_db->select()->from('province',array('provinceid','provincename as province'))->where('provinceid in(?)',$province_mapper);
			$result1=$this->_db->fetchAssoc($sql);
			foreach ($result as $key=>$values){
				$result[$key]['province']=$result1[$province_mapper[$key]]['province'];
			}
		}
		//echo $sql;
		//return array();
		return $result;
	}
	public function frndSuggestion($from,$limit){
		if(isset($this->authIdentity->userid)){
			$sql=$this->_db->select()->from('friends_vote',array('userid','incomingrequest','sentrequest'))->where('userid=?',$this->authIdentity->userid);
			$friendreqs=$this->_db->fetchRow($sql);
			$bendingreqs=unserialize($friendreqs['incomingrequest']);
			$sentreqs=unserialize($friendreqs['sentrequest']);
			$school=explode(' ',array_keys($this->authIdentity->school));
			$college=explode(' ', array_keys($this->authIdentity->college));
			$employer=explode(' ',array_keys($this->authIdentity->employer));
			$match_school=new Zend_Db_Expr('match(searchtable.school) against(\''.$school.'\' in boolean mode)');
			$match_college=new Zend_Db_Expr('match(searchtable.college) against(\''.$college.'\' in boolean mode)');
			$match_employer=new Zend_Db_Expr('match(searchtable.employer) against(\''.$employer.'\' in boolean mode)');
			$sql=$this->_db->select()->from('searchtable',array('userid','relevance'=>'((('.$match_school.')*2)+(('.$match_college.')*3)+(('.$match_employer.')*4)+((case isnull(ccplaces.infoid) when 0 then ccplaces.infoid=\''.$this->authIdentity->ht_info.'\'  when 1 then 0 end)*3)+((case isnull(htplaces.infoid) when 0 then htplaces.infoid=\''.$this->authIdentity->ht_info.'\'  when 1 then 0 end)*3))'))
			->joinLeft('user_info', 'user_info.userid=searchtable.userid',array('fname','lname'))->joinLeft('image', 'image.imageid=user_info.propic','url as propic_url')
			->joinLeft('friends_vote', 'friends_vote.userid=user_info.userid',array('friendlist'))
			->joinLeft('places as ccplaces', 'user_info.currentcity=ccplaces.id',array('name as ccname'))
			->joinLeft('places as htplaces', 'user_info.hometown=htplaces.id',array('htname'=>'name'))
			->where('searchtable.userid not in (?) and type=\'user\'',array_merge($this->authIdentity->friends,$bendingreqs,$sentreqs,array($this->authIdentity->userid)))
			->order('((('.$match_school.')*2)+(('.$match_college.')*3)+(('.$match_employer.')*4)+((case isnull(ccplaces.infoid) when 0 then ccplaces.infoid=\''.$this->authIdentity->cc_info.'\'  when 1 then 0 end)*3)+((case isnull(htplaces.infoid) when 0 then htplaces.infoid=\''.$this->authIdentity->ht_info.'\'  when 1 then 0 end)*3)) desc')->limit($limit,$from);
			$result=$this->_db->fetchAssoc($sql);
			foreach($result as $userid=>$values){
				$friends=unserialize($values['friendlist']);
				$result[$userid]['mutual']=array_intersect($friends, $this->authIdentity->friends);
	
			}
			return $result;
		}
	}
	public function placename($id){
		$sql=$this->_db->select()->from('places')->where("id=?",$id);
		$result=$this->_db->fetchRow($sql);
		return $result;
	}
}
