/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var request=new createXMLHttpRequest();
    var request1=new createXMLHttpRequest();
    var request2=new createXMLHttpRequest();
    var request3=new createXMLHttpRequest();
    var request4=new createXMLHttpRequest(); 
    



function initlisteners()
{
    //createchat();
    var i=setInterval(function(){
        getonlineusers();
        getbendingfrndrequestcount();
        getmsgcount();
        },1000);
        getstreams();
    //formatdate();    
    
}
/*var i=0;
function pushState1(e){
i=0;
$('a.user-navigate').click(function(e){
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
function updateSite(url,e)
{
clearInterval(streamsinterval);
 request.onreadystatechange=function(){updatePage(e);};
 request.open('get',url,true);
 request.send(null);
}
function updatePage(e)
{
if(request.readyState==4 && request.status==200){
var a=document.createElement('div');
a.innerHTML=request.responseText;
$('#maincontainer').html($(a).find('#maincontainer').html());
pushState1(e);
}
}
$(window).bind("popstate", function(e){
	e.preventDefault();
	var state = event.state;
	if(state){
		updateSite(state.page);
	}
	});
    */
    function createXMLHttpRequest(){
  // See http://en.wikipedia.org/wiki/XMLHttpRequest
  // Provide the XMLHttpRequest class for IE 5.x-6.x:
  if( typeof XMLHttpRequest == "undefined" ) XMLHttpRequest = function() {
    try {return new ActiveXObject("Msxml2.XMLHTTP.6.0")} catch(e) {}
    try {return new ActiveXObject("Msxml2.XMLHTTP.3.0")} catch(e) {}
    try {return new ActiveXObject("Msxml2.XMLHTTP")} catch(e) {}
    try {return new ActiveXObject("Microsoft.XMLHTTP")} catch(e) {}
    throw new Error( "This browser does not support XMLHttpRequest." )
  };
  return new XMLHttpRequest();
}
function login(e)
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
    var userid=document.getElementById("userid").value;
    var pass=document.getElementById("password").value;
    login_auth(userid,pass,true);
    }
}
function login_auth(userid,pass,redirect){
	
	request.onreadystatechange=function(){validate(redirect);};
	request.open("post","http://localhost/freniz_zend/public/login",true);
	request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
           request.setRequestHeader("connection","close");
            var parameters="username="+userid+"&password="+pass;
           request.setRequestHeader("content-length",parameters.length);
           request.send(parameters);

}
function validate(redirect)
{
	alert(request.status);
	if((request.readyState==4)&& (request.status==200))
        {
            var json=eval('('+request.responseText+')');
            alert(json.userid);
            if(json.status=='true' && redirect)
            {
                window.location='http://localhost/freniz_zend/public/'+json.userid;
                request.onreadystatechange=myprofile;
                request.open("get",'http://localhost/freniz_zend/public/'+json.userid,true);
                request.send(null);
            }
            else if(json.status=='false')
                {
                document.getElementById("userid").value='';
                document.getElementById("password").value='';
                alert('wrong login details')
                }
                else
                window.location='tab-second.php';
        }

}
function myprofile()
{
	alert('1');
    if((request.readyState==4) && (request.status==200))
        {
            var e=document.getElementById("container");
            e.innerHTML=request.responseText;
            e.style.visibility='visible';
        }

}


function checkusername()
{
    var e=document.getElementById("username");
    if(e.value.indexOf(' ')==-1)
        {
            request.onreadystatechange=resultusername;
            request.open("get","ajax/checkusername.php?userid="+e.value,true);
            request.send(null);
        }
        else
            e.style.background='red';

}
function showprofile(userid)
{
                request4.onreadystatechange=frndprofile;
                request4.open("get","profile.php?userid="+userid,true);
                request4.send(null);
}
function frndprofile()
{
    if((request4.readyState==4) && (request4.status==200))
        {
            var e=document.getElementById("container");
            e.innerHTML=request4.responseText;
            e.style.visibility='visible';
        }
}

