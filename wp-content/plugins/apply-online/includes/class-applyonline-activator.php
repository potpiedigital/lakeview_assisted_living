<?php

/**
 * Fired during plugin activation
 *
 * @link       http://wpreloaded.com/farhan-noor
 * @since      1.0.0
 *
 * @package    Applyonline
 * @subpackage Applyonline/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Applyonline
 * @subpackage Applyonline/includes
 * @author     Farhan Noor <profiles.wordpress.org/farhannoor>
 */
class Applyonline_Activator {
    
        static function dependencies(){
            //Register CPT here for proper Flush Rules.
            $slug = get_option_fixed('aol_slug', 'ads');
            register_post_type('aol_ad', array('has_archive' => true, 'rewrite' => array('slug'=>  $slug)));
            
            $types = aol_ad_types();
            foreach($types as $key => $val){
                register_post_type('aol_'.$key, array(
                'has_archive' => true, 
                'public' => true,
                'rewrite' => array('slug' => sanitize_key($val['plural'])),
                ));
            }
            flush_rewrite_rules();
        
            //Registering taxonomies (Ad Filters) to work at activation.
            $filters = aol_ad_filters();
            foreach($filters as $key => $filter){
                $val = register_taxonomy('aol_ad_'.sanitize_key($key), 'aol_ad');
            }
            
            //Register Application statuses.
            register_taxonomy('aol_application_status', 'aol_application');
        }

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
            //Again display Notice.
            $notices = get_option('aol_dismissed_notices', array());
            $notices = array_diff($notices,array('aol'));
            update_option('aol_dismissed_notices', $notices);
            
            //Run dependencies before terms insertion.
            self::dependencies();
            wp_insert_term(
                        'Shortlisted',
                        'aol_application_status',
                        array( 'description'=> __('Shortlisted', 'apply-online') )
                    );
            wp_insert_term(
                        'Rejected',
                        'aol_application_status'
                    );
            wp_insert_term(
                        'Pending',
                        'aol_application_status'
                    );
            
            
            wp_insert_term(
                'Management',
                'aol_ad_category',
                array( 'slug'=>'admin', 'description'=>'Ads relevent to management department',)
                );

            wp_insert_term(
                    'Finance',
                    'aol_ad_category',
                    array(
                        'slug'=>'finance',
                        'description'=>'Ads relevent to finance department',
                        )
                    );
            wp_insert_term(
                    'IT',
                    'aol_ad_category',
                    array( 
                        'slug'=>'it',
                        'description'=>'Ads relevent to IT department')
                    );
            wp_insert_term(
                'Full Time',
                'aol_ad_type',
                array('slug' => 'full-time', 'description'=>'Ads for full time work',)
                );

            wp_insert_term(
                    'Part Time',
                    'aol_ad_type',
                    array('slug' => 'part-time', 'description'=>'Ads for part time work')
                );
            wp_insert_term( 
                    'Weekend Only',
                    'aol_ad_type',
                    array('slug' => 'weekend', 'description'=>'Ads for work on weekends')
                );
            wp_insert_term( 
                    'Internship',
                    'aol_ad_type',
                    array('slug' => 'internship', 'description'=>'Ads for internship work')
                ); 
            wp_insert_term( 
                    'Morning',
                    'aol_ad_type',
                    array('slug' => 'morning', 'description'=>'Ads for morning session')
                );
            wp_insert_term( 
                    'Evening',
                    'aol_ad_type',
                    array('slug' => 'evening', 'description'=>'Ads for evening session')
                );
            wp_insert_term( 
                    'Islamabad',
                    'aol_ad_location',
                    array('slug' => 'islamabad', 'description'=>'Ads for Islamabad')
                );
            wp_insert_term( 
                    'New Delhi',
                    'aol_ad_location',
                    array('slug' => 'new-delhi', 'description'=>'Ads for New Delhi')
                );
            wp_insert_term( 
                    'London',
                    'aol_ad_location',
                    array('slug' => 'london', 'description'=>'Ads for London')
                );
            wp_insert_term( 
                    'Washington D.C.',
                    'aol_ad_location',
                    array('slug' => 'washington-D-C.', 'description'=>'Ads for Washington D.C.')
                );
            wp_insert_term( 
                    'Ankara',
                    'aol_ad_location',
                    array('slug' => 'ankara', 'description'=>'Ads for Ankara')
                );
            wp_insert_term( 
                    'London',
                    'aol_ad_location',
                    array('slug' => 'London', 'description'=>'Ads for London')
                );
            wp_insert_term( 
                    'Pretoria',
                    'aol_ad_location',
                    array('slug' => 'pretoria', 'description'=>'Ads for Pretoria')
                );
            wp_insert_term( 
                    'Ottawa',
                    'aol_ad_location',
                    array('slug' => 'ottawa', 'description'=>'Ads for Ottawa')
                );
            wp_insert_term( 
                    'Sydney',
                    'aol_ad_location',
                    array('slug' => 'sydney', 'description'=>'Ads for Sydney')
                );
            wp_insert_term( 
                    'Dhaka',
                    'aol_ad_location',
                    array('slug' => 'dhaka', 'description'=>'Ads for Dhaka')
                );
            wp_insert_term( 
                    'Brussels',
                    'aol_ad_location',
                    array('slug' => 'brussels', 'description'=>'Ads for Brussels')
                );
            wp_insert_term( 
                    'Berlin',
                    'aol_ad_location',
                    array('slug' => 'berlin', 'description'=>'Ads for Berlin')
                );
            wp_insert_term( 
                    'Jakarta',
                    'aol_ad_location',
                    array('slug' => 'jakarta', 'description'=>'Ads for Jakarta')
                );
            wp_insert_term( 
                    'Moscow',
                    'aol_ad_location',
                    array('slug' => 'moscow', 'description'=>'Ads for Moscow')
                );
            wp_insert_term( 
                    'Moscow',
                    'aol_ad_location',
                    array('slug' => 'moscow', 'description'=>'Ads for Moscow')
                );
            wp_insert_term( 
                    'Paris',
                    'aol_ad_location',
                    array('slug' => 'paris', 'description'=>'Ads for Paris')
                );
            
