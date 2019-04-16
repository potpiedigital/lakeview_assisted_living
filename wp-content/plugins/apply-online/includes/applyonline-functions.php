<?php

/**
 * @since   1.8
 * @access public
 * 
 * @var   mix $option   Option.
 * @return  string
 */ 
function get_aol_option($option, $default = NULL){
     $options = get_option('aol_options');
     $val = isset($options[$option]) ? $options[$option] : $default;
     return $val;
 }
 
 /**
 * Retrieves an option value based on an option name.
 *
 * If the option does not exist or does not have a value, then the return value
 * will be false. This is useful to check whether you need to install an option
 * and is commonly used during installation of plugin options and to test
 * whether upgrading is required.
 *
 * If the option was serialized then it will be unserialized when it is returned.
 *
 * Any scalar values will be returned as strings. You may coerce the return type of
 * a given option by registering an {@see 'option_$option'} filter callback.
 *
 * @since 1.5.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 * @param bool  $can_be_emtpy Optional. Return default value if the option exist with empty string, empty array or null value.
 * @return mixed Value set for the option.
 */
 function get_option_fixed($option, $default = NULL, $can_be_emtpy = FALSE){
    $value = get_option($option, $default);
    
    if(empty($value) AND $can_be_emtpy === FALSE) $value = $default;
    
    return $value;
 }

/**
 * Returns rich application form. 
 *
 * @since    1.6
 * @access   public
 * @var      string    $post_id    Post id.
 * @return   array     Application form fields.
 */
function aol_form($post_id = 0){
    $aol = new SinglePostTemplate();
    return $aol->application_form($post_id);
}

/**
 * Returns array of application form fields. 
 *
 * @since    1.6
 * @access   public
 * @var      string    $post_id    Post id.
 * @return   array     Application form fields.
 */

function aol_form_fields($post_id = 0){
    $aol = new SinglePostTemplate();
    return $aol->application_form_fields($post_id);
}

/**
 * Returns array of application features. 
 *
 * @since    1.6
 * @access   public
 * @var      string    $post_id    Post id.
 * @return   array     Application form fields.
 */
function aol_features($style = 'table'){
    $aol = new SinglePostTemplate();
    return $aol->ad_features(0, $style);
}

/**
 * Returns array of ad features. 
 *
 * @since    1.6
 * @access   public
 * @var      string    $post_id    Post id.
 * @return   array     Application form fields.
 */
function get_aol_ad_features($post_id){
    global $post;
    if(empty($post_id)) $post_id = $post->ID;
    $raw_fields = get_post_meta($post_id);
    $fields = array();
    $i=0;
    foreach($raw_fields as $key => $val){
        if(substr($key, 0, 13) == '_aol_feature_'){
            $fields[$key] = maybe_unserialize($val[0]); //
        }
    }
    
    return $fields;
}

/**
 * Returns array of application form fields in correct order. 
 *
 * @since    1.6
 * @access   public
 * @var      string    $post_id    Post id.
 * @return   array     Application form fields.
 */
function get_aol_ad_post_meta($post_id){
    $form_fields = array();
    $keys_order = get_post_meta($post_id, '_aol_fields_order', TRUE);
    $metas = get_post_meta($post_id);
    //If fields order is not set in DB then fetch all form fields without order.
    if(empty($keys_order)){
        foreach ($metas as $key => $val){ 
            if(substr($key, 0, 9) == '_aol_app_') $form_fields[$key] = unserialize ($val[0]);
        }
    }
    //Get fields according to field order.
    else{ 
        foreach ($keys_order as $key){
            $form_fields[$key] = unserialize($metas[$key][0]);
        }
    }
    
    return $form_fields;
}
/*
 * Returns Ad types with relevent data.
 */
function aol_ad_types(){
    return get_option_fixed('aol_ad_types', array('ad' => array('singular' => 'Ad', 'plural' => 'Ads', 'description' => 'All Ads', 'filters' => array())));
}

function add_aol_prefix($value){
    if(!strpos($value, 'aol_')) $value = 'aol_'.$value;
    return $value;
}

function remove_aol_prefix($value){
    if(strpos($value, 'aol_') !== FALSE) $value = substr($value, 4);
    return $value;
}

function aol_ad_prefix(&$value, $key){
    if(!strpos('aol_', $value)) $value = 'aol_'.$value;
}

