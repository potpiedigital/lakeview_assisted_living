<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://wpreloaded.com/farhan-noor
 * @since      1.0
 *
 * @package    Applyonline
 * @subpackage Applyonline/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0
 * @package    Applyonline
 * @subpackage Applyonline/includes
 * @author     Farhan Noor
 */
class Applyonline {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Applyonline_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
                if ( defined( 'APPLYONLINE_VERSION' ) ) {
			$this->version = APPLYONLINE_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'apply-online';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

                add_action( 'init', array( $this, 'register_aol_post_types' ), 5 );
                add_action( 'init', array($this, 'after_plugin_update'));
                add_action( 'wp_enqueue_scripts', array($this, 'load_dashicons_front_end') );
                add_filter( 'views_edit-aol_application', array($this, 'my_views' ));
                //add_action( 'tgmpa_register', array($this, 'applyonline_register_required_plugins' ));

                new Applyonline_AjaxHandler();
                new Applyonline_Labels();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Applyonline_Loader. Orchestrates the hooks of the plugin.
	 * - Applyonline_i18n. Defines internationalization functionality.
	 * - Applyonline_Admin. Defines all hooks for the admin area.
	 * - Applyonline_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-applyonline-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-applyonline-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-applyonline-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-applyonline-public.php';
                
                /*
                 * Form Builder addon
                 */
                //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/builder/class-functions.php';
                //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/builder/class-init.php';

                //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'required-plugins/class-tgm-plugin-activation.php';

		$this->loader = new Applyonline_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Applyonline_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Applyonline_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Applyonline_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                $this->loader->add_action('aol_schedule_event', $plugin_admin, 'close_ad');
                
                /*Schedule Ad*/
                $this->loader->add_filter('display_post_states', $plugin_admin, 'add_closed_state', 10, 2);
                $this->loader->add_action('post_submitbox_misc_actions', $plugin_admin, 'aol_ad_closing', 1);
                $this->loader->add_action( 'save_post', $plugin_admin, 'save_ad_closing' );
                
                /*Admin Notice*/
                $this->loader->add_action('admin_notices', $plugin_admin, 'settings_notice');
                $this->loader->add_action('wp_ajax_aol_dismiss_notice', $plugin_admin, 'admin_dismiss_notice');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Applyonline_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 1 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                
                /*Schedule Ad*/
                $this->loader->add_action('pre_get_posts', $plugin_public, 'check_ad_closing_status');
	}

