<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        $resolutions=array(32,50,75,200,500,0);
        $c=new Image_Compressimage($path,$resolutions);
        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {  
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if( move_uploaded_file($_FILES['qqfile']['tmp_name'], $path))
        {
            $resolutions=array(32,50,75,200,500,0);
        $c=new Image_compressimage($path,$resolutions);
        return true;
        }
 else {
    return false;
}
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class Image_Upload {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        
        $this->checkServerSettings();       

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
        }        
    }
    
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }
    
    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory,$replaceOldFile = FALSE){
    	if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        //$filename = $pathinfo['filename'];
        $filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
           /* $target = fopen("log.txt", "w");        
            fwrite($target, "hiiiiiiiiiii\n".$_REQUEST['album']."\n".$_SESSION['userid']."\n");
            $a=serialize(array());
            mysql_query("insert into image (title,description,url,albumid,userid,date,pinnedpeople,vote,pt,specificlist,hiddenlist,notifyusers,accepted,reqpinusers,pinmereq,comments) values('title','description','".$filename.'.'.$ext."','".$_REQUEST['album']."','".$_SESSION['userid']."',now(),'".$a."','".$a."','".$pt."','".$specific."','".$hidden."','a:0:{}','".$accepted."','a:0:{}','a:0:{}','a:0:{}')");
            $updtdid=mysql_insert_id();
            if($accepted=='yes'){
            
                $result=mysql_query("select count(imageid) count from image where albumid='".$_REQUEST['album']."' and date > date_sub(now(), interval 3 day)");
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
                }
                
            
            }
            else
            {
               $result3=mysql_query("select reviews from user_info where userid='".$ruser."'");
               while($row3=  mysql_fetch_assoc($result3)){
                   $reviews=unserialize($row3['reviews']);
                   if(isset($reviews['image']))
                   {
                       array_push($reviews['image'], $updtdid);
                   }
                   else
                       {
                       $reviews['image']=array($updtdid);
                   }
                   mysql_query("update user_info set reviews='".serialize($reviews)."' where userid='".$ruser."'");
                   if(isset($_SESSION['reqfrmme']['image']))
                        array_push($_SESSION['reqfrmme']['image'], $updtdid);
                    else
                        $_SESSION['reqfrmme']['image']=array($updtdid);
                    mysql_query("update user_info set reqfrmme='".serialize($_SESSION['reqfrmme'])."' where userid='".$_SESSION['userid']."'");
                                        
               }
            }
            fwrite($target, "\nmysql:".  mysql_error());
            $results=array('success'=>"true","fileid"=>$updtdid,"imgurl"=>($filename.".".$ext));
            fwrite($target, "\n".serialize($results));
            fclose($target);*/
            return array('success'=>"true","imgurl"=>($filename.".".$ext));
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }    
    function handleUpload1($uploadDirectory,$replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        //$filename = $pathinfo['filename'];
        $filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            return array('success'=>"true","fileid"=>0,"imgurl"=>($filename.".".$ext));
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
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
