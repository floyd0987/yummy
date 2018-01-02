<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www
 * @since      1.0.0
 *
 * @package    Yummy
 * @subpackage Yummy/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Yummy
 * @subpackage Yummy/public
 * @author     Eronne Bernucci <eronneb@gmail.com>
 */
class Yummy_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;


	//private $yummy_booking_post_meta = array('yummy_order_date','yummy_guests_number' );

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode('yummy', array($this,'yummy_shortcode'));

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yummy_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yummy_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/yummy-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Yummy_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Yummy_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/yummy-public.js', array( 'jquery' ), $this->version, false );

		wp_register_script( 'yummy-plugin-main', plugin_dir_url( __FILE__ ) . 'js/main.js', NULL, "1.0",true );
		wp_enqueue_script( 'yummy-plugin-main' );

		wp_localize_script( 'yummy-plugin-main', 'wpApiSettings', array(
			'root' => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
			'yummy_booking_post_meta' => YUMMY_BOOKING_POST_META
		) );



	}


	public function yummy_shortcode() {
			?>

		<form>

		<?php foreach (YUMMY_BOOKING_POST_META as $key => $value) {
					$input_type = Yummy::returnFieldType($value); ?>

			<div class="form-group">
				<label for="<?php echo $value; ?>"><?php echo $value; ?></label>
				<input type="<?php echo $input_type; ?>" class="form-control" name="<?php echo $value; ?>" id="<?php echo $value; ?>" aria-describedby="<?php echo $value; ?>-help" placeholder="">
				<!-- <small id="<?php echo $value; ?>-help" class="form-text text-muted">You must add a <?php echo $value; ?></small> -->
			</div>

		<?php
			} ?>


		<div class="form-group">
			<label for="yummy-content-input">Note</label>
			<textarea class="form-control" id="yummy-content-input" name="yummy-content" aria-describedby="content-input-help" rows="3"></textarea>

		</div>








		<button id='yummy-button' type="button" class="btn btn-primary">Submit</button>
	</form>

	<br>
	<div id='yummy-user-output'>yummy-user-output</div>
	<div id='yummy-output'></div>
	<?php
	}









}
