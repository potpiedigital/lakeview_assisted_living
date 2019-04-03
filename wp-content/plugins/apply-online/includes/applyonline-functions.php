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
 * @since   1.8
 * @access public
 * 
 * @var   mix $option   Option.
 * @return  string
 */ 
 function get_option_fixed($option, $default = NULL){
    $value = get_option($option);
    if(empty($value)) $value = $default;
    
    //Check Empty on arrays. Array may be missing an element for default value.
    if( is_array($default) and is_array($value) ){
        $value = array_merge($default, $value);
    }
        
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
function aol_features($post_id = 0){
    $aol = new SinglePostTemplate();
    return $aol->ad_features();
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
    $raw_fields = get_post_meta($post_id);
    $fields = array();
    $i=0;
    foreach($raw_fields as $key => $val){
        if(substr($key, 0, 13) == '_aol_feature_'){
            $fields[$key] = $val[0]; //
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

function aol_ad_prefix(&$value, $key){
    $value = 'aol_'.$value; 
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
    $filters = array(__('Category', 'apply-online') => __('Categories', 'apply-online'), __('Type', 'apply-online') => __('Types', 'apply-online'), __('Location', 'apply-online') => __('Locations', 'apply-online'));
    return apply_filters('aol_ad_filters', $filters);
}

function aol_app_statuses(){
    $filters = array('pending' => 'Pending', 'rejected'=>'Rejected', 'shortlisted' => 'Shortlisted');
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
    $filters = aol_ad_filters(); 
    $types = get_option_fixed('aol_ad_types', array('aol_ad')); 
    $cpt_filters = isset($types[$cpt]['filters']) ? (array)$types[$cpt]['filters']: array(); 
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

        if(isset($field['required']) AND (int)$field['required'] == 1){
            $required = '<span class="required-mark">*</span>'; $req_class = 'required';
        } else $required = $req_class = null;
        $field['label'] = isset($field['label']) ? $field['label'] : str_replace('_',' ',$field['key']);
        $wrapper_start = '<div class="form-group"><label for="'.$field['key'].'">'.$required.sanitize_text_field($field['label']).'</label>';
        $wrapper_end = '</div>';

        switch ($field['type']){
            case 'paragraph':
                $form_output.= '<div class="form-group"><p for="'.$field['key'].'">'.$required.$field['label'].'</p></div>';
                break;

            case 'text_area':
                $form_output.= $wrapper_start.'<textarea name="'.$prepend.$field['key'].'" class="form-control" id="'.$field['key'].'" '.$req_class.'>'.$field['val'].'</textarea>'.$wrapper_end;
                break;

            case 'date': 
                $form_output.=  $wrapper_start.'<input type="text" name="'.$prepend.$field['key'].'" class="form-control datepicker" id="'.$field['key'].'" value="'.$field['val'].'"  placeholder="'.__('example', 'apply-online').': '.current_time(get_option('date_format')).'" '.$req_class.'>'.$wrapper_end;
                break;

            case 'dropdown': 
                $form_output.=  $wrapper_start.'<div id="'.$field['key'].'" ><select name="'.$prepend.$field['key'].'" id="'.$field['key'].'" class="form-control '.$field['key'].'" '.$req_class.'>';
                foreach ($field['options'] as $key => $option) {
                    if($option == $field['val']) $checked = 'selected="selected"';
                    else $checked = null;
                    $form_output.=  '<option class="" value="'.$key.'" '.$checked.' >'.$option.' </option>';
                }
                $form_output.=  '</select></div></div>';
                break;

            case 'radio':
                $form_output.=  $wrapper_start.'<div id="'.$field['key'].'">';
                $i=0;
                foreach ($field['options'] as $key => $option) {
                    $checked = NULL;
                    if(empty($field['val']) and $i == 0) $checked = 'checked' ;
                    elseif($option == $field['val']) $checked = 'checked';
                    $form_output.=  '<label for="'.sanitize_key($option).'"><input type="'.$field['type'].'" id="'.sanitize_key($option).'" name="'.$prepend.$field['key'].'" class="aol-radio '.$field['key'].'" value="'.$key.'" '.$checked.' > '.$option .' &nbsp; &nbsp; </label>';
                    $i++;
                }
                $form_output.=  '</div>'.$wrapper_end;
                break;
            case 'checkbox':
                $form_output.=  $wrapper_start.'<div id="'.$field['key'].'" >';
                $i=0;
                foreach ($field['options'] as $key => $option) {
                    $checked = NULL;
                    if(!empty($field['val']) AND in_array($option, $field['val'])) $checked = 'checked';
                    $form_output.=  '<label for="'.sanitize_key($option).'"><input type="'.$field['type'].'" id="'.sanitize_key($option).'" name="'.$prepend.$field['key'].'[]" class="aol-checkbox '.$field['key'].'" id="'.$field['key'].'" value="'.$key.'" '.$checked.'> '.$option .'. &nbsp; &nbsp; </label>';
                    $i++;
                }
                $form_output.=  '</div>'.$wrapper_end;
                break;

            case 'separator':
                if($fieldset == 1) $form_output.=  '</fieldset>';
                $form_output.=  '<fieldset><legend>'.$field['label'].'</legend>';
                $fieldset = 1;
                break;
            case 'hidden':
                $form_output.=  '<input type="'.$field['type'].'" name="'.$prepend.$field['key'].'" class="form-control" id="'.$field['key'].'" value="'.$field['val'].'" '.$req_class.'>';
                break;
            //case 'text':
            //case 'email':
            //case 'file':
            //case 'number':
            default:
                $form_output.=  $wrapper_start.'<input type="'.$field['type'].'" name="'.$prepend.$field['key'].'" class="form-control" id="'.$field['key'].'" value="'.$field['val'].'" '.$req_class.'>'.$wrapper_end;
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