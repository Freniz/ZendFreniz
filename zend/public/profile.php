<?php $ud=$this->bodyContent; $session=(array)$this->mydetails;
$personalinfo=$ud['personalinfo'];
$wallpic=$ud['secondarypic1_id'];
if(empty($wallpic) || $ud['secondarypic1_url']=='wallpaper_user.png')
	$wallpic='no';
else
$wallpic=explode(',',$wallpic);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- hello -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
<meta charset="UTF-8">
<title><?php echo $ud['username']; ?></title>
<link rel="shortcut icon" href="http://www.freniz.com/favicon.ico" /> 
<link rel="stylesheet" href="http://localhost/freniz_zend/public/css/default.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="http://localhost/freniz_zend/public/js/jquery.js"></script> 
<script src="http://localhost/freniz_zend/public/js/ajax.js" type="text/javascript"></script>
<script type="text/javascript" src="http://localhost/freniz_zend/public/js/jquery.textarea-expander.js"></script>
<script type="text/javascript" src="http://localhost/freniz_zend/public/js/bsn.AutoSuggest_c_2.0.js"></script>
<link rel="stylesheet" href="http://localhost/freniz_zend/public/css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
	       <link rel="stylesheet" href="http://localhost/freniz_zend/public/css/forum.css" />
      <script src="http://localhost/freniz_zend/public/js/jquery.timeago.js" type="text/javascript"></script>
	          <script type="text/javascript" src="http://localhost/freniz_zend/public/js/leaf.js"></script> 
	<?php if($ud['userid']==$session['userid']){?>
	<script type="text/javascript">
$(function() {
	$('.span-change').click(function(){
		 $("#container").css('cursor','move');

		    $("#draggable").draggable({axis:"y",stop:function(event,ui){ 
	          var top=$(this).css('top').slice(0,-2); if(top>=0){ $(this).css({'top':'0px'});}
	          var topimg=$("#draggable").css('top');
	          secpicposition('<?php echo $wallpic[0]?>',topimg);
	          $("#container").css('cursor','pointer');
	          }
	    });
		});
	
	$("#container").mouseover(function(){
		$('.span-change').css('display','block');
	});
	$("#container").mouseout(function(){
		 $('.span-change').css('display','none');
	});
		
  
});
</script> 
	<?php }?>     
<script type="text/javascript" src="http://localhost/freniz_zend/public/js/jquery.ui.js"></script>

<script type="text/javascript">
   
        
        // in your app create uploader as soon as the DOM is ready
        // don't wait for the window to load  
 window.onload=function(){
    document.getElementById("loading").style.display='none';
}
 
$(document).ready(function(event){
	$('#edit-pro').live('click',function(){
		var a='<div class="option"style="margin-left:35px; margin-top:-60px"><ul><li id="edit-propic" onclick="location.href=\'http://www.freniz.com/getimages?albumid=<?php echo $session['propicalbum']; ?>\';"><a>change photo</a></li><li class="moody" id="mood-set" ><a>change mood</a></li><li id="mood-edit" ><a>edit mood</a></li></ul></div>';
		$('#edit-prof').html(a);
		$(".option").mouseover(function() {
		    $('.option').css('display','block');
		  }).mouseout(function(){
			  $('.option').css('display','none');
		  });
		});

	$('.mes-text').live('mouseover', function() {
	    $('.mes-text').toggle(function() {
	    	$('#mes-text').css('display','block');
	    	$('.mes-text').html('cancel');
	    },function(){
	    	$('#mes-text').css('display','none');
	    	$('.mes-text').html('send text');
	    });
	});
	
	
	$('.hover').live('mouseover',function(){
		var data=$(this).attr('data');
		miniprofile(data,this);
		$(".hover-div").live('mouseover',function() {
		    $('.hover-div').css('display','block');
		  }).live('mouseout',function(){
			  $('.hover-div').css('display','none');
		  });
		}).live('mouseout',function(){
			 $('.hover-div').css('display','none');
		  });
			
		$('.workeducation').click(function(){
   	   
		$('#workandeducations').css('display','block');
		$('.workeducation a').css('background-color','#fff');
		$('.personal a').css('background-color','#eee');
		$('.basic a').css('background-color','#eee');
		$('#basic-info-details').css('display','none');
		$('#personal').css('display','none');
		
	});
	$('.basic').click(function(){
		$('.basic a').css('background-color','#fff');
		$('.personal a').css('background-color','#eee');
		$('.workeducation a').css('background-color','#eee');	
	$('#basic-info-details').css('display','block');
	$('#workandeducations').css('display','none');
	$('#personal').css('display','none');
	});
	$('.personal').click(function(){
		$('.personal a').css('background-color','#fff');
		$('.workeducation a').css('background-color','#eee');
		$('.basic a').css('background-color','#eee');
		$('#personal').css('display','block');
		$('#basic-info-details').css('display','none');
		$('#workandeducations').css('display','none');
		});

	
	$('#remov-frds').click(function(){
	   $rem='<div style="width:500px;padding:10px; float:left;"><span style="float:left; font-weight:bold; font-size:24px;">Do you realy want to unfriend?</span><input class="greenbutton"style="float:left; margin-left:5px; margin-top:5px"type="button"onclick="removefriends(\'<?php echo $ud['userid']; ?>\')"value="Yes"/><input class="greenbutton"style="float:left; margin-left:5px;margin-top:5px"type="button"onclick="removelement()"value="No"/></div>';
	       
	     $('#light1').html($rem); 
	           $('#light1').css({'display':'block'});
	        $('#fade').css({'display':'block'});
	             
	         });
	$('#message-button').click(function(){
	    
	    $a='<div style="width:500px; height:200px; margin-left:20px; margin-top:20px; "><div style="width:30px; height:30px; margin-top:5px; margin-left:5px; float:left; ">To:</div><form name="sendmessage" onsubmit="sendmsguser()"><div style="width:400px; height:30px; margin-top:6px; margin-left:5px; float:left; "><input size="40" type="text" disabled="disable" name="msgto" value="<?php echo $ud["username"]; ?>"/><input type="hidden" value="<?php echo $ud['userid']; ?>" name="to"/></div><div id="msg-text" style="width:300px; height:100px; margin-top:10px; margin-left:60px; float:left;"><textarea rows="4" cols="50" name="msg" ></textarea></div><div style="width:300px; "><ul class="roundbuttons sendmessagewidth"><li><input type="button" name="cancel" value="cancel" onClick="document.getElementById(\'light1\').style.display=\'none\';   document.getElementById(\'fade\').style.display=\'none\';"  /></li><li><input type="button" name="send" value="send" onclick="sendmsguser(\'<?php echo $ud['userid'];?>\')" /></li></ul></div></form></div>';
	       
	     $('#light1').html($a); 
	              $('#light1').css({'display':'block'});
	              $('#fade').css({'display':'block'});
	             
	         });
	
	 var hash = location.hash.replace('#', '');
		switch(hash){
	
		case 'basicinfo':
			$('#basic-info-details').css('display','block');
			$('#workandeducations').css('display','none');
			$('#personal').css('display','none');
			$('.basic a').css('background-color','#fff');
			$('.personal a').css('background-color','#eee');
			$('.workeducation a').css('background-color','#eee');
			break;
		case 'workeducation':
			$('#basic-info-details').css('display','none');
			$('#workandeducations').css('display','block');
			$('#personal').css('display','none');
			$('.workeducation a').css('background-color','#fff');
			$('.personal a').css('background-color','#eee');
			$('.basic a').css('background-color','#eee');
			break;
		case 'personalinfo':
			$('#basic-info-details').css('display','none');
			$('#workandeducations').css('display','none');
			$('#personal').css('display','block');
			$('.personal a').css('background-color','#fff');
			$('.workeducation a').css('background-color','#eee');
			$('.basic a').css('background-color','#eee');
			break;	
		default:
			$('#basic-info-details').css('display','block');
		$('#workandeducations').css('display','none');
		$('.basic a').css('background-color','#fff');
		$('.personal a').css('background-color','#eee');
		$('.workeducation a').css('background-color','#eee');
		}	
	//pushState1(event);
	$("#loading").css('display','block');
});