function resultusername()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            if(json.status=='true')
                {
                    var e=document.getElementById("username");
                    e.style.background='green';
                }
                else
                    {
                    var e=document.getElementById("username");
                    e.style.background='red';
                    }
        }
}
function checkemail()
{
    var e=document.getElementById("eid");
    var value=e.value;
    if(value.indexOf(' ')==-1 && value.indexOf('@')!=-1 && value.indexOf('.')!=-1 && value.indexOf('@')!=0 && value.indexOf('@')!=value.length-1 && value.indexOf('.')!=0 && value.indexOf('.')!=value.length-1)
        {
            request.onreadystatechange=resultemail;
            request.open("get","ajax/checkemail.php?emailid="+value,true);
            request.send(null);
            
        }
    else
        {
            e.style.background='red';
        }
}
function resultemail()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')')
            if(json.status=='true')
                {
                    var e=document.getElementById("eid");
                    e.style.background="green";

                }
                else
                    {
                          e=document.getElementById("eid");
                        e.style.background="red";
                    }
        }
}
function checkpassword()
{
    var e=document.getElementById("password1");
    var value=e.value;
    if(value.length<6)
        e.style.background='red';
    else
        e.style.background='green';
}
function matchpassword()
{
    var e=document.getElementById("password1");
    var e1=document.getElementById("cpassword");
    if(e.value==e1.value)
        e1.style.background='green';
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
    if(sex.value==1)
        var sex1='female';
    else if(sex.value==2)
         sex1='male';
    if(un.style.backgroundColor=='green' && pass.style.backgroundColor=='green' && cpass.style.backgroundColor=='green' && email.style.backgroundColor=='green' && fname.value!='' && lname.value!='' && sex.value!=0 && bdd.value!=-1 && bdm.value!=-1 && bdy.value!=-1)
        {
            request1.onreadystatechange=function(){resultcreateacc1(un.value,pass.value)};
            request1.open("get","ajax/createacc.php?un="+un.value+"&pass="+pass.value+"&email="+email.value+"&fname="+fname.value+"&lname="+lname.value+"&sex="+sex1+"&bdd="+bdd.value+"&bdm="+bdm.value+"&bdy="+bdy.value,true);
            request1.send(null);
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
function resultcreateacc1(un,pass)
{
    if(request1.readyState==4 && request1.status==200)
        {
            var json=eval('('+request1.responseText+')');
            if(json.status=='success'){
            login_auth(un,pass,false);
            //window.location.href='tab-second.php';
            }
            else
            alert(json.html);
        }
}
var msginterval,streamsstate=0,streamsinterval,imgstreaminterval,imgstreamstate=0,notificationinterval,bendingrequestinterval;
function getmsgcount()
{
            request.onreadystatechange = unreadmsgcount;
            request.open("get","ajax/unreadmsgs.php",true);
            request.send(null);
        
}
function unreadmsgcount()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            if(json.session=='true')
                {
                    var e= document.getElementById("message");
                    e.innerHTML="message("+json.count+")";
                }
                else
                    {
                        alert('your session has expired please login login');
                    }
        }
}
var streamtimeout;
function getstreams(criteria)
{
		clearTimeout(streamtimeout);
                var url='ajax/getstreams1.php';
                request3.onreadystatechange=function(){friendsstreams()};
                if(criteria)
                    {
                        url='ajax/getstreams1.php?creteria='+criteria;
                        request3.onreadystatechange=function(){friendsstreams(criteria);};
                    }
               
        request3.open("get",url,true);
        request3.send(null);
    
    
}
function friendsstreams(criteria)
{
    if(request3.readyState==4 && request3.status==200)
        {
            var xml=request3.responseXML;
            /*var ids=xml.getElementsByTagName("id");
            var sendusers=xml.getElementsByTagName("suserid");
            var susername=xml.getElementsByTagName("susername");
            var suserpic=xml.getElementsByTagName("suserpic");
            var suseruserfrnds=xml.getElementsByTagName("suserfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
            var receivedusers=xml.getElementsByTagName("ruserid");
            var rusername=xml.getElementsByTagName("rusername");
            var ruserpic=xml.getElementsByTagName("ruserpic");
            var ruseruserfrnds=xml.getElementsByTagName("ruserfrnds");
            var ruservotes=xml.getElementsByTagName("ruservotes");
            var statuses=xml.getElementsByTagName("status");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var votecontains=xml.getElementsByTagName("votecontains");
            var commentcounts=xml.getElementsByTagName("commentcount");
            var dates=xml.getElementsByTagName("date");
            var e1=document.getElementById("streams");
            var b=document.createElement('div');
            b.classname='streamwidth1';
            for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.className='maindiv';
                    c.id=ids[i].childNodes[0].nodeValue;
                    var d=document.createElement('div');
                    d.className='userpic';
                    d.style.backgroundImage='url('+suserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(d);
                    var e=document.createElement('div');
                    e.className='writtenon';
                    e.innerHTML='Wirtten On';
                    c.appendChild(e);
                    var f=document.createElement('div');
                    f.className='frdpic';
                    f.style.backgroundImage='url('+ruserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(f);
                    var g=document.createElement('div');
                    g.className='gap';
                    c.appendChild(g);
                    var h=document.createElement('div');
                    h.className='username subfont';
                    h.innerHTML=susername[i].childNodes[0].nodeValue;
                    c.appendChild(h);
                    var i1=document.createElement('div');
                    i1.className='frdname subfont';
                    i1.innerHTML=rusername[i].childNodes[0].nodeValue;
                    c.appendChild(i1);
                    var j=document.createElement('div');
                    j.className='commentbox';
                    j.innerHTML=statuses[i].childNodes[0].nodeValue;
                    c.appendChild(j);
                    var k=document.createElement('div');
                    k.className='commenttab';
                    k.innerHTML='<div class="votesymbol"></div>';
                    if(votecontains[i].childNodes[0].nodeValue!="yes")
                        k.innerHTML+='<a onclick="votepost('+ids[i].childNodes[0].nodeValue+')" style="float:left">vote('+votecounts[i].childNodes[0].nodeValue+')</a>';
                    else
                        k.innerHTML+='<a onclick="withdrawpostvote('+ids[i].childNodes[0].nodeValue+')" style="float:left">withdraw('+votecounts[i].childNodes[0].nodeValue+')</a>';
                    k.innerHTML+='<date'+dates[i].childNodes[0].nodeValue+'</div><a href="#" style="float:right">viewcomments('+commentcounts[i].childNodes[0].nodeValue+')</a>'
                    c.appendChild(k);
                    b.appendChild(c);
                }
                
            e1.innerHTML=b.innerHTML;
        */
       var b=document.createElement('div');
       $(xml).find('stream').each(function(){
           switch($(this).find('type').text())
           {
               case 'post':
                   var a=document.createElement('div');
                   	
	a.className='main-update-stature';
	a.innerHTML='<div style="width:100%; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div style="width:100%; margin-top:10px; height:100px; border:solid 1px; float:left">'+$(this).find('status').text()+'</div><div style="width:100%; margin-top:10px; height:20px; border:solid 1px; float:left"><div style="width:100px; font-size:10px; height:20px; border:solid 1px; float:left">'+$(this).find('date').text()+'</div></div><div class="update-stature-comment" style="width:100%; height:30px; border:solid 1px; float:left"><ul><li><a href="#" style="float:left; font-size:14px; font-weight:bold;text-decoration:none; cursor:pointer;color:#666">Vote</a></li></ul><div style="margin-top:-15px;"><ul style="margin-top:3px;"><li><a href="#" style="float:right; font-size:14px; font-weight:bold; text-decoration:none; color:#666; cursor:pointer;">Comment</a></li></ul></div></div>';
	b.appendChild(a);
                   break;
            	case 'admire':
                case 'blog':
                   var a=document.createElement('div');
	a.className='main-update-stature';
	a.innerHTML='<div style="width:100%; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div style="width:100%; margin-top:10px; height:100px; border:solid 1px; float:left">'+$(this).find('status').text()+'</div><div style="width:100%; margin-top:10px; height:20px; border:solid 1px; float:left"><div style="width:100px; font-size:10px; height:20px; border:solid 1px; float:left">'+$(this).find('date').text()+'</div></div><div class="update-stature-comment" style="width:100%; height:30px; border:solid 1px; float:left"><ul><li><a href="#" style="float:left; font-size:14px; font-weight:bold;text-decoration:none; cursor:pointer;color:#666">Vote</a></li></ul><div style="margin-top:-15px;"></div></div>';
	b.appendChild(a);
                   break;
                   case 'video':
                    var a=document.createElement('div');
	a.className='main-update-stature';
	a.innerHTML='<div style="width:100%; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div style="width:100%; margin-top:10px; height:100px; border:solid 1px; float:left">'+$(this).find('videotitle').text()+'</div><div style="float:right;width:80%"><iframe style="width:300px;height:200px" src="'+$(this).find('url').text()+'"/></div><div style="width:100%; margin-top:10px; height:20px; border:solid 1px; float:left"><div style="width:100px; font-size:10px; height:20px; border:solid 1px; float:left">'+$(this).find('date').text()+'</div></div><div class="update-stature-comment" style="width:100%; height:30px; border:solid 1px; float:left"><ul><li><a href="#" style="float:left; font-size:14px; font-weight:bold;text-decoration:none; cursor:pointer;color:#666">Vote</a></li></ul><div style="margin-top:-15px;"></div></div>';
	b.appendChild(a);
                   break;
                   case 'image':
                       var a=document.createElement('div');
	a.className='updatepic-main-div';
	a.innerHTML='<div style="width:100%; height:30px; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div style="width:100%; border:solid 1px; float:left"></div><div style="margin-top:10px; margin-left:20%; border:solid 2px; float:left"><img src="images/200/200_'+$(this).find('imageurl').text()+'" height="200" width="200" /></div><div style="width:100%; height:20px; margin-top:10px; border:solid 1px; float:left"><div style="width:100px; height:20px; border:solid 1px; float:left">'+$(this).find('date').text()+'</div><div style="width:100px; height:20px; border:solid 1px; float:right">'+$(this).find('imagetitle').text()+'</div></div><div id="update-pic-comment" style="width:100%; height:20px; border:solid 1px; float:left"><ul><li><a href="#" style="float:left; font-size:14px; font-weight:bold; text-decoration:none; cursor:pointer;color:#666">Vote</a></li></ul><div style="margin-top:-15px;"><ul style="margin-top:3px;"><li><a href="#" style="float:right; font-size:14px; font-weight:bold; text-decoration:none; color:#666; cursor:pointer;">Comment</a></li><li><a style="float:right;" >-</a></li><li><a href="#" style="float:right; color:#666; font-size:14px; font-weight:bold; text-decoration:none; cursor:pointer; color:#666">Pin to me</a></li></ul></div>';
	b.appendChild(a);
                       break;
                       case 'propic':
                           var a=document.createElement('div');
	a.className='updatepic-main-div';
	
        var propicinnerhtml='<div style="width:100%; height:30px; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div>';
	$(this).find('users').each(function(){
            if($(this).find('user').length>1){
         $(this).find('user').each(function(){
              propicinnerhtml+='<div style="width:50px; height:50px; margin-left:10px; border:solid 1px; float:left"><img src="images/50/50_'+$(this).find('userpic').text()+'" /></div>';
            });
            }
        });
       propicinnerhtml+='</div></div>';
       a.innerHTML=propicinnerhtml;
        b.appendChild(a);
                           break;
                           case 'mood':
                                var a=document.createElement('div');
	a.className='updatepic-main-div';
	
        var propicinnerhtml='<div style="width:100%; height:30px; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div>';
	$(this).find('users').each(function(){
            if($(this).find('user').length>1){
         $(this).find('user').each(function(){
              propicinnerhtml+='<div style="width:50px; height:50px; margin-left:10px; border:solid 1px; float:left"><img src="images/50/50_'+$(this).find('userpic').text()+'" /><img src="images/mood/'+$(this).find('mood').text()+'" height="16" width="16" style="position:absolute;" /></div>';
            });
            }
        });
       propicinnerhtml+='</div></div>';
       a.innerHTML=propicinnerhtml;
        b.appendChild(a);
                           break;
                           case 'basic info':
                                var a=document.createElement('div');
	a.className='updatepic-main-div';
	
        var propicinnerhtml='<div style="width:100%; height:30px; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div>';
	$(this).find('users').each(function(){
            if($(this).find('user').length>1){
         $(this).find('user').each(function(){
              propicinnerhtml+='<div style="width:50px; height:50px; margin-left:10px; border:solid 1px; float:left"><img src="images/50/50_'+$(this).find('userpic').text()+'" /></div>';
            });
            }
        });
       propicinnerhtml+='</div></div>';
       a.innerHTML=propicinnerhtml;
        b.appendChild(a);
                           break;
                            case 'personal info':
                                case 'education info':
                                var a=document.createElement('div');
	a.className='updatepic-main-div';
	
        var propicinnerhtml='<div style="width:100%; height:30px; border:solid 1px; float:left"><div style="height:50px;width:50px;float:left"><img src="images/32/32_'+$(this).find('suserpic').text()+'" ></div><div style="float:right;width:80%">'+$(this).find('title').text()+'</div> </div><div>';
	$(this).find('users').each(function(){
            if($(this).find('user').length>1){
         $(this).find('user').each(function(){
              propicinnerhtml+='<div style="width:50px; height:50px; margin-left:10px; border:solid 1px; float:left"><img src="images/50/50_'+$(this).find('userpic').text()+'" /></div>';
            });
            }
        });
       propicinnerhtml+='</div></div>';
       a.innerHTML=propicinnerhtml;
        b.appendChild(a);
                           break;
           }
       });
       if(!criteria)
       $("#maincontainer").html(b.innerHTML);
       else if(criteria=='higher'){
       $('#streamcontainerdummy').prepend(b.childNodes);
       $('#newstreamscount').html($('#streamcontainerdummy').children().length+' new posts');
       if($('#streamcontainerdummy').children().length>0)
       $('#newstreamscount').css('display', 'block');
       }
       else if(criteria=='lower')
       $('#streamcontainerdummy').append(b.childNodes);
            streamtimeout=setTimeout(function(){getstreams('higher');},10000);
        }
}
function displaynewstreams()
{
    $('#newstreamscount').css('display','none');
    $('#maincontainer').prepend($('#streamcontainerdummy').children());
    $('#streamcontainerdummy').html('');
}
function reqmorestreams()
{
    streamsstate=streamsstate+100;
    request.onreadystatechange=viewmorestreams;
    request.open("get","ajax/getstreams.php?from="+streamsstate,true);
    request.send(null);


}
function viewmorestreams()
{
 if(request.readyState==4 && request.status==200)
     {
        
       var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var sendusers=xml.getElementsByTagName("suserid");
            var susername=xml.getElementsByTagName("susername");
            var suserpic=xml.getElementsByTagName("suserpic");
            var suseruserfrnds=xml.getElementsByTagName("suserfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
            var receivedusers=xml.getElementsByTagName("ruserid");
            var rusername=xml.getElementsByTagName("rusername");
            var ruserpic=xml.getElementsByTagName("ruserpic");
            var ruseruserfrnds=xml.getElementsByTagName("ruserfrnds");
            var ruservotes=xml.getElementsByTagName("ruservotes");
            var statuses=xml.getElementsByTagName("status");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var votecontains=xml.getElementsByTagName("votecontains");
            var commentcounts=xml.getElementsByTagName("commentcount");
            var dates=xml.getElementsByTagName("date");
            var b=document.getElementById("morestreams");
            for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.className='maindiv';
                    c.id=ids[i].childNodes[0].nodeValue;
                    var d=document.createElement('div');
                    d.className='userpic';
                    d.style.backgroundImage='url('+suserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(d);
                    var e=document.createElement('div');
                    e.className='writtenon';
                    e.innerHTML='Wirtten On';
                    c.appendChild(e);
                    var f=document.createElement('div');
                    f.className='frdpic';
                    f.style.backgroundImage='url('+ruserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(f);
                    var g=document.createElement('div');
                    g.className='gap';
                    c.appendChild(g);
                    var h=document.createElement('div');
                    h.className='username subfont';
                    h.innerHTML=susername[i].childNodes[0].nodeValue;
                    c.appendChild(h);
                    var i1=document.createElement('div');
                    i1.className='frdname subfont';
                    i1.innerHTML=rusername[i].childNodes[0].nodeValue;
                    c.appendChild(i1);
                    var j=document.createElement('div');
                    j.className='commentbox';
                    j.innerHTML=statuses[i].childNodes[0].nodeValue;
                    c.appendChild(j);
                    var k=document.createElement('div');
                    k.className='commenttab';
                    k.innerHTML='<div class="votesymbol"></div><a href="#" style="float:left">vote'+votecounts[i].childNodes[0].nodeValue+'</a>'+dates[i].childNodes[0].nodeValue+'<a href="#" style="float:right">comment</a>';
                    c.appendChild(k);
                    b.appendChild(c);
                }
               
     
     }
}

function getimagestreams()
{
    imgstreaminterval=setInterval(function(){
        request.onreadystatechange=imgstreams;
        request.open("get","ajax/getimagestream.php?from=0",true);
        request.send(null);
    },1000);
}
function imgstreams()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var users=xml.getElementsByTagName("userid");
            var albumids=xml.getElementsByTagName("albumid");
            var albumnames=xml.getElementsByTagName("albumname");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var pinnedpeoples=xml.getElementTagName("pinnedpeople");
            var pinnedpeople_counts=xml.getElementTagName("pinnedpeople_count");
            var dates=xml.getElementsByTagName("date");
            var b=document.createElement("div");
            var e=document.getElementById("imagestrams");
            for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.id=ids[i].childNodes[0].nodeValue;
                    c.style.width=620+"px";
                    c.style.height=70+"px";
                    c.innerHTML=c.id+"<br>"+sendusers[i].childNodes[0].nodeValue+"<br>"+statuses[i].childNodes[0].nodeValue+"<br>"+ dates[i].childNodes[0].nodeValue;
                    b.appendChild(c);
                }
                e.innerHTML=b.innerHTML;

        }
}
function moreimagestreams()
{
    imgstreamstate=imgstreamstate+100;
        request.onreadystatechange=moreimgstreams;
        request.open("get","ajax/getimagestream.php?from="+imgstreamstate,true);
        request.send(null);

}
function moreimgstreams()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var users=xml.getElementsByTagName("userid");
            var albumids=xml.getElementsByTagName("albumid");
            var albumnames=xml.getElementsByTagName("albumname");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var pinnedpeoples=xml.getElementTagName("pinnedpeople");
            var pinnedpeople_counts=xml.getElementTagName("pinnedpeople_count");
            var dates=xml.getElementsByTagName("date");
            var b=document.createElement("div");
            var e=document.getElementById("imagestrams");
            for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.id=ids[i].childNodes[0].nodeValue;
                    c.style.width=620+"px";
                    c.style.height=70+"px";
                    c.innerHTML=c.id+"<br>"+users[i].childNodes[0].nodeValue+"<br>"+albumids[i].childNodes[0].nodeValue+"<br>"+ dates[i].childNodes[0].nodeValue;
                    b.appendChild(c);
                }
                e.innerHTML=b.innerHTML;
        }
}

