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