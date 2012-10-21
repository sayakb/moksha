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

	// Prepare the confirm delete modal
	var modalDelete = $('#modal-delete');

	var btnDelete = $('.btn-delete').click(function() {
		if ($(this).data('dialog') !== true) {
			$(this).data('dialog', true);
			modalDelete.modal('show');

			return false;
		}
	});

	$('#modal-yes').click(function() {
		btnDelete.click();
		return false;
	});

	$('#modal-no').click(function() {
		btnDelete.data('dialog', false);
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
 * Returns the minimum value in an array
 *
 * @access	public
 * @return	object
 */
Array.prototype.min = function(comparer) {

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
Array.prototype.max = function(comparer) {

    if (this.length === 0) return null;
    if (this.length === 1) return this[0];

    comparer = (comparer || Math.max);

    var v = this[0];
    for (var i = 1; i < this.length; i++) {
        v = comparer(this[i], v);
    }

    return v;
}