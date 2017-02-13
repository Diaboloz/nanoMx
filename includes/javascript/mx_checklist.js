// <?php !! This fools phpdocumentor into parsing this file
/**
* pragmaMx CVS $Id: mx_checklist.js 6 2015-07-08 07:07:06Z PragmaMx $
*/

/** needed var adminForm;*/

// general utility for browsing a named array or object
function xshow(o) {
	s = '';
	for(e in o) {s += e+'='+o[e]+'\n';}
	alert( s );
}
/**
* Toggles the check state of a group of boxes
*
* Checkboxes must have an id attribute in the form cb0, cb1...
* @param The number of box to 'check'
* @param An alternative field name
*/
function checkAll( n, fldName ) {
  if (!fldName) {
     fldName = 'cb';
  }
	var f = document[adminForm];
	var c = f.toggle.checked;
	var n2 = 0;
	for (i=0; i < n; i++) {
		cb = eval( 'f.' + fldName + '' + i );
		if (cb) {
			cb.checked = c;
			n2++;
		}
	}
	if (c) {
		document[adminForm].boxchecked.value = n2;
	} else {
		document[adminForm].boxchecked.value = 0;
	}
}

function listItemTask( id, task ) {
    var f = document[adminForm];
    cb = eval( 'f.' + id );
    if (cb) {
        for (i = 0; true; i++) {
            cbx = eval('f.cb'+i);
            if (!cbx) break;
            cbx.checked = false;
        } // for
        cb.checked = true;
        f.boxchecked.value = 1;
        submitbutton(task);
    }
    return false;
}

function hideMainMenu()
{
	document[adminForm].hidemainmenu.value=1;
}

function isChecked(isitchecked){
	if (isitchecked == true){
		document[adminForm].boxchecked.value++;
	}
	else {
		document[adminForm].boxchecked.value--;
	}
}

/**
* Default function.  Usually would be overriden by the component
*/
function submitbutton(pressbutton) {
    submitform(pressbutton);
}

/**
* Submit the admin form
*/
function submitform(pressbutton){
	document[adminForm].task.value=pressbutton;
//	try {
//		document[adminForm].onsubmit();
//		}
//	catch(e){}
//	alert('submitform');
	document[adminForm].submit();
}

function onsubmitform() {
		document[adminForm].hidemainmenu.value++;
}

function validateForm() {
	if (document[adminForm].hidemainmenu.value==0){
	    i= false;
//		alert ('validateform false');
	} else {
		i= true;
//	    alert('validateForm true');
	}
	return i;	
}
/**
* Submit the control panel admin form
*/
function submitcpform(sectionid, id){
	document[adminForm].sectionid.value=sectionid;
	document[adminForm].id.value=id;
	submitbutton("edit");
}

/**
* Getting radio button that is selected.
*/
function getSelected(allbuttons){
	for (i=0;i<allbuttons.length;i++) {
		if (allbuttons[i].checked) {
			return allbuttons[i].value
		}
	}
}


function getElementByName( f, name ) {
	if (f.elements) {
		for (i=0, n=f.elements.length; i < n; i++) {
			if (f.elements[i].name == name) {
				return f.elements[i];
			}
		}
	}
	return null;
}