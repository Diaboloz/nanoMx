/* $Id: textarea.js 6 2015-07-08 07:07:06Z PragmaMx $ */

document.getElementsByClassName = function (needle)
{
  var         my_array = document.getElementsByTagName("*");
  var         retvalue = new Array();
  var        i;
  var        j;

  for (i = 0, j = 0; i < my_array.length; i++)
  {
    var c = " " + my_array[i].className + " ";
    if (c.indexOf(" " + needle + " ") != -1)
      retvalue[j++] = my_array[i];
  }
  return retvalue;
}

function addEvent(obj, evType, fn)
{
    if (obj.addEventListener)
    {
        obj.addEventListener(evType, fn, true);
        return true;
    }
    else if (obj.attachEvent)
    {
        var r = obj.attachEvent("on"+evType, fn);
        return r;
    }
    else
    {
        return false;
    }
}

addEvent(window, 'load', function()
{
    var resizables = document.getElementsByClassName('wysiwygarea');
    var i, ta, wrapper, bigger, smaller, biggerFont, smallerFont, biggerSpan, smallerSpan, biggerFontSpan, smallerFontSpan;
    for (i=0; i<resizables.length; i++)
    {
        ta = resizables[i];

        wrapper = document.createElement('DIV');
        wrapper.className = 'resizerWrapper';
        ta.parentNode.replaceChild(wrapper, ta);
        wrapper.appendChild(ta);

        bigger = document.createElement('A');
        bigger.className = 'resizeBigger';
        bigger.href = '#';

        smaller = document.createElement('A');
        smaller.className = 'resizeSmaller';
        smaller.href='#';

        biggerFont = document.createElement('A');
        biggerFont.className = 'resizeBiggerFont';
        biggerFont.href = '#';

        smallerFont = document.createElement('A');
        smallerFont.className = 'resizeSmallerFont';
        smallerFont.href='#';

        smallerSpan = document.createElement('SPAN');
        smallerSpan.appendChild(document.createTextNode('smaller'));

        biggerSpan = document.createElement('SPAN');
        biggerSpan.appendChild(document.createTextNode('bigger'));

        smallerFontSpan = document.createElement('SPAN');
        smallerFontSpan.appendChild(document.createTextNode('smaller font'));

        biggerFontSpan = document.createElement('SPAN');
        biggerFontSpan.appendChild(document.createTextNode('bigger font'));

        bigger.appendChild(biggerSpan);
        smaller.appendChild(smallerSpan);
        biggerFont.appendChild(biggerFontSpan);
        smallerFont.appendChild(smallerFontSpan);

        bigger.resizer = ta;
        smaller.resizer = ta;
        biggerFont.resizer = ta;
        smallerFont.resizer = ta;

        bigger.onclick = function()
        {
            if (this.resizer.style.height == '')
                this.resizer.style.height = this.resizer.clientHeight+'px';
            this.resizer.style.height = (parseInt(this.resizer.style.height) + 30) + "px";
            return false;
        }

        smaller.onclick = function()
        {
            if (this.resizer.style.height == '')
                this.resizer.style.height = this.resizer.clientHeight+'px';

            if ((parseInt(this.resizer.style.height) - 30) > 30)
                this.resizer.style.height = (parseInt(this.resizer.style.height) - 30) + "px";
            return false;
        }

        biggerFont.onclick = function()
        {
            if (this.resizer.style.fontSize == '')
                this.resizer.style.fontSize = '1em';

            this.resizer.style.fontSize = (parseFloat(this.resizer.style.fontSize) + 0.1) + "em";

            return false;
        }

        smallerFont.onclick = function()
        {
            if (this.resizer.style.fontSize == '')
                this.resizer.style.fontSize = '1em';

            if ((parseFloat(this.resizer.style.fontSize) - 0.1) > 0.5)
                this.resizer.style.fontSize = (parseFloat(this.resizer.style.fontSize) - 0.1) + "em";

            return false;
        }

        wrapper.appendChild(bigger);
        wrapper.appendChild(smaller);
        wrapper.appendChild(biggerFont);
        wrapper.appendChild(smallerFont);
    }
});


addEvent(window, 'load', function()
{
    var counts = document.getElementsByClassName('maxlength');
    var i, count, matches, countHolder;

    for (i=0; i<counts.length; i++)
    {
        count = counts[i];
        matches = count.className.match(/max_([0-9]+)/);
        count.maxVal = RegExp.$1;
        count.holder = document.getElementById(count.id + 'Count');
        if (count.holder)
        {
            count.holder.innerHTML = count.maxVal - count.value.length;
            count.onkeyup = function()
            {
                if (this.value.length > this.maxVal)
                    this.value = this.value.substring(0, this.maxVal);

                this.holder.innerHTML = this.maxVal - this.value.length;
            }
        }
    }
});

