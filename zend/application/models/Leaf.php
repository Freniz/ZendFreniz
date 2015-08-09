<?php

/**
 * Leaf
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Leaf extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'pages';
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
	
public function createLeaf($pagename,$type,$category,$subcategory,$songurl=null){
		$types=array('default','basic','standard','premium','songs','video');
		if(isset($this->authIdentity) && in_array($type, $types)&& !empty($pagename) && !empty($category) && !empty($subcategory))
		{
			$rand=mt_rand()."_".mt_rand();
			$a=array();
			if(strlen($rand)>25)
				$rand=substr ($rand, 0, 25);
			$admins=array($this->authIdentity->userid);
			$votes=array($this->authIdentity->userid);
			$creator=$this->authIdentity->userid;
			if($type=='default' || $type=='places'){
				$creator='default';
				$admins=array('default');
			}
			$admins=serialize($admins);
			$votes=serialize($votes);
			switch($category){
				case 'musics':
					$propic=3;
					break;
				case 'movies':
					$propic=13;
					break;
				case 'celebrities':
					$propic=12;
					break;
				case 'books':
					$propic=5;
					break;
				case 'sports':
					$propic=6;
					break;
				case 'games':
					$propic=7;
					break;
				default :
					$propic=8;
			}
			
			
			
			$freniz_data=array('userid'=>'leaf_'.$rand,'type'=>'page','url'=>'leaf_'.$rand,'adminpages'=>'a:0:{}','username'=>$pagename,'propic'=>$propic);
			$pages_data=array('pageid'=>'leaf_'.$rand,'pagename'=>$pagename,'type'=>$type,'category'=>$category,'subcategory'=>$subcategory,'creator'=>$creator,'admins'=>$admins,'vote'=>$votes,'date'=>new Zend_Db_Expr('now()'),'url'=>'leaf_'.$rand,'bannedusers'=>'a:0:{}','pagepic'=>$propic);
			$pagesinfo_data=array('pageid'=>'leaf_'.$rand,'info'=>'a:0:{}','tabs'=>'a:0:{}','tags'=>'a:0:{}','ratings'=>'a:0:{}');
			if($type=='songs' && isset($songurl))
				$pagesinfo_data['songurl']=$songurl;
			$this->_db->insert('freniz', $freniz_data);
			$this->_db->insert('pages', $pages_data);
			if(!($type=='default' || $type=='places')){
				array_push($this->authIdentity->adminpages, 'leaf_'.$rand);
				$this->_db->update('freniz',array('adminpages'=>serialize($this->authIdentity->adminpages)),array('userid=?'=>$this->authIdentity->userid));
				$this->_db->update('user_info',array('adminpages'=>serialize($this->authIdentity->adminpages)),array('userid=?'=>$this->authIdentity->userid));
				$this->_db->insert('pages_info', $pagesinfo_data);
				$AlbumModel=new Application_Model_Album($this->_db);
				$UserModel=new Application_Model_Users($this->_db);
				$pagepicalbum=$UserModel->createAlbumData('leaf_'.$rand, 'Page Pics');
				$banners=$UserModel->createAlbumData('leaf_'.$rand, 'Banners');
				$chartpic=$UserModel->createAlbumData('leaf_'.$rand, 'Chart Pics',true,true);
				$pagepicAlbumid=$AlbumModel->insert($pagepicalbum);
				$bannersAlbum=$AlbumModel->insert($banners);
				$chartpicAlbum=$AlbumModel->insert($chartpic);
				$this->update(array('pagepicalbum'=>$pagepicAlbumid,'banners'=>$bannersAlbum), array('pageid=?'=>'leaf_'.$rand));
			}
			//$user=array('userid'=>'leaf_'.$rand,'type'=>'page','username'=>$pagename,'user_url'=>'leaf_'.$rand,'pagepic_url'=>'default_page.jpg','page_vote'=>$votes,'category'=>$category,'subcategory'=>$subcategory,'bids'=>0);
			//$usersModel=new Application_Model_Users($this->_db);
			$search=array('userid'=>'leaf_'.$rand,'type'=>'page','username'=>$pagename);
			$this->_db->insert('searchtable', $search);
			//$usersModel->buildusers(array('leaf_'.$rand=>$user));
			return 'leaf_'.$rand;
				
		}
		else
			 return false;
		
	}

	public function addTags($tagid){
		if(isset($this->authIdentity)){
				if($this->authIdentity->type=='leaf' && $this->authIdentity->userid!=$tagid){
			$sql1=$this->_db->select()->from('pages_info')->where("pageid='$tagid'");
			$result1=$this->_db->fetchRow($sql1);
			$leafid=$this->authIdentity->userid;
			if($result1['tag_privacy']=='public'){
			$sql=$this->_db->select()->from('pages_info')->where("pageid='$leafid'");
			$result=$this->_db->fetchRow($sql);
			$tagged=unserialize($result1['tags']);
			$tags=unserialize($result['tags']);
			array_push($tagged, array($leafid));
			array_push($tags, array($tagid));
			$this->_db->update('pages_info',array('tags'=>serialize($tags)), "pageid='$leafid'");
			$this->_db->update('pages_info',array('tags'=>serialize($tagged)), "pageid='$tagid'");
			}
			else{
				$reviewtags=unserialize($result1['reviewtags']);
				array_push($reviewtags, $leafid);
				$this->_db->update('pages_info',array('reviewtags'=>serialize($reviewtags)), "pageid='$tagid'");
			}
			}
		}
	}
	public function accepttag($tagid){
		if(isset($this->authIdentity)){
			if($this->authIdentity->type=='leaf' && $this->authIdentity->userid!=$tagid){
		$leafid=$this->authIdentity->userid;
		$sql=$this->_db->select()->from('pages_info')->where("pageid='$leafid'");
		$result=$this->_db->fetchRow($sql);
		$sql1=$this->_db->select()->from('pages_info')->where("pageid='$tagid'");
		$result1=$this->_db->fetchRow($sql1);
		$tagged=unserialize($result1['tags']);
		array_push($tagged, array($leafid));
		$this->_db->update('pages_info',array('tags'=>serialize($tagged)), "pageid='$tagid'");
		$reviewtags=unserialize($result['reviewtags']);
		$reviewtags=array_diff($reviewtags, array($tagid));
		$accepttags=unserialize($result['tags']);
		array_push($accepttags, array($tagid));
		$this->_db->update('pages_info',array('reviewtags'=>serialize($reviewtags),'tags'=>serialize($accepttags)), "pageid='$leafid'");
			}
		}
	}
	public function leafTags($leafid){
		if(isset($this->authIdentity)){
		$sql=$this->select()->from('pages_info','tags')->where("pageid='$leafid'");
		$result=$this->_db->fetchRow($sql);
		$leafids=unserialize($result['tags']);
		$tag_sql=$this->_db->select()->from($this->_name)->joinLeft('image', 'pages.pagepic=image.imageid','image.url as tagpic_url')->where('pageid in (?)',$leafids);
		$tag_results=$this->_db->fetchAssoc($tag_sql);
		return $tag_results;
		}
	}
	public function leafTagged($leafid){
		if(isset($this->authIdentity)){
			$sql=$this->select()->from('pages_info','tagged')->where("pageid='$leafid'");
			$result=$this->_db->fetchRow($sql);
			$leafids=unserialize($result['tagged']);
			$tag_sql=$this->_db->select()->from($this->_name)->joinLeft('image', 'pages.pagepic=image.imageid','image.url as tagpic_url')->where('pageid in (?)',$leafids);
			$tagged_results=$this->_db->fetchAssoc($tag_sql);
			return $tagged_results;
		}
	}
	
	public function leafUpdate($leafid,$pagename,$category,$subcategory,$place=NULL,$website=NULL,$contact=NULL,$url){
		if(isset($this->authIdentity)){
			$update_data=array('category'=>$category,'subcategory'=>$subcategory,'website'=>$website,'contact'=>$contact);
			if($place!=null)
			$update_data=array_merge(array('place'=>$place),$update_data);
			$this->update($update_data,array('pageid=?'=>$leafid));
			$this->_db->update('freniz', array('url'=>$url,'username'=>$pagename),array('userid=?'=>$leafid));
		}
	}
	public function leafinfoUpdate($data){
		if(isset($this->authIdentity)){
		$info=array();
		foreach($data as $key=>$value)
		{
			if($key!='_' && $key!='controller' && $key!='action' && $key!='module' && $key!='leafid' && strlen(trim($value))>0){
				$info[$key]=htmlspecialchars($value);
			}
		}
		$this->_db->update('pages_info',array('info'=>serialize($info)),array('pageid=?'=>$data['leafid']));
		
		}
	}
	public function gettaglist($leafid){
		if(isset($this->authIdentity)){
			$sql1=$this->_db->select()->from('pages_info','tags')->where("pageid='$leafid'");
			$result=$this->_db->fetchRow($sql1);
			$ids=unserialize($result['tags']);
			$sql=$this->_db->select()->from('freniz',array('userid','user_url'=>'url','username','propic'))
			->joinLeft('pages', "pages.pageid=freniz.userid", array('page_vote'=>'vote','views','category','bids'))
			->joinLeft('image', 'image.imageid=freniz.propic',array('page_pageicurl'=>'url'))
			->where('freniz.userid in (?)',$ids)->order("pages.bids desc");
			$result1=$this->_db->fetchAssoc($sql);
			return $result1;
		}
		
	}
	public function searchtags($key,$leafid){
		if(isset($this->authIdentity)){
			$sql1=$this->_db->select()->from('pages_info','tags')->where("pageid='$leafid'");
			$result1=$this->_db->fetchRow($sql1);
			$ids=unserialize($result1['tags']);
			$match=new Zend_Db_Expr('match(username) against(\''.$key.'*\' in boolean mode)');
			$sql=$this->_db->select()->from('searchtable',array('userid','username','type'));
			$sql=$sql->joinLeft('pages', 'pages.pageid=searchtable.userid',array('pagepic','vote','url','category','views','bids'))
			->joinLeft('image', 'image.imageid=pages.pagepic','url as pagepic_url');
				$sql=$sql->where('searchtable.userid in(?)',$ids)->order("pages.bids desc");
				if(!empty($key))
				$sql=$sql->where($match." ");
			$result=$this->_db->fetchAssoc($sql);
			return $result;
		}
	}
	public function getrequesttag(){
		if(isset($this->authIdentity) && $this->authIdentity->type=='leaf'){
				$leafid=$this->authIdentity->userid;
			$sql1=$this->_db->select()->from('pages_info','reviewtags')->where("pageid='$leafid'");
			$result1=$this->_db->fetchRow($sql1);
			$ids=unserialize($result1['reviewtags']);
			if(!empty($ids)){
			$sql=$this->_db->select()->from('searchtable',array('userid','username','type'));
			$sql=$sql->joinLeft('pages', 'pages.pageid=searchtable.userid',array('pagepic','vote','url','category','views'))
			->joinLeft('image', 'image.imageid=pages.pagepic','url as pagepic_url');
			$sql=$sql->where('searchtable.userid in(?)',$ids);
				$result=$this->_db->fetchAssoc($sql);
			return $result;
			}
	}
	}
	public function addimages($imageurl,$name){
		if(isset($this->authIdentity) && $this->authIdentity->type='leaf'){
			$leafid=$this->authIdentity->userid;
			$sql=$this->_db->select()->from($this->_name,'albumurl')->where("pageid='$leafid'");
			$result=$this->_db->fetchRow($sql);
				$albumurl=unserialize($result['albumurl']);
				$albumurl[$name]=$imageurl;
			//array_merge($albumurl,)
			$uptdid=$this->_db->update($this->_name,array('albumurl'=>serialize($albumurl)),array('pageid=?'=>$leafid));
			return array('imageurl'=>$imageurl,'status'=>'success','id'=>$uptdid,'name'=>$name,'leafid'=>$leafid);
		}
	}
	public function changealbumname($name){
		if(isset($this->authIdentity) && $this->authIdentity->type='leaf'){
			$this->_db->update($this->_name, array('albumname'=>$name),array('pageid=?'=>$this->authIdentity->userid));
		}
	}
	public function getalbum($leafid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name,array('albumname','albumurl'))->where("pageid='$leafid'");
			$result=$this->_db->fetchRow($sql);
			$albumurl=unserialize($result['albumurl']);
			$album['albumurl']=$albumurl;
			$album['albumname']=$result['albumname'];
			return $album;
		}
	}
	
}
