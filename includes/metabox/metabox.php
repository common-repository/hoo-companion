<?php
/**
 * Adds custom metabox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The Metabox class
if ( ! class_exists( 'Hoo_Post_Metabox' ) ) {

	/**
	 * Main ButterBean class.  Runs the show.
	 *
	 * @access public
	 */
	final class Hoo_Post_Metabox {

		private $post_types;
		private $default_control;
		private $custom_control;

		/**
		 * Register this class with the WordPress API
		 *
		 */
		private function setup_actions() {

			// Capabilities
			$capabilities = apply_filters( 'hoo_main_metaboxes_capabilities', 'manage_options' );

			// Post types to add the metabox to
			$this->post_types = apply_filters( 'hoo_main_metaboxes_post_types', array(
				'post',
				'page',
				'product',
				'elementor_library',
			) );

			// Default butterbean controls
			$this->default_control = array(
				'select',
				'color',
				'image',
				'text',
				'number',
				'textarea',
			);

			// Custom butterbean controls
			$this->custom_control = array(
				'buttonset' 		=> 'Hoo_ButterBean_Control_Buttonset',
				'range' 			=> 'Hoo_ButterBean_Control_Range',
				'media' 			=> 'Hoo_ButterBean_Control_Media',
				'rgba-color' 		=> 'Hoo_ButterBean_Control_RGBA_Color',
				'multiple-select' 	=> 'Hoo_ButterBean_Control_Multiple_Select',
				'editor' 			=> 'Hoo_ButterBean_Control_Editor',
				'typography' 		=> 'Hoo_ButterBean_Control_Typography',
				'iconpicker' 		=> 'Hoo_ButterBean_Control_Iconpicker',
			);

			// Overwrite default controls
			add_filter( 'butterbean_pre_control_template', array( $this, 'default_control_templates' ), 10, 2 );

			// Register custom controls
			add_filter( 'butterbean_control_template', array( $this, 'custom_control_templates' ), 10, 2 );

			// Register new controls types
			add_action( 'butterbean_register', array( $this, 'register_control_types' ), 10, 2 );
			
			

			if ( current_user_can( $capabilities ) ) {

				// Register fields
				add_action( 'butterbean_register', array( $this, 'register' ), 10, 2 );

				// Register fields for the posts
				add_action( 'butterbean_register', array( $this, 'posts_register' ), 10, 2 );

				// Load scripts and styles.
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			}

			// Body classes
			add_filter( 'body_class', array( $this, 'body_class' ) );

			// Left sidebar
			add_filter( 'hoo_get_second_sidebar', array( $this, 'get_second_sidebar' ) );

			// Sidebar
			add_filter( 'hoo_get_sidebar', array( $this, 'get_sidebar' ) );

			// Display top bar
			add_filter( 'hoo_display_top_bar', array( $this, 'display_top_bar' ) );

			// Display header
			add_filter( 'hoo_display_header', array( $this, 'display_header' ) );

			// Custom menu
			add_filter( 'hoo_custom_menu', array( $this, 'custom_menu' ) );

			// Header style
			add_filter( 'hoo_header_style', array( $this, 'header_style' ) );

			// Left custom menu for center geader style
			add_filter( 'hoo_center_header_left_menu', array( $this, 'left_custom_menu' ) );

			// Custom header template
			add_filter( 'hoo_custom_header_template', array( $this, 'custom_header_template' ) );

			// Custom logo
			add_filter( 'get_custom_logo', array( $this, 'custom_logo' ) );

			// getustom logo ID for the retina function
			add_filter( 'hoo_custom_logo', array( $this, 'custom_logo_id' ) );

			// Custom retina logo
			add_filter( 'hoo_retina_logo', array( $this, 'custom_retina_logo' ) );

			// Custom logo max width
			add_filter( 'hoo_logo_max_width', array( $this, 'custom_logo_max_width' ) );

			// Custom logo max width tablet
			add_filter( 'hoo_logo_max_width_tablet', array( $this, 'custom_logo_max_width_tablet' ) );

			// Custom logo max width mobile
			add_filter( 'hoo_logo_max_width_mobile', array( $this, 'custom_logo_max_width_mobile' ) );

			// Custom logo max height
			add_filter( 'hoo_logo_max_height', array( $this, 'custom_logo_max_height' ) );

			// Custom logo max height tablet
			add_filter( 'hoo_logo_max_height_tablet', array( $this, 'custom_logo_max_height_tablet' ) );

			// Custom logo max height mobile
			add_filter( 'hoo_logo_max_height_mobile', array( $this, 'custom_logo_max_height_mobile' ) );

			// Menu colors
			add_filter( 'hoo_menu_link_color', array( $this, 'menu_link_color' ) );
			add_filter( 'hoo_menu_link_color_hover', array( $this, 'menu_link_color_hover' ) );
			add_filter( 'hoo_menu_link_color_active', array( $this, 'menu_link_color_active' ) );
			add_filter( 'hoo_menu_link_background', array( $this, 'menu_link_background' ) );
			add_filter( 'hoo_menu_link_hover_background', array( $this, 'menu_link_hover_background' ) );
			add_filter( 'hoo_menu_link_active_background', array( $this, 'menu_link_active_background' ) );
			add_filter( 'hoo_menu_social_links_bg', array( $this, 'menu_social_links_bg' ) );
			add_filter( 'hoo_menu_social_hover_links_bg', array( $this, 'menu_social_hover_links_bg' ) );
			add_filter( 'hoo_menu_social_links_color', array( $this, 'menu_social_links_color' ) );
			add_filter( 'hoo_menu_social_hover_links_color', array( $this, 'menu_social_hover_links_color' ) );

			// Display page header
			add_filter( 'hoo_display_page_header', array( $this, 'display_page_header' ) );

			// Display page header heading
			add_filter( 'hoo_display_page_header_heading', array( $this, 'display_page_header_heading' ) );

			// Page header style
			add_filter( 'hoo_page_header_style', array( $this, 'page_header_style' ) );

			// Page header title
			add_filter( 'hoo_title', array( $this, 'page_header_title' ) );

			// Page header subheading
			add_filter( 'hoo_post_subheading', array( $this, 'page_header_subheading' ) );

			// Display breadcrumbs
			add_filter( 'hoo_display_breadcrumbs', array( $this, 'display_breadcrumbs' ) );

			// Page header background image
			add_filter( 'hoo_page_header_background_image', array( $this, 'page_header_bg_image' ) );

			// Page header background color
			add_filter( 'hoo_post_title_background_color', array( $this, 'page_header_bg_color' ) );

			// Page header background image position
			add_filter( 'hoo_post_title_bg_image_position', array( $this, 'page_header_bg_image_position' ) );
			add_filter( 'hoo_post_title_bg_image_attachment', array( $this, 'page_header_bg_image_attachment' ) );
			add_filter( 'hoo_post_title_bg_image_repeat', array( $this, 'page_header_bg_image_repeat' ) );
			add_filter( 'hoo_post_title_bg_image_size', array( $this, 'page_header_bg_image_size' ) );

			// Page header height
			add_filter( 'hoo_post_title_height', array( $this, 'page_header_height' ) );

			// Page header background opacity
			add_filter( 'hoo_post_title_bg_overlay', array( $this, 'page_header_bg_opacity' ) );

			// Page header background overlay color
			add_filter( 'hoo_post_title_bg_overlay_color', array( $this, 'page_header_bg_overlay_color' ) );

			// Display footer widgets
			add_filter( 'hoo_display_footer_widgets', array( $this, 'display_footer_widgets' ) );

			// Display footer bottom
			add_filter( 'hoo_display_footer_bottom', array( $this, 'display_footer_bottom' ) );

			// Custom CSS
			add_filter( 'hoo_head_css', array( $this, 'head_css' ) );

		}

		/**
		 * Load scripts and styles
		 *
		 */
		public function enqueue_scripts( $hook ) {

			// Only needed on these admin screens
			if ( $hook != 'edit.php' && $hook != 'post.php' && $hook != 'post-new.php' ) {
				return;
			}

			// Get global post
			global $post;

			// Return if post is not object
			if ( ! is_object( $post ) ) {
				return;
			}

			// Post types scripts
			$post_types_scripts = apply_filters( 'hoo_metaboxes_post_types_scripts', $this->post_types );

			// Return if wrong post type
			if ( ! in_array( $post->post_type, $post_types_scripts ) ) {
				return;
			}

			$min = ( SCRIPT_DEBUG ) ? '' : '.min';

			// Default style
			wp_enqueue_style( 'hoocompanion-butterbean', plugins_url( '/controls/assets/css/butterbean'. $min .'.css', __FILE__ ) );

			// Default script.
			wp_enqueue_script( 'hoocompanion-butterbean', plugins_url( '/controls/assets/js/butterbean'. $min .'.js', __FILE__ ), array( 'butterbean' ), '', true );

			// Metabox script
			wp_enqueue_script( 'hoocompanion-metabox-script', plugins_url( '/assets/js/metabox.min.js', __FILE__ ), array( 'jquery' ), HOOC_VERSION, true );

			// Enqueue the select2 script, I use "hoocompanion-select2" to avoid plugins conflicts
			wp_enqueue_script( 'hoocompanion-select2', plugins_url( '/controls/assets/js/select2.full.min.js', __FILE__ ), array( 'jquery' ), false, true );

			// Enqueue the select2 style
			wp_enqueue_style( 'select2', plugins_url( '/controls/assets/css/select2.min.css', __FILE__ ) );

			// Enqueue color picker alpha
			wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( '/controls/assets/js/wp-color-picker-alpha.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

		}

		/**
		 * Registers control types
		 *
		 */
		public function register_control_types( $butterbean ) {
			$controls = $this->custom_control;

			foreach ( $controls as $control => $class ) {

				require_once( HOOC_PATH . '/includes/metabox/controls/'. $control .'/class-control-'. $control .'.php' );
				$butterbean->register_control_type( $control, $class );

			}
		}

		/**
		 * Get custom control templates
		 *
		 */
		public function default_control_templates( $located, $slug ) {
			$controls = $this->default_control;

			foreach ( $controls as $control ) {

				if ( $slug === $control ) {
					return HOOC_PATH . '/includes/metabox/controls/'. $control .'/template.php';
				}

			}

			return $located;
		}

		/**
		 * Get custom control templates
		 *
		 */
		public function custom_control_templates( $located, $slug ) {
			$controls = $this->custom_control;

			foreach ( $controls as $control => $class ) {

				if ( $slug === $control ) {
					return HOOC_PATH . '/includes/metabox/controls/'. $control .'/template.php';
				}

			}

			return $located;
		}

		/**
		 * Registration callback
		 *
		 */
		public function register( $butterbean, $post_type ) {

			// Post types to add the metabox to
			$post_types = $this->post_types;

			
			$brand = apply_filters( 'hoo_theme_branding',  esc_html__( 'Page Settings', 'hoo-companion' ) );

			// Register managers, sections, controls, and settings here.
			$butterbean->register_manager(
		        'hoo_mb_settings',
		        array(
		            'label'     => $brand ,
		            'post_type' => $post_types,
		            'context'   => 'normal',
		            'priority'  => 'high'
		        )
		    );
						
			$manager = $butterbean->get_manager( 'hoo_mb_settings' );
			
			
			
			$manager->register_section(
		        'hoo_mb_main',
		        array(
		            'label' => esc_html__( 'Main', 'hoo-companion' ),
		            'icon'  => 'dashicons-admin-generic'
		        )
		    );
			
			if( $post_type == 'post'){
				
				
			/*$manager->register_control(
		        'hoo_display_featured_image', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'buttonset',
		            'label'   		=> esc_html__( 'Display Featured Image', 'hoo-companion' ),
		            'description'   => '',
					'choices' 		=> array(
						'0' 		=> esc_html__( 'No', 'hoo-companion' ),
						'1' 	=> esc_html__( 'Yes', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_display_featured_image', // Same as control name.
		        array(
		            'sanitize_callback' => 'absint',
		            'default' 			=> '0',
		        )
		    );*/
			
			
			$manager->register_control(
		        'hoo_social_share', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'textarea',
		            'label'   		=> esc_html__( 'Social Share Code (e.g. Share This Code)', 'hoo-companion' ),
		            'description'   => '',
					
		        )
		    );
			
			$manager->register_setting(
		        'hoo_social_share', // Same as control name.
		        array(
		            'sanitize_callback' => 'wp_filter_post_kses',
		            'default' 			=> '',
		        )
		    );
			
				
			$manager->register_control(
		        'hoo_display_social_share', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'buttonset',
		            'label'   		=> esc_html__( 'Display Share Code Area', 'hoo-companion' ),
		            'description'   => '',
					'choices' 		=> array(
						'0' 		=> esc_html__( 'No', 'hoo-companion' ),
						'1' 	=> esc_html__( 'Yes', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_display_social_share', // Same as control name.
		        array(
		            'sanitize_callback' => 'absint',
		            'default' 			=> '0',
		        )
		    );
			
			$manager->register_control(
		        'hoo_post_zoom_icon', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'iconpicker',
		            'label'   		=> esc_html__( 'Post Thumbnail Zoom Icon', 'hoo-companion' ),
		            'description'   => '',
		        )
		    );
			
			$manager->register_setting(
		        'hoo_post_zoom_icon', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		        )
		    );
				
			}

		    /*$manager->register_control(
		        'hoo_post_layout', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'select',
		            'label'   		=> esc_html__( 'Content Layout', 'hoo-companion' ),
		            'description'   => esc_html__( 'Select your custom layout.', 'hoo-companion' ),
					'choices' 		=> array(
						'' 				=> esc_html__( 'Default', 'hoo-companion' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'hoo-companion' ),
						'left-sidebar' 	=> esc_html__( 'Left Sidebar', 'hoo-companion' ),
						'full-width' 	=> esc_html__( 'Full Width', 'hoo-companion' ),
						'full-screen' 	=> esc_html__( '100% Full Width', 'hoo-companion' ),
						'both-sidebars' => esc_html__( 'Both Sidebars', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_post_layout', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		        )
		    );

		    $manager->register_control(
		        'hoo_both_sidebars_style', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'select',
		            'label'   		=> esc_html__( 'Both Sidebars: Style', 'hoo-companion' ),
		            'description'   => esc_html__( 'Select your both sidebars style.', 'hoo-companion' ),
					'choices' 		=> array(
						'' 				=> esc_html__( 'Default', 'hoo-companion' ),
						'ssc-style' 	=> esc_html__( 'Sidebar / Sidebar / Content', 'hoo-companion' ),
						'scs-style' 	=> esc_html__( 'Sidebar / Content / Sidebar', 'hoo-companion' ),
						'css-style' 	=> esc_html__( 'Content / Sidebar / Sidebar', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_both_sidebars_style', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		        )
		    );

		    $manager->register_control(
		        'hoo_both_sidebars_content_width', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'number',
		            'label'   		=> esc_html__( 'Both Sidebars: Content Width (%)', 'hoo-companion' ),
		            'description'   => esc_html__( 'Enter for custom content width.', 'hoo-companion' ),
		            'attr'    		=> array(
						'min' 	=> '0',
						'step' 	=> '1',
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_both_sidebars_content_width', // Same as control name.
		        array(
		            'sanitize_callback' => array( $this, 'sanitize_absint' ),
		        )
		    );

		    $manager->register_control(
		        'hoo_both_sidebars_sidebars_width', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'number',
		            'label'   		=> esc_html__( 'Both Sidebars: Sidebars Width (%)', 'hoo-companion' ),
		            'description'   => esc_html__( 'Enter for custom sidebars width.', 'hoo-companion' ),
		            'attr'    		=> array(
						'min' 	=> '0',
						'step' 	=> '1',
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_both_sidebars_sidebars_width', // Same as control name.
		        array(
		            'sanitize_callback' => array( $this, 'sanitize_absint' ),
		        )
		    );
			*/
			$manager->register_control(
		        'hoo_sidebar', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'select',
		            'label'   		=> esc_html__( 'Sidebar', 'hoo-companion' ),
		            'description'   => esc_html__( 'Select your custom sidebar.', 'hoo-companion' ),
					'choices' 		=> $this->helpers( 'widget_areas' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_sidebar', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		        )
		    );
			
			$manager->register_control(
		        'hoo_second_sidebar', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_main',
		            'type'    		=> 'select',
		            'label'   		=> esc_html__( 'Second Sidebar', 'hoo-companion' ),
		            'description'   => esc_html__( 'Select your custom second sidebar.', 'hoo-companion' ),
					'choices' 		=> $this->helpers( 'widget_areas' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_second_sidebar', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		        )
		    );
			
			$manager->register_section(
		        'hoo_mb_shortcodes',
		        array(
		            'label' => esc_html__( 'Shortcodes', 'hoo-companion' ),
		            'icon'  => 'dashicons-editor-code'
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_before_top_bar', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode Before Top Bar', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed before the top bar.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_before_top_bar', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_after_top_bar', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode After Top Bar', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed after the top bar.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_after_top_bar', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_before_header', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode Before Header', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed before the header.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_before_header', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_after_header', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode After Header', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed after the header.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_after_header', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_has_shortcode', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode Before Title', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed before the page title.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_has_shortcode', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_after_title', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode After Title', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed after the page title.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_after_title', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_before_footer_widgets', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode Before Footer Widgets', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed before the footer widgets.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_before_footer_widgets', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_after_footer_widgets', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode After Footer Widgets', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed after the footer widgets.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_after_footer_widgets', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_before_footer_bottom', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode Before Footer Bottom', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed before the footer bottom.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_before_footer_bottom', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
		
			$manager->register_control(
		        'hoo_shortcode_after_footer_bottom', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_shortcodes',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Shortcode After Footer Bottom', 'hoo-companion' ),
		            'description'   => esc_html__( 'Add your shortcode to be displayed after the footer bottom.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_shortcode_after_footer_bottom', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_text_field',
		        )
		    );
			
			$manager->register_section(
		        'hoo_mb_menu',
		        array(
		            'label' => esc_html__( 'Menu', 'hoo-companion' ),
		            'icon'  => 'dashicons-menu'
		        )
		    );
			
			$manager->register_control(
		        'hoo_header_custom_menu', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_menu',
		            'type'    		=> 'select',
		            'label'   		=> esc_html__( 'Main Navigation Menu', 'hoo-companion' ),
		            'description'   => esc_html__( 'Choose which menu to display on this page/post.', 'hoo-companion' ),
					'choices' 		=> $this->helpers( 'menus' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_header_custom_menu', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		        )
		    );

			$manager->register_section(
		        'hoo_mb_title',
		        array(
		            'label' => esc_html__( 'Title', 'hoo-companion' ),
		            'icon'  => 'dashicons-admin-tools'
		        )
		    );
			
			$manager->register_control(
		        'hoo_disable_title', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_title',
		            'type'    		=> 'buttonset',
		            'label'   		=> esc_html__( 'Display Page Title', 'hoo-companion' ),
		            'description'   => esc_html__( 'Enable or disable the page title.', 'hoo-companion' ),
					'choices' 		=> array(
						'default' 	=> esc_html__( 'Default', 'hoo-companion' ),
						'enable' 	=> esc_html__( 'Enable', 'hoo-companion' ),
						'on' 		=> esc_html__( 'Disable', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_disable_title', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		            'default' 			=> 'default',
		        )
		    );
			
			/*$manager->register_control(
		        'hoo_disable_heading', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_title',
		            'type'    		=> 'buttonset',
		            'label'   		=> esc_html__( 'Display Heading', 'hoo-companion' ),
		            'description'   => esc_html__( 'Enable or disable the page title heading.', 'hoo-companion' ),
					'choices' 		=> array(
						'default' 	=> esc_html__( 'Default', 'hoo-companion' ),
						'enable' 	=> esc_html__( 'Enable', 'hoo-companion' ),
						'on' 		=> esc_html__( 'Disable', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_disable_heading', // Same as control name.
		        array(
		            'sanitize_callback' => 'sanitize_key',
		            'default' 			=> 'default',
		        )
		    );
		
			$manager->register_control(
		        'hoo_post_title', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_title',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Custom Title', 'hoo-companion' ),
		            'description'   => esc_html__( 'Alter the main title display.', 'hoo-companion' ),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_post_title', // Same as control name.
		        array(
		            'sanitize_callback' => 'wp_kses_post',
		        )
		    );
		*/
		}

		/**
		 * Registration callback
		 *
		 */
		public function posts_register( $butterbean, $post_type ) {

			// Return if it is not Post post type
			if ( 'post' != $post_type ) {
				return;
			}

			// Gets the manager object we want to add sections to.
			$manager = $butterbean->get_manager( 'hoo_mb_settings' );
						
			$manager->register_section(
		        'hoo_mb_video',
		        array(
		            'label' => esc_html__( 'Video', 'hoo-companion' ),
		            'icon'  => 'dashicons-format-video'
		        )
		    );

		   $manager->register_control(
		        'hoo_video_url', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_video',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'YouTube or Vimeo Video URL', 'hoo-companion' ),
		            'description'   => '',
		        )
		    );
			
			$manager->register_setting(
		        'hoo_video_url', // Same as control name.
		        array(
		            'sanitize_callback' => 'esc_url_raw',
		        )
		    );
			
			 $manager->register_control(
		        'hoo_video_height', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_video',
		            'type'    		=> 'text',
		            'label'   		=> esc_html__( 'Video Iframe Height', 'hoo-companion' ),
		            'description'   => '',
		        )
		    );
			
			$manager->register_setting(
		        'hoo_video_height', // Same as control name.
		        array(
		            'sanitize_callback' => 'absint',
					'default'   => '400',
		        )
		    );
			
			$manager->register_control(
		        'hoo_video_autoplay', // Same as setting name.
		        array(
		            'section' 		=> 'hoo_mb_video',
		            'type'    		=> 'buttonset',
		            'label'   		=> esc_html__( 'AutoPlay', 'hoo-companion' ),
		            'description'   => '',
					'choices' 		=> array(
						'0' 		=> esc_html__( 'No', 'hoo-companion' ),
						'1' 	=> esc_html__( 'Yes', 'hoo-companion' ),
					),
		        )
		    );
			
			$manager->register_setting(
		        'hoo_video_autoplay', // Same as control name.
		        array(
		            'sanitize_callback' => 'absint',
		            'default' 			=> '0',
		        )
		    );
			
			

		}

		/**
		 * Sanitize function for integers
		 */
		public function sanitize_absint( $value ) {
			return $value && is_numeric( $value ) ? absint( $value ) : '';
		}

		/**
		 * Helpers
		 */
		public static function helpers( $return = NULL ) {

			// Return array of WP menus
			if ( 'menus' == $return ) {
				$menus 		= array( esc_html__( 'Default', 'hoo-companion' ) );
				$get_menus 	= get_terms( 'nav_menu', array( 'hide_empty' => true ) );
				foreach ( $get_menus as $menu) {
					$menus[$menu->term_id] = $menu->name;
				}
				return $menus;
			}

			// Header template
			elseif ( 'library' == $return ) {
				$templates 		= array( esc_html__( 'Select a Template', 'hoo-companion' ) );
				$get_templates 	= get_posts( array( 'post_type' => 'hoo_library', 'numberposts' => -1, 'post_status' => 'publish' ) );

			    if ( ! empty ( $get_templates ) ) {
			    	foreach ( $get_templates as $template ) {
						$templates[ $template->ID ] = $template->post_title;
				    }
				}

				return $templates;
			}

			// Title styles
			elseif ( 'title_styles' == $return ) {
				return apply_filters( 'hoo_title_styles', array(
					''                 => esc_html__( 'Default', 'hoo-companion' ),
					'default'          => esc_html__( 'Default Style', 'hoo-companion' ),
					'centered'         => esc_html__( 'Centered', 'hoo-companion' ),
					'centered'         => esc_html__( 'Centered', 'hoo-companion' ),
					'centered-minimal' => esc_html__( 'Centered Minimal', 'hoo-companion' ),
					'background-image' => esc_html__( 'Background Image', 'hoo-companion' ),
					'solid-color'      => esc_html__( 'Solid Color and White Text', 'hoo-companion' ),
				) );
			}

			// Widgets
			elseif ( 'widget_areas' == $return ) {
				global $wp_registered_sidebars;
				$widgets_areas = array( esc_html__( 'Default', 'hoo-companion' ) );
				$get_widget_areas = $wp_registered_sidebars;
				if ( ! empty( $get_widget_areas ) ) {
					foreach ( $get_widget_areas as $widget_area ) {
						$name = isset ( $widget_area['name'] ) ? $widget_area['name'] : '';
						$id = isset ( $widget_area['id'] ) ? $widget_area['id'] : '';
						if ( $name && $id ) {
							$widgets_areas[$id] = $name;
						}
					}
				}
				return $widgets_areas;
			}

		}

		/**
		 * Body classes
		 */
		public function body_class( $classes ) {
			
			// Disabled margins
			if ( 'on' == get_post_meta( HooCompanion::post_id(), 'hoo_disable_margins', true )
				&& ! is_search() ) {
				$classes[] = 'no-margins';
			}

			return $classes;

		}

		/**
		 * Returns the correct second sidebar ID
		 *
		 */
		public function get_second_sidebar( $sidebar ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_second_sidebar', true ) ) {
				$sidebar = $meta;
			}

			return $sidebar;

		}

		/**
		 * Returns the correct sidebar ID
		 *
		 */
		public function get_sidebar( $sidebar ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_sidebar', true ) ) {
				$sidebar = $meta;
			}

			return $sidebar;

		}

		/**
		 * Display top bar
		 *
		 */
		public function display_top_bar( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_display_top_bar', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Display header
		 *
		 */
		public function display_header( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_display_header', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Custom menu
		 *
		 */
		public function custom_menu( $menu ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_header_custom_menu', true ) ) {
				$menu = $meta;
			}

			return $menu;

		}

		/**
		 * Header style
		 *
		 */
		public function header_style( $style ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_header_style', true ) ) {
				$style = $meta;
			}

			return $style;

		}

		/**
		 * Left custom menu for center geader style
		 *
		 */
		public function left_custom_menu( $menu ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_center_header_left_menu', true ) ) {
				$menu = $meta;
			}
			
			return $menu;

		}

		/**
		 * Custom header template
		 *
		 */
		public function custom_header_template( $template ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_header_template', true ) ) {
				$template = $meta;
			}

			return $template;

		}

		/**
		 * Custom logo
		 *
		 */
		public function custom_logo( $html ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo', true ) ) {

				$html = '';

				// We have a logo. Logo is go.
				if ( $meta ) {

					$custom_logo_attr = array(
						'class'    => 'custom-logo',
						'itemprop' => 'logo',
					);

					/*
					 * If the logo alt attribute is empty, get the site title and explicitly
					 * pass it to the attributes used by wp_get_attachment_image().
					 */
					$image_alt = get_post_meta( $meta, '_wp_attachment_image_alt', true );
					if ( empty( $image_alt ) ) {
						$custom_logo_attr['alt'] = get_bloginfo( 'name', 'display' );
					}

					/*
					 * If the alt attribute is not empty, there's no need to explicitly pass
					 * it because wp_get_attachment_image() already adds the alt attribute.
					 */
					$html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
						esc_url( home_url( '/' ) ),
						wp_get_attachment_image( $meta, 'full', false, $custom_logo_attr )
					);

				}

			}

			return $html;

		}

		/**
		 * Custom logo ID
		 *
		 */
		public function custom_logo_id( $logo_url ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo', true ) ) {
				$logo_url = $meta;
			}

			return $logo_url;

		}

		/**
		 * Custom retina logo
		 *
		 */
		public function custom_retina_logo( $logo_url ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_retina_logo', true ) ) {
				$logo_url = $meta;

				// Generate image URL if using ID
				if ( is_numeric( $logo_url ) ) {
					$logo_url = wp_get_attachment_image_src( $logo_url, 'full' );
					$logo_url = $logo_url[0];
				}
			}

			return $logo_url;

		}

		/**
		 * Custom logo max width
		 *
		 */
		public function custom_logo_max_width( $width ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo_max_width', true ) ) {
				$width = $meta;
			}

			return $width;

		}

		/**
		 * Custom logo max width tablet
		 */
		public function custom_logo_max_width_tablet( $width ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo_tablet_max_width', true ) ) {
				$width = $meta;
			}

			return $width;

		}

		/**
		 * Custom logo max width mobile
		 *
		 */
		public function custom_logo_max_width_mobile( $width ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo_mobile_max_width', true ) ) {
				$width = $meta;
			}

			return $width;

		}

		/**
		 * Custom logo max height
		 *
		 */
		public function custom_logo_max_height( $height ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo_max_height', true ) ) {
				$height = $meta;
			}

			return $height;

		}

		/**
		 * Custom logo max height tablet
		 *
		 */
		public function custom_logo_max_height_tablet( $height ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo_tablet_max_height', true ) ) {
				$height = $meta;
			}

			return $height;

		}

		/**
		 * Custom logo max height mobile
		 *
		 */
		public function custom_logo_max_height_mobile( $height ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_custom_logo_mobile_max_height', true ) ) {
				$height = $meta;
			}

			return $height;

		}

		/**
		 * Menu links color
		 *
		 */
		public function menu_link_color( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_link_color', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links color: hover
		 *
		 */
		public function menu_link_color_hover( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_link_color_hover', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links color: current menu item
		 *
		 */
		public function menu_link_color_active( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_link_color_active', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links background
		 *
		 */
		public function menu_link_background( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_link_background', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links background: hover
		 *
		 */
		public function menu_link_hover_background( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_link_hover_background', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Menu links background: current menu item
		 */
		public function menu_link_active_background( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_link_active_background', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu links background color
		 *
		 */
		public function menu_social_links_bg( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_social_links_bg', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu hover links background color
		 *
		 */
		public function menu_social_hover_links_bg( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_social_hover_links_bg', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu links color
		 *
		 */
		public function menu_social_links_color( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_social_links_color', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Social menu hover links color
		 *
		 */
		public function menu_social_hover_links_color( $color ) {

			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_menu_social_hover_links_color', true ) ) {
				$color = $meta;
			}

			return $color;

		}

		/**
		 * Display page header
		 *
		 */
		public function display_page_header( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_disable_title', true ) : '';

			// Check if enabled or disabled
			if ( 'enable' == $meta ) {
				$return = true;
			} elseif ( 'on' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Display page header heading
		 *
		 */
		public function display_page_header_heading( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_disable_heading', true ) : '';

			// Check if enabled or disabled
			if ( 'enable' == $meta ) {
				$return = true;
			} elseif ( 'on' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Page header style
		 *
		 */
		public function page_header_style( $style ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				$style = $meta;
			}

			return $style;

		}

		/**
		 * Page header title
		 */
		public function page_header_title( $title ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title', true ) ) {
				$title = $meta;
			}

			return $title;

		}

		/**
		 * Page header subheading
		 *
		 */
		public function page_header_subheading( $subheading ) {
			
			if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_subheading', true ) ) {
				$subheading = $meta;
			}

			return $subheading;

		}

		/**
		 * Display breadcrumbs
		 *

		 */
		public function display_breadcrumbs( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_disable_breadcrumbs', true ) : '';

			// Check if enabled or disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Title background color
		 *

		 */
		public function page_header_bg_color( $bg_color ) {

			if ( 'solid-color' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_background_color', true ) ) {
					$bg_color = $meta;
				}
			}

			return $bg_color;

		}

		/**
		 * Title background image
		 *

		 */
		public function page_header_bg_image( $bg_img ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_background', true ) ) {
					$bg_img = $meta;
				}
			}

			return $bg_img;

		}

		/**
		 * Title background image position
		 *

		 */
		public function page_header_bg_image_position( $bg_img_position ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_bg_image_position', true ) ) {
					$bg_img_position = $meta;
				}
			}

			return $bg_img_position;

		}

		/**
		 * Title background image attachment
		 *

		 */
		public function page_header_bg_image_attachment( $bg_img_attachment ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_bg_image_attachment', true ) ) {
					$bg_img_attachment = $meta;
				}
			}

			return $bg_img_attachment;

		}

		/**
		 * Title background image repeat
		 *

		 */
		public function page_header_bg_image_repeat( $bg_img_repeat ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_bg_image_repeat', true ) ) {
					$bg_img_repeat = $meta;
				}
			}

			return $bg_img_repeat;

		}

		/**
		 * Title background image size
		 *

		 */
		public function page_header_bg_image_size( $bg_img_size ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_bg_image_size', true ) ) {
					$bg_img_size = $meta;
				}
			}

			return $bg_img_size;

		}

		/**
		 * Title height
		 *

		 */
		public function page_header_height( $title_height ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_height', true ) ) {
					$title_height = $meta;
				}
			}

			return $title_height;

		}

		/**
		 * Title background opacity
		 *

		 */
		public function page_header_bg_opacity( $opacity ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_bg_overlay', true ) ) {
					$opacity = $meta;
				}
			}

			return $opacity;

		}

		/**
		 * Title background overlay color
		 *

		 */
		public function page_header_bg_overlay_color( $overlay_color ) {

			if ( 'background-image' == get_post_meta( HooCompanion::post_id(), 'hoo_post_title_style', true ) ) {
				if ( $meta = get_post_meta( HooCompanion::post_id(), 'hoo_post_title_bg_overlay_color', true ) ) {
					$overlay_color = $meta;
				}
			}

			return $overlay_color;

		}

		/**
		 * Display footer widgets
		 *

		 */
		public function display_footer_widgets( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_display_footer_widgets', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Display footer bottom
		 *

		 */
		public function display_footer_bottom( $return ) {
			
			// Check meta
			$meta = HooCompanion::post_id() ? get_post_meta( HooCompanion::post_id(), 'hoo_display_footer_bottom', true ) : '';

			// Check if disabled
			if ( 'on' == $meta ) {
				$return = true;
			} elseif ( 'off' == $meta ) {
				$return = false;
			}

			return $return;

		}

		/**
		 * Get CSS
		 *

		 */
		public static function head_css( $output ) {
			$id = HooCompanion::post_id();

			// Layout
			$layout 				= get_post_meta( $id, 'hoo_post_layout', true );

			// Global vars
			$content_width 			= get_post_meta( $id, 'hoo_both_sidebars_content_width', true );
			$sidebars_width 		= get_post_meta( $id, 'hoo_both_sidebars_sidebars_width', true );

			// Typography
			$menu_font_family 		= get_post_meta( $id, 'hoo_menu_typo_font_family', true );
			$menu_font_size 		= get_post_meta( $id, 'hoo_menu_typo_font_size', true );
			$menu_font_weight 		= get_post_meta( $id, 'hoo_menu_typo_font_weight', true );
			$menu_font_style 		= get_post_meta( $id, 'hoo_menu_typo_font_style', true );
			$menu_text_transform 	= get_post_meta( $id, 'hoo_menu_typo_transform', true );
			$menu_line_height 		= get_post_meta( $id, 'hoo_menu_typo_line_height', true );
			$menu_letter_spacing 	= get_post_meta( $id, 'hoo_menu_typo_spacing', true );

			// Define css var
			$css = '';
			$menu_typo_css = '';

			// If Both Sidebars layout
			if ( 'both-sidebars' == $layout ) {

				// Both Sidebars layout content width
				if ( ! empty( $content_width ) ) {
					$css .=
						'@media only screen and (min-width: 960px){
							.content-both-sidebars .content-area {width: '. $content_width .'%;}
							.content-both-sidebars.scs-style .widget-area.sidebar-secondary,
							.content-both-sidebars.ssc-style .widget-area {left: -'. $content_width .'%;}
						}';
				}

				// Both Sidebars layout sidebars width
				if ( ! empty( $sidebars_width ) ) {
					$css .=
						'@media only screen and (min-width: 960px){
							.content-both-sidebars .widget-area{width:'. $sidebars_width .'%;}
							.content-both-sidebars.scs-style .content-area{left:'. $sidebars_width .'%;}
							.content-both-sidebars.ssc-style .content-area{left:'. $sidebars_width * 2 .'%;}
						}';
				}

			}

			// Add menu font size
			if ( ! empty( $menu_font_size ) ) {
				$menu_typo_css .= 'font-size:'. $menu_font_size .';';
			}

			// Add menu font weight
			if ( ! empty( $menu_font_weight ) ) {
				$menu_typo_css .= 'font-weight:'. $menu_font_weight .';';
			}

			// Add menu font style
			if ( ! empty( $menu_font_style ) ) {
				$menu_typo_css .= 'font-style:'. $menu_font_style .';';
			}

			// Add menu text transform
			if ( ! empty( $menu_text_transform ) ) {
				$menu_typo_css .= 'text-transform:'. $menu_text_transform .';';
			}

			// Add menu line height
			if ( ! empty( $menu_line_height ) ) {
				$menu_typo_css .= 'line-height:'. $menu_line_height .';';
			}

			// Add menu letter spacing
			if ( ! empty( $menu_letter_spacing ) ) {
				$menu_typo_css .= 'letter-spacing:'. $menu_letter_spacing .';';
			}

			// Menu typography css
			if ( ! empty( $menu_typo_css ) ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a, .hoocompanion-mobile-menu-icon a {'. $menu_typo_css .'}';
			}
				
			// Return CSS
			if ( ! empty( $css ) ) {
				$output .= $css;
			}

			// Return output css
			return $output;

		}

		/**
		 * Returns the instance.
		 *

		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			static $instance = null;
			if ( is_null( $instance ) ) {
				$instance = new self;
				$instance->setup_actions();
			}
			return $instance;
		}

		/**
		 * Constructor method.
		 *

		 * @access private
		 * @return void
		 */
		private function __construct() {}

	}

	Hoo_Post_Metabox::get_instance();

}