function removelement(){
    $('#light1').css({'display':'none'});
    $('#fade').css({'display':'none'});
}
</script>


<script type="text/javascript" src="http://chat.freniz.com/js/json2xml.js"></script>
<script type="text/javascript" src="http://chat.freniz.com/js/chat.js"></script>
<link type="text/css" rel="stylesheet" media="all" href="http://chat.freniz.com/css/chat.css" />


 <style>

     .status-post-div ul{
	float:left;
	display:block;
	width:100px;
	margin-top:10px;
}
.status-post-div ul li{
	display:inline;
	border-radius: 8px;
	box-shadow: 3px 3px 4px rgba(0,0,0,.5);
	-moz-border-radius: 8px;
	padding:2px 10px;
	-moz-box-shadow: 3px 3px 4px rgba(0,0,0,.5);
	background: #6699FF;
	-webkit-border-radius: 8px;
	-webkit-box-shadow: 3px 3px 4px rgba(0,0,0,.5);
	
}
.status-post-div ul li a{
	text-decoration:none;
}
.status-post-div ul li a:hover{
	color:#FFF;
}
.status-post-div{
	display:none;
	width:99%;
        float: left;
	margin-left:-1px;
	margin-top:0px;
	border-bottom:solid 1px;
	
	
}
            span{
                padding:2px;
              
                font-weight: bold; font-size: 15px;
            }
            p{
                width:400px;
                
            }
            ul{
                display: block;
            	margin-left:-20px;
               
            }
            ul li{
                display: inline-block;
            }
            .wrkedu{
                width: 400px;
            	color:#aaa;
            }
            .labe{
            	color:#444;
            }
            .greenbutton{

background-color: #C1DF79;
background-image: -webkit-gradient(linear, left top, left bottom, from(#C1DF79), to(#C1DF79));
background-image: -webkit-linear-gradient(top, #C1DF79, #C1DF79);
background-image: -moz-linear-gradient(top, #C1DF79, #C1DF79);
background-image: -ms-linear-gradient(top, #C1DF79, #C1DF79);
background-image: -o-linear-gradient(top, #C1DF79, #C1DF79);
background-image: linear-gradient(top, #C1DF79, #C1DF79);
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
border-radius: 3px;
text-shadow: 0 1px 0 rgba(0, 0, 0, .5);
-moz-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
-webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
border: 1px solid #7E1515;
float:left;
height: 20px;

width: 100px;
cursor: pointer;
font: bold 12px Arial, Helvetica;
color: white;
}
.vote-bar {
                text-decoration: none;
                cursor: pointer;
                font-size: 14px; font-weight: bold;
                color: #000;
            }
           .vote-bar :hover{
                color: #fff;
            }
            .close {
                text-decoration: none;
                cursor: pointer;
                font-size: 8px; font-weight: bold;
                color: #000;
                padding: 5px;
            }
            .close:hover{
                color: #fff;
                 border-radius: 30px;
	-moz-border-radius: 30px;
	-webkit-border-radius: 30px; 
        padding: 5px;
                background-color: #aaa;
            }
.greenbutton{

background-color: #C1DF79;
background-image: -webkit-gradient(linear, left top, left bottom, from(#C1DF79), to(#C1DF79));
background-image: -webkit-linear-gradient(top, #C1DF79, #C1DF79);
background-image: -moz-linear-gradient(top, #C1DF79, #C1DF79);
background-image: -ms-linear-gradient(top, #C1DF79, #C1DF79);
background-image: -o-linear-gradient(top, #C1DF79, #C1DF79);
background-image: linear-gradient(top, #C1DF79, #C1DF79);
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
border-radius: 3px;
text-shadow: 0 1px 0 rgba(0, 0, 0, .5);
-moz-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
-webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
border: 1px solid #7E1515;
float:right;
height: 20px;

width: 50px;
cursor: pointer;
font: bold 12px Arial, Helvetica;
color: white;
}
#profilepic,#smileypic{
                 border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
             -moz-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
	        -webkit-box-shadow: 0px 0px 5px 5px rgba(68, 68, 68, 0.6);
	        -ms-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
	        box-shadow: 1px 1px 2px 2px rgba(68, 68, 68, 0.6);
            }  
            #screen,#draggable{
             -moz-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
	        -webkit-box-shadow: 0px 0px 5px 5px rgba(68, 68, 68, 0.6);
	        -ms-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
	        box-shadow: 1px 1px 2px 2px rgba(68, 68, 68, 0.6);
            }  
         #alert-span{
         	    border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
             -moz-box-shadow: 5px 5px 10px 10px rgba(68,68,68,1.0);
	        -webkit-box-shadow: 2px 2px 10px 10px rgba(68,68,68,1.0);
	        -ms-box-shadow: 2px 2px 10px 10px rgba(68,68,68,1.0);
	        box-shadow: 2px 2px 10px 10px rgba(68,68,68,1.0);
         }
            #stature-stream{
            	     border-radius: 10px;
	-moz-border-radius: 10px;
	-webkit-border-radius: 10px;
             -moz-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
	        -webkit-box-shadow: 0px 0px 5px 5px rgba(68, 68, 68, 0.6);
	        -ms-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
	        box-shadow: 1px 1px 2px 2px rgba(68, 68, 68, 0.6);
            background-color:#eee;
           
            }
            	nav #change {
		  margin-top:20px;
            		 margin-left:20px;
		  padding: 2px;
		  list-style: none;
		  position: relative;
		  float: left;
		  background: #eee;
		  border-bottom: 1px solid #fff;
		  -moz-border-radius: 3px;
		  -webkit-border-radius: 3px;
		  border-radius: 3px;    
		}
		
		nav #change li {
		  float: left;      
			   border-left:solid 1px; 
		}
     
      nav #change  li:first-child  a{
		   
			border-left:solid 1px #eee; 
		}
		
		nav #acountmenu a {
		  display: inline-block;
		  /*display: inline;
		  *zoom: 1;  */
		  height: 25px;
		  line-height: 25px;
		  font-weight: bold;
		  padding: 0 8px;
		  text-decoration: none;
		  color: #444;
		  text-shadow: 0 1px 0 #fff; 
		}
		
		nav #acountmenu a {
		  -moz-border-radius: 0 3px 3px 0;
		  -webkit-border-radius: 0 3px 3px 0;
		  border-radius: 0 3px 3px 0;
		}
		
		
			nav #acountmenu-trigger {
		  -moz-border-radius: 3px 0 0 3px;
		  -webkit-border-radius: 3px 0 0 3px;
		  border-radius: 3px 0 0 3px;
		}
		
		nav #acountmenu a:hover {
		  background: #fff;
		}
		.active{
			background: #fff;
		}
		#remove-items{
			border-radius: 10px;
-moz-border-radius: 10px;
-webkit-border-radius: 10px;
-moz-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
-webkit-box-shadow: 0px 0px 5px 5px rgba(68, 68, 68, 0.6);
-ms-box-shadow: 0px 0px 5px 5px rgba(68,68,68,0.6);
box-shadow: 1px 1px 2px 2px rgba(68, 68, 68, 0.6);
		}
#remove-items ul{
		display:inline;
		width:100px;
            background-color:#eee;
	}
	#remove-items ul li{
		display:block;
		background-color:#FFF;
		padding:10px;
	background-color:#eee;
		
		
	}
	#remove-items ul li a{
		text-decoration:none;
		color:black;
		position:absolute;
		padding:2px;
	}
	#remove-items ul li a:hover{
		background-color:#A4A4A4;
		width:100px;
		display:block;
		color:#FFF;
		 -moz-box-shadow: 0 2px 2px -1px rgba(0,0,0,.9);
		  -webkit-box-shadow: 0 2px 2px -1px rgba(0,0,0,.9);
		  box-shadow: 0 2px 2px -1px rgba(0,0,0,.9);	
		padding:2px;
		
		
	}
 #leaf-vote{
	            
	    	  cursor: pointer;
			  font-weight: bold;
			  padding: 6px 20px;
			  text-decoration: none;
			  color: #000;
			  text-shadow: 0 1px 0 #fff; 
			  -moz-border-radius: 6px;
			  -webkit-border-radius: 6px;
			  border-radius: 6px;
	                  background-color: rgb(146,213,0);
			  background-image: -webkit-gradient(linear, left top, left bottom, from(rgb(146,213,0)), to(rgb(146,213,0)));
			  background-image: -webkit-linear-gradient(top, rgb(146,213,0), rgb(146,213,0));
			  background-image: -moz-linear-gradient(top, rgb(146,213,0), rgb(146,213,0));
			  background-image: -ms-linear-gradient(top, rgb(146,213,0), rgb(146,213,0));
			  background-image: -o-linear-gradient(top, rgb(146,213,0), rgb(146,213,0));
			  background-image: linear-gradient(top, rgb(146,213,0), rgb(146,213,0));
	                   -webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-ms-box-sizing: border-box;
	-o-box-sizing: border-box;
	box-sizing: border-box;
	-webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.07),0 1px 0 rgba(255, 255, 255, 0.07) inset;
	-moz-box-shadow: 0 1px 0 rgba(255,255,255,0.07),0 1px 0 rgba(255,255,255,0.07) inset;
	-ms-box-shadow: 0 1px 0 rgba(255,255,255,0.07),0 1px 0 rgba(255,255,255,0.07) inset;
	-o-box-shadow: 0 1px 0 rgba(255,255,255,0.07),0 1px 0 rgba(255,255,255,0.07) inset;
	box-shadow: 0 1px 0 rgba(255, 255, 255, 0.07),0 1px 0 rgba(255, 255, 255, 0.07) inset;
	        
	        }
	        #leaf-vote:hover{
	            color:#fff;
	            
	        }
