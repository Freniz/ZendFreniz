/**
 *  author:		Timothy Groves - http://www.brandspankingnew.net
 *	version:	1.2 - 2006-11-17
 *              1.3 - 2006-12-04
 *              2.0 - 2007-02-07
 *
 */

var useBSNns;

if (useBSNns)
{
	if (typeof(bsn) == "undefined")
		bsn = {}
	_bsn = bsn;
}
else
{
	_bsn = this;
}



if (typeof(_bsn.Autosuggest) == "undefined")
	_bsn.Autosuggest = {}













_bsn.AutoSuggest = function (fldID, param)
{
	// no DOM - give up!
	//
	    if (!document.getElementById)
		return false;
	
	
	
	// get field via DOM
	//
	this.fld = _bsn.DOM.getElement(fldID);
	if (!this.fld){
            
		return false;}
	
	
	
	
	// init variables
	//
	this.sInput 		= "";
	this.nInputChars 	= 0;
	this.aSuggestions 	= [];
	this.iHighlighted 	= 0;
	
	
	
	
	// parameters object
	//
	this.oP = (param) ? param : {};
	// defaults	
	//
	if (!this.oP.minchars)									this.oP.minchars = 1;
	if (!this.oP.method)									this.oP.meth = "get";
	if (!this.oP.varname)									this.oP.varname = "input";
	if (!this.oP.className)									this.oP.className = "autosuggest";
	if (!this.oP.timeout)									this.oP.timeout = 3500;
	if (!this.oP.delay)										this.oP.delay = 500;
	if (!this.oP.offsety)									this.oP.offsety = -5;
	if (!this.oP.shownoresults)								this.oP.shownoresults = true;
	if (!this.oP.noresults)									this.oP.noresults = "No results!";
	if (!this.oP.maxheight && this.oP.maxheight !== 0)		this.oP.maxheight = 250;
	if (!this.oP.cache && this.oP.cache != false)			this.oP.cache = true;
	
	
	
	
	
	// set keyup handler for field
	// and prevent autocomplete from client
	//
	var pointer = this;
	
	// NOTE: not using addEventListener because UpArrow fired twice in Safari
	//_bsn.DOM.addEvent( this.fld, 'keyup', function(ev){ return pointer.onKeyPress(ev); } );
	
	this.fld.onkeyup 		= function(ev){return pointer.onKeyUp(ev);}
	
	this.fld.setAttribute("autocomplete","off");
}









_bsn.AutoSuggest.prototype.onKeyUp = function(ev)
{
	var key = (window.event) ? window.event.keyCode : ev.keyCode;
	


	// set responses to keydown events in the field
	// this allows the user to use the arrow keys to scroll through the results
	// ESCAPE clears the list
	// TAB sets the current highlighted value
	//
        var RETURN = 13;
	var TAB = 9;
	var ESC = 27;
	
	var ARRUP = 38;
	var ARRDN = 40;
	var BACKSPACE=8;
        var DELETE=46;
	var bubble = true;
        
        switch(key)
	{
                case BACKSPACE:
                        if(this.fld.value=="")
                        this.clearSuggestions();
                        else
                            this.getSuggestions(this.fld.value);
                        break;
                case DELETE:
                        if(this.fld.value=="")
                        this.clearSuggestions();
                        else
                            this.getSuggestions(this.fld.value);
                        break;
                case RETURN:
			this.setHighlightedValue();
			bubble = false;
			break;


		case ESC:
                        this.clearSuggestions();
			break;

		case ARRUP:
			this.changeHighlight(key);
			bubble = false;
			break;


		case ARRDN:
			this.changeHighlight(key);
			bubble = false;
			break;
		
		
		default:
			this.getSuggestions(this.fld.value);
	}

	return bubble;
	

}








_bsn.AutoSuggest.prototype.getSuggestions = function (val)
{
	
	// input length is less than the min required to trigger a request
	// reset input string
	// do nothing
	//
	if (val.length < this.oP.minchars)
	{
		this.sInput = "";
		return false;
	}
	
	// if caching enabled, and user is typing (ie. length of input is increasing)
	// filter results out of aSuggestions from last request
	//
	
		this.sInput = val;
		this.nInputChars = val.length;


		var pointer = this;
		clearTimeout(this.ajID);
		this.ajID = setTimeout( function() {pointer.doAjaxRequest()}, this.oP.delay );
	

	return false;
}





