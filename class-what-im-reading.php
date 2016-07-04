<?php

/**
 * class-what-im-reading.php
 *
 * @package   what-im-reading
 * @copyright Copyright (c) 2015, Ashley Evans
 * @license   GPL2+
 */
class What_Im_Reading {

	/**
	 * The single instance of the plugin.
	 * @var Naked_Social_Share
	 * @since 1.0
	 */
	private static $_instance = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0
	 */
	public $_version;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0
	 */
	public $assets_url;

	public function __construct( $file = '', $version = '1.0' ) {
		// Load plugin environment variables.
		$this->_version = $version;

		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( 'assets/', $this->file ) ) );

		// Include necessary files.
		$this->includes();

		// Create the widget.
		add_action( 'widgets_init',
			create_function( '', 'return register_widget("What_Im_Reading_Widget");' )
		);

		// Load front end JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts_styles' ) );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		// Display admin notice
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
	}

	/**
	 * Sets up the main What_Im_Reading instance
	 *
	 * @access public
	 * @since  1.0
	 * @return What_Im_Reading
	 */
	public static function instance( $file = '', $version = '1.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	}

	/**
	 * Load the plugin language files.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$domain = 'what-im-reading';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, plugin_basename( $this->file ) . '/lang/' );
	}

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'what-im-reading', false, plugin_basename( $this->file ) . '/lang/' );
	}

	/**
	 * Includes the necessary files.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function includes() {
		require_once plugin_dir_path( __FILE__ ) . 'widget-what-im-reading.php';
	}

	/**
	 * Adds the CSS and JavaScript for the plugin.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function add_scripts_styles() {
		wp_enqueue_style( 'what-im-reading-frontend', esc_url( $this->assets_url ) . 'css/what-im-reading.css', array(), $this->_version );
	}

	/**
	 * Displays an admin notice if UBB is activated.
	 *
	 * @access public
	 * @since  1.0
	 * @return void
	 */
	public function admin_notice() {
		if ( ! class_exists( 'UBB_What_Im_Reading_Widget' ) ) {
			return;
		}

		?>
		<div class="update-nag">
			<?php _e( 'You have the Ultimate Book Blogger Plugin installed, which has the Goodreads Shelf widget already built in. You can safetly deactivate the Goodreads Shelf plugin and use UBB instead.', 'what-im-reading' ); ?>
		</div>
		<?php
	}

}