.span-change{
	  cursor: pointer;
			  font-weight: bold;
			  padding: 6px 20px;
			  text-decoration: none;
			  color: #000;
			  -moz-border-radius: 6px;
			  -webkit-border-radius: 6px;
			  border-radius: 6px;
	                  background-color: #eee;
			                   -webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	-ms-box-sizing: border-box;
	-o-box-sizing: border-box;
	box-sizing: border-box;
	-webkit-box-shadow: 0 1px 0 rgba(255, 255, 255, 0.07),0 1px 0 rgba(255, 255, 255, 0.07) inset;
	-moz-box-shadow: 0 1px 0 rgba(255,255,255,0.07),0 1px 0 rgba(255,255,255,0.07) inset;
	-ms-box-shadow: 0 1px 0 rgba(255,255,255,0.07),0 1px 0 rgba(255,255,255,0.07) inset;
	-o-box-shadow: 0 1px 0 rgba(255,255,255,0.07),0 1px 0 rgba(255,255,255,0.07) inset;
	box-shadow: 0 1px 0 rgba(255, 255, 255, 0.07),0 1px 0 rgba(255, 255, 255, 0.07) inset;
}
#option ul,.option ul{
			display:inline;
		
		}
		#option,.option{
		width:150px;
		position:absolute;
		background-color:#ccc;
		 -moz-box-shadow: 0px 0px 5px 5px rgba(152,164,238,1.3);
	        -webkit-box-shadow: 0px 0px 5px 5px rgba(152,164,238,1.3);
	        -ms-box-shadow: 0px 0px 5px 5px rgba(152,164,238,1.3);
	        box-shadow: 1px 1px 2px 2px rgba(152,164,238,1.3);
		}
		#option ul li,.option ul li{
			display:block;
		width:144px;
		
			padding:2px;
	
		}
		#option ul li:hover,.option ul li:hover{
			display:block;
		background-color:#eee;
		cursor:pointer;
		}
		
		#option ul li a,.option ul li a{
			width:200px;
			padding:2px;
		color:#000;
			
		}
		
		
        </style></head>
