/**************************** Arkkimaagi JSLIB functions *******************************/
//Function that does nothing :)
function none(){}

//Shorthand for document.getElementById()
function dg(id){return document.getElementById(id);}

//This function helps creating event listneres (like when something should happen on mouseover)
function listen(e,o,f){
	function W3CDOM_Event(o){this.currentTarget=o;this.preventDefault=function(){window.event.returnValue=false;};return this;}
    if (o.addEventListener)o.addEventListener(e,f,false);
    else if (o.attachEvent)o.attachEvent("on"+e,function(){f(new W3CDOM_Event(o))});
}

//this is the textboxer main class. It has all the functions textboxer uses wrapped inside neatly.
function TextBoxer2(myName,previewURL,textareaName,previewSetup,maxSmilieHeight,bbdropdowns,L){
	window['getTB2']=function(){return eval(myName)};
	var xm=xmlhttp;
	var initOnce=true;
	this.previewMode=false;

	//This function inits the buttons soon after pageload
	//dg('user').innerHTML='<input type="Button" onclick="tb2.init()" value="test">';if(0)
	this.start=function(){window.setTimeout('getTB2().init()',5)};

	//This function loops trough all buttons and dropdown items, selects button icon, adds title to each.
	this.init = function(){
		var bb=bbcodes;
		if(o=dg('bbbuttons')){if(w=dg("explain").firstChild){
				da=document.all;
				function sp(o){c=4;while(!o.bbd&&c-->0){o=o.parentNode;}return o;}
				function mr(e){var o=(da)?event.srcElement:e.target;if(i=o.aID){w.nodeValue=o.title;}if(da){o=sp(o);if(o.bbd){o.bbd.className+=" over";}}}
				function mt(e){var o=(da)?event.srcElement:e.target;if(!o.aID){w.nodeValue=L["bbc"];}if(da){o=sp(o);if(o.bbd){o.bbd.className=o.bbd.className.replace(" over", "");}}}
				function mc(e){var o=(da)?event.srcElement:e.target;if(i=o.aID){getTB2().action(i);}}
				if(initOnce){
					listen("mouseover",o,mr);listen("mouseout",o,mt);listen("click",o,mc);
					initOnce=false;
				}
		}}
		function bp(o,m){o.firstChild.style.backgroundPosition="0px "+(-20*m)+"px";}

		for(i in bb){
			var n = bb[i][1];
			if(o = dg("tb_"+n)){
				fc=o.firstChild;

				et=bb[i][4]+" "+bb[i][2]+bb[i][3];
				o.title=et;
				bp(o,bb[i][0]);
				o.aID=i;

				fc.aID=i;
				fc.title=et;
			}
		}
		for(i in bbdropdowns){if(o=dg("bbd_"+i)){bp(o,bbdropdowns[i]);o.bbd=o;}}
		getTB2().previewMode=true;
	}

	//This function handles what happens when user clicks the button.
	this.action = function(id){

		function wrap(lft,mdl,rgt,chk){
			txa = dg(textareaName);
			out="";
			if (document.selection){
				txa.focus();
				sel = document.selection.createRange();
				mv=sel.text.length;
				if (mdl==""&&sel!=""){ out=sel.text; if(chk)document.selection.createRange().text = lft + sel.text + rgt; }
				else if(chk){sel.text=lft+mdl+rgt;mv=mdl.length}
				var s=-rgt.length-mv;var e=-rgt.length;
				if(chk==1){s=lft.length;e=mv;}
				if(chk){
					var c='character';
					sel.moveStart(c,s);
					sel.moveEnd(c,e);
					sel.select();
				}
			}else if (txa.setSelectionRange){
				var selS = txa.selectionStart;
				var selE = txa.selectionEnd;
				var s1 = (txa.value).substring(0,selS);
				var s2 = (txa.value).substring(selS, selE);
				var s3 = (txa.value).substring(selE, txa.textLength);
				out=s2;mv=0;
				if (mdl!=""){mv=mdl.length-s2.length;s2=mdl;}
				txa.value=s1+lft+s2+rgt+s3;
				txa.focus();
				txa.setSelectionRange(selS+lft.length,selE+lft.length+mv);
			}else{
				txa.value+=lft+mdl+rgt;
			}
			return out;
		}

		function promptIt(attr,inner,opt,n){
			sel=wrap("","","",0);
			txt=window.prompt(attr, sel);
			if(txt!=null&&(opt||txt!="")){
				url=window.prompt(inner, sel);
				if(url!=null&&url!=""){
					if((txt!=url&&txt!="")||!opt){lft="["+n+"="+url+"]";}
					else{txt=url;}
					wrap(lft,txt,rgt,2);
				}
			}
		}

		var lft = bbcodes[id][2];
		var rgt = bbcodes[id][3];
		var n=bbcodes[id][1];
		if(getTB2().previewMode||n=="preview"){
			if(n.substring(0,5)=='xtra_'){
				eval(n+"()");
			}else{
				switch(n){
					case "copy":
					case "cut":
					case "paste":
						txtarea=dg(textareaName);
						txtarea.focus();
						copied=document.selection.createRange();
						copied.execCommand(n);
						break;
					case "preview":
						this.togglePreview();
						break;
					case "url":
					case "email":
					case "page":
/*					case "link":
						promptIt(L[n+"p"],L[n],1,n);
						break;
	*/
					case "ac":
					case "user":
						promptIt(L[n+"p"],L[n],0,n);
						break;
/*	These are commented out, as Seditio does not support this yet.
					case "image":
						promptIt(L[n+"p"],L[n],1,"img");
						break;
					case "code"://*/
					case "quote":
						quo=window.prompt(L[n], "");
						if(quo!=null&&quo!=""){lft="["+n+"="+quo+"]";}
						wrap(lft,"",rgt,1);
						break;//*/
					default:
						wrap(lft,"",rgt,1);
						break;

				}
			}
		}else if(!getTB2().previewMode && n!="preview"){
			alert(L["warnPM"]);
		}
	}

	//Preview function. If browser is good enough, it loads the preview data from the server.
	this.togglePreview = function() {
		var fail=function(){if(a=dg("tb_preview"))show(a,0);alert(L["noSupport"]);};
		var s=[["absolute","hidden"],["static","visible"]];
		var pi=["<div class=\"previewInfo\">","</div>"];
		function show(o,z){o.style.position=s[z][0];o.style.visibility=s[z][1];o.bbv=z;}
		if((t=dg(textareaName))&&(p=dg("preview"))){
			if(p.bbv){
				show(p,0);
				p.innerHTML="";
				show(t,1);
				getTB2().previewMode=true;
			}else{
				getFromServer( previewSetup+"&p=1&t="+encodeURIComponent(t.value),
					function(){
						if(xm.readyState==4){
							p.innerHTML=pi[0]+L["PM"]+pi[1]+decodeURIComponent(xm.responseText);
						}
					}, function(){
						show(t,0);
						p.innerHTML=pi[0]+L["loadingPM"]+pi[1];
						show(p,1);
						getTB2().previewMode=false;
					}, fail);
				}
			}else{fail();}
	}

	//Loads smilies to textboxer is browser is good enough, or popups a new window with smilies.
	this.loadSmilies = function(){
		var fail=function(){help("smilies",formName,textareaName)};
		function getSmilies(){
			if(p=dg("smilies")){
				getFromServer("s=1",function(){
					if(xm.readyState==4){
						var dbc=document.body.clientWidth;
						try{eval(xm.responseText);}catch(e){fail();}
						bbd1=dg("bbd_1");
						bbd1.className+=" over";
						var pcw=p.clientWidth;
						pcw=Math.min(dbc-20,Math.max(pcw,(p.clientHeight/maxSmilieHeight)*pcw));
						p.style.width=pcw+"px";
						posx=findPosX(p);
						p.style.marginLeft=Math.max(-posx,Math.min(0,dbc-pcw-posx))+"px";
						bbd1.className=bbd1.className.replace(" over", "");
						bbd1.className=bbd1.className.replace(" over", "");
					}
				},function(){p.innerHTML=L["loadingSMI"];},fail);
			}else{fail();}
		}

		function findPosX(o){var lft=0;if(o.offsetParent){while(o.offsetParent){lft+=o.offsetLeft;o=o.offsetParent;}}else if(o.x){lft+=o.x;}return lft;}

		if(getTB2().previewMode){if(xm&&popupSmilies==0){window.setTimeout(getSmilies,1);}else{fail();}}
		else{alert(L["warnPM"]);}
	}

	//this gets data from server.
	function getFromServer(query,runme,prep,fail){
		if(xm&&(x=dg("x"))){xm.open("POST",previewURL,true);xm.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			xm.onreadystatechange=function(){
				if(xm.readyState==4){
					if (xmlhttp.status==200)runme();
					else if (xmlhttp.status==404) alert(L["preview404"]);
					else alert(L["previewError"]+xmlhttp.status);
				}
			};
			prep();
			xm.send("x="+x.value+"&"+query);
		}else{fail();}
	}
}