/**
 * Returns Registered AOL Ad Types.
 *
 * @since 1.8
 * This function do not accept any parameters.
 * @return array Array of ad types
 */
function get_aol_ad_types(){
    $types = aol_ad_types();
    $types = array_keys($types);
    array_walk($types, 'aol_ad_prefix');
    return $types;
}

function aol_manager_capability(){
    return 'edit_applications';
}
/*
 * Return array of filters
 */
function aol_ad_filters(){
    $filters = array(
        'category' => array('singular' => __('Category', 'ApplyOnline'), 'plural' => __('Categories', 'ApplyOnline')),
        'type' => array('singular' => __('Type', 'ApplyOnline'), 'plural' => __('Types', 'ApplyOnline')),
        'location' => array('singular' => __('Location', 'ApplyOnline'), 'plural' => __('Locations', 'ApplyOnline'))
        );
    return apply_filters('aol_ad_filters', $filters);
}

function aol_app_statuses(){
    $filters = array('pending' => __('Pending', 'ApplyOnline'), 'rejected'=> __('Rejected', 'ApplyOnline'), 'shortlisted' => __('Shortlisted', 'ApplyOnline'));
    return apply_filters('aol_app_statuses', $filters);
}

/*
 * Change post status similsar to its terms. 
 *  
 */
function aol_set_object_terms($object_id, $tt_id, $taxonomy){
    if($taxonomy == 'added_term_relationship') wp_update_post(array('ID' => $object_id, 'post_status' => $tt_id[0]));
}
//add_action('set_object_terms','aol_set_object_terms', 10, 3);

/*
 * Return active status of current Application(CPT)
 * 
 */
function aol_app_statuses_active(){
    $statuses = aol_app_statuses();
    $active = apply_filters('aol_app_active_statuses', get_option_fixed('aol_app_statuses', $statuses));
    foreach ($statuses as $key => $val){
        if(!in_array(sanitize_key($key), $active)) unset($statuses[$key]);
    }
    return $statuses;
}

function aol_ad_current_filters(){
    $filters = aol_ad_filters();
    $set_filters = get_option_fixed('aol_ad_filters', array());
    foreach ($filters as $key => $val){
        if(!in_array(sanitize_key($key), $set_filters)) unset($filters[$key]);
    }
    return $filters;
}

function aol_ad_cpt_filters($cpt){
    $cpt = remove_aol_prefix($cpt);
    $filters = aol_ad_filters();
    $types = get_option_fixed(
            'aol_ad_types', 
            array(
                'ad' => array(
                    'singular' => 'ad', 
                    'plural' => 'Ads', 
                    'filters' => array_keys( aol_ad_filters() )
                    )
                )
            );
    
    $cpt_filters = isset($types[$cpt]['filters']) ? (array)$types[$cpt]['filters']: array();
    
    //Remove filters which are not sett to the ad.
    foreach ($filters as $key => $val){
        if(!in_array(sanitize_key($key), $cpt_filters)) unset($filters[$key]);
    }
    return $filters;
}

function aol_sanitize_taxonomies($taxonomies){
    $tax_keys = array();
    foreach($taxonomies as $key => $tax){
        $tax_keys[] = 'aol_ad_'.sanitize_key($key);
    }
    return $tax_keys;
}

if ( ! function_exists( 'aol_set_current_menu' ) ) {

    function aol_set_current_menu( $parent_file ) {
        global $submenu_file, $current_screen, $pagenow;

        # Set the submenu as active/current while anywhere in your Custom Post Type (nwcm_news)
        if ( $current_screen->post_type == 'aol_ad' ) {
            if ( $pagenow == 'edit-tags.php' or $pagenow == 'term.php' ) {
                $submenu_file = 'edit-tags.php?taxonomy='.str_replace('edit-', '', $current_screen->id).'&post_type=' . $current_screen->post_type;
                $parent_file = 'aol-settings';
            }
        }
        return $parent_file;
    }
    add_filter( 'parent_file', 'aol_set_current_menu' );
}

function aol_array_check($array){
    if(!is_array($array)) $array = array();
    return $array;
}

function aol_sanitize_filters($types){
    foreach($types as $key => $type){
        $types[$key] = array_merge(array('filters' => null), $type);
    }
    return $types;
}

function aol_email_content_type() {
            return 'text/html';
        }
        
/*
 * @field   array   
 * $field
 */