<body> <img id="loading" src="images/prettyGallery/loading-gif-animation.gif" style="position:absolute; display: none; margin-left: 45%" height="32" width="32"></img>
    <div class="headerdiv">
<div id="freniz-logo" style="width:220px; float:left; height:60px; ">
    <a style="text-decoration:none; cursor: pointer; "href="#"><img src="http://localhost/freniz_zend/public/images/freniz.png"/></a>
</div>
<div id="smile-mood-change" style="width:40px; float:left; height:60px; ">
<?php if(isset($session['userid']) && $session['type']!='none' && $session['type']!='leaf'){ ?>
   <div style="width:40px; margin-top: 10px; float:left; height:40px; "><img id="top-smiley" style="cursor: pointer;marin-top:10px" src="http://localhost/freniz_zend/public/images/mood/32/<?php $smood=explode(',', $session['mood']); echo $smood[0];?>" width="40" height="40"/></div>
<?php } ?>
</div>
<div id="top-menu-items" style=" float:left; height:60px; ">
<div style="height:30px; margin-top: 15px; margin-left: 30px;  float:left; ">
    <?php if(isset($session['userid']) && $session['type']=='user'){ ?>
<ul id="top-menu">
	<li class="link-bord" id="top-underline"><a href="http://www.freniz.com/">Stream</a></li>
	<li class="link-bord" id="top-underline"><a href="http://www.freniz.com/<?php echo $session['userid']; ?>">Bio</a></li>
	<li class="link-bord" id="top-underline"><a id="mes-a" href="http://www.freniz.com/messages">Message</a></li>
	<li class="link-bord" id="top-underline"><a href="http://www.freniz.com/blog/<?php echo $session['userid']; ?>">Blog</a></li>
	
	<li class="link-bord">
		<a id="alert-a" href="#">Alert</a>
				<ul style=" margin-top: -5px;">
					<li><a id="noti-a" href="http://www.freniz.com/notification">Notifications</a></li>
					<li><a id="revi-a" href="http://www.freniz.com/review/reviews">Reviews</a></li>
				</ul>				
	</li>
	<li class="link-bord">
		<a href="#">Apps</a>	
		<ul style=" margin-top: -5px;">
					 <li ><a href="http://www.freniz.com/forum">Forum</a></li>
                    <li><a href="http://www.freniz.com/diary">Diary</a></li>
					<li><a href="http://www.freniz.com/slambook">Slam book</a></li>
				</ul>		
	</li>
	
</ul>
<?php }else{ ?>
<ul id="top-menu">
	<li class="link-bord" id="top-underline"><a href="http://www.freniz.com/<?php echo $session['userid']; ?>">Home</a></li>
<ul>

<?php } ?>
</div>

</div>
<div style=" width: 250px; margin-right: 10px; float:right; height:60px; ">
<?php if($session['userid']){ ?> 
<div id="accout-name-settings" style=" float:right; font-weight: bold; color: #fff; height:40px;">
   
 <nav>
           <ul>
		<li id="settings">
			<a id="acount-trigger-setting" href="javascript:void(0)" title="<?php echo $session['username']; ?>" class="active">
				<?php 
				$name = $session['username'];
                 if(strlen($name) > 24 )
                 {
                 echo substr($name, 0, 26).'...' ;
                                 }
               else {
                   echo $name;
               } ?> <span id="logspan">&#x25BC;</span>
			</a>
			<div id="acount-content-setting" >
                            <ul>
                                <label style="color:#999; font-size: 18px; font-weight: bold">Freniz Account</label>
                                <?php 
                                $val=$session['adminpages_details'];
                                unset($val[$session['userid']]);
                                foreach ($val as $value){
                                	?>
                            		<li title="<?php echo $value['username']; ?>" onclick="switchuser('<?php echo $value['userid'];?>')"  ><div style="border-bottom:solid 1px #ccc; padding:3px; height:16px"><label  style="color:#999; height:16px;float:left;font-weight: bold"><img style="width:16px; height:16px; float:left" src="http://localhost/freniz_zend/public/images/32/32_<?php echo $value['propic_url'];?>"/><a title="<?php echo $value['username']; ?>" style="font-size:10px;padding-left:4px;"><?php $leafname = $value['username'];
                 if(strlen($leafname) > 15 )
                 {
                 echo substr($leafname, 0, 15).'...' ;
                                 }
               else {
                   echo $leafname; } ?></a></label></div></li>
                                 	<?php } 
                                 	if($session['type']!='none' && $session['type']!='leaf'){
                                 	?>
                                 	 <li ><a href="http://www.freniz.com/index/personalinfo?log_aug=finis">Account Settings</a></li>
                                 <li><a href="http://www.freniz.com/index/privacysettings">Privacy Setting</a> </li>
                                <?php }elseif($session['type']=='leaf'){ ?>
                                   <li><a href="http://www.freniz.com/leafedit/<?php echo $session['userid']; ?>#privacyinfo">Privacy Setting</a> </li>
                                <?php } ?>
                                 <li><a href="#">Help</a></li>
                                 <li><a href="http://www.freniz.com/index/logout">Take Out</a> </li>
                            </ul>
			</div>                     
		</li>
                
            </ul>
        </nav>

    
 
</div>
 <?php }else {?>
      <div style="width:250px; height: 40px; float:right">
             <a style=" padding:3px 10px; float:right" href="http://www.freniz.com/loginattempt" id="leaf-vote">Login</a>
          </div>
    <?php } ?>
 