_bsn.AutoSuggest.prototype.doAjaxRequest = function ()
{
	
	var pointer = this;
	
	// create ajax request
	var url = this.oP.script+this.oP.varname+"="+this.fld.value+"&ref=widget";
	var meth = this.oP.meth;
	var onSuccessFunc = function (req) {pointer.setSuggestions(req)};
	var onErrorFunc = function (status) {alert("AJAX error: "+status);};
        var myAjax = new _bsn.Ajax();
		
        myAjax.makeRequest( url, meth, onSuccessFunc, onErrorFunc );
}





_bsn.AutoSuggest.prototype.setSuggestions = function (req)
{
	this.aSuggestions = [];
	
	
		var xml = req.responseXML;
	
		// traverse xml
		//
		        if(this.oP.type=='pages'){
                    var pageid=xml.getElementsByTagName('pageid');
                    var pagename=xml.getElementsByTagName('pagename');
                    var pagepic=xml.getElementsByTagName('pagepic');
                    var category=xml.getElementsByTagName('category');
                    var vote=xml.getElementsByTagName('vote');
                    var votecount=xml.getElementsByTagName('votecount');
                    var url=xml.getElementsByTagName('url');
                    for (var i=0;i<pageid.length;i++)
                    {
                            this.aSuggestions.push(  {'pageid':pageid[i].childNodes[0].nodeValue, 'pagename':pagename[i].childNodes[0].nodeValue, 'pagepic':pagepic[i].childNodes[0].nodeValue, 'category':category[i].childNodes[0].nodeValue, 'vote':vote[i].childNodes[0].nodeValue,'votecount':votecount[i].childNodes[0].nodeValue,'url':url[i].childNodes[0].nodeValue} );
                    }
                }
                else if(this.oP.type=='users' || this.oP.type=='friends')
                    {
                        var userid=xml.getElementsByTagName('userid');
                        var username=xml.getElementsByTagName('username');
                        var propic=xml.getElementsByTagName('propic');
                        var votes=xml.getElementsByTagName('votes');
                        var votecount=xml.getElementsByTagName('votecount');
                        var url=xml.getElementsByTagName('url');
                        for(var i=0;i<userid.length;i++)
                            {
                                this.aSuggestions.push(  {'userid':userid[i].childNodes[0].nodeValue, 'username':username[i].childNodes[0].nodeValue, 'propic':propic[i].childNodes[0].nodeValue, 'vote':votes[i].childNodes[0].nodeValue,'votecount':votecount[i].childNodes[0].nodeValue,'url':url[i].childNodes[0].nodeValue} );
                            }
                    }
                    else if(this.oP.type=="places"){
                        var id=xml.getElementsByTagName('id');
                        var name=xml.getElementsByTagName('name');
                        var country=xml.getElementsByTagName('country');
                        var province=xml.getElementsByTagName('province');
                        var placepic=xml.getElementsByTagName('placepic');
                        var votes=xml.getElementsByTagName('votes');
                        var votecount=xml.getElementsByTagName('votecount');
                        var url=xml.getElementsByTagName('url');
                        for(var i=0;i<id.length;i++)
                            {
                                this.aSuggestions.push(  {'id':id[i].childNodes[0].nodeValue, 'name':name[i].childNodes[0].nodeValue, 'country':country[i].childNodes[0].nodeValue, 'province':province[i].childNodes[0].nodeValue, 'placepic':placepic[i].childNodes[0].nodeValue, 'vote':votes[i].childNodes[0].nodeValue,'votecount':votecount[i].childNodes[0].nodeValue,'url':url[i].childNodes[0].nodeValue} );
                            }
                            
                    }
                    else if(this.oP.type=="all"){
                    	var id=xml.getElementsByTagName('id');
                        var name=xml.getElementsByTagName('name');
                        var pic=xml.getElementsByTagName('pic');
                        var type=xml.getElementsByTagName('type');
                        var votes=xml.getElementsByTagName('votes');
                        var votecount=xml.getElementsByTagName('votecount');
                        var url=xml.getElementsByTagName('url');
                        for(var i=0;i<id.length;i++)
                            {
                            	this.aSuggestions.push(  {'id':id[i].childNodes[0].nodeValue, 'name':name[i].childNodes[0].nodeValue, 'pic':pic[i].childNodes[0].nodeValue, 'type':type[i].childNodes[0].nodeValue, 'vote':votes[i].childNodes[0].nodeValue,'votecount':votecount[i].childNodes[0].nodeValue,'url':url[i].childNodes[0].nodeValue} );
                            }
                    }
	
	
	this.idAs = "as_"+this.fld.id;
	

	this.createList(this.aSuggestions);

}














