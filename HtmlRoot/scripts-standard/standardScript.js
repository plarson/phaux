
function addParameter(uri, key, value)
{
	var separator = "?";

	if(uri.indexOf("?") >= 0)
	    separator = "&";
	return uri + separator + key + "=" + escape(value);
}

function xmlLiveUpdaterForForm(aForm,uri){
	var newUri = uri;
	for (i=0; i<aForm.childNodes.length; i++) {
		
		if (aForm.childNodes[i].tagName == "INPUT") {
			if (aForm.childNodes[i].type == "checkbox" ||
					aForm.childNodes[i].type == "radio" ) {
				if (aForm.childNodes[i].checked) {
					newUri = addParameter(newUri,aForm.childNodes[i].name,aForm.childNodes[i].value);
				}else if (aForm.childNodes[i].type != "radio" ){
					newUri = addParameter(newUri,aForm.childNodes[i].name,aForm.childNodes[i].value);
				}
			}else{
			
				newUri = addParameter(newUri,aForm.childNodes[i].name,aForm.childNodes[i].value);
			}
		}else if(aForm.childNodes[i].tagName == "SELECT") {
			newUri = addParameter(newUri,
						aForm.childNodes[i].name,
						aForm.childNodes[i].options[aForm.childNodes[i].selectedIndex].value);
		}else if (aForm.childNodes[i].tagName == "TEXTAREA") {
			newUri = addParameter(newUri,aForm.childNodes[i].name,aForm.childNodes[i].value);
		}
	}
	xmlLiveUpdaterUri(newUri);
}
function createDataPacket(parameters) {
	var dataPacket = "";
	for(var i=0; i<parameters.length; i++) {
		var param = parameters[i] + ""; // ensure parameter is a string
		dataPacket += param.length + "\r" + param + "\r";
	}

	return dataPacket;
}

function makeXmlCallback(uri, callbackId) {
	return callback;

	function callback() {
		// Copy arguments to the function into a temp array
		var argsCalledWith = new Array(arguments.length);
		for(var i=0; i<arguments.length; i++)
			argsCalledWith[i] = arguments[i];

		var customHandler = null;
		// If last arg passed in is a function then it is the results handler function
		if (argsCalledWith.length > 0) {
			if (typeof argsCalledWith[argsCalledWith.length-1] == "function")
				customHandler = argsCalledWith.pop();
		}

		// create a closure to send to liveUpdater to use
		function helper() {
			return addParameter(uri, callbackId, createDataPacket(argsCalledWith));
		};

		function xmlProcessCallbackResults(response) {
			var serverResult = new Object();
			for(i=0; i < response.documentElement.childNodes.length; i++) {
				var child = response.documentElement.childNodes[i];
				if (child.tagName == "response")
					serverResult.text = child.firstChild.data;
				else
					xmlLiveProcessOne(child);
			}
			if (customHandler) customHandler(serverResult);
		}

		xmlLiveUpdater(helper, xmlProcessCallbackResults)();
	};
}

function xmlInstallCallback(id, callbackName, uri, callbackId) {
	var element = document.getElementById(id);
	if (element == null) {
		window.setTimeout("xmlInstallCallback(''" + id + "'', ''" + callbackName + "'', ''" + uri + "','" + callbackId + "'')", 250);
		return;
	}
	element[callbackName] = makeXmlCallback(uri, callbackId);
}




// Show the debug window
function showDebug() {
  window.top.debugWindow =
      window.open("",
                  "Debug",
                  "left=0,top=0,width=300,height=700,scrollbars=yes,"
                  +"status=yes,resizable=yes");
  window.top.debugWindow.opener = self;
  // open the document for writing
  window.top.debugWindow.document.open();
  window.top.debugWindow.document.write(
      "<HTML><HEAD><TITLE>Debug Window</TITLE></HEAD><BODY><PRE>\n");
}
// If the debug window exists, then write to it
function debug(text) {
  if (window.top.debugWindow && ! window.top.debugWindow.closed) {
    window.top.debugWindow.document.write(text+"\n");
  }
}
// If the debug window exists, then close it
function hideDebug() {
  if (window.top.debugWindow && ! window.top.debugWindow.closed) {
    window.top.debugWindow.close();
    window.top.debugWindow = null;
  }
}

function xmlAsString(element){
	if(element.xml){
		return element.xml;
	}
	return (new XMLSerializer()).serializeToString(element);
}

function liveUpdateDOM(target, template) {
	var childrenFound = false;
	for(var child = template.firstChild; child; child = child.nextSibling) {

		if (child.nodeType == 1) {

			childrenFound = true;
			if (!liveUpdateDOM(target[child.tagName], child))
				target[child.tagName] = child.firstChild.data;
		}

	
}

	return childrenFound;
}

