<?php

/**
 * Images
 *  
 * @author abdulnizam
 * @version 
 */
class Application_Model_Images extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'image';
	protected $authIdentity,$registry;
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
		$this->registry=Zend_Registry::getInstance();
	}
	public function getImages($albumid,$from,$type=null,$userid=null){
		if(isset($this->authIdentity)){
			$sub_sql=$this->_db->select()->from('image')->joinLeft('album', 'album.albumid=image.albumid',array('name','ruserid'=>'userid'))->joinLeft('freniz','image.userid=freniz.userid',array('type','username','user_url'=>'url'))->joinLeft('image as simage','freniz.propic=simage.imageid',array('surl'=>'url'))
				->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist');
			if($type=='album'){
				if($albumid!='pinnedpics')
				$sql=$sub_sql->where('image.albumid=?',$albumid);
				elseif(isset($userid)){
					$pinnedpics=$this->getPinnedPics($userid);
					if(!empty($pinnedpics)){
						//$sql=$sub_sql->where('image.imageid in (?)',$pinnedpics);
						$sql=$sub_sql->joinLeft('pinme', 'pinme.imageid=image.imageid and pinme.imageid in ('.$this->_db->quote($pinnedpics).') and pinme.userid=\''.$userid.'\'','')->where('image.imageid in (?) and pinme.imageid is null',$pinnedpics);
					}
				}
			}
			else if($type=='image')
			{
				$sql=$this->_db->select()->from('image','')->joinRight(array('image1'=>new Zend_Db_Expr('('.$sub_sql.')')), 'image1.albumid=image.albumid')->where('image.imageid=?',$albumid);
			}
			$sql=$sql->where('image.accepted=\'yes\'')->order('image.date desc');
			if(isset($from))
			$sql=$sql->limit($this->registry->limit,$from);
			$result=$this->_db->fetchAssoc($sql);
			if(count($result)==$this->registry->limit)
				$final_results['loadmore']=true;
			else
				$final_results['loadmore']=false;
			$myfrnds=$this->authIdentity->friends;
			$myid=$this->authIdentity->userid;
			foreach($result as $imageid=>$values)
			{
				$privacy=$values['pt'];
				$specific=unserialize($values['specificlist']);
				$hiddenlist=unserialize($values['hiddenlist']);
				$rusrfrnds=unserialize($values['friendlist']);
				
				$cprivacy=$values['cpt'];
				
				$result[$imageid]['iscommentable']=false;
				if((($cprivacy=='public'||($cprivacy=='friends' && in_array($values['ruserid'],$myfrnds))||($cprivacy=='fof' && count(array_intersect($rusrfrnds,$myfrnds)>=1) )||($cprivacy=='specific' && in_array($myid, unserialize($values['csu']))))&& !in_array($values['ruserid'],  $this->authIdentity->blocklistmerged) && !in_array($myid, unserialize($values['ciu'])))|| $myid==$values['ruserid'] || $myid==$values['userid']){
					$result[$imageid]['iscommentable']=true;
				}
				
				if((($privacy=='public'||($privacy=='friends' && in_array($values['ruserid'],$myfrnds))||($privacy=='fof' && count(array_intersect($rusrfrnds,$myfrnds)>=1) )||($privacy=='specific' && in_array($myid, $specific)))&& !in_array($values['ruserid'],  $this->authIdentity->blocklistmerged) && !in_array($myid, $hiddenlist))|| $myid==$values['ruserid'] || $myid==$values['userid']){
					$album_name=$result[$imageid]['name'];
					$album_userid=$result[$imageid]['ruserid'];
				}
				else
					unset ($result[$imageid]);
			}
			$album=array();
			if(empty($result)){
				$sql=$this->_db->select()->from('album',array('albumid','name','userid'))->where('albumid=?',array($albumid));
				$album=$this->_db->fetchAssoc($sql);
			}
			else
			{
				$album[$albumid]['albumid']=$albumid;
				$album[$albumid]['name']=$album_name;
				$album[$albumid]['userid']=$album_userid;

				
			}
			$final_result['images']=$result;
			$final_result['album']=$album[$albumid];

			$final_results['results']=$final_result;
			return $final_results;
		}
	}
	
	public function getComments($imageid,$from){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image_comments')->joinLeft('image', 'image_comments.imageid=image.imageid',array('suserid'=>'userid','commentcount','cpt','ciu','csu','pt','specificlist','hiddenlist'))
					->joinLeft('album','album.albumid=image.albumid','userid as ruserid' )
					->joinLeft('friends_vote', 'friends_vote.userid=album.userid','friendlist')
					->joinLeft('freniz', 'freniz.userid=image_comments.userid',array('type','username','url'))
					->joinLeft('image as userimage', 'userimage.imageid=freniz.propic',array('user_imgurl'=>'url'))
					->where('image_comments.imageid in(?)',$imageid)->order('commentid desc');
			if(!empty($from)){
					$sql=$sql->limit($this->registry->commentlimit,$from);	
			}
			else $sql=$sql->limit($this->registry->commentDefaultLimit);
			$result=$this->_db->fetchAssoc($sql);
			if(reset($result)){
			$key=key($result);
			$privacy=$result[$key]['pt'];
			$specific=unserialize($result[$key]['specificlist']);
			$hidden=unserialize($result[$key]['hiddenlist']);
			$friends=unserialize($result[$key]['friendlist']);
			}
			foreach($result as $key => $values ){
				if((($privacy=='public'||($privacy=='friends' && in_array($values['ruserid'],$this->authIdentity->friends)) || ($privacy=='fof' && (count(array_intersect($friends, $this->authIdentity->userid))>=1 || in_array($values['ruserid'], $this->authIdentity->friends))) || ($privacy=='specific' && in_array($values['ruserid'], $specific))) && !in_array($values['ruserid'], array_merge($this->authIdentity->blocklistmerged,$hidden))) || $this->authIdentity->userid==$values['suserid'] || $this->authIdentity->userid==$values['ruserid']){
					
				}
				else unset($result[$key]);
			}
			$final_result['result']=$result;
			$sql=$this->_db->select()->from('commentactivity','max(id) as maxcomment');
			$result=$this->_db->fetchRow($sql);
			$final_result['maxcomment']=$result['maxcomment'];
			return $final_result;
		}
	}
	
	
	public function getPinnedPeople($imageid){
		$result=$this->find($imageid);
		if(count($result)>0){
			$result=$result[0];
			$pinnedpeople=unserialize($result['pinnedpeople']);
			if(!empty($pinnedpeople)){
			$sql=$this->_db->select()->from('freniz',array('userid','username','url'))->joinLeft('pinreq',"freniz.userid=pinreq.userid and pinreq.userid in (".$this->_db->quote($pinnedpeople).") and pinreq.imageid='$imageid'",'')
					->joinLeft('image', 'freniz.propic=image.imageid','url as imageurl')
					->where('freniz.userid in (?) and pinreq.userid is NULL', $pinnedpeople);
			$result=$this->_db->fetchAssoc($sql);
			return $result;
			}
			return array();
		}
	}
	
	/*
	public function getComments($imageids,$from=null,$limit=null)
	{
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image_comments')
				->joinLeft('freniz', 'freniz.userid=image_comments.userid',array('type','username','url'))
				->joinLeft('image as userimage', 'userimage.imageid=freniz.propic',array('user_imgurl'=>'url'))
				->where('image_comments.imageid in(?)',$imageids);
			if(!is_array($imageids))
				$sql->limit($limit,$from);
			$result=$this->_db->fetchAssoc($sql);
			return $result;
		}
	}
	*/
	public function getArrayOfImages($imageids){
		if(isset($this->authIdentity) && isset($imageids)){
			$sql=$this->_db->select()->from('image')
			->joinLeft('album', 'image.albumid=album.albumid',array('name','ruserid'=>'userid'))
			->joinLeft('freniz as rfreniz','rfreniz.userid=album.userid',array('rusername'=>'username','ruser_url'=>'url'))
			->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist')
			->joinLeft('freniz', 'freniz.userid=image.userid',array('type','username','user_url'=>'url'))
			->joinLeft('image as userimage', 'userimage.imageid=freniz.propic',array('user_imgurl'=>'url'))
			->where('image.imageid in (?)',$imageids);
			$result=$this->_db->fetchAssoc($sql);
			$myfrnds=$this->authIdentity->friends;
			$myid=$this->authIdentity->userid;
			foreach($result as $imageid=>$values)
			{
				$privacy=$values['pt'];
				$specific=unserialize($values['specificlist']);
				$hiddenlist=unserialize($values['hiddenlist']);
				$rusrfrnds=unserialize($values['friendlist']);
				
				
				$cprivacy=$values['cpt'];
				
				$result[$imageid]['iscommentable']=false;
				if((($cprivacy=='public'||($cprivacy=='friends' && in_array($values['ruserid'],$myfrnds))||($cprivacy=='fof' && count(array_intersect($rusrfrnds,$myfrnds)>=1) )||($cprivacy=='specific' && in_array($myid, unserialize($values['csu']))))&& !in_array($values['ruserid'],  $this->authIdentity->blocklistmerged) && !in_array($myid, unserialize($values['ciu'])))|| $myid==$values['ruserid'] || $myid==$values['userid']){
					$result[$imageid]['iscommentable']=true;
				}
				if((($privacy=='public'||($privacy=='friends' && in_array($values['ruserid'],$myfrnds))||($privacy=='fof' && count(array_intersect($rusrfrnds,$myfrnds)>=1) )||($privacy=='specific' && in_array($myid, $specific)))&& !in_array($values['ruserid'],  $this->authIdentity->blocklistmerged) && !in_array($myid, $hiddenlist))|| $myid==$values['ruserid'] || $myid==$values['userid']){
					$result[$imageid]['comments']=$this->getComments($imageid,0);
				}
				else{
					unset ($result[$imageid]);
					return $imageid;
				}
			}
			return $result;
			
		}
	}
	private function filter_by_value ($array, $index, $value){
		if(is_array($array) && count($array)>0)
		{
			foreach(array_keys($array) as $key){
				$temp[$key] = $array[$key][$index];
	
				if ($temp[$key] == $value){
					$newarray[$key] = $array[$key];
				}
			}
		}
		return $newarray;
	} 
	public function editImageData($imagearray)
	{
	    foreach($imagearray as $id => $image)
	    {
	        if($image['title']=="" || !isset($image['title']))
	            $image['title']='Title';
	        if($image['description']=="" || !isset($image['description']))
	            $image['description']='Description';
	        $this->update($image, array('id=?'=>$id));
	    }
	}
	public function approveImages($imageids)
	{
	    if(isset($this->authIdentity)&& isset($imageids)){
	    	$sql=$this->_db->select()->from('image')->joinLeft('album','album.albumid=image.album',array('ruserid'=>'userid'))->where("imageid in (?) and album.userid='{$this->authIdentity->userid}'",$imageids);
	    	$result=$this->_db->fetchAssoc($sql);
	    	$activityModel=new Application_Model_Activity($this->getAdapter());
	    	$now=new Zend_Db_Expr('now()');
	    	foreach($result as $imageid => $values){
		    		$activity=array('userid'=>$result['userid'],'ruserid'=>$result['ruserid'],'contentid'=>$result['imageid'],'title'=>'post image','contenttype'=>'image','contenturl'=>'image.php?imageid='.$result['imageid'],'date'=>$now,'alternate_contentid'=>'image_'.$result['imageid']);
		    	
	    	}
	    	$this->update("accepted='yes'", array('imageid in (?)',array_keys($result)));
	    	
	    }
		       
	}
	public function denyImages($imageids){
		if(isset($this->authIdentity) && isset($imageids)){
			$sql=$this->_db->select()->from('image',array('imageid','url','albumid'))->joinLeft('album','album.albumid=image.albumid',array('ruserid'=>'userid'))->where("imageid in (?) and accepted='no' and album.userid='{$this->authIdentity->userid}'",$imageids);
			$result=$this->_db->fetchAssoc($sql);
			$this->deleteImageSources($result);
			$this->delete(array('imageid in (?)'=>array_keys($result)));
			
		}
	}
	public function deleteImages($imageid){
		if(isset($this->authIdentity)){
			$userid=$this->authIdentity->userid;
			$propics=array($this->authIdentity->propic);
			$imageid=explode(',',$imageid);
			if($this->authIdentity->secondarypic1){
				$secpic=explode(',',$this->authIdentity->secondarypic1);
				array_push($propics, $secpic[0]);
				
			}
			$sql=$this->_db->select()->from('image',array('imageid','url','userid'))->joinLeft('album', 'album.albumid=image.albumid',array('ruserid'=>'userid'))->where("(image.userid='$userid' or album.userid='$userid') and imageid not in(?)",$propics)->where("imageid in (?)",$imageid);
			$result=$this->_db->fetchAssoc($sql);
			if(!empty($result))
			{
				$this->deleteImageSources($result);
				$this->delete(array('imageid in (?)'=>array_keys($result)));
			}
			$propics1=array_intersect($propics, $imageid);
			if(is_array($imageid) && count($propics1)>0 && $this->authIdentity->userid!=1){
				if($this->authIdentity->type=='user'){
					foreach($propics1 as $imageid){
						if($imageid==$this->authIdentity->propic){
					$this->_db->update('freniz', array('propic'=>1),array('userid=?'=>$this->authIdentity->userid));
					$this->_db->update('user_info', array('propic'=>1),array('userid=?'=>$this->authIdentity->userid));
					$this->authIdentity->propic=1;
						}
						else {
							$this->_db->update('user_info', array('secondarypic1'=>10),array('userid=?'=>$this->authIdentity->userid));
							$this->authIdentity->secondarypic1=10;
						}
					}
					$sql=$this->_db->select()->from('image')->where('imageid in (?)',$propics1);
					$result=$this->_db->fetchAssoc($sql);
					$this->deleteImageSources($result);
					$this->delete(array('imageid in (?)'=>$propics1));
						
				}
				elseif($this->authIdentity->type=='page'){
					
					$this->_db->update('freniz', array('propic'=>2),array('userid=?'=>$this->authIdentity->userid)); 
					$this->_db->update('pages', array('pagepic'=>2),array('pageid=?'=>$this->authIdentity->userid)); 
					$images[$this->authIdentity->propic]=$result[0];
					$this->deleteImageSources($this->authIdentity->propic);
					$this->delete(array('imageid=?'=>$this->authIdentity->propic));
				}
			}
			return true;
		}
	}
	private function deleteImageSources($result)
	{
		foreach($result as $key => $values){
			unlink('../images/'.$values['url']);
			unlink('../images/original/'.$values['url']);
			unlink('../images/32/32_'.$values['url']);
			unlink('../images/50/50_'.$values['url']);
			unlink('../images/75/75_'.$values['url']);
			unlink('../images/200/200_'.$values['url']);
			unlink('../images/500/500_'.$values['url']);
		}	
	}
