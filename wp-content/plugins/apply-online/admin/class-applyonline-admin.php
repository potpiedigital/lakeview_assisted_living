<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpreloaded.com/farhan-noor
 * @since      1.0.0
 *
 * @package    Applyonline
 * @subpackage Applyonline/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Applyonline
 * @subpackage Applyonline/admin
 * @author     Farhan Noor <profiles.wordpress.org/farhannoor>
 */
class Applyonline_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	protected $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
                
                // Hook - Applicant Listing - Column Name
                add_filter( 'manage_edit-aol_application_columns', array ( $this, 'applicants_list_columns' ) );

                // Hook - Applicant Listing - Column Value
                add_action( 'manage_aol_application_posts_custom_column', array ( $this, 'applicants_list_columns_value' ), 10, 2 ); 
                
                //Fix comments on application
                add_filter('comment_row_actions', array($this, 'comments_fix'), 10, 2);
                
                add_filter('post_row_actions',array($this, 'aol_post_row_actions'), 10, 2);
                
                //Filter Aplications based on parent.
                add_action( 'pre_get_posts', array($this, 'applications_filter') );
                
                // Add Application data to the Application editor. 
                add_action ( 'edit_form_after_title', array ( $this, 'aol_application_post_editor' ) );  
                
                //Application Print
                add_action('init', array($this, 'application_print'));

                add_filter( 'post_date_column_status', array($this, 'application_date_column'), 10, 2);
                
                $this->hooks_to_search_in_post_metas();
                                
                new Applyonline_MetaBoxes();
                
                new Applyonline_Settings($version);
                
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Applyonline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Applyonline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/applyonline-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Applyonline_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Applyonline_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
            
                $localize['app_submission_message'] = 'Form has been submitted successfully. If required, we will get back to you shortly.'; 
                $localize['app_closed_alert'] = 'We are no longer accepting applications for this ad.'; 
                $localize['aol_required_fields_notice'] = 'Fields with (*)  are compulsory.';
                $localize['admin_url'] = admin_url();
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/applyonline-admin.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, false );
                wp_enqueue_script($this->plugin_name.'_md5', plugin_dir_url(__FILE__).'js/md5.min.js', array( 'jquery' ), $this->version, false);                
                wp_localize_script( $this->plugin_name, 'aol_admin', $localize );
	}
        
        public function settings_notice(){
            $notices = get_option('aol_dismissed_notices', array());
            if(in_array('aol', $notices) OR !current_user_can('manage_options')) return;
            ?>
                <div class="notice notice-warning is-dismissible aol">
                    <p><?php echo sprintf(__( "%sApply Online%s - It's good to %scheck things%s before a long drive.", 'apply-online' ), '<strong>', '</strong>', '<a href="'.  get_admin_url().'?page=aol-settings">', '</a>'); ?></p>
                </div>
            <?php
        }
        
        public function admin_dismiss_notice(){
            $notices = get_option('aol_dismissed_notices', array());
            $notices[] = 'aol';
            update_option('aol_dismissed_notices', $notices);
        }
        
    public function add_closed_state($post_states, $post){
        $timestamp = (int)get_post_meta($post->ID, '_aol_ad_closing_date', true);
        if($timestamp != null and $timestamp < time()){
            $post_states['ad_closed'] = __( 'Closed' );
        }
        return $post_states;
    }
    
    public function aol_ad_closing($post){
        $types = get_aol_ad_types();
        if( !in_array($post->post_type, $types)) return;
        
        $date = $closed_class = NULL;
        $close_type = get_post_meta($post->ID, '_aol_ad_close_type', true);
        $close_ad = ($close_type == 'ad' or empty($close_type)) ? 'checked': NULL;
        $close_form = ($close_type == 'form') ? 'checked': NULL;
        $timestamp = (int)get_post_meta($post->ID, '_aol_ad_closing_date', true);
        if(is_int($timestamp) and $timestamp != null){
            $date = date('j-m-Y' ,sanitize_text_field($timestamp));
            $closed_class  =  ($timestamp < time()) ? 'closed' : null;
        }
        ob_start(); ?>
    <div class="aol-ad-closing">
        <?php do_action('aol_ad_close_before', $post); ?>
        <div class="misc-pub-section curtime misc-pub-curtime">
            <span id="ad-closing">
            <strong><?php echo __('Expires on', 'apply-online'); ?></strong>
            </span>
            <input type="text" placeholder="<?php _e('Date', 'apply-online'); ?>" name="_aol_ad_closing_date" class="datepicker <?php echo $closed_class; ?>" value="<?php echo $date; ?>" />
            <p><i><?php _e('Leave empty to never close this ad.', 'apply-online') ?></i></p>
            <p><b>Format:</b><i> dd-mm-yyyy</i><br/><b>Example:</b> <i><?php echo current_time('j-m-Y'); ?></i><br/></p>
            <p><b>When Expires:</b><br /><input type="radio" id="hide_ad" name="_aol_ad_close_type" value="ad" <?php echo $close_ad; ?> /><label for="hide_ad">Hide Ad</label>  &nbsp; &nbsp; <input type="radio" id="hide_form" name="_aol_ad_close_type" value="form" <?php echo $close_form; ?> /><label for="hide_form">Hide Form</label></p>
        </div>
    </div>
        <?php 
        echo ob_get_clean();
    }
        
    public function save_ad_closing($post_id){
        /*
             * We need to verify this came from our screen and with proper authorization,
             * because the save_post action can be triggered at other times.
             */

            // Check if our nonce is set.
            if ( ! isset( $_POST['adpost_meta_box_nonce'] ) ) {
                    return;
            }

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST['adpost_meta_box_nonce'], 'myplugin_adpost_meta_awesome_box' ) ) {
                    return;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                    return;
            }
            
            if ( isset($_POST['_aol_ad_closing_date']) ) {
                if(empty($_POST['_aol_ad_closing_date'])){ 
                    update_post_meta( $post_id, '_aol_ad_closing_date', null); // Add new value. 
                }
                else{
                    $timestamp = strtotime($_POST['_aol_ad_closing_date']);
                    //$timestamp = $date->getTimestamp();
                    update_post_meta( $post_id, '_aol_ad_closing_date', $timestamp); //Add new value.
                }
            }
            update_post_meta( $post_id, '_aol_ad_close_type', $_POST['_aol_ad_close_type']); //Add new value.
    }
        
        function application_date_column($status, $post ){
            if($post->post_type == 'aol_application') $status = 'Received';
            return $status;
        }        
        
        /**
        * Extend WordPress search to include custom fields
        * Join posts and postmeta tables
        *
        * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
         * 
         * @since 1.6
        */
        function hooks_to_search_in_post_metas(){
           add_filter('posts_join', array($this, 'cf_search_join' ));
           add_filter( 'posts_where', array($this, 'cf_search_where' ));
           add_filter( 'posts_distinct', array($this, 'cf_search_distinct' ));
        }
        
       function cf_search_join( $join ) {
           global $wpdb;

           if ( is_search() and is_admin() ) {    
               $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
           }

           return $join;
       }

       /**
        * Modify the search query with posts_where
        *
        * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
        * 
        * @since 1.6
        */
       function cf_search_where( $where ) {
           global $wpdb;

           if ( is_search() and is_admin() ) {
               $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
           }
           return $where;
       }

       /**
        * Prevent duplicates
        *
        * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
        * 
        * @since 1.6
        */
       function cf_search_distinct( $where ) {
           global $wpdb;

           if ( is_search() and is_admin() ) {
           return "DISTINCT";
       }

           return $where;
       }
        
       /**
        * 
        */
       public function comments_fix($actions, $comment){
            $post_id = $comment->comment_post_ID;
            if(get_post_field('post_type', $post_id) == 'aol_application'){
                $author = get_user_by('email', $comment->comment_author_email );
                if(get_current_user_id() != $author->ID) unset($actions['quickedit']); //if not comment author, dont show the quick edit
                unset($actions['unapprove']);
                unset($actions['trash']);
                unset($actions['edit']);
            }
            return $actions;                
        }
        
        /**
         * Applicant Listing - Column Name
         *
         * @param   array   $columns
         * @access  public
         * @return  array
         */
        public function applicants_list_columns( $columns ){
            $columns = array (
                'cb'       => '<input type="checkbox" />',
                'title'    => __( 'Ad Title', 'apply-online' ),
                'applicant'=> __( 'Applicant', 'apply-online' ),
                'taxonomy' => __( 'Tags', 'apply-online' ),
                'date'     => __( 'Date', 'apply-online' ),
            );
            return $columns;
        }
        
        /**
        * Case in-sensitive array_search() with partial matches
        *
        * @param string $needle   The string to search for.
        * @param array  $haystack The array to search in.
        *
        * @author Bran van der Meer <branmovic@gmail.com>
        * @since 29-01-2010
        */
        function array_find($needle, array $haystack)
        { 
            foreach ($haystack as $key => $value) {
                if (stripos($value, $needle) !== FALSE) {
                    return $key;
                }
            }
            return false;
        }

        /**
         * Applicant Listing - Column Value
         *
         * @param   array   $columns
         * @param   int     $post_id
         * @access  public
         * @return  void
         */
        public function applicants_list_columns_value( $column, $post_id ){
            $keys = get_post_custom_keys( $post_id ); $values = get_post_meta($post_id); 
            $new = array();
            foreach($values as $key => $val){
                $new[$key]=$val[0];
            }
            $name = $this->array_find('name', $keys);
            switch ( $column ) {
                case 'applicant' :
                    if($name === FALSE):
                        $applicant_name = 'Undefined';
                    else:
                        $applicant = get_post_meta( $post_id, $keys[ $name ], TRUE );
                        if(is_object($applicant)) $applicant = NULL;
                        elseif(is_array($applicant))    $applicant = implode(',', $applicant);

                        $applicant_name = sprintf( 
                                '<a href="%s">%s</a>', 
                                esc_url( add_query_arg( array ( 'post' => $post_id, 'action' => 'edit' ), 'post.php' ) ), 
                                esc_html( $applicant )
                        );
                    endif;
                    echo $applicant_name; 
                    break;
                case 'taxonomy' :
                    //$parent_id = wp_get_post_parent_id( $post_id ); // get_post_field ( 'post_parent', $post_id );
                    $terms = get_the_terms( $post_id, 'aol_application_status' );
                    if ( ! empty( $terms ) ) {
                        $out = array ();
                        foreach ( $terms as $term ) {
                            $out[] = sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array ( 'post_type' => 'aol_application', 'aol_application_status' => $term->slug ), 'edit.php' ) ), esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'jobpost_category', 'display' ) )
                            );
                        }
                        echo join( ', ', $out );
                    }/* If no terms were found, output a default message. */ else {
                        _e( 'Undefined' , 'apply-online');
                    }
                    break;
            }
        }                
    
        public function aol_post_row_actions($actions, $post){
            $types = get_aol_ad_types();
            if($post->post_type == 'aol_application'){
                $actions = array(); //Empty actions.
                $actions['filters'] = '<a rel="permalink" title="Filter ad" href="'.  admin_url('edit.php?post_type=aol_application').'&ad='.$post->post_parent.'"><span class="dashicons dashicons-filter"></span></a>';
                $actions['ad'] = '<a rel="permalink" title="Edit ad" href="'.  admin_url('post.php?action=edit').'&post='.$post->post_parent.'"><span class="dashicons dashicons-admin-tools"></span></a>';
                $actions['view'] = '<a rel="permalink" title="View ad" target="_blank" href="'.  get_the_permalink($post->post_parent). '"><span class="dashicons dashicons-external"></span></a>';
            }
            elseif( in_array($post->post_type, $types) ){
                $actions['test'] = '<a rel="permalink" title="View All Applications" href="'.  admin_url('edit.php?post_type=aol_application').'&ad='.$post->ID.'">Applications</a>';
            }
            return $actions;
        }
        
        public function applications_filter( $query ) {
            if ( $query->is_main_query() AND is_admin() AND isset($_GET['ad'])) {
                $query->set( 'post_parent', $_GET['ad'] );
            }
        }
        
        public function application_print(){
            if(
                    current_user_can('edit_applications')
                    AND isset($_GET['aol_page'])
                    AND $_GET['aol_page'] == 'print' 
                ){
                $post = get_post($_GET['id']);
                $parent = get_post($post->post_parent);
                ?>
                <!DOCTYPE html>
                <html lang="en-US">
                <head>
                    <meta charset="UTF-8">
                    <title>Application <?php echo $_GET['id']; ?> - Apply online</title>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <meta name="robots" content="noindex,nofollow">

                    <link rel='stylesheet' id='open-sans-css'  href='https://fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=4.7.5' type='text/css' media='all' />
                    <link rel='stylesheet' id='single-style-css'  href='<?php echo plugin_dir_url(__FILE__); ?>css/print.css?ver=<?php echo $this->version; ?>' type='text/css' media='all' />
                </head> 
                <body class="body wpinv print">
                    <div class="row top-bar no-print">
                        <div class="container">
                            <div class="col-xs-6">
                                <a class="btn btn-primary btn-sm" onclick="window.print();" href="javascript:void(0)">Print Application</a>
                            </div>
                        </div>
                    </div>
                    <div class="container wrap">
                        <htmlpageheader name="pdf-header">
                            <div class="row header">
                                <div class="col-md-9 business">
                                    #<?php echo $_GET['id']; ?>
                                    <h3><?php echo $post->post_title; ?></h3>
                                    <?php echo $post->post_date; ?>
                                </div>

                                <div class="col-md-3">
                                    Application
                                    <h3><?php bloginfo('name'); ?></h3>
                                    
                                </div>
                            </div>
                        </htmlpageheader>
                        
                        <table class="table table-sm table-bordered table-responsive">
                            <tbody><?php 
                                        $keys = get_post_custom_keys($_GET['id']); 
                                        foreach ( $keys as $key ):
                                        if ( substr ( $key, 0, 9 ) == '_aol_app_' ) {

                                            $val = get_post_meta ( $post->ID, $key, true ); //Escaping output.
                                            if(is_array($val)) $val = implode(', ', $val);
                                            $val = esc_textarea($val);

                                            if (!filter_var($val, FILTER_VALIDATE_URL) === false) $val = '<a href="'.$val.'" target="_blank">Attachment</a>';

                                            echo '<tr><td>' . str_replace ( '_', ' ', substr ( $key, 9 ) ) . '</td><td>' . $val . '</td></tr>';
                                        }
                                    endforeach;
                                    ?>
                            </tbody>
                        </table>
                        <htmlpagefooter name="wpinv-pdf-footer">
                            <div class="row wpinv-footer">
                                <div class="col-sm-12">
                                    <div class="footer-text"><a target="_blank" href="<?php bloginfo('url') ?>"><?php bloginfo('url'); ?></a></div>
                                </div>
                            </div>
                    </htmlpagefooter>
                    </div>
                </body>
                </html>
            <?php 
            exit();
            }
        }
        
        /**
         * Creates Detail Page for Applicants
         * 
         * 
         * @access  public
         * @since   1.0.0
         * @return  void
         */
        public function aol_application_post_editor (){
            global $post;
            if ( !empty ( $post ) and $post->post_type =='aol_application' ):
                $keys = get_post_custom_keys ( $post->ID );
                ?>
                <div class="wrap"><div id="icon-tools" class="icon32"></div>
                    <h3><?php the_title(); ?></h3>
                    <h3>
                        <?php 
                        // _aol_attachment feature has obsolete since version 1.4, It is now treated as Post Meta.
                        if ( in_array ( '_aol_attachment', $keys ) ):
                            $files = get_post_meta ( $post->ID, '_aol_attachment', true );
                            ?>
                        &nbsp; &nbsp; <small><a href="<?php echo esc_url(get_post_meta ( $post->ID, '_aol_attachment', true )); ?>" target="_blank" ><?php echo __( 'Attachment' , 'apply-online' );?></a></small>
                        <?php endif; ?>

                    </h3>
                    <table class="widefat striped">
                        <?php
                        foreach ( $keys as $key ):
                            if ( substr ( $key, 0, 9 ) == '_aol_app_' ) {

                                $val = get_post_meta ( $post->ID, $key, true );
                                //Support to previuos versions where only URL was saved in the post meta.
                                if (!filter_var($val, FILTER_VALIDATE_URL) === false) $val = '<a href="'.$val.'" target="_blank">View</a> | <a href="'.esc_url ($val).'" download>Download</a>';

                                elseif(is_array($val)){
                                    //If the outputs is file attachment
                                    if(isset($val['url']) AND isset($val['type'])) 
                                        $val = '<a href="'.esc_url ($val['url']).'" target="_blank">View</a> | <a href="'.esc_url ($val['url']).'" download>Download</a>';
                                    
                                    //If output is a radio or checkbox.
                                    else $val = implode(', ', $val);
                                }
                                
                                echo '<tr><td>' . str_replace ( '_', ' ', substr ( $key, 9 ) ) . '</td><td>' . $val . '</td></tr>';
                            }
                        endforeach;
                        ?>
                    </table>
                </div>
                <?php do_action('aol_after_application', $post); ?>
                <h3><?php echo __( 'Notes' , 'apply-online' );?></h3>
                <?php
            endif;
        }        
    }

  /**
  * This class adds Meta Boxes to the Edit Screen of the Ads.
  * 
  * 
  * @since      1.0
  * @package    MetaBoxes
  * @subpackage MetaBoxes/includes
  * @author     Farhan Noor
  **/
 class Applyonline_MetaBoxes{
     
        /**
	 * Application Form Field Types.
	 *
	 * @since    1.3
	 * @access   public
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
        var $app_field_types;
             
        public function __construct() {
            $this->app_field_types = $this->app_field_types();
            add_action( 'add_meta_boxes', array($this, 'aol_meta_boxes'),1 );
            add_action('admin_menu', array($this, 'remove_submit_metabox'));
            add_action('aol_schedule_event', array($this, 'close_ad')); //keep this cron hook claing at top.
            add_action('post_submitbox_misc_actions', array($this, 'aol_metas'));
            add_action( 'save_post', array( $this, 'save_ad' ) );
            add_action('save_post_aol_application', array($this, 'save_application'));
            add_action('do_meta_boxes', array($this, 'alter_metaboxes_on_application_page'));
            add_action( 'admin_enqueue_scripts', array($this, 'enqueue_date_picker') );
        }
        
        function app_field_types(){
            return array(
                'text'=>'Text Field',
                'number'=>'Number Field',
                'text_area'=>'Text Area',
                'email'=>'E Mail Field',
                'date'=>'Date Field',
                'checkbox'=>'Check Boxes',
                'radio'=> 'Radio Buttons',
                'dropdown'=>'Dropdown Options', 
                'file'=>'Attachment Field',
                //'seprator' => 'Seprator', //Deprecated since 1.9.6. Need to be fixed for older versions.
                'separator' => 'Separator',
                'paragraph' => 'Paragraph',
                );
        }
                
        function save_application($post_id){
            if ( wp_is_post_revision( $post_id ) ) return;

            // Check if this post is in default category
            if ( isset($_POST['aol_tag']) AND !empty($_POST['aol_tag'])) {
                $result = wp_set_post_terms( $post_id, $_POST['aol_tag'], 'aol_application_status' );
                $term = get_term($result[0]);
                do_action('aol_application_status_change', $term, $post_id);
            }
        }
        
        function remove_submit_metabox(){
            remove_meta_box( 'submitdiv', 'aol_application', 'side' );
        }
        
        function close_ad($post_id){
            update_post_meta($post_id, '_aol_closed', 0);
        }
                
        function enqueue_date_picker(){
                wp_enqueue_script( 'jquery-ui-datepicker');
		wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css');
        }
        
        public function alter_metaboxes_on_application_page(){
            remove_meta_box('commentstatusdiv', 'aol_application', 'normal'); //Hide discussion meta box.
        }
        
        /**
	 * Metaboxes for Ads Editor
	 *
	 * @since     1.0
	 */
        function aol_meta_boxes($post) {
            $screens = array('aol_ad');
            $types = get_option_fixed('aol_ad_types');
            if(is_array($types)){
                foreach ($types as $type){
                    $screens[] = 'aol_'.strtolower($type['singular']);
                }
            }
            if(empty($screens) or !is_array($screens)) $screens = array();

            add_meta_box(
                'aol_ad_metas',
                __( 'Ad Features', 'apply-online' ),
                array($this, 'ad_features'),
                $screens,
                'advanced',
                'high'
            );

            add_meta_box(
                'aol_ad_app_fields',
                __( 'Application Form Builder', 'apply-online' ),
                array($this, 'application_form_fields'),
                $screens,
                'advanced',
                'high'
            );  
                        
            add_meta_box(
                'aol_application',
                __( 'Application Details', 'apply-online' ),
                array($this, 'application_general'),
                'aol_application',
                'side'
            );
            /*
            add_meta_box(
                'aol_form_builder',
                __( 'New Application Form Builder', 'apply-online' ),
                array($this, 'application_form_builder'),
                $screens,
                'advanced',
                'high'
            );
             * 
             */
        }
        
    function application_form_builder(){
        ?>
        <div class="um-admin-builder profile" data-form_id="1633">
                <div class="um-admin-clear"></div>

                <div class="um-admin-drag">

                        <div class="um-admin-drag-ajax ui-sortable" data-form_id="1633">


                                                <div class="um-admin-drag-row" data-original="_um_row_1">

                                                        <!-- Master Row Actions -->
                                                        <div class="um-admin-drag-row-icons">
                                                                <a href="#" class="um-admin-drag-rowsub-add um-admin-tipsy-n" title="Add Row" data-row_action="add_subrow"><i class="um-icon-plus"></i></a>
                                                                <a href="#" class="um-admin-drag-row-edit um-admin-tipsy-n" title="Edit Row" data-modal="UM_edit_row" data-modal-size="normal" data-dynamic-content="um_admin_edit_field_popup" data-arg1="row" data-arg2="1633" data-arg3="_um_row_1"><i class="um-faicon-pencil"></i></a>
                                                                <span class="um-admin-drag-row-start ui-sortable-handle"><i class="um-icon-arrow-move"></i></span>
                                                                                                                </div><div class="um-admin-clear"></div>

                                                        <div class="um-admin-drag-rowsubs ui-sortable">


                                                                        <div class="um-admin-drag-rowsub ui-sortable">

                                                                                <!-- Column Layout -->
                                                                                <div class="um-admin-drag-ctrls columns">

                                                                                        <a href="#" data-cols="1" class="active"></a><a href="#" data-cols="2" class=""></a><a href="#" data-cols="3" class=""></a>
                                                                                </div>

                                                                                <!-- Sub Row Actions -->
                                                                                <div class="um-admin-drag-rowsub-icons">
                                                                                        <span class="um-admin-drag-rowsub-start"><i class="um-icon-arrow-move"></i></span>
                                                                                                                                                                </div><div class="um-admin-clear"></div>

                                                                                <!-- Columns -->
                                                                                <div class="um-admin-drag-col ui-sortable cols-1 cols-last">


                                                                                                        <div class="um-admin-drag-fld um-admin-delete-area um-field-type-date Test" data-group="" data-key="Test" data-column="2" style="position: relative; left: 0px; top: 0px;">

                                                                                                                <div class="um-admin-drag-fld-title um-field-type-date">
                                                                                                                        Test</div>
                                                                                                                                                                                                                        <div class="um-admin-drag-fld-type um-field-type-date">Date Picker</div>
                                                                                                                <div class="um-admin-drag-fld-icons um-field-type-date">

                                                                                                                        <a href="#" class="um-admin-tipsy-n" title="Edit" data-modal="UM_edit_field" data-modal-size="normal" data-dynamic-content="um_admin_edit_field_popup" data-arg1="date" data-arg2="1633" data-arg3="Test"><i class="um-faicon-pencil"></i></a>

                                                                                                                        <a href="#" class="um-admin-tipsy-n um_admin_duplicate_field" title="Duplicate" data-silent_action="um_admin_duplicate_field" data-arg1="Test" data-arg2="1633"><i class="um-faicon-files-o"></i></a>


                                                                                                                                <a href="#" class="um-admin-tipsy-n" title="Delete" data-silent_action="um_admin_remove_field" data-arg1="Test" data-arg2="1633"><i class="um-faicon-trash-o"></i></a>


                                                                                                                </div><div class="um-admin-clear"></div>


                                                                                                        </div>


                                                                                <a href="#" class="um-admin-drag-add-field um-admin-tipsy-n" title="Add Field" data-modal="UM_fields" data-modal-size="normal" data-dynamic-content="um_admin_show_fields" data-arg2="1633" data-arg1=""><i class="um-icon-plus"></i></a></div>

                                                                                <div class="um-admin-drag-col-dynamic"></div>

                                                                                <div class="um-admin-clear"></div>

                                                                        </div>


                                                        </div>

                                                </div>


                        </div>

                        <div class="um-admin-drag-addrow um-admin-tipsy-n" title="Add Master Row" data-row_action="add_row"><i class="um-icon-plus"></i></div>

                </div>

        </div>        
        <?php
    }
                
    function application_general(){
        global $post;
        $post_terms = get_the_terms( $post->ID, 'aol_application_status');
        $stauses = aol_app_statuses_active();
        ?>
        <div class="submitpost">
            <div class="minor-publishing-actions">
                <p class="post-attributes-label-wrapper"><a href="<?php admin_url(); ?>?aol_page=print&id=<?php echo $post->ID; ?>" class="button button-secondary button-large" target="_blank">Print Application</a></p>
                <?php do_action('aol_app_updatebox_after');  ?>
                <p class="post-attributes-label-wrapper"><label class="post-attributes-label" for="parent_id">Application Status</label></p>
                <select name="aol_tag">
                    <?php
                    foreach($stauses as $key => $val){
                        $selected = ($key == $post_terms[0]->slug ) ? 'selected' : NULL;
                        echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
                    }
                    ?>
                    
                </select>
            </div>
            <div id="major-publishing-actions">
                <div id="delete-action">
                <a class="submitdelete deletion" href="<?php echo get_delete_post_link($post->ID); ?>">Move to Trash</a></div>

                <div id="publishing-action">
                <span class="spinner"></span>
                                <input name="original_publish" id="original_publish" value="Update" type="hidden">
                                <input name="save" class="button button-primary button-large" id="publish" value="Update" type="submit">
                </div>
                <div class="clear"></div>
            </div>  
        </div>
        <?php
    }
        
        /*
         * Generates shortcode and php code for the form.
         */
        function aol_metas($post){
            $types = get_aol_ad_types();
            if(!in_array($post->post_type, $types)) return;
            
            echo '<div class="misc-pub-section aol-meta">';
            echo '<p>Full ad shortcode <input type="text" value="[aol_ad id='.$post->ID.']" readonly></p>';
            echo '<p>Form shortcode <input type="text" value="[aol_form id='.$post->ID.']" readonly></p>';
            echo '<p><a rel="permalink" title="View All Applications" href="'.  admin_url('edit.php?post_type=aol_application').'&ad='.$post->ID.'">View All</a> applications</p>';
            do_action('aol_metabox_after', $post);
            echo '</div>';
        }
        
        function ad_features( $post ) {

            // Add a nonce field so we can check for it later.
                wp_nonce_field( 'myplugin_adpost_meta_awesome_box', 'adpost_meta_box_nonce' );

                /*
                 * Use get_post_meta() to retrieve an existing value
                 * from the database and use the value for the form.
                 */
            ?>
            <div class="ad_features adpost_fields">
                <ol id="ad_features">
                    <?php
                        $keys = get_post_custom_keys( $post->ID);
                        if($keys != NULL):
                            foreach($keys as $key):
                                if(substr($key, 0, 13)=='_aol_feature_'){
                                    $val=get_post_meta($post->ID, $key, TRUE);
                                    echo '<li><label for="'.$key.'">';
                                    _e( str_replace('_',' ',substr($key,13)), 'apply-online' );
                                    echo '</label> ';
                                    echo '<input type="text" id="'.$key.'" name="'.$key.'" value="' . sanitize_text_field( $val ) . '" /> &nbsp; <div class="button removeField">Delete</div></li>';
                                }
                            endforeach;
                        endif;
                    ?>
                </ol>
            </div>
            <div class="clearfix clear"></div>
            <table id="adfeatures_form" class="alignleft">
            <thead>
                <tr>
                    <th class="left"><label for="adfeature_name">Feature</label></th>
                    <th><label for="adfeature_value">Value</label></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="left" id="adFeature">
                        <input type="text" id="adfeature_name" />
                    </td><td>
                        <input type="text" id="adfeature_value" />
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class=""><div class="button" id="addFeature">Add Feature</div></div>
                    </td>
                </tr>
            </tbody>
            </table>
            <div class="clearfix clear"></div>
            <?php 
        }
        
        public function application_fields_generator($app_fields){

            ?>
            <div class="clearfix clear"></div>
            <table id="adapp_form_fields" class="alignleft">
            <thead>
                <tr>
                    <th class="left"><label for="metakeyselect">Field</label></th>
                    <th><label for="metavalue">Type</label></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="left" id="newmetaleft">
                        <input type="text" id="adapp_name" maxlength="245" />
                    </td><td>
                        <select id="adapp_field_type">
                            <?php
                                foreach($app_fields as $key => $val):
                                    echo '<option value="'.$key.'" class="'.$key.'">'.$val.'</option>';
                                endforeach;
                            ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" ><input id="adapp_field_options" class="adapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" ></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class=""><div class="button" id="addField">Add Field</div></div>
                    </td>
                </tr>
            </tbody>
            </table>
            <div class="clearfix clear"></div>
        <?php
        }
        
        public function application_form_fields( $post ) {
            //global $adfields;
            // Add a nonce field so we can check for it later.
            wp_nonce_field( 'myplugin_adpost_meta_awesome_box', 'adpost_meta_box_nonce' );
            do_action('aol_before_form_builder', $post);
            /*
             * Use get_post_meta() to retrieve an existing value
             * from the database and use the value for the form.
             */
            ?>
            <div class="app_form_fields adpost_fields">
                <table>
                <tbody id="app_form_fields">
                    <?php 
                    do_action('aol_before_form_builder', $post);
                    $fields = array();
                    if(isset($_GET['post'])){
                        //Fetch Feilds keys order
                        $fields = get_aol_ad_post_meta($post->ID);
                    }
                    else{
                            $fields = get_option('aol_default_fields', array());
                        }
                            foreach($fields as $key => $val):
                                    $label = isset($val['label']) ? $val['label'] : str_replace('_',' ',substr($key,9)); //
                                    if($val['type']=='seprator') $val['type'] = 'separator'; //Fixed bug before 1.9.6, spell mistake in the key.
                                    if(!isset($val['required'])) $val['required'] = 1;
                                    $req_class = ($val['required'] == 0) ? 'button-disabled': null;
                                    $fields = NULL;
                                    foreach($this->app_field_types as $field_key => $field_val){
                                        if($val['type'] == $field_key) $fields .= '<option value="'.$field_key.'" selected>'.$field_val.'</option>';
                                        else $fields .= '<option value="'.$field_key.'" >'.$field_val.'</option>';
                                    }
                                    $req_class .= ($val['type'] == 'separator' OR $val['type'] == 'paragraph') ? ' button-disabled' : ' toggle-required';
                                    
                                    //if($key.'[type]'=='text'){
                                    echo '<tr data-id="'.$key.'" class="'.$key.'">';
                                        echo '<td><label for="'.$key.'"><span class="dashicons dashicons-menu"></span> '.$label.'</label></td>';
                                        echo '<td>';
                                        echo '<input type="hidden" name="'.$key.'[label]" value="'.($label).'" />';
                                        echo '<input type="hidden" name="'.$key.'[required]" value="'.($val['required']).'" />';
                                        echo '<div class="button-primary button-required '.$req_class.'">Required</div> ';
                                        echo '<select class="adapp_field_type" name="'.$key.'[type]">'.$fields.'</select>';
                                        //if(!($val['type']=='text' or $val['type']=='email' or $val['type']=='date' or $val['type']=='text_area' or $val['type']=='file' )):
                                        if(in_array($val['type'], array('checkbox','dropdown','radio'))):
                                            echo '<input type="text" name="'.$key.'[options]" value="'.$val['options'].'" placeholder="Option1, option2, option3,......" />';
                                        else:
                                            echo '<input type="text" name="'.$key.'[options]" placeholder="Option1, option2, option3" style="display:none;"  />';
                                        endif;
                                        echo ' &nbsp; <div class="button removeField">Delete</div>';
                                        echo '</td>';
                                        do_action('aol_after_form_field', $key);
                                    echo '</tr>';
                                    //}
                            endforeach;
                            do_action('aol_after_form_builder', $post);
                    ?>
                </tbody></table>
            </div>  
            

            <?php
            $this->application_fields_generator($this->app_field_types);
        }     
        
        /**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
        function save_ad( $post_id ) {

            /*
             * We need to verify this came from our screen and with proper authorization,
             * because the save_post action can be triggered at other times.
             */

            // Check if our nonce is set.
            if ( ! isset( $_POST['adpost_meta_box_nonce'] ) ) {
                return;
            }

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST['adpost_meta_box_nonce'], 'myplugin_adpost_meta_awesome_box' ) ) {
                return;
            }

            /* OK, it's safe for us to save the data now. */
            $types = get_aol_ad_types();
            if ( !in_array($_POST['post_type'], $types) ) return;

            //Delete fields.
            $old_keys = (array)get_post_custom_keys($post_id); 
            $new_keys = array_keys($_POST);
            $new_keys = array_map('sanitize_key', $new_keys);
            $removed_keys = array_diff($old_keys, $new_keys); //List of removed meta keys.
            foreach($removed_keys as $key => $val):
                if(substr($val, 0, 13) == '_aol_feature_' OR substr($val, 0, 9) == '_aol_app_'){
                        delete_post_meta($post_id, $val); //Remove meta from the db.
                }
            endforeach;
            //
            $existing_keys = array_diff($old_keys, $removed_keys); //List of removed meta keys. UNUSED
            // Add/update new value.
            $fields_order = array();
            foreach ($_POST as $key => $val):
                // Make sure that it is set.
                if ( substr($key, 0, 13)=='_aol_feature_' and isset( $val ) ) {
                    //Sanitize user input.
                    $my_data = sanitize_text_field( $val );
                    update_post_meta( $post_id, sanitize_key($key),  $my_data); // Add new value.
                }
                // Make sure that it is set.
                elseif ( substr($key, 0, 9) == '_aol_app_' and isset( $val ) ) {
                    //$my_data = serialize($val);
                        if(in_array($val['type'], array('separator', 'seprator', 'paragraph'))) $val['required'] = 0;
                                
                        $val['options'] = explode(',', $val['options']);
                        $val['options'] = implode(',', array_map('trim',$val['options']));
                        /*END - Remove white spaces */
                        
                    update_post_meta( $post_id, sanitize_key($key),  $val); // Add new value.
                    $fields_order[] = sanitize_key($key);
                }
                // 
            endforeach;
            update_post_meta( $post_id, '_aol_fields_order',  $fields_order); // Add new value.
        }
        
        function ismd5($md5 ='') {
          return strlen($md5) == 32 && ctype_xdigit($md5);
        }

}

