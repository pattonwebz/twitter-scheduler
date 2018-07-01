/**
 * This it the file containing all the functions needed to work with the tweet
 * in the editor screen of our CPT.
 *
 * @package         Twitter Scheduler
 * @since           0.1.0
 * @author          William Patton <will@pattonwebz.com>
 * @copyright       Copyright (c) 2018, William Patton
 * @license         http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Adds a leading zero to any values that are only single digit.
 *
 * @param {Number} i the number to look at for single/double digits.
 */
function twsc_add_leading_zero(i) {
	if (i < 10) {
		i = "0" + i;
	}
	return i;
}

/**
 * Sets a date and time in some pickers.
 */
function twsc_set_date_and_time_in_pickers() {
	// get the current date and time.
	var d = new Date();

	// get some hours and minutes for the date.
	var hours = twsc_add_leading_zero( d.getHours() );
	var mins  = twsc_add_leading_zero( d.getMinutes() );

	// set out datepicker object to the current date.
	document.getElementById( 'twsc_datepicker' ).valueAsDate = d;
	// set the timepicker to the current times.
	document.getElementById( 'twsc_timepicker' ).value = hours + ':' + mins;

}



/**
 * Sets a date and time in some pickers.
 */
function twsc_set_date_and_time_in_pickers_farthest( timestamp ) {
	// get the current date and time.
	var d = new Date( ( timestamp ) );

	// get some hours and minutes for the date.
	var hours = twsc_add_leading_zero( d.getHours() );
	var mins  = twsc_add_leading_zero( d.getMinutes() );

	// set out datepicker object to the current date.
	document.getElementById( 'twsc_datepicker' ).valueAsDate = d;
	// set the timepicker to the current times.
	document.getElementById( 'twsc_timepicker' ).value = hours + ':' + mins;

}

/**
 * When DOMContentLoaded we add our 2 blutton click listeners.
 */
document.addEventListener(
	"DOMContentLoaded", function() {

		// this is the button to set it to right now.
		document.getElementById( 'twsc_datateime_now' ).addEventListener(
			'click', function(){
				twsc_set_date_and_time_in_pickers();
			}, false
		);

		// this is the button to set it to a time in future.
		document.getElementById( 'twsc_datetime_next' ).addEventListener(
			'click', function(){
				twsc_set_date_and_time_in_pickers_farthest( ( Number( twsc_data.farthest_scheduled ) + ( 60 * Math.floor( ( ( Math.random() * 60 ) + 1 ) ) * Math.floor( ( Math.random() * 3 ) + 3 ) ) ) * 1000 );
			}, false
		);

	}
);

/**
 * TODO: refactor thsi to vanilla JS.
 */
jQuery( document ).ready(
	function(){
		// finds the word counter and adds a row.
		jQuery( '#wp-word-count' ).parent().after( '<tr id="twsc-twitter"></tr>' );
		// adds some cols to the new row.
		jQuery( '#twsc-twitter' ).append( '<td class="twsc-chars" style="padding: 0px 10px 5px;"><div class="twsc-td-text">Excerpt length: <span id="character_counter"></span><span style="font-weight:bold; padding-left:7px;">/ 280</span><span style="font-weight:bold; padding-left:5px;">character(s).</span></div></td>' );
		jQuery( '#twsc-twitter' ).append( '<td id="twsc-hashtags" style="padding: 0px 10px 5px; text-align:right;"></td>' );
		// set the current length to the length in the editor box.
		jQuery( "span#character_counter" ).text( jQuery( "#content" ).val().length );

		/**
	 * Whenever a keyup event happens do some things.
	 */
		jQuery( "#content" ).keyup(
			function() {
				// get current tweet value and it's details.
				let tweet   = jQuery( "#content" ).val();
				let details = twttr.txt.parseTweet( tweet );
				// if the detauls say it's valid set text to green, otherwise red.
				if ( details.valid === false ) {
					jQuery( 'span#character_counter' ).css( 'color', 'red' );
				} else {
					jQuery( 'span#character_counter' ).css( 'color', 'green' );
				}
				// set the current counter to the returned weightedLength.
				jQuery( "span#character_counter" ).text( details.weightedLength );

				// get any hashtags from the tweet.
				let hashtags = twttr.txt.extractHashtags( tweet );
				// if we got hashtags...
				if ( hashtags.length > 0 ) {
					// start a string and loop through hashtags array till it's empty.
					let tags_string      = '';
					let tags_total_count = hashtags.length;
					for ( i = 0; i < tags_total_count; i++ ) {
						/**
				 * First hashtag added has no space or comma, additional tags
				 * have a comma and a space.
				 */
						if ( i === 0 ) {
							tags_string = '#' + hashtags[i];
						} else {
							tags_string = tags_string + ', #' + hashtags[i];
						}
					}
					// display the tags string in the editor.
					jQuery( '#twsc-hashtags' ).text( tags_string );
				}
			}
		);
	}
);
