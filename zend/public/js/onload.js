
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
                $(document).ready(function(){
                
	$("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled - Adds empty span tag after ul.subnav
	
	$("ul.topnav li span").click(function() { //When trigger is clicked...
		
		//Following events are applied to the subnav itself (moving subnav up and down)
		$(this).parent().find("ul.subnav").slideDown('fast').show(); //Drop down the subnav on click

		$(this).parent().hover(function() {
		}, function(){	
			$(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
		});

		//Following events are applied to the trigger (Hover events for the trigger)
		}).hover(function() { 
			$(this).addClass("subhover"); //On hover over, add class "subhover"
		}, function(){	//On Hover Out
			$(this).removeClass("subhover"); //On hover out, remove class "subhover"
	});

});
        