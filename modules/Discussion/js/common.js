/**
 * mxBoard, pragmaMx Module
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: common.js 245 2016-10-31 19:28:34Z PragmaMx $
 *
 * Javascript code from:
 * Unclassified NewsBoard
 * Copyright 2003-5 by Yves Goergen
 * Homepage: http://newsboard.unclassified.de
 * See http://newsboard.unclassified.de/about#license for
 * distribution/license details
 *
 * thank you very much for this great work, Yves
 */

// Dummy function for <a href="javascript:..."> links with an onclick handler
//
function nothing()
{
}

// Open a new popup window
//
// in url = (string) URL to open
// in w = (int) Width in pixels
// in h = (int) Height in pixels
//
function UnbPopup(url, name, w, h)
{
	window.open(url, name, "width=" + w + ", height=" + h + ", resizable=yes, scrollbars=yes");
}

// Get an element by its ID
//
// in id = (string) object ID
//
function getel(id)
{
	return document.getElementById(id);
}

// ---------- Global enhanced keyboard controls support ----------

var globalKeyHandlers = new Array();

// Register a key handler function
//
// in keycode = (int) Keycode to listen on
// in ascii = (int) ASCII value to listen on. Either keycode or ASCII code must
//                  be specified
// in flags = (int) Key flags to listen on. Combination of:
//                  1: Alt key
//                  2: Control key
//                  4: Shift key
// in funcname = (string) Key handler function name
// in funcparam = (string) Parameter to pass to the key handler function
//
function UnbGlobalRegisterKeyHandler(keycode, ascii, flags, funcname, funcparam)
{
	var newarr = new Array();
	newarr["keycode"] = keycode;
	newarr["ascii"] = ascii;
	newarr["flags"] = flags;
	newarr["funcname"] = funcname;
	newarr["funcparam"] = funcparam;
	globalKeyHandlers.push(newarr);
}

// Keypress handler
//
function UnbGlobalKeyDispatcher(e)
{
	var myFlags;
	var item;

	for (var i in globalKeyHandlers)
	{
		item = globalKeyHandlers[i];

		myFlags = 0;
		if (e.altKey) myFlags |= 1;
		if (e.ctrlKey) myFlags |= 2;
		if (e.shiftKey) myFlags |= 4;

		if ((item["keycode"] && item["keycode"] == e.keyCode ||
		     item["ascii"] && item["ascii"] == e.which) &&
		    item["flags"] == myFlags)
		{
			return item["funcname"](e, item["funcparam"]);
		}
	}
	//alert("unknown key pressed: keycode = " + e.keyCode + " which = " + e.which);
}

// enable keypress dispatcher (not for IE)
if (navigator.appName != "Microsoft Internet Explorer")
{
	window.captureEvents(Event.KEYPRESS);
	window.onkeypress = UnbGlobalKeyDispatcher;
}

// eBoard Spezial:
function IM(username,w,h) {
	UnbPopup("modules.php?name=Private_Messages&file=buddy&op=compose&to=" + username, "u2u", w, h);
}

