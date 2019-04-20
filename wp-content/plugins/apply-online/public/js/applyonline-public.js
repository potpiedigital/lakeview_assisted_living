(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
        
        $(document).ready(function(){
            $('.datepicker').datepicker({
                yearRange: "-99:+50",
                //dateFormat : aol_public.date_format,
                changeMonth: true,
                changeYear: true,
            });
            
         $( ".aol_app_form" ).submit(function(){
            var datastring = new FormData(document.getElementById("aol_app_form"));
            $.ajax({
                    url: aol_public.ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: datastring,
                    //async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function(){
                        $('#aol_form_status').removeClass();
                        $('#aol_form_status').html('Submitting . . . . . ');
                        $('#aol_form_status').addClass('alert alert-warning');
                        $(".aol-form-button").prop('disabled', true);
                    },
                    success:function(response){
                        $(document).trigger('afterAppSubmit', response); //Custom event  on ajax completiong
                        
                        if(response['success']==true){
                            $('#aol_form_status').removeClass();
                            $('#aol_form_status').addClass('alert alert-success');
                            $('#aol_form_status').html(response['message']);
                            $(".aol-form-button").prop('disabled', false);
                            if(response['hide_form']==true) $('.aol_app_form').slideUp(); //Show a sliding effecnt.
                            
                            //Divert to thank you page. 
                            if(response.divert != null){
                                var page = response.divert;
                                window.location.href = stripslashes(page);
                            }
                        }
                        else if(response['success']==false){
                            $('#aol_form_status').removeClass();
                            $('#aol_form_status').addClass('alert alert-danger');
                            $('#aol_form_status').html(response['error']);
                            $(".aol-form-button").prop('disabled', false);
                        }
                        //If response is not jSon.
                        else{
                            $('#aol_form_status').addClass('alert alert-danger');
                            $('#aol_form_status').html('Form saved with errors. Please contact us for more information. ');
                            $(".aol-form-button").prop('disabled', false);
                        }
                    },
                    error: function(xhr, type, error){
                        $('#aol_form_status').removeClass();
                        $('#aol_form_status').addClass('alert alert-danger');
                        $('#aol_form_status').html('An unexpected error occured with error code: <u>' + xhr.status + " " + xhr.statusText+'</u>. Please contact us for more information.');
                        $(".aol-form-button").prop('disabled', false);
                    }
            });
            return false;
          });     
        })

})( jQuery );

function stripslashes (str) {
            return (str + '').replace(/\\(.?)/g, function (s, n1) {
              switch (n1) {
              case '\\':
                return '\\';
              case '0':
                return '\u0000';
              case '':
                return '';
              default:
                return n1;
              }
            });
        }