<div id="search-option-items" style="width:200px; margin-top: -10px;margin-right:50px;  float:right; height:20px; ">
    <input class="search-box" id="searchusers"  type="text" placeholder="search" onfocusout="searchitemsout(this)" onfocus="searchitemsin(this)" style="width:250px; height:30px" ><span id="searchusers-load" style="margin-top:-24px; margin-left: 215px; color: #ffffff; display: none; position: absolute;"><img src="images/blackloading.gif" height="16" width="16"/></span></input>
    <span style="color:#ccc; cursor: pointer; font-size: 12px; float: right; margin-top: -24px; margin-right:-45px">&#x25BC;</span>
</div>
</div>


</div>  
<!-- main container begins  -->

 
<div id='topcontainer' style="width:1000px; margin-left: auto; margin-right: auto;  ">
   
    <div id="sub-tab-menu-items" style="width: 1000px; margin-top: -10px; float: left; height: 40px; ">
           
            <div id="account-tab" style="" >

                <ul id="sub-menu" style="width: 450px; ">
                        <li ><a href="http://www.freniz.com/scribbles?userid=<?php echo $ud['userid']; ?>">Scribbles</a></li>
                        <li ><a href="http://www.freniz.com/<?php echo $ud['userid']; ?>/albums">Gallery</a></li>
                        <li ><a href="http://www.freniz.com/videos/<?php echo $ud['userid']; ?>">Videos</a></li>
                        <li ><a href="http://www.freniz.com/admire/<?php echo $ud['userid']; ?>">Admiration</a></li>
                        <li ><a href="http://www.freniz.com/blog/<?php echo $ud['userid']; ?>">Blog</a></li>
                         <li ><a href="http://www.freniz.com/forum">Forum</a></li>
                </ul>
               <?php if($session['userid']!=''){?>
                <ul id="sub-menu" style="float: right;  margin-top: -15px; ">
                         <?php if($session['userid']== $ud['userid']){ ?>
                    <li><a href="#">Theme</a></li> <?php }?>
                     <?php if(($session['userid']!= $ud['userid'])){ ?>
                     
                        <?php  if($ud['privacy_request'] && !in_array($session['userid'], $ud['friends']) && !in_array($ud['userid'], $session['incomingrequest']) && !in_array($ud['userid'], $session['sentrequest'])) {?>  

                        <li ><a title="Add Me" onclick="addfriends('<?php echo $ud['userid'] ?>');" href="javascript:void(0)">Add Me</a></li><?php } ?>
                        <?php if(in_array($ud['userid'], $session['sentrequest'])){ ?>
                        <li ><a  onclick="cancelfriends('<?php echo $ud['userid']; ?>')" href="javascript:void(0)">Cancel Req </a></li>
                            <?php } ?>
                         <?php if(in_array($ud['userid'], $session['incomingrequest'])){ ?>
                        <li ><a title="Add to list" onclick="acceptfriends('<?php echo $ud['userid']; ?>')" href="javascript:void(0)">Accept</a></li><?php } ?>
                       <?php if($ud['privacy_message']) {?> 
                        <li ><a title="Send Message to friends" id="message-button" href="javascript:void(0)">Message</a></li><?php } ?>
                                       
                        <li style="position: relative" ><a href="streams.php">Options</a>
                            <ul style=" margin-top: -15px;margin-left: -60px;">
                                        <?php $frnds=$ud['friends'];
                                                if(in_array($session['userid'], $frnds)){
                                                ?>
                                        <li><a id="remov-frds" onclick="" href="javascript:void(0)">Unfriend</a></li><?php }?>
					
                                        <li><a href="#">Report</a></li>
					<li><a href="#">Block</a></li>
				</ul>	
                           
                        </li>
                         <?php } ?>
                         
                </ul>
                <?php } ?>
            </div>
            
        </div> 
 
<div id="maincontainer" style="margin-top:10px; ">

  <div style="width:1000px; font-size:24px;font-weight:bold; margin-top:10px; margin-left:20px; float:left">
 <div style=" margin-top:-10px">
 <?php if ($wallpic!='no')
 	      echo $ud['username']; 
 ?>
 <input id="ccuserid" type="hidden" value="<?php echo $ud['userid']; ?>" />

    <?php 
    if($session['userid']!=$ud['userid']){
                        if(!in_array($session['userid'], $ud['vote'])){
                        ?>
						 <span style="float:right; margin-top:-4px; margin-right:20px; position:relative" onclick="uservote('<?php echo $ud['userid']; ?>',this)" id="leaf-vote">Wink</span>
                        <?php }else{ ?>
                       <span style="float:right; margin-top:-4px;margin-right:20px; position:relative" onclick="userunvote('<?php echo $ud['userid']; ?>',this)" id="leaf-vote">Winked</span>
                        <?php } } 
                      if(count($ud['vote'])!=0){ ?>
 <label style="float:right; font-size:16px; font-weight:bold; margin-right:5px"><span id="vote-count"><?php echo count($ud['vote']);?></span> people <?php if($ud['userid']==$session['userid']) echo 'winked';?></label>
    <?php }?>
 </div>
  
 
