/**
 * Moksha javascript library
 *
 * @package		Moksha
 * @category	Assets
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

/**
 * Perform startup activities
 *
 * @access	public
 * @return	void
 */
function initPage() {
	// Initialize WYSIWYG editors
	$('.wysiwyg').wysihtml5();

	// Prettify code boxes
	prettyPrint();

	// Date picker
	$('.datepicker').datepicker({ dateFormat: "mm/dd/yy" });

	// Prepare the confirm delete modal
	var modalDelete = $('#modal-delete');

	var btnDelete = $('.btn-delete').click(function () {
		if ($(this).data('dialog') !== true) {
			$(this).data('dialog', true);
			modalDelete.modal('show');

			return false;
		}
	});

	$('#modal-yes').click(function () {
		btnDelete.click();
		return false;
	});

	$('#modal-no').click(function () {
		btnDelete.data('dialog', false);
	});

	// Auto resize images for carousels
	$(window).load(function() {
		$('.carousel').each(function() {
			var max = 0;

			$(this).find('img').each(function() {
				if ($(this).width() > max) {
					max = $(this).width();
				}
			});

			if (max > 0) {
				$(this).width(max);
			}
		});
	});

	// Preserve tab state for tabbable
	$('a[data-toggle="tab"]').on('shown', function (e) {
		localStorage.setItem('moksha_last_tab', $(e.target).attr('href'));
	});
}

/**
 * Generates a unique hash for usage with elements
 *
 * @access	public
 * @return	string	generated hash
 */
function hash() {
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
		var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(16);
	});
}

/**
 * Checks if an element has scroll bars
 *
 * @access	public
 * @return	bool	true if scroll is found
 */
function hasScrollBar(jQobj) {
	var elt = jQobj[0];
	return elt.clientHeight < elt.scrollHeight || elt.clientWidth < elt.scrollWidth;
}

/**
 * Restored a saved tab
 *
 * @access	public
 * @param	string	current state
 * @return	void
 */
function setTabState(state) {
	if (state == 'error') {
		var lastTab = localStorage.getItem('moksha_last_tab');

		if (lastTab) {
			$('a[href=' + lastTab + ']').tab('show');
		}
	}
	else if (state == 'success') {
		localStorage.removeItem('moksha_last_tab');
	}
}

/**
 * Encodes a HTML for display
 *
 * Example: htmlentities("foo'bar", "ENT_QUOTES");
 *
 * @access	public
 * @param	string	html to encode
 * @param	string	quote style
 * @param	string	character set
 * @param	bool	indicates whether to encode twice
 * @return	string	encoded string
 */
