<?php
define('MEMORY_TO_ALLOCATE',	'100M');

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of compressimage
 *
 * @author jameema
 */
class Image_Compressimage {
    function __construct($path,$resolutions) {
       $this->filename= basename($path);
       if(isset($this->filename)){
           foreach($resolutions as $res)
            $this->res[$res]=$this->compress($path, $res);
            
       }
    }
    //put your code here
    function compress($path,$res)
    {
    	$size=getimagesize($path);
        $width=$size[0];
        $height=$size[1];
        $quality=75;
        if($res!=0 )
        {
            if(($res<=$width || $res<=$height)){
        $xRatio		= $res / $width;
        $yRatio		= $res / $height;
        if ($xRatio * $height < $res)
        { // Resize the image based on width
                $tnHeight	= ceil($xRatio * $height);
                $tnWidth	= $res;
        }
        else // Resize the image based on height
        {
                $tnWidth	= ceil($yRatio * $width);
                $tnHeight	= $res;
        }
        
        }
        else
        {
            $tnHeight=$height;
            $tnWidth=$width;
        }
        if(!file_exists(dirname($path).'/'.$res))
        	mkdir (dirname($path).'/'.$res);
        	$newfile=dirname($path).'/'.$res.'/'.$res.'_'.  basename($path);
        }
        else
        {
            if(max(array($width,$height))>1000)
            {
                $ratio=$width/$height;
                if($ratio>1)
                {
                    $tnWidth=960;
                    $tnHeight=round($tnWidth/$ratio);
                }
                else
                {
                    $tnHeight=960;
                    $tnWidth=round($tnHeight*$ratio);
                }
            }
            else
            {
                $tnHeight=$height;
                $tnWidth=$width;
            }
            if(!file_exists(dirname($path).'/original'))
                mkdir (dirname($path).'/original');
            copy($path,dirname($path).'/original/'.basename($path));
            $filesize=round(filesize($path)/1024);
            if($filesize>500)
                $quality=60;
            $newfile=$path;
        }
        ini_set('memory_limit', MEMORY_TO_ALLOCATE);
        $dst	= imagecreatetruecolor($tnWidth, $tnHeight);
        switch($size['mime'])
        {
            case 'image/gif':
               $src=imagecreatefromgif($path);
               $this->setalpha($dst);
               $quality=round(10 - ($quality / 10));
               $sharpen=false;
               $createfile='imagepng';
               break;
            case 'image/x-png':
            case 'image/png':
               $src=imagecreatefrompng($path);
               $this->setalpha($dst);
               $quality=round(10 - ($quality / 10));
               $sharpen=false;
               $createfile='imagepng';
               break;
           default:
               $src=imagecreatefromjpeg($path);
               $sharpen=true;
               $createfile='imagejpeg';
            break;
           
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $tnWidth, $tnHeight, $width, $height);
        if($sharpen)
        {
            $sharpness	= $this->findSharp($width, $tnWidth);
            $sharpenMatrix	= array(
                    array(-1, -2, -1),
                    array(-2, $sharpness + 12, -2),
                    array(-1, -2, -1)
            );
            $divisor		= $sharpness;
            $offset			= 0;
            imageconvolution($dst, $sharpenMatrix, $divisor, $offset);
        }
        if(!file_exists(dirname($newfile)))
            mkdir (dirname ($newfile));
        $tmp=$createfile($dst,$newfile,$quality);
        
        imagedestroy($src);
        imagedestroy($dst);
        return $tmp;
    }
    function findSharp($orig, $final) // function from Ryan Rud (http://adryrun.com)
    {
        $final	= $final * (750.0 / $orig);
        $a		= 52;
        $b		= -0.27810650887573124;
        $c		= .00047337278106508946;

        $result = $a + $b * $final + $c * $final * $final;
        return max(round($result), 0);
    }
    function setalpha($dst)
    {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    
}

?>