function aol_form_generator($fields, $fieldset = 0, $prepend = NULL, $post_id = 0){
    $form_output = NULL;
    foreach($fields as $field):
        //$field['val'] = isset($field['value']) ? $field['value'] : NULL;
        $field['val'] = isset($field['val']) ? $field['val'] : NULL;
        //Used by Tracker add-on to display saved value.
        //$field['val'] = apply_filters('aol_form_field_value', $field['val'], $field['key'], $field['type'], $post_id);

        $field_key = esc_attr($field['key']);
        if(isset($field['required']) AND (int)$field['required'] == 1){
            $required = '<span class="required-mark">*</span>'; $req_class = 'required';
        } else $required = $req_class = null;
        $field['label'] = isset($field['label']) ? $field['label'] : str_replace('_',' ',$field['key']);
        $field['description'] = isset($field['description']) ? $field['description'] : NULL;
        $wrapper_start = '<div class="form-group"><label for="'. $field_key.'">'.$required.sanitize_text_field($field['label']).'</label>';
        $wrapper_end = '<small id="help'.$field_key.'" class="help-block">'.sanitize_text_field($field['description']).'</small></div>';

        switch ($field['type']){
            case 'paragraph':
                $field['description'] = empty($field['description']) ? $field['label'] : $field['description'];
                $form_output.= '<div class="form-group"><label for="'. $field_key.'">'.$required.sanitize_text_field($field['label']).'</label><p id="'.$field_key.'">'.$required. str_replace(array('[a', '[/a]', ']'), array('<a', '</a>', '>'), sanitize_text_field($field['description'])).'</p></div>';
                break;

            case 'text_area':
                $form_output.= $wrapper_start.'<textarea name="'.$prepend.$field_key.'" class="form-control" id="'.$field_key.'" '.$req_class.' aria-describedby="help'.$field_key.'">'. sanitize_textarea_field($field['val']).'</textarea>'.$wrapper_end;
                break;

            case 'date': 
                $form_output.=  $wrapper_start.'<input type="text" name="'.$prepend.$field_key.'" class="form-control datepicker" id="'.$field_key.'" value="'.sanitize_text_field($field['val']).'"  placeholder="'.__('e.g.', 'ApplyOnline').' '.current_time(get_option('date_format')).'" '.$req_class.'  aria-describedby="help'.$field_key.'" >'.$wrapper_end;
                break;

            case 'dropdown': 
                $form_output.=  $wrapper_start.'<div id="'.$field_key.'" ><select name="'.$prepend.$field_key.'" id="'.$field_key.'" class="form-control '.$field_key.'" '.$req_class.'  aria-describedby="help'.$field_key.'>';
                foreach ($field['options'] as $key => $option) {
                    if($option == $field['val']) $checked = 'selected="selected"';
                    else $checked = null;
                    $form_output.=  '<option class="" value="'.esc_attr($key).'" '.$checked.' >'. sanitize_text_field($option).' </option>';
                }
                $form_output.=  '</select><span id="help'.$field_key.'" class="help-block">'.sanitize_text_field($field['description']).'</span></div></div>';
                break;

            case 'radio':
                $form_output.=  $wrapper_start.'<div id="'.$field_key.'">';
                $i=0;
                foreach ($field['options'] as $key => $option) {
                    $checked = NULL;
                    if(empty($field['val']) and $i == 0) $checked = 'checked' ;
                    elseif($option == $field['val']) $checked = 'checked';
                    $form_output.=  '<label for="'.sanitize_key($option).'"><input type="'.esc_attr($field['type']).'" id="'. esc_attr($option).'" name="'.$prepend.$field_key.'" class="aol-radio '.$field_key.'" value="'.$key.'" '.$checked.' > '.sanitize_text_field($option) .' &nbsp; &nbsp; </label>';
                    $i++;
                }
                $form_output.=  '</div>'.$wrapper_end;
                break;
                
            case 'checkbox':
                $form_output.=  $wrapper_start.'<div id="'.$field_key.'" >';
                $i=0;
                foreach ($field['options'] as $key => $option) {
                    $checked = NULL;
                    if(!empty($field['val']) AND in_array($option, $field['val'])) $checked = 'checked';
                    $form_output.=  '<label for="'.sanitize_key($option).'"><input type="'.sanitize_key($field['type']).'" id="'.sanitize_key($option).'" name="'.$prepend.$field_key.'[]" class="aol-checkbox '.$field_key.'" id="'.$field_key.'" value="'.$key.'" '.$checked.'> '.sanitize_text_field($option) .' &nbsp; &nbsp; </label>';
                    $i++;
                }
                $form_output.=  '</div>'.$wrapper_end;
                break;

            case 'separator':
                if($fieldset == 1) $form_output.=  '</fieldset>';
                $form_output.=  '<fieldset><legend>'.sanitize_text_field($field['label']).'</legend>';
                $fieldset = 1;
                break;
                
            case 'hidden':
                $form_output.=  '<input type="'.esc_attr($field['type']).'" name="'.$prepend.$field_key.'" class="form-control" id="'.$field_key.'" value="'.sanitize_text_field($field['val']).'" '.$req_class.'>';
                break;
            
            //case 'text':
            //case 'email':
            //case 'file':
            //case 'number':
            default:
                $form_output.=  $wrapper_start.'<input type="'.esc_attr($field['type']).'" name="'.$prepend.$field_key.'" class="form-control" id="'.$field_key.'" value="'. sanitize_text_field($field['val']).'" '.$req_class.'>'.$wrapper_end;
                break;
        }
    endforeach;
    if($fieldset == 1) $form_output.=  '</fieldset>';
    
    return $form_output;
}