function getmystreams(userid,type)
{
    streamsinterval=setInterval(function(){
        request.onreadystatechange=mystreams;
        request.open("get","ajax/mystreams.php?userid="+userid+"&from=0&type="+type,true);
        request.send(null);

    },10000);
}
function mystreams()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var sendusers=xml.getElementsByTagName("suserid");
            var susername=xml.getElementsByTagName("susername");
            var suserpic=xml.getElementsByTagName("suserpic");
            var suserfrnds=xml.getElementsByTagName("suserfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
            var statuses=xml.getElementsByTagName("status");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var votecontains=xml.getElementsByTagName("votecontains");
            var commentcounts=xml.getElementsByTagName("commentcount");
            var dates=xml.getElementsByTagName("date");
            var e1=document.getElementById("userstream");
            var b=document.createElement('div');
              
              for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.className='maindiv';
                    c.id=ids[i].childNodes[0].nodeValue;
                    var c1=document.createElement('div');
                    c1.className='subdiv'
                    var c2=document.createElement('div');
                    c2.className='closetag'
                    c2.innerHTML='<a href="#">x</a>';
                    c1.appendChild(c2);
                    var c3=document.createElement('div');
                    c3.className='dateitems'
                    c3.innerHTML=dates[i].childNodes[0].nodeValue;
                    c1.appendChild(c3);
                    c.appendChild(c1);
                    var d=document.createElement('div');
                    d.className='userpic';
                    d.style.backgroundImage='url('+suserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(d);
                    var h=document.createElement('div');
                    h.className='username';
                    h.innerHTML=susername[i].childNodes[0].nodeValue;
                    c.appendChild(h);
                    var h1=document.createElement('div');
                    h1.className='gap';
                    c.appendChild(h1);
                    var j=document.createElement('div');
                    j.className='statusitems';
                    j.innerHTML=statuses[i].childNodes[0].nodeValue;
                    c.appendChild(j);
                    var k=document.createElement('div');
                    k.className='commentbar';
                    if(votecontains[i].childNodes[0].nodeValue!="yes")
                        k.innerHTML+='<a onclick="votepost('+ids[i].childNodes[0].nodeValue+')" style="float:left">vote('+votecounts[i].childNodes[0].nodeValue+')</a>';
                    else
                        k.innerHTML+='<a onclick="withdrawpostvote('+ids[i].childNodes[0].nodeValue+')" style="float:left">withdraw('+votecounts[i].childNodes[0].nodeValue+')</a>';
                    k.innerHTML+='<a href="#" style="float:right">viewcomments('+commentcounts[i].childNodes[0].nodeValue+')</a>'
                    c.appendChild(k);
                    var k1=document.createElement('div');
                    k1.className='div-margin-gap';
                    b.appendChild(k1);
                    b.appendChild(c);
                }
                
            e1.innerHTML=b.innerHTML;
        


        }
}


function createblogstatus()
{
    alert('1');
     var text=escape(document.blogmessage.blg.value);
     if(text==''){alert("please fill the information");}else{
    request.onreadystatechange=blogstatus;
    request.open("post","ajax/addblog.php",true);
    request.setRequestHeader("content-type","application/x-www-form-urlencoded");
	var title=escape(document.blogmessage.blg_title.value);
	var imgurl=escape(document.blogmessage.blg_url.value);
     var parameters="text="+text+"&title="+title+"&imgurl="+imgurl;
     alert(parameters);
	request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
    }
}
function blogstatus()
{
     if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            var e=document.getElementById("light4");
            e.style.width=500+'px';
            e.style.height=250+"px";
            e.innerHTML=json.status+'<ul class="roundbuttons sendmessagewidth"><li><input type="button" name="cancel" value="cancel" onClick="document.getElementById("light4").style.display="none";   document.getElementById("fade4").style.display="none";  /></li></ul>';
            
        }
}
function createadmirestatus(userid)
{
     var text=escape(document.admiremess.admr.value);
    if(text==''){alert("please fill the information");}else{
    request.onreadystatechange=admirestatus;
    request.open("post","ajax/addtesty.php",true);
    request.setRequestHeader("content-type","application/x-www-form-urlencoded");
     var parameters="text="+text+"&userid="+userid;
    request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
    }
}
function admirestatus()
{
     if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            var e=document.getElementById("light5");
            e.style.width=500+'px';
            e.style.height=250+"px";
            e.innerHTML=json.status+'<ul class="roundbuttons sendmessagewidth"><li><input type="button" name="cancel" value="cancel" onClick="document.getElementById("light5").style.display="none";   document.getElementById("fade5").style.display="none";  /></li></ul>';
            
        }
}
function getblogs(userid)
{

        request.onreadystatechange=myblog;
        request.open("get","ajax/getblogs.php?userid="+userid+"&from=0",true);
        request.send(null);

}



function myblog()
{
  if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
          /*  var ids=xml.getElementsByTagName("id");
            var sendusers=xml.getElementsByTagName("suserid");
			var title=xml.getElementsByTagName("title");
			var imgurl=xml.getElementsByTagName("imgurl");
            var susername=xml.getElementsByTagName("susername");
            var suserpic=xml.getElementsByTagName("suserpic");
            var suserfrnds=xml.getElementsByTagName("suserfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
			var title=xml.getElementsByTagName("title");
			var imgurl=xml.getElementsByTagName("imgurl");
            var statuses=xml.getElementsByTagName("blog1");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var votecontains=xml.getElementsByTagName("votecontains");
            var dates=xml.getElementsByTagName("date");
            var e1=document.getElementById("userstream");
            var b=document.createElement('div');
             var b1=document.createElement('div');
             b1.className='blogheader';
             b1.innerHTML='Blog';
              b.appendChild(b1);
              
              for(var i=0;i<ids.length;i++)
                {  
				    var a=document.createElement('div');
					var main=document.createElement('div');
	main.className='blog-main1-div';
	main.id= ids[i].childNodes[0].nodeValue;
	main.innerHTML='<div class="blog-title-div" ><a style="text-decoration:underline" href="#">'+title[i].childNodes[0].nodeValue+'</a></div><div class="blog-time-div" >'+dates[i].childNodes[0].nodeValue+'</div><div class="blog-content-div" >'+statuses[i].childNodes[0].nodeValue+'</div>';
	//alert(imgurl[i].childNodes[0].nodeValue);
              // if((imgurl[i].childNodes[0].nodeValue)==''){
					        var image=document.createElement('div');
							image.className='blog-img-div';
							image.innerHTML='imgurl';
							main.appendChild(image);
				//}
	                    var vot=document.createElement('div');
						vot.className='blog-vote-div';
						  if(votecontains[i].childNodes[0].nodeValue!="yes")
                        vot.innerHTML+='<a onclick="voteblog('+ids[i].childNodes[0].nodeValue+')" style="float:left">vote'+votecounts[i].childNodes[0].nodeValue+'</a>';
                    else
                        vot.innerHTML+='<a onclick="withdrawblogvote('+ids[i].childNodes[0].nodeValue+')" style="float:left">withdraw'+votecounts[i].childNodes[0].nodeValue+'</a>';
						main.appendChild(vot);
						a.appendChild(main);
	
	/*
                    var c=document.createElement('div');
                    c.className='maindiv';
                    c.id=ids[i].childNodes[0].nodeValue;
                    var c1=document.createElement('div');
                    c1.className='subdiv'
                    var c2=document.createElement('div');
                    c2.className='closetag'
                    c1.appendChild(c2);
                    var c3=document.createElement('div');
                    c3.className='dateitems'
                    c3.innerHTML=dates[i].childNodes[0].nodeValue;
                    c1.appendChild(c3);
                    c.appendChild(c1);
                    var d=document.createElement('div');
                    d.className='userpic';
                    d.style.backgroundImage='url('+suserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(d);
                    var h=document.createElement('div');
                    h.className='username';
                    h.innerHTML=susername[i].childNodes[0].nodeValue;
                    c.appendChild(h);
                    var h1=document.createElement('div');
                    h1.className='gap';
                    c.appendChild(h1);
                    var j=document.createElement('div');
                    j.className='statusitems';
                    j.innerHTML=statuses[i].childNodes[0].nodeValue;
                    c.appendChild(j);
                    var k=document.createElement('div');
                    k.className='commentbar';
                    
                    if(votecontains[i].childNodes[0].nodeValue!="yes")
                        k.innerHTML+='<a onclick="voteblog('+ids[i].childNodes[0].nodeValue+')" style="float:left">vote'+votecounts[i].childNodes[0].nodeValue+'</a>';
                    else
                        k.innerHTML+='<a onclick="withdrawblogvote('+ids[i].childNodes[0].nodeValue+')" style="float:left">withdraw'+votecounts[i].childNodes[0].nodeValue+'</a>';
                    c.appendChild(k);
                    b.appendChild(c);
                 
                }
                
            e1.innerHTML=a.innerHTML;
        
   */
     var e1=document.getElementById("userstream");
            var b=document.createElement('div');
             var b1=document.createElement('div');
             b1.className='blogheader';
             b1.innerHTML='Blog';
              b.appendChild(b1);
     $(xml).find('blog').each(function(){
	
    var a=document.createElement('div');
					var main=document.createElement('div');
	main.className='blog-main1-div';
	main.id= $(this).find('id').text();
	main.innerHTML='<div class="blog-title-div" ><a  href="#">'+$(this).find('title').text()+'</a></div><div class="blog-time-div" >'+$(this).find('date').text()+'</div><div class="blog-content-div" >'+$(this).find('blog1').text()+'</div>';
	
               if(($(this).find('imgurl').text())!=''){
					        var image=document.createElement('div');
							image.className='blog-img-div';
							image.innerHTML='imgurl';
							main.appendChild(image);
				}
	                    var vot=document.createElement('div');
						vot.className='blog-vote-div';
						  if($(this).find('votecontains').text()!="yes")
                        vot.innerHTML+='<a onclick="voteblog('+$(this).find('id').text()+')" style="float:left">vote'+$(this).find('vote_count').text()+'</a>';
                    else
                        vot.innerHTML+='<a onclick="withdrawblogvote('+$(this).find('id').text()+')" style="float:left">withdraw'+$(this).find('vote_count').text()+'</a>';
						main.appendChild(vot);
						a.appendChild(main);
						b.appendChild(a);
						
	 }); 
	 e1.innerHTML=b.innerHTML;
        }
}
function getadmire(userid)
{
        request.onreadystatechange=myadmire;
        request.open("get","ajax/gettesty.php?userid="+userid+"&from=0",true);
        request.send(null);

}
function myadmire()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
          
		   var e1=document.getElementById("userstream");
            var b=document.createElement('div');
             var b1=document.createElement('div');
             b1.className='admireheader';
             b1.innerHTML='Admiration';
              b.appendChild(b1);
     $(xml).find('testy').each(function(){
	
    var a=document.createElement('div');
					var main=document.createElement('div');
	main.className='admire-main1-div';
	main.id= $(this).find('id').text();
	main.innerHTML='<div style="width:32px; margin-top:10px; margin-left:5px; float:left; height:32px; border:solid 1px"></div><div class="admire-title-div" ><a href="#">'+$(this).find('susername').text()+'</a></div><div class="admire-time-div" >'+$(this).find('date').text()+'</div><div class="admire-content-div" >'+$(this).find('message').text()+'</div>';
	
	                    var vot=document.createElement('div');
						vot.className='admire-vote-div';
						  if($(this).find('votecontains').text()!="yes")
                        vot.innerHTML+='<a onclick="voteadmire('+$(this).find('id').text()+')" style="float:left">vote'+$(this).find('vote_count').text()+'</a>';
                    else
                        vot.innerHTML+='<a onclick="withdrawadmirevote('+$(this).find('id').text()+')" style="float:left">withdraw'+$(this).find('vote_count').text()+'</a>';
						main.appendChild(vot);
						a.appendChild(main);
						b.appendChild(a);
						
	 }); 
	 e1.innerHTML=b.innerHTML;
		  
		  /*  var ids=xml.getElementsByTagName("id");
            var sendusers=xml.getElementsByTagName("suserid");
            var susername=xml.getElementsByTagName("susername");
            var suserpic=xml.getElementsByTagName("suserpic");
            var suserfrnds=xml.getElementsByTagName("suserfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
            var statuses=xml.getElementsByTagName("message");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var votecontains=xml.getElementsByTagName("votecontains");
            var dates=xml.getElementsByTagName("date");
            var e1=document.getElementById("userstream");
            var b=document.createElement('div');
              var b1=document.createElement('div');
             b1.className='admireheader';
             b1.innerHTML='Admiration';
              b.appendChild(b1); 
              for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.className='maindiv';
                    c.id=ids[i].childNodes[0].nodeValue;
                    var c1=document.createElement('div');
                    c1.className='subdiv'
                    var c2=document.createElement('div');
                    c2.className='closetag'
                    c1.appendChild(c2);
                    var c3=document.createElement('div');
                    c3.className='dateitems'
                    c3.innerHTML=dates[i].childNodes[0].nodeValue;
                    c1.appendChild(c3);
                    c.appendChild(c1);
                    var d=document.createElement('div');
                    d.className='userpic';
                    d.style.backgroundImage='url('+suserpic[i].childNodes[0].nodeValue+')';
                    c.appendChild(d);
                    var h=document.createElement('div');
                    h.className='username';
                    h.innerHTML=susername[i].childNodes[0].nodeValue;
                    c.appendChild(h);
                    var h1=document.createElement('div');
                    h1.className='gap';
                    c.appendChild(h1);
                    var j=document.createElement('div');
                    j.className='statusitems';
                    j.innerHTML=statuses[i].childNodes[0].nodeValue;
                    c.appendChild(j);
                    var k=document.createElement('div');
                    k.className='commentbar';
                    if(votecontains[i].childNodes[0].nodeValue!="yes")
                        k.innerHTML+='<a onclick="voteadmire('+ids[i].childNodes[0].nodeValue+')" style="float:left">vote'+votecounts[i].childNodes[0].nodeValue+'</a>';
                    else
                        k.innerHTML+='<a onclick="withdrawadmirevote('+ids[i].childNodes[0].nodeValue+')" style="float:left">withdraw'+votecounts[i].childNodes[0].nodeValue+'</a>';
                    c.appendChild(k);
                    b.appendChild(c);
                }
                
            e1.innerHTML=b.innerHTML;
        

                                 */
        }
}


