var farbtastic;
var farbtastic2;

(function($){
	var pickColor = function(a) {
		farbtastic.setColor(a);
		$( '#qtsndtps-link-color' ).val(a);
		$( '#qtsndtps-link-color-example' ).css( 'background-color', a );
	};

	var pickColor2 = function(a) {
		farbtastic2.setColor(a);
		$( '#qtsndtps-text-color' ).val( a );
		$( '#qtsndtps-text-color-example' ).css( 'background-color', a );
	};
	
	$(document).ready( function() {
		farbtastic = $.farbtastic( '#colorPickerDiv', pickColor );
		farbtastic2 = $.farbtastic( '#colorPickerDiv1', pickColor2 );

		pickColor( $( '#qtsndtps-link-color' ).val() );
		pickColor2( $( '#qtsndtps-text-color' ).val() );

		$( '.pickcolor' ).click( function(e) {
			$( '#colorPickerDiv' ).show();
			e.preventDefault();
		});

		$( '.pickcolor1' ).click( function(e) {
			$( '#colorPickerDiv1' ).show();
			e.preventDefault();
		});
		
		$( '#qtsndtps-link-color' ).keyup( function() {
			var a = $( '#qtsndtps-link-color' ).val(),
				b = a;

			a = a.replace(/[^a-fA-F0-9]/, '');
			if ( '#' + a !== b )
				$( '#qtsndtps-link-color' ).val(a);
			if ( a.length === 3 || a.length === 6 )
				pickColor( '#' + a );
		});

		$( '#qtsndtps-text-color' ).keyup( function() {
			var a = $( '#qtsndtps-text-color' ).val(),
				b = a;

			a = a.replace(/[^a-fA-F0-9]/, '');
			if ( '#' + a !== b )
				$('#qtsndtps-text-color').val(a);
			if ( a.length === 3 || a.length === 6 )
				pickColor2( '#' + a );
		});
		
		$(document).mousedown( function() {
			$( '#colorPickerDiv, #colorPickerDiv1' ).hide();
		});
		
		$( '.qtsndtps_hidden' ).hide();

		$( '#qtsndtps_additional_options' ).change( function() {
			if ( $( this ).is( ':checked' ) )
				$( '.qtsndtps_additions_block' ).show();
			else
				$( '.qtsndtps_additions_block' ).hide();
		});
		
		if ( $( '.qtsndtps_title_post:checked' ).val() == '1' )
			$( '.qtsndtps_title_post_fields' ).hide();

		$( '.qtsndtps_title_post' ).change( function() {
			if ( $( this ).is( ':checked' ) && $( this ).val() == '1' )
				$( '.qtsndtps_title_post_fields' ).hide();
			else if ( $( this ).is( ':checked' ) && $( this ).val() == '0' )
				$( '.qtsndtps_title_post_fields' ).show();
		});

		$( '#qtsndtps-link-color-example, #qtsndtps-text-color-example' ).on( "click", function() {
			if ( typeof bws_show_settings_notice == 'function' ) {
				bws_show_settings_notice();
			}
		});		
	});
})(jQuery);