public function uploadImage($album,$description=NULL){
		if(isset($this->authIdentity) && isset($album)){
			
			$allowedExtensions = array();
			// max file size in bytes
			$sizeLimit = 2 * 1024 * 1024;
			$uploader=new Image_Upload($allowedExtensions,$sizeLimit);

			
			
			$sql=$this->_db->select()->from('album',array('userid','canupload','pt','specificlist','hiddenlist','cpt','ciu','csu'))->joinLeft('freniz', 'album.userid=freniz.userid','type')
			->joinLeft('privacy', "freniz.userid=privacy.userid and freniz.type='user'",array('advancedprivacyimage','autoacceptusers','blockactivityusers'))->joinLeft('pages',"freniz.userid=pages.pageid and freniz.type='page'",array('bannedusers','admins','vote'))->where('albumid=?',$album);
			$result=$this->_db->fetchRow($sql);
			//return $result;
			$myid=$this->authIdentity->userid;$isvalid=false;
				
			$final_data=array('title'=>'title','description'=>'description','albumid'=>$album,'userid'=>$myid,'date'=>new Zend_Db_Expr('now()'),'pinnedpeople'=>'a:0:{}','vote'=>'a:0:{}','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','notifyusers'=>'a:0:{}','reqpinusers'=>'a:0:{}','pinmereq'=>'a:0:{}','comments'=>'a;0:{}');
			if(!empty($description))
				$final['description']=$description;
			$final_data['dontnotify']='a:0:{}';
			if($myid!=$result['userid']){
				if($this->authIdentity->type=='user' && $result['type']=='user'){
					if(($result['canupload']=='friends' && !in_array($result['userid'], $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, unserialize($result['ciu'])) && in_array($result['userid'], $this->authIdentity->friends)) ){
						if(!in_array($myid, unserialize($result['blockactivityusers']))){
							if($result['advancedprivacyimage']=='on' ){
								if(in_array($_SESSION['userid'], unserialize($result['autoacceptusers']))){
										
											$isvalid=true;
											$final_data['accepted']='yes';
									}
									else
									{
										$isvalid=true;
										//$result = $uploader->handleUpload('../images/',$row['pt'],$row['specificlist'],$row['hiddenlist'],$row['userid'],'not');
										
									}
									$final_data['pt']=$result['pt'];
									$final_data['specificlist']=$result['specificlist'];
									$final_data['hiddenlist']=$result['hiddenlist'];
									$final_data['ciu']=$result['ciu'];
									$final_data['csu']=$result['csu'];
									$final_data['cpt']=$result['cpt'];
							}
							else{
								$isvalid=true;
								$final_data['accepted']='yes';
								$final_data['pt']=$result['pt'];
								$final_data['specificlist']=$result['specificlist'];
								$final_data['hiddenlist']=$result['hiddenlist'];
								$final_data['ciu']=$result['ciu'];
								$final_data['csu']=$result['csu'];
								$final_data['cpt']=$result['cpt'];
							}
						}
					}
				}
				else if($result['type']=='page')
				{
					if(in_array($myid, unserialize($result['admins'])))
					{
						$isvalid=true;
						$final_data['accepted']='yes';
						$final_data['pt']=$result['pt'];
						$final_data['specificlist']=$result['specificlist'];
						$final_data['hiddenlist']=$result['hiddenlist'];
						$final_data['ciu']=$result['ciu'];
						$final_data['csu']=$result['csu'];
						$final_data['cpt']=$result['cpt'];
						
					}
					else if(!in_array($result['userid'], unserialize($result['bannedusers'])) && in_array($myid,unserialize($result['vote']))){
						$isvalid=true;
						$final_data['accepted']='yes';
						$final_data['pt']=$result['pt'];
						$final_data['specificlist']=$result['specificlist'];
						$final_data['hiddenlist']=$result['hiddenlist'];
						$final_data['ciu']=$result['ciu'];
						$final_data['csu']=$result['csu'];
						$final_data['cpt']=$result['cpt'];
						
					}
				}
			}
			else {
				$isvalid=true;
				$final_data['accepted']='yes';
				$final_data['pt']=$result['pt'];
				$final_data['specificlist']=$result['specificlist'];
				$final_data['hiddenlist']=$result['hiddenlist'];
				$final_data['ciu']=$result['ciu'];
				$final_data['csu']=$result['csu'];
				$final_data['cpt']=$result['cpt'];
			}
			if($isvalid){
					
				//$final_data['upload']=$uploader->handleUpload('/var/www/freniz_zend/public/images/');
				$upload_result=$uploader->handleUpload('images/');
					
				if(!isset($upload_result['error'])){
					try{
					$final_data['url']=$upload_result['imgurl'];
					$updtdid=$this->insert($final_data);
					if($final_data['accepted']=='yes'){
						if($this->authIdentity->userid!=$result['userid'] && $result['type']!='group'){
							$notify_data=array('userid'=>$result['userid'],'contenturl'=>'image/'.$updtdid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> added image on your chart','userpic'=>$this->authIdentity->propic);
							$this->_db->insert('notifications', $notify_data);
								
						}
					$sql=$this->select()->from($this,new Zend_Db_Expr('count(imageid) as count'))->where("albumid='$album' and date > date_sub(now(), interval 3 day)");
					$result1=$this->fetchRow($sql)->toArray();
					$activity_data=array('userid'=>$myid,'ruserid'=>$result['userid'],'contentid'=>$updtdid,'title'=>'post image','contenttype'=>'image','contenturl'=>'image.php?imageid='.$updtdid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'image_'.$updtdid);
					if($result1['count']>1 && $result['userid']==$myid){
						$activity_data['contentid']=$album;
						$activity_data['title']='posted '.$result1['count'].' images';
						$activity_data['contenttype']='album';
						$activity_data['contenturl']='album.php?albumid='.$album;
						$activity_data['alternate_contentid']='album_'.$album;
						
					}
					$this->_db->insert('activity', $activity_data);
					$upload_result['fileid']=$updtdid;
					}
					return $upload_result;
				 }
				 catch(Exception $e){
				 	return array($e->getMessage());
				 	
				 }
				 /*$result=mysql_query("select count(imageid) count from image where albumid='".$_REQUEST['album']."' and date > date_sub(now(), interval 3 day)");
                fwrite($target, "mysql:".  mysql_error());
                $count=0;
                while($row=  mysql_fetch_assoc($result))
                {
                    $count=$row['count'];
                }
                if($count==0)
                mysql_query("insert into activity (userid,ruserid,contentid,title,contenttype,contenturl,date,alternate_contentid) values ('".$_SESSION['userid']."','".$ruser."','".$updtdid."','post image','image','image.php?imageid=".$updtdid."',now(),'image_".$updtdid."')");
                else{
                    mysql_query ("insert into activity (userid,ruserid,contentid,title,contenttype,contenturl,date,alternate_contentid) values ('".$_SESSION['userid']."','".$ruser."','".$_REQUEST['album']."','posted $count images','album','album.php?albumid=".$_REQUEST['album']."',now(),'album_".$_REQUEST['album']."')");
                }*/
				}
				else
					return $upload_result;
			}
		}
		
	}
	
	
	
	
	

	public function doComment($imageid,$text){
		if(isset($this->authIdentity)){
			$a=array();
			$select=$this->_db->select()->from($this->_name,array('suserid'=>'userid','notifyusers','dontnotify','pinnedpeople','vote','cpt','ciu','csu'))->joinLeft('album', 'image.albumid=album.albumid',array('ruserid'=>'userid'))
				->joinLeft('freniz','freniz.userid=album.userid',array('type as rtype','username as rusername'))
				->joinLeft('pages','pages.pageid=album.userid',array('admins','canpost','page_vote'=>'vote','bannedusers'))
				->where('imageid=?',$imageid);
			$result=$this->_db->fetchRow($select);
				
			if(isset($result)){
				$commentdata=array('imageid'=>$imageid,'userid'=>$this->authIdentity->userid,'comment'=>trim($text),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
				if($this->authIdentity->type=='user' && $result['rtype']=='user'){
					$ignorelist=unserialize($result['ciu']);
					if((($result['cpt']=='public' || ($result['cpt']=='friends' && in_array($result['ruserid'], $this->authIdentity->friends)) ||($result['cpt']=='fof' && ((count(array_intersect(unserialize($result['friendlist']), $this->authIdentity->friends))>=1) || in_array($result['ruserid'], $this->authIdentity->friends))) || ($result['cpt']=='specific' && in_array($this->authIdentity->userid, unserialize($result['csu'])) )) && !in_array($this->authIdentity->userid, $ignorelist) && !in_array($result['userid'], $this->authIdentity->blocklistmerged) ) || $this->authIdentity->userid==$result['suserid'] || $this->authIdentity->userid==$result['ruserid'] ){
					//if(($result['post']=='friends' && !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && in_array($result['ruserid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid==$result['ruserid'] || $this->authIdentity->userid==$result['susersid']){
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$imageid,'title'=>'commented on','contenttype'=>'image','contenturl'=>'image.php?imageid='.$imageid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'image_'.$imageid);
						$this->_db->insert('image_comments', $commentdata);
						$commentid=$this->_db->lastInsertId('image_comments');
						$commentactivity_data=array('commentid'=>$commentid,'comment'=>trim($text),'userid'=>$this->authIdentity->userid,'objid'=>$imageid,'type'=>'image');
						$this->_db->insert('commentactivity', $commentactivity_data);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
						 
						 
						$notifyusers=unserialize($result['notifyusers']);
						$pinnedpeople=unserialize($result['pinnedpeople']);
						
						if(!in_array($this->authIdentity->userid, $notifyusers))
							array_push($notifyusers, $this->authIdentity->userid);
						if(!empty($pinnedpeople))
						{
							$sql=$this->_db->select()->from('freniz','userid')->joinLeft('pinreq', 'pinreq.userid=freniz.userid and pinreq.imageid='.$this->_db->quote($imageid).' and pinme.userid in ('.$this->_db->quote($pinnedpeople).')','')
									->where('pinreq.userid is NULL and freniz.userid in (?)',$pinnedpeople);
							$result1=$this->_db->fetchAssoc($sql);
							$notifyusers1=array_merge($notifyusers,array_keys($result1));
						}
						
						$votes=unserialize($result['vote']);
						$dontnotify=unserialize($result['dontnotify']);
						$notifyusers1=array_unique(array_diff(array_merge($notifyusers1,array($result['suserid'],$result['ruserid']),$votes),$dontnotify,array($this->authIdentity->userid)));
						//print_r($notifyusers1);
						if(!empty($notifyusers1)){
							$query='insert into notifications(userid,contenturl,notification,userpic) values ';
							$userpic=$this->authIdentity->propic;
							foreach ($notifyusers1 as $user){
								if(sizeof($notifyusers)>2)
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($notifyusers)-2)." other commented on";
								}
								else
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  commented on";
								}
								if($user==$result['suserid'] && $user==$result['ruserid'])
								{
									$notificationtext.=" your image";
								}
								else if($user==$result['suserid']){
									$notificationtext.=" your image of <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s chart";
								}
								else if($user==$result['ruserid'])
								{
									$notificationtext.=" your image";
								}
								else
								{
									$notificationtext.=" <a href='".$result['ruserid']."'>".$result['rusername']."</a>'s image";
								}
								$query.=' ('.$this->_db->quote(array($user,'image/'.$imageid,$notificationtext,$userpic)).'),';
							}
							$query=substr($query, 0,-1);
							$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
							$this->_db->query($query);
						}
						
						$update_imagedata=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_imagedata, "imageid='$imageid'");
	
					}
				}
				else if($result['type']=='page'){
					$votes=unserialize($result['votes']);
					$bannedusers=unserialize($result['bannedusers']);
					$admins=unserialize($result['admins']);
					if((($result['canpost']=='public' ||($result['canpost']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['ruserid']   )){
						$this->_db->insert('comment', $commentdata);
						$commentid=$this->_db->lastInsertId('image_comments');
						$commentactivity_data=array('commentid'=>$commentid,'comment'=>mysql_real_escape_string(trim($text)),'userid'=>$this->authIdentity->userid,'objid'=>$imageid,'type'=>'image');
						$this->_db->insert('commentactivity', $commentactivity_data);
						
						$notify_data=array('userid'=>$result['ruserid'],'contenturl'=>'image/'.$imageid,'notification'=>'<a href="'.$this->authIdentity->userid.'">'.$this->authIdentity->username.'</a> commented on your image','userpic'=>$this->authIdentity->propic);
						$this->_db->insert('notifications', $notify_data);
						
						
						$update_data=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_data, "imageid='$imageid'");
						//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
							
					}
	
				}
					
			}
		return array("id"=>$commentid,"time"=>date('c'),'content'=>mysql_real_escape_string($text),"propic_url"=>$this->authIdentity->propic_url,"url"=>$this->authIdentity->url,"username"=>$this->authIdentity->username,"status"=>"success");
		
		}
	}
	

	public function cropImage( $source, $dest,$resolutions,$x=null,$y=null,$nw=null, $nh=null,$orgImg=false) {
		$size = getimagesize($source);

		$w = $size[0];

		$h = $size[1];
		if(isset($x) && isset($y) && !empty($nw) && !empty ($nh) ){
			if(!orgImg){

			$base_h;$base_w;

			if($w>=200 || $h>=200){

				if($w>$h)

				{

					$base_w=200;

					$base_h=($h*$base_w)/$w;

				}

				else

				{

					$base_h=200;

					$base_w=($w*$base_h)/$h;

				}

			}

			else {

				$base_h=$h;

				$base_w=$w;

			}

	

			$x=($x/$base_w)*$w;

			$y=($y/$base_h)*$h;

			$nw=($nw/$base_w)*$w;

			$nh=($nh/$base_h)*$h;
			//return array($x,$y,$nw,$nh);
			}

		}

		else {

			$x=0;$y=0;$nw=$w;$nh=$h;

		}
		$quality=0;
		switch($size['mime']) {

		case 'image/gif':

				$simg = imagecreatefromgif($source);

				$create='imagepng';
				break;

				case 'image/png':

				case 'image/x-png':

				$simg = imagecreatefrompng($source);

				$create='imagepng';
				break;

				default :

				$simg = imagecreatefromjpeg($source);

				$create='imagejpeg';
				$quality=100;

				break;

	

	}

	$dimg = imagecreatetruecolor($nw, $nh);
	
	imagecopyresampled($dimg,$simg,0,0,$x,$y,$nw,$nh,$nw,$nh);
	$create($dimg,$dest,$quality);

	imagedestroy($simg);

	$c=new Image_Compressimage($dest, $resolutions);
	//return $c->res;

	}

	
	public function crop($imageid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name)->where("imageid='$imageid'");
			$result=$this->_db->fetchRow($sql);
			return $result;
		}
	}
	
	public function SetProfilePicture($imageid,$deletesrc=NULL,$x=NULL,$y=NULL,$width=NULL,$height=NULL){
		if(isset($this->authIdentity)){
		$select_data=array('url','albumid','userid','pinnedpeople');

		$sql=$this->_db->select()->from($this->_name,$select_data)
								->joinLeft('album', 'image.albumid=album.albumid','userid as ruserid')
								->where("imageid='$imageid'");
		$result=$this->_db->fetchRow($sql);
		
			$url=$result['url'];
			$albumid=$result['albumid'];
			$pinnedpeople=unserialize($result['pinnedpeople']);

			$suserid=$result['userid'];

				$ruserid=$result['ruserid'];
				$myid=$this->authIdentity->userid;
				$type=$this->authIdentity->type;
				$user_albumid=$this->authIdentity->propicalbum;
			$sql=$this->_db->select()->from('album',array('cpt','ciu','csu'))->where('albumid=?',$user_albumid);
			$result1=$this->_db->fetchRow($sql);
		if($user_albumid==$albumid ){
				if($deletesrc=='true'){

				$resolutions=array(32,50,75,200);

				$this->cropImage( 'images/'.$url, 'images/'.$url,$resolutions,$x, $y, $width, $height);

			}
			$filename=$url;
			$this->_db->update('freniz', array('propic'=>$imageid),array('userid=?'=>$myid));
			if($type=='user'){
			$this->_db->update('user_info', array('propic'=>$imageid),array('userid=?'=>$myid));
			}elseif($type=='page'){
			$this->_db->update('pages', array('pagepic'=>$imageid),array('pageid=?'=>$myid));
			}
			$this->authIdentity->propic=$imageid;
			$updtid=$imageid;
			//mysql_query("insert into image (title,description,url,albumid,userid,date,pinnedpeople,vote,pt,specificlist,hiddenlist,notifyusers,accepted,reqpinusers,pinmereq,comments) values('title','description','".$filename.'.'.$ext."','".$_REQUEST['album']."','".$_SESSION['userid']."',now(),'".$a."','".$a."','".$pt."','".$specific."','".$hidden."','a:0:{}','".$accepted."','a:0:{}','a:0:{}','a:0:{}')");

			//$Imgs->setAsProPic($_REQUEST['imageid'],$_REQUEST['deletesrc'],$_REQUEST['x'],$_REQUEST['y'],$_REQUEST['width'],$_REQUEST['height']);
		
		}
	

		else if($myid==$suserid || $myid==$ruserid || in_array($myid, $pinnedpeople) )

		{

			$pathinfo=pathinfo($url);

			$ext=$pathinfo['extension'];

			$filename=md5(uniqid());

			while(file_exists('images/'.$filename.'.'.$ext))

				$filename.=rand(10,25);

			$resolutions=array(32,50,75,200,500,0);

			$this->cropImage( 'images/'.$url, 'images/'.$filename.'.'.$ext,$resolutions,$x, $y, $width, $height);

			$insert_data=array('title'=>'title','description'=>'description','url'=>$filename.'.'.$ext,'albumid'=>$user_albumid,'userid'=>$myid,'date'=>new Zend_Db_Expr('Now()'),'pinnedpeople'=>'a:0:{}','vote'=>'a:0:{}','pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','notifyusers'=>'a:0:{}','accepted'=>'yes','reqpinusers'=>'a:0:{}','pinmereq'=>'a:0:{}','comments'=>'a:0:{}','cpt'=>$result1['cpt'],'ciu'=>$result1['ciu'],'csu'=>$result1['csu']);
			$updtid=$this->insert($insert_data);
				$this->_db->update('freniz', array('propic'=>$updtid),array('userid=?'=>$myid));
				if($type=='user'){
				$this->_db->update('user_info', array('propic'=>$updtid),array('userid=?'=>$myid));
				}else{
					$this->_db->update('pages', array('pagepic'=>$updtid),array('pageid=?'=>$myid));
				}
				$this->authIdentity->propic=$updtid;
				}
			
			if(!empty($updtid)){
			$insert_act=array('userid'=>$myid,'ruserid'=>$myid,'contentid'=>$updtid,'title'=>'changed propic','contenttype'=>'propic','contenturl'=>'propic.php','date'=>new Zend_Db_Expr('Now()'),'alternate_contentid'=>'propic');

		$this->_db->insert('activity', $insert_act);
		
			return array('status'=>'sucess','url'=>$filename,'fileid'=>$updtid);
			}
			else {
				return array('error'=>'you do not have permission to set this picture as your profile picture','status'=>'error');
			}	

		
		}

	}

	

	public function setSecondarypicture($imageid,$deletesrc=NULL,$x=NULL,$y=NULL,$width=NULL,$height=NULL,$secpicno,$top=0){
		if(isset($this->authIdentity)){
			$select_data=array('url','albumid','userid','pinnedpeople');
	
			$sql=$this->_db->select()->from($this->_name,$select_data)
	
			->joinLeft('album', 'image.albumid=album.albumid','userid as ruserid')
	
			->where("imageid='$imageid'");
	
			$result=$this->_db->fetchRow($sql);
	
			$myid=$this->authIdentity->userid;
	
			$url=$result['url'];
	
			$albumid=$result['albumid'];
	
			$pinnedpeople=unserialize($result['pinnedpeople']);
	
			$suserid=$result['userid'];
	
			$ruserid=$result['ruserid'];
			$user_albumid=$this->authIdentity->secondarypicalbum;
	
			$sql=$this->_db->select()->from('album',array('cpt','ciu','csu'))->where('albumid=?',$user_albumid);
			$result1=$this->_db->fetchRow($sql);
	
	
			if($user_albumid==$albumid ){
	
				if($deletesrc=='true'){
	
					$resolutions=array(32,50,75,200);
	
					$this->cropImage( 'images/'.$url, 'images/'.$url,$resolutions,$x, $y, $width, $height);
	
				}
	
				if($secpicno==1)
	
					$this->_db->update('user_info', array('secondarypic1'=>$imageid.','.$top),array('userid=?'=>$myid));
				else
	
					$this->_db->update('user_info', array('secondarypic2'=>$imageid),array('userid=?'=>$myid));
	
	
				//mysql_query("insert into image (title,description,url,albumid,userid,date,pinnedpeople,vote,pt,specificlist,hiddenlist,notifyusers,accepted,reqpinusers,pinmereq,comments) values('title','description','".$filename.'.'.$ext."','".$_REQUEST['album']."','".$_SESSION['userid']."',now(),'".$a."','".$a."','".$pt."','".$specific."','".$hidden."','a:0:{}','".$accepted."','a:0:{}','a:0:{}','a:0:{}')");
	
				//$Imgs->setAsProPic($_REQUEST['imageid'],$_REQUEST['deletesrc'],$_REQUEST['x'],$_REQUEST['y'],$_REQUEST['width'],$_REQUEST['height']);
	
			}
	
			else if($myid==$suserid || $myid==$ruserid && in_array($pinnedpeople, $myid))
	
			{
	
				$pathinfo=pathinfo($url);
	
				$ext=$pathinfo['extension'];
	
				$filename=md5(uniqid());
	
				while(file_exists('images/'.$filename.'.'.$ext))
	
					$filename.=rand(10,25);
	
				$resolutions=array(32,50,75,200,500,0);
	
				$this->cropImage( 'images/'.$url, 'images/'.$filename.'.'.$ext,$resolutions,$x, $y, $width, $height);
	
				$insert_data=array('title'=>'title','description'=>'description','url'=>$filename.'.'.$ext,'albumid'=>$user_albumid,'userid'=>$myid,'date'=>new Zend_Db_Expr('Now()'),'pinnedpeople'=>'a:0:{}','vote'=>'a:0:{}','pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','notifyusers'=>'a:0:{}','accepted'=>'yes','reqpinusers'=>'a:0:{}','pinmereq'=>'a:0:{}','comments'=>'a:0:{}','cpt'=>$result1['cpt'],'ciu'=>$result1['ciu'],'csu'=>$result1['csu']);
	
				$updtid=$this->insert($insert_data);
	
				if($secpicno==1)
	
					$this->_db->update('user_info', array('secondarypic1'=>$updtid),array('userid=?'=>$myid));
	
				else
	
					$this->_db->update('user_info', array('secondarypic2'=>$updtid),array('userid=?'=>$myid));
	
	
			}
	
				
	
				
	
			$insert_act=array('userid'=>$myid,'ruserid'=>$myid,'contentid'=>$updtid,'title'=>'changed secpic','contenttype'=>'secpic','contenturl'=>'secpic.php','date'=>new Zend_Db_Expr('Now()'),'alternate_contentid'=>'secpic');
	
			$this->_db->insert('activity', $insert_act);
	
			return array('status'=>'sucess');
				
				
		}
	}
	public function tobeReviewed($from){

		if(isset($this->authIdentity)){
			
			$sql=$this->_db->select()->from('image')->joinLeft('album', 'album.albumid=image.albumid',array('name','ruserid'=>'userid'))->joinLeft('freniz','image.userid=freniz.userid',array('type','username','user_url'=>'url'))->joinLeft('image as simage','freniz.propic=simage.imageid',array('surl'=>'url'))
			->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist')
			->where('album.userid=? and image.accepted=\'not\'',$this->authIdentity->userid)->order('image.date desc')->limit($this->registry->limit,$from);
			//$sql=$this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)->joinLeft('album', 'album.albumid=image.albumid',array('name','ruserid'=>'userid'))->joinLeft('freniz','image.userid=freniz.userid',array('type','username','user_url'=>'url'))->joinLeft('user_info', 'user_info.userid=freniz.userid','propic')->joinLeft('image as simage','user_info.propic=simage.imageid',array('surl'=>'url'))

			//->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist')

			//->joinLeft('pages','freniz.userid=pages.pageid','pagepic')->joinLeft('image as pimage','pages.pagepic=pimage.imageid',array('purl'=>'url'))

			//->where('album.userid=? and image.accepted=\'not\'',$this->authIdentity->userid)->order('image.date desc')->limit($this->registry->limit,$from);
			$results=$this->_db->fetchAssoc($sql);

			if(count($results)==$this->registry->limit)
				$final_results['loadmore']=true;
			else $final_results['loadmore']=false;
			$final_results['results']['images']=$results;
			return $final_results;
		}

	}

	public function reviewImages($imageids=null,$accept=true){

		if (isset($this->authIdentity->userid)){

			$sql=$this->_db->select()->from('image',array('imageid','userid as ruserid','albumid'))
			->joinLeft('freniz', 'freniz.userid=image.ruserid',array('username'))
			->joinLeft('album', 'album.albumid=image.imageid','userid as ruserid')->where('album.userid=? and accepted=\'not\'',$this->authIdentity->userid);

			if(isset($imageids)){

				$sql=$sql->where('imageid in (?)',$imageids);

			}

			$result=$this->_db->fetchAssoc($sql);

			$imageids=array_keys($result);

			if(!empty($imageids)){

				if($accept)

					$this->update(array('accepted'=>'yes'),array('imageid in (?)'=>$imageids));

				else

					$this->delete(array('imageid in (?)',$imageids));

			}

		}

	}

	

	

	public function addpin($imageid,$userids){

		if(isset($this->authIdentity)){

			$sql=$this->_db->select()->from('image',array('imageid','albumid','userid','pinnedpeople'))->joinLeft('album', 'album.albumid=image.albumid','userid as ruserid')->where('imageid=?',$imageid);

			$result=$this->_db->fetchAssoc($sql);
			$result=$result[$imageid];

			if($result['ruserid']==$this->authIdentity->userid){

				$pinnedpeople=unserialize($result['pinnedpeople']);

				$userids=array_diff($userids, $pinnedpeople);
				$userids=array_intersect($userids, $this->authIdentity->friends);
				$query = 'INSERT INTO ' . $this->_db->quoteIdentifier('pinreq') . ' (`imageid`, `userid`) VALUES ';

				$imageid1=$this->_db->quote($imageid);

				foreach ($userids as $userid){

					$query.='('.$imageid1.','.$this->_db->quote($userid).'),';

					array_push($pinnedpeople, $userid);

				}
				$query=substr($query, 0,-1);
				$this->_db->query($query);
				$this->update(array('pinnedpeople'=>serialize($pinnedpeople)), array('imageid=?'=>$imageid));

			}

		}

	}
	public function unpin($imageid,$userid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image',array('imageid','albumid','userid','pinnedpeople'))->joinLeft('album', 'album.albumid=image.albumid','userid as ruserid')->where('imageid=?',$imageid);
			
			$result=$this->_db->fetchAssoc($sql);
			$result=$result[$imageid];
			
			if($result['ruserid']==$this->authIdentity->userid || $userid==$this->authIdentity->userid){
			
				$pinnedpeople=unserialize($result['pinnedpeople']);
				$pinnedpeople=array_diff($pinnedpeople, array($userid));
				$this->update(array('pinnedpeople'=>serialize($pinnedpeople)), array('imageid=?'=>$imageid));
			}
			
		}
	}
	public function pinmereq($imageid){

		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('user_info',array('userid','pinnedpic'))->where('userid=?',$this->authIdentity->userid);

			$result=$this->_db->fetchAssoc($sql);

			$pinnedpic=unserialize($result[$this->authIdentity->userid]['pinnedpic']);

			if(!in_array($imageid, $pinnedpic)){

				array_push($pinnedpic, $imageid);

				$this->_db->update('user_info',array('pinnedpic'=>serialize($pinnedpic)),array('userid=?'=>$this->authIdentity->userid));

				$data=array('imageid'=>$imageid,'userid'=>$this->authIdentity->userid);

				$this->_db->insert('pinme', $data);

			}

		}

	}

	public function reviewPinReq($imageid,$accept=true){

		if(isset($this->authIdentity)){

			if($accept=='true'){
				$sql=$this->_db->select()->from('user_info',array('userid','pinnedpic'))->where('userid=?',$this->authIdentity->userid);

				$result=$this->_db->fetchAssoc($sql);

				$result=$result[$this->authIdentity->userid];

				$pinnedpic=unserialize($result['pinnedpic']);

				array_push($pinnedpic, $imageid);

				$this->_db->update('user_info', array('pinnedpic'=>serialize($pinnedpic)),array('userid=?'=>$this->authIdentity->userid));

				$this->_db->delete('pinreq',array('imageid=?'=>$imageid,'userid=?'=>$this->authIdentity->userid));

			}

			else {

				$this->_db->update('pinreq', array('reviewed'=>'true'),array('imageid=?'=>$imageid,'userid=?'=>$this->authIdentity->userid));

			}

		}

	}

	

	

	public function reviewPinMeReq($imageid,$userid,$accept=true){

		if(isset($this->authIdentity)){

			if($accept){

				$sql=$this->_db->select()->from('image',array('imageid','userid','pinnedpeople'))->joinLeft('album', 'album.albumid=image.albumid',array('albumid','ruserid'=>'userid'))->where('imageid=?',$imageid);

				$result=$this->_db->fetchAssoc($sql);
				$result=$result[$imageid];

				$suserid=$this->authIdentity->userid;

				if ($suserid==$result['ruserid']){

					$pinnedpeople=unserialize($result['pinnedpeople']);

					array_push($pinnedpeople, $userid);

					$this->_db->update('image', array('pinnedpeople'=>serialize($pinnedpeople)),array('imageid=?'=>$imageid,'userid=?'=>$suserid));

					$this->_db->delete('pinme',array('imageid=?'=>$imageid,'userid=?'=>$userid));

				}

			}
			else {

				$this->_db->update('pinme', array('reviewed'=>'true'),array('imageid=?'=>$imageid,'userid=?'=>$userid));

			}

		}

		

	}

	

	public function getPinMeReq($imageid){
		if(isset($this->authIdentity->userid)){

			$sql=$this->_db->select()->from('pinme','userid')->joinLeft('freniz','pinme.userid=freniz.userid',array('username','url'))->joinLeft('image','pinme.imageid=image.imageid','')->joinLeft('album','album.albumid=image.albumid','userid as ruserid')->where('pinme.userid=? and reviewed=\'false\'',$this->authIdentity->userid)->where('pinme.imageid=?',$imageid);

			$result=$this->_db->fetchAssoc($sql);

			return $result;

		}

	}
	public function getPinMeReviews($from){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('pinme',array('imageid','userid'))->joinLeft('image', 'image.imageid=pinme.imageid',array('suserid'=>'userid','imageurl'=>'url','title','description','date'))
			->joinLeft('album', 'album.albumid=image.albumid','name as albumname')
			->joinLeft('freniz as sfreniz', 'image.userid=sfreniz.userid',array('susername'=>'username','surl'=>'url'))
			->joinLeft('image as simage', 'sfreniz.propic=simage.imageid','url as suserpic')
			->joinLeft('freniz', 'freniz.userid=pinme.userid',array('requser'=>'username','requser_url'=>'url'))
			->joinLeft('image as requserimage', 'freniz.propic=requserimage.imageid','url as requserimage')
			->where('album.userid=? and pinme.reviewed=\'false\'',$this->authIdentity->userid)->order('pinme.date desc')->limit($this->registry->limit,$from);
			$results=$this->_db->fetchAll($sql);
			$images=array();
			foreach($results as $row){
	
				if(empty($images[$row['imageid']]))
					$images[$row['imageid']]=array('url'=>$row['imageurl'],'suserid'=>$row['suserid'],'susername'=>$row['susername'],'surl'=>$row['surl'],'suserpic'=>$row['suserpic'],'title'=>$row['title'],'description'=>$row['description'],'date'=>$row['date'],'albumname'=>$row['albumname']);
				$images[$row['imageid']]['users'][$row['userid']]=array('username'=>$row['requser'],'url'=>$row['requser_url'],'propic'=>$row['requserimage']);
	
					
			}
			if(count($results)==$this->registry->limit)
				$final_results['loadmore']=true;
			else $final_results['loadmore']=false;
			$final_results['results']=$images;
			return $final_results;
		}
	}
	public function getPinReq($from){
	
		if(isset($this->authIdentity->userid)){
	
			$sql=$this->_db->select()->from('pinreq','')->joinLeft('image','pinreq.imageid=image.imageid')->joinLeft('album', 'album.albumid=image.albumid',array('name','ruserid'=>'userid'))->joinLeft('freniz','image.userid=freniz.userid',array('type','username','user_url'=>'url'))->joinLeft('image as simage','freniz.propic=simage.imageid',array('suserpic'=>'url'))
			->where('pinreq.userid=? and reviewed=\'false\'',$this->authIdentity->userid)
			->order('pinreq.date desc')->limit($this->registry->limit,$from);
	
			$results=$this->_db->fetchAssoc($sql);
	
			//return $results;
			if(count($results)==$this->registry->limit)
				$final_results['loadmore']=true;
			else $final_results['loadmore']=false;
			$final_results['results']['images']=$results;
			return $final_results;
	
		}
	
	}
	/*
	public function getPinReq(){

		if(isset($this->authIdentity->userid)){

			$sql=$this->_db->select()->from('pinreq','imageid')->joinLeft('image','pinreq.imageid=image.imageid',array('img_url'=>'url','suserid'=>'userid'))->joinLeft('freniz','image.userid=freniz.userid',array('susername'=>'username','suser_url'=>'url'))->joinLeft('album','album.albumid=image.albumid','userid as ruserid')->joinLeft('freniz as ruser', 'album.userid=ruser.userid',array('rusername'=>'username','ruser_url'=>'url'))->where('pinreq.userid=? and reviewed=\'false\'',$this->authIdentity->userid);

			$result=$this->_db->fetchAssoc($sql);

			return $result;

		}

	}
	*/
	
	public function uploadPropic($imageurl,$request){
		$sql=$this->_db->select()->from('album',array('ciu','csu','cpt'))->where('albumid=?',$this->authIdentity->propicalbum);
		$result1=$this->_db->fetchRow($sql);
		$this->cropImage('images/'.$imageurl, 'images/'.$imageurl, array(32,50,75,200,500),$request['x1'],$request['y1'],$request['width'],$request['height'],true);
		$insert_data=array('title'=>'title','description'=>'description','url'=>$imageurl,'albumid'=>$this->authIdentity->propicalbum,'userid'=>$this->authIdentity->userid,'date'=>new Zend_Db_Expr('Now()'),'pinnedpeople'=>'a:0:{}','vote'=>'a:0:{}','pt'=>'public','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','notifyusers'=>'a:0:{}','accepted'=>'yes','reqpinusers'=>'a:0:{}','pinmereq'=>'a:0:{}','comments'=>'a:0:{}','cpt'=>$result1['cpt'],'ciu'=>$result1['ciu'],'csu'=>$result1['csu']);
		$updtid=$this->insert($insert_data);
		$this->_db->update('freniz', array('propic'=>$updtid),array('userid=?'=>$this->authIdentity->userid));
		$this->_db->update('user_info', array('propic'=>$updtid),array('userid=?'=>$this->authIdentity->userid));
		$this->authIdentity->propic=$updtid;	
		$insert_act=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$updtid,'title'=>'changed propic','contenttype'=>'propic','contenturl'=>'propic.php','date'=>new Zend_Db_Expr('Now()'),'alternate_contentid'=>'propic');
		$this->_db->insert('activity', $insert_act);
	}
	
	
	public function getPinnedPics($userid,$from=null){
		$sql=$this->_db->select()->from('user_info',array('userid','pinnedpic'))->where('userid=?',$userid);
		$result=$this->_db->fetchRow($sql);
		$pinnedpics=unserialize($result['pinnedpic']);
		rsort($pinnedpics);
		//if(isset($from))
		//$pinnedpictures=array_slice($pinnedpics,$from,20);
		//else
			 return $pinnedpics;
		
	}
	public function adddescription($imageid,$text){
		if(isset($this->authIdentity)){
			
			$query='update image join album on (image.albumid=album.albumid) set description='.$this->_db->quote($text).'where album.userid='.$this->_db->quote($this->authIdentity->userid).' and image.imageid='.$this->_db->quote($imageid);
			$this->_db->query($query);
			
		}
	}
	public function voteimage($imageid){
	if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image',array('imageid','albumid','notifyusers','pinnedpeople','dontnotify','userid','vote'))->joinLeft('album', 'album.albumid=image.albumid','userid as ruserid')->joinLeft('freniz', 'freniz.userid=album.userid','username as rusername')->where('imageid=?',$imageid);
			$result=$this->_db->fetchRow($sql);
			if(!empty($result)){
			$vote=unserialize($result['vote']);
			if(!in_array($this->authIdentity->userid, $vote)){
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "imageid='$imageid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$imageid,'title'=>'voted on','contenttype'=>'image','contenturl'=>'image.php?imageid='.$imageid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'image_'.$imageid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			
			
			$notifyusers=unserialize($result['notifyusers']);
			$pinnedpeople=unserialize($result['pinnedpeople']);
			if(!empty($notifyusers))
			{
				$sql=$this->_db->select()->from('freniz','userid')->joinLeft('pinreq', 'pinreq.userid=freniz.userid and pinreq.imageid='.$this->_db->quote($imageid).' and pinme.userid in ('.$this->_db->quote($pinnedpeople).')','')
				->where('pinreq.userid is NULL and freniz.userid in (?)',$pinnedpeople);
				$result1=$this->_db->fetchAssoc($sql);
				if(!empty($notifyusers))
				$notifyusers=array_merge($notifyusers,array_keys($result1));
			}
			
			$vote1=array_diff($vote,array($this->authIdentity->userid));
				
			$dontnotify=unserialize($result['dontnotify']);
			$notifyusers=array_unique(array_diff(array_merge($notifyusers,array($result['userid'],$result['ruserid']),$vote1),$dontnotify,array($this->authIdentity->userid)));
			if(!empty($notifyusers)){
				$query='insert into notifications(userid,contenturl,notification,userpic) values ';
				$userpic=$this->authIdentity->propic;
				foreach($notifyusers as $user){
					if(sizeof($vote1)>1)
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($vote1)-1)." other voted on";
					}
					else
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a>  voted on";
					}
					if($user==$result['ruserid'] )
					{
						$notificationtext.=" your image";
					}
					else
					{
						$notificationtext.=" <a href='".$result['userid']."'>".$result['username']."</a>'s image";
					}
					$query.=' ('.$this->_db->quote(array($user,'image/'.$imageid,$notificationtext,$userpic)).'),';
				}
				$query=substr($query, 0,-1);
				$query.=' on duplicate key update notification='.$this->_db->quote($notificationtext).', read1=0, time=now()';
				$this->_db->query($query);
			}
			
			}
			}
		}
	}
	public function unVoteimage($imageid)
	{
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image',array('imageid','albumid','userid','vote'))->joinLeft('album', 'album.albumid=image.albumid','userid as ruserid')->where('imageid=?',$imageid);
			$result=$this->_db->fetchRow($sql);
				$vote=unserialize($result['vote']);
				if(in_array($this->authIdentity->userid, $vote)){
					$vote=array_diff($vote, array($this->authIdentity->userid));
					$updatedata=array('vote'=>serialize($vote));
					$this->update($updatedata, array('imageid=?'=>$imageid));
				}
		}
	}
	public function deleteimageComment($commentid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image_comments')->joinLeft($this->_name, 'image_comments.imageid=image.imageid','userid as image_userid')->where('commentid=?',$commentid);
			$result=$this->_db->fetchRow($sql);
			$userid=$this->authIdentity->userid;
			if($result['userid']==$userid || $result['image_userid']==$userid){
				$this->_db->delete('image_comments',"commentid='$commentid'");
			}
		}
	}
	
	
	public function getbanners(){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('pages','banners')->where('pageid=?',$this->authIdentity->userid);
			$result=$this->_db->fetchRow($sql);
			$albumid=$result['banners'];
			   $wallpic=$this->getImages($albumid, '0','album');
			return $wallpic;	
		}
	}
}
