function twscCopyPost( e ) {
	e.preventDefault()
	var ajax_url = twsc_copy.ajax_url;
	jQuery.post(
		ajaxurl,
		{
			'action': 'copy_tweet',
			'postID': e.currentTarget.dataset.postid,
		},
		function(response) {
			console.log('The server responded: ', response);
			window.location.href = 'http://local.sandbox/wp-admin/post.php?post=' + response + '&action=edit'
		}
	);
}
jQuery( document ).ready( function() {
	jQuery( ".twsc-copy-trigger" ).bind( "click", twscCopyPost );
} );