_bsn.AutoSuggest.prototype.createList = function(arr)
{
	var pointer = this;
	
	
	// get rid of old list
	// and clear the list removal timeout
	//
	_bsn.DOM.removeElement(this.idAs);
	this.killTimeout();
	
	
	// create holding div
	//
	var div = _bsn.DOM.createElement("div", {id:this.idAs, className:this.oP.className});	
	
	var hcorner = _bsn.DOM.createElement("div", {className:"as_corner"});
	var hbar = _bsn.DOM.createElement("div", {className:"as_bar"});
	var header = _bsn.DOM.createElement("div", {className:"as_header"});
	header.appendChild(hcorner);
	header.appendChild(hbar);
	div.appendChild(header);
	
	
	
	
	// create and populate ul
	//
	var ul = _bsn.DOM.createElement("ul", {id:"as_ul"});
	
	
	
	
	// loop throught arr of suggestions
	// creating an LI element for each suggestion
	//
        for (var i=0;i<arr.length;i++)
	{
		// format output with the input enclosed in a EM element
		// (as HTML, not DOM)
		//
                if(this.oP.type=='pages'){
		
                var pagename = arr[i].pagename;
		var st = pagename.toLowerCase().indexOf( this.sInput.toLowerCase() );
		var output = pagename.substring(0,st) + "<em>" + pagename.substring(st, st+this.sInput.length) + "</em>" + pagename.substring(st+this.sInput.length);
		var pagepic=arr[i].pagepic;
		var image="<img src='images/32/32_"+pagepic+"' height='32' width='32' />"
		var span2 		= _bsn.DOM.createElement("div", {style:"width:50px; float:left; height:50px;"}, output, true);
		var span1 		= _bsn.DOM.createElement("div", {style:"float:left"},image , true);
		var span 		= _bsn.DOM.createElement("span", {style:"float:left"} );
		
                	var br			= _bsn.DOM.createElement("br", {});
			span2.appendChild(br);
			var small		= _bsn.DOM.createElement("small", {},"votes :"+arr[i].votecount);
			span2.appendChild(small);
                        span.appendChild(span1);
                        span.appendChild(span2);
		}
              else if(this.oP.type=='places'){
			var placename = arr[i].name+","+arr[i].province+","+arr[i].country;
		var st = placename.toLowerCase().indexOf( this.sInput.toLowerCase() );
		var output = placename.substring(0,st) + "<em>" + placename.substring(st, st+this.sInput.length) + "</em>" + placename.substring(st+this.sInput.length);
		var placepic=arr[i].placepic;
		var image="<img src='images/32/32_"+placepic+"' height='32' width='32' />"
		var span2 		= _bsn.DOM.createElement("div", {style:"width:50px; float:left; height:50px;"}, output, true);
		var span1 		= _bsn.DOM.createElement("div", {style:"float:left"},image , true);
		var span 		= _bsn.DOM.createElement("span", {style:"float:left"} );
		
                	var br			= _bsn.DOM.createElement("br", {});
			span2.appendChild(br);
			var small		= _bsn.DOM.createElement("small", {},"votes :"+arr[i].votecount);
			span2.appendChild(small);
                        span.appendChild(span1);
                        span.appendChild(span2);
		} else if(this.oP.type=='users' || this.oP.type=="friends"){
                    var username=arr[i].username;
                    var st = username.toLowerCase().indexOf( this.sInput.toLowerCase() );
                    var output = username.substring(0,st) + "<em>" + username.substring(st, st+this.sInput.length) + "</em>" + username.substring(st+this.sInput.length);
                    var propic=arr[i].propic;
                    var image="<img src='images/32/32_"+propic+"' height='32' width='32' />"
                    var span2 		= _bsn.DOM.createElement("div", {style:"width:50px; float:left; height:50px;"}, output, true);
                    var span1 		= _bsn.DOM.createElement("div", {style:"float:left"},image , true);
                    var span 		= _bsn.DOM.createElement("span", {style:"float:left"} );

                            var br			= _bsn.DOM.createElement("br", {});
                            span2.appendChild(br);
                            var small		= _bsn.DOM.createElement("small", {},"votes :"+arr[i].votecount);
                            span2.appendChild(small);
                            span.appendChild(span1);
                            span.appendChild(span2);
                }
                else if(this.oP.type=='all'){
                    var name=arr[i].name;
                    var st = name.toLowerCase().indexOf( this.sInput.toLowerCase() );
                    var output = name.substring(0,st) + "<em>" + name.substring(st, st+this.sInput.length) + "</em>" + name.substring(st+this.sInput.length);
                    var propic=arr[i].pic;
                    var image="<img src='images/32/32_"+propic+"' height='32' width='32' />"
                    var span2 		= _bsn.DOM.createElement("div", {style:"width:50px; float:left; height:50px;"}, output, true);
                    var span1 		= _bsn.DOM.createElement("div", {style:"float:left"},image , true);
                    var span 		= _bsn.DOM.createElement("span", {style:"float:left"} );

                            var br			= _bsn.DOM.createElement("br", {});
                            span2.appendChild(br);
                            var small		= _bsn.DOM.createElement("small", {},"Type : "+arr[i].type+"; votes :"+arr[i].votecount);
                            span2.appendChild(small);
                            span.appendChild(span1);
                            span.appendChild(span2);
                }
		var a 			= _bsn.DOM.createElement("a", {href: window.location.hash});
		
		var tl 		= _bsn.DOM.createElement("span", {className:"tl"}, " ");
		var tr 		= _bsn.DOM.createElement("span", {className:"tr"}, " ");
		a.appendChild(tl);
		a.appendChild(tr);
		
		a.appendChild(span);
		
		a.name = i+1;
		a.onclick = function () {pointer.setHighlightedValue();return false;}
		a.onmouseover = function () {pointer.setHighlight(this.name);}
		
		var li 			= _bsn.DOM.createElement(  "li", {}, a  );
		
		ul.appendChild( li );
	}
	
	
	// no results
	//
	if (this.oP.type!='pages' && arr.length == 0)
	{
		var li 			= _bsn.DOM.createElement(  "li", {className:"as_warning"}, this.oP.noresults  );
		
		ul.appendChild( li );
	}
	div.appendChild( ul );
	
	
	var fcorner = _bsn.DOM.createElement("div", {className:"as_corner"});
	var fbar = _bsn.DOM.createElement("div", {className:"as_bar"});
	var footer = _bsn.DOM.createElement("div", {className:"as_footer"});
	footer.appendChild(fcorner);
	footer.appendChild(fbar);
	div.appendChild(footer);
	
	
	// get position of target textfield
	// position holding div below it
	// set width of holding div to width of field
	//
	var pos = _bsn.DOM.getPos(this.fld);
	
	div.style.left 		= pos.x + "px";
	div.style.top 		= ( pos.y + this.fld.offsetHeight + this.oP.offsety ) + "px";
	div.style.width 	= this.fld.offsetWidth + "px";
	
	
	
	// set mouseover functions for div
	// when mouse pointer leaves div, set a timeout to remove the list after an interval
	// when mouse enters div, kill the timeout so the list won't be removed
	//
	div.onmouseover 	= function(){pointer.killTimeout()}
	div.onmouseout 		= function(){pointer.resetTimeout()}


	// add DIV to document
	//
        div.style.position='absolute';
        if(this.oP.appendto=='body')
        document.getElementsByTagName(this.oP.appendto)[0].appendChild(div);
        else
            document.getElementById(this.oP.appendto).appendChild(div);
    	
	document.getElementById('search-top-loading').style.display='none';
	// currently no item is highlighted
	//
	if(arr.length>0){
	this.iHighlighted = 1;
	this.setHighlight(1);
	}
	else
	{
	this.iHighlighted=0;
	}
	
	
	
	
	
	// remove list after an interval
	//
	var pointer = this;
	this.toID = setTimeout(function () {pointer.clearSuggestions()}, this.oP.timeout);
}