function htmlentities(string, quote_style, charset, double_encode) {
	var hash_map = get_html_translation_table('HTML_ENTITIES', quote_style);
	var symbol = '';

	string = string == null ? '' : string + '';

	if (!hash_map) {
		return false;
	}

	if (quote_style && quote_style === 'ENT_QUOTES') {
		hash_map["'"] = '&#039;';
	}

	if (!!double_encode || double_encode == null) {
		for (symbol in hash_map) {
			if (hash_map.hasOwnProperty(symbol)) {
				string = string.split(symbol).join(hash_map[symbol]);
			}
		}
	}
	else {
		string = string.replace(/([\s\S]*?)(&(?:#\d+|#x[\da-f]+|[a-zA-Z][\da-z]*);|$)/g, function (ignore, text, entity) {
			for (symbol in hash_map) {
				if (hash_map.hasOwnProperty(symbol)) {
					text = text.split(symbol).join(hash_map[symbol]);
				}
			}

			return text + entity;
		});
	}

	return string;
}

/**
 * Decodes a HTML for display
 *
 * Example: html_entity_decode('&amp;lt;');
 *
 * @access	public
 * @param	string	html to encode
 * @param	string	quote style
 * @return	string	decoded string
 */
function html_entity_decode(string, quote_style) {
	var hash_map = {};
	var symbol = '';
	var tmp_str = string.toString();
	var entity = '';

	if (false === (hash_map = get_html_translation_table('HTML_ENTITIES', quote_style))) {
		return false;
	}

	delete (hash_map['&']);
	hash_map['&'] = '&amp;';

	for (symbol in hash_map) {
		entity = hash_map[symbol];
		tmp_str = tmp_str.split(entity).join(symbol);
	}

	tmp_str = tmp_str.split('&#039;').join("'");
	return tmp_str;
}

/**
 * Returns the translation table corresponding to a specific constant
 *
 * @access	public
 * @param	string	table to look up
 * @param	string	quote style to look up
 * @return	string	corresponding values
 */
function get_html_translation_table(table, quote_style) {
	var entities = {};
	var hash_map = {};
	var decimal;
	var constMappingTable = {};
	var constMappingQuoteStyle = {};
	var useTable = {};
	var useQuoteStyle = {};

	// Translate arguments
	constMappingTable[0] = 'HTML_SPECIALCHARS';
	constMappingTable[1] = 'HTML_ENTITIES';
	constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
	constMappingQuoteStyle[2] = 'ENT_COMPAT';
	constMappingQuoteStyle[3] = 'ENT_QUOTES';

	useTable = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
	useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

	if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
		throw new Error("Table: " + useTable + ' not supported');
	}

	entities['38'] = '&amp;';
	if (useTable === 'HTML_ENTITIES') {
		entities['160'] = '&nbsp;';
		entities['161'] = '&iexcl;';
		entities['162'] = '&cent;';
		entities['163'] = '&pound;';
		entities['164'] = '&curren;';
		entities['165'] = '&yen;';
		entities['166'] = '&brvbar;';
		entities['167'] = '&sect;';
		entities['168'] = '&uml;';
		entities['169'] = '&copy;';
		entities['170'] = '&ordf;';
		entities['171'] = '&laquo;';
		entities['172'] = '&not;';
		entities['173'] = '&shy;';
		entities['174'] = '&reg;';
		entities['175'] = '&macr;';
		entities['176'] = '&deg;';
		entities['177'] = '&plusmn;';
		entities['178'] = '&sup2;';
		entities['179'] = '&sup3;';
		entities['180'] = '&acute;';
		entities['181'] = '&micro;';
		entities['182'] = '&para;';
		entities['183'] = '&middot;';
		entities['184'] = '&cedil;';
		entities['185'] = '&sup1;';
		entities['186'] = '&ordm;';
		entities['187'] = '&raquo;';
		entities['188'] = '&frac14;';
		entities['189'] = '&frac12;';
		entities['190'] = '&frac34;';
		entities['191'] = '&iquest;';
		entities['192'] = '&Agrave;';
		entities['193'] = '&Aacute;';
		entities['194'] = '&Acirc;';
		entities['195'] = '&Atilde;';
		entities['196'] = '&Auml;';
		entities['197'] = '&Aring;';
		entities['198'] = '&AElig;';
		entities['199'] = '&Ccedil;';
		entities['200'] = '&Egrave;';
		entities['201'] = '&Eacute;';
		entities['202'] = '&Ecirc;';
		entities['203'] = '&Euml;';
		entities['204'] = '&Igrave;';
		entities['205'] = '&Iacute;';
		entities['206'] = '&Icirc;';
		entities['207'] = '&Iuml;';
		entities['208'] = '&ETH;';
		entities['209'] = '&Ntilde;';
		entities['210'] = '&Ograve;';
		entities['211'] = '&Oacute;';
		entities['212'] = '&Ocirc;';
		entities['213'] = '&Otilde;';
		entities['214'] = '&Ouml;';
		entities['215'] = '&times;';
		entities['216'] = '&Oslash;';
		entities['217'] = '&Ugrave;';
		entities['218'] = '&Uacute;';
		entities['219'] = '&Ucirc;';
		entities['220'] = '&Uuml;';
		entities['221'] = '&Yacute;';
		entities['222'] = '&THORN;';
		entities['223'] = '&szlig;';
		entities['224'] = '&agrave;';
		entities['225'] = '&aacute;';
		entities['226'] = '&acirc;';
		entities['227'] = '&atilde;';
		entities['228'] = '&auml;';
		entities['229'] = '&aring;';
		entities['230'] = '&aelig;';
		entities['231'] = '&ccedil;';
		entities['232'] = '&egrave;';
		entities['233'] = '&eacute;';
		entities['234'] = '&ecirc;';
		entities['235'] = '&euml;';
		entities['236'] = '&igrave;';
		entities['237'] = '&iacute;';
		entities['238'] = '&icirc;';
		entities['239'] = '&iuml;';
		entities['240'] = '&eth;';
		entities['241'] = '&ntilde;';
		entities['242'] = '&ograve;';
		entities['243'] = '&oacute;';
		entities['244'] = '&ocirc;';
		entities['245'] = '&otilde;';
		entities['246'] = '&ouml;';
		entities['247'] = '&divide;';
		entities['248'] = '&oslash;';
		entities['249'] = '&ugrave;';
		entities['250'] = '&uacute;';
		entities['251'] = '&ucirc;';
		entities['252'] = '&uuml;';
		entities['253'] = '&yacute;';
		entities['254'] = '&thorn;';
		entities['255'] = '&yuml;';
	}

	if (useQuoteStyle !== 'ENT_NOQUOTES') {
		entities['34'] = '&quot;';
	}

	if (useQuoteStyle === 'ENT_QUOTES') {
		entities['39'] = '&#39;';
	}

	entities['60'] = '&lt;';
	entities['62'] = '&gt;';

	// ASCII decimals to real symbols
	for (decimal in entities) {
		if (entities.hasOwnProperty(decimal)) {
			hash_map[String.fromCharCode(decimal)] = entities[decimal];
		}
	}

	return hash_map;
}

/**
 * Returns the minimum value in an array
 *
 * @access	public
 * @return	object
 */
Array.prototype.min = function (comparer) {

	if (this.length === 0) return null;
	if (this.length === 1) return this[0];

	comparer = (comparer || Math.min);
	var v = this[0];

	for (var i = 1; i < this.length; i++) {
		v = comparer(this[i], v);
	}

	return v;
}

/**
 * Returns the maximum value in an array
 *
 * @access	public
 * @return	object
 */
Array.prototype.max = function (comparer) {

	if (this.length === 0) return null;
	if (this.length === 1) return this[0];

	comparer = (comparer || Math.max);
	var v = this[0];

	for (var i = 1; i < this.length; i++) {
		v = comparer(this[i], v);
	}

	return v;
}