class Applyonline_Updater{
    function db_updater(){
        global $wpdb;
        //$rows = $wpdb->get_results( "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_aol_app_%' AND meta_value != ".'%"label"%' );
    }
}

  /**
  * This class contains all nuts and bolts of plugin settings.
  * 
  * 
  * @since      1.3
  * @package    Applyonline_settings
  * @author     Farhan Noor
  **/
class Applyonline_Settings extends Applyonline_MetaBoxes{
    
    private $version;

    public function __construct($version) {
        
        //parent::__construct(); //Acitvating Parent's constructor
        
        $this->version = $version;
        
        //Registering Submenus.
        add_action('admin_menu', array($this, 'sub_menus'));
        
        //Registering Settings.
        add_action( 'admin_init', array($this, 'registers_settings') );
        
        add_filter( 'plugin_row_meta', array($this, 'plugin_row_meta'), 10, 2 );
        
        //Manageing AOL role capabilites.
        add_filter( "option_page_capability_aol_settings_group", 'aol_manager_capability' );
        add_filter( "option_page_capability_aol_ad_template", 'aol_manager_capability' );
        add_filter( "option_page_capability_aol_ads", 'aol_manager_capability' );
        add_filter( "option_page_capability_aol_applications", 'aol_manager_capability' );
    }

