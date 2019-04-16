(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
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
        var i = 0;
        $(document).ready(function(){
            jQuery(document).on( 'click', '.aol .notice-dismiss', function() {

                jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'aol_dismiss_notice'
                    }
                })
            });
            
            $('.datepicker').datepicker({
                minDate:    +1,
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                changeYear: true
            });
                $("#app_form_fields").sortable({
                  placeholder: "ui-state-highlight",
                  update: function( event, ui ) {
                      $('#aol_keys_order_wrapper').empty(); //
                      $( '#app_form_fields tr' ).each(function(){
                          var field = $(this).data( "id" );
                          $('#aol_keys_order_wrapper').append('<input type="hidden" id="aol_fields_order" name="_aol_fields_order[]" value="'+field+'" />');
                      });
                  }
                });
                
            /*Ad Types Settings*/
            $('#ad_aol_type').click(function(){
                var fieldNameRawSingular=$('#ad_type_singular').val(); // Get Raw value.
                var fieldNameRawPlural=$('#ad_type_plural').val(); // Get Raw value.
                var fieldNameRawDesc=$('#ad_type_description').val(); // Get Raw value.
                
                var fieldNameSingular = sanitize_me(fieldNameRawSingular);


                if(fieldNameSingular != '' && fieldNameRawPlural != ''){
                    $('#ad_types').append('<li>'+fieldNameRawSingular+' ('+fieldNameRawPlural+') '+fieldNameRawDesc+' <input type="hidden" class="'+fieldNameSingular+' aol_ad_type" name="aol_ad_types['+fieldNameSingular+'][singular]" value="'+fieldNameRawSingular+'" /><input type="hidden" name="aol_ad_types['+fieldNameSingular+'][plural]" value="'+fieldNameRawPlural+'" /><input type="hidden" name="aol_ad_types['+fieldNameSingular+'][description]" value="'+fieldNameRawDesc+'" /> <p><i>Save changes to get shortcode & filters. </i></p> <div class="button button-small aol-remove button-danger">Delete</div></li>');
                    i++;
                    $('#ad_type_singular').val(''); // Get Raw value.
                    $('#ad_type_plural').val(''); // Get Raw value.
                    $('#ad_type_description').val(''); // Get Raw value.
                }
                else{
                    $('#adapp_name').css('border','1px solid #F00');

                }

            });
            $('#ad_types').on('click', 'li .aol-remove',function(){
                $(this).parentsUntil('ol', 'li').remove();
            });
            $('#ad_types').find('.default').click(function(){
                return false;
            });
                
            /*Ad editor Scripts*/

            /*Application Field Type change for new Field only*/
            $('#adapp_field_type, .aol_form_fields_changer').change(function(){
               var fieldType=$(this).val();

               if(fieldType == 'checkbox' || fieldType == 'dropdown' || fieldType == 'radio'){
                   $(this).siblings('#adapp_field_options').show();
               }
               else{
                   $(this).siblings('#adapp_field_options').hide();
                   $(this).siblings('#adapp_field_options').val('');
               }
            });

            /*Add Application Field (Group Fields)*/
            $('#addField, .addField').on('click', function(e){
                e.preventDefault();
                var wrapper = $(this).closest('.aolFormBuilder');
                
                var tempID = $(this).data('temp'); 
                
                var fieldNameRaw = wrapper.find('#adapp_name').val(); // Get Raw value.
                //var fieldName = md5(fieldNameRaw)
                //alert(fieldNameRaw);
                var fieldID = sanitize_me(fieldNameRaw); //Replace white space with _.
                if( tempID == '' ) var fieldName = '_aol_app_'+fieldID;
                else if( tempID == 'new' ) var fieldName = 'new[_aol_app_'+fieldID+']';
                else if( tempID != '' ) var fieldName = tempID+'[_aol_app_'+fieldID+']';
                //alert(fieldName);
                var fieldType = wrapper.find('#adapp_field_type').val(); 
                var fieldOptions = wrapper.find('#adapp_field_options').val();
                var fieldDesccription = wrapper.find('#adapp_field_help').val();
                var style;

                //Highlight culprut
                $(this).siblings().css('border','1px solid #ddd');

                var fieldTypeHtml = $('#adapp_field_type').html();
                if(fieldName != '' && fieldType != ''){
                    if(fieldType=='text' || fieldType=='email' || fieldType=='date' || fieldType == 'text_area'|| fieldType == 'file' || fieldType == 'separator' || fieldType == 'paragraph'  || fieldType == 'number'){
                        style = "display:none";
                    }
                    else {
                        style = "";
                        wrapper.find('#adapp_field_options').val('');
                        wrapper.find('#adapp_field_options').hide();
                    }
                    //$('#app_form_fields').append('<tr class="'+fieldName+'"><td><label for="'+fieldName+'"><span class="dashicons dashicons-menu"></span> '+fieldNameRaw+'</label> &nbsp; </td><td><input name="'+fieldName+'[label]" value="'+fieldNameRaw+'" type="hidden"><input name="'+fieldName+'[required]" value="1" type="hidden"><div class="button-primary toggle-required">Required</div>  <select class="adapp_field_type '+fieldName+'" name="'+fieldName+'[type]">'+fieldTypeHtml+'</select><input type="text" class="adapp_field_help adapp_field_description" name="'+fieldName+'[description]" value="'+fieldDesccription+'" placeholder="Narration or help text" /><input type="text" class="adapp_field_options" name="'+fieldName+'[options]" value="'+fieldOptions+'" placeholder="Option1, Option2, Option3" style="'+style+'" /> &nbsp; <div class="button aol-remove">Delete</div></td></tr>');
                    wrapper.find('.app_form_fields').append('<tr class="'+fieldID+'"><td><label for="'+fieldID+'"><span class="dashicons dashicons-menu"></span> '+fieldNameRaw+'</label> &nbsp; </td><td><input name="'+fieldName+'[label]" value="'+fieldNameRaw+'" type="hidden"><input name="'+fieldName+'[required]" value="1" type="hidden"> <div class="button-primary toggle-required">Required</div> <select id="'+fieldID+'"  class="adapp_field_type '+fieldID+'" name="'+fieldName+'[type]">'+fieldTypeHtml+'</select><input type="text" class="adapp_field_help adapp_field_description" name="'+fieldName+'[description]" value="'+fieldDesccription+'" placeholder="Help text" /><input type="text" class="adapp_field_options" name="'+fieldName+'[options]" value="'+fieldOptions+'" placeholder="Option1, Option2, Option3" style="'+style+'" /> &nbsp; <span class="dashicons dashicons-trash aol-remove"></span></td></tr>');
                    $('#aol_keys_order_wrapper').append('<input type="hidden" id="aol_fields_order" name="_aol_fields_order[]" value="'+fieldName+'" />');
                    $("."+fieldID+" ."+fieldType).attr('selected','selected');
                    $('#adapp_name, #adapp_field_help').val('');
                    $('#adapp_field_type').val('');
                    //window.tb_remove();
                }
                else{
                    if(fieldName == '') wrapper.find('#adapp_name').css('border','1px solid #F00');
                    if(fieldType == '') wrapper.find('#adapp_field_type').css('border','1px solid #F00');
                }
                return false;
            });

            /* Application Field Type change for existing fields. */
            $('#app_form_fields, .app_form_fields').on('change', 'tr td .adapp_field_type',function(){

                var fieldType=$(this).val();
                
                if(!(fieldType == 'separator' || fieldType == 'paragraph')){
                    $(this).parent().find('.button-required').addClass('toggle-required');
                }

                if(fieldType == 'checkbox' || fieldType == 'dropdown' || fieldType == 'radio'){
                   $(this).next().next().show();
                }
                else{
                   $(this).next().next().hide();
                }
            });

            /*Add advertisement Feature*/
            $('#addFeature').click(function(){
                var fieldNameRaw=$('#adfeature_name').val(); // Get Raw value.
                var fieldNameRaw = fieldNameRaw.trim();    // Remove White Spaces from both ends.
                var fieldName = fieldNameRaw.replace(" ", "_"); //Replace white space with _.

                var fieldVal = $('#adfeature_value').val();
                var fieldVal = fieldVal.trim();

                if(fieldName != '' && fieldVal!=''){
                    $('#ad_features').append('<li class="'+fieldName+'"><label for="'+fieldName+'">'+fieldNameRaw+'</label> &nbsp; <input type="text" name="_aol_feature_'+fieldName+'[label]" value="'+fieldNameRaw+'" placeholder="Label" > &nbsp;  <input type="text" name="_aol_feature_'+fieldName+'[value]" value="'+fieldVal+'" laceholder="Value" > &nbsp; <div class="button aol-remove">Delete</div></li>');
                    $('#adfeature_name').val(""); //Reset Field value.
                    $('#adfeature_value').val(""); //Reset Field value.
                }
            });
            /*Remove Job app or ad Feature Fields*/
            $('.adpost_fields').on('click', 'li .aol-remove',function(){
                $(this).parent('li').remove();
            });
            $('#app_form_fields, .app_form_fields').on('click', 'tr td .aol-remove',function(){
                $(this).parentsUntil('tbody', 'tr').remove();
            });
            
            //Toggle Required
            $('.adpost_fields').on('click', 'tr .toggle-required', function(){
                var required = $(this).prev('input').val();
                $(this).prev('input').val(required === '1'? '0': '1');
                $(this).toggleClass('button-disabled');
            });
            /*END Ad editor Scripts*/
            
            /*Settings Tabs*/
            $('.aol-settings').children('.tab-data:first').show();
            $('.aol-primary').children('.nav-tab').click(function(){
                $('.aol-primary').find('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                var target = $(this).data("id");

                $('.aol-settings').children('.tab-data').hide();
                $("#"+target).show();
            });
            /*End Settings Tabs*/
            
            /*Template Tabs*/
            $('.aol-template-wrapper').children('.templateForm:first').show();
            $('.aol-template-tabs').children('.nav-tab').click(function(){
                $('.aol-template-tabs').find('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                var target = $(this).data("id");

                $('.aol-template-wrapper').children('.templateForm').hide();
                $("#"+target).show();
            });
            
            //Remove Template.
            $('.templateForm').on('click', '.aol-remove', function(){
                var target = $(this).closest('.templateForm').attr('id');
                $('#' + target).remove();
                //Remove relevant tab too.
                $('.aol-template-tabs [data-id="'+target+'"]').remove();
                $('.aol-template-tabs [data-id="templateFormNew"]').addClass('nav-tab-active');
                $("#templateFormNew").show();
            });
            /*End Template Tabs*/
            
            $('#aol_submission_default').click(function(event){
                event.preventDefault();
                $('#aol_submission_default_message').val(aol_admin.app_submission_message);
                $('#aol_submission_default_message').text(aol_admin.app_submission_message);
            });
            
            $('#app_closed_alert_button').click(function(event){
                event.preventDefault();
                $('#app_closed_alert').val(aol_admin.app_closed_alert);
                $('#app_closed_alert').text(aol_admin.app_closed_alert);
            });
            
            $('#aol_required_fields_button').click(function(event){
                event.preventDefault();
                $('#aol_required_fields_notice').val(aol_admin.aol_required_fields_notice);
                $('#aol_required_fields_notice').text(aol_admin.aol_required_fields_notice);
            });
        

            /*Form Builder Template rendor*/
            $('#aol_template_loader').on( 'change', function() {
                var dd = $(this);
                var temp = $(this).val();
                var tempName = $('#aol_template_loader option[value='+temp+']').text();

                var decision = confirm("You will lose any existing form.");
                if(decision == false || temp == ''){
                    dd.val('');
                    return;
                }
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'aol_template_render',
                        template: temp,
                    },
                    beforeSend: function(){
                        $('.template_loading_status').html('<img src="'+decodeURI(aol_admin.aol_url)+'images/loading.gif">');
                        //$('#app_form_fields').html('<img src="'+decodeURI(aol_admin.aol_url)+'images/loading.gif">');
                    },
                    success:function(response){
                        //dd.val('');
                        $('.template_loading_status').text('Form Template loaded successfully.');
                        $('#app_form_fields').html(response);
                    }
                });
            });
            /*END Form Builder Template rendor*/         

        }); //Document Ready State.
        
})( jQuery );
function sanitize_me(field){
    field = field.trim();
    field = field.replace(/\s/g, "_");
    field = field.toLowerCase();
    field = field.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return field;
}