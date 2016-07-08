jQuery( function ( $ ) {
	var file_frame = [],
		$button = $( '.meta-box-upload-button' ),
		$removebutton = $( '.meta-box-upload-button-remove' );
    $addmorebutton = $( '.meta-box-upload-button-addmore' );
	$('#meta_box_id').on( 'click', '.meta-box-upload-button', function ( event ) {
		event.preventDefault();
		var $this = $( this ),
			id = $this.attr( 'id' );
		// If the media frame already exists, reopen it.
		if ( file_frame[ id ] ) {
			file_frame[ id ].open();

			return;
		}
		// Create the media frame.
		file_frame[ id ] = wp.media.frames.file_frame = wp.media( {
			title    : $this.data( 'uploader_title' ),
			button   : {
				text : $this.data( 'uploader_button_text' )
			},
			multiple : false  // Set to true to allow multiple files to be selected
		} );

		// When an image is selected, run a callback.
		file_frame[ id ].on( 'select', function() {

			// We set multiple to false so only get one image from the uploader
			var attachment = file_frame[ id ].state().get( 'selection' ).first().toJSON();

			// set input
			$( '#' + id + '-value' ).val( attachment.id );

			// set preview
			var img = attachment.filename + ' (' + attachment.filesizeHumanReadable + ')';

			$this.next( 'input' ).next( 'input' ).next( '.image-preview' ).html( img );

		} );

		// Finally, open the modal
		file_frame[ id ].open();

	} );

	$('#meta_box_id').on( 'click', '.meta-box-upload-button-remove', function( event ) {
		event.preventDefault();
		var $this = $( this ),
			id = $this.prev( 'input' ).attr( 'id' );
		$this.next().next( '.image-preview' ).html( '' );
		$( '#' + id + '-value' ).val( 0 );

	} );
  $('#meta_box_id').on( 'click', '.meta-box-upload-button-addmore', function( event ) {
		event.preventDefault();
		var $this = $( this );
    var mediacount = parseInt($( '#media_fieldcount' ).val()) + 1;
    $( '#media_fieldcount' ).val(mediacount);
    var $name = 'media'+mediacount;
		$this.next( 'div' ).next( 'br' ).after("<input type='hidden' id='"+ $name +"-value'  class='small-text' name='meta-box-media["+ $name +"]' value='' /><input type='button' id='"+ $name +"' class='button meta-box-upload-button' value='Upload' /><input type='button' id='"+ $name +"-remove' class='button meta-box-upload-button-remove' value='Remove' /><input type='button' id='"+ $name +"-addmore' class='button meta-box-upload-button-addmore' value='Add More' /><div class='image-preview'></div><br/>");
    $this.hide();
	} );
} );