</div>


        <div style="width:1000px;  margin-top:-16px; overflow: hidden; float: left;  ">
         <!-- profile pic -->   
         <div style="width:1000px; padding:5px; height: <?php if ($wallpic=='no'){ echo '210px'; }else echo '310px';?>; border-bottom:solid 1px">
         
             <div style="width:<?php if ($wallpic=='no'){ echo '800px;';  }else echo '200px';?>; margin-left: 5px; float:left; height: <?php if ($wallpic=='no'){ echo '200px'; }else echo '300px';?>;">
                 
                  <div style="float: left; margin-top: 10px;">
                  <img height="200" width="200" id="profilepic" src='http://localhost/freniz_zend/public/images/200/200_<?php echo $ud['propic_url'] ?>' />
         </div>   
         <?php if ($wallpic=='no'){ ?>
         <div style="float: left; width:400px; font-size:24px;font-weight:bold; margin-left:10px; margin-top: 10px;">
          <?php echo $ud['username']; ?>
          </div>
         <?php }?>
                 <div id="change-smiley" style="width:200px; margin-left:10px; padding: 5px; float: left; margin-top: 5px; max-height: 70px; border-top:solid 1px #ccc">
               
                 <?php $mood=explode(',', $ud['mood'])
                 
                 ?>
                     <img id="smileypic" alt="<?php echo $mood[0]; ?>" style="float:left" src="http://localhost/freniz_zend/public/images/mood/32/<?php echo $mood[0]; ?>" height="50" width="50"/>
                      <span id="mood-desc" style="width:130px; margin-left: 5px;"><?php echo $mood[1]; ?></span>
                    
         </div> 
          <div style="width:50px; margin-top:<?php if ($wallpic=='no'){ echo '190px;margin-left:220px'; }else echo '285px';?>; position:absolute; height:10px;">
           <div id="edit-prof"></div>
            <?php if ($session['userid']==$ud['userid'] && !empty($session)){ ?>
          <a id="edit-pro" href="javascript:void(0)" style="text-decoration:none; font-size:16px; font-weight:bold; color:#444">edit</a>
                   <?php } ?>
                     </div>  
         </div>  
         <div id="container" style="margin-top:10px;" >
        <?php if ($wallpic!='no'){ ?>
              <div id="screen" style="width:770px; border:solid 4px #ccc; overflow:hidden; position:absolute; clear:both; height: 300px; margin-left: 215px; ">
              <img style="width:100%; top:<?php echo $wallpic[1]?>;position:relative;" class="drag-image" id="draggable" src='images/500/500_<?php echo $ud['secondarypic1_url'] ?>' />
         </div>
     <?php }?>
         <?php if ($wallpic=='no' && !empty($session) && $session['userid']==$ud['userid']){ ?>
         <span onclick="location.href='http://www.freniz.com/getimages?albumid=<?php echo $session['secondarypicalbum']; ?>';" style="position: absolute; display:block; margin-left:800px; margin-top:170px" id="leaf-vote">set wallphoto</span>
       <?php }if ($wallpic!='no' && !empty($session) && $session['userid']==$ud['userid']){?>
          <span class="span-change" style="position: absolute; display:none; margin-left:800px; margin-top:270px" id="change-span">Change</span>
       <?php }?>
         </div> 
      </div>
         
               
        
          <div style="width:1000px; float: left; padding: 5px;  border-bottom:solid 1px">
              <!-- basic info and friends -->
            <div style="width:1000px; float: left;  ">
                 <div style="width:500px; float: left;  ">
                 <!-- basic info -->
                     
                     <div  style="width:450px; overflow:hidden; float: left; border-bottom:solid 1px">
                    <label style="font-weight:bold; font-size:24px;">Friends</label>
                   <?php if($session['userid']!=$ud['userid']){?>
                    <a style="float:right; font-size:12px; font-weight:bold;">Mutual : <?php echo count(array_intersect($session['friends'], $ud['friends'])); ?></a>
                   <?php }?>
                    <ul>
                   <?php foreach($ud['friends_profiles'] as $userid=> $values){ ?> 
                     <li><a  title="<?php echo $values['fname'].' '.$values['lname']; ?>" href="<?php echo $values['userid']; ?>"><div id="leaf_fav" class="hover" data="<?php echo $values['userid'];?>"style=" width:75px;  margin-left:5px;float:left; position:relative; height:75px; background-image:url('images/75/75_<?php echo $values['imageurl']; ?>');background-color:#cccccc; border:solid 1px"><div style=" width:75px; position:absolute; bottom:0; opacity:0.6; color:#000; background-color:#cccccc;  filter:alpha(opacity=60);"><?php echo $values['fname']; ?></div></div></a></li>
                        <?php }?>
                    
                    </ul>
                     <?php if(count($ud['friends_profiles'])>4){   ?>
                    <div style="width:50px; margin-top: -20px; float: right; ">
                             <span><a href="javascript:void(0)" onclick="getfriendslist('<?php echo $ud['userid'];?>')">More..</a></span>
                         </div> 
                           <?php } ?>
                </div>
                 
                    
                  <div style=" width:500px; float: left; ">
                 <div id="stature" style="width:500px; float: left; ">
                         <label style="float:left; width:150px; font-size: 14px; border-bottom:solid 1px; font-size:18px; font-weight: bold;">Today's update</label>
                       <div style="width:400px; margin-left: 30px; font-weight: bold; float: left; ">
                       <?php if(isset($ud['stature'])){ ?>
                           <?php echo htmlspecialchars($ud['stature']); ?>
               <?php }else{?>
               <p>No updates!</p><?php }?>
                 </div>   
                         
                         <div style="width:50px; float: right; ">
                             <span><a href="#">More..</a></span>
                         </div> 
                            <div id="main-content">
       
    <div style="width:100%; float:left">
        <form name="postform" >
<div style="width:100%; margin-top: 10px; float:left; ">
    <label>User Stream</label>
<textarea id="message-content"  placeholder="Write what's on your mind" name="post" style="resize:none;  outline: none; margin-top:0px; margin-left:0px; width:99%; height: 30px;" class="expand" onclick="expand()" ></textarea>
</div>

<div class="status-post-div">
<input onclick="addstature()" class="greenbutton" type="button" value="Update" />


<div style="width:200px; float:left">
</div>

<div style="float: right; margin-top:20px; width:150px;height:20px;"><label style="float: left; padding-right:5px">visible</label><select id="stature-pt"><option <?php  if( $session['privacy']['staturevisi']=='friends') {echo 'selected="selected"';} ?> value="friends">Friends</option><option <?php if( $session['privacy']['staturevisi']=='fof') {echo 'selected="selected"';} ?> value="fof">FOF</option><option <?php if( $session['privacy']['staturevisi']=='public') {echo 'selected="selected"';} ?> value="public">Public</option><option <?php if( $session['privacy']['staturevisi']=='private') {echo 'selected="selected"';} ?> value="private">Private</option><option <?php if( $session['privacy']['staturevisi']=='specific') {echo 'selected="selected"';} ?> value="specific">Specific</option></select></div>
				