        /**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Applyonline_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
        
    function applyonline_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array();
        $plugins = apply_filters('aol_wp_required_plugins', $plugins);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'apply-online',                 // Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',                      // Default absolute path to bundled plugins.
		'menu'         => 'applyonline-plugins', // Menu slug.
		'parent_slug'  => 'plugins.php',            // Parent menu slug.
		'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
		'has_notices'  => true,                    // Show admin notices or not.
		'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => TRUE,                   // Automatically activate plugins after installation or not.
		'message'      => '',                      // Message to output right before the plugins table.
                'strings'      => array(
                    'menu_title'                      => __( 'Apply Online Plugins', 'apply-online' ),
                )
	);

	tgmpa( $plugins, $config );
    }
        
        function after_plugin_update(){
            require_once plugin_dir_path( __FILE__ ).'class-applyonline-activator.php';
            $saved_version = (float)get_option('aol_version', 0);
            if($saved_version < 1.6) {
                Applyonline_Activator::bug_fix_before_16();
            }
            
            if($saved_version < 1.61){
                Applyonline_Activator::fix_roles();
                update_option('aol_version', $this->get_version(), TRUE);                
            }
        }
        
        
        function load_dashicons_front_end() {
          wp_enqueue_style( 'dashicons' );
        }

        function my_views($views){
            unset($views['mine']); unset($views['publish']); 
            $statuses = aol_app_statuses();
            foreach ($statuses as $key => $status){
                (isset($_GET['aol_application_status']) AND $_GET['aol_application_status'] == $key)? $class = 'current' : $class = NULL;
                $views[$status] = '<a class="'.$class.'" href="'.  admin_url("edit.php?post_type=aol_application&aol_application_status=$key").'">'.$status.'</a>';        
            }
            return $views;
        }

        public function cpt_generator($cpt, $singular, $plural, $description, $args_custom = array()){
            if($singular != NULL){
            $labels=array(
                'name'  => $plural,
                'singular_name'  => __($singular, 'apply-online' ),
                'add_new_item'       => __('Add New '.$singular, 'apply-online' ),
		'new_item'           => __( 'New '.$singular, 'apply-online' ),
		'edit_item'          => __( 'Edit '.$singular, 'apply-online' ),
		'view_item'          => __( 'View '.$singular, 'apply-online' ),
                'search_items'      => __('Search '.$plural, 'apply-online'),
                );
            }

            $args=array(
                'labels'=> $labels,
                'public'=>  true,
                'show_in_nav_menus' => false,
                'capability_type'   => array('ad', 'ads'),
                'map_meta_cap'      => TRUE,
                'has_archive'   => true,
                'menu_icon'  => 'dashicons-admin-site',
                'show_in_menu'  => 'edit.php?post_type=aol_ad',
                'description' => $description,
                'rewrite'       => array('slug' => sanitize_key($plural)),
                'supports' => array('editor', 'excerpt', 'title', 'thumbnail', 'revisions', 'author'),
            );
            register_post_type('aol_'.sanitize_key($cpt), array_merge($args, $args_custom));
        }
        
        public function taxonomy_generator($singular, $plural,  $hierarchical = TRUE){
            // Add new taxonomy, make it hierarchical (like categories)
            $labels = array(
                'name'              => __( $plural, 'apply-online' ),
                'singular_name'     => __( $singular, 'apply-online' ),
                'search_items'      => sprintf(__( 'Search %s', 'apply-online' ), $plural),
                'all_items'         => sprintf(__( 'All %s', 'apply-online' ), $plural),
                'parent_item'       => sprintf(__( 'Parent %s', 'apply-online' ), $singular),
                'parent_item_colon' => sprintf(__( 'Parent %s:', 'apply-online' ), $singular),
                'edit_item'         => sprintf(__( 'Edit %s', 'apply-online' ), $singular),
                'update_item'       => sprintf(__( 'Update %s', 'apply-online' ), $singular),
                'add_new_item'      => sprintf(__( 'Add New %s', 'apply-online' ), $singular),
                'new_item_name'     => sprintf(__( 'New %s Name', 'apply-online' ), $singular),
            );
            
            $capabilities = array(
		'manage_terms'               => 'manage_ad_terms',
		'edit_terms'                 => 'edit_ad_terms',
		'delete_terms'               => 'delete_ad_terms',
		'assign_terms'               => 'assign_ad_terms',
                );

            $args = array(
                    'hierarchical'      => $hierarchical,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                    'show_in_menu'      => false,
                    'rewrite'           => array( 'slug' => sanitize_key('ad-'.$singular) ),
                    'capabilities'      => $capabilities,
            );
            $cpts = get_option_fixed('aol_ad_types', array());
            $types = array();
            if(!is_array($types)) $types = array();
            foreach ($cpts as $cpt => $val){
                if(isset($val['filters']) AND in_array(sanitize_key($singular), (array)$val['filters'])) $types[] = 'aol_'.$cpt;
            }
            register_taxonomy( 'aol_ad_'.sanitize_key($singular), $types, $args );
        }


        /*
         * @todo make label of the CPT editable from plugin settings so user can show his own title on the archive page
         */
        public function register_aol_post_types(){
            $slug = get_option_fixed('aol_slug', 'ads');
            /*Register Main Post Type*/
            $labels=array(
                'add_new'  => __('Create Ad', 'apply-online' ),
                'add_new_item'  => __('New Ad', 'apply-online' ),
                'edit_item'  => __('Edit Ad', 'apply-online' ),
                'all_items' => __('Ads', 'apply-online' ),
                //'menu_name' => __('Apply Online', 'apply-online' )
            );
            $args=array(
                'label' => __( 'All Ads', 'apply-online' ),
                'labels'=> $labels,
                'show_in_menu'  => true,
                'description' => __( 'All Ads' ),
                'rewrite' => array('slug'=>  $slug),
                'menu_position' => 30,
            );
            //register_post_type('aol_ad',$args);
            $this->cpt_generator('ad', 'Ad', 'Ads', 'All Ads', $args);
            $types = get_option_fixed('aol_ad_types', array());
            unset($types['ad']); //Already reigstered couple of lines before. 
            if(!empty($types)){
                foreach($types as $cpt => $type){
                    $this->cpt_generator($cpt, $type['singular'], $type['plural'], $type['description']);
                }
            }
            
            $filters = aol_ad_filters();
            foreach($filters as $key => $val){
                $this->taxonomy_generator($key, $val);
            }
            
            /*Register Applications Post Type*/
            $lables= array(
                'edit_item'=>'Application',
                'not_found' => __( 'No applications found.', 'apply-online' ),
                'not_found_in_trash'  => __( 'No applications found.', 'apply-online' )
                );
            $args=array(
                'label' => __( 'Applications', 'apply-online' ),
                'labels' => $lables,
                'show_ui'           => true,
                'public'   => false,
                'exclude_from_search'=> true,
                'capability_type'   => array('application', 'applications'),
                'description' => __( 'List of Applications', 'apply-online' ),
                'supports' => array('comments', 'editor'),
                'map_meta_cap'      => TRUE,
                'show_in_menu'      => 'aol-settings',
        );
            register_post_type('aol_application',$args);
            
            //Application tags
            $labels = array(
                'name' => _x( 'Application Status', 'apply-online' ), 
                'singular_name' => 'Status',
                );
            $args = array(
                    'label' =>          'Status',
                    'hierarchical'      => false,
                    'labels'            => $labels,
                    'show_ui'           => false,
                    'show_admin_column' => false,
                    'query_var'         => true,
                    'show_in_menu'      => false,
                    'show_in_nav_menus' => false,
            );
            register_taxonomy( 'aol_application_status', 'aol_application', $args );
        }
}

