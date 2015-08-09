
var request=new createXMLHttpRequest();
    
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
function basicaccount()
{
    
     var fname = document.getElementById('fname');
     var lname = document.getElementById('lname');
     var bdm = document.getElementById('bdm');
     var bdd = document.getElementById('bdd');
     var bdy = document.getElementById('bdy');
     var sex = document.getElementById('sex');
     var status = document.getElementById('status');
     var religious = document.getElementById('religious');
     var ccity = document.getElementById('ccity');
     var htown = document.getElementById('htown');
    
     if(fname.value != '' && lname.value != '' && bdm.value != -1 && bdd.value != -1 && bdy.value != -1 && sex.value != 0 && status.value != '' && religious.value != '' && ccity.value != '' && htown.value != '')
         {
            
             var sex1;
             if(sex.value == 1)
                 sex1 = "female";
             else
                 sex1 = "male";
             request.onreadystatechange = basicaccountsettings;
             alert("ajax/updatebasicinfo.php?fname="+fname.value+"&lname="+lname.value+"&bdm="+bdm.value+"&bdd="+bdd.value+"&bdy="+bdy.value+"&sex="+sex1+"&status="+status.value+"&religious="+religious.value+"&ccity="+ccity.value+"&htown="+htown.value);
             request.open("get","ajax/updatebasicinfo.php?fname="+fname.value+"&lname="+lname.value+"&bdm="+bdm.value+"&bdd="+bdd.value+"&bdy="+bdy.value+"&sex="+sex1+"&status="+status.value+"&religious="+religious.value+"&ccity="+ccity.value+"&htown="+htown.value,true);
             request.send(null);
         }
         
 }
 function basicaccountsettings()
 {
    
     if(request.readyState==4 && request.status==200)
         {
             var json = eval('('+request.responseText+')');
             alert(json.status);
         }
 }
 


function basiceducation(pageid)
 {
    
     
     
     if(pageid.value != '')
         {
            
             request.onreadystatechange = educationdetails;
            
             request.open("get","ajax/updatefav.php?action=add&category=employer&pageid="+pageid,true);
             request.send(null);
             
         }
 }
 
function educationdetails()
{
    
    if(request.readyState==4 && request.status==200)
        {
            var jason = eval('('+ request.responseText+')');
            alert(jason.success);
        }
}

function addlanguages(pageid)
{
    
    request.onreadystatechange=addlanguage;
    request.open("get","ajax/updatefav.php?action=add&category=language&pageid="+pageid,true);
    request.send(null);
}
function addlanguage()
{
    
    
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
function addschools(pageid)
{
   
    request.onreadystatechange=addschool;
    request.open("get","ajax/updatefav.php?action=add&category=school&pageid="+pageid,true);
    request.send(null);
}
function addschool()
{
    
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}

function addcolleges(pageid)
{
   
    request.onreadystatechange=addcollege;
    request.open("get","ajax/updatefav.php?action=add&category=college&pageid="+pageid,true);
    request.send(null);
}
function addcollege()
{
    
    if(request.readyState==4 && request.status==200)
        {
            var json=eval('('+request.responseText+')');
            alert(json.success);
        }
}
