var farbtastic;
var farbtastic2;

(function($){
	var pickColor = function(a) {
		farbtastic.setColor(a);
		$('#link-color').val(a);
		$('#link-color-example').css('background-color', a);
	};

	var pickColor2 = function(a) {
		farbtastic2.setColor(a);
		$('#text-color').val(a);
		$('#text-color-example').css('background-color', a);
	};
	
	$(document).ready( function() {
		farbtastic = $.farbtastic('#colorPickerDiv', pickColor);
		farbtastic2 = $.farbtastic('#colorPickerDiv1', pickColor2);

		pickColor( $('#link-color').val() );
		pickColor2( $('#text-color').val() );

		$('.pickcolor').click( function(e) {
			$('#colorPickerDiv').show();
			e.preventDefault();
		});

		$('.pickcolor1').click( function(e) {
			$('#colorPickerDiv1').show();
			e.preventDefault();
		});
		

		$('#link-color').keyup( function() {
			var a = $('#link-color').val(),
				b = a;

			a = a.replace(/[^a-fA-F0-9]/, '');
			if ( '#' + a !== b )
				$('#link-color').val(a);
			if ( a.length === 3 || a.length === 6 )
				pickColor( '#' + a );
		});

		$('#text-color').keyup( function() {
			var a = $('#text-color').val(),
				b = a;

			a = a.replace(/[^a-fA-F0-9]/, '');
			if ( '#' + a !== b )
				$('#text-color').val(a);
			if ( a.length === 3 || a.length === 6 )
				pickColor( '#' + a );
		});
		
		$(document).mousedown( function() {
			$('#colorPickerDiv').hide();
			$('#colorPickerDiv1').hide();
		});
		
		$('.qtsndtps_hidden').hide();

		$('#qtsndtps_additional_options').change( function() {
			if($(this).is(':checked') )
				$('.qtsndtps_additions_block').show();
			else
				$('.qtsndtps_additions_block').hide();
		});
		
		if( $('.qtsndtps_title_post:checked').val() == '1' )
			$('.qtsndtps_title_post_fields').hide();

		$('.qtsndtps_title_post').change( function() {
			if($(this).is(':checked') && $(this).val() == '1' )
				$('.qtsndtps_title_post_fields').hide();
			else if($(this).is(':checked') && $(this).val() == '0' )
				$('.qtsndtps_title_post_fields').show();
		});
		
	});
})(jQuery);