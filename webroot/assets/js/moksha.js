/**
 * Moksha javascript library
 *
 * @package		Moksha
 * @category	Assets
 * @author		Sayak Banerjee <sayakb@kde.org>
 */

/**
 * Perform staartup activities
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
	$('.btn-delete').click(confirmDelete);
	$('#modal-yes').click(submitDelete);
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
 * Deletion confirmation modal
 *
 * @access	public
 * @return	bool	false to stop navigation
 */
function confirmDelete() {
	var btn = $(this).attr('name');
	$('#modal-delete').attr('data-control', btn).modal('show');

	return false;
}

/**
 * Submit delete confirmation
 *
 * @access	public
 * @return	bool	false to stop navigation
 */
function submitDelete() {
	var btn = $('#modal-delete').attr('data-control');
	$('[name=' + btn + ']').click();

	return false;
}