    public function plugin_row_meta($links, $file){
        if ( strpos( $file, 'apply-online.php' ) !== false ) {
            $links['settings'] = '<a href="'.  admin_url().'?page=aol-settings">Settings</a>';
	}
	
	return $links;
    }

    public function sub_menus(){
        add_menu_page( __('Settings', 'apply-online'), _x('Apply Online', 'admin', 'apply-online'), 'edit_applications', 'aol-settings', array($this, 'settings_page_callback'), 'dashicons-admin-site',31 );
        add_submenu_page('aol-settings', __('Settings', 'apply-online'), __('Settings', 'apply-online'), 'delete_others_ads', 'aol-settings');
        $filters = aol_ad_filters();
        foreach($filters as $key => $val){
            add_submenu_page( 'aol-settings', '', sprintf(__('%s Filter', 'apply-online'), $val), 'delete_others_ads', "edit-tags.php?taxonomy=aol_ad_".sanitize_key($key)."&post_type=aol_ad", null );            
        }
    }

    function save_settings(){
        if(!current_user_can('edit_applications')) return;
        if ( !empty( $_POST['aol_default_app_fields'] ) && check_admin_referer( 'aol_awesome_pretty_nonce','aol_default_app_fields' ) ) {
            //Remove unnecessary fields
            foreach($_POST as $key => $val){
                //If not an aol meta key, unset it & continue to next iteration.
                if(substr($key, 0, 4) != '_aol'){
                    unset($_POST[$key]);
                    continue;
                }
                
                //Replacing meta key with sanitized one.
                unset($_POST[$key]);
                $_POST[sanitize_key($key)] = $val;
            }
            //Save aol default fields in DB.
            update_option('aol_default_fields', $_POST);
        }
    }
    