function reqmoremystreams(userid)
{

    mystreamsstate=mystreamsstate+500;
    request.onreadystatechange=viewmoremystreams;
    request.open("get","ajax/mystreams.php?userid="+userid+"&from="+mystreamsstate,true);
    request.send(null);


}
function viewmoremystreams()
{
 if(request.readyState==4 && request.status==200)
     {

       var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var sendusers=xml.getElementsByTagName("suserid");
            var susername=xml.getElementByTagName("susername");
            var suserpic=xml.getElementByTagName("suserpic");
            var suserplace=xml.getElementByTagName("suserplace");
            var suseruserfrnds=xml.getElementsByTagName("suserfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
            var suserpages=xml.getElementByTagName("suserpages");
            var receivedusers=xml.getElementsByTagName("ruserid");
            var rusername=xml.getElementByTagName("rusername");
            var ruserpic=xml.getElementByTagName("ruserpic");
            var ruserplace=xml.getElementByTagName("ruserplace");
            var ruseruserfrnds=xml.getElementsByTagName("ruserfrnds");
            var ruservotes=xml.getElementsByTagName("ruservotes");
            var ruserpages=xml.getElementByTagName("ruserpages");
            var statuses=xml.getElementsByTagName("status");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var dates=xml.getElementsByTagName("date");
            var b=document.getElementById("morestreams");
            for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.id=ids[i].childNodes[0].nodeValue;
                    c.style.width=620+"px";
                    c.style.height=70+"px";
                    c.innerHTML=c.id+"<br>"+sendusers[i].childNodes[0].nodeValue+"<br>"+statuses[i].childNodes[0].nodeValue+"<br>"+ dates[i].childNodes[0].nodeValue;
                    b.appendChild(c);
                }


     }
}





function sendfrndreq(userid,text,songurl,imageurl)
{
    
    request.onreadystatechange=frndreq;
    request.open("post","ajax/frndreq.php",true);
    var params="userid="+userid+"&text="+text+"&songurl="+songurl+"&imageurl="+imageurl;
    request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",params.length);
    request.send(params);
}
function frndreq()
{
    alert(request.status);
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseText);
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}

function getinvites(userid){   
    $.ajax({
    url:'ajax/getinvites.php?userid='+userid,
    cache:false,
    dataType:"xml",
    success:function(xml){$(xml).find('invite').each(function(){
            displayinvites($(this).find('suserid').text(),$(this).find('susername').text(),$(this).find('suserpic').text(),$(this).find('mutualfrnds').text(),$(this).find('suservotes').text(),$(this).find('text').text(),$(this).find('songurl').text(),$(this).find('imageurl').text());
            //displayinvites("\'"+$(this).find('suserid').text()+"\'","\'"+$(this).find('susername').text()+"\'","\'"+$(this).find('suserpic').text()+"\'","\'"+$(this).find('mutualfrnds').text()+"\'","\'"+$(this).find('suservotes').text()+"\'","\'"+$(this).find('text').text()+"\'","\'"+$(this).find('songurl').text()+"\'","\'"+$(this).find('imageurl').text()+"\'");
            });}
    });
    /*request.onreadystatechange=function(){getinvite();};
    request.open("get","ajax/getinvites.php?userid="+userid,false);
    request.send(null);*/
}

function getinvite(xml)
{
    if(request.readyState==4 && request.status==200)
        {
            var xm=request.responseXML;
            $(xml).find('invite').each(function(){
            alert('1');
            displayinvites($(this).find('suserid').text(),$(this).find('susername').text(),$(this).find('suserpic').text(),$(this).find('mutualfrnds').text(),$(this).find('suservotes').text(),$(this).find('text').text(),$(this).find('songurl').text(),$(this).find('imageurl').text());
            });
           /* var ids=xml.getElementsByTagName("id");
            var suserid=xml.getElementsByTagName("suserid");
            var susername=xml.getElementsByTagName("susername");
            var suserpic=xml.getElementsByTagName("suserpic");
            var mutualfrnds=xml.getElementsByTagName("mutualfrnds");
            var suservotes=xml.getElementsByTagName("suservotes");
            var text2=xml.getElementsByTagName("text");
            var songurl=xml.getElementsByTagName("songurl");
            var imageurl=xml.getElementsByTagName("imageurl");
            for(var i=0;i<ids.length;i++){
              
                displayinvites(suserid[i].childNodes[0].nodeValue,susername[i].childNodes[0].nodeValue,suserpic[i].childNodes[0].nodeValue,mutualfrnds[i].childNodes[0].nodeValue,suservotes[i].childNodes[0].nodeValue,text2[i].childNodes[0].nodeValue,songurl[i].childNodes[0].nodeValue,imageurl[i].childNodes[0].nodeValue)
            }*/
        }
}




function removefrnd(userid)
{
    request.onreadystatechange=remvefrnd;
    request.open("get","ajax/removefrnd.php?userid="+userid,true);
    request.send(null);
}
function remvefrnd()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function cancelfrndreq(userid)
{
    request.onreadystatechange=cancelfrnd;
    request.open("get","ajax/cancelfrndreq.php?userid="+userid,true);
    request.send(null);
}
function cancelfrnd()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}

function getnotificationscount()
{
  notificationinterval=setInterval( function(){request.onreadystatechange=notificationcount;
    request.open("get","ajax/notificationcount.php",true);
    request.send(null);},1000);
}
function notificationcount()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.count);
        }
}
function getbendingfrndrequestcount()
{
      request2.onreadystatechange=bendingrequestcount;
        request2.open("get","ajax/bendingrequestcount.php",true);
        request2.send(null);
        
    
}
function bendingrequestcount()
{
    if(request2.readyState==4 && request2.status==200)
        {
            var json=eval('('+request2.responseText+')');
                  var e=document.getElementById("invites");
                    e.innerHTML="Invites("+json.reqcount+")";
        }
}
function getbendingrequest()
{
    request.onreadystatechange=bendingrequest;
    request.open("get","ajax/bendingrequest.php",true);
    request.send(null);
}
function bendingrequest()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;

        }
}
function addfrnd(userid)
{
    request.onreadystate=acceptfrnd;
    request.open("get","ajax/addfrnd.php?userid="+userid,true);
    request.send(null);
}
function acceptfrnd()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function ignorerequest(userid)
{
    request.onreadystatechange=ignore;
    request.open("get","ajax/ignorereq.php?userid="+userid,true);
    request.send(null);
}
function ignore()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function getincomingrequests()
{
   request.onreadystatechange=incomingrequests;
   request.open("get","ajax/incomingrequest.php",true);
   request.send(null);
}
function incomingrequests()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
        }
}
function getsentrequests()
{
    request.onreadystatechange=sentrequest;
    request.open("get","ajax/sentrequest.php",true);
    request.send(null);
}
function sentrequests()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=reqquest.responseXML;
        }
}
function getMessages()
{
    request.onreadystatechange=messages;
    request.open("get","ajax/messages.php",true);
    request.send(null);
}
function messages()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
        }
}
function getsentmessages()
{
    request.onreadystatechange=sentmessages;
    request.open("get","ajax/sentmessages.php",true);
    request.send(null);
}
function sentmessages()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
        }
}
function sentmessage(userid)
{
    request.onreadystatechange=senttouser;
    request.open("get","ajax/sentmessage.php?user="+userid,true);
    request.send(null);
}
function senttouser()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function searchuser(key)
{
    request.onreadystatechange=searchuserresults;
    request.open("get","ajax/search.php?type=users&key="+key,true);
    request.send(null);
}
function searchuserresults()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            alert(request.responseText);
            
        }
}
function searchpages(key)
{
    request.onreadystatechange=searchpageresult;
    request.open("get","ajax/search.php?type=pages&key="+key,true);
    request.send(null);
}
function searchpagesresult()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            alert(request.responseText);
        }
}
function searchbooks(key)
{
    request.onreadystatechange=searchbooksresult;
    request.open("get","ajax/search.php?type=pages&category=books&key="+key,true);
    request.send(null);
}
function searchbooksresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseText);
            var xml=request.responseXML;
        }
}
function searchmusics(key)
{
    request.onreadystatechange=searchmusicsresult;
    request.open("get","ajax/search.php?type=pages&category=musics&key="+key,true);
    request.send(null);
}
function searchmusicsresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function searchmovies(key)
{
    request.onreadystatechange=searchmoviesresult;
    request.open("get","ajax/search.php?type=pages&category=movies&key="+key,true);
    request.send(null);
}
function searchmoviesresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function searchcelebrities(key)
{
    request.onreadystatechange=searchcelebritiesresult;
    request.open("get","ajax/search.php?type=pages&category=celebrities&key="+key,true);
    request.send(null);
}
function searchcelebritiesresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function searchgames(key)
{
    request.onreadystatechange=searchgamesresult;
    request.open("get","ajax/search.php?type=pages&category=games&key="+key,true);
    request.send(null);
}
function searchgamesresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function searchsports(key)
{
    request.onreadystatechange=searchsportsresult;
    request.open("get","ajax/search.php?type=pages&category=sports&key="+key,true);
    request.send(null);
}
function searchsportsresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function searchothers(key)
{
    request.onreadystatechange=searchothersresult;
    request.open("get","ajax/search.php?type=pages&category=other&key="+key,true);
    request.send(null);
}
function searchothersresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function searchsongs(key)
{
    request.onreadystatechange=searchsongsresult;
    request.open("get","ajax/search.php?type=pages&category=songs&key="+key,true);
    request.send(null);
}
function searchsongsresult()
{
    if(request.readyState==4 && request.status==200)
        {
            alert(request.responseyText);
            var xml=request.responseXML;
        }
}
function addtoplaylist(leafid)
{
    request.onreadystatechange=songadded;
    request.open("get","ajax/addtoplaylist.php?leafid="+leafid,true);
    request.send(null);
}
function songadded()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}

