/**
 * Created by Mydde on 12/03/15.
 */
 ucfirst = function(str) {
// discuss at: http://phpjs.org/functions/ucfirst/
    str += '';
    var f = str.charAt(0)
        .toUpperCase();
    return f + str.substr(1);
}
nl2br = function (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br>' : '<br>';
    return str.replace(/\n/g, '<br \/>');

}
empty = function (mixed_var) {
    //  discuss at: http://phpjs.org/functions/empty/
    // original by: Philippe Baumann
    //    input by: Onno Marsman
    //    input by: LH
    //    input by: Stoyan Kyosev (http://www.svest.org/)
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Onno Marsman
    // improved by: Francesco
    // improved by: Marc Jansen
    // improved by: Rafal Kukawski
    //   example 1: empty(null);
    //   returns 1: true
    //   example 2: empty(undefined);
    //   returns 2: true
    //   example 3: empty([]);
    //   returns 3: true
    //   example 4: empty({});
    //   returns 4: true
    //   example 5: empty({'aFunc' : function () { alert('humpty'); } });
    //   returns 5: false

    var undef, key, i, len;
    var emptyValues = [undef, null, false, 0, '', '0'];

    for (i = 0, len = emptyValues.length; i < len; i++) {
        if (mixed_var === emptyValues[i]) {
            return true;
        }
    }

    if (typeof mixed_var === 'object') {
        for (key in mixed_var) {
            // TODO: should we check for own properties only?
            //if (mixed_var.hasOwnProperty(key)) {
            return false;
            //}
        }
        return true;
    }

    return false;
}
function str_replace(search, replace, subject, count) {
    //  discuss at: http://phpjs.org/functions/str_replace/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Gabriel Paderni
    // improved by: Philip Peterson
    // improved by: Simon Willison (http://simonwillison.net)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Onno Marsman
    // improved by: Brett Zamir (http://brett-zamir.me)
    //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // bugfixed by: Anton Ongson
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Oleg Eremeev
    //    input by: Onno Marsman
    //    input by: Brett Zamir (http://brett-zamir.me)
    //    input by: Oleg Eremeev
    //        agent_note: The count parameter must be passed as a string in order
    //        agent_note: to find a global variable in which the result will be given
    //   example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    //   returns 1: 'Kevin.van.Zonneveld'
    //   example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
    //   returns 2: 'hemmo, mars'
    // bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca)
    //   example 3: str_replace(Array('S','F'),'x','ASDFASDF');
    //   returns 3: 'AxDxAxDx'
    // bugfixed by: Glen Arason (http://CanadianDomainRegistry.ca) Corrected count
    //   example 4: str_replace(['A','D'], ['x','y'] , 'ASDFASDF' , 'cnt');
    //   returns 4: 'xSyFxSyF' // cnt = 0 (incorrect before fix)
    //   returns 4: 'xSyFxSyF' // cnt = 4 (correct after fix)

    var i = 0,
        j = 0,
        temp = '',
        repl = '',
        sl = 0,
        fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = Object.prototype.toString.call(r) === '[object Array]',
        sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);

    if(typeof(search) === 'object' && typeof(replace) === 'string' ) {
        temp = replace;
        replace = new Array();
        for (i=0; i < search.length; i+=1) {
            replace[i] = temp;
        }
        temp = '';
        r = [].concat(replace);
        ra = Object.prototype.toString.call(r) === '[object Array]';
    }

    if (count) {
        this.window[count] = 0;
    }

    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }
        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp)
                .split(f[j])
                .join(repl);
            if (count) {
                this.window[count] += ((temp.split(f[j])).length - 1);
            }
        }
    }
    return sa ? s : s[0];
}
var uniqid = function (prefix, more_entropy) {
	//  discuss at: http://phpjs.org/functions/uniqid/
	// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//  revised by: Kankrelune (http://www.webfaktory.info/)
	//        agent_note: Uses an internal counter (in php_js global) to avoid collision
	//        test: skip
	//   example 1: uniqid();
	//   returns 1: 'a30285b160c14'
	//   example 2: uniqid('foo');
	//   returns 2: 'fooa30285b1cd361'
	//   example 3: uniqid('bar', true);
	//   returns 3: 'bara20285b23dfd1.31879087'

	if ( typeof prefix === 'undefined' ) {
		prefix = '';
	}

	var retId;
	var formatSeed = function (seed, reqWidth) {
		seed = parseInt (seed, 10)
			.toString (16); // to hex str
		if ( reqWidth < seed.length ) {
			// so long we split
			return seed.slice (seed.length - reqWidth);
		}
		if ( reqWidth > seed.length ) {
			// so short we pad
			return Array (1 + (reqWidth - seed.length))
				       .join ('0') + seed;
		}
		return seed;
	};

	// BEGIN REDUNDANT
	if ( !this.php_js ) {
		this.php_js = {};
	}
	// END REDUNDANT
	if ( !this.php_js.uniqidSeed ) {
		// init seed with big random int
		this.php_js.uniqidSeed = Math.floor (Math.random () * 0x75bcd15);
	}
	this.php_js.uniqidSeed++;

	// start with prefix, add current milliseconds hex string
	retId = prefix;
	retId += formatSeed (parseInt (new Date ()
		                               .getTime () / 1000, 10), 8);
	// add seed hex string
	retId += formatSeed (this.php_js.uniqidSeed, 5);
	if ( more_entropy ) {
		// for more entropy we add a float lower to 10
		retId += (Math.random () * 10)
			.toFixed (8)
			.toString ();
	}

	return retId;
}
function strip_tags(input, allowed) {

    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '')
        .replace(tags, function ($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
        });
}
function trim(str, charlist) {
    //  discuss at: http://phpjs.org/functions/trim/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: mdsjack (http://www.mdsjack.bo.it)
    // improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Steven Levithan (http://blog.stevenlevithan.com)
    // improved by: Jack
    //    input by: Erkekjetter
    //    input by: DxGx
    // bugfixed by: Onno Marsman
    //   example 1: trim('    Kevin van Zonneveld    ');
    //   returns 1: 'Kevin van Zonneveld'
    //   example 2: trim('Hello World', 'Hdle');
    //   returns 2: 'o Wor'
    //   example 3: trim(16, 1);
    //   returns 3: 6

    var whitespace, l = 0,
        i = 0;
    str += '';

    if (!charlist) {
        // default list
        whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
    } else {
        // preg_quote custom list
        charlist += '';
        whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
    }

    l = str.length;
    for (i = 0; i < l; i++) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(i);
            break;
        }
    }

    l = str.length;
    for (i = l - 1; i >= 0; i--) {
        if (whitespace.indexOf(str.charAt(i)) === -1) {
            str = str.substring(0, i + 1);
            break;
        }
    }

    return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}