            //Insert default fields.
            $fields = array (
                '_aol_app_Name' => 
                array (
                  'type' => 'text',
                  'options' => '',
                ),
                '_aol_app_Email' => 
                array (
                  'type' => 'email',
                  'options' => '',
                ),
            );
            if(!get_option('aol_default_fields')) update_option('aol_default_fields', $fields);
            if(!get_option('aol_recipients_emails')) update_option('aol_recipients_emails', get_bloginfo('admin_email'));
            if(!get_option('aol_allowed_file_types')) update_option('aol_allowed_file_types', 'jpg,jpeg,png,doc,docx,pdf,rtf,odt,txt');
            if(!get_option('aol_slug')) update_option('aol_slug', 'ads');
            if(!get_option('aol_application_message')) update_option('aol_application_message', 'Form has been submitted successfully. If required, we will get back to you shortly!');
            if(!get_option('aol_required_fields_notice')) update_option('aol_required_fields_notice', 'Fields with (*)  are compulsory.');
            if(!get_option('aol_app_statuses')) update_option('aol_app_statuses', array('pending', 'rejected', 'shortlisted'));
            if(!get_option('aol_show_filter')) update_option('aol_show_filter', 0);
            if(!get_option('aol_ad_filters')) update_option('aol_ad_filters', array('category', 'type', 'location'));
            
            self::fix_roles();
            self::bug_fix_before_16();
            //exit(wp_redirect(admin_url('edit.php?post_type=aol_ad&page=settings')));
        }

        
        static function fix_roles(){
            $caps = array(
                'delete_ads' =>TRUE,
                'delete_others_ads' =>TRUE,
                'delete_published_ads' =>TRUE,
                'edit_ads' =>TRUE,
                'edit_others_ads' =>TRUE,
                'edit_private_ads' =>TRUE,
                'edit_published_ads' =>TRUE,
                'publish_ads' =>TRUE,
                'read_private_ads' =>TRUE,
                'delete_applications' =>TRUE,
                'delete_others_applications'=>TRUE,
                'delete_published_applications' =>TRUE,
                'edit_application'         =>TRUE,
                'read_application'         =>TRUE,
                //'delete_application'         =>TRUE,
                'edit_applications'         =>TRUE,
                'edit_others_applications'  =>TRUE,
                'edit_private_applications' =>TRUE,
                'edit_published_applications'=>TRUE,
                'publish_applications'       =>FALSE,
                'create_applications'       =>FALSE,
                'read_private_applications' =>TRUE,
                'read'                      =>TRUE,
                'manage_ads'                =>TRUE,
                'view_admin_dashboard'      => TRUE, //WooCommerce fix, the alternate read capability.
                'manage_ad_terms'       => TRUE,
                'edit_ad_terms'         => TRUE,
                'delete_ad_terms'       => TRUE,
                'assign_ad_terms'          => TRUE,
                );
            
            $role = get_role('administrator');
            foreach($caps as $cap => $val){
                $role->add_cap( $cap, $val ); 
            }

            remove_role('aol_manager');
            add_role('aol_manager', 'AOL Manager', $caps);
        }
        
        /**
         * This function fixes a bug in versions prior to 1.6
         * 
         * The Bug: Application form fields(Post Metas) were serialized twice before save. 
         * 
         * The Fix: Check each app form field and converts it from dual serilized to single serialized value.
         * 
         * @since 1.6
         * 
         */
        static function bug_fix_before_16(){
            global $wpdb;
            $fields = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ID=post_id WHERE post_type = 'aol_ad' AND meta_key LIKE '_aol_app_%'");
            foreach ($fields as $field){
                if (is_string(unserialize($field->meta_value))) update_post_meta ($field->post_id, $field->meta_key, unserialize(unserialize($field->meta_value)));
            }
        }

}