function addpage(name,description)
{
    request.onreadystatechange=addpages;
    request.open("get","ajax/addpage.php?name="+name+"&description="+description,true);
    request.send(null);
}
function addpages()
{
    if(request.readyState==4 && reaquest.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function voteuser(userid)
{
    request.onreadystatechange=voteusr;
    request.open("get","ajax/voteuser.php?userid="+userid,true);
    request.send(null);
}
function voteusr()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function voteblog(blogid)
{
    request.onreadystatechange=voteblg;
    request.open("get","ajax/voteblogs.php?blogid="+blogid,true);
    request.send(null);
}
function voteblg()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function voteadmire(admireid)
{
    request.onreadystatechange=voteadmir;
    request.open("get","ajax/votetesty.php?testyid="+admireid,true);
    request.send(null);
}
function voteadmir()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function withdrawuservote(userid)
{
    request.onreadystatechange=withdrawuser;
    request.open("get","ajax/withdrawuser.php?userid="+userid,true);
    request.send(null);
}
function withdrawuser()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function votepost(postid)
{
    request.onreadystatechange=votepst;
    request.open("get","ajax/votepost.php?postid="+postid,true);
    request.send(null);
}
function votepst()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function voteleaf(leafid)
{
    request.onreadystatechange=vteleaf;
    request.open("get","ajax/voteleaf.php?leafid="+leafid,true);
    request.send(null);
}
function vteleaf()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function withdrawleaf(leafid)
{
    request.onreadystatechange=wthdrawleaf;
    request.open("get","ajax/withdrawleaf.php?leafid="+leafid,true);
    request.send(null);
}
function wthdrawleaf()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function withdrawpostvote(postid)
{
    request.onreadystatechange=withdrawpost;
    request.open("get","ajax/withdrawpost.php?postid="+postid,true);
    request.send(null);
}

function withdrawpost()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function withdrawblogvote(blogid)
{
    request.onreadystatechange=withdrawblog;
    request.open("get","ajax/withdrawblog.php?blogid="+blogid,true);
    request.send(null);
}

function withdrawblog()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function withdrawadmirevote(admireid)
{
    request.onreadystatechange=withdrawtesty;
    request.open("get","ajax/withdrawtesty.php?testyid="+admireid,true);
    request.send(null);
}

function withdrawtesty()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}

function sendmsguser()
{
    request.onreadystatechange=sendmessage;
    request.open("post","ajax/sendmessage.php",true);
    request.setRequestHeader("content-type","application/x-www-form-urlencoded");
    var userid=escape(document.sendmessage.to.value);
    var message=escape(document.sendmessage.msg.value);
    var username=escape(document.sendmessage.msgto.value);
    var parameters="userid="+userid+"&message="+message+"&username="+username;
    request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
}
function sendmessage()
{
     if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            var e=document.getElementById("light3");
            e.style.width=500+'px';
            e.style.height=250+"px";
            e.innerHTML=json.status+'<ul class="roundbuttons sendmessagewidth"><li><input type="button" name="cancel" value="cancel" onClick="document.getElementById("light3").style.display="none";   document.getElementById("fade3").style.display="none";  /></li></ul>';
            
        }
}
function createchat()
{
    var c=document.createElement("div");
    c.id='onlineusers';
    c.style.height=window.innerHeight;
    c.style.width=220+'px';
    c.style.right=0+"px";
    c.style.top=0+"px";
    c.style.position='fixed';
    c.style.border="solid 1px";
    document.body.appendChild(c);
}
function getonlineusers()
{
    
    request1.onreadystatechange=onlinefrnds;
        request1.open("get","ajax/getonlinefrnds.php",true);
        request1.send(null);
        
}
function onlinefrnds()
{
    if(request1.readyState==4 && request1.status==200)
        {
            var e= document.getElementById("onlineusers");
            e.innerHTML=request1.responseText;
        }
}
function getusername(userid)
{
    request.onreadystatechange=function(){
        if(request.readystate==4 && request.status==200)
            {
                var json=eval('('+request.responseText+')');
                return json.username;
            }
    };
    request.open("get","getusername.php?userid="+userid);
    request.send(null);
}

function createalbum()
{
    if(document.getElementById('albumname').value!=''){
        
    request.onreadystatechange=cretealbum;
    request.open("get","ajax/createalbum.php?albumname="+document.getElementById('albumname').value,true);
    request.send(null);
    }
}
function cretealbum()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
            document.getElementById('albumname').value='';
            getalbums(json.user);
        }
}
function getalbums(userid)
{
   clearInterval(streamsinterval);
    request.onreadystatechange=getalbum;
    request.open("get","ajax/getalbum.php?userid="+userid);
    request.send(null);
}
function getalbum()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var names=xml.getElementsByTagName("name");
            var dates=xml.getElementsByTagName("date");
            var e=document.getElementById('userstream');
            e.innerHTML='';
            for(var i=0; i<ids.length; i++)
                {
                    var album=document.createElement('div');
                    album.className='album-seper'
                      album.innerHTML="<a onclick='getimages("+ids[i].childNodes[0].nodeValue+")'>"+names[i].childNodes[0].nodeValue+"</a>";
                    e.appendChild(album);
                }
                
        }
}

function getimages(albumid)
{
    request.onreadystatechange=function(){images(albumid);};
    request.open("get","ajax/getimages.php?albumid="+albumid,true);
    request.send(null);
}
function images(albumid)
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var urls=xml.getElementsByTagName('url');
            var albumids=xml.getElementsByTagName("albumid");
            var albumnames=xml.getElementsByTagName("albumname");
            var userids=xml.getElementsByTagName("userid");
            var votecounts=xml.getElementsByTagName("vote_count");
            var votes=xml.getElementsByTagName("vote");
            var pinnedpeoples=xml.getElementsByTagName("pinnedpeople");
            var pinnedpeoplecounts=xml.getElementsByTagName("pinnedpeople_count");
            var dates=xml.getElementsByTagName("date");
            var e=document.getElementById("userstream");
           
            e.innerHTML='<div style="width:100%; background-color:#000; font-size:26px; font-weight:bold; color:#fff; height:40px; border:solid 1px">'+xml.getElementsByTagName("albumname")[0].childNodes[0].nodeValue+'</div><div id="file-uploader-demo1"><noscript><p>Please enable JavaScript to use file uploader.</p><!-- or put a simple form for upload here --></noscript></div>';
            if(albumnames[0].childNodes[0].nodeValue=='Profilepics' || albumnames[0].childNodes[0].nodeValue=='SecondaryProfilepics'){
            createUploader(albumid,false,false);
            }
            else{
                createUploader(albumid,true,true);
            }
            for(var i=0; i<ids.length; i++)
                {
                 
                    var image=document.createElement('div');
                    image.style.float='left';
                    image.style.padding='3px';
                    var options={"id":ids[i].childNodes[0].nodeValue, "url":urls[i].childNodes[0].nodeValue,"albumid":albumids[i].childNodes[0].nodeValue,"albumname":albumnames[i].childNodes[0].nodeValue,"userid":userids[i].childNodes[0].nodeValue,"votecount":votecounts[i].childNodes[0].nodeValue,"vote":votes[i],"pinnedpeople":pinnedpeoples[i],"pinnedpeoplecount":pinnedpeoplecounts[i].childNodes[0].nodeValue,"date":dates[i].childNodes[0].nodeValue};
                    image.innerHTML="<a href='image.php?albumid="+albumids[i].childNodes[0].nodeValue+"#"+ids[i].childNodes[0].nodeValue+"'><img style='float:left'  src='images/200/200_"+urls[i].childNodes[0].nodeValue+"' /></a>";
                    e.appendChild(image);
                }
        }
}
function uploadimages(albumid)
{
    var e=document.getElementById("light4");
    e.innerHTML="<iframe src='ajax/uploadimages.php?albumid="+albumid+"' width='600px' height='400px' />";
    e.style.display='block';
    var f=document.getElementById('fade4');
    f.style.display='block';
}
function closeupload(albumid)
{
    getimages(albumid);
    document.getElementById('light4').style.display='none';
    document.getElementById('fade4').style.display='none';
}
function showimage(options)
{
    alert('1');
    var e=document.getElementById("light5_image");
    var e1=document.getElementById("light5");
    e1.style.display='block';
    var f=document.getElementById('fade5');
    f.style.display='block';
    e.innerHTML="<image src='images/"+options.url+"/>";
    var imageinterval=setInterval(function(){
    request.onreadystatechange=getimagecomments;
    request.open("get","getimagecomments.php?imageid="+options.id,true);
    request.send(null);
},3000)
}
function getimagecomments()
{
    if(request.readyState==4 && request.status==200)
        {
            var e=document.getElementById("light5_comments");
            var d=document.createElement('div');
            d.style.overflow="scroll";
            d.style.width=624+'px';
            d.style.height=600+'px';
            var xml=request.responseXML;
            var cids=xml.getElementsByTagName("id");
            var userids=xml.getElementsByTagName("userid");
            var comments=xml.getElementsByTagName("comment");
            var votes=xml.getElementsByTagName("vote");
            var votecounts=xml.getElementsByTagName("vote_count");
            var dates=xml.getElementsByTagName("date");
            for(var i=0;i<cids.length; i++)
            {
                var c=document.createElement('div');
                var f=document.createElement('div');
                f.style.width=624+'px';
                f.style.height=20+'px';
                f.innerHTML="<a class='user-navigate' href='profile.php?userid="+userids[i].childNodes[0].nodeValue+"'>"+userids[0].childNodes[0].nodeValue+"</a>";
                c.appendChild(f);
                var g=document.createElement('div');
                g.style.width=624+'px';
                g.innerHTML=comments[i].childNodes[0].nodeValue;
                c.appendChild(g);
                d.appendChild(c);
            }
            e.innerHTML=d.innerHTML;

                
        }
}
function dopost(userid,type)
{
    var text=document.postform.post.value;
    if(text!='')
        {
           request.onreadystatechange=post;
           request.open("post","ajax/dopost.php",true);
           request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
           request.setRequestHeader("connection","close");
           var text1=text;
           var userid1=userid;
           var parameters="userid="+userid1+"&text="+text1+"&type="+type;
           request.setRequestHeader("content-length",parameters.length);
           request.send(parameters);
        }
        else
            alert("message cannot be posted blank");
}
function post()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            document.postform.post.value='';
            alert(json.status);
            
        }
}
function docomment(postid)
{
    var text=document.getElementById("commentbox_"+postid);
    if(text!='')
        {
            request.onreadystate=comment;
            request.open("post","docomment.php",true);
            request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
            request.setRequestHeader("connection","close");
            var text1=escape(text);
            var postid1=escape(postid);
            var parameters="postid="+postid1+"&text="+text1;
            request.setRequestHeader("content-length",parameters.length);
            request.send(parameters);
        }
}
function comment()
{
    if(request.readyStatus==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json);
        }
}