function urldecode(str) {
    //       discuss at: http://phpjs.org/functions/urldecode/
    //      original by: Philip Peterson
    //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //      improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //      improved by: Brett Zamir (http://brett-zamir.me)
    //      improved by: Lars Fischer
    //      improved by: Orlando
    //      improved by: Brett Zamir (http://brett-zamir.me)
    //      improved by: Brett Zamir (http://brett-zamir.me)
    //         input by: AJ
    //         input by: travc
    //         input by: Brett Zamir (http://brett-zamir.me)
    //         input by: Ratheous
    //         input by: e-mike
    //         input by: lovio
    //      bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //      bugfixed by: Rob
    // reimplemented by: Brett Zamir (http://brett-zamir.me)
    //             agent_note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    //             agent_note: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
    //             agent_note: pages served as UTF-8
    //        example 1: urldecode('Kevin+van+Zonneveld%21');
    //        returns 1: 'Kevin van Zonneveld!'
    //        example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    //        returns 2: 'http://kevin.vanzonneveld.net/'
    //        example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    //        returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    //        example 4: urldecode('%E5%A5%BD%3_4');
    //        returns 4: '\u597d%3_4'

    return decodeURIComponent((str + '')
        .replace(/%(?![\da-f]{2})/gi, function () {
            // PHP tolerates poorly formed escape sequences
            return '%25';
        })
        .replace(/\+/g, '%20'));
}
function array_merge() {
    //  discuss at: http://phpjs.org/functions/array_merge/
    // original by: Brett Zamir (http://brett-zamir.me)
    // bugfixed by: Nate
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //    input by: josh
    //   example 1: arr1 = {"color": "red", 0: 2, 1: 4}
    //   example 1: arr2 = {0: "a", 1: "b", "color": "green", "shape": "trapezoid", 2: 4}
    //   example 1: array_merge(arr1, arr2)
    //   returns 1: {"color": "green", 0: 2, 1: 4, 2: "a", 3: "b", "shape": "trapezoid", 4: 4}
    //   example 2: arr1 = []
    //   example 2: arr2 = {1: "data"}
    //   example 2: array_merge(arr1, arr2)
    //   returns 2: {0: "data"}

    var args = Array.prototype.slice.call(arguments),
        argl = args.length,
        arg,
        retObj = {},
        k = '',
        argil = 0,
        j = 0,
        i = 0,
        ct = 0,
        toStr = Object.prototype.toString,
        retArr = true;

    for (i = 0; i < argl; i++) {
        if (toStr.call(args[i]) !== '[object Array]') {
            retArr = false;
            break;
        }
    }

    if (retArr) {
        retArr = [];
        for (i = 0; i < argl; i++) {
            retArr = retArr.concat(args[i]);
        }
        return retArr;
    }

    for (i = 0, ct = 0; i < argl; i++) {
        arg = args[i];
        if (toStr.call(arg) === '[object Array]') {
            for (j = 0, argil = arg.length; j < argil; j++) {
                retObj[ct++] = arg[j];
            }
        } else {
            for (k in arg) {
                if (arg.hasOwnProperty(k)) {
                    if (parseInt(k, 10) + '' === k) {
                        retObj[ct++] = arg[k];
                    } else {
                        retObj[k] = arg[k];
                    }
                }
            }
        }
    }
    return retObj;
}
function array_unique(inputArr) {
    //  discuss at: http://phpjs.org/functions/array_unique/
    // original by: Carlos R. L. Rodrigues (http://www.jsfromhell.com)
    //    input by: duncan
    //    input by: Brett Zamir (http://brett-zamir.me)
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Nate
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    // improved by: Michael Grier
    //        agent_note: The second argument, sort_flags is not implemented;
    //        agent_note: also should be sorted (asort?) first according to docs
    //   example 1: array_unique(['Kevin','Kevin','van','Zonneveld','Kevin']);
    //   returns 1: {0: 'Kevin', 2: 'van', 3: 'Zonneveld'}
    //   example 2: array_unique({'a': 'green', 0: 'red', 'b': 'green', 1: 'blue', 2: 'red'});
    //   returns 2: {a: 'green', 0: 'red', 1: 'blue'}

    var key = '',
        tmp_arr2 = {},
        val = '';

    var __array_search = function (needle, haystack) {
        var fkey = '';
        for (fkey in haystack) {
            if (haystack.hasOwnProperty(fkey)) {
                if ((haystack[fkey] + '') === (needle + '')) {
                    return fkey;
                }
            }
        }
        return false;
    };

    for (key in inputArr) {
        if (inputArr.hasOwnProperty(key)) {
            val = inputArr[key];
            if (false === __array_search(val, tmp_arr2)) {
                tmp_arr2[key] = val;
            }
        }
    }

    return tmp_arr2;
}
function array_keys(input, search_value, argStrict) {
    //  discuss at: http://phpjs.org/functions/array_keys/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //    input by: Brett Zamir (http://brett-zamir.me)
    //    input by: P
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    // improved by: jd
    // improved by: Brett Zamir (http://brett-zamir.me)
    //   example 1: array_keys( {firstname: 'Kevin', surname: 'van Zonneveld'} );
    //   returns 1: {0: 'firstname', 1: 'surname'}

    var search = typeof search_value !== 'undefined',
        tmp_arr = [],
        strict = !! argStrict,
        include = true,
        key = '';

    if (input && typeof input === 'object' && input.change_key_case) {
        // Duck-type check for our own array()-created PHPJS_Array
        return input.keys(search_value, argStrict);
    }

    for (key in input) {
        if (input.hasOwnProperty(key)) {
            include = true;
            if (search) {
                if (strict && input[key] !== search_value) {
                    include = false;
                } else if (input[key] != search_value) {
                    include = false;
                }
            }

            if (include) {
                tmp_arr[tmp_arr.length] = key;
            }
        }
    }

    return tmp_arr;
}
function parse_str(str, array) {
    //       discuss at: http://phpjs.org/functions/parse_str/
    //      original by: Cagri Ekin
    //      improved by: Michael White (http://getsprink.com)
    //      improved by: Jack
    //      improved by: Brett Zamir (http://brett-zamir.me)
    //      bugfixed by: Onno Marsman
    //      bugfixed by: Brett Zamir (http://brett-zamir.me)
    //      bugfixed by: stag019
    //      bugfixed by: Brett Zamir (http://brett-zamir.me)
    //      bugfixed by: MIO_KODUKI (http://mio-koduki.blogspot.com/)
    // reimplemented by: stag019
    //         input by: Dreamer
    //         input by: Zaide (http://zaidesthings.com/)
    //         input by: David Pesta (http://davidpesta.com/)
    //         input by: jeicquest
    //             agent_note: When no argument is specified, will put variables in global scope.
    //             agent_note: When a particular argument has been passed, and the returned value is different parse_str of PHP. For example, a=b=c&d====c
    //             test: skip
    //        example 1: var arr = {};
    //        example 1: parse_str('first=foo&second=bar', arr);
    //        example 1: $result = arr
    //        returns 1: { first: 'foo', second: 'bar' }
    //        example 2: var arr = {};
    //        example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
    //        example 2: $result = arr
    //        returns 2: { str_a: "Jack and Jill didn't see the well." }
    //        example 3: var abc = {3:'a'};
    //        example 3: parse_str('abc[a][b]["c"]=def&abc[q]=t+5');
    //        returns 3: {"3":"a","a":{"b":{"c":"def"}},"q":"t 5"}

    var strArr = String(str)
            .replace(/^&/, '')
            .replace(/&$/, '')
            .split('&'),
        sal = strArr.length,
        i, j, ct, p, lastObj, obj, lastIter, undef, chr, tmp, key, value,
        postLeftBracketPos, keys, keysLen,
        fixStr = function (str) {
            return decodeURIComponent(str.replace(/\+/g, '%20'));
        };

    if (!array) {
        array = this.window;
    }

    for (i = 0; i < sal; i++) {
        tmp = strArr[i].split('=');
        key = fixStr(tmp[0]);
        value = (tmp.length < 2) ? '' : fixStr(tmp[1]);

        while (key.charAt(0) === ' ') {
            key = key.slice(1);
        }
        if (key.indexOf('\x00') > -1) {
            key = key.slice(0, key.indexOf('\x00'));
        }
        if (key && key.charAt(0) !== '[') {
            keys = [];
            postLeftBracketPos = 0;
            for (j = 0; j < key.length; j++) {
                if (key.charAt(j) === '[' && !postLeftBracketPos) {
                    postLeftBracketPos = j + 1;
                } else if (key.charAt(j) === ']') {
                    if (postLeftBracketPos) {
                        if (!keys.length) {
                            keys.push(key.slice(0, postLeftBracketPos - 1));
                        }
                        keys.push(key.substr(postLeftBracketPos, j - postLeftBracketPos));
                        postLeftBracketPos = 0;
                        if (key.charAt(j + 1) !== '[') {
                            break;
                        }
                    }
                }
            }
            if (!keys.length) {
                keys = [key];
            }
            for (j = 0; j < keys[0].length; j++) {
                chr = keys[0].charAt(j);
                if (chr === ' ' || chr === '.' || chr === '[') {
                    keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
                }
                if (chr === '[') {
                    break;
                }
            }

            obj = array;
            for (j = 0, keysLen = keys.length; j < keysLen; j++) {
                key = keys[j].replace(/^['"]/, '')
                    .replace(/['"]$/, '');
                lastIter = j !== keys.length - 1;
                lastObj = obj;
                if ((key !== '' && key !== ' ') || j === 0) {
                    if (obj[key] === undef) {
                        obj[key] = {};
                    }
                    obj = obj[key];
                } else {
                    // To insert new dimension
                    ct = -1;
                    for (p in obj) {
                        if (obj.hasOwnProperty(p)) {
                            if (+p > ct && p.match(/^\d+$/g)) {
                                ct = +p;
                            }
                        }
                    }
                    key = ct + 1;
                }
            }
            lastObj[key] = value;
        }
    }
}
function implode(glue, pieces) {
	//  discuss at: http://phpjs.org/functions/implode/
	// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// improved by: Waldo Malqui Silva (http://waldo.malqui.info)
	// improved by: Itsacon (http://www.itsacon.net/)
	// bugfixed by: Brett Zamir (http://brett-zamir.me)
	//   example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
	//   returns 1: 'Kevin van Zonneveld'
	//   example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
	//   returns 2: 'Kevin van Zonneveld'

	var i = '',
		retVal = '',
		tGlue = '';
	if (arguments.length === 1) {
		pieces = glue;
		glue = '';
	}
	if (typeof pieces === 'object') {
		if (Object.prototype.toString.call(pieces) === '[object Array]') {
			return pieces.join(glue);
		}
		for (i in pieces) {
			retVal += tGlue + pieces[i];
			tGlue = glue;
		}
		return retVal;
	}
	return pieces;
}
function explode(delimiter, string, limit) {
	//  discuss at: http://phpjs.org/functions/explode/
	// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	//   example 1: explode(' ', 'Kevin van Zonneveld');
	//   returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}

	if (arguments.length < 2 || typeof delimiter === 'undefined' || typeof string === 'undefined') return null;
	if (delimiter === '' || delimiter === false || delimiter === null) return false;
	if (typeof delimiter === 'function' || typeof delimiter === 'object' || typeof string === 'function' || typeof string ===
		'object') {
		return {
			0: ''
		};
	}
	if (delimiter === true) delimiter = '1';

	// Here we go...
	delimiter += '';
	string += '';

	var s = string.split(delimiter);

	if (typeof limit === 'undefined') return s;

	// Support for limit
	if (limit === 0) limit = 1;

	// Positive limit
	if (limit > 0) {
		if (limit >= s.length) return s;
		return s.slice(0, limit - 1)
			.concat([s.slice(limit - 1)
				.join(delimiter)
			]);
	}

	// Negative limit
	if (-limit >= s.length) return [];

	s.splice(s.length + limit);
	return s;
}

md5 = function  (str) {
	//  discuss at: http://locutus.io/php/md5/
	// original by: Webtoolkit.info (http://www.webtoolkit.info/)
	// improved by: Michael White (http://getsprink.com)
	// improved by: Jack
	// improved by: Kevin van Zonneveld (http://kvz.io)
	//    input by: Brett Zamir (http://brett-zamir.me)
	// bugfixed by: Kevin van Zonneveld (http://kvz.io)
	//      note 1: Keep in mind that in accordance with PHP, the whole string is buffered and then
	//      note 1: hashed. If available, we'd recommend using Node's native crypto modules directly
	//      note 1: in a steaming fashion for faster and more efficient hashing
	//   example 1: md5('Kevin van Zonneveld')
	//   returns 1: '6e658d4bfcb59cc13f96c14450ac40b9'

	var hash;
	try {
		var crypto = require('crypto')
		var md5sum = crypto.createHash('md5')
		md5sum.update(str)
		hash = md5sum.digest('hex')
	} catch (e) {
		hash = undefined
	}

	if (hash !== undefined) {
		return hash
	}

	var utf8Encode = require('../xml/utf8_encode')
	var xl

	var _rotateLeft = function (lValue, iShiftBits) {
		return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits))
	}

	var _addUnsigned = function (lX, lY) {
		var lX4, lY4, lX8, lY8, lResult
		lX8 = (lX & 0x80000000)
		lY8 = (lY & 0x80000000)
		lX4 = (lX & 0x40000000)
		lY4 = (lY & 0x40000000)
		lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF)
		if (lX4 & lY4) {
			return (lResult ^ 0x80000000 ^ lX8 ^ lY8)
		}
		if (lX4 | lY4) {
			if (lResult & 0x40000000) {
				return (lResult ^ 0xC0000000 ^ lX8 ^ lY8)
			} else {
				return (lResult ^ 0x40000000 ^ lX8 ^ lY8)
			}
		} else {
			return (lResult ^ lX8 ^ lY8)
		}
	}

	var _F = function (x, y, z) {
		return (x & y) | ((~x) & z)
	}
	var _G = function (x, y, z) {
		return (x & z) | (y & (~z))
	}
	var _H = function (x, y, z) {
		return (x ^ y ^ z)
	}
	var _I = function (x, y, z) {
		return (y ^ (x | (~z)))
	}

	var _FF = function (a, b, c, d, x, s, ac) {
		a = _addUnsigned(a, _addUnsigned(_addUnsigned(_F(b, c, d), x), ac))
		return _addUnsigned(_rotateLeft(a, s), b)
	}

	var _GG = function (a, b, c, d, x, s, ac) {
		a = _addUnsigned(a, _addUnsigned(_addUnsigned(_G(b, c, d), x), ac))
		return _addUnsigned(_rotateLeft(a, s), b)
	}

	var _HH = function (a, b, c, d, x, s, ac) {
		a = _addUnsigned(a, _addUnsigned(_addUnsigned(_H(b, c, d), x), ac))
		return _addUnsigned(_rotateLeft(a, s), b)
	}

	var _II = function (a, b, c, d, x, s, ac) {
		a = _addUnsigned(a, _addUnsigned(_addUnsigned(_I(b, c, d), x), ac))
		return _addUnsigned(_rotateLeft(a, s), b)
	}

	var _convertToWordArray = function (str) {
		var lWordCount
		var lMessageLength = str.length
		var lNumberOfWordsTemp1 = lMessageLength + 8
		var lNumberOfWordsTemp2 = (lNumberOfWordsTemp1 - (lNumberOfWordsTemp1 % 64)) / 64
		var lNumberOfWords = (lNumberOfWordsTemp2 + 1) * 16
		var lWordArray = new Array(lNumberOfWords - 1)
		var lBytePosition = 0
		var lByteCount = 0
		while (lByteCount < lMessageLength) {
			lWordCount = (lByteCount - (lByteCount % 4)) / 4
			lBytePosition = (lByteCount % 4) * 8
			lWordArray[lWordCount] = (lWordArray[lWordCount] |
			(str.charCodeAt(lByteCount) << lBytePosition))
			lByteCount++
		}
		lWordCount = (lByteCount - (lByteCount % 4)) / 4
		lBytePosition = (lByteCount % 4) * 8
		lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition)
		lWordArray[lNumberOfWords - 2] = lMessageLength << 3
		lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29
		return lWordArray
	}

	var _wordToHex = function (lValue) {
		var wordToHexValue = ''
		var wordToHexValueTemp = ''
		var lByte
		var lCount

		for (lCount = 0; lCount <= 3; lCount++) {
			lByte = (lValue >>> (lCount * 8)) & 255
			wordToHexValueTemp = '0' + lByte.toString(16)
			wordToHexValue = wordToHexValue + wordToHexValueTemp.substr(wordToHexValueTemp.length - 2, 2)
		}
		return wordToHexValue
	}

	var x = []
	var k
	var AA
	var BB
	var CC
	var DD
	var a
	var b
	var c
	var d
	var S11 = 7
	var S12 = 12
	var S13 = 17
	var S14 = 22
	var S21 = 5
	var S22 = 9
	var S23 = 14
	var S24 = 20
	var S31 = 4
	var S32 = 11
	var S33 = 16
	var S34 = 23
	var S41 = 6
	var S42 = 10
	var S43 = 15
	var S44 = 21

	str = utf8Encode(str)
	x = _convertToWordArray(str)
	a = 0x67452301
	b = 0xEFCDAB89
	c = 0x98BADCFE
	d = 0x10325476

	xl = x.length
	for (k = 0; k < xl; k += 16) {
		AA = a
		BB = b
		CC = c
		DD = d
		a = _FF(a, b, c, d, x[k + 0], S11, 0xD76AA478)
		d = _FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756)
		c = _FF(c, d, a, b, x[k + 2], S13, 0x242070DB)
		b = _FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE)
		a = _FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF)
		d = _FF(d, a, b, c, x[k + 5], S12, 0x4787C62A)
		c = _FF(c, d, a, b, x[k + 6], S13, 0xA8304613)
		b = _FF(b, c, d, a, x[k + 7], S14, 0xFD469501)
		a = _FF(a, b, c, d, x[k + 8], S11, 0x698098D8)
		d = _FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF)
		c = _FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1)
		b = _FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE)
		a = _FF(a, b, c, d, x[k + 12], S11, 0x6B901122)
		d = _FF(d, a, b, c, x[k + 13], S12, 0xFD987193)
		c = _FF(c, d, a, b, x[k + 14], S13, 0xA679438E)
		b = _FF(b, c, d, a, x[k + 15], S14, 0x49B40821)
		a = _GG(a, b, c, d, x[k + 1], S21, 0xF61E2562)
		d = _GG(d, a, b, c, x[k + 6], S22, 0xC040B340)
		c = _GG(c, d, a, b, x[k + 11], S23, 0x265E5A51)
		b = _GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA)
		a = _GG(a, b, c, d, x[k + 5], S21, 0xD62F105D)
		d = _GG(d, a, b, c, x[k + 10], S22, 0x2441453)
		c = _GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681)
		b = _GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8)
		a = _GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6)
		d = _GG(d, a, b, c, x[k + 14], S22, 0xC33707D6)
		c = _GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87)
		b = _GG(b, c, d, a, x[k + 8], S24, 0x455A14ED)
		a = _GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905)
		d = _GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8)
		c = _GG(c, d, a, b, x[k + 7], S23, 0x676F02D9)
		b = _GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A)
		a = _HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942)
		d = _HH(d, a, b, c, x[k + 8], S32, 0x8771F681)
		c = _HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122)
		b = _HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C)
		a = _HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44)
		d = _HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9)
		c = _HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60)
		b = _HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70)
		a = _HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6)
		d = _HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA)
		c = _HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085)
		b = _HH(b, c, d, a, x[k + 6], S34, 0x4881D05)
		a = _HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039)
		d = _HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5)
		c = _HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8)
		b = _HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665)
		a = _II(a, b, c, d, x[k + 0], S41, 0xF4292244)
		d = _II(d, a, b, c, x[k + 7], S42, 0x432AFF97)
		c = _II(c, d, a, b, x[k + 14], S43, 0xAB9423A7)
		b = _II(b, c, d, a, x[k + 5], S44, 0xFC93A039)
		a = _II(a, b, c, d, x[k + 12], S41, 0x655B59C3)
		d = _II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92)
		c = _II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D)
		b = _II(b, c, d, a, x[k + 1], S44, 0x85845DD1)
		a = _II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F)
		d = _II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0)
		c = _II(c, d, a, b, x[k + 6], S43, 0xA3014314)
		b = _II(b, c, d, a, x[k + 13], S44, 0x4E0811A1)
		a = _II(a, b, c, d, x[k + 4], S41, 0xF7537E82)
		d = _II(d, a, b, c, x[k + 11], S42, 0xBD3AF235)
		c = _II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB)
		b = _II(b, c, d, a, x[k + 9], S44, 0xEB86D391)
		a = _addUnsigned(a, AA)
		b = _addUnsigned(b, BB)
		c = _addUnsigned(c, CC)
		d = _addUnsigned(d, DD)
	}

	var temp = _wordToHex(a) + _wordToHex(b) + _wordToHex(c) + _wordToHex(d)

	return temp.toLowerCase()
}