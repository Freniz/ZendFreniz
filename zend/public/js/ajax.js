/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

    



var i=0;

var postsfrom={statures:0,scribbles:0,blogs:0,admires:0,images:0,videos:0,votes:0,pinnedpics:0,messges:0,favourites:0,searchuers:0,searchleafs:0,searchplaces:0};
var limit_default=20;
var comment_max=0;
var comments={statures:{},scribbles:{},images:{},videos:{}};
var defaultcomments=3,commentlimit=50;
var loadCommentsTimeout=0;
var suggestionfrom=0;
var suggestionlimit=4;


/*
function pushState1(e){
i=0;
$('a').click(function(e){
e.preventDefault();
var url = $(this).attr("href");
if(i==0){
		history.pushState({page:url}, url, url);
		updateSite(url,e);
		}
		i++;
		
});
}
$(document).ready(function(e){
pushState1(e);
});
*/
function updateSite(url,e)
{
	
$.ajax({
	url:url,
	cache:false,
	dataType:"text",
	success:function(text){updatePage(text);}
});
 pushState1(e);
}
function updatePage(text)
{
	
var a=document.createElement('div');
a.innerHTML=text;
document.title=$(a).find('title').text();

$('#sub-tab-menu-items').html($(a).find('#sub-tab-menu-items').html());
$('#maincontainer').html($(a).find('#maincontainer').html());
}
$(window).bind("popstate", function(e){
	e.preventDefault();
	var state = event.state;
	if(state){
		updateSite(state.page);
	}
	});
    
    function login(e)
    {
    	if(e)
		{
		var keynum;
	if(window.event) // IE8 and earlier
		{
		keynum = e.keyCode;
		}
	else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		{
		keynum = e.which;
		}
	
	        if(keynum!=13){
	        	
	        	return;
	        }
	}
	        var userid=document.getElementById("userid").value.trim();
        var pass=document.getElementById("password").value.trim();
        if(userid!='' && pass!=''){
         login_auth(userid,pass,true);
        }else{
        		alert('Username and Password cannot be empty');
        }
    }
    function login_auth(userid,pass,redirect){
    	document.getElementById('light').style.display='block';
   	  document.getElementById('fade').style.display='block';
  		var parameters='username='+userid+'&password='+pass+'&redir='+redirect;
  		$.ajax({
    		  type: 'post',
    		  url: "http://localhost/freniz_zend/public/login",
    		  dataType: 'json',
    		  data: parameters,
    		  success:function(json){ validate(json);}
    		});
    	
    }
    
    function validate(json)
    {
    
    	        if(json.status=='true' && json.redirect=='true')
                {
                    window.location.href='http://localhost/freniz_zend/public/'+json.userid;
                }
                else if(json.status=='false')
                    {
                    document.getElementById("userid").value='';
                    document.getElementById("password").value='';
                    window.location.href='http://localhost/freniz_zend/public/loginattempt?error=1og_fail';
                    }
                else if(json.redirect!='false'){
                	window.location=json.redirect;
                }
                    else
                    window.location='http://localhost/freniz_zend/public/index/secondstep';
        

    }
    
    
    function sendmsguser(userid,element){
		var message;
    	if(element){
    	 message=$('#messag-text').val();
    	}else
    	 message=$('#msg-text textarea').val();
    	var parameters="userid="+userid+"&message="+message;
    	$.ajax({
    		url:'http://localhost/freniz_zend/public/sendmessages',
    		cache:false,
    		data:parameters,
    		type:'post',
    		dataType:"json",
    		success:function(json){
    		$('#mes-text').css('display','none');
    			var span=document.createElement('div');
		    	span.id='vote-user';
				if(json.status=='success')
					span.innerHTML='Hey your message has successfully sent :)';
				else
					span.innerHTML='There is an Error';
				$('#light1').html(span);
				  
				setTimeout(function(){  $('#light1').css({'display':'none'});$('#fade').css({'display':'none'});},5000);
    		}
    	});
    }
    
     
   
      function createb()
       {
    	  var title=escape(document.blogmessage.blg_title.value);
       	var imgurl=escape(document.blogmessage.blg_url.value);
       var text=escape(document.blogmessage.blg.value);
         
          if(text==''){alert("please fill the information");}else{
        		var parameters="title="+title+"&text="+text+"&imageurl="+imgurl;
        		alert('http://localhost/freniz_zend/public/addblog?'+parameters);
            	$.ajax({
            		url:'http://localhost/freniz_zend/public/addblog',
            		cache:false,
            		data:parameters,
            		type:'post',
            		dataType:"json",
            		success:function(json){
            			var span=document.createElement('span');
            			$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
	    		    	span.id='alert-span';
	    				if(json.status=='success')
	    					span.innerHTML='Hey you sucessfully updated your blog :)';
	    				else
	    					span.innerHTML='There is an Error';
	    				$('#blog-container').append(span);
	    				setTimeout(function(){$('#alert-span').remove();},3000);	
	    				var main=document.getElementById('scribbles');
            			  var top=document.createElement('div');
                    	  top.id='user-pic';
                    	  top.className="user-pic"+json.blogid;
              	        top.style.width='600px';
              				top.style.padding='10px';
              				 top.style.cssFloat='left';  
              				top.style.marginTop='10px';
              				top.style.backgroundColor='#eee';
              	          var b=document.createElement('div');
              	             b.className='comment-tab';
              	             b.innerHTML='<div style="width:600px; float:left; "><a id="blog-del'+json.blogid+'" class="remove"data="'+json.blogid+'" title="delete blog" style="float:right; font-size:12px; postion:absolute;  cursor:pointer; margin-right:8px;">x</a><label style="font-size:24px; padding:5px; font-weight:bold">'+json.title+'</label><br/><span style="font-size:14px; margin-left:10px; font-weight:bold; color:#ccc;"><div class="timeago" style="float:left; margin-top:3px;" title="'+json.time+'">'+json.time+'</div></span></div><div style="width:540px; padding:5px; text-align:justify; margin-left:10px; float:left;">'+json.content+'</div>';
              	           if(json.imageurl!=''){
                	             var b1=document.createElement('div');
                	         $(b1).css({'margin-left':'20px','background-color': '#c1d8a9','float':'left','margin-top':'5px'});
                	         b1.innerHTML='<img alt="image"src="http://images.freniz.com/'+json.imageurl+'"width="400">';
                	        b.appendChild(b1);
                	        }
              	         var c=document.createElement('div');
              	              c.style.width='600px';
              	              c.style.cssFloat='left';    
              	           
                            	 c.innerHTML='<a onclick="voteblog(\''+json.blogid+'\',this)"class="vote-bar"href="javascript:void(0)"style="float: right">wink( 0 )</a>';
                            
              	            b.appendChild(c);
              	        
              	        
              	        	top.appendChild(b);
              	        	$(main).prepend(top);
              	        	 $(".remove").toggle(function(){
              		    		var data=$(this).attr('data');
              		    		var position = $("#blog-del"+data).offset();
              		    	$('#remove-items').css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-120)});
              		    	$("#remove-items a:first").attr("onclick","deleteblog('"+data+"')");
              		    	$("#remove-items a:last").attr("onclick","report('"+data+"')");
              		    		
              		    	},function(){
              		    		$('#remove-items').css({'position':'none','display':'none'});
              		    	});
              	        	prettyLinks();
              	        	$('#blog-top').remove();
            		}
            	});
        
         }
       }
      function getblogtemp(userid){
       	 $.ajax({
       		    url:'http://localhost/freniz_zend/public/blog/'+userid+'?format=xml&from='+postsfrom.blogs,
       		    cache:false,
       		    dataType:"xml",
       		    success:function(xml){
					postsfrom.blogs+=limit_default;
       		    	 blogstatus(xml,userid);
       		    }
    		    } );
       	}
      function blogstatus(xml,userid)
	    {
			$('#loadingblogs').remove();
    	  var main=document.getElementById('scribbles');
    	  $(xml).find('blogs').each(function(){
			  //alert($(this).find('blog').length);
              $(this).find('blog').each(function(){
            	  var top=document.createElement('div');
            	  top.className='user-pic'+$(this).find('id').text().trim();
            	  top.id='user-pic';
      	        top.style.width='600px';
      				top.style.padding='10px';
      				 top.style.cssFloat='left';  
      				top.style.marginTop='10px';
      				top.style.backgroundColor='#eee';
      	          var b=document.createElement('div');
      	             b.className='comment-tab';
      	             b.innerHTML='<div style="width:600px; float:left; "><a id="blog-del'+$(this).find('id').text().trim()+'" class="remove"data="'+$(this).find('id').text().trim()+'" title="delete blog" style="float:right; font-size:12px; postion:absolute;  cursor:pointer; margin-right:8px;">x</a><label style="font-size:24px; padding:5px; font-weight:bold">'+$(this).find('title').text()+'</label><br/><span style="font-size:14px; margin-left:10px; font-weight:bold; color:#ccc;"><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></span></div><div style="width:540px; padding:5px; text-align:justify; margin-left:10px; float:left;">'+$(this).find('blog1').text()+'</div>';
      	        if($(this).find('imgurl').text().trim()!=''){
      	             var b1=document.createElement('div');
      	         $(b1).css({'margin-left':'20px','background-color': '#c1d8a9','float':'left','margin-top':'5px'});
      	         b1.innerHTML='<img alt="image"src="http://images.freniz.com/'+$(this).find('imgurl').text().trim()+'"width="400">';
      	        b.appendChild(b1);
      	        }
      	         var c=document.createElement('div');
      	              c.style.width='600px';
      	              c.style.cssFloat='left';    
      	            if($(this).find('votecontains').text()=='no'){
                    	 c.innerHTML='<a onclick="voteblog(\''+$(this).find('id').text().trim()+'\',this)"class="vote-bar"href="javascript:void(0)"style="float: right">wink('+$(this).find('vote_count').text()+')</a>';
                     }else{
                    	 c.innerHTML='<a onclick="unvoteblog(\''+$(this).find('id').text().trim()+'\',this)"class="vote-bar"href="javascript:void(0)"style="float: right">unwink('+$(this).find('vote_count').text()+')</a>';
                         
                     }   
      	            b.appendChild(c);
      	          $(".remove").toggle(function(){
    		    		var data=$(this).attr('data');
    		    		var position = $("#blog-del"+data).offset();
    		    	$('#remove-items').css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-120)});
    		    	$("#remove-items a:first").attr("onclick","deleteblog('"+data+"')");
    		    	$("#remove-items a:last").attr("onclick","report('"+data+"')");
    		    		
    		    	},function(){
    		    		$('#remove-items').css({'position':'none','display':'none'});
    		    	});
      	        
      	        	top.appendChild(b);
      	        	$(main).append(top);
      	        	
      	          if($(this).find('myid').text()!=userid){
      	        	$('.delete').css('display','none');
   				}
      	          
            	
              });
               if($(this).find('loadmore').text()=='yes'){
						var loading=document.createElement('div');
						loading.id='loadingblogs';
						loading.innerHTML='<div style="width:600px; padding:10px; margin-top:20px;text-align:center; background-color:#eee;float:left">loading...</div>';
						$(main).append(loading);
						
						setTimeout(function(){getblogtemp(userid);},5000);
					}
          });
    	 
    	  prettyLinks();
	    }
      
      function prettyLinks(){
    	  $(".timeago").timeago();
    	    }
     	setInterval(prettyLinks, 5000);
    	
      function deleteblog(blogid){
    	  $.ajax({
  		    url:'http://localhost/freniz_zend/public/deleteblog?blogid='+blogid,
  		    cache:false,
  		    dataType:"json",
  		    success:function(json){
  		    	$(".user-pic"+blogid).remove();
  		    	$('#remove-items').css({'position':'none','display':'none'});
  		    	
  		    }
		    } );
      }
      function getadmire(userid)
      {
    	  $.ajax({
    		    url:'http://localhost/freniz_zend/public/admire/'+userid+'?format=xml&from='+postsfrom.admires,
    		    cache:false,
    		    dataType:"xml",
    		    success:function(xml){
					postsfrom.admires+=limit_default;
    		    	 myadmire(xml,userid);
    		    }
 		    } );
             

      }
      function myadmire(xml,userid)
      {
		  $('#loadingadmires').remove();
    	  var main=document.getElementById('admires');
    	      $(xml).find('admires').each(function(){
    	    	  $(this).find('admire').each(function(){
    	    		  var top=document.createElement('div');
    	    		  top.className='user-pic'+$(this).find('id').text().trim();
                	  top.id='user-pic';
          	        top.style.width='700px';
          				top.style.padding='10px';
          				 top.style.cssFloat='left';  
          				top.style.marginTop='10px';
          				top.style.backgroundColor='#eee';
          				
          				 var a=document.createElement('div');
         				a.style.width='700px;';
         				a.style.padding='5px';
         				a.innerHTML='<div id="user-pic" style="float:left;  "><img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'" /></div>';
         				top.appendChild(a);
          	          var b=document.createElement('div');
          	             b.className='comment-tab';
          	             b.innerHTML='<div style="width:600px; float:left; "><a id="admire-del'+$(this).find('id').text().trim()+'" class="remove"data="'+$(this).find('id').text().trim()+'" title="delete admire" style="float:right; font-size:12px; postion:absolute;  cursor:pointer; margin-right:-5px;">x</a><label style="font-size:24px; padding:5px; font-weight:bold"><a href="http://localhost/freniz_zend/public/'+$(this).find('suserid').text()+'">'+$(this).find('susername').text()+'</a></label><br/><span style="font-size:14px; margin-left:10px; font-weight:bold; color:#ccc;"><div class="timeago" style="float:left; margin-top:-5px; margin-left:5px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></span></div><div style="width:540px; padding:5px; text-align:justify; margin-left:10px; float:left;">'+$(this).find('message').text()+'</div><div class="remove-items'+$(this).find('id').text()+'"id="remove-items"style="width:90px; display:none; background-color:#eee;float:left; "><ul><li class="delete-'+$(this).find('id').text()+'"><a href="javascript:void(0)">delete</a></li><li><a href="javascript:void(0)">settings</a></li><li><a href="javascript:void(0)">Report</a></li></ul></div>';
          	         
          	         var c=document.createElement('div');
          	              c.style.width='600px';
          	              c.style.cssFloat='left';    
          	            if($(this).find('votecontains').text()=='no'){
                        	 c.innerHTML='<a onclick="voteadmire(\''+$(this).find('id').text().trim()+'\',this)"class="vote-bar"href="javascript:void(0)"style="float: right">wink( '+$(this).find('vote_count').text()+' )</a>';
                         }else{
                        	 c.innerHTML='<a onclick="unvoteadmire(\''+$(this).find('id').text().trim()+'\',this)"class="vote-bar"href="javascript:void(0)"style="float: right">unwink( '+$(this).find('vote_count').text()+' )</a>';
                             
                         } 
          	         
          	            b.appendChild(c);
          	        
          	        
          	        	top.appendChild(b);
          	        	$(main).append(top); 

          	          if($(this).find('myid').text()!=$(this).find('suserid').text() && $(this).find('myid').text()!=userid){
          	        	  $('.delete-'+$(this).find('id').text()).css('display','none');
         				}
    	    
                 
              });
    	    	  $(".remove").toggle(function(){
        	    		var data=$(this).attr('data');
        	    		
        	    		var position = $("#admire-del"+data).offset();
        	    		
        	    		$('.remove-items'+data).css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-80)});
        	    	$(".remove-items"+data+" a:first").attr("onclick","deleteadmire('"+data+"')");
        	    	$(".remove-items"+data+" a:last").attr("onclick","report('"+data+"')");
        	    	},function(){
        	    		var data=$(this).attr('data');
        	    		$(".remove-items"+data).css({'position':'none','display':'none'});
        	    	});
               if($(this).find('loadmore').text()=='yes'){
						var loading=document.createElement('div');
						loading.id='loadingadmires';
						loading.innerHTML='<div style="width:600px; padding:10px; margin-top:20px;text-align:center; background-color:#eee;float:left">loading...</div>';
						$(main).append(loading);
						
						setTimeout(function(){getadmire(userid);},5000);
					}
    	      });
	        	
    	      prettyLinks();
         
      }

      function createadmirestatus(userid)
 	  {
	 	   var text=escape(document.admiremess.admr.value);
 	      if(text==''){alert("please fill the information");}else{
 	       var parameters="text="+text+"&userid="+userid;
 	      $.ajax({
      		url:'http://localhost/freniz_zend/public/addadmiration',
      		cache:false,
      		data:parameters,
      		type:'post',
      		dataType:"json",
      		success:function(json){
      			var span=document.createElement('div');
  		    	span.id='admire-user';
  				if(json.status=='success')
  					span.innerHTML='Hey you have admired this user :)';
  				else
  					span.innerHTML='There is an Error';
  				$('#light').html(span);
  				  
  				setTimeout(function(){  $('#light').css({'display':'none'});$('#fade').css({'display':'none'});},3000);
      			
      		}
      	});
 	      }
 	  }
 	  function admirestatus()
 	  {
	 	   if(request.readyState==4 && request.status==200)
 	          {
 	              var json=eval('('+request.responseText+')');
 	              alert(json.status);
 	              var e=document.getElementById("light");
 	              e.style.width=500+'px';
 	              e.style.height=250+"px";
 	              e.innerHTML=json.status+'<ul class="roundbuttons sendmessagewidth"><li><input type="button" name="cancel" value="cancel" onClick="document.getElementById("light").style.display="none";   document.getElementById("fade").style.display="none";  /></li></ul>';
 	              
 	          }
 	  }
 	   function deleteadmire(admireid){
     	  $.ajax({
   		    url:'http://localhost/freniz_zend/public/deleteadmire?admireid='+admireid,
   		    cache:false,
   		    dataType:"json",
   		    success:function(json){
   		    	$(".user-pic"+admireid).remove();
   		 	$('#remove-items').css({'position':'none','display':'none'});
   		    }
 		    } );
       }
	 	
		 	function getalbums(userid)
		 	{
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/'+userid+'/albums?format=xml',
		    		    cache:false,
		    		    dataType:"xml",
		    		    success:function(xml){
		    		    	 getalbum(xml);
		    		    }
		 		    } );
		 		
		 	  
		 	}
		 	function getalbum(xml)
		 	{
		 		     var e=document.getElementById('userstream');
		 	            e.innerHTML='';
		 	           $(xml).find('albums').each(function(){
			    	    	  $(this).find('album').each(function(){
		 	                    var album=document.createElement('div');
		 	                      album.innerHTML='<a style="font-weight:bold; color:#444" href="http://localhost/freniz_zend/public/getimages?albumid='+$(this).find('id').text()+'"><div style="width: 320px; margin: 20px; border: solid 1px; float: left"><div style="width: 100px; height: 100px; margin: 10px; border: solid 1px; float: left"><img src="http://images.freniz.com/75/75_'+$(this).find('coverurl').text()+'" height="100" width="100"/></div><div style="width: 190px; margin-top: 20px; height: 20px;  float: left">'+$(this).find('name').text()+'</div><div style="width: 190px; margin-top: 5px; height: 20px; font-size:12px; float: left" class="timeago" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></a>';
		 	                   $(e).append(album);
		 	              
			    	    	  });
		 	           });
		 	          prettyLinks();
		 	}

		 	
		 	
		 	
		 	function getvideos(userid)
		 	{
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/videos/'+userid+'?format=xml',
		    		    cache:false,
		    		    dataType:"xml",
		    		    success:function(xml){
		    		    	 getvideo(xml);
		    		    	 
		    		    }
		 		    } );
		 		
		 	  
		 	}
		 	function getvideo(xml){
		 	    var main=document.getElementById("video-div");
	            $(xml).find('videos').each(function(){
	    	    	  $(this).find('video').each(function(){
	    	    		  var e=document.createElement('div');
	            e.innerHTML='<a style="color:#000;" href="http://localhost/freniz_zend/public/getvideos?videoid='+$(this).find('videoid').text()+'"><div style="float:left; width:250px;margin:20px;"><div style="width: 250px; height: 200px; border: solid 1px;  float: left">'+$(this).find('embeddcode').text()+'<div style="position:absolute;margin-top:-200px; height:200px; width:250px"></div></div><div class="timeago" style="float:left; padding:3px; color:#444; margin-left:5px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></a>';
	            $(main).append(e);
	    	    	  });
	            });
	            $("iframe").attr({ 
	            	  width: "250",
	            	  height: "200"
	            	});
	            prettyLinks();
	                }
		 	
		 	function getsinglevideo(videoid)
		 	{
		 			 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/getvideos?videoid='+videoid+'&format=xml',
		    		    cache:false,
		    		    dataType:"xml",
		    		    success:function(xml){
		    		    	 getvideosingle(xml,videoid);
		    		    }
		 		    } );
		 		
		 	  
		 	}
		 	
		
		 	function getvideosingle(xml,videoid){
		 		 var top=document.getElementById("video-div");
		 		  
		            $(xml).find('videos').each(function(){
						clearTimeout(loadCommentsTimeout);
						if($(this).find('maxcomment').text()>comment_max)
						comment_max=$(this).find('maxcomment').text();
		              $(this).find('video').each(function(){
		    	    		  var main=document.createElement('div');
		    	    		  main.id='video-box';
		    	    		  main.innerHTML='<label style="font-size:32px; border-bottom:solid 1px; font-weight:bold">'+$(this).find('susername').text()+'\'s Video</label>';
		    	    		  var e=document.createElement('div');
		    	    		  e.innerHTML='<div style="width: 400px; margin-top: 10px; margin-left: 60px; height: 30px; font-size:24px; font-weight:bold;">'+$(this).find('title').text()+'</div><a class="close" style="float:right; cursor:pointer; margin-right:20px;margin-top:-30px;" onclick="deletevideo('+$(this).find('videoid').text()+')">x</a><div style="width: 600px; height: 300px; border: solid 1px; margin-left: 60px; margin-top: 5px; float: left">'+$(this).find('embeddcode').text()+'</div>';
		    		            $(main).append(e);
		            var a=document.createElement('div');
		            a.id='vote-div';
		            if($(this).find('votecontains').text()=='no'){
	                	 a.innerHTML='<div style="width:400px; height: 20px; margin-top:2px;  float: left"class="timeago" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div><a onclick="votevideo('+$(this).find('videoid').text()+',this)" class="vote-bar"style="float: right">wink('+$(this).find('vote_count').text()+')</a>';
	                 }else{
	                	 a.innerHTML='<div style="width:400px; height: 20px; margin-top:2px;  float: left"class="timeago" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div><a onclick="unvotevideo('+$(this).find('videoid').text()+',this)" class="vote-bar"style="float: right">winked('+$(this).find('vote_count').text()+')</a>';
	                 }
		            $(main).append(a);
		            var b=document.createElement('div');
		            b.id='comment-main-div';
		            var c=document.createElement('div');
						c.id='comment-sub-div';
					comments.videos[videoid]={};
				    comments.videos[videoid]['totalcomments']=$(this).find('commentcount').text();
				      
					
					
		            $(this).find('comments').each(function(){
		            	$(this).find('comment').each(function(){
		            	  var d=document.createElement('div');
		            	  d.innerHTML='<div class="commentbox'+$(this).find('commentid').text()+'" style="width: 560px; float: left; border-bottom: solid 1px #fff"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('commentpic_url').text()+'"/></div><div style="width: 520px;  margin-left: 5px; margin-top: 5px; float: left; "><label>'+$(this).find('comment_name').text()+':</label>'+$(this).find('comment-comment').text()+'</div><div style="height:10px; margin-left: 5px; font-size:10px; float: left" class="timeago" title="'+$(this).find('comment_date').text()+'">'+$(this).find('comment_date').text()+'</div><a class="close" style="float:right; cursor:pointer; margin-top:-20px;" onclick="deletevideocomment('+$(this).find('commentid').text()+')">x</a></div>';
				             $(c).append(d);
		              
		            	});
		            	 comments.videos[videoid]['commentsdisplayed']=defaultcomments;
				            	if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:videoid+'-loadcomments'}).html('<a href="javascript:void(0)" onclick="loadvideocomments(\''+videoid+'\')">load previous</a>').prependTo(c);
								}
				            	else
				            	$('#'+videoid+'-loadcomments').remove();
		            });
		            $(b).append(c);
		            var f=document.createElement('div');
		            f.innerHTML='<div id="comment-feild"style="width: 560px;height: 25px;padding:5px; background-color:#ccc; float: left;"><input id="text-comment-'+$(this).find('videoid').text()+'" onkeydown="docommentvideo('+$(this).find('videoid').text()+',event)" type="text"placeholder="write to comment"style="width: 560px; height: 25px;"/></div>';
		            $(b).append(f);
		            $(main).append(b);
		            top.innerHTML=main.innerHTML;
		    	    	  });
		            });
		            $("iframe").attr({ 
		            	  width: "600",
		            	  height: "300"
		            	});
		            prettyLinks();
		 	}
			function loadvideocomments(videoid){
				//alert("http://localhost/freniz_zend/public/scribbles/getcomments?scribbleid="+scribbleid+"&from="+comments.scribbles[scribbleid]['commentsdisplayed']);
				$.ajax({
					url:"http://localhost/freniz_zend/public/videos/getcomments/videoid/"+videoid+"?from="+comments.videos[videoid]['commentsdisplayed'],
					dataType:"xml",
					cache:false,
					success:function(xml){
							$(xml).find('comments').each(function(){
								var i=document.getElementById('comment-sub-div');
								$(this).find('comment').each(function(){
									// video comment design
									 var d=document.createElement('div');
					            	  d.innerHTML='<div id="commentbox'+$(this).find('id').text()+'" style="width: 560px; float: left; border-bottom: solid 1px #fff"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('imageurl').text()+'"/></div><div style="width: 520px;  margin-left: 5px; margin-top: 5px; float: left; "><label>'+$(this).find('username').text()+':</label>'+$(this).find('comment-comment').text()+'</div><div style="height:10px; margin-left: 5px; font-size:10px; float: left" class="timeago" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div><a class="close" style="float:right; cursor:pointer; margin-top:-20px;" onclick="deletevideocomment('+$(this).find('id').text()+')">x</a></div>';
							             $(i).append(d);
								});
								prettyLinks();
								comments.videos[videoid]['commentsdisplayed']+=commentlimit;
								if(comments.video[videoid]['totalcomments']<comments.videos[videoid]['commentsdisplayed']){
									comments.videos[videoid]['commentsdisplayed']=comments.videos[videoid]['totalcomments'];
									$('#'+videoid+'-loadcomments').remove();
								}
							});
						}
					});
			}
		 	 function doimagecomment(imageid,e)
	            {	
		 		var keynum;
		 		if(e){
		 		if(window.event) // IE8 and earlier
		 			{
		 			keynum = e.keyCode;
		 			}
		 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		 			{
		 			keynum = e.which;
		 			}
		 			}
		 		        if(keynum==13 || !e){
		 		        	var text=$('#'+imageid+'_comment').val();
		 		        		//document.getElementById(imageid+'-comment').value;
		 		        	if(text!=''){
		 		        	  var parameters="text="+text;
		 		        	 $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/image/comment/imageid/'+imageid,
		 	              	  data: parameters,
		 	              	  success: function(json){
		 	              		 
		 	              	  },
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById(imageid+'_comment').value=''; 
		 		        	}
		 		    }
	               
	               
	            }
		 	
		 	 function docommentvideo(videoid,e)
	            {	
		 		var keynum;
		 		if(e){
		 		if(window.event) // IE8 and earlier
		 			{
		 			keynum = e.keyCode;
		 			}
		 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		 			{
		 			keynum = e.which;
		 			}
		 			}
		 		        if(keynum==13 || !e){
		 		        	var text=document.getElementById('text-comment-'+videoid).value;
		 		        	if(text!=''){
		 		        	  var parameters="videoid="+videoid+"&text="+text;
		 		           $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/docommentvideo',
		 	              	  data: parameters,
		 	              	  success: function(json){alert(json.status);},
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById('text-comment-'+videoid).value=''; 
		 		        	}
		 		    }
	               
	               
	            }
		 	 
		 	function votevideo(videoid,element){
	 			 $.ajax({
	    		    url:'http://localhost/freniz_zend/public/votevideo?videoid='+videoid,
	    		    cache:false,
	    		    dataType:"json",
	    		    success:function(json){
	    		    	var span=document.createElement('div');
	    		    	$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
	    		    	span.id='alert-span';
	    				if(json.status=='success')
	    					span.innerHTML='Hey you sucessfully winked this video :)';
	    				else
	    					span.innerHTML='There is an Error';
	    				 var innerHtml=$(element).html();
    		    		 var pos1=innerHtml.indexOf('(')+2;
    		    		 var pos2=innerHtml.indexOf(')')-1;
    		    		 var length=pos2-pos1;
    		    		 var votecount=1+parseInt(innerHtml.substr(pos1,length));
    		    		 $(element).html('winked( '+votecount+' )');
    		    		 $(element).attr('onclick',"unvotevideo("+userid+",'this')");
	    				$('#maincontainer').append(span);
	    				setTimeout(function(){$('#alert-span').remove();},3000);
	    		    }
	 		    } );
		 		
		 	 }
		 	function unvotevideo(videoid,element){

	 			 $.ajax({
	    		    url:'http://localhost/freniz_zend/public/unvotevideo?videoid='+videoid,
	    		    cache:false,
	    		    dataType:"json",
	    		    success:function(json){
	    		    	if(json.status=='success'){
	    		    		 var innerHtml=$(element).html();
	    		    		 var pos1=innerHtml.indexOf('(')+2;
	    		    		 var pos2=innerHtml.indexOf(')')-1;
	    		    		 var length=pos2-pos1;
	    		    		 var votecount=parseInt(innerHtml.substr(pos1,length))-1;
	    		    		 $(element).parent().html('<a onclick="votevideo('+videoid+',this)"style="float:right; font-weight:bold; text-decoration:none;color:#000; "href="javascript:void(0)">wink( '+votecount+' )</a>');
	    		    	 }
	    			
	    		    }
	 		    } );
		 		
		 	 }
		 	function getimages(albumid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/getimages?albumid='+albumid+'&format=xml&from='+postsfrom.images,
		    		    cache:false,
		    		    dataType:"xml",
		    		    success:function(xml){
							postsfrom.images+=limit_default;
		    		    	 getimage(xml,albumid);
		    		    }
		 		    } );
		 	}
		 	function getimage(xml,albumid){
				$('#loadingimages').remove();
		 		 var main=document.getElementById("image-div");
		 		     $(xml).find('images').each(function(){
		 		    	 $(this).find('album').each(function(){
		 		    		 if($(this).find('show-uploader').text()=='yes'){
		 		    			 if($(this).find('albumname').text()=='Profilepics' || $(this).find('albumname').text()=='SecondaryProfilepics'){
		 		    				 createUploader(albumid,false,false);
		 		    			 }
		 		    			 else
		 		    				createUploader(albumid,true,true);
		 		    		 }
		 		    	 });
		    	    	  $(this).find('image').each(function(){
		    	    		  var e=document.createElement('div');
		    	    		  
		            e.innerHTML='<a href="http://localhost/freniz_zend/public/image/'+$(this).find('albumid').text()+'#'+$(this).find('id').text()+'"><div style=" margin: 10px;  float: left"><div style=" margin: 5px; background-color:#ccc; float: left"><img src="http://images.freniz.com/200/200_'+$(this).find('url').text()+'"/></div></div></a>';
		            $(main).append(e);
		    	    	  });
		    	    	  if($(this).find('loadmore').text()=='yes'){
							var loading=document.createElement('div');
							loading.id='loadingimages';
							loading.innerHTML='<div style="width:600px; padding:10px; margin-top:0px; margin-left:30px;text-align:center; background-color:#ccc;float:left">loading...</div>';
							$(main).append(loading);
							
							setTimeout(function(){getimages(albumid);},5000);
						}
		            });
		 	}
		 	function deleteimagecomment(commentid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/image/deleteimagecomment/commentid/'+commentid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$("#comment-box"+commentid).remove();
		    		    }
		 		    } );
		 		 
		 	 }
		 	function displayimage(imageid)
		 	{
				comments.images={};
				comments.images[imageid]={commentsdisplayed:0,totalcomments:0};
				$.ajax({
					url:"http://localhost/freniz_zend/public/image/getpinpeople/imageid/"+imageid,
					dataType:"xml",
					cache:false,
					success:function(xml){
						var a=document.createElement('div');
						$(xml).find('users').each(function(){
							$(this).find('user').each(function(){
								 var e=document.createElement('div');
          						e.className='usersname';
          	                    $(e).attr('name',$(this).find('id').text());
          	                    e.innerHTML='<a href="'+$(this).find('url').text()+'">'+$(this).find('username').text()+'</a><a style="width:10px; height:10px; text-decoration:none; background-color:red; font-size:14px; margin-bottom:20px" onclick="$(this).parent().remove();unpin('+imageid+',\''+$(this).find('id').text()+'\')" href="javascript:void(0)">x</a>';
          						$(a).append(e);
								});
							});
						$('#'+imageid+'_pinnedpeople_list').html(a);
							
					}
					});
			}
		 	function getImageComments(imageid){
				$.ajax({
				url:"http://localhost/freniz_zend/public/image/getcomments/imageid/"+imageid+"?from="+comments.images[imageid]['commentsdisplayed'],
				dataType:"xml",
				cache:false,
				success:function(xml){
					clearTimeout(loadCommentsTimeout);
					$(xml).find('comments').each(function(){
							if(comments.images[imageid]['totalcomments']==0){
								comments.images[imageid]['totalcomments']=$(this).find('totalcomments').text();
								$('#'+imageid+'_com').html('');
							}
							 if(comment_max==0)
					  comment_max=$(this).find('maxcomment').text();
							//alert(imageid);
						$(this).find('comment').each(function(){
							var d=document.createElement('div');
										d.id='comment-box'+$(this).find('id').text();
										d.innerHTML='<div style="width: 500px; margin-top:5px; padding-bottom:5px; float: left; border-bottom: solid 1px #fff"><div style="width: 32px; height: 32px; float: left;"><img height="32" width="32" src="http://images.freniz.com/32/32_'+$(this).find('imageurl').text()+'"/></div><div style="width: 460px;  margin-left: 5px; margin-top: 5px; float: left; "><label style="padding-right:2px; font-weight:bold;">'+$(this).find('username').text()+':</label>'+$(this).find('comment-comment').text()+'</div><a href="javascript:void(0)" onclick="deleteimagecomment('+$(this).find('id').text()+')" style="float:right; margin-top:-20px;">x</a><div class="timeago" style="height:10px; margin-left: 5px; font-size:10px; float: left"title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div>';
										$('#'+imageid+'_com').append(d);				            
										
						});
						if(comments.images[imageid]['commentsdisplayed']==0)
							comments.images[imageid]['commentsdisplayed']=3;
						else
							comments.images[imageid]['commentsdisplayed']+=commentlimit;
						if(comments.images[imageid]['totalcomments']<comments.images[imageid]['comments_displayed']){
									comments.images[imageid]['commentsdisplayed']=comments.images[imageid]['totalcomments'];
									$('#'+imageid+'-loadcomments').remove();
								}
								else
								{
									if(!document.getElementById(imageid+'-loadcomments')){
										var div=document.createElement('div');
										div.id=imageid+"-loadcomments";
										div.innerHTML='<a href="javascript:void(0)" onClick="getImageComments(\''+imageid+'\')">view more comments</a>';
										$('#'+imageid+'_com').prepend(div);
									}
								}
					}); 
					 prettyLinks();
					loadCommentsTimeout=setTimeout(function(){loadcomments();},2000);
				}
				});
				 
			}
		 	
		 	function getscribbles(userid){
				 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/streams/mystreams?userid='+userid+'&format=xml&from='+postsfrom.scribbles,
		    		    cache:false,
		    		    dataType:"xml",
		    		    success:function(xml){
							postsfrom.scribbles+=limit_default;
		    		    	scribbles(xml,userid);
		    		    }
		 		    } );
		 	}
		 	 function scribbles(xml,userid)
			    {
		 		clearTimeout(loadCommentsTimeout);
					$('#loadingscribbles').remove();
		 		  var main=document.getElementById('scribbles');
		 		  $(xml).find('streams').each(function(){
		    		  if($(this).find('maxcomment').text()>comment_max)
						  comment_max=$(this).find('maxcomment').text();
		              $(this).find('stream').each(function(){
						  switch($(this).find('type').text()){
							  case 'post':
						  var postid=$(this).find('id').text();
		            	  var top=document.createElement('div');
		            	  top.style.width='700px';
		            	  top.id="scribbles-"+$(this).find('id').text();
		            	 var pic=document.createElement('div');
		             	 pic.className='user-pic'+$(this).find('id').text();
		            	 pic.style.width='75px';
		            	 pic.style.height='75px';
		            	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpropic').text()+'"/>';
		            	  pic.style.cssFloat='left';
		            	   $(top).append(pic);
		            	  var a=document.createElement('div');
		            	   a.id='stature-stream';
		            	   a.className='stature-stream'+$(this).find('id').text();
		                   a.style.width='600px';
		                   a.style.cssFloat='left';
		                   a.style.borderBottom='solid 1px #ccc';
		                   a.style.padding='5px';
		                      a.style.margin='0 0 5px 7px';
		                 	var c=document.createElement('div');
		                 	c.id='main-scrible-div';
		                 	c.style.width='600px';
		                 	c.style.float='left';
		                 	c.innerHTML='<div style="width:580px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('id').text()+'" class="close"data="'+$(this).find('id').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 24px; font-weight: bold; height: 25px; "><a class="hover" data="'+$(this).find('suserid').text()+'" href="http://localhost/freniz_zend/public/'+$(this).find('suserid').text()+'">'+$(this).find('susername').text()+'</a><span style="margin-left:5px;font-size:14px; color:#ccc">'+$(this).find('pt').text()+'</span></div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div><div style="width:460px; margin-left: 20px; float: left; margin-top: 5px; padding:5px ">'+$(this).find('status').text()+'</div>';
		                  var b=document.createElement('div');
		                 b.style.width='580px';
		                 b.style.padding='5px';
		                 b.style.float='left';
		                 if($(this).find('vote-contains').text().trim()=='no'){
		                	 b.innerHTML='<a onclick="votescribbles('+$(this).find('id').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
		                  }else{
		                	  b.innerHTML='<a onclick="unvotescribbles('+$(this).find('id').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote').text()+' )</a>';
		                  }
		                 $(c).append(b);
		                 comments.scribbles[postid]={};
				          comments.scribbles[postid]['totalcomments']=$(this).find('commentcounts').text();
				      
		                 var g=document.createElement('div');
				            g.id='stature-comment-main-div'+$(this).find('id').text();
				            var i=document.createElement('div');
				            i.id='stature-comment-sub-div'+$(this).find('id').text();
				          
				            $(this).find('comments').each(function(){
				            	$(this).find('comment').each(function(){
				            		var d=document.createElement('div');
				            		 d.id='stature-comment-box'+$(this).find('comment-id').text();
				            d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-propic').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;"class="hover" data="'+$(this).find('comment-userid').text()+'" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
				              $(i).append(d);
				            
				              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
				            	  $('#stature-comment-del'+$(this).find('comment-id').text()).css('display','none');
		         				}
				            	});
				            	comments.scribbles[postid]['commentsdisplayed']=defaultcomments;
				            	if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:postid+'-loadcomments'}).html('<a href="javascript:void(0)" onclick="loadscribblecomments(\''+postid+'\')">load previous</a>').appendTo(g);
								}
				            	else
				            	$('#'+postid+'-loadcomments').remove();
				         
				            	
				            });
				            $(g).append(i);
				            if($(this).find('iscommentable').text()=='yes'){
				            var f=document.createElement('div');
				            f.innerHTML='<div id="comment-feild"style="width: 470px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('id').text()+'" onkeydown="postcommentscribles('+$(this).find('id').text()+',event)" type="text"placeholder="write to post"style="width: 430px; height: 20px;"/></div></div>';
				            $(g).append(f);
				            }
				            $(c).append(g);
		                 
		                  $(a).append(c);
		                  $(top).append(a);
		                 $(main).append(top);
		                 break;
		                 
		                 
		                 
		                 case 'video':
		         var videoid=$(this).find('id').text();
				 var top=document.createElement('div');
				  $(top).css({'width':'700px','float':'left'});
		           var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('id').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpropic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
				var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='video-stream'+$(this).find('id').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('id').text()+'" class="close"data="'+$(this).find('id').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  "><a href="http://localhost/freniz_zend/public/'+$(this).find('suserurl').text()+'">'+$(this).find('susername').text()+'</a></div><div style="width:500px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
	                	 var b1=document.createElement('div');
		      	         $(b1).css({'margin-left':'20px','float':'left','margin-top':'15px'});
		      	         b1.innerHTML=$(this).find('url').text();
		      	        c.appendChild(b1);
	                 var b=document.createElement('div');
	                b.style.width='580px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="votevideo('+$(this).find('id').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvotevideo('+$(this).find('id').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                var g=document.createElement('div');
		            g.id='video-comment-main-div'+$(this).find('id').text();
		            var i=document.createElement('div');
		            i.id='video-comment-sub-div'+$(this).find('id').text();
		          comments.videos[videoid]={};
					          comments.videos[videoid]['totalcomments']=$(this).find('commentcounts').text();
				    $(this).find('comments').each(function(){
		            	$(this).find('comment').each(function(){
		            		var d=document.createElement('div');
		            		 d.id='video-comment-box'+$(this).find('comment-id').text();
		            d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-propic').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
		              $(i).append(d);
		            
		              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
		            	  $('#video-comment-del'+$(this).find('comment-id').text()).css('display','none');
        				}
		            	});
		            	comments.videos[videoid]['commentsdisplayed']=defaultcomments;
			              		if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:videoid+'-loadcomments-video'}).css({'width':'500px','height':'10px','float':'left'}).html('<a href="javascript:void(0)" onclick="loadvideocomments(\''+videoid+'\')">load previous</a>').appendTo(g);
								}
				            	else{
									
				            	$('#'+videoid+'-loadcomments-video').remove();
							}
				   
		            });
		            $(g).append(i);
		            if($(this).find('iscommentable').text()=='yes'){
		            var f=document.createElement('div');
		            f.innerHTML='<div id="comment-feild"style="width: 570px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('id').text()+'" onkeydown="docommentvideo('+$(this).find('id').text()+',event)" type="text"placeholder="write to post"style="width: 430px; height: 20px;"/></div></div>';
		            $(g).append(f);
		            }
		            $(c).append(g);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);
	                 $("iframe").attr({ 
		            	  width: "500",
		            	  height: "300"
		            	});
				break;
				
				case 'image':
				
				var top=document.createElement('div');
	           	  $(top).css({'width':'700px','float':'left','display':contentdisplay});
		          var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('id').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpropic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='image-stream'+$(this).find('id').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('id').text()+'" class="close"data="'+$(this).find('id').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	  var b1=document.createElement('div');
	      	         $(b1).css({'margin-left':'20px','background-color': '#c1d8a9','float':'left','margin-top':'5px'});
	      	         b1.innerHTML='<a href="http://localhost/freniz_zend/public/image/'+$(this).find('albumid').text()+'#'+$(this).find('id').text()+'"><img alt="image"src="http://images.freniz.com/500/500_'+$(this).find('imageurl').text().trim()+'"></a>';
	      	        c.appendChild(b1);
	      	      	
	                 var b=document.createElement('div');
	                b.style.width='580px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="voteimage('+$(this).find('id').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvoteimage('+$(this).find('id').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                var g=document.createElement('div');
		            g.id='image-comment-main-div'+$(this).find('id').text();
		            var i=document.createElement('div');
		            i.id='image-comment-sub-div'+$(this).find('id').text();
		          
		            $(this).find('comments').each(function(){
		            	$(this).find('comment').each(function(){
		            		var d=document.createElement('div');
		            		 d.id='image-comment-box'+$(this).find('comment-id').text();
		            d.innerHTML='<div style="width: 580px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-userpic').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
		              $(i).append(d);
		            
		              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
		            	  $('#image-comment-del'+$(this).find('comment-id').text()).css('display','none');
        				}
		            	});
		              	
		            });
		            $(g).append(i);
		            if($(this).find('iscommentable').text()=='yes'){
		            var f=document.createElement('div');
		            f.innerHTML='<div id="comment-feild"style="width: 570px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('id').text()+'" onkeydown="postcommentscribles('+$(this).find('id').text()+',event)" type="text"placeholder="write to post"style="width: 430px; height: 20px;"/></div></div>';
		            $(g).append(f);
		            }
		            $(c).append(g);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);  
				break;
				
				case 'propic':
				var top=document.createElement('div');
	           	  $(top).css({'width':'700px','float':'left','min-height':'100px'});
	           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('userpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('username').text()+' has changed profile picture</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	   $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);  
				break;
				
				case 'mood':
					var top=document.createElement('div');
	           	$(top).css({'width':'700px','float':'left','min-height':'100px'});
	           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('userpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('username').text()+' has updated the mood</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	   $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);  
				break;
				case 'basicinfo':
					var top=document.createElement('div');
	           	 $(top).css({'width':'700px','float':'left','min-height':'100px'});
	           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('userpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('username').text()+' has updated the basic info</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	   $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);  
				break;
				case 'personalinfo':
					var top=document.createElement('div');
	           	  $(top).css({'width':'700px','float':'left','min-height':'100px'});
	           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('userpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('username').text()+' has updated the personal info</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	   $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);  
				break;
				case 'city':
					var top=document.createElement('div');
	           	  $(top).css({'width':'700px','float':'left','min-height':'100px'});
	           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('userpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='600px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='600px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:520px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('username').text()+' has updated the city</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	   $(a).append(c);
	                  $(top).append(a);
	                 $(main).append(top);  
				break;
				
		             }
		             
		                 
		              });
		              $(".close").toggle(function(){
		      	    		var data=$(this).attr('data');
		      	    		var position = $("#remove-"+data).offset();
		      	    	$('#remove-items').css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-120)});
		      	    	$("#remove-items a:first").attr("onclick","deletescribbles('"+data+"')");
		      	    	$("#remove-items a:last").attr("onclick","report('"+data+"')");
		      	    		
		      	    	},function(){
		      	    		$('#remove-items').css({'position':'none','display':'none'});
		      	    	});
			    	  prettyLinks();
		              if($(this).find('loadmore').text()=='yes'){
						var loading=document.createElement('div');
						loading.id='loadingscribbles';
						loading.innerHTML='<div style="width:600px; padding:10px; margin-top:0px; margin-left:30px;text-align:center; background-color:#ccc;float:left">loading...</div>';
						$(main).append(loading);
						
						setTimeout(function(){getscribbles(userid);},5000);
					}
		          });
		    	  loadCommentsTimeout=setTimeout(function(){loadcomments();},2000);
			    }
		 	 function loadscribblecomments(scribbleid){
					//alert("http://localhost/freniz_zend/public/scribbles/getcomments?scribbleid="+scribbleid+"&from="+comments.scribbles[scribbleid]['commentsdisplayed']);
					$.ajax({
						url:"http://localhost/freniz_zend/public/scribbles/getcomments?scribbleid="+scribbleid+"&from="+comments.scribbles[scribbleid]['commentsdisplayed'],
						dataType:"xml",
						cache:false,
						success:function(xml){
								$(xml).find('comments').each(function(){
									var scribbleid=$(this).find('scribbleid').text();
									var i=document.getElementById('stature-comment-sub-div'+$(this).find('scribbleid').text());
									$(this).find('comment').each(function(){
										var d=document.createElement('div');
										d.id='comment-box';
										d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('imageurl').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('user_url').text()+'">'+$(this).find('username').text()+':</a>'+$(this).find('comment-comment').text()+'</div><a id="stature-comment-del'+$(this).find('id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
										$(i).prepend(d);				            
										/*if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('commentid').text()==$(this).parent().parent().find('myid').text()){
											$('#stature-comment-del'+$(this).find('commentid').text()).css('display','none');
										}*/
									});
									comments.scribbles[scribbleid]['commentsdisplayed']+=commentlimit;
									if(comments.scribbles[scribbleid]['totalcomments']<comments.scribbles[scribbleid]['commentsdisplayed']){
										comments.scribbles[scribbleid]['commentsdisplayed']=comments.scribbles[scribbleid]['totalcomments'];
										$('#'+scribbleid+'-loadcomments').remove();
									}
								});
								 prettyLinks();
							}
						});
				}
		 	function loadcomments(){
				var url="http://localhost/freniz_zend/public/comment/getcomments?maxcomment="+comment_max;
				var statures=Array();
				for(var i in comments.statures)
				statures.push(i);
				var scribbles=Array();
				for(var i in comments.scribbles)
				scribbles.push(i);
				var images=Array();
				for(var i in comments.images)
				images.push(i);
				var videos=Array();
				for(var i in comments.videos)
				videos.push(i);
				url+='&statures='+statures+'&scribbles='+scribbles+'&images='+images+'&videos='+videos;
				$.ajax({
					url:url,
					dataType:"xml",
					cache:false,
					success:function(xml){
						clearTimeout(loadCommentsTimeout);
						$(xml).find('comments').each(function(){
							if($(this).find('maxcomment').text()>comment_max)
							comment_max=$(this).find('maxcomment').text();
							$(this).find('comment').each(function(){
								switch($(this).find('type').text()){
									case 'stature':
									var i=document.getElementById('stature-comment-sub-div'+$(this).find('objectid').text());
									var d=document.createElement('div');
									d.id='stature-comment-box'+$(this).find('id').text();
									d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('imageurl').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('userid').text()+'">'+$(this).find('username').text()+':</a>'+$(this).find('comment-comment').text()+'</div><a id="stature-comment-del'+$(this).find('id').text()+'" href="javascript:void(0)" onclick="deletestaturecomment('+$(this).find('id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
									$(i).append(d);
									comments.statures[$(this).find('objectid').text()]['totalcomments']+=1;				            
									comments.statures[$(this).find('objectid').text()]['commentsdisplayed']+=1;				            
									break;
									case 'scribble':
									var d=document.createElement('div');
									d.id='comment-box';
									d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('imageurl').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('user_url').text()+'">'+$(this).find('username').text()+':</a>'+$(this).find('comment-comment').text()+'</div><a id="stature-comment-del'+$(this).find('id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
									$('#stature-comment-sub-div'+$(this).find('objectid').text()).append(d);
									comments.scribbles[$(this).find('objectid').text()]['totalcomments']+=1;				            
									comments.scribbles[$(this).find('objectid').text()]['commentsdisplayed']+=1;				            
									break;
									case 'image':
										var d=document.createElement('div');
										d.id='comment-box'+$(this).find('id').text();
										d.innerHTML='<div style="width: 500px; margin-top:5px; padding-bottom:5px; float: left; border-bottom: solid 1px #fff"><div style="width: 32px; height: 32px; float: left;"><img height="32" width="32" src="http://images.freniz.com/32/32_'+$(this).find('user_image').text()+'"/></div><div style="width: 460px;  margin-left: 5px; margin-top: 5px; float: left; "><label style="padding-right:2px; font-weight:bold;">'+$(this).find('username').text()+':</label>'+$(this).find('comment-comment').text()+'</div><a href="javascript:void(0)" onclick="deleteimagecomment('+$(this).find('id').text()+')" style="float:right; margin-top:-20px;">x</a><div class="timeago" style="height:10px; margin-left: 5px; font-size:10px; float: left"title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div>';
										$('#'+$(this).find('objectid').text()+'-loadcomments').after(d);
										comments.images[$(this).find('objectid').text()]['totalcomments']+=1;				            
										comments.images[$(this).find('objectid').text()]['commentsdisplayed']+=1;				            
										break;
								}
								});
							
							});
						 prettyLinks();
							loadCommentsTimeout=setTimeout(function(){loadcomments();},2000);
					}
					
					});
			}
		    
		 	
		 	 function postcommentscribles(postid,e)
	            {	
		 		var keynum;
		 		if(e){
		 		if(window.event) // IE8 and earlier
		 			{
		 			keynum = e.keyCode;
		 			}
		 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		 			{
		 			keynum = e.which;
		 			}
		 			}
		 		        if(keynum==13 || !e){
		 		        	var text=document.getElementById("text-comment-"+postid).value;
		 		        	if(text!=''){
		 		        	  var parameters="postid="+postid+"&text="+text;
		 		        	
		 		        	   $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/docommentscribbles',
		 	              	  data: parameters,
		 	              	  success: function(json){alert(json.status);},
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById('text-comment-'+postid).value=''; 
		 		        	}
		 		    }
	               
	               
	            }
		 	 function deletescribbles(postid){
		 		 $.ajax({
	    		    url:'http://localhost/freniz_zend/public/deletescribbles?postid='+postid,
	    		    cache:false,
	    		    dataType:"json",
	    		    success:function(json){
	    		  if(json.status=='success'){
	    		    	$("#scribbles-"+postid).remove();
	    		    	$("#remove-items").css({'display':'none'});
	    		  	}	 else
	    	   			    alert(json.status);
	    		    }
	 		    } );
		 	 }
		 	 function deletescribblescomment(commentid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/deletescribblescomment?commentid='+commentid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$("#stature-comment-box"+commentid).remove();
		    		    }
		 		    } );
		 		 
		 	 }
			 function deletevideocomment(commentid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/deletevideocomment?commentid='+commentid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$(".commentbox"+commentid).remove();
		    		    }
		 		    } );
		 		 
		 	 }
			 function deletevideo(videoid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/deletevideo?videoid='+videoid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	window.location.href="http://localhost/freniz_zend/public/videos/"+json.userid;
		    		    }
		 		    } );
		 		 
		 	 }
		 	 
		 	function votescribbles(postid,element){
	 			
	 			 $.ajax({
	    		    url:'http://localhost/freniz_zend/public/votescribbles?postid='+postid,
	    		    cache:false,
	    		    dataType:"json",
	    		    success:function(json){
	    		    	 if(json.status=='success'){
	    		    		 var innerHtml=$(element).html();
	    		    		 var pos1=innerHtml.indexOf('(')+2;
	    		    		 var pos2=innerHtml.indexOf(')')-1;
	    		    		 var length=pos2-pos1;
	    		    		 var votecount=1+parseInt(innerHtml.substr(pos1,length));
	    		    		 $(element).parent().html('<a onclick="unvotescribbles('+postid+',this)"style="float:right; font-weight:bold; text-decoration:none; color:#000"href="javascript:void(0)">winked( '+votecount+' )</a>');
	    		    	 }
	    		    }
	 		    } );
		 		
		 	 }
		 	function unvotescribbles(postid,element){

		 		 $.ajax({
	    		    url:'http://localhost/freniz_zend/public/unvotescribbles?postid='+postid,
	    		    cache:false,
	    		    dataType:"json",
	    		    success:function(json){
	    		    	if(json.status=='success'){
	    		    		 var innerHtml=$(element).html();
	    		    		 var pos1=innerHtml.indexOf('(')+2;
	    		    		 var pos2=innerHtml.indexOf(')')-1;
	    		    		 var length=pos2-pos1;
	    		    		 var votecount=parseInt(innerHtml.substr(pos1,length))-1;
	    		    		 $(element).parent().html('<a onclick="votescribbles('+postid+',this)"style="float:right; font-weight:bold; text-decoration:none;color:#000; "href="javascript:void(0)">wink( '+votecount+' )</a>');
	    		    	 }
	    		    }
	 		    } );
		 		
		 	 }
		 	function postvideos(userid){
		 		var title=document.getElementById("video-title").value;
		 		var embeddcode=document.getElementById("video-embeddcode").value;
		 		  	if(title!='' && embeddcode!=''){
		        	  var parameters="userid="+userid+"&title="+title+"&embeddcode="+embeddcode;
		        	  $.ajax({
	              	  type: 'POST',
	              	  url: 'http://localhost/freniz_zend/public/addvideo',
	              	  data: parameters,
	              	  success: function(json){
	              	$('#video-add-div').remove();
	              		$('#video-title').val('');
	              			$('#video-embeddcode').val('');
	              	  },
	              	  dataType: "json"
	              	});
	               
		 	}
		 		  	else{
		 		  		alert('Fill the empty field..');
		 		  	}
		 	}
		 	function postscribbles(userid){
		 		var text=document.getElementById("scribble-content").value;
		 		var pt=$('#post-pt').val();
		 		var cpt=$('#comment-pt').val();
		 		  	if(text!=''){
		        	  var parameters="userid="+userid+"&text="+text+"&pt="+pt+'&cpt='+cpt;
		        	    $.ajax({
	              	  type: 'POST',
	              	  url: 'http://localhost/freniz_zend/public/addscribbles',
	              	  data: parameters,
	              	  success: function(json){
	              	  
	              	   var main=document.getElementById('scribbles'); 
	              	      var top=document.createElement('div');
		            	  top.style.width='700px';
		            	    top.id="scribbles-"+json.id;
		            	 var pic=document.createElement('div');
		             	 pic.className='user-pic'+json.id;
		            	 pic.style.width='75px';
		            	 pic.style.height='75px';
		            	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+json.propic_url+'"/>';
		            	  pic.style.cssFloat='left';
		            	   $(top).append(pic);
		            	  var a=document.createElement('div');
		            	   a.id='stature-stream';
		            	   a.className='stature-stream'+json.id;
		                   a.style.width='600px';
		                   a.style.cssFloat='left';
		                   a.style.borderBottom='solid 1px #ccc';
		                   a.style.padding='5px';
		                      a.style.margin='0 0 5px 7px';
		                 	var c=document.createElement('div');
		                 	c.id='main-scrible-div';
		                 	c.style.width='600px';
		                 	c.style.float='left';
		                 	c.innerHTML='<div style="width:600px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 580px;margin-top: 5px;  float: right"><a id="remove-'+json.id+'" class="close"data="'+json.id+'"href="javascript:void(0)">X</a></div><div style="width:420px; float: left; font-size: 24px; font-weight: bold; height: 25px; "><a class="hover" data="'+json.userid+'" href="http://localhost/freniz_zend/public/'+json.userid+'">'+json.username+'</a><span style="margin-left:5px;font-size:14px; color:#ccc">'+json.pt+'</span></div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+json.date+'">'+json.date+'</div></div></div><div style="width:460px; margin-left: 20px; float: left; margin-top: 5px; padding:5px;  ">'+json.post+'</div>';
		                  var b=document.createElement('div');
		                 b.style.width='580px';
		                 b.style.padding='5px';
		                 b.style.float='left';
		                	 b.innerHTML='<a onclick="votescribbles('+json.id+',this)" class="vote-bar"style="float: right">wink( 0 )</a>';
		                 $(c).append(b);
		                      var g=document.createElement('div');
				            g.id='stature-comment-main-div'+json.id;
				          
				            var f=document.createElement('div');
				            f.innerHTML='<div id="comment-feild"style="width: 470px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+json.id+'" onkeydown="postcommentscribles('+json.id+',event)" type="text"placeholder="write to post"style="width: 430px; height: 20px;"/></div></div>';
				            $(g).append(f);
				             $(c).append(g);
		                 
		                  $(a).append(c);
		                  $(top).append(a);
		                 $(main).prepend(top);
		                $(".close").toggle(function(){
		      	    		var data=$(this).attr('data');
		      	    		var position = $("#remove-"+data).offset();
		      	    	$('#remove-items').css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-120)});
		      	    	$("#remove-items a:first").attr("onclick","deletescribbles('"+data+"')");
		      	    	$("#remove-items a:last").attr("onclick","report('"+data+"')");
		      	    		
		      	    	},function(){
		      	    		$('#remove-items').css({'position':'none','display':'none'});
		      	    		});
		      	  	  prettyLinks();
		           	  },
	              	  dataType: "json"
	              	});
	                document.getElementById("scribble-content").value=''; 
	                $("#remove-items").css({'display':'none'});
		 	}
		 		  	else{
		 		  		alert('Please Scribble Something..');
		 		  	}
		 	}
		 	function getstatures(userid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/getuserstatures?userid='+userid+'&format=xml&from='+postsfrom.statures,
		    		    cache:false,
		    		    dataType:"xml",
		    		    success:function(xml){
							postsfrom.statures+=limit_default;
		    		    	statures(xml,userid);
		    		    	
		    		    }
		 		    } );
		 	}
			 function statures(xml,userid)
			 {
					$('#loadingstature').remove();
		 		  var main=document.getElementById('statures');
		    	  $(xml).find('statures').each(function(){
		    		  if($(this).find('maxcomment').text()>comment_max)
						  comment_max=$(this).find('maxcomment').text();
		    		  $(this).find('stature').each(function(){
		    			  var statureid=$(this).find('statureid').text();
		            	   var a=document.createElement('div');
		            	   a.id='stature-stream';
		            	   a.className='stature-stream'+$(this).find('statureid').text();
			                 
		                   a.style.width='500px';
		                   a.style.cssFloat='left';
		                   a.style.borderBottom='solid 1px #ccc';
		                   a.style.padding='5px';
		                      a.style.margin='0 0 5px 0';
		                 	var c=document.createElement('div');
		                 	c.id='main-scrible-div';
		                 	c.style.width='500px';
		                 	c.style.float='left';
		                 	c.innerHTML='<div style="width:500px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 480px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('statureid').text()+'" class="close"data="'+$(this).find('statureid').text()+'"href="javascript:void(0)">X</a></div><div style="width:420px; float: left; font-size: 24px; font-weight: bold; height: 25px; ">'+$(this).find('username').text()+'</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date_stature').text()+'">'+$(this).find('date_stature').text()+'</div></div></div><div style="width:460px; margin-left: 20px; float: left; margin-top: 5px; padding:5px;  ">'+$(this).find('message').text()+'</div>';
		                  	
		                  var b=document.createElement('div');
		                 b.style.width='480px';
		                 b.style.padding='5px';
		                 b.style.float='left';
		                 if($(this).find('votecontains').text().trim()=='no'){
		                	 b.innerHTML='<a onclick="votestature('+$(this).find('statureid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote_count').text()+' )</a>';
		                  }else{
		                	  b.innerHTML='<a onclick="unvotestature('+$(this).find('statureid').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote_count').text()+' )</a>';
		                  }
		                 $(c).append(b);
		                
		                 var g=document.createElement('div');
				            g.id='stature-comment-main-div'+$(this).find('statureid').text();
				            var i=document.createElement('div');
				            i.id='stature-comment-sub-div'+$(this).find('statureid').text();
					          comments.statures[statureid]={};
					          comments.statures[statureid]['totalcomments']=$(this).find('commentcounts').text();
				            $(this).find('comments').each(function(){
				            	$(this).find('comment').each(function(){
				            		
				            		var d=document.createElement('div');
				            		 d.id='stature-comment-box'+$(this).find('commentid').text();
				            d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('commentpic_url').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" class="hover" data="'+$(this).find('cuserid').text()+'" href="'+$(this).find('cuserid').text()+'">'+$(this).find('cusername').text()+':</a>'+$(this).find('comment-comment').text()+'</div><a id="stature-comment-del'+$(this).find('commentid').text()+'" href="javascript:void(0)" onclick="deletestaturecomment('+$(this).find('commentid').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
				              $(i).append(d);
				            
				              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('commentid').text()==$(this).parent().parent().find('myid').text()){
				            	  $('#stature-comment-del'+$(this).find('commentid').text()).css('display','none');
		         				}
				            	});
				            	comments.statures[statureid]['commentsdisplayed']=defaultcomments;
				            	if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:statureid+'-loadcomments'}).html('<a href="javascript:void(0)" onclick="loadstaturecomments(\''+statureid+'\')">load previous</a>').appendTo(g);
								}
				            	else
				            	$('#'+statureid+'-loadcomments').remove();
				            });
				            $(g).append(i);
		             
				            var f=document.createElement('div');
				            f.innerHTML='<div id="comment-feild"style="width: 470px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('statureid').text()+'" onkeydown="postcommentstature('+$(this).find('statureid').text()+',event)" type="text"placeholder="write to post"style="width: 430px; height: 20px;"/></div></div>';
				            $(g).append(f);
				            $(c).append(g);
		                 
		                  $(a).append(c);
		                 
		                 $(main).append(a);
		                 $(this).find('comments').each(function(){
				            	$(this).find('comment').each(function(){
				            	  
				              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() && $(this).find('cuserid').text()!=$(this).parent().parent().find('myid').text()){
			         				
				            	  $('#stature-comment-del'+$(this).find('commentid').text()).css('display','none');
		         				}
				            	});
				            });
		                 if($(this).find('myid').text()!=$(this).find('userid').text() ){
	         					$('.delete').css('display','none');
	         				}
		              });
		              if($(this).find('loadmore').text()=='yes'){
						var loading=document.createElement('div');
						loading.id='loadingstature';
						loading.innerHTML='<div style="width:500px; padding:10px; margin-top:20px;text-align:center; background-color:#eee;float:left">loading...</div>';
						$(main).append(loading);
						setTimeout(function(){getstatures(userid);},5000);
					}
		          });
		    	  $(".close").toggle(function(){
		    		 	var data=$(this).attr('data');
	      	    		var position = $("#remove-"+data).offset();
	      	    	$('#remove-items').css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-120)});
	      	    	$("#remove-items a:first").click(function(){deletestature(data);});
	      	    	$("#remove-items a:last").click(function(){report(data);});
	      	    		
	      	    	},function(){
	      	    		$('#remove-items').css({'position':'none','display':'none'});
	      	    	});
		          loadCommentsTimeout=setTimeout(function(){loadcomments();},2000);
		    	  prettyLinks();
			    }
			 function loadstaturecomments(statureid){
					$.ajax({
						url:"http://localhost/freniz_zend/public/statures/getcomments?statureid="+statureid+"&from="+comments.statures[statureid]['commentsdisplayed'],
						dataType:"xml",
						cache:false,
						success:function(xml){
								$(xml).find('comments').each(function(){
									var statureid=$(this).find('statureid').text();
									var i=document.getElementById('stature-comment-sub-div'+$(this).find('statureid').text());
									$(this).find('comment').each(function(){
										var d=document.createElement('div');
										d.id='stature-comment-box'+$(this).find('id').text();
										d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('imageurl').text()+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('userid').text()+'">'+$(this).find('username').text()+':</a>'+$(this).find('comment-comment').text()+'</div><a id="stature-comment-del'+$(this).find('id').text()+'" href="javascript:void(0)" onclick="deletestaturecomment('+$(this).find('id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
										$(i).prepend(d);				            
										/*if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('commentid').text()==$(this).parent().parent().find('myid').text()){
											$('#stature-comment-del'+$(this).find('commentid').text()).css('display','none');
										}*/
									});
									comments.statures[statureid]['commentsdisplayed']+=commentlimit;
									if(comments.statures[statureid]['totalcomments']<comments.statures[statureid]['commentsdisplayed']){
										comments.statures[statureid]['commentsdisplayed']=comments.statures[statureid]['totalcomments'];
										$('#'+statureid+'-loadcomments').remove();
									}
								});
								 prettyLinks();
							}
						});
						
				}
		 	function addstature(){
		 		var text=document.getElementById("message-content").value;
		 		var visi=$('#stature-pt').val();
		 		var cpt=$('#comment-pt').val();
		 		  	if(text!=''){
		        	  var parameters="text="+text+"&visi="+visi+"&cpt="+cpt;
		        	    $.ajax({
	              	  type: 'POST',
	              	  url: 'http://localhost/freniz_zend/public/addstatures',
	              	  data: parameters,
	              	  success: function(json){
	              		 var main=document.getElementById('statures'); 
	              		   var a=document.createElement('div');
		            	   a.id='stature-stream';
		            	   a.className='stature-stream'+json.id;
			                 
		                   a.style.width='500px';
		                   a.style.cssFloat='left';
		                   a.style.borderBottom='solid 1px #ccc';
		                   a.style.padding='5px';
		                      a.style.margin='0 0 5px 0';
		                 	var c=document.createElement('div');
		                 	c.id='main-scrible-div';
		                 	c.style.width='500px';
		                 	c.style.cssFloat='left';
		                 	c.innerHTML='<div style="width:500px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 480px;margin-top: 5px;  float: right"><a id="remove-'+json.id+'" class="close"data="'+json.id+'"href="javascript:void(0)">X</a></div><div style="width:420px; float: left; font-size: 24px; font-weight: bold; height: 25px; ">'+json.username+'</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+json.date+'">'+json.date+'</div></div></div><div style="width:460px; margin-left: 20px; float: left; margin-top: 5px; padding:5px;  ">'+json.stature+'</div>';
		                  	
		                  var b=document.createElement('div');
		                 b.style.width='480px';
		                 b.style.padding='5px';
		                 b.style.float='left';
		                	 b.innerHTML='<a onclick="votestature('+json.id+',this)" class="vote-bar"style="float: right">wink( 0 )</a>';
		                  $(c).append(b);
		                
		                 var g=document.createElement('div');
				            g.id='stature-comment-main-div'+json.id;
				            var i=document.createElement('div');
				            i.id='stature-comment-sub-div'+json.id;
				          
				          
				            $(g).append(i);
		             
				            var f=document.createElement('div');
				            f.innerHTML='<div id="comment-feild"style="width: 470px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+json.id+'" onkeydown="postcommentstature('+json.id+',event)" type="text"placeholder="write to post"style="width: 430px; height: 20px;"/></div></div>';
				            $(g).append(f);
				            $(c).append(g);
		                 
		                  $(a).append(c);
		                  
			                 $(main).prepend(a);
			                 $(".close").toggle(function(){
				      	    		var data=$(this).attr('data');
				      	    		var position = $("#remove-"+data).offset();
				      	    	$('#remove-items').css({'position':'absolute','display':'block','top':+(position.top+16),'left':+(position.left-120)});
				      	    	$("#remove-items a:first").attr("onclick","deletestature('"+data+"')");
				      	    	$("#remove-items a:last").attr("onclick","report('"+data+"')");
				      	    		
				      	    	},function(){
				      	    		$('#remove-items').css({'position':'none','display':'none'});
				      	    	});
			                 prettyLinks();
			                 },
	              	  dataType: "json"
	              	});
	                document.getElementById("message-content").value=''; 
		 	}
		 		  	else{
		 		  		alert('Please Write your stature..');
		 		  	}
		 	}
		 	 function deletestature(statureid){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/deletestature?statureid='+statureid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$(".stature-stream"+statureid).remove();
		    		    	$('#remove-items').css({'position':'none','display':'none'});
		    		    }
		 		    } );
		 		 
		 	 }
		 	 function postcommentstature(statureid,e)
	            {	
		 		var keynum;
		 		if(e){
		 		if(window.event) // IE8 and earlier
		 			{
		 			keynum = e.keyCode;
		 			}
		 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		 			{
		 			keynum = e.which;
		 			}
		 			}
		 		        if(keynum==13 || !e){
		 		        	var text=document.getElementById("text-comment-"+statureid).value;
		 		        	if(text!=''){
		 		        	  var parameters="statureid="+statureid+"&text="+text;
		 		        	   $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/dostaturecomment',
		 	              	  data: parameters,
		 	              	  success: function(json){
		 	              		  
		 	              		var main=document.getElementById('stature-comment-sub-div'+json.statureid);
		 	              		var d=document.createElement('div');
		 	              	 d.id="stature-comment-box"+json.commentid;
			            d.innerHTML='<div style="width: 480px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left; "><img src="http://images.freniz.com/32/32_'+json.userpic+'" height="32" width="32" /></div><div style="width: 390px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+json.userid+'">'+json.username+':</a>'+json.comment+'</div><a href="javascript:void(0)" onclick="deletestaturecomment('+json.commentid+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+json.date+'">'+json.date+'</div></div></div>';
			              $(main).append(d);
			              prettyLinks();
		 	              		  },
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById('text-comment-'+statureid).value=''; 
		 		        	}
		 		    }
	               
	               
	            }
		 	 function deletestaturecomment(commentid){
		 			 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/deletestaturecomment?commentid='+commentid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$("#stature-comment-box"+commentid).remove();
		    		    }
		 		    } );
		 		 
		 	 }
		 	 function votestature(statureid,element){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/votestature?statureid='+statureid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	 if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=1+parseInt(innerHtml.substr(pos1,length));
		    		    		 $(element).parent().html('<a onclick="unvotestature('+statureid+',this)" href="javascript:void(0)" class="vote-bar"style="float: right">unwink( '+votecount+' )</a>');
		    		    	 }
		    		    }
		 		    } );
		 		 
		 	 }
		 	 function unvotestature(statureid,element){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/unvotestature?statureid='+statureid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=parseInt(innerHtml.substr(pos1,length))-1;
		    		    		 $(element).parent().html('<a onclick="votestature('+statureid+',this)" href="javascript:void(0)" class="vote-bar"style="float: right">wink( '+votecount+' )</a>');
		    		    	 }
		    		    }
		 		    } );
		 		 
		 	 }
		 	 
		 	 function voteadmire(admireid,element){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/voteadmire?admireid='+admireid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
	    		    	if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=1+parseInt(innerHtml.substr(pos1,length));
		    		    		 $(element).parent().html('<a onclick="unvoteadmire('+admireid+',this)"class="vote-bar"href="javascript:void(0)"style="float: right">unwink( '+votecount+' )</a>');
		    		    	}
		    		    }
		 		    } );
		 		 
		 	 }
		 	 function unvoteadmire(admireid,element){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/unvoteadmire?admireid='+admireid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=parseInt(innerHtml.substr(pos1,length))-1;
		    		    		 $(element).parent().html('<a onclick="voteadmire('+admireid+',this)"class="vote-bar"href="javascript:void(0)"style="float: right">wink( '+votecount+' )</a>');
		    		    	 }
		    		    }
		 		    } );
		 		 
		 	 }
		 	function voteblog(blogid,element){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/voteblog?blogid='+blogid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	 if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=1+parseInt(innerHtml.substr(pos1,length));
		    		    		 $(element).parent().html('<a onclick="unvoteblog('+blogid+',this)"class="vote-bar"href="javascript:void(0)"style="float: right">unwink( '+votecount+' )</a>');
		    		    	 }
		    		    		
		    		    }
		 		    } );
		 		 
		 	 }
		 	 function unvoteblog(blogid,element){
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/unvoteblog?blogid='+blogid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=parseInt(innerHtml.substr(pos1,length))-1;
		    		    		 $(element).parent().html('<a onclick="voteblog('+blogid+',this)"class="vote-bar"href="javascript:void(0)"style="float: right">wink( '+votecount+' )</a>');
		    		    	 }
		    		    }
		 		    } );
		 		 
		 	 }
		 	  function propiccroping(imageid,deletesrc)
		 	    {
		 	        var x=$('#x1').val();
		 	        var y=$('#y1').val();
		 	        var w=$('#w').val();
		 	        var h=$('#h').val();
		 	        setaspropic(imageid,deletesrc,x,y,w,h);
		 	    }
		 	    function secpiccroping(imageid,deletesrc,secpicno)
		 	    {
		 	        
		 	        var x=$('#x1').val();
		 	        var y=$('#y1').val();
		 	        var w=$('#w').val();
		 	        var h=$('#h').val();
		 	        setassecpic(imageid,deletesrc,secpicno,x,y,w,h);
		 	    }
		 	   function setaspropic(imageid,deletesrc,x,y,width,height)
		 	  {
				   $.ajax({
		    		    url:'http://localhost/freniz_zend/public/setprofilepicture?imageid='+imageid+'&x='+x+'&y='+y+'&width='+width+'&height='+height+'&deletesrc='+deletesrc,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$('.crop-button').css('display','block');
		    		    	$('.done-button').css('display','none');
		    		    	alert(json.status);
							set_propic(json.fileid,"http://images.freniz.com/200/200_"+json.url,"true");
							
		    		    }
		 		    } );
		 	       
		 	  }
		 	  function set_propic(imageid,url,deletesrc){
		 	 var main=document.getElementById('target-container');
            main.innerHTML='<img src="'+url+'"  id="target" alt="Flowers" /><input class="greenbutton" style="margin-left:5px;" type="button" value="crop" onclick="propiccroping(\''+imageid+'\',\''+deletesrc+'\')"/><form id="coords" class="coords" onsubmit="return false;" ><div style="display:none"><input type="hidden" size="4" id="x1" name="x1" value="0" /><input type="hidden" size="4" id="y1" name="y1" value="0" /><input type="hidden" size="4" id="x2" name="x2" value"0" /><input type="hidden" size="4" id="y2" name="y2" value="0" /><input type="hidden" size="4" id="w" name="w" value="0" /><input type="hidden" size="4" id="h" name="h" value="0" /></div></form>';
            initcrop(1/1);

}
		 	  function set_secpic(imageid,url,deletesrc,secpicno){
            var main=document.getElementById('light');
            main.innerHTML='<img src="'+url+'"  id="target" alt="Flowers" /><input type="button" value="done croping" onclick="secpiccroping(\''+imageid+'\',\''+deletesrc+'\',\''+secpicno+'\')"/><input type="button" style="float:right" value="Skip" onclick="secpiccroping(\''+imageid+'\',\''+deletesrc+'\',\''+secpicno+'\')"/><form id="coords" class="coords" onsubmit="return false;" ><div style="display:none"><input type="hidden" size="4" id="x1" name="x1" value="0" /><input type="hidden" size="4" id="y1" name="y1" value="0" /><input type="hidden" size="4" id="x2" name="x2" value"0" /><input type="hidden" size="4" id="y2" name="y2" value="0" /><input type="hidden" size="4" id="w" name="w" value="0" /><input type="hidden" size="4" id="h" name="h" value="0" /></div></form>';
            document.getElementById('light').style.display='block';
            document.getElementById('fade').style.display='block';
            initcrop(2/1);

}
		 	  function setassecpic(imageid,deletesrc,secpicno,x,y,width,height)
		 	 {
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/setsecondarypicture?imageid='+imageid+'&x='+x+'&y='+y+'&width='+width+'&height='+height+'&deletesrc='+deletesrc+'&secpicno='+secpicno,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	$('.crop-button').css('display','block');
		    		    	$('.done-button').css('display','none');
		    		    }
		 		    } );
		 	   
		 	 }
		 	  function secpicposition(imageid,top)
			 	 {
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/setsecondarypicture?imageid='+imageid+'&secpicno=1&top='+top,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	 alert(json.status);
			    		    }
			 		    } );
			 	   
			 	 }
		 		function questions(tags)
			 	{
		 			document.getElementById("load").style.display='block';
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/forum/search?type=tags&order=popular&key='+tags,
			    		    cache:false,
			    		    dataType:"xml",
			    		    success:function(xml){
			    		    	 question(xml);
			    		    	
			 			 		
			    		    }
			 		    } );
			 		
			 	  
			 	}
			 	function question(xml)
			 	{
			 		     var e=document.getElementById('forum-results');
			 	            e.innerHTML='';
			 	          
			 	           $(xml).find('forum_search').each(function(){
				    	    	  $(this).find('search').each(function(){
			 	                    var album=document.createElement('div');
			 	                      album.innerHTML='<a href="http://localhost/freniz_zend/public/question/'+$(this).find('id').text()+'" class="fore"><div style="width: 690px; padding:5px; float: left; border-bottom: solid 1px"><div style="width: 690px; float: left; font-size: 24px; font-weight: bold;">'+$(this).find('question').text()+'</div><div style="width: 300px; margin-left: 7px;  float: left; font-size: 12px; font-weight: bold; color: #000; "><span class="timeago" style="color:#aaa" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</span> Asked by '+$(this).find('askedby').text()+'</div><div style="width: 690px; float: left; "><label style="float:left; font-weight: bold; ">Topics:</label><div style="float: left; font-weight: bold; color: #C7BB00; width: 200px; font-size: 16px">'+$(this).find('tags').text()+'</div><div id="forum-tabs"style="float: right; max-width: 400px;  "><ul><li>Votes('+$(this).find('vote').text()+')</li><li>-</li><li>Views('+$(this).find('views').text()+')</li><li>-</li><li>Solved('+$(this).find('solved').text()+')</li></ul></div></div></div></a>';
			 	                   $(e).append(album);
			 	              
				    	    	  });
			 	           });
			 	          document.getElementById("load").style.display='none';
			 	        prettyLinks();
			 	}
			 	function quest(){
			 		var key=document.getElementById('find-ques').value;
			 		window.location.href='?s='+key;
			 	}
			 	function forumquestions(key){
			 //		var key=document.getElementById('find-ques').value;
			 		//http://localhost/freniz_zend/public/forum/search?type=tags&order=popular&key=php
			 		if(key!=''){
			 			 $.ajax({
				    		    url:'http://localhost/freniz_zend/public/forum/search?type=question&order=popular&key='+key,
				    		    cache:false,
				    		    dataType:"xml",
				    		    success:function(xml){
				    		    	 question(xml);
				    		    }
				 		    } );
			 		}else alert('feild cannot be empty');
			 		
			 		
			 	}
			 	function topics(key){
			 		document.getElementById("load").style.display='block';
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/forum/search?type=tags&order=popular&key='+key,
			    		    cache:false,
			    		    dataType:"xml",
			    		    success:function(xml){
			    		    	question(xml);
			    		    	document.getElementById("load").style.display='none';
			    		    }
			 		    } );
			 	}
			 	
			 	function removefriends(userid){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/removefriends?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	alert(json.status);
			    		    }
			 		    } );
			 	}
			 	function addfriends(userid){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/addfriends?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	var span=document.createElement('div');
			    		    	$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
			    		    	span.id='alert-span';
			    				if(json.status=='success')
			    					span.innerHTML='Hey your request sent successfully please be waited :)';
			    				else
			    					span.innerHTML='There is an Error';
			    					$('#maincontainer').append(span);
			    				setTimeout(function(){$('#alert-span').remove();},3000);
			    		    }
			 		    } );
			 	}
			 	function cancelfriends(userid){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/cancelfriends?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	var span=document.createElement('div');
			    		    	$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
			    		    	span.id='alert-span';
			    				if(json.status=='success')
			    					span.innerHTML='You ve cancel this friend :(';
			    				else
			    					span.innerHTML='There is an Error';
			    					$('#maincontainer').append(span);
			    				setTimeout(function(){$('#alert-span').remove();},3000);
			    		    }
			 		    } );
			 	}
			 	function acceptfriends(userid){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/acceptfriends?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	alert(json.status);
			    		    }
			 		    } );
			 	}
			 	
			 	function denyfriends(userid){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/denyfriends?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	alert(json.status);
			    		    }
			 		    } );
			 	}
			 	function uservote(userid,element){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/uservote?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	var span=document.createElement('div');
			    		    	$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
			    		    	span.id='alert-span';
			    				if(json.status=='success')
			    					span.innerHTML='Hey you sucessfully voted this user :)';
			    				else
			    					span.innerHTML='There is an Error';
			    				var count=$('#vote-count').html();
			    				$('#vote-count').html((count+1));
			    				  $(element).html('winked');
		    		    		 $(element).attr('onclick',"userunvote("+userid+",'this')");
			    				$('#topcontainer').append(span);
			    				setTimeout(function(){$('#alert-span').remove();},3000);
			    		    }
			 		    } );
			 	}
			 	function userunvote(userid,element){
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/profile/userunvote?userid='+userid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	var span=document.createElement('div');
			    		    	$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
			    		    	span.id='alert-span';
			    				if(json.status=='success')
			    					span.innerHTML='Hey you sucessfully unvote this user :)';
			    				else
			    					span.innerHTML='There is an Error';
			    				var count=$('#vote-count').html();
			    				$('#vote-count').html((count-1));
			    				 $(element).html('wink');
		    		    		 $(element).attr('onclick',"uservote("+userid+",'this')");
			    			
			    				$('#topcontainer').append(span);
			    				setTimeout(function(){$('#alert-span').remove();},3000);
			    		    }
			 		    } );
			 	}
			 
			 	function answers(questionid)
			 	{
			 		 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/answers/'+questionid,
			    		    cache:false,
			    		    dataType:"xml",
			    		    success:function(xml){
			    		    	 answer(xml);
			    		    }
			 		    } );
			 		
			 	  
			 	}
			 	function answer(xml)
			 	{
			 		     var e=document.getElementById('ans-quest');
			 	            e.innerHTML='';
			 	           var ol=document.createElement('ol');
			 	           $(xml).find('answers').each(function(){
				    	    	  $(this).find('answer').each(function(){
				    	    		  var li=document.createElement('li');
				    	    		  var main=document.createElement('div');
				    	    		  main.style.width='600px';
			 	                    var album=document.createElement('div');
			 	                      album.innerHTML='<div style="width: 600px; font-size: 18px; font-weight: bold; ">'+$(this).find('ans').text()+'</div><div style=" margin-left: 20px;font-size: 12px; margin-top: -3px; border-bottom: solid 1px; font-weight: bold; "><div style="float:left; color:#ccc;padding-right:5px"class="timeago" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div>answered by <a href="http://localhost/freniz_zend/public/'+$(this).find('url').text()+'">'+$(this).find('ansby').text()+'</a></div>';
			 	                      $(main).append(album);
			 	                     var ul=document.createElement('ul');  
			 	                     $(this).find('comments').each(function(){
						    	    	  $(this).find('comment').each(function(){
						    	    		  var comli=document.createElement('li');
			 	                      var ans=document.createElement('div');
			 	                      ans.innerHTML='<div style="width: 550px; margin-left:20px;"><div white-space="pre" style="width: 550px; font-size: 16px; font-weight: bold;  ">'+$(this).find('comment-comment').text()+'</div><div style="width: 300px; margin-left: 20px;font-size: 10px; margin-top: -3px; font-weight: bold; ">'+$(this).find('comment-date').text()+'</div></div>';
			 	                      $(comli).append(ans);
			 	                     $(ul).append(comli);
						    	    	  });
			 	                     });
			 	                    $(main).append(ul);
			 	                     var answerid=$(this).find('id').text().trim();
			 	                    var com=document.createElement('div');
			 	                    com.innerHTML='<div style="width:460px;  padding:5px; background-color:#eee; margin-left:40px; height:25px; "><input id="answer-comment'+answerid+'" type="text"onkeydown="postanswercomment('+answerid+',event)"style="width:450px; float:left; height:20px; margin-left:3px;"placeholder="Enter Your Comment"/></div>';
			 	                   $(main).append(com);
			 	                       var vot=document.createElement('div');
			 	                      vot.innerHTML='<div style="width: 600px; height: 20px;"><div style="height: 40px; margin-right: 20px; margin-top: 5px; float: right;"><a href="javascript:void(0)" onclick="voteanswer('+$(this).find('id').text()+')">('+$(this).find('votecount').text()+')Voted</a></div>';
			 	                     $(main).append(vot);
			 	                      
			 	                    $(li).append(main);
			 	                   $(ol).append(li);
						 	       
				    	    	  });
			 	           });
			 	           $(e).append(ol);
			 	          prettyLinks();
			 	}
			 	
			 	
			 	function postanswer(questionid,date,e)
	            {	
			 	var keynum;
		 		if(e){
		 		if(window.event) // IE8 and earlier
		 			{
		 			keynum = e.keyCode;
		 			}
		 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		 			{
		 			keynum = e.which;
		 			}
		 			}
		 		        if(keynum==13 || !e){
		 		        	var text=document.getElementById("answer-question").value;
		 		        	if(text!=''){
		 		        	  var parameters="questionid="+questionid+"&answer="+text+"&date="+date;
		 		        	   $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/ansquestion',
		 	              	  data: parameters,
		 	              	  success: function(json){alert(json.status);},
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById("answer-question").value=''; 
		 		        	}
		 		    }
	               
	               
	            }
				function postanswercomment(answerid,e)
	            {	
			 	var keynum;
		 		if(e){
		 		if(window.event) // IE8 and earlier
		 			{
		 			keynum = e.keyCode;
		 			}
		 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
		 			{
		 			keynum = e.which;
		 			}
		 			}
		 		        if(keynum==13 || !e){
		 		        		var text=document.getElementById("answer-comment"+answerid).value;
		 		        	if(text!=''){
		 		        	  var parameters="answerid="+answerid+"&comment="+text;
		 		        	    $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/commentanswer',
		 	              	  data: parameters,
		 	              	  success: function(json){alert(json.status);},
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById("answer-comment"+answerid).value=''; 
		 		        	}
		 		    }
	               
	               
	            }
				 function votequestion(questionid){
					 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/votequestion?questionid='+questionid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	 alert(json.status);
			    		    }
			 		    } );
			 		 
			 	 } 
				 function voteanswer(answerid){
					 $.ajax({
			    		    url:'http://localhost/freniz_zend/public/voteanswer?answerid='+answerid,
			    		    cache:false,
			    		    dataType:"json",
			    		    success:function(json){
			    		    	 alert(json.status);
			    		    }
			 		    } );
			 		 
			 	 }
				 
				 function findtags(element,event)
				 	{
					
				 		
					 var key=element.value;
				 	 $.ajax({
				    		    url:'http://localhost/freniz_zend/public/forum/gettags?key='+key,
				    		    cache:false,
				    		    dataType:"xml",
				    		    success:function(xml){
				    		    	 findtag(xml,element,event);
				    		    }
				 		    } );
				 		
				 		        
				 	}
				
				 function gettags()
				 	{
					 var key=document.getElementById("tags").value;
				 		 $.ajax({
				    		    url:'http://localhost/freniz_zend/public/forum/gettags?key='+key,
				    		    cache:false,
				    		    dataType:"xml",
				    		    success:function(xml){
				    		    	 tags(xml);
				    		    }
				 		    } );
				 		
				 	  
				 	}
				 function searchname()
				 	{
					 
					 var key=document.getElementById("welcome-search-element").value;
					 var type=document.getElementById('select-field').value;
					 $.ajax({
				    		    url:"http://localhost/freniz_zend/public/search?name="+key.trim()+"&type="+type,
				    		    cache:false,
				    		    dataType:"xml",
				    		    success:function(xml){
				    		    	searchnames(xml,type);
				    		    }
				 		    } );
				 		
				 	  
				 	}
				 
				 function searchnames(xml,type){
					 var tags=Array();
					 if(type=='user' || type=='skills'){
						 $(xml).find('users').each(function(){
			    	    	  $(this).find('user').each(function(){
			    	    		  tags.push($(this).find('username').text()) ;
			    	    	  });
					   });
					 }
					 else if(type=='page'){
						 $(xml).find('pages').each(function(){
			    	    	  $(this).find('page').each(function(){
			    	    		  tags.push($(this).find('pagename').text()) ;
			    	    	  });
					   });
					 }
					  
					   function split( val ) {
				            return val.split( /,\s*/ );
				        }
				        function extractLast( term ) {
				            return split( term ).pop();
				        }
				        $( "#welcome-search-element" )
				            // don't navigate away from the field on tab when selecting an item
				            .bind( "keydown", function( event ) {
				                if ( event.keyCode === $.ui.keyCode.TAB &&
				                        $( this ).data( "autocomplete" ).menu.active ) {
				                    event.preventDefault();
				                }
				            })
				            .autocomplete({
				                minLength: 0,
				                source: function( request, response ) {
				                    // delegate back to autocomplete, but extract the last term
				                    response( $.ui.autocomplete.filter(
				                        tags, extractLast( request.term ) ) );
				                },
				                focus: function() {
				                    // prevent value inserted on focus
				                    return false;
				                },
				                select: function( event, ui ) {
				                	 var terms = split( this.value );
				                     // remove the current input
				                     terms.pop();
				                     // add the selected item
				                     terms.push(ui.item.value );
				                     // add placeholder to get the comma-and-space at the end
				                    // terms.push( "" );
				                    this.value = $.trim(terms);
				                    changepage();
				                     return false;
				                }
				            });
				 }
				 function findskills(element,event)
				 	{
					 var key=element.value;
				 	 $.ajax({
				    		    url:'http://localhost/freniz_zend/public/settings/getskills?key='+key,
				    		    cache:false,
				    		    dataType:"xml",
				    		    success:function(xml){
				    		    	 findtag(xml,element,event);
				    		    }
				 		    } );
				 		
				 		        
				 	}
				 function findtag(xml,element,e){
					 var keynum;
				 		if(e){
				 		if(window.event) // IE8 and earlier
				 			{
				 			keynum = e.keyCode;
				 			}
				 		else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
				 			{
				 			keynum = e.which;
				 			}
				 			}
				 		     
					  var tags=Array();
					   $(xml).find('tags').each(function(){
			    	    	  $(this).find('tag').each(function(){
			    	    		  tags.push($(this).find('tag_name').text()) ;
			    	    		  
			    	    	  });
					   });
					   if(tags.length>0){
					   function split( val ) {
				            return val.split( /,\s*/ );
				        }
				        function extractLast( term ) {
				            return split( term ).pop();
				        }
				        $(element)
				            // don't navigate away from the field on tab when selecting an item
				            .bind( "keydown", function( event ) {
				             if ( event.keyCode === $.ui.keyCode.TAB &&
				                        $( this ).data( "autocomplete" ).menu.active ) {
				                    event.preventDefault();
				                }
				               
				            });
				        
				            $(element).autocomplete({
				                minLength: 0,
				                source: function( request, response ) {
				                    // delegate back to autocomplete, but extract the last term
				                    response( $.ui.autocomplete.filter(
				                        tags, extractLast( request.term ) ) );
				                },
				                focus: function() {
				                    // prevent value inserted on focus
				                    return false;
				                },
				                select: function( event, ui ) {
				                	 var terms = split( this.value );
				                     // remove the current input
				                     terms.pop();
				                     // add the selected item
				                     terms.push(ui.item.value );
				                     // add placeholder to get the comma-and-space at the end
				                    // terms.push( "" );
				                    this.value = $.trim(terms);
				                    if(element.id=='tag'){
				                    	$('#tag').val(ui.item.value);
				                    	 changepage();
				                    }
				                    if(element.id!='forum_tag' && element.id!='skills'){
				                    questions(ui.item.value);}
				                    else{
				                    	
				                    	if($('#tag-div').children().length<5){
				                    	 if(!$('#tag-div div[name="'+ui.item.value+'"]').is('*') && element.value!=''){
				                    	 var e=document.createElement('div');
	                 						e.className='usersname';
	                 	                    $(e).attr('name',ui.item.value);
	                 	                    e.innerHTML=ui.item.value+'<a style="width:10px; height:10px; text-decoration:none; background-color:red; font-size:14px; margin-bottom:20px" onclick="$(this).parent().remove();" href="javascript:void(0)">x</a>';
	                 						$('#tag-div').append(e);
				                    	 }
				                    }else {
				                    	alert('Max-tag is 5');
	                 						
				                    }
				                    	$(element).val('');
				                    }
				                     return false;
				                }
				            });
				        }else if(keynum==13){
					            		 if($('#tag-div').children().length<5 ){
					                    	 if(!$('#tag-div div[name="'+element.value+'"]').is('*') && element.value!=''){
					                    	 var e=document.createElement('div');
				       						e.className='usersname';
				       	                    $(e).attr('name',element.value);
				       	                    e.innerHTML=element.value+'<a style="width:10px; height:10px; text-decoration:none; background-color:red; font-size:14px; margin-bottom:20px" onclick="$(this).parent().remove();" href="javascript:void(0)">x</a>';
				       						$('#tag-div').append(e);
					                    	 }
					                    }else {
					                    	alert('Max-tag is 5');
				       						
					                    }
					                    	$(element).val('');
				        }
			    	
				 }
				  function askquestion()
			       {
			    	 	var title=escape(document.getElementById('forum_title').value);
			         var text=escape(document.getElementById('forum').value);
			         var tags='';
			         $('#tag-div').children('div').each(function(){
							tags+=$(this).attr('name')+',';
						});
						if(tags.length>0){
							tags=tags.slice(0, -1);
						}
			          if(text=='' || tags==''){alert("please fill the information");}else{
			        		var parameters="question="+title+"&description="+text+"&tags="+tags.trim();
			            	$.ajax({
			            		url:'http://localhost/freniz_zend/public/askquestion',
			            		cache:false,
			            		data:parameters,
			            		type:'post',
			            		dataType:"json",
			            		success:function(json){
			            			$('#forum-top').remove();
			            		}
			            	});
			        
			         }
			       }
				 
				 function tags(xml){
					  var tags=Array();
					   $(xml).find('tags').each(function(){
			    	    	  $(this).find('tag').each(function(){
			    	    		  tags.push($(this).find('tag_name').text()) ;
			    	    	  });
					   });
					   function split( val ) {
				            return val.split( /,\s*/ );
				        }
				        function extractLast( term ) {
				            return split( term ).pop();
				        }
				        $( "#tags" )
				            // don't navigate away from the field on tab when selecting an item
				            .bind( "keydown", function( event ) {
				                if ( event.keyCode === $.ui.keyCode.TAB &&
				                        $( this ).data( "autocomplete" ).menu.active ) {
				                    event.preventDefault();
				                }
				            })
				            .autocomplete({
				                minLength: 0,
				                source: function( request, response ) {
				                    // delegate back to autocomplete, but extract the last term
				                    response( $.ui.autocomplete.filter(
				                        tags, extractLast( request.term ) ) );
				                },
				                focus: function() {
				                    // prevent value inserted on focus
				                    return false;
				                },
				                select: function( event, ui ) {
				                	var va=$('#sel').html();
				                	if(va=='')
				                		$('#sel').html(ui.item.value.trim());
				                	else
				                		$('#sel').html(va+','+ui.item.value.trim()); 
				                     $('#tags').val('');
				                    return false;
				                }
				            });
				       
					   
			    	
				 }
				 function placeempty(){
					if( $('#search-places').val()==''){
						$('#search-places-hidden').val('');
					}
				 }
				 function changepage(){
					   var key=document.getElementById('welcome-search-element').value;
						var type=document.getElementById('select-field').value;
						var places=document.getElementById('search-places-hidden').value;
					    if(key.trim()=='' && type!='skills'){
					    		window.location.href='http://localhost/freniz_zend/public/frenizsearch?s=';
					    }else{
					    	  var category=document.getElementById('category').value;
					           var subcategory=document.getElementById('subcategory').value;
					           var skills=document.getElementById('tag').value;
					           var placetype=document.getElementById('select-field-place').value;
					           	var query;
					           	query ='http://localhost/freniz_zend/public/frenizsearch?s='+key+'&type='+type;
					        	if(places!='' && type=='user')
							    	query+='&'+placetype+'='+places;
					           	if(type!='skills'){
					    		if(category!='0'){
					    			query+='&category='+category;
					    		}
					    		if(subcategory!='0'){
					    			query+='&subcategory='+subcategory;
					    		}
					    				
					    		}else{
					    			query+='&skills='+skills;
					    		}
					    		
					    		window.location.href=query;
					    }
				   
						    
				 }
				 

				 function welcomesearch(type,key,category,subcategory,skills,ccity,htown){
					  if(key.trim().length>0){
				    if(type=='user' && ccity=='' && htown==''){
				          
				              url="http://localhost/freniz_zend/public/search?key="+key.trim()+"&type="+type;    
				       
				      }else if(type=='user' && ccity!=''){
							url="http://localhost/freniz_zend/public/search?key="+key.trim()+"&ccity="+ccity+"&type="+type;
				      }
				      else if(type=='user' && htown!=''){
							url="http://localhost/freniz_zend/public/search?key="+key.trim()+"&htown="+htown+"&type="+type;
				      }
				    
				      else if(type=='page'){
                            url="http://localhost/freniz_zend/public/search?key="+key.trim()+"&category="+escape(category)+"&subcategory="+subcategory+"&type="+type;
				    
				      }
				       else if(type=='places'){
				            url="http://localhost/freniz_zend/public/search?key="+key.trim()+"&type="+type;
				        
				       }
				     
				      
				   
				       }
				     else{
				        document.getElementById('search-welcome-results').innerHTML='';
				     }
				        if(type=='skills'){
				        	 url="http://localhost/freniz_zend/public/search?key="+key.trim()+"&skills="+skills+"&type=user";
					      }
				        $.ajax({
		 	              	  type: 'get',
		 	              	  url: url,
		 	              	  cache:false,
		 	              	  success: function(xml,type){
		 	              		  searchwelcome(xml,type);},
		 	              	  dataType: "xml"
		 	              	});
				    
				 }

				 function searchwelcome(xml,type)
				 {
				   
				             var b=document.createElement('div');
				             $(xml).find('users').each(function(){
				                 $(this).find('user').each(function(){
				                     var a=document.createElement('div');
				                     a.className="main-wel-div";
				                     
				                    a.innerHTML='<div style="width: 50px; height: 70px; float: left; "><div style="width: 50px; height: 50px; float: left;"><img src="http://images.freniz.com/50/50_'+$(this).find('propic').text()+'" height="50" width="50"/></div></div><div style="width: 400px; font-weight:bold; font-size:18px; color:#000; margin-left:10px; float: left;"><a style="color:#000; text-decoration:none" href="'+$(this).find('url').text()+'">'+$(this).find('username').text()+'</a></div>';
				                  var space=document.createElement('div');
				                    var skil=$(this).find('skills').text();
				                  if(skil.trim().length>0){
				                    var spaceskil=document.createElement('div');
				                    spaceskil.className='search-skills';
					                  spaceskil.innerHTML='<div style="float:left"><label style="color:#000;">skills:</label>'+$(this).find('skills').text()+'</div>';
					                  space.appendChild(spaceskil);
				                     }
					                  var live=$(this).find('ccity').text();
					                   if(live.trim().length>0){
					                       var livin=document.createElement('div');
					                    livin.innerHTML='<div style="float:left; width:500px; color:#000; font-size:12px; font-weight:bold; margin-left:20px;"><label style="color:#444;font-size:12px; font-weight:bold;">Livingin:</label>'+$(this).find('ccity').text()+'</div>';
					                    space.appendChild(livin);}
					                   var htown=$(this).find('htown').text();
					                   if(htown.trim().length>0){
					                       var htown=document.createElement('div');
					                       htown.innerHTML='<div style="float:left; width:500px; color:#000; font-size:12px; font-weight:bold; margin-left:20px;"><label style="color:#444;font-size:12px; font-weight:bold;">Hometown:</label>'+$(this).find('htown').text()+'</div>';
					                    space.appendChild(htown);}
					                   a.appendChild(space);
					                /*  var stdy=$(this).find('college').text();
					                   if(stdy.trim().length>0){
					                       var study=document.createElement('div');
					                    study.innerHTML='<div style="width: 500px; margin-left:10px; height: 20px; float: left; font-size:12px; font-weight:bold; color:#000;  "><label style="color:#444;">Studied at:</label>'+$(this).find('college').text()+'</div>';
					                    a.appendChild(study);}
				                    var city=$(this).find('employer').text();
				                   if(city.trim().length>0){
				                       var ccity=document.createElement('div');
				                    ccity.innerHTML='<div style="width: 500px; height: 20px; float: left; font-size:12px; font-weight:bold; color:#000; margin-left:10px; "><label style="color:#ccc;">Worked at:</label>'+$(this).find('employer').text()+'</div>';
				                    a.appendChild(ccity);}
				                    */
				                   var s=document.createElement('div');
					                  s.innerHTML='<div style="width: 600px; float: left; border:solid 1px #ccc"></div>';
					                  a.appendChild(s);
					              
				                    $(b).append(a);
				                 });
				             });
				            $(xml).find('pages').each(function(){
				                 $(this).find('page').each(function(){
				                	 var a=document.createElement('div');
				                     a.className="main-wel-div";
				                     
				                    a.innerHTML='<div style="width: 50px; height: 70px; float: left; "><div style="width: 50px; height: 50px; float: left; "><img src="http://images.freniz.com/50/50_'+$(this).find('pagepic').text()+'" height="50" width="50"/></div></div><div style="width: 400px; font-weight:bold; font-size:18px; color:#000; margin-left:10px; float: left;"><a style="color:#000;text-decoration:none" href="http://localhost/freniz_zend/public/'+$(this).find('url').text()+'">'+$(this).find('pagename').text()+'</a></div>';
				                    var space=document.createElement('div');
				                    space.className='search-skills';
					                  space.innerHTML='<div style="float:left; color:#000;"><label style="color:#444;">category:</label>'+$(this).find('category').text()+'</div>';
					                  a.appendChild(space);
					                       var livin=document.createElement('div');
					                    livin.innerHTML='<div style="float:left;  color:#000; margin-left:20px;"><label style="color:#444;">Subcategory:</label>'+$(this).find('subcategory').text()+'</div>';
					                    space.appendChild(livin);
				                       
					                        var study=document.createElement('div');
					                    study.innerHTML='<div style="width: 500px; margin-left:10px; height: 20px; float: left; font-size:12px; font-weight:bold; color:#000;  "><label style="color:#444;">Votes:</label>'+$(this).find('votecount').text()+'</div>';
					                    a.appendChild(study);
				                 
				                   var s=document.createElement('div');
					                  s.innerHTML='<div style="width: 600px; float: left; border:solid 1px #ccc"></div>';
					                  a.appendChild(s);
					              
				                    $(b).append(a);
				                 });
				             });
				             $(xml).find('places').each(function(){
				                 $(this).find('place').each(function(){
				                     var a=document.createElement('div');
				                     a.className="main-wel-div";
				                    a.innerHTML=' <div style="width: 50px; height: 50px; float: left; border: solid 1px"><img src="http://images.freniz.com/50/50_'+$(this).find('placepic').text()+'" height="50" width="50"/></div><div style="width: 400px; font-weight:bold; font-size:22px; color:#000; margin-left:10px; float: left;"><a style="text-decoration:none" href="http://localhost/freniz_zend/public/'+$(this).find('url').text()+'">'+$(this).find('name').text()+'</a></div><div style="width: 400px;  margin-left:10px; height: 20px;  margin-top:5px; float: left;"><div style="float: left;font-weight:bold; font-size:16px; color:#000;">State:</div><div style="float: left; font-weight:bold; font-size:14px; margin-top:1px; ">'+$(this).find('province').text()+'</div></div><div style="width: 400px;  margin-left:10px; height: 20px; float: left;"><div style="float: left;font-weight:bold; font-size:16px; color:#000;">Country:</div><div style="float: left; font-weight:bold; font-size:14px; margin-top:3px; ">'+$(this).find('country').text()+'</div></div><div style="width: 300px; height: 20px; float: left; margin-left:10px; ">vote:'+$(this).find('votecount').text()+'</div><div style="width: 600px; margin-top:20px; float: left; border:solid 1px #ccc"></div>';
				                    $(b).append(a);
				                 });
				             });
				             $('#search-welcome-results').html(b);
				             change();
							 
				         
				     
				 }
				 
				 
				 
				 function checkusername()
				 {
					 var e=document.getElementById("username");
					 if(e.value.indexOf(' ')==-1 && e.value.length>=6)
				         {
				    	     $.ajax({
				            	 url:'http://localhost/freniz_zend/public/createusr/checkuser?username='+e.value,
				            	 dataType:"json",
				            	 cache:false,
				            	 success:function(json){
				            		 if(json.status=='true')
					                 {
					                     var e=document.getElementById("username");
					                     e.style.background='#A9F5A9';
					                 }
					                 else
					                     {
					                     var e=document.getElementById("username");
					                     e.style.background='red';
					                     }
				            	 }
				            	 
				             
				             });
				    	    
				         }
				         else
				             e.style.background='red';

				 }
				 
				 
				 function checkemail()
				{
					var e=document.getElementById("eid");
					var value=e.value;
					if(value.indexOf(' ')==-1 && value.indexOf('@')!=-1 && value.indexOf('.')!=-1 && value.indexOf('@')!=0 && value.indexOf('@')!=value.length-1 && value.indexOf('.')!=0 && value.indexOf('.')!=value.length-1)
						{
							$.ajax({
								url:"http://localhost/freniz_zend/public/createusr/checkmail?email="+value,
								dataType:"json",
								cache:false,
								success:function(json){
									if(json.status=='true')
									{
										var e=document.getElementById("eid");
										e.style.background="#A9F5A9";

									}
									else
										{
											  e=document.getElementById("eid");
											e.style.background="red";
										}
									}
								});
							
							
						}
					else
						{
							e.style.background='red';
						}
				}

				 
				 function checkpassword()
				 {
				     var e=document.getElementById("password1");
				     var value=e.value;
				     if(value.length<6)
				         e.style.background='red';
				     else
				         e.style.background='#A9F5A9';
				 }
				 function matchpassword()
				 {
				     var e=document.getElementById("password1");
				     var e1=document.getElementById("cpassword");
				     if(e.value==e1.value)
				         e1.style.background='#A9F5A9';
				     else
				         e1.style.background='red';
				 }

				 function createaccount()
				 {
				   var un=document.getElementById("username");
				     var pass=document.getElementById("password1");
				     var cpass=document.getElementById("cpassword");
				     var email=document.getElementById("eid");
				     var fname=document.getElementById("fname");
				     var lname=document.getElementById("lname");
				     var sex=document.getElementById("sex");
				     var bdd=document.getElementById("birthday_day");
				     var bdm=document.getElementById("birthday_month");
				     var bdy=document.getElementById("birthday_year");
				     var sex1;
				     if(sex.value==1)
				       sex1='female';
				     else if(sex.value==2)
				          sex1='male';
				     if(un.style.backgroundColor=='rgb(169, 245, 169)' && pass.style.backgroundColor=='rgb(169, 245, 169)' && cpass.style.backgroundColor=='rgb(169, 245, 169)' && email.style.backgroundColor=='rgb(169, 245, 169)' && fname.value!='' && lname.value!='' && sex.value!=0 && bdd.value!=-1 && bdm.value!=-1 && bdy.value!=-1)
				         {
				         		document.getElementById('light').style.display='block';
   					          document.getElementById('fade').style.display='block';
  				  			var parameters="username="+un.value+"&password="+pass.value+"&confirmpassword="+cpass.value+"&email="+email.value+"&firstname="+fname.value+"&lastname="+lname.value+"&gender="+sex1+"&dob="+bdy.value+"-"+bdm.value+"-"+bdd.value;
				             $.ajax({
				    	 		url:'http://localhost/freniz_zend/public/index/signup',
				    	 		data:parameters,
				    	 		dataType:"json",
				    	 		cache:false,
				    	 		type:"post",
				    	 		success:function(json){
									if(json.status=='true'){
						             login_auth(json.un,json.pass,'http://localhost/freniz_zend/public/index/personalinfo?log_aug=true');
						             }
						             else
						             alert(json.message);
				    	 			}
				    	 		
				    	 	});
				         }
				         else
				             {
				             alert('*** Please fill the mantatory fields ***');
				              

				             }
				 }
				 function createleafaccount()
				 {
				     var pass=document.getElementById("password1");
				     var cpass=document.getElementById("cpassword");
				     var email=document.getElementById("eid");
				     var bdd=document.getElementById("birthday_day");
				     var bdm=document.getElementById("birthday_month");
				     var bdy=document.getElementById("birthday_year");
				     if(pass.style.backgroundColor=='rgb(169, 245, 169)' && cpass.style.backgroundColor=='rgb(169, 245, 169)' && email.style.backgroundColor=='rgb(169, 245, 169)' && bdd.value!=-1 && bdm.value!=-1 && bdy.value!=-1)
				         {
				             var parameters="password="+pass.value+"&confirmpassword="+cpass.value+"&email="+email.value+"&dob="+bdy.value+"-"+bdm.value+"-"+bdd.value;
				             $.ajax({
				    	 		url:'http://localhost/freniz_zend/public/leaf/createleafaccount',
				    	 		data:parameters,
				    	 		dataType:"json",
				    	 		cache:false,
				    	 		type:"post",
				    	 		success:function(json){
				    	 			if(json.status=='true'){
				    	 				login_auth(json.un,json.pass,'leaf/createleaf');
						             }
						             else
						             alert(json.message);
				    	 			}
				    	 		
				    	 	});
				         }
				         else
				             {
				                 var e=document.getElementById("light");
				                 var c=document.createElement("div");
				                 c.style.height="20px";
				                 c.style.width=e.style.width;
				                 c.style.color='red';
				                 c.innerHTML="*** Please fill the mantatory fields ***";
				                 e.appendChild(c);

				             }
				 }
				
				
			function personalinfo()            
            {
				$('#basicload').css('display','block');
				var body=document.getElementById('body_tpe').value;
                var look=document.getElementById('look_tpe').value;
                var smoke=document.getElementById('smoke_tpe').value;
                var drink=document.getElementById('drink_tpe').value;
                var passion=document.getElementById('passion_tpe').value;
                var pet=document.getElementById('pet_tpe').value;
                var ethnicity=document.getElementById('ethnicity_tpe').value;
                var sexual=document.getElementById('sexual_tpe').value;
                var humor=document.getElementById('humor_tpe').value;
                var parameters="";
                if(body!= '')
                    parameters+="body="+body;
                if(look!= '')
                    parameters+="&look="+look;
                if(smoke!= '')
                    parameters+="&smoke="+smoke;
                if(drink!= '')
                    parameters+= "&drink="+drink;
                if(passion!= '')
                    parameters+="&passion="+passion;
                if(pet!= '')
                    parameters+="&pets="+pet;
                if(ethnicity!= '')
                    parameters+="&ethnicity="+ethnicity;
                 if(sexual!= '')
                    parameters+= "&sexual="+sexual;
                 if(humor!= '')
                    parameters+= "&humor="+humor;
             $.ajax({
              url:'http://localhost/freniz_zend/public/updatepersonalinfo',
              data:parameters,
              cache:false,
              dataType:"json",
              type:"post",
              success:function(json){
				$('#basicload').css('display','none');
				
				var span=document.createElement('span');
				span.id='basic-info-message';
				if(json.status=='success')
					span.innerHTML='Hey your basic info sucessfully updated :)';
				else
					span.innerHTML='There is an Error';
				$('#basic-details').append(span);
				setTimeout(function(){$('#basic-info-message').remove();},10000);
              }
              
              });
              
              
                
            }
            
            
            
            
            
            
            
            
            
            function createpage(pagename,category,subcategory,type,fav,option)
			{
				if(category!=0)
					{
				if(!subcategory){
					subcategory=category;
				}
				if(!type)
					type='default'
					var parameters="type="+type+"&leafname="+pagename+"&category="+category+"&subcategory="+subcategory;
					if(fav)
					parameters+="&fav="+fav;
					$('#loading').css('display','block');
					$.ajax({
						url:'http://localhost/freniz_zend/public/leaf/create',
						data:parameters,
						dataType:'json',
						cache:false,
						success:function(json){
							if(option){ option.callback(json);} 
							else {
								$('#loading').css('display','none');
				var span=document.createElement('span');
				span.id='favr-info-message';
				span.className='span-res';
					if(json.status=='success')
					span.innerHTML='Hey your favourite created sucessfully updated :)';
				else
					span.innerHTML='sorry! There is an Error , please try again later';
				$('#favorite-details').append(span);
				var main=document.getElementById('favor-list');
				var a=document.createElement('div');
				 a.innerHTML='<div id="sug-'+json.leafid+'" class="show"style=" width:100px; margin-top:5px; margin-left:5px;float:left; position:relative; height:100px; border:solid 1px"><a id="add-fav'+json.leafid+'" class="fav-hidden"style="margin:20px; font-size:18px;text-decoration:none; font-weight:bold"onClick="addtofavr(\''+json.leafid+'\',\''+category+'\')"href="javascript:void(0)">Add</a><a id="remo-fav'+json.leafid+'" class="fav-show"style="margin:20px; font-size:18px;text-decoration:none; font-weight:bold"onClick="removefromfavr(\''+json.leafid+'\',\''+category+'\')"href="javascript:0">remove</a><div style=" width:100px; position:absolute; bottom:0; opacity:0.6; color:#000;  filter:alpha(opacity=60);">'+json.name+'</div></div>';
					 main.appendChild(a); 
					setTimeout(function(){$('#favr-info-message').remove();},10000);
				
							}
						}
						});
					}
			}
			
	 
		 function addfavorites(pageids,category,type)
				 {
					 $('#fav-load').css('display','block');
						var url;
					 if(type=='school' || type=='college' || type=='employer')
						url='http://localhost/freniz_zend/public/updatetofavorites?pageids='+pageids+'&category='+category+'&type='+type+"&from=2007&end=2011";
					 else
						url='http://localhost/freniz_zend/public/updatetofavorites?pageids='+pageids+'&category='+category+'&type='+type;
						 if(pageids!=0){
				    	 $.ajax({
		 	              	  type: 'get',
		 	              	  url: url,
		 	              	  cache:false,
		 	              	  success: function(json){
									$('#fav-load').css('display','none');
				var span=document.createElement('span');
				span.id='favr-info-message';
				span.className='span-res';
					if(json.status=='success')
					span.innerHTML='Hey your favourite info sucessfully updated :)';
				else
					span.innerHTML='sorry! There is an Error , please try again later';
				$('#favorite-details').append(span);
				setTimeout(function(){$('#favr-info-message').remove();},10000);  
					      		},
		 	              	  dataType: "json"
		 	              	}); 
				    
				 }
				
				 }
				 
				 
	 
				 function getfavsug(){
						 var key=document.getElementById('favourites-update').value;
					 var category=document.getElementById('favorite-change').value;
					var url='http://localhost/freniz_zend/public/search?type=page&key='+key;
					if(category!='other')
					url+='&category='+category;
					$.ajax({
	 	              	  type: 'get',
	 	              	  url: url,
	 	              	  cache:false,
	 	              	  success: function(xml){
	 	              		 
	 	              	getsugfav(xml,category);},
	 	              	  dataType: "xml"
	 	              	}); 
			    
				 }
				 function getsugfav(xml,category){
					
					 var main=document.getElementById('suggestion-list');
					  var b=document.createElement('div');
					  $(xml).find('pages').each(function(){
			                 $(this).find('page').each(function(){
								 
			                	if(!document.getElementById('sug-'+$(this).find('pageid').text())){
			                  	 var a=document.createElement('div');
								 a.innerHTML='<div id="sug-'+$(this).find('pageid').text()+'" class="show"style="background-image:url(\'http://images.freniz.com/75/75_'+$(this).find('pagepic').text()+'\'); background-repeat:no-repeat;  width:100px; overflow:hidden; margin-top:5px; margin-left:5px;float:left; position:relative; height:100px; border:solid 1px"><a id="add-fav'+$(this).find('pageid').text()+'" class="fav-show"style="margin:20px; font-size:18px;text-decoration:none; font-weight:bold"onClick="addtofavr(\''+$(this).find('pageid').text()+'\',\''+category+'\')"href="javascript:void(0)">Add</a><a id="remo-fav'+$(this).find('pageid').text()+'" class="fav-hidden"style="margin:20px; font-size:18px;text-decoration:none; font-weight:bold"onClick="removefromfavr(\''+$(this).find('pageid').text()+'\',\''+category+'\')"href="javascript:0">remove</a><div style=" width:100px; position:absolute; bottom:0; opacity:0.6; background-color:#ccc; color:#000;  filter:alpha(opacity=60);"><a style="text-decoration:none; color:#444" href="http://localhost/freniz_zend/public/'+$(this).find('pageid').text()+'">'+$(this).find('pagename').text()+'</a></div></div>';
								 b.appendChild(a); 
							 }
			                 });
					  });
					  $(main).html(b);
			                 }
				
				
				function createfav(){
					
					var type=document.getElementById("favorite-change").value;
					var name=document.getElementById("favourites-update").value;
					switch(type)
					{
						case 'Books':
						createpage(name,type,'book','default',type);
						break;
						case 'Musics':
						createpage(name,type,'Music','default',type);
						break;
						case 'Movies':
						createpage(name,type,'Movie','default',type);
						break;
						case 'Celebrities':
						alert(name);
						createpage(name,type,'public figure','default',type);
						break;
						case 'Games':
						createpage(name,type,'Game','default',type);
						break;
						case 'Sports':
						createpage(name,type,'sport','default',type);
						break;
						case 'other':
						createpage(name,type,'other','default',type);
						break;
						
					}
				} 
				
           function addtofavr(id,category){
                    $('#remo-fav'+id).addClass('fav-show');
					$('#remo-fav'+id).removeClass('fav-hidden');
					 $('#add-fav'+id).removeClass('fav-show');
					$('#add-fav'+id).addClass('fav-hidden');
					$('#sug-'+id).appendTo('#favor-list');
					$('#favorite-change').remove('#sug-'+id);
					addfavorites(id,category,'add');	
				 }
			function removefromfavr(id,category){
					
					$('#sug-'+id).addClass('fav-hidden');
					addfavorites(id,category,'remove');
			}
			
			function addEducationInfo(type){
				var pageids='';
				$('#'+type).children().each(function(){
					if(pageids=='')
					pageids=this.id;
					else
					pageids+=','+this.id;
				});
				if(type=='work')
				type='employer';
				addfavorites(pageids,type,'update');
			}
			function updatemood(){
				var id=$('#ccuserid').val();
				var mood=$('#mood-image').attr('alt');
				var description=$('#mood-description').val();
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/settings/updatemood?mood='+mood+'&description='+description,
		    		    cache:false,
		    		    dataType:"json",
		    		    type:'post',
		    		    success:function(json){
		    		    	$('#mood-smile').css('display','none');
		    		    	$('#top-smiley').attr('src','http://localhost/freniz_zend/public/images/mood/32/'+json.mood);
		    		    if(id==json.id){
		    		    	var main =document.getElementById('change-smiley');
		    		    	main.innerHTML='<img id="smileypic" alt="'+json.mood+'" style="float:left" src="http://images.freniz.com/mood/32/'+json.mood+'" height="50" width="50"/><span id="mood-desc" style="width:130px; margin-left: 5px;">'+json.description+'</span>';
		    		    }
		    		    $("#mood-set-div").css("display","none");
		    		    }
		 		    } );
		 		 
		 	 }
			
			function updatehometown(){
				$('#loading-hometown').css('display','block');
				var city=document.getElementById('search-place-update-hidden').value;
				$.ajax({
					url:"http://localhost/freniz_zend/public/settings/updatecity?type=hometown&city="+city,
					dataType:'json',
					cache:false,
					success:function(json){
						
					$('#loading-hometown').css('display','none');
				
				var span=document.createElement('span');
				span.id='hometown-info-message';
				if(json.status=='success')
					span.innerHTML='Hey your hometown updated successfully :)';
				else
					span.innerHTML='sorry ! There is an Error, please try again later';
				$('#home-details').append(span);
				setTimeout(function(){$('#hometown-info-message').remove();},5000);
				
              
					}
					
					});
			}
			function updatelivingin(){
				$('#loading-livingin').css('display','block');
				
				var city=document.getElementById('search-cc-place-update-hidden').value;
				$.ajax({
					url:"http://localhost/freniz_zend/public/settings/updatecity?type=currentcity&city="+city,
					dataType:'json',
					cache:false,
					success:function(json){
						$('#loading-livingin').css('display','none');
				
				var span=document.createElement('span');
				span.id='livingin-info-message';
				if(json.status=='success')
					span.innerHTML='Hey your hometown updated successfully :)';
				else
					span.innerHTML='sorry ! There is an Error, please try again later';
				$('#livingin-details').append(span);
				setTimeout(function(){$('#livingin-info-message').remove();},5000);
             
					}
					
					});
			}
			function changemood(mood){
				$('#mood-image').attr('src','http://localhost/freniz_zend/public/images/mood/32/'+mood);
				$('#mood-image').attr('alt',mood);
				$('#mood-description').val('');
				$('#mood-smile div').css('display','block');
			}
			
			
			
			function pinpeople(imageid){
				//alert("http://localhost/freniz_zend/public/image/addpin/imageid/"+imageid+"?userids="+pinpeoples);
				var pinnedpeople='';
				$('#'+imageid+"_pinnedpeople_list").children('div').each(function(){
					pinnedpeople+=$(this).attr('name')+',';
				});
				if(pinnedpeople.length>0){
					pinnedpeople=pinnedpeople.slice(0, -1);
				}
				$.ajax({
					url:"http://localhost/freniz_zend/public/image/addpin/imageid/"+imageid+"?userids="+pinnedpeople,
					dataType:"json",
					cache:false,
					success:function(json){
						alert('success');
					}
				});
				
			}
			function adddescription(){
				var imageid=document.getElementById("imgid").value;
				var text=document.getElementById("edit-desc-text").value;
				if(text!=''){
		        	  var parameters="text="+text;
		        	   $.ajax({
	              	  type: 'POST',
	              	  url: 'http://localhost/freniz_zend/public/image/adddescription/imageid/'+imageid,
	              	  data: parameters,
	              	  success: function(json){
	              		$('#image-desc').html(text.trim());
	              	  },
	              	  dataType: "json"
	              	});
		        	  
		        	}
			}
			function pinme(imageid){
				$.ajax({
					url:"http://localhost/freniz_zend/public/image/pinmereq/imageid/"+imageid+"/",
					dataType:"json",
					cache:false,
					success:function(json){
								alert('success');
							}
					});
			}
			function unpin(imageid,userid){
				$.ajax({
					url:"http://localhost/freniz_zend/public/image/unpin/imageid/"+imageid+"/userid/"+userid+"/",
					dataType:"json",
					cache:false,
					success:function(json){
						alert('success');
							}
					});
			}
			function voteimage(imageid,element){
				 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/image/voteimage/imageid/'+imageid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	 if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=1+parseInt(innerHtml.substr(pos1,length));
		    		    		 $(element).parent().html('<a onclick="unvoteimage('+imageid+',this)"style="float:right; font-weight:bold; text-decoration:none; color:#000"href="javascript:void(0)">winked( '+votecount+' )</a>');
		    		    	 }
		    		    		
		    		    }
		 		    } );
		 		 
		 	 }
			function deleteimage(imageid){
				 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/image/deleteimages/imageid/'+imageid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	 location.reload();
		    		    		
		    		    }
		 		    } );
		 		 
		 	 }
		 	 function unvoteimage(imageid,element){
			 		
		 		 $.ajax({
		    		    url:'http://localhost/freniz_zend/public/image/unvoteimage/imageid/'+imageid,
		    		    cache:false,
		    		    dataType:"json",
		    		    success:function(json){
		    		    	if(json.status=='success'){
		    		    		 var innerHtml=$(element).html();
		    		    		 var pos1=innerHtml.indexOf('(')+2;
		    		    		 var pos2=innerHtml.indexOf(')')-1;
		    		    		 var length=pos2-pos1;
		    		    		 var votecount=parseInt(innerHtml.substr(pos1,length))-1;
		    		    		 $(element).parent().html('<a onclick="voteimage('+imageid+',this)"style="float:right; font-weight:bold; text-decoration:none;color:#000; "href="javascript:void(0)">wink( '+votecount+' )</a>');
		    		    	 }
		    		    }
		 		    } );
		 		 
		 	 }
			
			function getmessages()
			{
				
				$.ajax({
					url:'http://localhost/freniz_zend/public/messages?format=xml',
					cache:false,
					dataType:"xml",
					success:function(xml){mymessages(xml);}
				});

			}
			function mymessages(xml)
			{
				var main=document.getElementById('primarydiv');
				 $(xml).find('messages').each(function(){
			         $(this).find('message').each(function(){
			             var a=document.createElement('div');
			             a.style.width="540px";
			             a.id="message-"+$(this).find('suserid').text();
							var top=document.createElement('a');
							top.style.cssFloat="left";
							top.href='http://localhost/freniz_zend/public/messages/'+$(this).find('suserid').text();
							  top.innerHTML='<div style="width:500px; border-bottom:solid 1px; padding:10px;float:left; height: 50px; "><div style="width:50px; float:left; height: 50px;"><img width="50" height="50" src="http://images.freniz.com/50/50_'+$(this).find('propic').text()+'"/></div><div style="width:435px; margin-left:5px; padding:5px; float:left; height: 40px; "><label style="font-size:19px; font-weight: bold"><a href="http://localhost/freniz_zend/public/'+$(this).find('suserid').text()+'"style="text-decoration:none; float:left;padding-right:5px; font-weight: bold; color:#000; font-size:20px;">'+$(this).find('from').text()+'</a></label><div style="float:left;color:#ccc;margin-top:3px;" class="timeago" title="'+$(this).find('date').text().trim()+'">'+$(this).find('date').text()+'</div><br/><br/><div style=" width:20px; height:20px; float:left; padding:2px; margin-top:-10px; border:solid 1px"></div><span style="float:left; margin-left:3px">'+$(this).find('msg').text()+'</span</div></div>';

								 $(a).append(top);
								 var b=document.createElement('div');
								 b.style.cssFloat="left";
								 b.innerHTML='<a onclick="deleteallmessages(\''+$(this).find('suserid').text()+'\')" href="javascript:void(0)"style="text-decoration:none;float:left; margin-top:-5px; margin-left:-15px; color:#444; padding:3px;">x</a><span style=" color:#444; margin-top:20px; margin-left:-15px; float:left; padding:3px;">o</span>';
								 $(a).append(b);
								 $(main).append(a);
			         });
				 });
				 prettyLinks(); 
				        
			       
			}
			function getusermessages(userid)
			{
				$.ajax({
					url:"http://localhost/freniz_zend/public/messages/"+userid+"?format=xml",
					cache:false,
					dataType:"xml",
					success:function(xml){usermessages(xml);}
				});
				
			}
			function usermessages(xml)
			{
				var main=document.getElementById('primarydiv');
				 $(xml).find('messages').each(function(){
			         $(this).find('message').each(function(){
			        	 var top=document.createElement('div');
			        	 var a=document.createElement('div');
			        	 if($(this).find('suserid').text()==$(this).find('myid').text()){
			        		 a.innerHTML='<div id="'+$(this).find('id').text()+'" style="width:490px; border-bottom:solid 1px #ccc;float:left; padding:20px 20px 5px 20px;"><div style="width:32px; height:32px; float:left;"><img width="32" height="32" src="http://images.freniz.com/32/32_'+$(this).find('propic').text()+'"/></div><span id="left-mess">'+$(this).find('msg').text()+'</span><a href="javascript:void(0)"style="float:right; color:#ccc;text-decoration:none; font-weight:bold">x</a><div style="float:right; width:500px;"><span style="float:left;color:#ccc; margin-top:3px;font-size:14px;"title="'+$(this).find('date').text()+'" class="timeago">'+$(this).find('date').text()+'</span></div></div>';
			        	 }else{
			        		 a.innerHTML='<div id="'+$(this).find('id').text()+'" style="width:490px; border-bottom:solid #ccc 1px; float:left; padding:20px 20px 5px 20px;"><div style="width:32px; height:32px; float:right;"><img width="32" height="32" src="http://images.freniz.com/32/32_'+$(this).find('propic').text()+'"/></div><span id="right-mess">'+$(this).find('msg').text()+'</span><a href="javascript:void(0)"style="float:left; color:#ccc; text-decoration:none;  font-weight:bold">x</a><div style="float:right; width:500px;"><span style="float:right;color:#ccc; margin-top:3px; font-size:14px;"title="'+$(this).find('date').text()+'" class="timeago">'+$(this).find('date').text()+'</span></div></div>';
			        	 }
			        	 $(top).append(a);
			        	 $(main).append(top);
			        	 
			         });
				 });
				 var heit=($(window).height()-260);
			        $('#primarydiv').css('max-height',heit+'px');
				 prettyLinks(); 
			
			}
			function sendmessageuser(userid,e){
				var text;
				if(e)
					{
					var keynum;
				if(window.event) // IE8 and earlier
					{
					keynum = e.keyCode;
					}
				else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
					{
					keynum = e.which;
					}
				
				        if(keynum!=13){
				        	
				        	return;
				        }
				        text=$('#send-text').val();
				}
				else
					text=$('#mess-cont').val();
				        if(text!=''){
		 		        	  var parameters="userid="+userid+"&message="+text;
		 		        	   $.ajax({
		 	              	  type: 'POST',
		 	              	  url: 'http://localhost/freniz_zend/public/sendmessages',
		 	              	  data: parameters,
		 	              	  success: function(json){
		 	              		if(e){
		 	              		if(json.status=='success'){
		 	              			var main=document.getElementById('primarydiv');
		 	              		var a='<div id="'+json.messageid+'" style="width:490px; border-bottom:solid 1px #ccc;float:left; padding:20px 20px 5px 20px;"><div style="width:32px; height:32px; float:left;"><img width="32" height="32" src="http://images.freniz.com/32/32_'+json.propic+'"/></div><span id="left-mess">'+json.content+'</span><a href="javascript:void(0)"style="float:right; color:#ccc;text-decoration:none; font-weight:bold">x</a><div style="float:right; width:500px;"><span style="float:left;color:#ccc; margin-top:3px;font-size:14px;"title="'+json.time+'" class="timeago">'+json.time+'</span></div></div>';
		 	              		$(main).append(a);
		 	              		
		 	              		}else{
		 	              			alert(json.error);
		 	              		}
		 	              		}else {
		 	              		$('#mess-inner').remove();
		 	              		}
		 	              		$('#'+json.messageid).focus();
		 	              		
		 	              		
		 	              	 prettyLinks(); 
		 	              		  },
		 	              	  dataType: "json"
		 	              	});
		 	                document.getElementById('send-text').value=''; 
		 		        	}


				}
			
			 function deleteallmessages(userid){
		    	  $.ajax({
		  		    url:'http://localhost/freniz_zend/public/deletemessage?userid='+userid,
		  		    cache:false,
		  		    dataType:"json",
		  		    success:function(json){
		  		    	if(json.status=='success')
		  		    		$('#message-'+userid).remove();
		  		    	else alert(json.status);
		  		    	
		  		    }
				    } );
		    	  
		      }
			 function switchuser(id){
	    		  $.ajax({
			  		    url:'http://localhost/freniz_zend/public/index/switch?id='+id,
			  		    cache:false,
			  		    dataType:"json",
			  		    success:function(json){
			  		    	if(json.status=='success')
			  		    		window.location.href="http://localhost/freniz_zend/public/"+id;
			  		    	else alert(json.status);
			  		    	
			  		    }
					    } );
	    	  }
			 function addtolist(id){
	    		  $.ajax({
			  		    url:'http://localhost/freniz_zend/public/leaf/addtags?tagid='+id,
			  		    cache:false,
			  		    dataType:"json",
			  		    success:function(json){
			  		    	alert(json.status);
			  		    }
					    } );
	    	  }
			 function accepttolist(id){
				var leafid= $('#search-tags').attr('data');
	    		  $.ajax({
			  		    url:'http://localhost/freniz_zend/public/leaf/accepttags?tagid='+id,
			  		    cache:false,
			  		    dataType:"json",
			  		    success:function(json){
			  		    	if(json.status=='success'){
			  		    	getleaftaglist(leafid);
			  		    	$("#req-"+id).remove();
			  		    	}else alert(json.status);
			  		    }
					    } );
	    	  }
			 
			 function getleaftaglist(leafid)
				{
					$.ajax({
						url:"http://localhost/freniz_zend/public/leaf/getleaftaglist?leafid="+leafid,
						cache:false,
						dataType:"xml",
						success:function(xml){taglist(xml);}
					});
					
				}
			 function taglist(xml){
				 var main=document.getElementById('taglistdiv');
				 var top=document.createElement('div');
				 $(xml).find('pages').each(function(){
			         $(this).find('page').each(function(){
			        	 var a=document.createElement('div');
			        	 $(a).css({'width':'250px','margin':'10px','padding':'5px','float':'left','border':'solid 1px'});
			        	 a.innerHTML='<div style="width:50px; height:50px; float:left; border:solid 1px"><img src="http://images.freniz.com/50/50_'+$(this).find('pagepic').text()+'"></div>';
			        	var b=document.createElement('div');
			        	$(b).css({'width':'180px','padding':'5px','margin-left':'5px','height':'50px','float':'left'});
			        	if($(this).find('votecontain').text()=='yes')
			        	b.innerHTML='<label><a href="http://localhost/freniz_zend/public/'+$(this).find('leafurl').text()+'">'+$(this).find('leafname').text()+'</a></label><br/><span><a onclick="addfavorites(\''+$(this).find('leafid').text()+'\',\''+$(this).find('category').text()+'\',\'add\')">winked( '+$(this).find('vote_count').text()+' )</a></span>';
			        	else
			        	b.innerHTML='<label><a href="http://localhost/freniz_zend/public/'+$(this).find('leafurl').text()+'">'+$(this).find('leafname').text()+'</a></label><br/><span><a onclick="addfavorites(\''+$(this).find('leafid').text()+'\',\''+$(this).find('category').text()+'\',\'remove\')">wink( '+$(this).find('vote_count').text()+' )</a></span>';
			        	$(a).append(b);
			        	$(top).append(a);
			         });
				 });
				 $(main).html(top);
			//	 getrequesttags();
			         }
			 function searchtags(key,leafid){
				 $.ajax({
						url:"http://localhost/freniz_zend/public/leaf/searchtags?leafid="+leafid+"&key="+key,
						cache:false,
						dataType:"xml",
						success:function(xml){tagsearch(xml);}
					});
			 }
			 function tagsearch(xml){
				 var main=document.getElementById('taglistdiv');
				 var top=document.createElement('div');
				 $(xml).find('pages').each(function(){
			         $(this).find('page').each(function(){
			        	 var a=document.createElement('div');
			        	 $(a).css({'width':'250px','margin':'10px','padding':'5px','float':'left','border':'solid 1px'});
			        	 a.innerHTML='<div style="width:50px; height:50px; float:left; border:solid 1px"><img src="http://images.freniz.com/50/50_'+$(this).find('pagepic').text()+'"></div>';
			        	var b=document.createElement('div');
			        	$(b).css({'width':'180px','padding':'5px','margin-left':'5px','height':'50px','float':'left'});
			        	if($(this).find('votecontain').text()=='yes')
			        	b.innerHTML='<label><a href="http://localhost/freniz_zend/public/'+$(this).find('leafurl').text()+'">'+$(this).find('leafname').text()+'</a></label><br/><span><a onclick="addfavorites(\''+$(this).find('leafid').text()+'\',\''+$(this).find('category').text()+'\',\'add\')">winked( '+$(this).find('vote_count').text()+' )</a></span>';
			        	else
			        	b.innerHTML='<label><a href="http://localhost/freniz_zend/public/'+$(this).find('leafurl').text()+'">'+$(this).find('leafname').text()+'</a></label><br/><span><a onclick="addfavorites(\''+$(this).find('leafid').text()+'\',\''+$(this).find('category').text()+'\',\'remove\')">wink( '+$(this).find('vote_count').text()+' )</a></span>';
			        	$(a).append(b);
			        	$(top).append(a);
			         });
				 });
				 $(main).html(top);
			    }
			 function getrequesttags(){
				 $.ajax({
						url:"http://localhost/freniz_zend/public/leaf/getrequesttag",
						cache:false,
						dataType:"xml",
						success:function(xml){requesttag(xml);}
					});
			 }
			 function requesttag(xml){
				 var main=document.getElementById('request-tags');
				 $(xml).find('pages').each(function(){
					 if($(this).find('norequests').text()=='no'){
						 main.innerHTML=' <label style="font-size:20px; border-bottom:solid 1px">Requested list</label>';
			         $(this).find('page').each(function(){
			        	 var a=document.createElement('div');
			        	 a.id="req-"+$(this).find('leafid').text();
			        	 $(a).css({'width':'230px','margin':'3px','padding':'5px','float':'left','border-bottom':'solid 1px'});
			        	 a.innerHTML='<div style="width:50px; height:50px; float:left; border:solid 1px"><img src="http://images.freniz.com/50/50_'+$(this).find('pagepic').text()+'"></div>';
			        	var b=document.createElement('div');
			        	$(b).css({'width':'160px','padding':'5px','margin-left':'5px','height':'50px','float':'left'});
			        	b.innerHTML='<label><a href="http://localhost/freniz_zend/public/'+$(this).find('leafurl').text()+'">'+$(this).find('leafname').text()+'</a></label><br/><span><a onclick="accepttolist(\''+$(this).find('leafid').text()+'\');">Accept</a></span>';
			        	$(a).append(b);
			        	 $(main).append(a);
			         });
					 }
				 });
				 
			    }
			 function getminiprofile(id,top,left){
				 var left;
				 	if(left<700)
				 		left=(left+50);
				 	else
				 		left=(left-350);
				 var top=(top-150);
				 var main=document.getElementById('hover-data');
				      	 var a=document.createElement('div');
			        	 a.className='hover-div';
			        	 $(a).css({'position':'absolute','background':'#fff','width':'350px','padding':'10px','float':'left','border':'solid 1px','top':(top+120),'left':left});
			        	 a.innerHTML='Loading...';
			        	$(main).html(a);
			        
				 $.ajax({
						url:"http://localhost/freniz_zend/public/index/getminiprofile?id="+id,
						cache:false,
						dataType:"xml",
						success:function(xml){getmini(xml,top,left);}
					});
			 }
			 function getmini(xml,top,left){
				 var main=document.getElementById('hover-data');
				 $(xml).find('profile').each(function(){
			         $(this).find('user').each(function(){
			        	 var a=document.createElement('div');
			        	 a.className='hover-div';
			        	 $(a).css({'position':'absolute','background':'#fff','width':'350px','padding':'10px','float':'left','border':'solid 1px','height':'130px','top':top,'left':left});
			        	 a.innerHTML='<div style="width:100px; height:100px; float:left;"><img src="http://images.freniz.com/75/75_'+$(this).find('propic_url').text()+'" width="100"/></div><div style="width:230px; padding:3px; margin-left:5px; float:left;"><label style="font-weight:bold;float:left; font-size:18px"><a href="http://localhost/freniz_zend/public/'+$(this).find('url').text()+'">'+$(this).find('username').text()+'</a></label></div>';
			        	var a1=document.createElement('div');
			        	 $(a1).css({'width':'230px','padding':'5px','float':'left','margin-left':'5px','height':'40px'});
			        	 $(this).find('friends').each(function(){
				            	$(this).find('friend').each(function(){
			        	 var aa=document.createElement('a');
			        	 aa.href="http://localhost/freniz_zend/public/"+$(this).find('friendurl').text();
			        	 $(aa).css({'cursor':'pointer','text-decoration':'none'});
			        	 aa.title=$(this).find('fusername').text();
			        	 aa.innerHTML='<img src="http://images.freniz.com/50/50_'+$(this).find('imgurl').text()+'"height="32"width="32"/>';
				            	$(a1).append(aa);
				            	});
			        	 });
			        	if($(this).find('ismyid').text()=='no' && $(this).find('type').text()=='user'){
			        			var b=document.createElement('div');
			        	var b1=document.createElement('span');
			        	$(b1).css({'float':'right','font-size':'12px','margin-top':'10px'});
			        	b1.id='leaf-vote';
			        	b1.className='mes-text';
			        	b1.innerHTML='send text';
			        	 $(b).append(b1);
			        	  var b2=document.createElement('a');
				        	$(b2).css({'float':'right','font-size':'12px','margin-top':'10px','margin-right':'5px'});
				        	b2.id='leaf-vote';
				        	if($(this).parent().find('isfriends').text()=='no'){
				        	var id=$(this).parent().find('id').text();
				        	 $(b2).click(function(){addfriends(id)});
				        	b2.innerHTML='Add request';
				        	}else
				        		b2.innerHTML='Friends';
				        	 $(b).append(b2);	
				        		var b4=document.createElement('div');
			        	 var b4='<div id="mes-text" style="width:320px; display:none; position:absolute; margin-top:50px; margin-left:10px; float:left; padding:5px; background-color:#ccc; border:solid 1px"><div style="width:240px; padding-left:5px; margin-left:5px; float:left;  border-left:solid 1px #fff"><textarea id="messag-text" style="width:240px; outline:none; height:35px"placeholder="write a message to your friends"></textarea></div><input class="greenbutton" style="float:right; margin-top:7px" type="button" value="send" onclick="sendmsguser(\''+$(this).find('userid').text()+'\',this)"/></div>';
			        	 $(b).append(b4);
			        	$(a1).append(b);
			        	}
			        	$(a).append(a1);
			        	$(main).html(a);
			         });
					 
				 });
			 }
			 function getleafalbum(leafid){
				 $.ajax({
						url:"http://localhost/freniz_zend/public/leaf/getleafalbum?leafid="+leafid,
						cache:false,
						dataType:"xml",
						success:function(xml){leafalbum(xml);}
					});
			 }
			 function leafalbum(xml){
				 var main=document.getElementById('leaf-gall');
				var top=document.createElement('div');
				top.className='pikachoose';
				$(top).css({'margin-left':'-20px'});
				var ui=document.createElement('ul');
				ui.id='pikame';
				ui.className='jcarousel-skin-pika';
				
				 $(xml).find('albums').each(function(){
			         $(this).find('album').each(function(){
			        	 $(this).find('images').each(function(){
				            	$(this).find('image').each(function(){
			        	 var a=document.createElement('li');
			        	 a.innerHTML='<a href="javascript:void(0)"><img src="http://images.freniz.com/'+$(this).find('imageurl').text()+'"/></a><span>'+$(this).find('imagename').text()+'</span>';
				            	$(ui).append(a);
				            	});
			        	 });
			         });
				 });
				
				 $(top).append(ui);
				 $(main).append(top);
				 $("#pikame").PikaChoose({carousel:true, carouselVertical:true});
			 }
			 function createalbumname(){
				 var albumname=$('#album-name').val();
				 $.ajax({
						url:"http://localhost/freniz_zend/public/albums/createalbum?name="+albumname,
						cache:false,
						dataType:"json",
						success:function(json){
							$('#album-na').remove();
							var span=document.createElement('div');
		    		    	$(span).css({'position':'absolute','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
		    		    	span.id='alert-span';
		    				if(json.status=='success')
		    					span.innerHTML='Hey your request sent successfully please be waited :)';
		    				else
		    					span.innerHTML='There is an Error';
		    					$('#album-div').append(span);
		    				setTimeout(function(){$('#alert-span').remove();},3000);
						}
					});
			 }
			 function getwallphoto(id){
				 $.ajax({
						url:"http://localhost/freniz_zend/public/leaf/getwallphotos?id="+id,
						cache:false,
						dataType:"xml",
						success:function(xml){getwallpho(xml);}
					});
			 }
			 function getwallpho(xml){
				 var main=document.getElementById('slider1Content');
				 $(xml).find('images').each(function(){
			         $(this).find('image').each(function(){
			        	 var a=document.createElement('li');
			        	 a.className='slider1Image';
			        	 a.innerHTML=' <a href="javascript:void(0)"><img width="625" src="http://images.freniz.com/'+$(this).find('url').text()+'" alt="'+$(this).find('id').text()+'" /></a><span class="bottom"><strong>'+$(this).find('description').text()+'</strong>';
			        	$(main).append(a);
			         });
				 });
				 $b='<div class="clear slider1Image"></div>';
				 $(main).append($b);
				 $('#slider1').s3Slider({
			         timeOut: 4000 
			     });				 
			    }
			 function getfriendslist(userid){
				 var main=document.getElementById('com-container');
					var top=document.createElement('div');
					top.id='alert-span';
					$(top).css({'max-height':'400px','overflow':'auto','position':'absolute','width':'320px','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
					   top.innerHTML='Loading...';
				        	   	 $(main).append(top);
				 $.ajax({
						url:"http://localhost/freniz_zend/public/profile/getfriendslist?userid="+userid,
						cache:false,
						dataType:"xml",
						success:function(xml){
							var main=document.getElementById('com-container');
							var top=document.createElement('div');
							top.id='alert-span';
							$(top).css({'max-height':'400px','overflow':'auto','position':'absolute','width':'320px','top':'40%','left':'40%','background-color':'#fff','border':'solid 1px','padding':'25px'});
							 $(xml).find('users').each(function(){
						         $(this).find('user').each(function(){
						        	 var a1=document.createElement('div');
						        	 $(a1).css({'float':'left'});
						        	 var a=document.createElement('div');
						        	 a.innerHTML='<div style="width:320px; padding:5px; float:left; border-bottom:solid 1px"><div style="width:50px; height:50px; float:left;"><img src="http://images.freniz.com/50/50_'+$(this).find('suserpic').text()+'" alt="'+$(this).find('userid').text()+'" /></div><div style="width:240px; padding:5px; margin-left:5px; height:50px;  float:left; "><label style="font-weight:bold;font-size:18px"><a href="http://localhost/freniz_zend/public/'+$(this).find('userid').text()+'">'+$(this).find('username').text()+'</a></label><br/><span>mutual:'+$(this).find('mutual').text()+'</span><span class="add-span'+$(this).find('userid').text()+'" onclick="addfriends(\''+$(this).find('userid').text()+'\');" id="span-change" style="float:right">Add</span><span class="friend-span'+$(this).find('userid').text()+'" id="span-change" style="float:right">friend</span></div></div>';
						        	 $(a1).append(a);
						        	 $(top).append(a1);
						        	 if($(this).find('friend').text()=='yes')
						        		 $('#add-span'+$(this).find('userid').text()).remove();
						        	 else
						        		 $('#friend-span'+$(this).find('userid').text()).remove();
						         });
							 });
							 $(main).append(top);
							 $(xml).find('users').each(function(){
						         $(this).find('user').each(function(){
						        	 if($(this).find('friend').text()=='yes')
						        		 $('.add-span'+$(this).find('userid').text()).remove();
						        	 else
						        		 $('.friend-span'+$(this).find('userid').text()).remove();
						      
						         });
							 });
							 $b='<span style="float:right;" onclick="$(\'#alert-span\').remove()" id="leaf-vote">Done</span>';
							 $(top).append($b);
							
							
							
						}
					});
			 }
			 
			function basicaccount(){
				var fname=$('#fname').val();
				var lname=$('#lname').val();
				var bdd=$('#bdd').val();
				var bdy=$('#bdy').val();
				var bdm=$('#bdm').val();
				var sex=$('#sex').val();
				var rstatus=$('#status').val();
				var skills='';
				var religion=$('#religious').val();
				 $('#tag-div').children('div').each(function(){
						skills+=$(this).attr('name')+',';
					});
					if(skills.length>0){
						skills=skills.slice(0, -1);
					}
				parameters="fname="+fname+"&lname="+lname+"&bdd="+bdd+"&bdy="+bdy+"&bdm="+bdm+"&sex="+sex+"&religion="+religion+"&rstatus="+rstatus+"&skills="+skills;
				$.ajax({
					url:"http://localhost/freniz_zend/public/settings/updatebasicinfo",
					data:parameters,
					dataType:"json",
					method:"post",
					cache:false,
					success:function(json){
						alert(json.status);
					}

					});
				
			}
			function getcount(){
				$.ajax({
					url:"http://localhost/freniz_zend/public/review/getcount",
					dataType:"json",
					cache:false,
					success:function(json){
						   var mess=document.getElementById('mes-a');
						     var a='<span id="mess-count">'+json.message+'</span>';
								 $(mess).append(a);
								 var aler=document.getElementById('alert-a');
							     var b='<span id="alert-count">'+json.alerts+'</span>';
									 $(aler).append(b);
									 var noti=document.getElementById('noti-a');
								     var c='<span id="noti-count">'+json.notifications+'</span>';
										 $(noti).append(c);
										 var rev=document.getElementById('revi-a');
									     var d='<span id="rev-count">'+json.reviews+'</span>';
											 $(rev).append(d);
						
							}
					});
				setTimeout(function(){getcount();},5000);
			}
			
			function changepassword(){
				var old=$('#oldpassword').val();
				var neww=$('#newpassword').val();
				var conf=$('#conformpassword').val();
				$.ajax({
					url:"http://localhost/freniz_zend/public/index/changepass?old="+old+"&new="+neww+"&conf="+conf,
					dataType:"json",
					cache:false,
					success:function(json){
						  alert(json.status);
						
							}
					});
			
			}
			function notification(){
				$.ajax({
					url:"http://localhost/freniz_zend/public/notification/index/",
					dataType:"xml",
					cache:false,
					success:function(xml){
						  notify(xml);
						
							}
					});
				
			}
			function notify(xml){
				var main=document.getElementById('maincontainer');
				var a=document.createElement('div');
				 $(xml).find('notifications').each(function(){
			         $(this).find('notification').each(function(){
			        	 var b=document.createElement('li');
			        	 b.id="notify";
			        	 b.innerHTML='<div onclick="window.location.href=\''+$(this).find('contenturl').text()+'\'" style="width:600px; height:60px; padding:5px; border-bottom:solid 1px"><div style="width:50px; height:50px; float:left; border:solid 1px"><img src="http://images.freniz.com/50/50_'+$(this).find('userpic').text()+'" /></div><div style="width:530px; margin-left:4px; height:30px; float:left; ">'+$(this).find('message').text()+'</div><div style="width:530px; margin-left:4px;height:15px; float:left;"><span class="timeago"title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</span></div></div>';
			        	 $(a).append(b);
			         });
				 });
				 $(main).html(a);
				 prettyLinks();
			}
			function suggestion(){
			
		$.ajax({
					url:"http://localhost/freniz_zend/public/search/suggestion?from="+suggestionfrom+"&limit="+suggestionlimit,
					dataType:"xml",
					cache:false,
					success:function(xml){
						  suggest(xml);
						
							}
					});
			
			}
			
			function suggest(xml){
						  var main=document.getElementById('suggestion-list');
				 var top=document.createElement('div');
				 top.innerHTML='<label style="font-size:16px; font-weight:bold">Suggestion</label>';
				 $(xml).find('users').each(function(){
				 if($(this).find('user').length==0){
				 	suggestionfrom=0;
				 	
				 }
				 else
				 suggestionfrom+=suggestionlimit;
			         $(this).find('user').each(function(){
			        	 var a=document.createElement('div');
			        	 $(a).css({'width':'250px','margin':'10px','padding':'5px','float':'left','border':'solid 1px'});
			        	 a.innerHTML='<div style="width:50px; height:50px; float:left; border:solid 1px"><img src="http://images.freniz.com/50/50_'+$(this).find('propic').text()+'"></div>';
			        	var b=document.createElement('div');
			        	$(b).css({'width':'180px','padding':'5px','margin-left':'5px','height':'50px','float':'left'});
			        	b.innerHTML='<label><a href="http://localhost/freniz_zend/public/'+$(this).find('id').text()+'">'+$(this).find('username').text()+'</a></label><br/><span style="font-size:14px">Mutual: ( '+$(this).find('mutual').text()+' )</span><br/><span><a href="javascript:void(0)" onclick="addfriends(\''+$(this).find('id').text()+'\')">Add</a></span>';
			        	$(a).append(b);
			        	$(top).append(a);
			         });
				 });
				 $(main).html(top);
				 setTimeout(function(){suggestion()},30000);
				 
			}
$(document).ready(function() {
	    
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37271806-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
  
  });


			var activitylist=new Array();
			var minId=0,maxId=0;

			function getstreams(criteria,users)
			{
				if(!criteria)
				criteria='';
				var contentdisplay='none';
				if(maxId==0 || criteria!='higher')
					contentdisplay='block';
				var url='http://localhost/freniz_zend/public/streams?maxid='+maxId+'&activitylist='+activitylist+'&minid='+minId+'&criteria='+criteria;
				if(users)
				url+='&userids='+users;
					$.ajax({
 	              	  type: 'get',
 	              	  url: url,
 	              	  cache:false,
 	              	  success: function(xml){
							if(users)
							streams(xml,contentdisplay,users);
							else
							streams(xml,contentdisplay);
							
 	              		  },
 	              	  dataType: "xml"
 	              	});
			}
			function streams(xml,contentdisplay){
				var main=document.getElementById('streams');
				 $(xml).find('streams').each(function(){
					 if(maxId<$(this).find('maxId').text())
					 maxId=$(this).find('maxId').text();
					 if((minId>$(this).find('minId').text() || minId==0) && $(this).find('minId').text()!='')
					 minId=$(this).find('minId').text();
					 if(comment_max==0)
					 comment_max=$(this).find('maxcomment').text();
	                 $(this).find('stream').each(function(){
						 activitylist.push($(this).find('alternateId').text());
			switch($(this).find('type').text()){
			case 'post':
					var postid=$(this).find('contentid').text();
	           	  var top=document.createElement('div');
	           	  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		            var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='post-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold; ">'+$(this).find('title').text()+'</div><div style="width:500px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div><div style="width:360px; margin-left: 20px; float: left; margin-top: 5px; padding:5px; max-height: 200px; ">'+$(this).find('status').text()+'</div>';
	                 	
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text()=='no'){
	               	 b.innerHTML='<a onclick="votescribbles('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvotescribbles('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">winked( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                comments.scribbles[postid]={};
				          comments.scribbles[postid]['totalcomments']=$(this).find('commentcounts').text();
				      
	                  var g=document.createElement('div');
			            g.id='post-comment-main-div'+$(this).find('contentid').text();
			            var i=document.createElement('div');
			            i.id='post-comment-sub-div'+$(this).find('contentid').text();
			          
			            $(this).find('comments').each(function(){
			            	$(this).find('comment').each(function(){
			            		var d=document.createElement('div');
			            		 d.id='post-comment-box'+$(this).find('comment-id').text();
			            d.innerHTML='<div style="width: 380px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-propic').text()+'" height="32" width="32" /></div><div style="width: 330px;  margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; ">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
			              $(i).append(d);
			            
			              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
			            	  $('#post-comment-del'+$(this).find('comment-id').text()).css('display','none');
	        				}
			            	});
			              	comments.scribbles[postid]['commentsdisplayed']=defaultcomments;
				            	if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:postid+'-loadcomments-post'}).html('<a href="javascript:void(0)" onclick="loadscribblecomments(\''+postid+'\')">load previous</a>').appendTo(g);
								}
				            	else
				            	$('#'+postid+'-loadcomments-post').remove();
				         
			            });
			            $(g).append(i);
			            if($(this).find('iscommentable').text()=='yes'){
			            var f=document.createElement('div');
			            f.innerHTML='<div id="comment-feild"style="width: 370px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('contentid').text()+'" onkeydown="postcommentscribles('+$(this).find('contentid').text()+',event)" type="text"placeholder="write to post"style="width: 330px; height: 20px;"/></div></div>';
			            $(g).append(f);
			            }
			            $(c).append(g);
	                 
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
				break;
			case 'stature':
				var statureid=$(this).find('contentid').text();
		         var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		            var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 370px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold; ">'+$(this).find('title').text()+'</div><div style="width:300px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div><div style="width:360px; margin-left: 20px; float: left; margin-top: 5px; padding:5px; ">'+$(this).find('status').text()+'</div>';
	                 	
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="votestature('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvotestature('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">winked( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                  var g=document.createElement('div');
			            g.id='stature-comment-main-div'+$(this).find('contentid').text();
			            var i=document.createElement('div');
			            i.id='stature-comment-sub-div'+$(this).find('contentid').text();
							comments.statures[statureid]={};
					          comments.statures[statureid]['totalcomments']=$(this).find('commentcounts').text();
				            
			            $(this).find('comments').each(function(){
			            	$(this).find('comment').each(function(){
			            		var d=document.createElement('div');
			            		 d.id='stature-comment-box'+$(this).find('comment-id').text();
			            d.innerHTML='<div style="width: 380px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-propic').text()+'" height="32" width="32" /></div><div style="width: 330px;  margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; ">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
			              $(i).append(d);
			            
			              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
			            	  $('#stature-comment-del'+$(this).find('comment-id').text()).css('display','none');
	        				}
			            	});
			              	comments.statures[statureid]['commentsdisplayed']=defaultcomments;
			              		if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:statureid+'-loadcomments-stature'}).css({'width':'400px','height':'10px','float':'left'}).html('<a href="javascript:void(0)" onclick="loadstaturecomments(\''+statureid+'\')">load previous</a>').appendTo(g);
								}
				            	else
				            	$('#'+statureid+'-loadcomments-stature').remove();
				            
			            });
			            $(g).append(i);
			            if($(this).find('iscommentable').text()=='yes'){
			            var f=document.createElement('div');
			            f.innerHTML='<div id="comment-feild"style="width: 470px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('contentid').text()+'" onkeydown="postcommentstature('+$(this).find('contentid').text()+',event)" type="text"placeholder="write to post"style="width: 330px; height: 20px;"/></div></div>';
			            $(g).append(f);
			            }
			            $(c).append(g);
	                 
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
				break;
			case 'image':
		            	
				var top=document.createElement('div');
	           	  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		          var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='image-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	  var b1=document.createElement('div');
	      	         $(b1).css({'margin-left':'5px','background-color': '#c1d8a9','float':'left','margin-top':'5px'});
	      	         b1.innerHTML='<a href="http://localhost/freniz_zend/public/image/'+$(this).find('albumid').text()+'#'+$(this).find('contentid').text()+'"><img width="100%" alt="image"src="http://images.freniz.com/500/500_'+$(this).find('imageurl').text().trim()+'"></a>';
	      	        c.appendChild(b1);
	      	      	
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="voteimage('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvoteimage('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                var g=document.createElement('div');
		            g.id='image-comment-main-div'+$(this).find('contentid').text();
		            var i=document.createElement('div');
		            i.id='image-comment-sub-div'+$(this).find('contentid').text();
		          
		            $(this).find('comments').each(function(){
		            	$(this).find('comment').each(function(){
		            		var d=document.createElement('div');
		            		 d.id='image-comment-box'+$(this).find('comment-id').text();
		            d.innerHTML='<div style="width: 380px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-userpic').text()+'" height="32" width="32" /></div><div style="width: 290px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right; margin-top:10px;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
		              $(i).append(d);
		            
		              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
		            	  $('#image-comment-del'+$(this).find('comment-id').text()).css('display','none');
        				}
		            	});
		              	
		            });
		            $(g).append(i);
		            if($(this).find('iscommentable').text()=='yes'){
		            var f=document.createElement('div');
		            f.innerHTML='<div id="comment-feild"style="width: 370px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('contentid').text()+'" onkeydown="postcommentscribles('+$(this).find('contentid').text()+',event)" type="text"placeholder="write to post"style="width: 330px; height: 20px;"/></div></div>';
		            $(g).append(f);
		            }
		            $(c).append(g);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);    
				break;
			case 'video':
				 var videoid=$(this).find('contentid').text();
				 var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		           var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='video-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:300px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
	                	 var b1=document.createElement('div');
		      	         $(b1).css({'margin-left':'20px','float':'left','margin-top':'15px'});
		      	         b1.innerHTML=$(this).find('url').text();
		      	        c.appendChild(b1);
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="votevideo('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvotevideo('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                var g=document.createElement('div');
		            g.id='video-comment-main-div'+$(this).find('contentid').text();
		            var i=document.createElement('div');
		            i.id='video-comment-sub-div'+$(this).find('contentid').text();
		          comments.videos[videoid]={};
					          comments.videos[videoid]['totalcomments']=$(this).find('commentcounts').text();
				            
		            $(this).find('comments').each(function(){
		            	$(this).find('comment').each(function(){
		            		var d=document.createElement('div');
		            		 d.id='video-comment-box'+$(this).find('comment-id').text();
		            d.innerHTML='<div style="width: 380px; margin-top:5px;  padding:5px; float: left; background-color:#E6E6E6;"><div style="width: 32px; height: 32px; float: left;"><img src="http://images.freniz.com/32/32_'+$(this).find('comment-propic').text()+'" height="32" width="32" /></div><div style="width: 290px;   margin-left: 5px; font-weight:normal; margin-top: 5px; float: left; "><a style="font-size:14px; padding:2px; text-decoration;" href="'+$(this).find('comment-userid').text()+'">'+$(this).find('comment-username').text()+':</a>'+$(this).find('comment-message').text()+'</div><a id="stature-comment-del'+$(this).find('comment-id').text()+'" href="javascript:void(0)" onclick="deletescribblescomment('+$(this).find('comment-id').text()+')" style="text-decoration:none; color:#000; font-size:10px;float:right;">x</a><div style="height:10px; margin-left: 5px; font-size:10px; float: left"><div class="timeago" style="float:left" title="'+$(this).find('comment-date').text()+'">'+$(this).find('comment-date').text()+'</div></div></div>';
		              $(i).append(d);
		            
		              if($(this).parent().parent().find('myid').text()!=$(this).parent().parent().find('userid').text() || $(this).find('comment-id').text()==$(this).parent().parent().find('myid').text()){
		            	  $('#video-comment-del'+$(this).find('comment-id').text()).css('display','none');
        				}
		            	});
		              	comments.videos[statureid]['commentsdisplayed']=defaultcomments;
			              		if($(this).find('loadprevious').text()=='yes'){
									$('<div/>').attr({id:videoid+'-loadcomments-video'}).css({'width':'300px','height':'10px','float':'left'}).html('<a href="javascript:void(0)" onclick="loadvideocomments(\''+videoid+'\')">load previous</a>').appendTo(g);
								}
				            	else
				            	$('#'+videoid+'-loadcomments-video').remove();
		            });
		            $(g).append(i);
		            if($(this).find('iscommentable').text()=='yes'){
		            var f=document.createElement('div');
		            f.innerHTML='<div id="comment-feild"style="width: 370px;height: 32px; margin-bottom:3px; padding:5px; float: left;"><div style="background-color:#ccc;margin-top: 5px; padding:5px; float: left; "><input id="text-comment-'+$(this).find('contentid').text()+'" onkeydown="postcommentscribles('+$(this).find('contentid').text()+',event)" type="text"placeholder="write to post"style="width: 330px; height: 20px;"/></div></div>';
		            $(g).append(f);
		            }
		            $(c).append(g);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
	                 $("iframe").attr({ 
		            	  width: "400",
		            	  height: "300"
		            	});
				break;
			case 'mood':
				var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:300px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
	                	 var b1=document.createElement('div');
		      	         $(b1).css({'margin-left':'20px','float':'left','margin-top':'-15px'});
		      	         b1.innerHTML='<img style="float:left;" alt="image"src="http://images.freniz.com/mood/32/'+$(this).find('mood').text().trim()+'"><span style="float:left; margin-left:5px; font-weight:bold">'+$(this).find('mood-description').text().trim()+'</span>';
		      	        c.appendChild(b1);
		      	      
		 	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);  
				break;
			case 'blog':
				 var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		             var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:300px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div><div style="width:360px; margin-left: 20px; float: left; margin-top: 5px; padding:5px;">'+$(this).find('blog').text()+'</div>';
	                 	
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="voteblog('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvoteblog('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">unwink( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
				break;
			case 'admire':
				 var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		            var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:300px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div><div style="width:360px; margin-left: 20px; float: left; margin-top: 5px; padding:5px;  ">'+$(this).find('message').text()+'</div>';
	                 	
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="voteadmire('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink('+$(this).find('vote').text()+')</a>';
	                 }else{
	               	  b.innerHTML='<a onclick="unvoteadmire('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">unwink('+$(this).find('vote').text()+')</a>';
	                 }
	                $(c).append(b);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
				break;
			case 'friends':
				var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		           	 var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:320px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:300px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
	                	  var b1=document.createElement('div');
		 	      	         $(b1).css({'margin-left':'20px','float':'left','margin-top':'-20px'});
			 	      	      $(this).find('rusers').each(function(){
					            	$(this).find('ruser').each(function(){
		 	      	         var c1=document.createElement('div');
			 	      	      $(c1).css({'margin-left':'5px','float':'left'});
				 	      	    
		 	      	         c1.innerHTML='<a href="'+$(this).find('ruserid').text().trim()+'"><img height="50" width="50"title="'+$(this).find('rusername').text().trim()+'" alt="image"src="http://images.freniz.com/50/50_'+$(this).find('userpic').text().trim()+'"></a>';
							b1.appendChild(c1);
					            	});
			 	      	      });
			 	      	        c.appendChild(b1);
		                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
				break;
			case 'propic':
				var top=document.createElement('div');
	           	  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		          var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div>';
		    	  var b1=document.createElement('div');
	      	         $(b1).css({'margin-left':'5px','background-color': '#c1d8a9','float':'left','margin-top':'5px'});
	      	         b1.innerHTML='<img alt="image" width="100%"src="http://images.freniz.com/500/500_'+$(this).find('suserpic').text().trim()+'">';
	      	        c.appendChild(b1);
	      	      	
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);  
				break;
				
			case 'forum':
				 var top=document.createElement('div');
				  $(top).css({'width':'500px','float':'left','min-height':'100px','display':contentdisplay});
		            var pic=document.createElement('div');
	            	 pic.className='user-pic'+$(this).find('contentid').text();
	           	 pic.style.width='75px';
	           	 pic.style.height='75px';
	           	 pic.innerHTML='<img id="user-pic" src="http://images.freniz.com/75/75_'+$(this).find('suserpic').text()+'"/>';
	           	  pic.style.cssFloat='left';
	           	   $(top).append(pic);
	           	  var a=document.createElement('div');
	           	   a.id='stature-stream';
	           	   a.className='stature-stream'+$(this).find('contentid').text();
	                  a.style.width='400px';
	                  a.style.cssFloat='left';
	                  a.style.borderBottom='solid 1px #ccc';
	                  a.style.padding='5px';
	                     a.style.margin='0 0 5px 7px';
	                	var c=document.createElement('div');
	                	c.id='main-scrible-div';
	                	c.style.width='400px';
	                	c.style.float='left';
	                	c.innerHTML='<div style="width:400px; float: left; font-size: 8px; margin-top: 2px; height: 45px; "><div style="width: 10px; height: 10px; position: absolute; margin-left: 380px;margin-top: 5px;  float: right"><a id="remove-'+$(this).find('contentid').text()+'" class="close"data="'+$(this).find('contentid').text()+'"href="javascript:void(0)">X</a></div><div style="width:380px; float: left; font-size: 16px; font-weight: bold;  ">'+$(this).find('title').text()+'</div><div style="width:400px; float: left; font-size: 12px; font-weight: bold; height: 15px; margin-left:10px; color: #aaa; "><div class="timeago" style="float:left; margin-top:2px;" title="'+$(this).find('date').text()+'">'+$(this).find('date').text()+'</div></div></div><div style="width:460px; margin-left: 20px; float: left; margin-top: -10px; padding:5px; ">'+$(this).find('question').text()+'</div><div style="width:460px; margin-left: 20px; float: left; margin-top: -2px; color:#aaa; padding:5px; ">'+$(this).find('description').text()+'</div>';
	                 	
	                 var b=document.createElement('div');
	                b.style.width='380px';
	                b.style.padding='5px';
	                b.style.float='left';
	                if($(this).find('vote-contains').text().trim()=='no'){
	               	 b.innerHTML='<a onclick="votequestion('+$(this).find('contentid').text()+',this)" class="vote-bar"style="float: right">wink( '+$(this).find('vote').text()+' )</a>';
	                 }else{
	               	  b.innerHTML='<a class="vote-bar"style="float: right">winked( '+$(this).find('vote').text()+' )</a>';
	                 }
	                $(c).append(b);
	                  $(a).append(c);
	                  $(top).append(a);
	                 $(main).prepend(top);
				break;
						
			}
							
	                 });
	                 
				 });
				 $('#new-streams-count').html($('#streams').children(':hidden').length);
				 if($('#streams').children(':hidden').length>0)
					$('#new-streams').css('display','block');
					
					
					setTimeout(function(){ 
						if(users) 
						getstreams('higher',users);
						else
						getstreams('higher');
						},3000);
				 prettyLinks();
			}
			
			
			
			function setStreamsVisible()
			{
				$('#streams').children(':hidden').each(function(){$(this).css('display','block');});
				$('#new-streams').css('display','none');
				$('#new-streams-count').html(0);
			}
			function deletelist(){
					var id=$('#lists-id').attr('data');
				$.ajax({
					url:"http://localhost/freniz_zend/public/list/remove?id="+id,
					dataType:"json",
					cache:false,
					success:function(json){
						 if(json.status=='success')
						 			window.location.href="http://localhost/freniz_zend/public";
						 else
						 	alert('Error occured please try again later');
						
							}
					});
			}
