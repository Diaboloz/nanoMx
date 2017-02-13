/**
 * pragmaMx - Web Content Management System.
 * Copyright by pragmaMx Developer Team - http://www.pragmamx.org
 * $Id: unb_lib.js 6 2015-07-08 07:07:06Z PragmaMx $
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


// changes for pragmaMx
// openerdoc muss definiert werden, wenn UnbInsertText() 
// von einem Poupfenster aus aufgerufen wird.
// Es muss das opener.document als Object enthalten
var openerdoc = null;
// end changes for pragmaMx

// Event handler for keypress events on a textbox
//
// in where = (object) Textbox to handle the event for
//
// returns (bool) false: event was handled
//         null: event was not handled and should be passed on
//
function UnbTextKeydownHandler(where)
{
alert(window);

	if (!window.event) return true;
	var keycode = window.event.keyCode;


	if (keycode == 9)
	{
		if (document.selection.createRange().duplicate().text.length)
		{
			if (!window.event.shiftKey)
			{
				document.selection.createRange().duplicate().text =
					"\t" +
					document.selection.createRange().duplicate().text.replace(/\n/g, "\n\t") +
					"\n";
			}
			else
			{
				document.selection.createRange().duplicate().text =
					document.selection.createRange().duplicate().text.replace(/\n\t/g, "\n");
				document.selection.createRange().duplicate().text =
					document.selection.createRange().duplicate().text.replace(/^\t/g, "") +
					"\n";
			}
		}
		else
		{
			if (!window.event.shiftKey)
			{
				UnbInsertText("\t");
			}
		}
		return false;
	}
}

// Handle a text editor button
//
// in event = (object) Original event object
// in cmd = (string) Command name
// in arg1 = Used for additional data like colour, font or text size
//
function UnbEditorDoCmd(event, cmd, arg1)
{
	textbox.focus();   // TODO: make "textbox" more flexible, also in other functions
	switch (cmd)
	{
		case "bold":
			UnbEncloseText("[b]", "[/b]", 1); break;
		case "italic":
			UnbEncloseText("[i]", "[/i]", 1); break;
		case "underline":
			UnbEncloseText("[u]", "[/u]", 1); break;
		case "center":
			UnbEncloseText("[center]", "[/center]", 1); break;
		case "line":
			UnbInsertText("[line]"); break;
		//case "strike":
		//	UnbEncloseText("[s]", "[/s]", 1); break;
		//case "mono":
		//	UnbEncloseText("[m]", "[/m]", 1); break;
		case "list":
			UnbEncloseText("[list][*]", " [/list]", 1); break;
		case "list_1":
			UnbEncloseText("[list=1][*]", " [/list=1]", 1); break;
		case "list_a":
			UnbEncloseText("[list=a][*]", " [/list=a]", 1); break;
		case "list_A":
			UnbEncloseText("[list=A][*]", " [/list=A]", 1); break;
		case "email":
			UnbEncloseText("[email]", "[/email]", 1); break;
		case "quote":
			if (event.shiftKey)
			{
				UnbInsertText("[/quote]\n\n\n\n[quote]\n");
				// If on Mozilla, move cursor to correct position
				if (textbox.selectionStart >= 0) textbox.selectionStart = textbox.selectionEnd -= 10;
			}
			else
			{
				UnbEncloseText("[quote]\n", "\n[/quote]", 1);
			}
			break;
		case "code":
			if (event.shiftKey)
			{
				UnbInsertText("[/code]\n\n\n\n[code]\n");
				// If on Mozilla, move cursor to correct position
				if (textbox.selectionStart >= 0) textbox.selectionStart = textbox.selectionEnd -= 10;
			}
			else
			{
				UnbEncloseText("[code]\n", "\n[/code]", 1);
			}
			break;
		case "php":
			if (event.shiftKey)
			{
				UnbInsertText("[/php]\n\n\n\n[php]\n");
				// If on Mozilla, move cursor to correct position
				if (textbox.selectionStart >= 0) textbox.selectionStart = textbox.selectionEnd -= 10;
			}
			else
			{
				UnbEncloseText("[php]\n", "\n[/php]", 1);
			}
			break;
		case "url":
			if (event.shiftKey)
			{
				UnbEncloseText("[url=]", "[/url]", 0);
				// If on Mozilla, move cursor to correct position
				if (textbox.selectionStart >= 0) textbox.selectionStart = textbox.selectionEnd = textbox.selectionStart + 5;
			}
			else
			{
				UnbEncloseText("[url]", "[/url]", 1);
			}
			break;
		case "img":
			UnbEncloseText("[img]", "[/img]", 1); break;
		case "color":
			UnbEncloseText("[color=" + arg1 + "]", "[/color]", 1); break;
		case "font":
			UnbEncloseText("[font=" + arg1 + "]", "[/font]", 1); break;
		case "size":
			UnbEncloseText("[size=" + arg1 + "]", "[/size]", 1); break;

		// "not currently supported":
		//case "undo":
		//	document.selection.createRange().execCommand("Undo");
		//case "redo":
		//	document.selection.createRange().execCommand("Redo");
	}
}

// Enclose the currently selected text with an opening and closing text
//
// If there is no selection, the text is inserted or appended.
// If the current selection already includes the text, it is removed instead.
//
// in t_open = (string) Opening text
// in t_close = (string) Closing text
// in cursorpos = (int) 0: begin | 1: middle | 2: end
//                      (only relevant if no text is selected)
//
function UnbEncloseText(t_open, t_close, cursorpos)
{
	// TODO: get from clientinfo lib
	var is_ie = (navigator.appName == "Microsoft Internet Explorer");
	if (is_ie && document.selection && document.selection.createRange().duplicate().text.length)
	{
		// IE with selected text
		var seltext = document.selection.createRange().duplicate().text;
		if (seltext.substring(0, t_open.length) == t_open &&
			seltext.substring(seltext.length - t_close.length, seltext.length) == t_close)
		{
			// tags are already there, remove them
			document.selection.createRange().duplicate().text = seltext.substring(t_open.length, seltext.length - t_close.length);
		}
		else
		{
			document.selection.createRange().duplicate().text = t_open + seltext + t_close;
		}
	}
	else if (textbox.selectionEnd && (textbox.selectionEnd - textbox.selectionStart > 0))
	{
		// Mozilla with selected text
		var start_selection = textbox.selectionStart;
		var end_selection = textbox.selectionEnd;
		var new_endsel;
		var scroll_top = textbox.scrollTop;
		var scroll_left = textbox.scrollLeft;

		// fetch everything from start of text area to selection start
		var start = textbox.value.substring(0, start_selection);
		// fetch everything from start of selection to end of selection
		var seltext = textbox.value.substring(start_selection, end_selection);
		// fetch everything from end of selection to end of text area
		var end = textbox.value.substring(end_selection, textbox.textLength);

		if (seltext.substring(0, t_open.length) == t_open &&
			seltext.substring(seltext.length - t_close.length, seltext.length) == t_close)
		{
			// tags are already there, remove them
			seltext = seltext.substring(t_open.length, seltext.length - t_close.length);
			new_endsel = end_selection - t_open.length - t_close.length;
		}
		else
		{
			seltext = t_open + seltext + t_close;
			new_endsel = end_selection + t_open.length + t_close.length;
		}

		textbox.value = start + seltext + end;

		textbox.selectionStart = start_selection;
		textbox.selectionEnd = new_endsel;
		textbox.scrollTop = scroll_top;
		textbox.scrollLeft = scroll_left;
	}
	else
	{
		// no selection, insert opening/closing tags alone
		UnbInsertText(t_open + t_close);
		if (cursorpos <= 1) textbox.selectionEnd -= t_close.length;
		if (cursorpos <= 0) textbox.selectionEnd -= t_open.length;
	}
}

// Insert text into a textbox at the current cursor position
//
// in what = (string) Text to insert
// in replace = (int) replace characters before cursor?
//
function UnbInsertText(what, replace)
{
	if (replace == null) replace = 0;
	
// changes for pragmaMx
	if (openerdoc) {
		curdoc = openerdoc;
	} else {
		curdoc = document;
	}
// end changes for pragmaMx

	if (textbox.createTextRange)
	{
		textbox.focus();
		curdoc.selection.createRange().duplicate().text = what;
		textbox.focus();
	}
	else if (textbox.selectionStart >= 0)
	{
		// Mozilla without selected text
		var start_selection = textbox.selectionStart;
		var end_selection = textbox.selectionEnd;
		var scroll_top = textbox.scrollTop;
		var scroll_left = textbox.scrollLeft;

		// fetch everything from start of text area to selection start
		var start = textbox.value.substring(0, start_selection - replace);
		// fetch everything from end of selection to end of text area
		var end = textbox.value.substring(end_selection, textbox.textLength);

		textbox.value = start + what + end;

		textbox.selectionStart = textbox.selectionEnd = start_selection - replace + what.length;
		textbox.focus();
		textbox.scrollTop = scroll_top;
		textbox.scrollLeft = scroll_left;
	}
	else
	{
		textbox.value += what;
		textbox.focus();
	}
// changes for pragmaMx
	if (openerdoc) {
		window.focus();
	}
// end changes for pragmaMx
}