function xmlLiveProcessOne(child) {

	/* 
	** This script came with some added functionality that 
	** I don't understand. 
	** It looks like it might be able to update
	** visual properties of the page but I am only
	** intrested (at the moment) in updateing the innerHtml
	** Leaving the functionality in for future reference
	*/
	
	/*
	** If we get an html tag we can expect that it is unexpected
	** and most likely an error. Replace the contents of the
	** entire page
	*/
	if (child.tagName == "dom") {
		var elementId = child.getAttribute("id");
		var element = document.getElementById(elementId);
		liveUpdateDOM(element, child);
	}
	else if(child.tagName == "script") {
		if(child.textContent){
			eval(child.textContent);
		}
		else if(child.text){
			eval(child.text);
		}
		else if(child.innerText){
			eval(child.innerText);
		}else if(child.childNodes[1].data){
			//Safari!
			eval(child.childNodes[1].data);
		}
	}else /*if (child.tagName == "innerHtml")*/ {
		var elementId = child.getAttribute("id");
		
		var element = document.getElementById(elementId);
		if(child.firstChild.data){
			element.innerHTML = child.firstChild.data;
		}else{
			var iHtml = '';
			for(i=0; i < child.childNodes.length; i++) {
				iHtml = iHtml + xmlAsString(child.childNodes[i]);
			}
		
			element.innerHTML = iHtml;
			
		}
	}
	
}



function xmlProcessResults(response) {
	
	for(var i=0; i < response.documentElement.childNodes.length; i++) {
		var child = response.documentElement.childNodes[i];
		xmlLiveProcessOne(child);
	}
}


function xmlLiveUpdaterUri(uri) {
	/*
	** I have to encode all the attribute values on a page
	** in order for the page to be parsable XML so edencode 
	** The &amp; s 
	*/
	uri = uri.replace("&amp;","&");

    return xmlLiveUpdater(
function() { return uri; }, xmlProcessResults);

}



function xmlLiveUpdater(uriFunc, processResultsFunc)
{
	var oldCur = document.documentElement.style.cursor;
	document.documentElement.style.cursor = "wait";
    var request = false;

    if (window.XMLHttpRequest) {
       	request = new XMLHttpRequest();
    }


    update();
    function update()
    {
		
       if(request && request.readyState < 4)
            request.abort();

            
        if(!window.XMLHttpRequest)
            request = new ActiveXObject("Microsoft.XMLHTTP");
        
        request.onreadystatechange = processRequestChange;
	   var uri = addParameter(uriFunc(), "timestamp", (new Date()).getTime().toString());

        request.open("POST", uri.split("?")[0]);
	   request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

	   window.status = "Sending commands...";

        request.send(uri.split("?")[1]);
	
        return false;

    }
    
    function processRequestChange() {
		if(request.readyState == 4) {

		     window.status = "Getting new instructions...";
		
			if(request && request.responseXML && request.responseXML.documentElement) {
				
				processResultsFunc(request.responseXML);

				window.status = "Done";
				
			} else {
				/*
				**If we are here assume we reseived an error
				** and we should replace the entire contents of
				** the page with the responce
				*/
				
				document.documentElement.innerHTML = request.responseText;
				//document.location.reload();

			}
			document.documentElement.style.cursor = oldCur;

		}
		

    }
	
    return update;

}

function containsDOM (container, containee) {
  var isParent = false;
  do {
    if ((isParent = container == containee))
      break;
    containee = containee.parentNode;
  }
  while (containee != null);
  return isParent;
}

function checkMouseEnter (element, evt) {
  if (element.contains && evt.fromElement) {
    return !element.contains(evt.fromElement);
  }
  else if (evt.relatedTarget) {
    return !containsDOM(element, evt.relatedTarget);
  }
}

function checkMouseLeave (element, evt) {
  if (element.contains && evt.toElement) {
    return !element.contains(evt.toElement);
  }
  else if (evt.relatedTarget) {
    return !containsDOM(element, evt.relatedTarget);
  }
}




function windowBounds() {
	var x = window.innerWidth
		|| document.documentElement.clientWidth
		|| document.body.clientWidth
		|| 0;
	var y = window.innerHeight
		|| document.documentElement.clientHeight
		|| document.body.clientHeight
		|| 0;
	return [x, y];
}

function fullscreen(element) {
	element = $(element);
	var bounds = windowBounds();
	element.style.position = "absolute";
	element.style.left = element.style.top = 0;
	element.style.width = bounds[0] + "px";
	element.style.height = bounds[1] + "px";
}
function visualCenter(element) {
	element = $(element);
	var extent = elementDimensions(element);
	var bounds = windowBounds();
	var x = (bounds[0] - extent.w) / 2;
	var y = (bounds[1] - extent.h) / 3.5;
	x = x < 0 ? 0 : x; y = y < 0 ? 0 : y;
	element.style.position = "absolute";
	element.style.left = x + "px";
	element.style.top = y + "px";
	
}
function updateModelBox(){
	fullscreen("model-overlay");
	visualCenter("model-window");
}

function setSelectedIndexOnSelectFromLabel(select,label){

	for(var i = 0; i < select.options.length -1; i++){
		if(select.options[i].text.replace(/^\s+|\s+$/g, '') == label){
			select.selectedIndex = i;
		}
	}

}