function setaspropic(imageid,deletesrc,x,y,width,height)
{
    request.onreadystatechange=setpropic;
    request.open("get","/fz-proto/ajax/setaspropic.php?imageid="+imageid+"&deletesrc="+deletesrc+"&x="+x+"&y="+y+"&width="+width+"&height="+height,true);
    alert("/fz-proto/ajax/setaspropic.php?imageid="+imageid+"&deletesrc="+deletesrc+"&x="+x+"&y="+y+"&width="+width+"&height="+height);
    request.send(null);
    
}
function setpropic()
{
    alert(request.readyState+"\n"+request.status);
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function setassecpic(imageid,deletesrc,secpicno,x,y,width,height)
{
    request.onreadystatechange=setsecpic;
    request.open("get","/fz-proto/ajax/setassecpic.php?imageid="+imageid+"&deletesrc="+deletesrc+"&secpicno="+secpicno+"&x="+x+"&y="+y+"&width="+width+"&height="+height,true);
    request.send(null);
    
}
function setsecpic()
{
    alert(request.readyState+"\n"+request.status);
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}

function updatemood(mood)
{
    request.onreadystatechange=setmood;
    request.open("get","ajax/updatemood.php?mood="+mood,true);
    request.send(null);
}
function setmood()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function setsecondarypic1(imageid)
{
    request.onreadystatechange=updatesecondarypic1;
    request.open("get","ajax/setsp1.php?imageid="+imageid,true);
    request.send(null);
}
function updatesecondarypic1()
{
    if(request.readystate==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function setsecondarypic2(imageid)
{
    request.onreadystatechange=updatesecondarypic2;
    request.open("get","ajax/setsp2.php?imagid="+imageid,true);
    request.send(null);
}
function updatesecondarypic2()
{
    if(request.readystate==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}

function addfavbooks(pageid)
{
    if(pageid!=0){
    request.onreadystatechange=addbooks;
    request.open("get","ajax/updatefav.php?action=add&category=books&pageid="+pageid,true);
    request.send(null);
}
else
alert('false');
}
function addbooks()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addfavmusics(pageid)
{
    request.onreadystatechange=addmusics;
    request.open("get","ajax/updatefav.php?action=add&category=musics&pageid="+pageid,true);
    request.send(null);
}
function addmusics()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addfavmovies(pageid)
{
    request.onreadystatechange=addmovies;
    request.open("get","ajax/updatefav.php?action=add&category=movies&pageid="+pageid,true);
    request.send(null);
}
function addmovies()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addfavcelebrities(pageid)
{
    request.onreadystatechange=addcelebrities;
    request.open("get","ajax/updatefav.php?action=add&category=celebrities&pageid="+pageid,true);
    request.send(null);
}
function addcelebrities()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addfavgames(pageid)
{
    request.onreadystatechange=addgames;
    request.open("get","ajax/updatefav.php?action=add&category=games&pageid="+pageid,true);
    request.send(null);
}
function addgames()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addfavsports(pageid)
{
    request.onreadystatechange=addbooks;
    request.open("get","ajax/updatefav.php?action=add&category=sports&pageid="+pageid,true);
    request.send(null);
}
function addsports()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addfavothers(pageid)
{
    request.onreadystatechange=addbooks;
    request.open("get","ajax/updatefav.php?action=add&category=other&pageid="+pageid,true);
    request.send(null);
}
function addothers()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function deletepost(postid)
{
    request.onreadystatechange=delpost;
    request.open("get","deletepost.php?postid="+postid,true);
    request.send(null);
}
function delpost()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function deletecomment(commentid)
{
    request.onreadystatechange=delcomment;
    request.open("get","deletecomment.php?commentid="+commentid,true);
    request.send(null);
}
function delcomment()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function formatdate()
{
    var dates=document.getElementsByName("date");
    for (var i=0;i<dates.length;i++)
        {
            var date=new date(dates[i].innerHTML);
            alert(date.toLocaleString());
        }
}
function doimagecomment(imageid,e)
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
        if(keynum==13){
    var e=document.getElementById(imageid+"_comment");
        if(e.value!=''){
    request.onreadystatechange=function(){commentimage(imageid)};
    request.open("post","ajax/doimagecomment.php",true);
    var imageid1=imageid;
    var comment=e.value;
    request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("connection","close");
    var parameters="imageid="+imageid+"&comment="+comment;
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
    }
        } 
}
function commentimage(imageid)
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
            document.getElementById(imageid+"_comment").value='';
            imgcom(imageid);
        }
}
function deleteimagecomment(commentid)
{
    request.onreadystatechange=delimgcomment;
    request.open("get","/sn/ajax/deleteimgcomment.php?commentid="+commentid,true);
    request.send(null);
}
function delimgcomment()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function deleteimage(imageid)
{
    request.onreadystatechange=delimage;
    request.open("get","deleteimage.php?image="+imageid,true);
    request.send(null);
}
function delimage()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function addpin(imageid)
{
    var flag=0;
    var d=document.getElementById(imageid+"_pinuser");
    var e=document.getElementById(imageid+"_pinpeople");
    if(d.value!='')
        {
            if(e.value!='')
                {
                    var f=e.value.split(",");
                    for(var i=0;i<f.length;i++)
                        {
                            if(f[i]==d.value)
                                flag=1;
                        }
                        if(flag!=1)
                            {
                            e.value=e.value+","+d.value;
                            d.value='';
                            }
                        else
                            {
                            alert("already added to list");
                            d.value='';
                            }
                }
                else
                    {
                    e.value=d.value;
                    d.value='';
                    }
        }
}

function pinpeople(imageid,people)
{
    if(!people){
    var value=document.getElementById(imageid+"_pinpeople").value;
    }
    else
        {
            var value=people;
        }
    if(value!='')
        {
            request.onreadystatechange=addpinpeople;
            request.open("post","ajax/addpin.php",true);
            request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
            request.setRequestHeader("connection","close");
            var imageid1=escape(imageid);
            var people=escape(value);
            var parameters="imageid="+imageid1+"&people="+people;
            request.setRequestHeader("content-length",parameters.length);
            request.send(parameters);
    
        }
}
function addpinpeople()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
            window.location=json.href;
            var e=document.getElementById(imageid+"_pinpeople");
            e.value='';
        }
}
function removepin(imageid,userid)
{
    request.onreadystatechange=removepinpeople;
    request.open("get","/sn/ajax/removepin.php?imageid="+imageid+"&userid="+userid);
    request.send(null);
}
function removepinpeople()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.rsponsetext+')');
            alert(json.status);
        }
}

function writeslambook(userid)
{
    request.onreadystatechange=writeslam;
    request.open("post","/fz/ajax/updateslambook.php",true);
    var userid=escape(userid);
    var name=escape(document.aboutme.name.value);
    var bday=escape(document.aboutme.bday.value);
    var email=escape(document.aboutme.email.value);
    var phone=escape(document.aboutme.phone.value);
    var ambition=escape(document.aboutme.ambition.value);
    var hobby=escape(document.aboutme.hobby.value);
    var believe=escape(document.aboutme.believe.value);
    var friendship=escape(document.aboutme.friendship.value);
    var love=escape(document.aboutme.love.value);
    var hate=escape(document.aboutme.hate.value);
    var philosophy=escape(document.aboutme.philosophy.value);
    var film=escape(document.fav.film.value);
    var music=escape(document.fav.music.value);
    var actor=escape(document.fav.actor.value);
    var actress=escape(document.fav.actress.value);
    var sports=escape(document.fav.sports.value);
    var sportsman=escape(document.fav.sportsman.value);
    var dress=escape(document.fav.dress.value);
    var food=escape(document.fav.food.value);
    var place=escape(document.fav.place.value);
    var friends=escape(document.fav.friends.value);
    var feel=escape(document.fav.feel.value);
    var advice=escape(document.advice.advice.value);
    var parameters="userid="+userid+"&Name="+name+"&Born On="+bday+"&Email="+email+"&Ring Me="+phone+"&Ambition="+ambition+"&My Hobby="+hobby+"&I Believed in="+believe+"&About Friendship="+friendship+"&About Love="+love+"&I hate="+hate+"&My Philosophy="+philosophy+"&Fav Film="+film+"&Fav Music="+music+"&Fav Actor="+actor+"&Fav Actress="+actress+"&Fav Sports="+sports+"&Fav Sportsman="+sportsman+"&Fav Dress="+dress+"&Fav Food="+food+"&Fav Place="+place+"&Close Friends="+friends+"&I Feel About You="+feel+"&My Advice for You="+advice;
    request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);       
}
function writeslam()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
    document.aboutme.name.value='';
    document.aboutme.bday.value='';
    document.aboutme.email.value='';
    document.aboutme.phone.value='';
    document.aboutme.ambition.value='';
    document.aboutme.hobby.value='';
    document.aboutme.believe.value='';
    document.aboutme.friendship.value='';
    document.aboutme.love.value='';
    document.aboutme.hate.value='';
    document.aboutme.philosophy.value='';
    document.fav.film.value='';
    document.fav.music.value='';
    document.fav.actor.value='';
    document.fav.actress.value='';
    document.fav.sports.value='';
    document.fav.sportsman.value='';
    document.fav.dress.value='';
    document.fav.food.value='';
    document.fav.place.value='';
    document.fav.friends.value='';
    document.fav.feel.value='';
    document.advice.advice.value='';
    
            
        }
}
function writediary(date,value)
{
    alert(date+'\n'+value);
    request.onreadystatechange=wrtediary;
    request.open("post","ajax/updatediary.php","true");
    var notes=value;
    var parameters="date="+date+"&notes="+notes;
    request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters); 
}
function wrtediary()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
            
        }
}

function createleaf()
{
    var pagename=document.getElementById('pagename').value;
    var category=document.getElementById('category').value;
    var subcategory=document.getElementById('subcategory').value;
    if(pagename && category!=0)
        createpage(pagename,category,subcategory,'normal');
}
function createpage(pagename,category,subcategory,type)
{
    if(category!=0)
        {
    if(!subcategory){
        subcategory=category;
    }
    if(!type)
        type='default'
    request.onreadystatechange=function(){pagecreated(category);};
    request.open("get","ajax/createpage.php?type="+type+"&pagename="+pagename+"&category="+category+"&subcategory="+subcategory,true);
    alert("ajax/createpage.php?type="+type+"&pagename="+pagename+"&category="+category+"&subcategory="+subcategory);
    request.send(null);
        }
}
function pagecreated(category)
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            if(json.status=='success')
             {
                 for(var k in json)
                 alert(k+"=>"+json[k]);
                 var leafid=json.leafid;
                 alert(leafid);
                 if(category=='books')
                    addfavbooks(leafid);
                else if(category=='musics')
                    addfavmusics(leafid);
                else if(category=='movies')
                    addfavmovies(leafid);
                else if(category=='celebrities')
                    addfavcelebrities(leafid);
                else if(category=='games')
                    addfavgames(leafid);
                else if(category=='sports')
                    addfavsports(leafid);
                else if(category=='school')
            		addschools(leafid);
            	else if(category=='college')
            		addcolleges(leafid);
            	else if(category=='work')
            		basiceducation(leafid);
                else
                    addfavothers(leafid);
             }
             else
                 alert(json.status);
        }
}

function removefav(pageid,category)
{
    request.onreadystatechange=removefavs;
    request.open("get","ajax/updatefav.php?action=remove&category="+category+"&pageid="+pageid,true);
    request.send(null);
}

function removefavs()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}

function updateimagedata(imageids)
{
    request1.onreadystatechange=updateimgdata;
    request1.open("post","ajax/editpics.php",true);
    var parameters="imageids="+imageids;
    request1.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request1.setRequestHeader("connection","close");
    request1.setRequestHeader("content-length",parameters.length);
    request1.send(parameters);
    
}
function updateimgdata()
{
    if(request1.readyState==4 && request1.status==200)
        {
            var xml=request1.responseXML;
            var ids=xml.getElementsByTagName("id");
            var urls=xml.getElementsByTagName('url');
            var pinnedpeoples=xml.getElementsByTagName("pinnedpeople");
            var titles=xml.getElementsByTagName("title");
            var descriptions=xml.getElementsByTagName("description");
            var main=document.getElementById('light1');
            var imageids=new Array();
            for(var i=0;i<ids.length;i++){
                var a=document.createElement('div');
                a.className='edit-phot-main';
                a.innerHTML='<div style="width:200px; height:200px; border:solid 1px; float:left"><img src="images/200/200_'+urls[i].childNodes[0].nodeValue+'" /></div><div style="width:400px; border:solid 1px; float:left"><div style="width:400px; border:solid 1px; float:left"><label>Title</label><input id="'+ids[i].childNodes[0].nodeValue+'_title" type="text" size="50" value="" /></div><div style="width:400px; border:solid 1px; float:left"><label>Description</label><textarea id="'+ids[i].childNodes[0].nodeValue+'_description"  style="width:300px; height:50px;"></textarea></div><div style="width:400px; border:solid 1px;float:left"><label>Pin to Friends</label><input id="'+ids[i].childNodes[0].nodeValue+'_pinnedpeople"  type="text"  style="width:200px; height:20px" /><input type="hidden" id="'+ids[i].childNodes[0].nodeValue+'_pinnedpeople_hidden" value="" /></div><div style="width:400px; border:solid 1px; float:left"></div></div>';
                main.appendChild(a);
                imageids.push(ids[i].childNodes[0].nodeValue);
                var as_xmlsearch = new AutoSuggest(ids[i].childNodes[0].nodeValue+'_pinnedpeople', options_xmlsearch('friends','light1'));
                
            }
            document.getElementById('light1').style.display='block';document.getElementById('fade1').style.display='block';
            var b=document.createElement('div');
            b.style.width='50%';
            b.style.font='left';
            b.innerHTML='<input type="button" style="float:right" onclick="editphoto(['+imageids+'])" value="post to my album" />';
            main.appendChild(b);

        }
}