    public function settings_page_callback(){
        $this->save_settings();
        $tabs = $tabs = json_decode(json_encode($this->settings_api()), FALSE);
        ob_start();
        ?>
            <div class="wrap aol-settings">
                <h2>
                    <?php echo _x('Apply Online', 'admin', 'apply-online'); ?> 
                    <small class="wp-caption alignright"><i>version <?php echo $this->version; ?></i></small>
                </h2>
                <span class="alignright" style="display: none">
                    <a target="_blank" title="Love" class="aol-heart" href="https://wordpress.org/plugins/apply-online/#reviews"><span class="dashicons dashicons-heart"></span></a> &nbsp;
                    <a target="_blank" title="Support" class="aol-help" href="https://wordpress.org/support/plugin/apply-online/"><span class="dashicons dashicons-format-chat"></span></a> &nbsp;
                    <a target="_blank" title="Stats" class="aol-stats" href="https://wordpress.org/plugins/apply-online/advanced/"><span class="dashicons dashicons-chart-pie"></span></a> &nbsp;
                    <a target="_blank" title="Shop" class="aol-shop" href="http://wpreloaded.com/shop/"><span class="dashicons dashicons-cart"></span></a> &nbsp;
                </span>
                <h2 class="nav-tab-wrapper">
                    <?php 
                        foreach($tabs as $tab){
                            if( isset($tab->capability) AND !current_user_can($tab->capability) ) continue;
                            empty($tab->href) ? $href = null : $href = 'href="'.$tab->href.'" target="_blank"';
                            isset($tab->classes) ? $classes = $tab->classes : $classes = null;
                            echo '<a class="nav-tab '.$classes.'" data-id="'.$tab->id.'" '.$href.'>'.$tab->name.'</a>';
                        }
                    ?>
                </h2>
                <?php 
                    foreach($tabs as $tab){
                        $func = 'tab_'.$tab->id;
                        echo '<div class="tab-data wrap" id="'.$tab->id.'">';
                        if(isset($tab->name)) echo '<h3>'.$tab->name.'</h3>';
                        if(isset($tab->desc)) echo '<p>'.$tab->desc.'</p>';
                        
                        echo isset($tab->output) ? $tab->output : $this->$func(); //Return $output or related method of the same variable name.
                        echo '</div>';
                    }
                ?>
            </div>
            <style>
                h3{margin-bottom: 5px;}
                .nav-tab{cursor: pointer}
                .tab-data{display: none;}
            </style>
        <?php
        return ob_get_flush();
    }           