/**
  * This class is responsible to hanld Ajax data.
  * 
  * 
  * @since      1.0
  * @package    AjaxHandler
  * @author     Farhan Noor
  **/
 class Applyonline_AjaxHandler{
     
     /*
      * Upload meta, after a successfull file upload.
      */   
     var $uploads;
        
        public function __construct() {
            add_action( 'wp_ajax_aol_app_form', array($this, 'aol_process_app_form') );
            add_action( 'wp_ajax_nopriv_aol_app_form', array($this, 'aol_process_app_form') );
            add_action( 'aol_form_errors', array($this, 'file_uploader'), 10, 10 ); //Call file uploader when form is being processed.
        }
        
        function upload_folder($uploads){
                $dir = apply_filters('aol_upload_folder', 'applyonline');
                $uploads['path'] = WP_CONTENT_DIR . '/uploads/' . $dir;
                $uploads['url'] = WP_CONTENT_URL . '/uploads/' . $dir;
                $uploads['subdir'] = '/' . $dir;
                return $uploads;
        }

        function file_uploader($errors, $post, $files){
            if(empty($files)) return $errors; //If no files are being uploaded, just quit.
            
            $upload_size = get_option_fixed('aol_upload_max_size', 1);
            $max_upload_size = $upload_size*1048576; //Multiply by KBs

            $file_types = get_option_fixed('aol_allowed_file_types', 'jpg,jpeg,png,doc,docx,pdf,rtf,odt,txt');

            $upload_overrides = array( 'test_form' => false );

            /*Initialixing Variables*/
            //$errors = new WP_Error();
            $error_assignment = null;
            
            $uploads = array();
            $user = get_userdata(get_current_user_id());
        
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            
            foreach($files as $key => $val):
                if(empty($val['name'])) continue;
                
                if($max_upload_size < $val['size']){
                        $errors->add('max_size', sprintf(__( '%s is oversized. Must be under %s MB', 'apply-online' ), $val['name'] , $upload_size));
                }

                /* Check File Size */
                $file_type_match = 0;
                $filetype = wp_check_filetype (  $val['name'] );
                $file_ext = strtolower($filetype['ext']);
                if(strstr($file_types, $file_ext) == FALSE) $errors->add('file_type', sprintf(__( 'Invalid file %s. Allowed file types are %s', 'apply-online' ), $val['name'], $file_types));
                $errors = apply_filters('aol_before_file_upload_errors', $errors);
                if(empty($errors->errors)){
                    do_action('aol_before_file_upload', $key, $val, $post);
                    add_filter('upload_dir', array($this, 'upload_folder')); //Change upload path.
                    $movefile = wp_handle_upload( $val, $upload_overrides ); 
                    if ( $movefile && ! isset( $movefile['error'] ) ) {
                        $uploads[$key] = $movefile;
                        $uploads[$key]['name'] = $val['name'];
                        //update_user_meta(get_current_user_id(), $key, $movefile['url'] );
                    } else {
                        /**
                         * Error generated by _wp_handle_upload()
                         * @see _wp_handle_upload() in wp-admin/includes/file.php
                         */
                         $errors->add('file_move', $val['name'].': '.$movefile['error']);
                    }
                }
            endforeach;
            //return array('errors' => $errors, 'uploads' => $uploads);
            $this->uploads = $uploads;
            return $errors;
        }
                        
        public function aol_process_app_form(){
            $nonce=$_POST['wp_nonce'];
            if(!wp_verify_nonce($nonce, 'the_best_aol_ad_security_nonce')){
                header( "Content-Type: application/json" );
                echo json_encode( array( 'success' => false, 'error' => __( 'Session Expired, please try again', 'apply-online' ) ));
                exit;
            }

            /*Initializing Variables*/
            $errors = new WP_Error();
            $error_assignment = null;
            
            //Check for required fields
            $form = apply_filters('aol_pre_form_validation', get_post_meta($_POST['ad_id']), $_POST, $_FILES); //Get parent ad value for which the application is being submitted.
            $app_field = array();
            foreach($form as $key => $val){
                if(substr($key, 0, 9) == '_aol_app_'){
                    
                    $app_field = apply_filters('aol_pre_form_field_validation', unserialize($val[0]), $key, $val, $_POST);
                    if(in_array($app_field['type'], array('separator', 'seprator', 'paragraph'))) continue; //Excludes seprator & paragraph from validation & verification
                    //eMail validation
                    if($app_field['type'] == 'email'){
                        if(!empty($_POST[$key]) and is_email($_POST[$key])==FALSE) $errors->add('email', str_replace('_',' ', substr($key, 9)). __(' is invalid.', 'apply-online'));
                    }
                    //File validation & verification.
                    if($app_field['type'] == 'file'){
                        if(!isset($_FILES[$key]['name'])) $errors->add('file', str_replace('_',' ', substr($key, 9)).__(' is not a file.', 'apply-online'));
                        if((int)$app_field['required'] == 1 and empty($_FILES[$key]['name'])) $errors->add('required', str_replace('_',' ', substr($key, 9)).__(' is required.', 'apply-online'));
                    }
                    
                    //chek required fields for non File Fields
                    if((int)$app_field['required'] == 1 and $app_field['type'] != 'file'){
                        $_POST[$key] = is_array($_POST[$key]) ? array_map(sanitize_text_field, $_POST[$key]) : sanitize_textarea_field($_POST[$key]);
                        if(empty($_POST[$key])) $errors->add('required', str_replace('_',' ', substr($key, 9)).__(' is required.', 'apply-online'));
                    }
                }
            }
            
            $errors = apply_filters('aol_form_errors', $errors, $_POST, $_FILES); //You can hook 3rd party (i.e. add-ons) form errors here.
            $error_messages = $errors->get_error_messages();
            //$error_messages = array_merge($error_messages, $upload_error_messages);
            
            if(!empty($error_messages )){
                $error_html = implode('<br />', $error_messages);
                $response = json_encode( array( 'success' => false, 'error' => $error_html ));    // generate the response.
                
                // response output
                header( "Content-Type: application/json" );
                die($response);
                exit;
            } 
            //End - Check for required fields            
            foreach($this->uploads as $name => $file){
                $_POST[$name] = $file;
            }

            $args=  array(
                'post_type'     =>'aol_application',
                'post_content'  =>'',
                'post_parent'   => $_POST['ad_id'],
                'post_title'    =>get_the_title($_POST['ad_id']),
                'post_status'   =>'publish',
                'tax_input'     => array('aol_application_status' => 'pending'),
                'meta_input'    => NULL,
            );
            do_action('aol_before_app_save', $_POST);
            $args = apply_filters('aol_insert_app_data', $args, $_POST);
            $pid = wp_insert_post($args);

            if($pid>0){
                foreach($_POST as $key => $val):
                    if(substr($key,0,9) == '_aol_app_'){
                        $val = is_array($val) ? array_map('wp_normalize_path', $val) : sanitize_textarea_field($val);
                        update_post_meta($pid, $key, $val);
                        $args['meta_input'][$key] = $val;
                    }
                endforeach;
                $post = get_post($_POST['ad_id']);
                update_post_meta($pid, 'aol_ad_id', $post->ID);
                update_post_meta($pid, 'aol_ad_author', $post->post_author);
                
                wp_set_post_terms( $pid, 'pending', 'aol_application_status' );

                do_action('aol_after_app_save', $pid, $_POST);
                
                //Email notification
                if( $args['post_status'] != 'draft') $this->application_email_notification($pid, $args, $this->uploads);

                $divert_page = get_option('aol_thankyou_page');

                empty($divert_page) ? $divert_link = null :  $divert_link = get_page_link($divert_page);
                $message = __('Form has been submitted successfully. If required, we will get back to you shortly.', 'apply-online');
                $response = array( 'success' => true, 'divert' => $divert_link, 'hide_form'=>TRUE , 'message'=>$message );    // generate the response.
            }

            else $response = array( 'success' => false );    // generate the response.

            $response = apply_filters('aol_form_submit_response', $response, $_POST);

            // response output
            header( "Content-Type: application/json" );
            echo json_encode($response);

            exit;
        }
        
        function application_email_notification($post_id, $post, $uploads){
            $post = (object)$post;
            //send email alert.
            $post_url = admin_url("post.php?post=$post_id&action=edit");

            $admin_email = get_option('admin_email');
            $emails_raw = get_option('aol_recipients_emails', $admin_email);
            $emails = explode("\n", $emails_raw);
            
            // Get the site domain and get rid of www.
            $sitename = strtolower( $_SERVER['SERVER_NAME'] );
            if ( substr( $sitename, 0, 4 ) == 'www.' ) {
                $sitename = substr( $sitename, 4 );
            }
            $from_email = 'do-not-reply@' . $sitename;

            $subject = "New application for $post->post_title";
            $headers = array('Content-Type: text/html; charset=UTF-8', "From: ". get_bloginfo('name')." <$from_email>");
            $attachments = array();

            //@todo need a filter hook to modify content of this email message and to add a from field in the message.
            $message = "<p>Hi,</p>"
                    . '<p>You have received an application for <b>'.$post->post_title.'</b> on <a href="'.  site_url().'" >'.get_bloginfo('name').'</a>.</p>'
                    . "<p><b><a href='".$post_url."'>Click Here</a></b> to access this application</p>"
                    . '<p>----<br />This is an automated response from Apply Online plugin on <a href="'.  site_url().'" >'.site_url().'</a></p>';

            $message = apply_filters('aol_email_notification', $message, $post_id); //Deprecated.

            $aol_email = apply_filters(
                        'aol_email', 
                        array('to' => $emails, 'subject' => $subject, 'message' => nl2br($message), 'headers' => $headers, 'attachments' => $attachments), 
                        $post_id, 
                        $post, 
                        $uploads
                    );

            do_action('aol_email_before', array('to' => $emails, 'subject' => $subject, 'message' => nl2br($message), 'headers' => $headers, 'attachments' => $attachments), $post_id, $post, $uploads);

            add_filter( 'wp_mail_content_type', 'aol_email_content_type' );

            wp_mail( $aol_email['to'], $aol_email['subject'], $aol_email['message'], $aol_email['headers'], $aol_email['attachments']);
            
            remove_filter( 'wp_mail_content_type', 'aol_email_content_type' );
            
            do_action('aol_email_after', $emails, $subject, nl2br($message), $headers, $attachments);
            
            return true;
        }
        
        private function sanitize_post_array(&$value,$key){
            $value = sanitize_text_field($value);
        }
        
        public function save_setting_template(){
            // Check the user's permissions.

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;

            } else {

                    if ( ! current_user_can( 'edit_post', $post_id ) ) {
                            return;
                    }
            }

            /* OK, it's safe for us to save the data now. */

            //Delete fields.
            $old_keys = "SELECT $wpdb->options WHERE option_name like '_aol_app_%'";
            $new_keys = array_keys($_POST);
            $removed_keys = array_diff($old_keys, $new_keys); //List of removed meta keys.
            foreach($removed_keys as $key => $val):
                if(substr($val, 0, 3) == '_ad') delete_post_meta($post_id, $val); //Remove meta from the db.
            endforeach;

            array_walk($_POST[$key], array($this, 'sanitize_post_array')); //Sanitizing each element of the array            
            // Add new value.
            foreach ($_POST as $key => $val):
                // Make sure that it is set.
                if ( substr($key, 0, 13)=='_aol_feature_' and isset( $val ) ) {
                    //Sanitize user input.
                    update_post_meta( $post_id, sanitize_key($key),  sanitize_text_field( $val )); // Add new value.
                }

                // Make sure that it is set.
                elseif ( substr($key, 0, 9)=='_aol_app_' and isset( $val ) ) {
                    $my_data = serialize($val); 
                    update_post_meta( $post_id, sanitize_key($key),  $my_data); // Add new value.
                }
                    //Update the meta field in the database.
            endforeach;
        }
}