function editphoto(imageids){
    var b=new Array();
    for(var k in imageids){
    var a=new Array();	
    a['title']=document.getElementById(imageids[k]+"_title").value;
    a['description']=document.getElementById(imageids[k]+"_description").value;
    pinpeople(imageids[k],document.getElementById(imageids[k]+"_pinnedpeople_hidden").value);
    b[imageids[k]]=a;
}
request1.onreadystatechange=editimagedata;
    request1.open("post","ajax/updatepics.php",true);
    var parameters="imagearray="+php_serialize(b);
    request1.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request1.setRequestHeader("connection","close");
    request1.setRequestHeader("content-length",parameters.length);
    request1.send(parameters);
}
function editimagedata()
{
    if(request1.readyState==4 && request1.status==200)
        {
            var json=eval("("+request1.responseText+")");
            alert(json.status);
            document.getElementById("light1").innerHTML='';
        }
}
function cancelpinrequser(userid,fldid)
{
                     
    var hiddenValue= document.getElementById(fldid+"_hidden").value;
                    
                      var userarray=hiddenValue.split(',');
                      var userarray1=new Array();
                      for(var k in userarray){
                          
                          if(userarray[k]!=userid){
                              userarray1.push(userarray[k]);
                          }
                        }
document.getElementById(fldid+"_hidden").value=userarray1;
var e1=document.getElementById(fldid+"_display");
alert(document.getElementById(fldid+"_hidden").value);
var e=document.getElementById(userid+"_"+fldid+"_reqpinnedpeople");
e1.removeChild(e);
}
 
 
 
 function createsong()
            {
                var url='ajax/createleaf.php?category=song&subcategory=song&type=song&pagename='+document.getElementById('songname').value+'&songurl='+document.getElementById('songurl').value+'&valid=true';
                request.onreadystatechange=cretesng;
                request.open("get",url,true);
                alert(url);
                request.send(null);
                
            }
            
            function cretesng()
            {
                alert(request.readyState+"\n"+request.status);
                
                if(request.readyState==4 && request.status==200)
                    {
                        var json=eval('('+request.responseText+')');
                        addtoplaylist(json.leafid);
                    }
            }
function personalinfo()            
            {
                var body=document.getElementById('body_tpe').value;
                var look=document.getElementById('look_tpe').value;
                var smoke=document.getElementById('smoke_tpe').value;
                var drink=document.getElementById('drink_tpe').value;
                var passion=document.getElementById('passion_tpe').value;
                var pet=document.getElementById('pet_tpe').value;
                var ethnicity=document.getElementById('ethnicity_tpe').value;
                var sexual=document.getElementById('sexual_tpe').value;
                var humor=document.getElementById('humor_tpe').value;
                var url="ajax/personalinfo.php?";
                if(body!= '')
                    url+="body="+body;
                if(look!= '')
                    url+="&look="+look;
                if(smoke!= '')
                    url+="&smoke="+smoke;
                if(drink!= '')
                    url+= "&drink="+drink;
                if(passion!= '')
                    url+="&passion="+passion;
                if(pet!= '')
                    url+="&pets="+pet;
                if(ethnicity!= '')
                    url+="&ethnicity="+ethnicity;
                 if(sexual!= '')
                    url+= "&sexual="+sexual;
                 if(humor!= '')
                    url+= "&humor="+humor;
                    alert(url);
                    
             /*$.ajax({
              url:url,
              cache:false,
              dataType:"json",
              success:function(data){alert(data.status);}
              });*/
              request.onreadystatechange=personal1;
                //request.onreadystatechange=personal1;
              alert('1');
              request.open("get",url,true);
              request.send(null);
                
            }
            
            function personal1()
            {
            alert(request.readyState);
                 if(request.readyState==4 && request.status==200)
                    {
                        var json=eval('('+request.responseText+')');
                        alert(json.status);
                    }
            }
             
             
     
     
     var testtimeout;
     function test1(){
    request.onreadystatechange=test2;
    request.open("post","ajax/doimagecomment.php",true);
    request.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    request.setRequestHeader("connection","close");
    var parameters="imageid=39&comment=nice";
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
    alert(3);
    
     }
     function test2(){
         if(request.readyState==4 && request.status==200)
                    {
                        testtimeout=setTimeout(function(){test1()},10);
                    }
     }
     function test3(){
         clearTimeout(testtimeout);
     }
     function getFeaturedLeafs(placeid)
     {
         request.onreadystatechange=featuredLeafs;
         request.open("get","ajax/featuredLeafs.php?placeid="+placeid,true);
         request.send(null);
     }
     function featuredLeafs()
     {
         if(request.readyState==4 && request.status==200)
             {
                 var xml=request.responseXML;
                 $('#userstream').html('');
                 $(xml).find('page').each(function(){
                     var a=document.createElement('div');
                     a.innerHTML='<div style="height:50px; width:200px;"><div style="height:32px; float:right; margin:5px; width:32px; border:solid 1px"><img src="images/32/32_'+$(this).find('pagepic').text()+'" height="32" width="32" /></div><div style="height:50px; float:right; width:120px"><div style=" font-size: 12px; font-weight: bold;  width:120px; "><a class="user-navigate" href="leaf.php?leafid='+$(this).find('id').text()+'">'+$(this).find('pagename').text()+'</a></div><div style="height:25px; font-size: 12px; width:120px;"><div style="height:25px; width:60px; float: left"><a>Bids : '+$(this).find('bids').text()+'</a></div><div style="height:25px; width:60px; float: left "><a>votes : '+$(this).find('votes').text()+'</a></div></div></div><div style=" width:180px; margin-top: 15px; margin-left: 10px; border-bottom: solid 1px; "></div></div>';
                     $('#userstream').append(a);
                     
                 });
             }
     }
     function getSearchBusinessPlaces(placeid,category,key)
     {
         if(category!='-1'){
         var url="ajax/searchbusinessplaces.php?placeid="+placeid+"&category="+category;
         if(key)
             url+="&key="+key;
         request.onreadystatechange=featuredLeafs;
         request.open("get",url,true);
         request.send(null);
         }
     }
     
     
     
     function sentitems()
{
    request.onreadystatechange=sentmessages;
    request.open("get","ajax/sentmessages.php",true);
    request.send(null);
}
function sentmessages()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var suserid=xml.getElementsByTagName("ruserid");
            var from=xml.getElementsByTagName("to");
            var propic=xml.getElementsByTagName("propic");
            var message=xml.getElementsByTagName("msg");
            var read=xml.getElementsByTagName("read");
            var date=xml.getElementsByTagName("date");
            for(var i=0;i<suserid.length;i++)
                {
                    var main=document.getElementById('primarydiv');
                    var msgread=read[i].childNodes[0].nodeValue;
                     var a1=document.createElement('div');
                    if(msgread==0){
                     a1.className='messagetopdiv';   
                    }
                    else{
                         a1.className='messagetopdiv-read';
                    }
                    a1.innerHTML='<input type="checkbox" value='+suserid[i].childNodes[0].nodeValue+' style="float:left; display:none; margin:10px;">';
                    var a2=document.createElement('a');
                    a2.className="user-navigate";
                    a2.href='message.php?userid='+suserid[i].childNodes[0].nodeValue;
                    var a=document.createElement('div');
                    a.className='messagemaindiv';
                    var b=document.createElement('div');
                    b.className='messageclose';
                    b.innerHTML='<a style="text-decoration:none; cursor:pointer;" onclick="deleteallmessages(\''+suserid[i].childNodes[0].nodeValue+'\')">x</a>';
                    a1.appendChild(b);
                    var b1=document.createElement('div');
                    b1.className='messageclose';
                    b1.innerHTML=read[i].childNodes[0].nodeValue;
                    a.appendChild(b1);
                    
                    var c=document.createElement('div');
                    c.className='messagedate';
                    c.innerHTML=date[i].childNodes[0].nodeValue;
                    a.appendChild(c);
                    var d=document.createElement('div');
                    d.className='messageuserpic';
                    d.style.backgroundImage="url('images/32/32_"+propic[i].childNodes[0].nodeValue+"')";
                    a.appendChild(d);
                    var e=document.createElement('div');
                    e.className='messageusername';
                    e.innerHTML=from[i].childNodes[0].nodeValue;
                    a.appendChild(e);
                     var e4=document.createElement('div');
                    e4.className='message-gap';
                     a.appendChild(e4);
                    var f=document.createElement('div');
                    f.className='messagecontent';
                    f.innerHTML=message[i].childNodes[0].nodeValue;
                    a.appendChild(f);
                    a2.appendChild(a);
                    a1.appendChild(a2);
                    main.appendChild(a1);
                    
                }
              
        }
       
}
    
function getmessages()
{
        request.onreadystatechange=mymessages;
        request.open("get","ajax/getmessages.php",true);
        request.send(null);

}
function mymessages()
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var suserid=xml.getElementsByTagName("suserid");
            var from=xml.getElementsByTagName("from");
            var propic=xml.getElementsByTagName("propic");
            var message=xml.getElementsByTagName("msg");
            var read=xml.getElementsByTagName("read");
            var date=xml.getElementsByTagName("date");
            for(var i=0;i<suserid.length;i++)
                {
                    var main=document.getElementById('primarydiv');
                    var msgread=read[i].childNodes[0].nodeValue;
                     var a1=document.createElement('div');
                    if(msgread==0){
                     a1.className='messagetopdiv';   
                    }
                    else{
                         a1.className='messagetopdiv-read';
                    }
                    a1.innerHTML='<input type="checkbox" value='+suserid[i].childNodes[0].nodeValue+' style="float:left; display:none; margin:10px;">';
                    var a2=document.createElement('a');
                    a2.className="user-navigate";
                    a2.href='message.php?userid='+suserid[i].childNodes[0].nodeValue;
                    var a=document.createElement('div');
                    a.className='messagemaindiv';
                    var b=document.createElement('div');
                    b.className='messageclose';
                    b.innerHTML='<a style="text-decoration:none; cursor:pointer;" onclick="deleteallmessages(\''+suserid[i].childNodes[0].nodeValue+'\')">x</a>';
                    a1.appendChild(b);
                    var b1=document.createElement('div');
                    b1.className='messageclose';
                    a.appendChild(b1);
                    
                    var c=document.createElement('div');
                    c.className='messagedate';
                    c.innerHTML=date[i].childNodes[0].nodeValue;
                    a.appendChild(c);
                    var d=document.createElement('div');
                    d.className='messageuserpic';
                    d.style.backgroundImage="url('images/32/32_"+propic[i].childNodes[0].nodeValue+"')";
                    a.appendChild(d);
                    var e=document.createElement('div');
                    e.className='messageusername';
                    e.innerHTML=from[i].childNodes[0].nodeValue;
                    a.appendChild(e);
                      var e4=document.createElement('div');
                    e4.className='message-gap';
                     a.appendChild(e4);
                    var f=document.createElement('div');
                    f.className='messagecontent-main';
                    f.innerHTML=message[i].childNodes[0].nodeValue;
                    a.appendChild(f);
                    a2.appendChild(a);
                    a1.appendChild(a2);
                    main.appendChild(a1);
                    
                }
        }
}

