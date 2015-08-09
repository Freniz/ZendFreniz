/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var imagetimeout;
var imageid=window.location.hash.replace('#', '');
window.onload=function(){
 imageid=window.location.hash.replace('#', '');
imgcom(imageid);
}
function imgcom(imageid)
{
    clearTimeout(imagetimeout);
        request.onreadystatechange=function(){feedback(imageid)};
        request.open("get","ajax/getimgcomment.php?imageid="+imageid,true);
        request.send(null);
    
    
}
function feedback(imageid)
{
    if(request.readyState==4 && request.status==200)
        {
            var xml=request.responseXML;
            /*var img=xml.getElementsByTagName("id")
            if(img){
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
            var dates=xml.getElementsByTagName("date");
            var e1=document.getElementById(imageid+"_com");
            var b=document.createElement('div');
            
            for(var i=0;i<ids.length;i++)
                {
                    var c=document.createElement('div');
                    c.className="commentbox1";
                    var d=document.createElement('div');
                    d.className="datecontainer1";
                    var e=document.createElement('div');
                    e.className="date1";
                    e.innerHTML=dates[i].childNodes[0].nodeValue;
                    d.appendChild(e);
                    c.appendChild(d);
                    var f=document.createElement('div');
                    f.className="commentcon1";
                    f.innerHTML=statuses[i].childNodes[0].nodeValue;
                    c.appendChild(f);
                    var h=document.createElement('div');
                    h.className="imagecon1";
                    h.innerHTML='<img width="50" height="50" src="images/32/32_"'+suserpic[i].childNodes[0].nodeValue+' />';
                    c.appendChild(h);
                   var g=document.createElement('div');
                    g.className="namecon1";
                    g.innerHTML=susername[i].childNodes[0].nodeValue;
                    c.appendChild(g);
                     b.appendChild(c);
                     
                }
            e1.innerHTML=b.innerHTML;
             $("#"+imageid+"_com").niceScroll({cursorborder:"",cursorcolor:"#FFF",boxzoom:false}); // First scrollable DIV
    
            }*/
        var b=document.createElement('div');
        $(xml).find('comment').each(function(){
            var id=$(this).find('id').text();
            var senduser=$(this).find("suserid").text();
            var susername=$(this).find("susername").text();
            var suserpic=$(this).find("suserpic").text();
            var suserfrnd=$(this).find("suserfrnds").text();
            var suservote=$(this).find("suservotes").text();
            var status=$(this).find("status").text();
            var votecount=$(this).find("vote_count").text();
            var vote=$(this).find("vote").text();
            var votecontais=$(this).find("votecontains").text();
            var date=$(this).find("date").text();
            var c=document.createElement('div');
                    c.className="commentbox1";
                    var d=document.createElement('div');
                    d.className="datecontainer1";
                    var e=document.createElement('div');
                    e.className="date1";
                    e.innerHTML=date;
                    d.appendChild(e);
                    c.appendChild(d);
                    var f=document.createElement('div');
                    f.className="commentcon1";
                    f.innerHTML=status;
                    c.appendChild(f);
                    var h=document.createElement('div');
                    h.className="imagecon1";
                    h.innerHTML='<img width="50" height="50" src="images/32/32_"'+suserpic+' />';
                    c.appendChild(h);
                   var g=document.createElement('div');
                    g.className="namecon1";
                    g.innerHTML=susername;
                    c.appendChild(g);
                    b.appendChild(c);
                    
            
        });
        $('#'+imageid+"_com").html(b.innerHTML);
        $("#"+imageid+"_com").niceScroll({cursorborder:"",cursorcolor:"#FFF",boxzoom:false});
            imagetimeout=setTimeout(function(){imgcom(imageid)},3000);
            
        }
}

function askforpin(imageid)
{
    request.onreadystatechange=askfrpin;
        request.open("get","ajax/pinmereq.php?imageid="+imageid,true);
        request.send(null);
}

function askfrpin()
{
    if(request.readyState==4 && request.status==200){
        var json=eval('('+request.responseText+')');
        alert(json.status);
    }
}