    public function registers_settings(){
        register_setting( 'aol_settings_group', 'aol_recipients_emails' );
        register_setting( 'aol_settings_group', 'aol_application_message' );
        register_setting( 'aol_settings_group', 'aol_shortcode_readmore' );
        register_setting( 'aol_settings_group', 'aol_application_submit_button' );
        register_setting( 'aol_settings_group', 'aol_required_fields_notice');
        register_setting( 'aol_settings_group', 'aol_thankyou_page' );
        register_setting( 'aol_settings_group', 'aol_form_heading' ); 
        register_setting( 'aol_settings_group', 'aol_slug', 'sanitize_title' ); 
        register_setting( 'aol_settings_group', 'aol_upload_max_size', array('type' => 'integer', 'default' => 1, 'sanitize_callback' => 'intval') );
        register_setting( 'aol_settings_group', 'aol_upload_folder', array('sanitize_callback' => 'sanitize_text_field') );
        register_setting( 'aol_settings_group', 'aol_allowed_file_types', array('sanitize_callback' => 'sanitize_text_field') );
        register_setting( 'aol_ad_template', 'aol_default_fields');
        register_setting( 'aol_ads', 'aol_ad_types', array('sanitize_callback' => 'aol_sanitize_filters') );
        register_setting( 'aol_ads', 'aol_ad_filters', array('sanitize_callback' => 'aol_array_check') );
        register_setting( 'aol_applications', 'aol_app_statuses', array('default' => array()));
        
        //On update of aol_slug field, update permalink too.
        add_action('update_option_aol_slug', array($this, 'refresh_permalink'));
        add_action('update_option_aol_ad_types', array($this, 'refresh_types_permalink'), 10, 3);
    }
    
