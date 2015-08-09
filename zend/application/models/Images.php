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
	protected $authIdentity;
	public function __construct($db) {
		$this->_db = $db;
		$auth=Zend_Auth::getInstance();
		if($auth->hasIdentity()){
			$this->authIdentity=$auth->getIdentity();
		}
	}
	public function getImages($albumid){
		if(isset($this->authIdentity)){
			$sql=$this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false)->joinLeft('album', 'album.albumid=image.albumid',array('name','ruserid'=>'userid'))->joinLeft('freniz','image.userid=freniz.userid',array('type','username','user_url'=>'url'))->joinLeft('user_info', 'user_info.userid=freniz.userid','propic')->joinLeft('image as simage','user_info.propic=simage.imageid',array('surl'=>'url'))
				->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist')		
				->joinLeft('pages','freniz.userid=pages.pageid','pagepic')->joinLeft('image as pimage','pages.pagepic=pimage.imageid',array('purl'=>'url'))->where('image.albumid=?',$albumid);
			$result=$this->_db->fetchAssoc($sql);
			$myfrnds=$this->authIdentity->friends;
			$myid=$this->authIdentity->userid;
			foreach($result as $imageid=>$values)
			{
				$privacy=$values['pt'];
				$specific=unserialize($values['specificlist']);
				$hiddenlist=unserialize($values['hiddenlist']);
				$rusrfrnds=unserialize($values['friendlist']);
				if((($privacy=='public'||($privacy=='friends' && in_array($values['ruserid'],$myfrnds))||($privacy=='fof' && count(array_intersect($rusrfrnds,$myfrnds)>=1) )||($privacy=='specific' && in_array($myid, $specific)))&& !in_array($values['ruserid'],  $this->authIdentity->blocklistmerged) && !in_array($myid, $hiddenlist))|| $myid==$values['ruserid'] || $myid==$values['userid']){
					
				}
				else
					unset ($result[$imageid]);
			}
			return $result;
		}
		else
			return array("status","please give the valid information");
	}
	
	public function getComments($imageids,$from=null,$limit=null)
	{
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from('image_comments')
				->joinLeft('freniz', 'freniz.userid=image_comments.userid',array('type','username','url'))
				->joinLeft('user_info','user_info.userid=freniz.userid','propic')
				->joinLeft('image as userimage', 'userimage.imageid=user_info.propic',array('user_imgurl'=>'url'))
				->joinLeft('pages', 'pages.pageid=freniz.userid','pagepic')
				->joinLeft('image as pageimage','pageimage.imageid=pages.pagepic','url as page_imgurl')
				->where('image_comments.imageid in(?)',$imageids);
			if(!is_array($imageids))
				$sql->limit($limit,$from);
			$result=$this->_db->fetchAssoc($sql);
			return $result;
		}
		else
			return array("status","please give the valid information");
	}
	public function getArrayOfImages($imageids){
		if(isset($this->authIdentity) && isset($imageids)){
			$sql=$this->_db->select()->from('image')
			->joinLeft('album', 'image.albumid=album.albumid',array('name','ruserid'=>'userid'))
			->joinLeft('freniz as rfreniz','rfreniz.userid=album.userid',array('rusername'=>'username','ruser_url'=>'url'))
			->joinLeft('friends_vote','album.userid=friends_vote.userid','friendlist')
			->joinLeft('freniz', 'freniz.userid=image.userid',array('type','username','user_url'=>'url'))
			->joinLeft('user_info','user_info.userid=freniz.userid','propic')
			->joinLeft('image as userimage', 'userimage.imageid=user_info.propic',array('user_imgurl'=>'url'))
			->joinLeft('pages', 'pages.pageid=freniz.userid','pagepic')
			->joinLeft('image as pageimage','pageimage.imageid=pages.pagepic','url as page_imgurl')
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
				if((($privacy=='public'||($privacy=='friends' && in_array($values['ruserid'],$myfrnds))||($privacy=='fof' && count(array_intersect($rusrfrnds,$myfrnds)>=1) )||($privacy=='specific' && in_array($myid, $specific)))&& !in_array($values['ruserid'],  $this->authIdentity->blocklistmerged) && !in_array($myid, $hiddenlist))|| $myid==$values['ruserid'] || $myid==$values['userid']){
					$result[$imageid]['comments']=$this->getComments($imageid);
				}
				else{
					unset ($result[$imageid]);
					return $imageid;
				}
			}
			return $result;
			
		}
		else
			return array("status","please give the valid information");
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
	    else
	    	return array("status","please give the valid information");
		       
	}
	public function denyImages($imageids){
		if(isset($this->authIdentity) && isset($imageids)){
			$sql=$this->_db->select()->from('image',array('imageid','url','albumid'))->joinLeft('album','album.albumid=image.albumid',array('ruserid'=>'userid'))->where("imageid in (?) and accepted='no' and album.userid='{$this->authIdentity->userid}'",$imageids);
			$result=$this->_db->fetchAssoc($sql);
			$this->deleteImageSources($result);
			$this->delete(array('imageid in (?)'=>array_keys($result)));
			
		}
		else
			return array("status","please give the valid information");
	}
	public function deleteImages($imageid){
		if(isset($this->authIdentity)){
			$userid=$this->authIdentity->userid;
			$sql=$this->_db->select()->from('image',array('imageid','imageurl','userid'))->joinLeft('album', 'album.albumid=image.albumid',array('ruserid'=>'userid'))->where("(image.userid='$userid' or album.userid='$userid') and imageid!='{$this->authIdentity->propic }' and imageid in (?)",$imageid);
			$result=$this->_db->fetchAssoc($sql);
			$this->deleteImageSources($result);
			$this->delete(array('imageid in (?)'=>array_keys($result)));
			if(is_array($imageid) && in_array($this->authIdentity->propic, $imageid) && $this->authIdentity->userid!=1){
				if($this->authIdentity->type=='user'){
					$this->_db->update('user_info', array('propic'=>1),array('userid=?'=>$this->authIdentity->userid));
					$this->authIdentity->propic=1;
					$result=$this->find($this->authIdentity->propic);
					$images[$this->authIdentity->propic]=$result[0];
					$this->deleteImageSources($images);
					$this->delete(array('imageid=?'=>$this->authIdentity->propic));
				}
				elseif($this->authIdentity->type=='page'){
					$this->_db->update('pages', array('pagepic'=>2),array('userid=?'=>$this->authIdentity->userid)); 
					$images[$this->authIdentity->propic]=$result[0];
					$this->deleteImageSources($this->authIdentity->propic);
					$this->delete(array('imageid=?'=>$this->authIdentity->propic));
				}
			}
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
	public function uploadImage($album){
		if(isset($this->authIdentity) && isset($album)){
			$allowedExtensions = array();
			// max file size in bytes
			$sizeLimit = 2 * 1024 * 1024;
			$uploader=new Image_Upload($allowedExtensions,$sizeLimit);

			
			
			$sql=$this->_db->select()->from('album',array('userid','canupload','ignorelist','pt','specificlist','hiddenlist'))->joinLeft('freniz', 'album.userid=freniz.userid','type')
			->joinLeft('privacy', "freniz.userid=privacy.userid and freniz.type='user'",array('advancedprivacyimage','autoacceptusers','blockactivityusers'))->joinLeft('pages',"freniz.userid=pages.pageid and freniz.type='page'",array('bannedusers','admins','vote'))->where('albumid=?',$album);
			$result=$this->_db->fetchRow($sql);
			//return $result;
			$myid=$this->authIdentity->userid;$isvalid=false;
			$final_data=array('title'=>'title','description'=>'description','albumid'=>$album,'userid'=>$myid,'date'=>new Zend_Db_Expr('now()'),'pinnedpeople'=>'a:0:{}','vote'=>'a:0:{}','specificlist'=>'a:0:{}','hiddenlist'=>'a:0:{}','notifyusers'=>'a:0:{}','reqpinusers'=>'a:0:{}','pinmereq'=>'a:0:{}','comments'=>'a;0:{}');
			if($myid!=$result['userid']){
				if($this->authIdentity->type=='user' && $result['type']=='user'){
					if(($result['canupload']=='friends' && !in_array($result['userid'], $this->authIdentity->blocklistmerged) && !in_array($this->authIdentity->userid, unserialize($result['ignorelist'])) && in_array($result['userid'], $this->authIdentity->friends)) ){
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
							}
							else{
								$isvalid=true;
								$final_data['accepted']='yes';
								$final_data['pt']=$result['pt'];
								$final_data['specificlist']=$result['specificlist'];
								$final_data['hiddenlist']=$result['hiddenlist'];
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
						
					}
					else if(!in_array($result['userid'], unserialize($result['bannedusers'])) && in_array($myid,unserialize($result['vote']))){
						$isvalid=true;
						$final_data['accepted']='yes';
						$final_data['pt']=$result['pt'];
						$final_data['specificlist']=$result['specificlist'];
						$final_data['hiddenlist']=$result['hiddenlist'];
						
					}
				}
			}
			else {
				$isvalid=true;
				$final_data['accepted']='yes';
				$final_data['pt']=$result['pt'];
				$final_data['specificlist']=$result['specificlist'];
				$final_data['hiddenlist']=$result['hiddenlist'];
			}
			if($isvalid){
				//$final_data['upload']=$uploader->handleUpload('/var/www/freniz_zend/public/images/');
				$upload_result=$uploader->handleUpload('/var/www/freniz_zend/public/images/');
				if(!isset($upload_result['error'])){
					try{
					$final_data['url']=$upload_result['imgurl'];
					$updtdid=$this->insert($final_data);
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
		/*mysql_connect("localhost", "nizam", "ajith786") or fwrite($target,"coudnt connect to the database");
		 mysql_select_db("fztest1") or fwrite($target,"coudnt find database");
		if(!isset($_REQUEST['type']) || $_REQUEST['type']=='image'){
		$result1=mysql_query("select userid,canupload,ignorelist,pt,specificlist,hiddenlist from album where albumid='".$_REQUEST['album']."'");
			while($row=  mysql_fetch_assoc($result1))
			{
				if($_SESSION['userid']!=$row['userid']){
					if(($row['canupload']=='friends' && !in_array($row['userid'], $_SESSION['blocklist']) && !in_array($row['userid'], $_SESSION['blockedby']) && !in_array($_SESSION['userid'], unserialize($row['ignorelist'])) && in_array($row['userid'], $_SESSION['friends'])) || ($row['userid']==$_SESSION['userid']))
					{
					$result2=mysql_query("select advancedprivacyimage,autoacceptusers,blockactivityusers from privacy where userid='".$row['userid']."'");
						while($row2=  mysql_fetch_assoc($result2)){
							if($row2['advancedprivacyimage']=='on' && !in_array($_SESSION['userid'], unserialize($row2['blockactivityusers']))){
								if(in_array($_SESSION['userid'], unserialize($row2['autoacceptusers']))){
								$allowedExtensions = array();
								// max file size in bytes
								$sizeLimit = 10 * 1024 * 1024;
							
								$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
								$result = $uploader->handleUpload('../images/',$row['pt'],$row['specificlist'],$row['hiddenlist'],$row['userid'],'yes');
								// to pass data through iframe you will need to encode all html tags
								echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
								break;
							
								}
								else
								{
								$allowedExtensions = array();
								// max file size in bytes
								$sizeLimit = 10 * 1024 * 1024;
							
								$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
								$result = $uploader->handleUpload('../images/',$row['pt'],$row['specificlist'],$row['hiddenlist'],$row['userid'],'not');
								// to pass data through iframe you will need to encode all html tags
								echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
								break;
								}
							}
							else if(!in_array($_SESSION['userid'], unserialize($row2['blockactivityusers']))){
						
							// list of valid extensions, ex. array("jpeg", "xml", "bmp")
							$allowedExtensions = array();
							// max file size in bytes
							$sizeLimit = 10 * 1024 * 1024;
						
							$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
							$result = $uploader->handleUpload('../images/',$row['pt'],$row['specificlist'],$row['hiddenlist'],$row['userid'],'yes');
							// to pass data through iframe you will need to encode all html tags
							echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
							break;
							}
						}
	
					}
				}
				else{
				$allowedExtensions = array();
				// max file size in bytes
				$sizeLimit = 10 * 1024 * 1024;
			
				$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
				$result = $uploader->handleUpload('../images/',$row['pt'],$row['specificlist'],$row['hiddenlist'],$row['userid'],'yes');
				// to pass data through iframe you will need to encode all html tags
				echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
				break;
			
				}
			}
		}
		else if($_REQUEST['type']=='blog')
		{
		$result=$uploader->handleUpload1($uploadDirectory);
		}
		mysql_close();*/
	}
	
	
	
	
	
	
	public function doComment($imageid,$text){
		if(isset($this->authIdentity)){
			$a=array();
			$select=$this->_db->select()->from($this->_name,array('suserid'=>'userid','notifyusers'))->joinLeft('album', 'image.albumid=album.albumid',array('ruserid'=>'userid'))
				->joinLeft('freniz','freniz.userid=album.userid',array('type as rtype','username as rusername'))
			->joinLeft('pages','pages.pageid=album.userid',array('admins','canpost','vote','bannedusers'))
			->joinLeft('privacy','privacy.userid=album.userid',array('post','postignore'))->where('imageid=?',$imageid);
			$result=$this->_db->fetchRow($select);
				
			if(isset($result)){
				$commentdata=array('imageid'=>$imageid,'userid'=>$this->authIdentity->userid,'comment'=>mysql_real_escape_string(trim($text)),'vote'=>'a:0:{}','date'=>new Zend_Db_Expr('NOW()'));
				if($this->authIdentity->type=='user' && $result['rtype']=='user'){
					$ignorelist=unserialize($result['postignore']);
					if(($result['post']=='friends' && !in_array($result['ruserid'], $this->authIdentity->blocklistmerged) && in_array($result['ruserid'], $this->authIdentity->friends) && !in_array($this->authIdentity->userid, $ignorelist)) || $this->authIdentity->userid==$result['ruserid'] || $this->authIdentity->userid==$result['susersid']){
						$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$result['ruserid'],'contentid'=>$imageid,'title'=>'commented on','contenttype'=>'image','contenturl'=>'image.php?imageid='.$imageid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'image_'.$imageid);
						$this->_db->insert('image_comments', $commentdata);
						$activityModel=new Application_Model_Activity($this->_db);
						$activityModel->insert($activity);
						 
						 
						$notifyusers=unserialize($result['notifyusers']);
						$notifyusers=array_diff($notifyusers, array($this->authIdentity->userid));
						$notifyusers1=$notifyusers;
						array_push($notifyusers1, $result['ruserid']);
						array_push($notifyusers1, $result['suserid']);
						$notifyusers1=array_diff($notifyusers1, array($this->authIdentity->userid));
						$notifyusers1=array_unique($notifyusers1);
						if(!empty($notifyusers1)){
							$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
							$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
							$update_notification=array();
							foreach($result_notifyusers as $user => $notifications)
							{
								$notifications=unserialize($notifications['notifications']);
								if(sizeof($notifyusers)>1)
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($notifyusers)-1)." other commented on";
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
								$notifications["image.php?imageid=".$imageid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
								$update_notification[$user]=$notifications;
								//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
							}
							$notification_case=' case userid ';
							foreach($update_notification as $user=>$notification){
								$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
							}
							$notification_case.=' end';
							$where_clause=array(' userid in (?)'=>array_keys($update_notification));
							$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),$where_clause);
							array_push($notifyusers,$this->authIdentity->userid);
						}
						$update_staturedata=array('notifyusers'=>serialize($notifyusers));
						$this->update($update_staturedata, "imageid='$imageid'");
	
					}
				}
				else if($result['type']=='page'){
					$votes=unserialize($result['votes']);
					$bannedusers=unserialize($result['bannedusers']);
					$admins=unserialize($result['admins']);
					if((($result['canpost']=='public' ||($result['canpost']=='votedusers' && in_array($this->authIdentity->userid, $votes)))&& !in_array($this->authIdentity->userid, $bannedusers)||  in_array($this->authIdentity->userid, $admins) || $this->authIdentiy->userid==$result['ruserid']   )){
						$this->_db->insert('comment', $commentdata);
							
						$notifyusers=unserialize($result['notifyusers']);
						$notifyusers=array_diff($notifyusers, array($_SESSION['userid']));
						$notifyusers1=$notifyusers;
						$notifyusers1=array_merge($notifyusers, $result['admins'],array($result['ruserid'],$result['suserid']));
						$notifyusers1=array_diff($notifyusers1, array($_SESSION['userid']));
						$notifyusers1=array_unique($notifyusers1);
						if(!empty($notifyusers1)){
							$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
							$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
							$update_notification=array();
							foreach($result_notifyusers as $user => $notifications)
							{
								$notifications=unserialize($notifications['notifications']);
								if(sizeof($notifyusers)>1)
								{
									$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($notifyusers)-1)." other commented on";
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
								$notifications["image.php?imageid=".$imageid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
								$update_notification[$user]=$notifications;
								//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
							}
							$notification_case=' case userid ';
							foreach($update_notification as $user=>$notification){
								$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
							}
							$notification_case.=' end';
	
							$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));
	
							array_push($notifyusers,$_SESSION['userid']);
						}
						$update_data=array('commentcount'=>new Zend_Db_Expr('commentcount+1'),'notifyusers'=>serialize($notifyusers));
						$this->update($update_data, "imageid='$imageid'");
						//mysql_query("update status set commentcount='."',notifyusers='".  serialize($notifyusers)."' where statusid='".$postid."'");
							
					}
	
				}
					
			}
	
		}
	}
	
	
	public function vote($imageid){
		if(isset($this->authIdentity)){
			$sql=$this->_db->select()->from($this->_name,array('vote','notifyusers','albumid','userid as suserid'))
										->joinLeft('album','album.albumid=image.albumid','userid as ruserid')
										->joinLeft('user_info', 'user_info.userid=album.userid',array('fname','lname'))
										->where("imageid='$imageid'");
				$result=$this->_db->fetchRow($sql);
				$rusername=$result['fname'].$result['lname'];
				$vote=unserialize($result['vote']);
	
			array_push($vote, $this->authIdentity->userid);
			$vote=array_unique($vote);
			$update_data=array('vote'=>serialize($vote));
			$this->update($update_data, "imageid='$imageid'");
			$activity=array('userid'=>$this->authIdentity->userid,'ruserid'=>$this->authIdentity->userid,'contentid'=>$imageid,'title'=>'voted on','contenttype'=>'image','contenturl'=>'image.php?imageid='.$imageid,'date'=>new Zend_Db_Expr('now()'),'alternate_contentid'=>'image_'.$imageid);
			$activityModel=new Application_Model_Activity($this->_db);
			$activityModel->insert($activity);
			$notifyusers=unserialize($result['notifyusers']);
			$notifyusers=array_merge($notifyusers, array($result['suserid'],$result['ruserid']),$vote);
			$notifyusers1=$notifyusers;
			//array_push($notifyusers1, $result['userid']);
			$notifyusers1=array_diff($notifyusers1, array($this->authIdentity->userid));
			$notifyusers1=array_unique($notifyusers1);
			$votes1=array_diff($vote, array($this->authIdentity->userid));
			if(!empty($notifyusers1)){
				$select_notifyusers=$this->_db->select()->from('notification')->where('userid in(?)',$notifyusers1);
				$result_notifyusers=$this->_db->fetchAssoc($select_notifyusers);
				$update_notification=array();
				foreach($result_notifyusers as $user => $notifications)
				{
					$notifications=unserialize($notifications['notifications']);
					if(sizeof($vote)>1)
					{
						$notificationtext="<a href='".$this->authIdentity->userid."'>".$this->authIdentity->username."</a> and ".(sizeof($votes1)-1)." other voted on";
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
						$notificationtext.=" <a href='".$result['ruserid']."'>".$rusername."</a>'s image";
					}
					$notifications["image.php?imageid=".$imageid]=array("notification"=>  htmlspecialchars($notificationtext,ENT_QUOTES),"read"=>"0","time"=>  time());
					$update_notification[$user]=$notifications;
					//mysql_query("update notification set notifications='".mysql_real_escape_string(serialize($notifications))."' where userid='".$user."'");
				}
				$notification_case=' case userid ';
				foreach($update_notification as $user=>$notification){
					$notification_case.=" when '$user' then '".mysql_real_escape_string(serialize($notification))."'";
				}
				$notification_case.=' end';
					
				$this->_db->update('notification',array('notifications'=>new Zend_Db_Expr($notification_case)),array(' userid in (?)'=>array_keys($update_notification)));
			}
		}
	}
	public function unVote($imageid)
	{
		if(isset($this->authIdentity)){
			$result=$this->find($imageid);
			if($result){
				$result=$result[0];
	
				$vote=unserialize($result['vote']);
				if(in_array($this->authIdentity->userid, $vote)){
					$vote=array_diff($vote, array($this->authIdentity->userid));
					$updatedata=array('vote'=>serialize($vote));
					$this->update($updatedata, array('imageid=?'=>$imageid));
				}
			}
		}
	}
	
	
}