<div style="float:right; margin-top:20px; height:20px; width:150px"><label style="float: left;padding-right:5px">comment</label><select id="comment-pt"><option <?php if( $session['privacy']['stature']=='friends') {echo 'selected="selected"';} ?> value="friends">Friends</option><option <?php if( $session['privacy']['stature']=='fof') {echo 'selected="selected"';} ?> value="fof">FOF</option><option <?php if( $session['privacy']['stature']=='public') {echo 'selected="selected"';} ?> value="public">Public</option><option <?php if( $session['privacy']['stature']=='private') {echo 'selected="selected"';} ?> value="private">Private</option><option <?php if( $session['privacy']['stature']=='specific') {echo 'selected="selected"';} ?> value="specific">Specific</option></select></div>
			
			</div>
        </form>
</div>


    </div>
                 </div> 
                 
                 
                 <div id="statures" style="width:100%; margin-top:10px;float:left;">
  <script type="text/javascript">
	getstatures('<?php echo $ud['userid'];?>');
  </script>
</div>

                 
                 
                 
                 
                 </div>  
                  
                 </div>
                 <div style="width:450px;  margin-left: 20px; padding-left:5px; float: left; border-left:solid 1px">
					           
					                <nav>
						<ul id="change">
								<li id="acountmenu" style="border-left:solid 1px #eee" class="basic"><a href="#basicinfo" >Basicinfo</a></li>
								<li id="acountmenu" class="workeducation"><a href="#workeducation" >Workandedu</a></li>
							<li id="acountmenu" class="personal"><a href="#personalinfo" >Personalinfo</a></li>
						</ul>
					</nav>
					
	                     <div id="basic-info-details" style="width:450px; float: left;  border-bottom:solid 1px">
                         <label style="float:left; width:450px; border-bottom:solid 1px; font-size: 14px; font-weight: bold;">Basic info</label>
                       <div style="width:350px; margin-left: 10px; padding:3px; float: left; ">
                           <?php if(isset($ud['dob'])) {?>
                           <span class="wrkedu">B'day:<label class="labe"><?php echo $ud['dob'];?></label></span><br/>
                           <?php }?>
                           <span class="wrkedu">Gender:<label class="labe"><?php echo $ud['sex']; ?></label></span><br/>
                            <?php if(isset($ud['currentcity'])) {?>
                           <span class="wrkedu" >Living in:<a class="labe" href="javascript:void(0)"><?php echo $ud['currentcity_name']; ?></a></span><br/><?php }?>
                         <?php if(isset($ud['hometown'])) {?>
                           <span class="wrkedu" >Home town:<a class="labe"href="javascript:void(0)"><?php echo $ud['hometown_name']; ?></a></span><br/>
                           <?php }?>
                            <?php if(isset($ud['rstatus'])) {?>
                           <span class="wrkedu" >R'status:<label class="labe"><?php echo $ud['rstatus']; ?></label></span><br/>
                           <?php }?>
                           <?php if(isset($ud['religion'])) {?>
                           <span class="wrkedu">Religion:<label class="labe"><?php echo $ud['religion'];?></label></span><br/>
                           <?php }?>
                    </div>   
                         
                        
                 </div> 
                 
                 <div id="workandeducations" style="width:450px; display:none; float: left;  border-bottom:solid 1px">
                         <label style="float:left; width:450px; font-size: 14px; border-bottom:solid 1px; font-weight: bold;">Work and Education info</label>
                       <div style="width:350px; margin-left: 30px; float: left; ">
                           <span class="wrkedu"  >Worked at:<?php
								 $employers=array_reverse($ud['employer']);
						$i=0;
						foreach($employers as $employer){
						      if($i<1){
						          
						          echo '<a style="text-decoration:none; color:#444" href="'.$employer.'">'.$ud['fav_pages'][$employer]['pagename'].'</a>';
						          
						           }
						           $i++;
						      }
						?></span><br/>
                           <span class="wrkedu">College at:<?php
								 $colleges=array_reverse($ud['college']);
						$i=0;
						foreach($colleges as $college){
						      if($i<1){
						          echo '<a style="text-decoration:none; color:#444" href="'.$college.'">'.$ud['fav_pages'][$college]['pagename'].'</a>';
						          
						           }
						           $i++;
						      }
						?></span><br/>
						    <span class="wrkedu">School at:<?php
								 $schools=$ud['school'];
						$i=0;
						foreach($schools as $school){
						      if($i<1){
						          echo '<a style="text-decoration:none; color:#444" href="'.$school.'">'.$ud['fav_pages'][$school]['pagename'].'</a>';
						           }
						           $i++;
						      }
						?></span>
                            
                 </div>   
                         
                        
                 </div> 
                 
                  <div id="personal" style="width:450px;  display:none; float: left;  border-bottom:solid 1px">
                 <div style="width:450px;  float:left;">
                 <?php   if(!empty($personalinfo)){ if(isset( $personalinfo['body'])){?>
<div style="width:400px; padding:5px;  border-top:solid 1px ">
   <label style="font-size:18px; font-weight:bold"> Build</label>
 <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php  echo $personalinfo['body']; ?>
</div>

</div>
<?php } if(isset( $personalinfo['look'])){?>
  <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Look</label>
   <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php  echo $personalinfo['look']; ?>
</div>

</div>
<?php } if(isset( $personalinfo['ethnicity'])) {?>
  <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Ethicity</label>
  <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php echo $personalinfo['ethnicity']; ?>
</div>

</div>
<?php } if(isset( $personalinfo['smoke'])){?>
    <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Smoking</label>
  
 <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php  echo $personalinfo['smoke']; ?>
</div>

</div>
<?php } if(isset( $personalinfo['drink'])){?>
     <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Drinking</label>
   <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php echo $personalinfo['drink']; ?>
</div>

</div>
<?php } if(isset( $personalinfo['pets'])){?>
    <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Pet</label>
  <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php echo $personalinfo['pets']; ?>
</div>

</div>
<?php } if(isset( $personalinfo['sexual'])){?>
  <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Sexual</label>
   <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php  echo $personalinfo['sexual']; ?>
</div>

</div>
<?php }if(isset( $personalinfo['humor'])){?>
   <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Humor</label>
   <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php  echo $personalinfo['humor']; ?>
</div>
 

</div>

<?php } if(isset( $personalinfo['passion'])){?>
 <div style="width:400px; padding:5px;  border-top:solid 1px">
  <label style="font-size:18px; font-weight:bold"> Passions</label>
   <div style="max-width:400px; margin-left:30px; margin-top:10px; padding:5px;">
<?php  echo $personalinfo['passion']; ?>
</div>
 

</div>
<?php  } }else echo 'no updates yet.';?>
</div>
                        </div>
                 
                  <div style="width:450px;  float: left; border-bottom:solid 1px">
                    <label style="border-bottom:solid 1px;">Photos</label>
                  <ul>
                        <?php
                        if(empty($ud['pinnedpic']['images'])){
                        echo 'No Photos';
                        }else{
                        
                         foreach($ud['pinnedpic']['images'] as $imageid=>$values){?>
                         <li><div style="width:50px; height: 50px; border: solid 1px"><img src="http://localhost/freniz_zend/public/images/50/50_<?php echo $values['url'];?>" height="50" width="50" /></div></li>
                    	<?php } }?>
                    	  
                    		
                    	
                    </ul>
                      <?php if(count($ud['pinnedpic']['images'])>6){   ?>
                    <div style="width:50px; margin-top: -20px; float: right; ">
                             <span><a href="http://www.freniz.com/getimages?albumid=<?php echo $ud['chartpic'];?>">More..</a></span>
                         </div> 
                         <?php } ?>
                </div>
                 
                <div style="width:500px; float: left; ">
                    <label style="border-bottom:solid 1px;">Winked Leaf's</label>
                    <ul>
                    <?php 
                     if(count($ud['fav_pages_merged'])>8){
                    	$fav_pages=array_rand($ud['fav_pages_merged'],8);
                    	
                    }else {
					$fav_pages=array_keys($ud['fav_pages_merged']);
					
}					