_bsn.AutoSuggest.prototype.changeHighlight = function(key)
{	
	var list = _bsn.DOM.getElement("as_ul");
	if (!list)
		return false;
	
	var n;

	if (key == 40)
		n = this.iHighlighted + 1;
	else if (key == 38)
		n = this.iHighlighted - 1;
	
	
	if (n > list.childNodes.length)
		n = list.childNodes.length;
	if (n < 1)
		n = 1;
	
	
	this.setHighlight(n);
}



_bsn.AutoSuggest.prototype.setHighlight = function(n)
{
	var list = _bsn.DOM.getElement("as_ul");
	if (!list)
		return false;
	if (this.iHighlighted > 0)
		this.clearHighlight();
	
	this.iHighlighted = Number(n);
	
	list.childNodes[this.iHighlighted-1].className = "as_highlight";


	this.killTimeout();
}


_bsn.AutoSuggest.prototype.clearHighlight = function()
{
	var list = _bsn.DOM.getElement("as_ul");
	if (!list)
		return false;
	
	if (this.iHighlighted > 0)
	{
		list.childNodes[this.iHighlighted-1].className = "";
		this.iHighlighted = 0;
	}
}


_bsn.AutoSuggest.prototype.setHighlightedValue = function ()
{
	if (this.iHighlighted)
	{
            if(this.oP.type=='pages'){
                if(this.oP.category=='books')
                    addfavbooks(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                else if(this.oP.category=='musics')
                    addfavmusics(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                else if(this.oP.category=='movies')
                    addfavmovies(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                else if(this.oP.category=='celebrities')
                    addfavcelebrities(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                else if(this.oP.category=='games')
                    addfavgames(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                else if(this.oP.category=='sports')
                    addfavsports(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                else if(this.oP.category=='other')
                    addfavothers(this.aSuggestions[ this.iHighlighted-1 ].pageid);
                this.sInput = this.fld.value = this.aSuggestions[ this.iHighlighted-1 ].pagename;
		
		// move cursor to end of input (safari)
		//
		this.fld.focus();
		if (this.fld.selectionStart)
			this.fld.setSelectionRange(this.sInput.length, this.sInput.length);
		

		this.clearSuggestions();
		
		// pass selected object to callback function, if exists
		//
		if (typeof(this.oP.callback) == "function")
			this.oP.callback( this.aSuggestions[this.iHighlighted-1] );
            }
            else if(this.oP.type=='places'){
            this.fld.value=this.aSuggestions[this.iHighlighted-1].name;
            this.clearSuggestions();
            document.getElementById(this.fld.id+"-hidden").value=this.aSuggestions[this.iHighlighted-1].id;
            }
            else if(this.oP.type=='users'){
                window.location.href=this.aSuggestions[ this.iHighlighted-1 ].url;
            }
            else if(this.oP.type=='friends'){
                var hiddenValue= document.getElementById(this.fld.id+"_hidden").value;
                var userarray=hiddenValue.split(",");
                var iscontains=false;
                for(var k in userarray){
                    if(this.aSuggestions[this.iHighlighted-1].userid==userarray[k])
                        iscontains=true;
                }
                if(iscontains)
                    {
                        alert("user already in pinnedpeople");
                        
                    }
                    else
                        {
                            var pinpeople=document.getElementById(this.fld.id+"_display");
                            if(userarray.length==1){
                                if(userarray[0]==""){
                                userarray=this.aSuggestions[this.iHighlighted-1].userid;
                                }
                                else
                                userarray.push(this.aSuggestions[this.iHighlighted-1].userid);
                            }
                            else
                                userarray.push(this.aSuggestions[this.iHighlighted-1].userid);
                            var div_pin=document.createElement("div");
                            var cons = userarray.constructor.toString();
                            var match = cons.match(/(\w+)\(/);
                            if (match) {
                            cons = match[1].toLowerCase();
                            }
                            var type;
                            var types = ["boolean", "number", "string", "array"];
                            for (var key in types) {
                                if (cons == types[key]) {
                                    type=types[key];
                                    break;
                                }
                            }
                            var a=document.createElement('a');
                              a.id=this.aSuggestions[this.iHighlighted-1].userid+"_"+this.fld.id+"_reqpinnedpeople";
                              a.setAttribute("onclick", 'cancelpinrequser(\''+this.aSuggestions[this.iHighlighted-1].userid+'\',\''+this.fld.id+'\')');
                              a.innerHTML=this.aSuggestions[this.iHighlighted-1].username+",";
                              pinpeople.appendChild(a);
                            document.getElementById(this.fld.id+"_hidden").value=userarray;
                            this.fld.value="";
                        }
                        this.clearSuggestions();
            }
            else if(this.oP.type=='all'){
                this.fld.value='';
                window.location.href=this.aSuggestions[ this.iHighlighted-1 ].url;
            }
            
	} else{
            if(this.oP.type=='pages'){
            	if(this.fld.value!=''){
				
                if(this.oP.category=='books')
                    createpage(this.fld.value,this.oP.category,'Book');
                else if(this.oP.category=='musics')
                    createpage(this.fld.value,this.oP.category,'Music');
                else if(this.oP.category=='movies')
                    createpage(this.fld.value,this.oP.category,'Movie');
                else if(this.oP.category=='celebrities')
                    createpage(this.fld.value,this.oP.category,'Public figure');
                else if(this.oP.category=='games')
                    createpage(this.fld.value,this.oP.category,'Game');
                else if(this.oP.category=='sports')
                    createpage(this.fld.value,this.oP.category,'Sport');
                else if(this.oP.category=='school')
            		createpage(this.fld.value,'Organisations','School/University');
            	else if(this.oP.category=='college')
            		createpage(this.fld.value,'Organisations','School/University');
            	else if(this.oP.category=='work')
            		createpage(this.fld.value,'Organisations','Comapny');
                }
            }
        }
        
}













_bsn.AutoSuggest.prototype.killTimeout = function()
{
	clearTimeout(this.toID);
}

_bsn.AutoSuggest.prototype.resetTimeout = function()
{
	clearTimeout(this.toID);
	var pointer = this;
	this.toID = setTimeout(function () {pointer.clearSuggestions()}, 4000);
}







_bsn.AutoSuggest.prototype.clearSuggestions = function ()
{
	
	this.killTimeout();
	
	var ele = _bsn.DOM.getElement(this.idAs);
	var pointer = this;
	if (ele)
	{
		var fade = new _bsn.Fader(ele,1,0,250,function () {_bsn.DOM.removeElement(pointer.idAs)});
	}
}










// AJAX PROTOTYPE _____________________________________________


if (typeof(_bsn.Ajax) == "undefined")
	_bsn.Ajax = {}



_bsn.Ajax = function ()
{
	this.req = {};
	this.isIE = false;
}



_bsn.Ajax.prototype.makeRequest = function (url, meth, onComp, onErr)
{
    document.getElementById('search-top-loading').style.display='block';
    	if (meth != "POST")
		meth = "GET";
	
	this.onComplete = onComp;
	this.onError = onErr;
	
	var pointer = this;
	
	// branch for native XMLHttpRequest object
	if (window.XMLHttpRequest)
	{
		this.req = new XMLHttpRequest();
		this.req.onreadystatechange = function () {pointer.processReqChange()};
		this.req.open("GET", url, true); //
		this.req.send(null);
	// branch for IE/Windows ActiveX version
	}
	else if (window.ActiveXObject)
	{
		this.req = new ActiveXObject("Microsoft.XMLHTTP");
		if (this.req)
		{
			this.req.onreadystatechange = function () {pointer.processReqChange()};
			this.req.open(meth, url, true);
			this.req.send();
		}
	}
}


_bsn.Ajax.prototype.processReqChange = function()
{
	
	// only if req shows "loaded"
	if (this.req.readyState == 4) {
		// only if "OK"
		if (this.req.status == 200)
		{
			this.onComplete( this.req );
		} else {
			this.onError( this.req.status );
		}
	}
}










// DOM PROTOTYPE _____________________________________________


if (typeof(_bsn.DOM) == "undefined")
	_bsn.DOM = {}




_bsn.DOM.createElement = function ( type, attr, cont, html )
{
	var ne = document.createElement( type );
	if (!ne)
		return false;
		
	for (var a in attr)
		ne[a] = attr[a];
		
	if (typeof(cont) == "string" && !html)
		ne.appendChild( document.createTextNode(cont) );
	else if (typeof(cont) == "string" && html)
		ne.innerHTML = cont;
	else if (typeof(cont) == "object")
		ne.appendChild( cont );

	return ne;
}





_bsn.DOM.clearElement = function ( id )
{
	var ele = this.getElement( id );
	
	if (!ele)
		return false;
	
	while (ele.childNodes.length)
		ele.removeChild( ele.childNodes[0] );
	
	return true;
}









_bsn.DOM.removeElement = function ( ele )
{
	var e = this.getElement(ele);
	
	if (!e)
		return false;
	else if (e.parentNode.removeChild(e))
		return true;
	else
		return false;
}





_bsn.DOM.replaceContent = function ( id, cont, html )
{
	var ele = this.getElement( id );
	
	if (!ele)
		return false;
	
	this.clearElement( ele );
	
	if (typeof(cont) == "string" && !html)
		ele.appendChild( document.createTextNode(cont) );
	else if (typeof(cont) == "string" && html)
		ele.innerHTML = cont;
	else if (typeof(cont) == "object")
		ele.appendChild( cont );
}









_bsn.DOM.getElement = function ( ele )
{
	if (typeof(ele) == "undefined")
	{
		return false;
	}
	else if (typeof(ele) == "string")
	{
            
		var re = document.getElementById( ele );
		if (!re)
                    return false;
		else if (typeof(re.appendChild) != "undefined" ) {
			return re;
		} else {
                    
			return false;
		}
	}
	else if (typeof(ele.appendChild) != "undefined")
		return ele;
	else
		return false;
}







_bsn.DOM.appendChildren = function ( id, arr )
{
	var ele = this.getElement( id );
	
	if (!ele)
		return false;
	
	
	if (typeof(arr) != "object")
		return false;
		
	for (var i=0;i<arr.length;i++)
	{
		var cont = arr[i];
		if (typeof(cont) == "string")
			ele.appendChild( document.createTextNode(cont) );
		else if (typeof(cont) == "object")
			ele.appendChild( cont );
	}
}









_bsn.DOM.getPos = function ( ele )
{
	var ele = this.getElement(ele);

	var obj = ele;

	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;


	var obj = ele;
	
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;

	return {x:curleft, y:curtop}
}










// FADER PROTOTYPE _____________________________________________



if (typeof(_bsn.Fader) == "undefined")
	_bsn.Fader = {}





_bsn.Fader = function (ele, from, to, fadetime, callback)
{	
	if (!ele)
		return false;
	
	this.ele = ele;
	
	this.from = from;
	this.to = to;
	
	this.callback = callback;
	
	this.nDur = fadetime;
		
	this.nInt = 50;
	this.nTime = 0;
	
	var p = this;
	this.nID = setInterval(function() {p._fade()}, this.nInt);
}




_bsn.Fader.prototype._fade = function()
{
	this.nTime += this.nInt;
	
	var ieop = Math.round( this._tween(this.nTime, this.from, this.to, this.nDur) * 100 );
	var op = ieop / 100;
	
	if (this.ele.filters) // internet explorer
	{
		try
		{
			this.ele.filters.item("DXImageTransform.Microsoft.Alpha").opacity = ieop;
		} catch (e) { 
			// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
			this.ele.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+ieop+')';
		}
	}
	else // other browsers
	{
		this.ele.style.opacity = op;
	}
	
	
	if (this.nTime == this.nDur)
	{
		clearInterval( this.nID );
		if (this.callback != undefined)
			this.callback();
	}
}



_bsn.Fader.prototype._tween = function(t,b,c,d)
{
	return b + ( (c-b) * (t/d) );
}