    public function refresh_permalink(){
        //Re register post type for proper Flush Rules.
        $slug = get_option_fixed('aol_slug', 'ads');
        /*Register Main Post Type*/
        register_post_type('aol_ad', array('has_archive' => true, 'rewrite' => array('slug'=>  $slug)));
        flush_rewrite_rules();
    }
    
    function register_cpts_for_flushing($cpt, $plural){
        $result = register_post_type('aol_'.$cpt, array(
            'has_archive' => true, 
            'public' => true,
            'rewrite' => array('slug' => sanitize_key($plural)),
            ));
    }
    
    function refresh_types_permalink($old, $new, $option){
        wp_cache_delete ( 'alloptions', 'options' );
        foreach($new as $cpt => $val){
            $this->register_cpts_for_flushing($cpt, $val['plural']);
        }
        flush_rewrite_rules();
    }
    
    function settings_api(){
        $tabs = array(
                'general' => array(
                    'id'        => 'general',
                    'name'      => __( 'General' ,'apply-online' ),
                    'desc'      => __( 'General settings of the plugin', 'apply-online' ),
                    'href'      => null,
                    'classes'     => 'nav-tab-active',
                ),
                'template' => array(
                    'id'        => 'template',
                    'name'      => __('Template' ,'apply-online'),
                    'desc'      => __( 'Application form template for new ads.', 'apply-online' ),
                    'href'      => null,
                ),
                'applications' => array(
                    'id'        => 'applications',
                    'name'      => __('Applications' ,'apply-online'),
                    'desc'      => __( 'Add status to each application you have received.', 'apply-online' ),
                    'href'      => null,
                ),
                'types' => array(
                    'id'        => 'types',
                    'name'      => __('Ad Types' ,'apply-online'),
                    'desc'      => __( 'Define different types of ads e.g. Careers, Classes, Memberships. These types will appear under All Ads section.', 'apply-online' ),
                    'href'      => null,
                ),
                /*'labels' => array(
                    'id'        => 'labels',
                    'name'      => __('Labels' ,'apply-online'),
                    'desc'      => __( 'Modify labels on front-end.', 'apply-online' ),
                    'href'      => null,
                ),*/
        );
        $tabs = apply_filters('aol_settings_tabs', $tabs);
        $tabs['faqs'] = array(
                    'id'        => 'faqs',
                    'name'      => __('FAQs' ,'apply-online'),
                    'desc'      => __('Frequently Asked Questions.' ,'apply-online'),
                    'href'      => null,
                );
        $tabs['extend'] = array(
                    'id'        => 'extend',
                    'name'      => __('Extend' ,'apply-online'),
                    'desc'      => __('Extend Plugin' ,'apply-online'),
                    'href'      => 'http://wpreloaded.com/plugins/apply-online',
                    'capability' => 'manage_options'
                );
        return $tabs;
    }
    
    private function wp_pages(){
        $pages = get_pages();
        $pages_arr = array();
        foreach ( $pages as $page ) {
            $pages_arr[$page->ID] = $page->post_title;
        }
        return $pages_arr;
    }
    
