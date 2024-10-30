<?php
/*
	Plugin Name: Hoo Companion
	Description: Theme metabox options for HooThemes.
	Author: HooThemes
	Author URI: http://www.hoothemes.com/
	Version: 1.0.2
	Text Domain: hoo-companion
	Domain Path: /languages
	License: GPL v2 or later
*/

defined('ABSPATH') or die("No script kiddies please!");

if (!class_exists('HooCompanion')){

	class HooCompanion{	
		public $slider = array();
		
		public function __construct($atts = NULL)
		{
			
			$theme = wp_get_theme();			
			$option_name = $theme->get( 'Template' );
			if( $option_name == '' )
				$option_name = $theme->get( 'TextDomain' );
						
			$this->plugin_url 		= plugin_dir_url( __FILE__ );
			$this->plugin_path 		= plugin_dir_path( __FILE__ );
			
			define( 'HOOC_URL', $this->plugin_url );
			define( 'HOOC_PATH', $this->plugin_path );
			define( 'HOOC_VERSION', '1.0.2' );
			define( 'HOOC_THEME_TEXTDOMAIN', sanitize_title($option_name) );
			
			require_once( HOOC_PATH .'includes/widgets.php' );
			
			add_action('init', array(&$this , 'init') );
			
			add_action( 'admin_enqueue_scripts', array(&$this,'admin_scripts') );

			add_action( 'wp_enqueue_scripts', array(&$this,'front_scripts') );
			
			//add_action( 'customize_controls_init', array(&$this,'customize_controls_enqueue') );
			add_action( 'wp_ajax_hoo_get_categories', array(&$this, 'get_categories_by_taxonomy'));
			add_action( 'wp_ajax_nopriv_hoo_get_categories', array(&$this, 'get_categories_by_taxonomy'));
			add_action('init', array(&$this, 'html_tags_code' ));
			add_filter('hoo_sidebar',array(&$this,'filter_sidebar'));
			add_filter('hoo_admin_page_button',array(&$this,'importer_button'));
			
		}
		
	function init(){
		
		require_once( HOOC_PATH .'includes/metabox/controls/typography/webfonts.php' );
		require_once( HOOC_PATH .'includes/metabox/butterbean/butterbean.php' );
		require_once( HOOC_PATH .'includes/metabox/metabox.php' );
		require_once( HOOC_PATH .'includes/metabox/shortcodes.php' );
		require_once( HOOC_PATH .'includes/metabox/gallery-metabox/gallery-metabox.php' );
		
		require_once HOOC_PATH .'includes/importer/class-sites-helper.php';
		require_once HOOC_PATH .'includes/importer/class-customizer-import.php';
		require_once HOOC_PATH .'includes/importer/wxr-importer/class-hoo-wxr-importer.php';
		require_once HOOC_PATH .'includes/importer/class-site-options-import.php';
		require_once HOOC_PATH .'includes/importer/class-widgets-importer.php';
		require_once HOOC_PATH .'includes/importer/sites-importer.php';
		
		}

	/**
	 * Enqueue admin scripts
	*/
	function admin_scripts( $hook ) {
			
			global $pagenow;
			
			// Include custom jQuery file
			if( $pagenow == 'post.php' ){
				wp_enqueue_script( 'hoo-companion-script-handle', plugins_url( 'js/admin.js', __FILE__ ), array( 'wp-color-picker' ), HOOC_VERSION, true );			
				wp_enqueue_style( 'hoo-companion-front', plugins_url('css/admin.css', __FILE__) );
			}
			if( isset($_GET['page']) && $_GET['page'] == 'govideo-welcome'){
				wp_enqueue_script( 'hoo-companion-importer', plugins_url( 'js/site-importer.js', __FILE__ ), array( 'jquery' ), '', true );
				wp_localize_script( 'hoo-companion-importer', 'hooSiteImporter',
				array(
					'ajaxurl' => admin_url('admin-ajax.php'),
					'nonce' => wp_create_nonce( 'wp_rest' ),
					'i18n' =>array(
						's0' => __( "Executing Demo Import will make your site similar as preview. Please bear in mind -\n\n1. It is recommended to run import on a fresh WordPress installation.\n\n2. Importing site does not delete any pages or posts. However, it can overwrite your existing content.\n\n", 'hoo-companion' ),					
						's1'=> __( 'Importing Customizer...', 'hoo-companion' ),
						's2'=> __( 'Import Customizer Failed', 'hoo-companion' ),
						's3'=> __( 'Customizer Imported', 'hoo-companion' ),
						's4'=> __( 'Preparing WXR Data...', 'hoo-companion' ),
						's5'=> __( 'Import WXR Failed', 'hoo-companion' ),
						's6'=> __( 'Importing WXR...', 'hoo-companion' ),
						's6_1'=> __( 'Importing Media, Pages, Posts...', 'hoo-companion' ),
						's7'=> __( 'WXR Successfully imported!', 'hoo-companion' ),
						's8'=> __( 'Importing Theme Options...', 'hoo-companion' ),
						's9'=> __( 'Importing Options Failed', 'hoo-companion' ),
						's10'=> __( 'Theme Options Successfully imported!', 'hoo-companion' ),
						's11'=> __( 'Importing Widgets...', 'hoo-companion' ),
						's12'=> __( 'Import Widgets Failed', 'hoo-companion' ),
						's13'=> __( 'Widgets Successfully imported!', 'hoo-companion' ),
						's14'=> __( 'Site import complete!', 'hoo-companion' ),
						's14_1'=> sprintf(__( 'Site import complete! <a href="%s" target="_blank">Visit your website</a>', 'hoo-companion' ), esc_url( home_url( '/' ) )),
						  ),
				) );
			}
		}
	
	/**
	 * Load dynamic logic for the customizer controls area.
	 */
	function customize_controls_enqueue() {
		
	}

	/**
	 * Enqueue front scripts
	*/
	function front_scripts( ) {
		
		if( is_singular() ){
			global $post;
			$visits = get_post_meta( $post->ID, 'hoo_visits', true );
			$visits = absint($visits);
			$visits++;
			update_post_meta( $post->ID, 'hoo_visits', $visits );
		}

	}
	
	function importer_button(){
		
		$feed_url = esc_url('https://hoothemes.com/downloads/xml/govideo.xml');
		
		delete_transient('feed_' . md5($feed_url));
		delete_transient('feed_mod_' . md5($feed_url));

		$rss = fetch_feed( $feed_url );
		$sites_lis = array();
		$maxitems = 0;
		$required_plugins[] = array('name' => __( 'Hoo Companion', 'hoo-companion' ),'slug' => 'hoo-companion', "init" => "hoo-companion");
		
		if ( ! is_wp_error( $rss ) ) :
		
			$maxitems = $rss->get_item_quantity( 1 ); 
		
			$rss_items = $rss->get_items( 0, $maxitems );
		
		endif;
	
		if ( $maxitems == 0 ) :
		 
		else :
			
			foreach ( $rss_items as $item ) : 

			$options = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'options');
			$wxr = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'wxr');
			$widgets = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'widgets');
			$customizer = $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'customizer');
			
			

			if(isset($wxr[0]['data'])){
				$sites_list[] = array(
					'wxr' => $wxr[0]['data'],
					'required_plugins' => $required_plugins,
					'options' => $options[0]['data'],
					'widgets' => $widgets[0]['data'],
					'customizer' => $customizer[0]['data'],
					
				);
				}
		
				 endforeach; 
		 endif;
		 
		 if( isset($sites_list[0]) && !empty($sites_list[0]) ){
		?>
        
 
        <h3><?php esc_html_e( 'Import Demo', 'hoo-companion' ); ?></h3>
        <p><?php esc_html_e( 'Executing Demo Import will make your site similar as preview. Please bear in mind. It is recommended to run import on a fresh WordPress installation. Importing site does not delete any pages or posts. However, it can overwrite your existing content.', 'hoo-companion' ) ?></p>
        <p>
            <a href="javascript:;" class="hoo-import-site button button-primary" 
             data-site-wxr="<?php echo $sites_list[0]['wxr'];?>"
             data-site-options="<?php echo $sites_list[0]['options'];?>" 
             data-site-widgets="<?php echo $sites_list[0]['widgets'];?>" 
             data-site-customizer="<?php echo $sites_list[0]['customizer'];?>" 
            ><?php esc_html_e( 'Import', 'govideo' ); ?></a>
        </p>
        <div class="hoo-importer-status" style="background-color:#ddd; padding: 10px;"></div>
   
    
    <div class="hoo-required-plugins" style="display:none;">
						<?php
						foreach ( $required_plugins as $details ) {
				
							$file_name = isset($details['init'])?$details['init']:'';
							
							if ( HooCompanion::check_plugin_state( $details['slug'],$file_name ) === 'install' ) {
								echo '<div class="hoo-installable plugin-card-' . esc_attr( $details['slug'] ) . '">';
								echo '<span class="dashicons dashicons-no-alt"></span>';
								echo $details['name'];
								echo HooCompanion::get_button_html( $details['slug'],$file_name );
								echo '</div>';
							} elseif ( HooCompanion::check_plugin_state( $details['slug'],$file_name ) === 'activate' ) {
								echo '<div class="hoo-activate plugin-card-' . esc_attr( $details['slug'] ) . '">';
								echo '<span class="dashicons dashicons-admin-plugins" style="color: #ffb227;"></span>';
								echo $details['name'];
								echo HooCompanion::get_button_html( $details['slug'],$file_name );
								echo '</div>';
							} else {
								echo '<div class="hoo-installed plugin-card-' . esc_attr( $details['slug'] ) . '">';
								echo '<span class="dashicons dashicons-yes" style="color: #34a85e"></span>';
								echo $details['name'];
								echo '</div>';
							}
						}
						?>
					</div>
		
	<?php
	}
}
	
	
	/**
	 * Generate action button html.
	 *
	 */
	public static function get_button_html( $slug, $file='' ) {
		$button = '';
		if ( $file=='' )
			$file = $slug;
			
		$state  = HooCompanion::check_plugin_state( $slug, $file );
		if ( ! empty( $slug ) ) {
			switch ( $state ) {
				case 'install':
					$nonce  = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'install-plugin',
								'from'   => 'import',
								'plugin' => $slug,
							),
							network_admin_url( 'update.php' )
						),
						'install-plugin_' . $slug
					);
					$button .= '<a data-slug="' . $slug . '" class="install-now vela-install-plugin button button-primary" href="' . esc_url( $nonce ) . '" data-name="' . $slug . '" aria-label="Install ' . $slug . '">' . __( 'Install and activate', 'hoo-companion' ) . '</a>';
					break;
				case 'activate':
					$plugin_link_suffix = $slug . '/' . $file . '.php';
					$nonce              = add_query_arg(
						array(
							'action'   => 'activate',
							'plugin'   => rawurlencode( $plugin_link_suffix ),
							'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin_link_suffix ),
						), network_admin_url( 'plugins.php' )
					);
					$button             .= '<a data-slug="' . $slug . '" class="activate-now button button-primary" href="' . esc_url( $nonce ) . '" aria-label="Activate ' . $slug . '">' . __( 'Activate', 'hoo-companion' ) . '</a>';
					break;
			}// End switch().
		}// End if().
		return $button;
	}