class Applyonline_labels{
    public function __construct() {
        add_filter('gettext', array($this, 'translations'), 3, 3);
        add_filter('gettext_with_context', array($this, 'gettext_with_context'), 3, 4);
    }
    
    function translations( $translated_text, $text, $domain ) {
        //Stop if not applyOnlin text domain.
        if($domain != 'apply-online') return $translated_text;
        
            switch ( $text ) {
                
                case 'Fields with (*)  are compulsory.' :
                    $translated_text = get_option('aol_required_fields_notice', 'Fields with (*)  are compulsory.');
                    break;
                case 'Form has been submitted successfully. If required, we will get back to you shortly.' :
                    $translated_text = get_option('aol_application_message', 'Form has been submitted successfully. If required, we will get back to you shortly.');
                    break;
                case 'Submit' :
                    $translated_text = get_option('aol_application_submit_button', 'Submit');
                    break;
                case 'Read More' :
                    $translated_text = get_option('aol_shortcode_readmore', 'Read More');
                    break;
            }
        return $translated_text;
    }
    
    /**
    * @param string $translated
    * @param string $text
    * @param string $context
    * @param string $domain
    * @return string
    */
    function gettext_with_context( $translated, $text, $context, $domain ) {
        //Stop if not applyOnlin text domain.
        if($domain != 'apply-online') return $translated;
        
        if($context == 'public' AND $text == 'Apply Online'){
            $translated = get_option('aol_form_heading', 'Apply Online');
        }

        return $translated;
    }
}