<?php

if ( !class_exists( 'Shoestrap_Framework' ) ) {

	/**
	* The "Advanced" module
	*/
	class Shoestrap_Framework {

		/**
		 * Class constructor
		 */
		function __construct() {

			$settings         = get_option( SHOESTRAP_OPT_NAME );
			$active_framework = $settings['framework'];

			// Add the frameworks select to redux.
			add_filter( 'redux/options/' . SHOESTRAP_OPT_NAME . '/sections', array( $this, 'options' ), 75 );

			// Include all frameworks
			$modules_path = new RecursiveDirectoryIterator( SHOESTRAP_MODULES_PATH . '/framework/' );
			$recIterator  = new RecursiveIteratorIterator( $modules_path );
			$regex        = new RegexIterator( $recIterator, '/\/*.php$/i' );

			foreach( $regex as $item ) {
				require_once $item->getPathname();
			}

			$frameworks       = $this->frameworks_list();

			// Return the classname of the active framework.
			foreach ( $frameworks as $framework ) {
				if ( $active_framework == $framework['shortname'] ) {
					$active = $framework['classname'];
				}
			}

			// If no framework is active, return.
			if ( !isset( $active ) ) {
				return;
			}

			$this->fw = new $active;
		}

		/**
		 * Get a list of all the available frameworks.
		 */
		function frameworks_list() {
			$frameworks = apply_filters( 'shoestrap_frameworks_array', array() );

			return $frameworks;
		}

		/*
		 * Create the framework selector
		 */
		function options( $sections ) {
			global $redux;
			$settings = get_option( SHOESTRAP_OPT_NAME );

			$frameworks = $this->frameworks_list();

			$frameworks_select = array();
			foreach ( $frameworks as $framework ) {
				$frameworks_select[$framework['shortname']] = $framework['name'];
			}

			// Blog Options
			$section = array(
				'title' => __( 'Framework', 'shoestrap' ),
				'icon'  => 'el-icon-home',
			);

			$fields[] = array(
				'title'     => __( 'Framework Select', 'shoestrap' ),
				'desc'      => __( 'Select a framework.', 'shoestrap' ),
				'id'        => 'framework',
				'default'   => '',
				'type'      => 'select',
				'options'   => $frameworks_select,
				'compiler'  => true,
			);

			$section['fields'] = $fields;

			do_action( 'shoestrap_module_layout_options_modifier' );
			
			$sections[] = $section;
			return $sections;
		}

		/**
		 * Calls the framework-specific make_row() function
		 */
		function make_row( $context = 'open', $element = 'div', $id = null, $extra_classes = null, $properties = null ) {
			return $this->fw->make_row( $context, $element, $id, $extra_classes, $properties );
		}

		/**
		 * Calls the framework-specific make_col() function
		 */
		function make_col( $context = 'open', $element = 'div', $sizes = array( 'normal' => 12 ), $id = null, $extra_classes = null, $properties = null ) {
			return $this->fw->make_col( $context, $element, $sizes, $id, $extra_classes, $properties );
		}

		/**
		 * Calls the framework-specific button_classes() function
		 */
		function button_classes( $color = 'primary', $size = 'medium', $type = 'normal', $extra = null ) {
			return $this->fw->button_classes( $color, $size, $type, $extra );
		}

		/**
		 * Calls the framework-specific clearfix() function
		 */
		function clearfix() {
			return $this->fw->clearfix();
		}

		/**
		 * Calls the framework-specific alert() function
		 */
		function alert( $type = 'info', $content = '', $id = null, $extra_classes = null, $dismiss = false ) {
			$this->fw->alert( $type, $content, $id, $extra_classes, $dismiss );
		}
	}
	$frameworks = new Shoestrap_Framework();
}