/**
	 * Check plugin state.
	 */
	public static function check_plugin_state( $slug, $file='' ) {
		if($file =='')
			$file = $slug;
		if ( file_exists( WP_CONTENT_DIR . '/plugins/' . $slug . '/' . $file . '.php' ) || file_exists( WP_CONTENT_DIR . '/plugins/' . $slug . '/index.php' ) ) {
			require_once( ABSPATH . 'wp-admin' . '/includes/plugin.php' );
			$needs = ( is_plugin_active( $slug . '/' . $file . '.php' ) ||
			           is_plugin_active( $slug . '/index.php' ) ) ?
				'deactivate' : 'activate';

			return $needs;
		} else {
			return 'install';
		}
	}
	
/*
  *  Custom sidebar
  */
  
  function filter_sidebar( $sidebar){
	 
	  if( is_singular() ){
		   global $post;
		   $hoo_sidebar = get_post_meta( $post->ID, 'hoo_sidebar', true );
		  if( $hoo_sidebar!= '' ){
			  $sidebar = $hoo_sidebar;
			  }
	 }
		 
	return $sidebar;
	  
	  }
	
  /*
  *  Allow tags
  */
  
  function html_tags_code() {
	  
	  global $allowedposttags;
	  $allowed_atts = array(
		'align'      => array(),
		'class'      => array(),
		'type'       => array(),
		'id'         => array(),
		'dir'        => array(),
		'lang'       => array(),
		'style'      => array(),
		'xml:lang'   => array(),
		'src'        => array(),
		'alt'        => array(),
		'href'       => array(),
		'rel'        => array(),
		'rev'        => array(),
		'target'     => array(),
		'novalidate' => array(),
		'type'       => array(),
		'value'      => array(),
		'name'       => array(),
		'tabindex'   => array(),
		'action'     => array(),
		'method'     => array(),
		'for'        => array(),
		'width'      => array(),
		'height'     => array(),
		'data'       => array(),
		'title'      => array(),
		'async'      => array(),
	);
	$allowedposttags["script"] = $allowed_atts;
	 
  }
	
	/**
	 * Get categories
	*/
	
	function get_categories_by_taxonomy(){
		
		$taxonomy = esc_attr($_POST['taxonomy']);
		$name = esc_attr($_POST['name']);
		$id = esc_attr($_POST['id']);
		
		$args = array(
			'selected' => '',
			'name' => $name,
			'id' => $id,
			'show_option_all' => __( 'All Categories', 'hoo-companion' ),
			'show_option_none' => __( '=Select Category=', 'hoo-companion' ),
			'option_none_value' => '',
			'taxonomy' => $taxonomy,
			'class' => 'hoo-categories',
			'echo' => 0,
			'value_field' => 'slug',
		);
		
		$select = wp_dropdown_categories( $args );
		$select  = preg_replace( '#<select([^>]*)>#', '', $select ); 
		$select  = preg_replace( '#</select>#', '', $select ); 
		echo $select;
		
		exit(0);
	
	}
	
  /**
   * Get post id
  */
  public static function post_id() {

	  // Default value
	  $id = '';

	  // If singular get_the_ID
	  if ( is_singular() ) {
		  $id = get_the_ID();
	  }

	  // Get ID of WooCommerce product archive
	  elseif ( class_exists( 'WooCommerce' ) && is_shop() ) {
		  $shop_id = wc_get_page_id( 'shop' );
		  if ( isset( $shop_id ) ) {
			  $id = $shop_id;
		  }
	  }

	  // Posts page
	  elseif ( is_home() && $page_for_posts = get_option( 'page_for_posts' ) ) {
		  $id = $page_for_posts;
	  }

	  // Apply filters
	  $id = apply_filters( 'hoo_post_id', $id );

	  // Sanitize
	  $id = $id ? $id : '';

	  // Return ID
	  return $id;

  }
	
/**
 * Get zomm icon
 *
 */
 public static function get_zoom_icon( $size = 5 ){
	 
	return apply_filters('hoo_zoom_icon', '', $size );
	 
	 }

/**
 * Get default featured image
 */
public static function get_featured_image(){
	
	return apply_filters('hoo_default_featured_image', '' );
	
	}
	
/**
 * Get first tag
 */
public static function get_first_tag( $postid ){
	
	 $first_tag = '';
	 $post_tags = wp_get_post_tags( $postid );
		 
	 if ($post_tags)
		 $first_tag = $post_tags[0]->name;
		return $first_tag;
	}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
public static function posted_on() {
	
	return apply_filters( 'hoo_entry_meta', '' );
}

	}
	
	new HooCompanion;
}