/*
 * returns domain name to use into email addresses.
 */
function aol_get_domain(){
    // Get the site domain and get rid of www.
    $sitename = strtolower( $_SERVER['SERVER_NAME'] );
    if ( substr( $sitename, 0, 4 ) == 'www.' ) {
        $sitename = substr( $sitename, 4 );
    }
    
    return $sitename;
}

/**
 * Returns array of an existing application data.
 *
 * @since    1.9.92
 * @access   public
 * @var      string    $post    Post Object.
 * @return   array     Application data.
 */
function aol_application_data($post, $parent = NULL){
    $keys = get_post_custom_keys ( $post->ID );
    $parent = get_post_meta( $post->post_parent );
    $data = array();
    foreach ( $keys as $key ):
        if ( substr ( $key, 0, 9 ) == '_aol_app_' ) {

            $val = get_post_meta ( $post->ID, $key, true );
            //Support to previuos versions where only URL was saved in the post meta.
            if (!filter_var($val, FILTER_VALIDATE_URL) === false) $val = '<a href="'.$val.'" target="_blank">View</a> | <a href="'.esc_url ($val).'" download>Download</a>';

            elseif(is_array($val)){ 
                //If the outputs is file attachment
                if(isset($val['file']) AND isset($val['type'])) 
                    $val = '<a href="'.admin_url('?aol_attachment=').$val['file'].'" target="_blank">Attachment</a>';
                
                elseif(isset($val['url']) AND isset($val['type'])) 
                    $val = '<a href="'.esc_url($val['url']).'" target="_blank">Attachment</a>';

                //If output is a radio or checkbox.
                else $val = implode(', ', $val);
            }
            $parent[$key][0] = maybe_unserialize($parent[$key][0]);
            $label = isset($parent[$key][0]['label'])? $parent[$key][0]['label'] : str_replace( '_', ' ', substr ( $key, 9 ) ); 
            $data[] = array('label' => $label, 'value' => $val);
        }
    endforeach;
    return $data;
}

/**
* Encrypt and decrypt
* 
* @author Nazmul Ahsan <n.mukto@gmail.com>
* @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
*
* @param string $string string to be encrypted/decrypted
* @param string $action what to do with this? e for encrypt, d for decrypt
*/
function aol_crypt( $string, $action = 'e' ) {
// you may change these values to your own
    $secret_key = wp_salt('my_simple_secret_key');
    $secret_iv = wp_salt('my_simple_secret_iv');
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
    if( $action == 'e' ) {
    $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
    $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }
    return $output;
}

function rich_print($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';    
}

function get_aol_settings(){
    $settings = apply_filters('aol_settings', array());
    return $settings;
}

/*Quick hack for a fatal error on Application Editor*/
if( !function_exists('has_blocks') ){
    function has_blocks( $post = null ) {
	if ( ! is_string( $post ) ) {
		$wp_post = get_post( $post );
		if ( $wp_post instanceof WP_Post ) {
			$post = $wp_post->post_content;
		}
	}

	return false !== strpos( (string) $post, '<!-- wp:' );
}
}