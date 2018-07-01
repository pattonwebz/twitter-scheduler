/**
 * This the handlers for the special 'duplicate' option added to the actions
 * row on the tweets admin list page.
 *
 * @package         Twitter Scheduler
 * @since           0.1.0
 * @author          William Patton <will@pattonwebz.com>
 * @copyright       Copyright (c) 2018, William Patton
 * @license         http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Copy post function.
 */
function twscCopyPost( e ) {
	e.preventDefault()
	var ajax_url = twsc_copy.ajax_url;
	jQuery.post(
		ajaxurl,
		{
			'action'  : 'copy_tweet',
			'postID'  : e.currentTarget.dataset.postid,
			'_wpnonce': e.currentTarget.dataset.nonce,
		},
		function(response) {
			console.log( 'The server responded: ', response );
			window.location.href = twsc_copy.site_url + '/wp-admin/post.php?post=' + response + '&action=edit'
		}
	);
}
jQuery( document ).ready( function() {
	jQuery( ".twsc-copy-trigger" ).bind( "click", twscCopyPost );
} );