function getusermessages(userid)
{
    request.onreadystatechange=function(){usermessages(userid)};
        request.open("get","ajax/getusermessages.php?userid="+userid,true);
        request.send(null);
}
function usermessages(userid)
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            var ids=xml.getElementsByTagName("id");
            var suserid=xml.getElementsByTagName("suserid");
            var from=xml.getElementsByTagName("from");
            var propic=xml.getElementsByTagName("propic");
            var message=xml.getElementsByTagName("msg");
            var read=xml.getElementsByTagName("read");
            var date=xml.getElementsByTagName("date");
            var main1=document.getElementById('primarydiv');
            var main=document.createElement('div');
            for(var i=0;i<suserid.length;i++)
                {
                 
                    var a1=document.createElement('div');
                    a1.className='messagetopdiv-read';
                    a1.innerHTML='<input type="checkbox" value='+ids[i].childNodes[0].nodeValue+' style="float:left; display:none; margin:10px;">';
                    var a=document.createElement('div');
                    a.className='messagemaindiv';
                    var b=document.createElement('div');
                    b.className='messageclose';
                    b.innerHTML='<a style="text-decoration:none; cursor:pointer;" onclick="deletemessage(\''+ids[i].childNodes[0].nodeValue+'\')">x</a>';
                    a.appendChild(b);
                    var c=document.createElement('div');
                    c.className='messagedate';
                    c.innerHTML=date[i].childNodes[0].nodeValue;
                    a.appendChild(c);
                    var e=document.createElement('div');
                    e.className='messageusername';
                    e.innerHTML='<a href="#" >'+from[i].childNodes[0].nodeValue+'</a>';
                    a.appendChild(e);
                     var e4=document.createElement('div');
                    e4.className='message-gap';
                     a.appendChild(e4);
                    var f=document.createElement('div');
                    f.className='messagecontent';
                    f.innerHTML=message[i].childNodes[0].nodeValue;
                    a.appendChild(f);
                    a1.appendChild(a);
                    main.appendChild(a1);
                }
                  var c1=document.createElement('div');
                c1.className='message-input-div';
                c1.innerHTML='<input id="reply-message" onfocusout="repmessout(this)" onfocus="repmessin(this)" onkeydown="sendmessageuser(\''+userid+'\',event)" type="text" value="Reply..." style="width:100%; color:#000; height:25px"/>';
                main.appendChild(c1);
                main1.innerHTML=main.innerHTML;
        }
        

}

    function repmessin(element) {
      if(element.value=='Reply...'){
  	element.value = '';
      }
  
   }
   function repmessout(element) {
       if(element.value==''){
   	element.value = 'Reply...';}
     }
function sendmessageuser(userid,e){
var keynum;
if(window.event) // IE8 and earlier
	{
	keynum = e.keyCode;
	}
else if(e.which) // IE9/Firefox/Chrome/Opera/Safari
	{
	keynum = e.which;
	}
        if(keynum==13){
    var value=$("#reply-message").val();
   
    request.onreadystatechange=function(){sendreplymessage(userid)};
    request.open("post","ajax/sendmessage.php",true);
    request.setRequestHeader("content-type","application/x-www-form-urlencoded");
    var userid=escape(userid);
    var message=escape(value);
    var parameters="userid="+userid+"&message="+message;
    request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
}


}
function sendreplymessage(userid)
{
     if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            document.getElementById("reply-message").value='Reply...';
            getusermessages(userid);
           
        }
}
function deletemessage(msgid)
{
        request.onreadystatechange=delmsg;
        request.open("get","ajax/deletemessage.php?messageid="+msgid,true);
        request.send(null);
}
function delmsg()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function deleteallmessages(usrid)
{
        request.onreadystatechange=delallmsgs;
        request.open("get","ajax/deleteallmessages.php?userid="+usrid,true);
        request.send(null);
}
function delallmsgs()
{
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.status);
        }
}
function updateschoolandcityinfo()
{
var hometown=document.getElementById('search-place-update-hidden').value;
var curcity=document.getElementById('search-cc-place-update-hidden').value;
var school=document.getElementById('search-school-update-hidden').value;;
var college=document.getElementById('search-college-update-hidden').value;
var work=document.getElementById('search-work-update-hidden').value;
if(hometown!='')
{
updatehometown(hometown);
}
if(curcity!='')
{
updatecurcity(curcity);
}
if(school!='')
{
addschools(school);
}
if(college!='')
{
addcolleges(college);
}
if(work!='')
{
basiceducation(work);
}
}







function set_propic(imageid,url,deletesrc){
            var main=document.getElementById('light');
            main.innerHTML='<img src="'+url+'"  id="target" alt="Flowers" /><input type="button" value="done croping" onclick="propiccroping(\''+imageid+'\',\''+deletesrc+'\')"/><input type="button" style="float:right" value="Skip" onclick="propiccroping(\''+imageid+'\',\''+deletesrc+'\')"/><form id="coords" class="coords" onsubmit="return false;" ><div style="display:none"><input type="hidden" size="4" id="x1" name="x1" value="0" /><input type="hidden" size="4" id="y1" name="y1" value="0" /><input type="hidden" size="4" id="x2" name="x2" value"0" /><input type="hidden" size="4" id="y2" name="y2" value="0" /><input type="hidden" size="4" id="w" name="w" value="0" /><input type="hidden" size="4" id="h" name="h" value="0" /></div></form>';
            document.getElementById('light').style.display='block';
            document.getElementById('fade').style.display='block';
            initcrop(1/1);

}
function set_secpic(imageid,url,deletesrc,secpicno){
            var main=document.getElementById('light');
            main.innerHTML='<img src="'+url+'"  id="target" alt="Flowers" /><input type="button" value="done croping" onclick="secpiccroping(\''+imageid+'\',\''+deletesrc+'\',\''+secpicno+'\')"/><input type="button" style="float:right" value="Skip" onclick="secpiccroping(\''+imageid+'\',\''+deletesrc+'\',\''+secpicno+'\')"/><form id="coords" class="coords" onsubmit="return false;" ><div style="display:none"><input type="hidden" size="4" id="x1" name="x1" value="0" /><input type="hidden" size="4" id="y1" name="y1" value="0" /><input type="hidden" size="4" id="x2" name="x2" value"0" /><input type="hidden" size="4" id="y2" name="y2" value="0" /><input type="hidden" size="4" id="w" name="w" value="0" /><input type="hidden" size="4" id="h" name="h" value="0" /></div></form>';
            document.getElementById('light').style.display='block';
            document.getElementById('fade').style.display='block';
            initcrop(2/1);

}


function initcrop(aspectRatio){
		jQuery(function($){

      var c=$('#target').Jcrop({
        onChange:   showCoords,
        onSelect:   showCoords,
        onRelease:  clearCoords,
        aspectRatio: aspectRatio
      }).Coords;

    });
    }
    function propiccroping(imageid,deletesrc)
    {
        
        var url=document.getElementById("target").src;
        var x=$('#x1').val();
        var y=$('#y1').val();
        var w=$('#w').val();
        var h=$('#h').val();
        alert(x+"\n"+y+"\n"+w+"\n"+h);
        setaspropic(imageid,deletesrc,x,y,w,h);
    }
    function secpiccroping(imageid,deletesrc,secpicno)
    {
        
        var url=document.getElementById("target").src;
        var x=$('#x1').val();
        var y=$('#y1').val();
        var w=$('#w').val();
        var h=$('#h').val();
        alert(x+"\n"+y+"\n"+w+"\n"+h);
        setassecpic(imageid,deletesrc,secpicno,x,y,w,h);
    }
    

    // Simple event handler, called from onChange and onSelect
    // event handlers, as per the Jcrop invocation above
    function showCoords(c)
    {
        
      $('#x1').val(c.x);
      $('#y1').val(c.y);
      $('#x2').val(c.x2);
      $('#y2').val(c.y2);
      $('#w').val(c.w);
      $('#h').val(c.h);
    };

    function clearCoords()
    {
      $('#coords input').val('');
      $('#h').css({color:'red'});
      window.setTimeout(function(){
        $('#h').css({color:'inherit'});
      },500);
    };
function createleafinfo(leafid,category,subcategory){
    var main=document.getElementById('leaf-edit-info');
    main.innerHTML='<div style="width:700px"><input type="text" id="title" /><input type="button" onclick="addtextarea(document.getElementById(\'title\').value);document.getElementById(\'title\').value=\'\'" value="Add titles" /><form id="leaf-edit" name="pageinfo" ><table id="pagesinfo" cellpadding="3" cellspacing="3"></table><center><input type="button" onclick="submit_form(\''+leafid+'\',\''+category+'\',\''+subcategory+'\')" value="submit"/></center></form></div>';

     } 
function addtextarea(value)
{
    var e=document.getElementById("pagesinfo");
    var e1=document.createElement('tr');
    var e2=document.createElement('td');
    e2.innerHTML=value;
    e1.appendChild(e2);
    var e3=document.createElement('td');
    var e4=document.createElement('textarea');
    e4.name=value;
    e4.style.width='500px';
    e4.style.height='50px';
    e3.appendChild(e4);
    e1.appendChild(e3);
    e.appendChild(e1);
}
function submit_form(id,category,subcategory)
{
    var a=$('#leaf-edit').serialize();
    alert(a);
     request.onreadystatechange=createeditinfo;
    request.open("post","ajax/addpagesinfo.php",true);
    request.setRequestHeader("content-type","application/x-www-form-urlencoded");
	
     var parameters=a+"&pageid="+id+"&category="+category+"&subcategory="+subcategory;
     alert(parameters);
	request.setRequestHeader("connection","close");
    request.setRequestHeader("content-length",parameters.length);
    request.send(parameters);
    
}


function createeditinfo()
{alert(request.status);
     if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json);
                
        }
}


function welcomesearch(){
 
     var type=document.getElementById('select-field').value;
      
      var key=document.getElementById('welcome-search-element').value;
      if(key.trim().length>0){
   request.onreadystatechange=function(){searchwelcome(type)};
   if(type=='users'){
          var place=document.getElementById('search-places-hidden').value;
          var emptyplace=document.getElementById('search-places').value;
          if(place=='' || emptyplace==''){
              request.open("get","ajax/search.php?key="+key.trim()+"&type="+type+"&ref=login&from=0",true);    
      }else{
    request.open("get","ajax/search.php?key="+key.trim()+"&place="+place+"&type="+type+"&ref=login&from=0",true);
   
          }
     }
     else if(type=='pages'){
          var category=document.getElementById('category').value;
          var subcategory=document.getElementById('subcategory').value;
           request.open("get","ajax/search.php?key="+key.trim()+"&category="+escape(category)+"&subcategory="+subcategory+"&type="+type+"&ref=login&from=0",true);
   
     }
      else if(type=='places'){
           request.open("get","ajax/search.php?key="+key.trim()+"&type="+type+"&ref=login&from=0",true);
       
      }
      request.send(null);
     
  
      }
    
    
   
}

function searchwelcome(type)
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            
            var b=document.createElement('div');
            $(xml).find('users').each(function(){
                $(this).find('user').each(function(){
                    var a=document.createElement('div');
                    a.className="main-wel-div";
                    
                   a.innerHTML='<div style="width: 50px; height: 50px; float: left; border: solid 1px">'+$(this).find('propic').text()+'</div>  <div style="width: 400px; height: 20px; float: left;"><a href="'+$(this).find('url').text()+'">'+$(this).find('username').text()+'</a></div><div style="width: 300px; height: 20px; float: left; ">vote:'+$(this).find('votecount').text()+'</div><div style="width: 600px; margin-top:20px; float: left; border:solid 1px"></div> ';
                   $(b).append(a);
                });
            });
           $(xml).find('pages').each(function(){
                $(this).find('page').each(function(){
                    var a=document.createElement('div');
                    a.className="main-wel-div";
                   a.innerHTML='<div style="width: 50px; height: 50px; float: left; border: solid 1px">'+$(this).find('pagepic').text()+'</div>  <div style="width: 400px; height: 20px; float: left;"><a href="'+$(this).find('url').text()+'">'+$(this).find('pagename').text()+'</a></div><div style="width: 300px; height: 20px; float: left; ">vote:'+$(this).find('votecount').text()+'</div><div style="width: 600px; margin-top:20px; float: left; border:solid 1px"></div>  ';
                   $(b).append(a);
                });
            });
            $(xml).find('places').each(function(){
                $(this).find('place').each(function(){
                    var a=document.createElement('div');
                    a.className="main-wel-div";
                   a.innerHTML='<div style="width: 50px; height: 50px; float: left; border: solid 1px">'+$(this).find('placepic').text()+'</div>  <div style="width: 400px; height: 20px; float: left; "><a href="'+$(this).find('url').text()+'">'+$(this).find('name').text()+','+$(this).find('province').text()+','+$(this).find('country').text()+'</a></div><div style="width: 300px; height: 20px; float: left;">vote:'+$(this).find('votecount').text()+'</div><div style="width: 600px; margin-top:20px; float: left; border:solid 1px"></div>  ';
                   $(b).append(a);
                });
            });
            $('#search-welcome-results').html(b);
        }
}