    private function tab_general(){
        ?>
            <form action="options.php" method="post" name="">
                <table class="form-table">
                <?php
                    settings_fields( 'aol_settings_group' ); 
                    do_settings_sections( 'aol_settings_group' );
                    
                ?>
                    <tr>
                        <th><label for="aol_recipients_emails"><?php _e('List of e-mails to get application alerts', 'apply-online'); ?></label></th>
                        <td><textarea id="aol_recipients_emails" class="small-text code" name="aol_recipients_emails" cols="50" rows="5"><?php echo sanitize_textarea_field(get_option_fixed('aol_recipients_emails') ); ?></textarea><p class="description">Just one email id in one line.</p></td>
                    </tr>
                    <tr>
                        <th><label for="aol_required_fields_notice"><?php _e('Required form fields notice', 'apply-online'); ?></label></th>
                        <td>
                            <textarea class="small-text code" name="aol_required_fields_notice" cols="50" rows="3" id="aol_required_fields_notice"><?php echo sanitize_text_field( get_option_fixed('aol_required_fields_notice', 'Fields with (*)  are compulsory.') ); ?></textarea>
                            <br />
                            <button class="button" id="aol_required_fields_button"><?php _e('Default Notice', 'apply-online'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="app_closed_alert"><?php _e('Closed Application alert', 'apply-online'); ?></label></th>
                        <td>
                            <textarea id="app_closed_alert" class="small-text code" name="aol_application_close_message" cols="50" rows="3"><?php echo sanitize_text_field( get_option_fixed('aol_application_close_message', 'We are no longer accepting applications for this ad.') ); ?></textarea>
                            <br />
                            <button id="app_closed_alert_button" class="button"><?php _e('Default Alert', 'apply-online'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_application_message"><?php _e('Application submission message', 'apply-online'); ?></label></th>
                        <td>
                            <textarea id="aol_submission_default_message" class="small-text code" name="aol_application_message" cols="50" rows="3"><?php echo sanitize_text_field( get_option_fixed('aol_application_message', 'Form has been submitted successfully. If required, we will get back to you shortly.') ); ?></textarea>
                            <br />
                            <button id="aol_submission_default" class="button"><?php _e('Default Message', 'apply-online'); ?></button>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_form_heading"><?php _e('Application form title', 'apply-online'); ?></label></th>
                        <td>
                            <input type="text" id="aol_form_heading" class="regular-text" name="aol_form_heading" value="<?php echo sanitize_text_field(get_option('aol_form_heading', 'Apply Online')); ?>">
                            <p class="description"><?php _e('Default: Apply Online', 'apply-online'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_application_submit_button"><?php _e('Application form Submit Button', 'apply-online'); ?></label></th>
                        <td>
                            <input type="text" id="aol_form_heading" class="regular-text" name="aol_application_submit_button" value="<?php echo sanitize_text_field(get_option_fixed('aol_application_submit_button', 'Submit')); ?>">
                            <p class="description"><?php _e('Default: Submit', 'apply-online'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_shortcode_readmore"><?php _e('Shortcode archive Read More button', 'apply-online'); ?></label></th>
                        <td>
                            <input type="text" id="aol_form_heading" class="regular-text" name="aol_shortcode_readmore" value="<?php echo sanitize_text_field(get_option_fixed('aol_shortcode_readmore', 'Read More')); ?>">
                            <p class="description"><?php _e('Default: Read More', 'apply-online'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_date_format"><?php _e('Date format for date fields', 'apply-online'); ?></label></th>
                        <td>
                            <p>Update format on Wordpress <a href="<?php echo admin_url('options-general.php#timezone_string'); ?>" target="_blank" />General Settings</a> Page.</p>
                        </td>
                    </tr>                    
                    <tr>
                        <th><label for="thanks-page"><?php _e('Thank you page', 'apply-online'); ?></label></th>
                        <td>
                            <select id="thank-page" name="aol_thankyou_page">
                                <option value=""><?php echo sanitize_text_field( __( 'Select page' ) ); ?></option> 
                                <?php 
                                $selected = get_option('aol_thankyou_page');

                                 $pages = get_pages();
                                 foreach ( $pages as $page ) {
                                     $attr = null;
                                     if($selected == $page->ID) $attr = 'selected';

                                       $option = '<option value="' . $page->ID . '" '.$attr.'>';
                                       $option .= $page->post_title;
                                       $option .= '</option>';
                                       echo $option;
                                 }
                                ?>
                           </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_slug"><?php _e('Ads slug', 'apply-online'); ?></label></th>
                        <td>
                            <input id="aol_slug" type="text" class="regular-text" name="aol_slug" placeholder="ads" value="<?php echo sanitize_text_field(get_option_fixed('aol_slug', 'ads') ); ?>" />
                            <?php $permalink_option = get_option('permalink_structure'); if(empty($permalink_option)): ?>
                                <p>This option doesn't work with Plain permalinks structure. Check <a href="<?php echo admin_url('options-permalink.php')?> ">Permalink Settings</a></p>
                            <?php else: ?>
                                <p class="description"><?php _e('Current permalink is ', 'apply-online'); ?><a href="<?php echo get_post_type_archive_link('aol_ad'); ?>" target="_blank"><?php echo get_post_type_archive_link('aol_ad'); ?></a></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_form_max_file_size"><?php _e('Max file attachment size', 'apply-online'); ?></label></th>
                        <td>
                            <input id="aol_form_max_upload_size" type="number" name="aol_upload_max_size" placeholder="1" value="<?php echo sanitize_text_field( get_option('aol_upload_max_size', 1) ); ?>" />MBs
                            <p class="description"><?php _e('Max limit by server is ', 'apply-online'); ?><?php echo floor(wp_max_upload_size()/1000000); ?> MBs</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="aol_allowed_file_types"><?php _e('Allowed file types', 'apply-online'); ?></label></th>
                        <td>
                            <textarea id="aol_allowed_file_types" name="aol_allowed_file_types" class="code" placeholder="jpg,jpeg,png,doc,docx,pdf,rtf,odt,txt" cols="50" rows="2"><?php echo esc_textarea(get_option_fixed('aol_allowed_file_types', 'jpg,jpeg,png,doc,docx,pdf,rtf,odt,txt')); ?></textarea>
                            <p class="description"><?php _e('Comma separated names of file extentions. Default: ', 'apply-online'); ?>jpg,jpeg,png,doc,docx,pdf,rtf,odt,txt</p>
                        </td>
                    </tr>
                    <?php 
                        //$general = apply_filters('general_settings', $general); 
                    ?>
                </table>
                <?php submit_button(); ?>
            </form>
            <?php 
    }
    
    private function tab_template(){
        ?>
            <form id="template_form" method="post">
            <div class="app_form_fields adpost_fields default_fields">
                <table class="aol_table">
                <tbody id="app_form_fields">
                    <?php 
                        $app_fields = $this->app_field_types();
                        settings_fields( 'aol_ad_template' );
                        do_settings_sections( 'aol_ad_template' );

                        $keys= apply_filters('aol_ad_default_fields', get_option('aol_default_fields'));
                        if($keys != NULL):
                            foreach($keys as $key => $val):
                                echo '<tr class="'.$key.'">';
                                if(substr($key, 0, 9)=='_aol_app_'):
                                    $label = isset($val['label']) ? $val['label'] : str_replace('_',' ',substr($key,9));

                                    $fields = NULL;
                                    foreach($app_fields as $field_key => $field_val){
                                        if($val['type']==$field_key) $fields .= '<option value="'.$field_key.'" selected>'.$field_val.'</option>';
                                        else $fields .= '<option value="'.$field_key.'" >'.$field_val.'</option>';
                                    }
                                        if($val['type']=='checkbox' or $val['type']=='radio' or $val['type']=='dropdown'):
                                            $options = 'value="'.$val['options'].'"';
                                        else:
                                            $options = 'style="display:none;"';
                                        endif;
                                    ?>
                                        <td>
                                            <span class="dashicons dashicons-menu"></span> &nbsp; <label for="<?php echo $key ?>"><?php echo $label; ?><input type="hidden" name="<?php echo $key; ?>[label]" value="<?php echo $label; ?>" /></label>
                                        </td>
                                        <td>
                                            <select class="adapp_field_type" name="<?php echo $key; ?>[type]"><?php echo $fields; ?></select>
                                            <input type="text" name="<?php echo $key; ?>[options]" <?php echo $options; ?> placeholder="Option1, option2, option3" />
                                        </td>
                                        <?php do_action('aol_after_application_template_field', $key); ?>
                                        <td><span class="dashicons dashicons-trash removeField button-trash"></span> </td>
                                    <?php
                                endif;
                                echo '</tr>';
                            endforeach;
                        endif;
                    ?>
                </tbody>
                </table>
            </div>  
            <?php $this->application_fields_generator($this->app_field_types()); ?>
            <hr />
            <?php submit_button(); ?>
            <?php wp_nonce_field( 'aol_awesome_pretty_nonce','aol_default_app_fields' ); ?>
        </form>
        <?php
    }
    
    private function tab_types(){
            $types= aol_ad_types();
        ?>
            <form id="types_form" method="post" action="options.php" >
                <div class="app_form_fields adpost_fields">
                    <div class="aol_table">
                    <ol id="ad_types">
                        <?php 
                            settings_fields( 'aol_ads' ); 
                            do_settings_sections( 'aol_ads' );
                            if(!empty($types)): 
                                foreach($types as $key => $type):
                                    $count = wp_count_posts('aol_'.sanitize_key($type['singular']));
                                    echo '<li><p><a href="'.  admin_url('edit.php?post_type=aol_'.sanitize_key($type['singular'])).'">'.sanitize_text_field( $type['singular'] ) .' ('. sanitize_text_field( $type['plural'] ) .')</a></p>';
                                    echo '<p><b>Description: </b><input type="text" name="aol_ad_types['.$key.'][description]" value="'.$type['description'].'" Placeholder="'.__('Not set', 'apply-online').'"/></p>';
                                    echo '<p><b>Shortcode: </b><input type="text" readonly value="[aol type=&quot;'.sanitize_key($type['singular']).'&quot;]" /></p>';
                                    echo '<p><b>Direct URL: <a href="'.get_post_type_archive_link( 'aol_'.$key ).'" target="_blank">'.get_post_type_archive_link( 'aol_'.$key ).'</a></b></p>';
                                    echo '<input type="hidden" name="aol_ad_types['.$key.'][singular]" value="'.$type['singular'].'"/>';
                                    echo '<input type="hidden" name="aol_ad_types['.$key.'][plural]" value="'.$type['plural'].'"/>';
                                    $this->filters($type['filters'], $key);
                                    if(!$key == 'ad') echo ' &nbsp; <button class="button button-small removeField button-danger">Delete</button></li>';
                                endforeach;
                            endif;
                        ?>
                    </ol>
                    </div>
                </div>  
                
                <!--Generator -->
                <div class="clearfix clear"></div>
                <table id="adapp_form_fields" class="alignleft">
                <tbody>
                    <tr>
                        <td class="left" id="singular">
                            <input type="text" id="ad_type_singular" placeholder="<?php _e('Singular e.g. Career', 'apply-online'); ?>" />
                        </td>
                        <td class="left" id="plural">
                            <input type="text" id="ad_type_plural" placeholder="<?php _e('Plural e.g. Careers', 'apply-online'); ?>" />
                        </td>
                        <td class="left" id="desc">
                            <input type="text" id="ad_type_description" placeholder="<?php _e('Description', 'apply-online'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class=""><div class="button" id="ad_aol_type">Add Type</div></div>
                        </td>
                    </tr>
                </tbody>
                </table>
                <div class="clearfix clear"></div>
                <p><strong>IMPORTANT: </strong><i>If you get 404 error on direct links, try saving this section once again. Filters are used to narrow down ads listing on front-end and work with <strong>[aol]</strong> shortcode only.</i></p>
                <p>Need more customized filters? <a href="http://wpreloaded.com/product/apply-online-filters/" target="_blank">Click here</a>.</p>
                <hr />
                <?php submit_button(); ?>
            <?php //wp_nonce_field( 'aol_awesome_pretty_nonce','aol_ad_type_nonce' ); ?>
        </form>     
        <?php 
    }
    
    private function filters($set_filters, $cpt){
        ?>
            <ul id="ad_filters">
                <?php 
                $filters = aol_ad_filters();
                if(empty($set_filters)) $set_filters = array();
                foreach ($filters as $key => $val){
                    $checked = in_array(sanitize_key($key), $set_filters) ? 'checked' : NULL;
                    echo '<li><input id="filter-'.$cpt.'-'.$key.'" type="checkbox" name="aol_ad_types['.$cpt.'][filters][]" value="'.sanitize_key($key).'" '.$checked.'><label for="filter-'.$cpt.'-'.$key.'">Enable '.$val.' filter.</label></li>';
                }
                ?>
            </ul>
                <?php do_action('aol_after_filters'); ?>
        <?php
    }
    
    private function tab_filters_x(){
        $filter = get_option('aol_show_filter', 1);
        ?>
            <form  action="options.php" method="post"  id="aol_filters_form">
            <div class="app_form_fields adpost_fields">
                <div class="aol_table">
                    <input type="radio" id="show_filter" name="aol_show_filter" value="1" <?php if($filter == 1) echo 'checked'; ?> /><label for="show_filter">Show filter &nbsp; &nbsp; &nbsp; </label><input type="radio" id="hide_filter" name="aol_show_filter" value="0" <?php if($filter == 0) echo 'checked'; ?> /><label for="hide_filter">Hide filter</label>
                <ul id="ad_filters">
                    <?php 
                    settings_fields( 'aol_filters' ); 
                    do_settings_sections( 'aol_filters' );
                    $filters = aol_ad_filters();
                    $set_filters = get_option_fixed('aol_ad_filters', array());
                    $i=0;
                    foreach ($filters as $key => $val){
                        if($i >= 3) break;
                        $checked = in_array(sanitize_key($key), $set_filters) ? 'checked' : NULL;
                        echo '<li><input id="filter-'.$key.'" type="checkbox" name="aol_ad_filters[]" value="'.sanitize_key($key).'" '.$checked.'><label for="filter-'.$key.'">Enable '.$val.' filter.</label></li>';
                        $i++;
                    }
                    ?>
                </ul>
                    <?php do_action('aol_after_filters'); ?>
                    <p>Need more customized filters? <a href="http://wpreloaded.com/product/apply-online-filters/" target="_blank">Click Here</a></p>
                </div>
            </div>  
            <hr />
            <?php submit_button(); ?>
        </form>     
        <?php 
    }
    
    private function tab_applications(){
        ?>
            <form  action="options.php" method="post"  id="aol_applications_form">
            <div class="app_form_fields adpost_fields">
                <div class="aol_table">
                <ul id="ad_applications">
                    <?php 
                    settings_fields( 'aol_applications' );
                    do_settings_sections( 'aol_applications' );
                    $filters = aol_app_statuses();
                    $set_filters = get_option_fixed('aol_app_statuses', array());
                    $i = 0;
                    foreach ($filters as $key => $val){
                        $checked = in_array(sanitize_key($key), $set_filters) ? 'checked' : NULL;
                        echo '<li><input id="filter-'.$key.'" type="checkbox" name="aol_app_statuses[]" value="'.sanitize_key($key).'" '.$checked.'><label for="filter-'.$key.'">Enable '.$val.' status.</label></li>';
                        $i++;
                    }
                    ?>
                </ul>
                    <?php do_action('aol_after_application_setting'); ?>
                    <p>Need more customized statuses? <a href="http://wpreloaded.com/product/apply-online-statuses/" target="_blank"> Click Here</a></p>
                </div>
            </div>  
            <hr />

            <div class="clearfix clear"></div>
            <?php submit_button(); ?>
        </form>     
        <?php 
    }
    
    private function tab_labels(){
        
    }

    private function tab_faqs(){
        $slug = get_option_fixed('aol_slug', 'ads');
        ?>
        <div class="card" style="max-width:100%">
            <h3><?php echo __('How to create an ad?' ,'apply-online') ?></h3>
            In your WordPress admin panel, go to "All Ads" menu with globe icon and add a new ad listing here.

            <h3>How to show ad listings on the front-end?</h3>
            <!-- @todo Fix empty return value of aol_slug option. !-->
            You may choose either option.
            <ol>
                <li>Write [aol] shortcode in an existing page or add a new page and write shortcode anywhere in the page editor. Now click on VIEW to see all of your ads on front-end.
                <li>The url <b><a href="<?php echo get_post_type_archive_link( 'aol_ad' ); ?>" target="_blank" ><?php echo get_post_type_archive_link( 'aol_ad' ); ?></a></b> lists all the applications using your theme's default look and feel.<br />
                    &nbsp; &nbsp;&nbsp;(If above not working, try saving <a href="<?php echo get_admin_url().'/options-permalink.php'; ?>"  >permalinks</a> without any change)</li>
            </ol>

            <h3>I have a very long application form to fill, how can i facilitate applicant to fill it conveniently?</h3>
            With <a href="https://wpreloaded.com/product/apply-online-application-tracking-system/" target="_blank" class="strong">Application Tracking System</a> extention, applicant can save/update incomplete form for multiple times before final submission.
            
            <h3>Can I show selected ads on front-end?</h3>
            Yes, you can show any number of ads on your website by using shortcode with "ads" attribute. Ad ids must be separated with commas i.e. [aol ads="1,2,3" type="ad"]. Default type is "ad".

            <h3>Can I show ads without excerpt/summary?</h3>
            Yes, use shortcode with "excerpt" attribute i.e. [aol excerpt="no"]

            <h3>What attributes can i use in the shortcode?</h3>
            Shortcode with default attributes is [aol ads="1,2,3" excerpt="yes" type="ad"]. Use only required attributes.

            <h3>Can I display application form only using shortocode?</h3>
            Yes, [aol_form id="0"] is the shortcode to display a particular application form in WordPress pages or posts. Use correct form id in the shortocode.
            
            <h3>Filters under ApplyOnline section are not accessible.</h3>
            Try deactivating & then reactivating this plugin.
            
            <h3>I Have enabled the filters but they are not visible on the 'ads' page.</h3>
            Possible reasons for not displaying ad filters are given as under:

            <ol>
                <li>Filters are visible when you show your ad on front-end using [aol] shortcode only. </li>
                <li>Make sure Filters are enabled under Apply Online/Settings/Ad Types section in wordpress Admin Panel.</li>
                <li>When you create an ad, there is an option to categorize that ad under each filter e.g. Types or Locations.</li>
            </ol>
            
            <h3>Ads archive page on front-end shows 404 error or Nothing Found.</h3>
            Try saving <a href="<?php echo get_admin_url().'/options-permalink.php'; ?>"  >permalinks</a> without any change.
            
            <h3>I am facing a different problem. I may need a new feature in the plugin.</h3>
            Please contact us through <a href="http://wpreloaded.com/contact-us/" target="_blank">plugin's website</a> for more information.
        </div>    
        <?php
    }
    
    private function tab_extend(){
        ?>
        <div class="card" style="max-width:100%">
            <p>Looking for more options?</p>
            <p>There's a range of ApplyOnline extensions available to put additional power in your hands. <a href="http://wpreloaded.com/shop" target="_blank">Click Here</a> for docs and extensions.</p>
        </div>            
        <?php 
    }
 }