foreach($fav_pages as $pageid){

					?>
                        <li><a class="hover" data="<?php echo $ud['fav_pages'][$ud['fav_pages_merged'][$pageid]]['pageid'];?>" style="color:#000;" href="<?php echo $ud['fav_pages'][$ud['fav_pages_merged'][$pageid]]['page_url']; ?>"><div style="width:100px; height: 100px; margin-left:5px; border: solid 1px"><div id="leaf_fav" class="show"style=" width:100px; margin-top:5px; margin-left:5px;float:left; position:relative; height:100px; background-image:url('http://localhost/freniz_zend/public/images/75/75_<?php echo $ud['fav_pages'][$ud['fav_pages_merged'][$pageid]]['imageurl'];?>'); background-repeat:no-repeat; border:solid 1px"><div style=" width:100px; position:absolute; bottom:0; opacity:0.6; color:#000; background-color:#cccccc;  filter:alpha(opacity=60);"><?php echo $ud['fav_pages'][$ud['fav_pages_merged'][$pageid]]['pagename']; ?></div></div></div></a></li>
                     
                      <?php }?> 
                  
                    </ul>
                    <div style="width:150px; margin-right: 30px; margin-top: -20px; float: right; ">
                             <span><a href="http://www.freniz.com/index/favourites">Go to favorites..</a></span>
                         </div> 
                    <div style="width:440px; margin-left: 10px; float: left; border-bottom:solid 1px">
                    </div>
                </div>
                 
                        <!-- friends -->
                 <div style="width:250px; margin-left: 10px; margin-top: 10px; float: left; ">
                         <div id="fourthdiv" style="width:230px;  float:left;  ">



</div>
                     <div style="width:300px; height: 200px;  float: left;">
                          <div id="suggestion-list" style="width:280px; float: left; border:solid 1px">
                         </div>
                     </div>
                 </div>
                 </div>
             
                
            </div>
              
              
              
              
              
         </div>  
         
             
         </div>    
            
            
            
            
        </div>
    <!-- right side end  -->
    
    
    
    
   
<div id="footer" style="width: 100%; height: 100px; float: left;">
    
    
    
</div>
 
        

</div>


        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        

    


  
  <div id="hover-data"></div>
 <div id="com-container"></div>
  <div id="musictab">

 <div class="foot-tab">
     <ul>
 <li><a href="#">About</a></li>
         <li><a href="http://www.freniz.com/search">Search</a></li>
         <li><a href="#">Terms</a></li>
         <li><a href="#">Privacy</a></li>
         <li><a href="#">Help</a></li>
         <li><a href="http://www.freniz.com/createleaf">Create a leaf</a></li>
          <li><a href="http://www.freniz.com/developers">Developers</a></li>
         <li><a href="http://www.freniz.com/hireus">Hire Us</a></li>
     </ul>
     
 </div> 
       
</div>

<script type="text/javascript">
			getcount();	
				suggestion();				
					function miniprofile(data,element){
							var position=$(element).offset();
							getminiprofile(data,position.top,position.left);
						}


					var options_xmlsearch = function(type,appendto){
					     if(!appendto)
					        appendto='body'; 
					     var options={
					                script:"http://www.freniz.com/search?type="+type+"&",
							varname:"key",
					                type:type,
					                appendto:appendto,
					                callback:function(suggestion,fld){
												window.location.href="http://www.freniz.com/"+suggestion.id;
					                       }
					                
					            };
					            return options;
						}
           var options_xmlsearch = new AutoSuggest('searchusers', options_xmlsearch('all'));
        
        
        
</script>
<div id="light1" class="white_content"></div>
<div id="fade" onClick="removelement()" class="black_overlay">
        </div>
<div id="normal"></div>
 <div id="remove-items" style="width:130px; display:none; background-color:#eee;float:left; ">
<ul>
<li class="delete" ><a href="javascript:void(0)">delete</a></li>
<li><a  href="javascript:void(0)">settings</a></li>
<li><a  href="javascript:void(0)">Report</a></li>
</ul>
</div> 
<div id="mood-set-div"style="position:absolute;  display:none;top:30%; left:25%; width:445px; height:180px; ">
 <span-smiley style="background:none; position:relative" id="mood-smile" >

</span-smiley>
 <span class="edit-cancel-span"style="float:right; position:relative;margin-top:160px; margin-right:20px" id="leaf-vote">Cancel</span>
                   
</div>
 
</body>
</html>
