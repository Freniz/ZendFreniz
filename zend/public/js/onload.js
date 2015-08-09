
       var prevres=1024;
        window.onload=function()
        {
            redirect();
            
            var hash=window.location.hash;
            var page=hash.substring(1,hash.indexOf('?'));
            var query=hash.substring(hash.indexOf('?')+1,hash.length);
            if(page!=''){
            request.onreadystatechange=changepage;
            request.open("get",page+".php?"+query,true);
            request.send(null);
            }
        }  	
                
        