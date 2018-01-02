<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www
 * @since      1.0.0
 *
 * @package    Yummy
 * @subpackage Yummy/includes
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
 * @since      1.0.0
 * @package    Yummy
 * @subpackage Yummy/includes
 * @author     Eronne Bernucci <eronneb@gmail.com>
 */
class Yummy
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Yummy_Loader    $loader    Maintains and registers all hooks for the plugin.
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
    public function __construct()
    {
        if (defined('PLUGIN_NAME_VERSION')) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'yummy';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();




        add_action('init', array($this, 'create_post_types'));
        add_action('rest_api_init', array($this,'register_yummy_post_meta'));

				add_filter( 'rest_prepare_yummy-booking', array($this,'custom_json_fields'), 12, 3  );

        add_filter( 'rest_prepare_yummy-menu', array($this,'custom_json_fields_menu'), 12, 3  );

        // add_action('admin_head-edit.php', array($this,'add_scheduler_in_booking_list' ));

        add_action( 'admin_menu', array($this,'my_admin_menu' ));




    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Yummy_Loader. Orchestrates the hooks of the plugin.
     * - Yummy_i18n. Defines internationalization functionality.
     * - Yummy_Admin. Defines all hooks for the admin area.
     * - Yummy_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-yummy-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-yummy-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-yummy-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-yummy-public.php';



        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-yummy-meta-box.php';

        //plugins
        require_once plugin_dir_path(dirname(__FILE__)) . 'plugins/wp-api-meta-data.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'plugins/basic-auth.php';





        $this->loader = new Yummy_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Yummy_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Yummy_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Yummy_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Yummy_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Yummy_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }



    // YUMMY METHODS
    public function create_post_types()
    {
        register_post_type(
                'yummy-booking',
                array(
                    'labels' => array(
                        'name' => __('Booking'),
                        'singular_name' => __('Booking')
                    ),
                    'public' => true,
                    'has_archive' => true,
                    'supports' =>array('title','editor','custom-fields'),
                    'show_in_rest' => true,
                )
            );


        register_post_type(
                'yummy-tables',
                array(
                    'labels' => array(
                        'name' => __('Tables'),
                        'singular_name' => __('Table')
                    ),
                    'public' => true,
                    'has_archive' => true,
                    'supports' =>array('title','custom-fields'),
                    'show_in_rest' => true,
                )
            );


            register_post_type(
                    'yummy-menu',
                    array(
                        'labels' => array(
                            'name' => __('Menu'),
                            'singular_name' => __('Menu')
                        ),
                        'public' => true,
                        'has_archive' => true,
                        'supports' =>array('title','editor','thumbnail','custom-fields'),
                        'show_in_rest' => true,
                    )
                );

    }








    public function register_yummy_post_meta()
    {
        $yummy_booking_post_meta = YUMMY_BOOKING_POST_META;

        foreach ($yummy_booking_post_meta as $post_meta) {
            register_rest_field(
                    'yummy-booking',
                    $post_meta,
                        array(
                                // 'get_callback'    => function ( $object, $field_name, $request ) {
                                // 											return get_post_meta( $object[ 'id' ], $field_name );
                                // 									},

                                'update_callback' => function ($value, $object, $field_name) {
                                    if (! $value || ! is_string($value)) {
                                        return;
                                    }
                                    return update_post_meta($object->ID, $field_name, strip_tags($value));
                                },

                                'schema'          => null,
                        )
                );
        }



        register_rest_field(
                'user',
                'user_email',
                array(
                    'get_callback'    => function ($user) {
                        return $user['email'];
                    },
                    'update_callback' => null,
                    'schema'          => null,
                )
            );
    }






		public function custom_json_fields( $data, $post, $context ) {

			// $timestamp = strtotime($data->data['meta']['yummy_order_date']) + 60*90;
			// $time_end = date('Y-m-d H:i', $timestamp);

		    $data =  [

		        // 'id'    => $data->data['id'],
		        // 'date'     => $data->data['date'],
						// 'title'     => $data->data['title']['rendered'],
						// 'content'     => $data->data['content']['rendered'],
						// 'meta'     => $data->data['meta']




						// 'id'    => $data->data['id'],
		        // 'start_date'     => $data->data['meta']['yummy_order_date'] . ' ' . $data->data['meta']['yummy_order_time'],
						// 'end_date'     => $data->data['meta']['yummy_order_date'] . ' ' . $time_end,
						// 'text'     => strip_tags($data->data['title']['rendered']) . ' ' . strip_tags($data->data['meta']['yummy_guests_number']) . ' ospiti',
						// 'details'  => strip_tags($data->data['content']['rendered']) .  strip_tags($data->data['meta']['yummy_user_email']),

            'id'    => $data->data['id'],
            'title'     => $data->data['meta']['yummy_user_lastname'] . ' ' . $data->data['meta']['yummy_user_name'] . ' <br />' . strip_tags($data->data['meta']['yummy_guests_number']) . ' ospiti',
						'content'     => strip_tags($data->data['content']['rendered']),
		        'start_date'     => $this->dateFormat( $data->data['meta']['yummy_order_start_date'] ) ,
						'end_date'     =>  $this->dateFormat( $data->data['meta']['yummy_order_end_date'] ) ,
						'text'     => $data->data['meta']['yummy_user_lastname'] . ' ' . $data->data['meta']['yummy_user_name'] . ' <br />' . strip_tags($data->data['meta']['yummy_guests_number']) . ' ospiti',
						// 'details'  => strip_tags($data->data['content']['rendered']) .  strip_tags($data->data['meta']['yummy_user_email']),

            'yummy_user_name' => $data->data['meta']['yummy_user_name'],
            'yummy_user_lastname' => $data->data['meta']['yummy_user_lastname'],
            'yummy_user_email' => $data->data['meta']['yummy_user_email'],
            'yummy_user_telephone' => $data->data['meta']['yummy_user_telephone'],
            'yummy_guests_number' => $data->data['meta']['yummy_guests_number'],


		    ];

				return $data;

		}




    public function custom_json_fields_menu( $data, $post, $context ) {



		    $data =  [

		        'id'    => $data->data['id'],
		        'date'     => $data->data['date'],
						'title'     => $data->data['title']['rendered'],
						'content'     => $data->data['content']['rendered'],
						'meta'     => $data->data['meta'],
            'image' =>  wp_get_attachment_image_src( get_post_thumbnail_id( $data->data['id'] ), 'full' )[0]



		    ];

				return $data;

		}



    public function dateFormat($date) {
       $timestamp = strtotime($date);
			 $date = date('Y-m-d H:i', $timestamp);
       return $date;
    }




    public function returnFieldType($field)
    {
        switch ($field) {
            case 'yummy_order_start_date': $input_type="datetime-local"; break;
            case 'yummy_order_end_date': $input_type="datetime-local"; break;
            case 'yummy_guests_number': $input_type="number"; break;
            case 'yummy_user_email': $input_type="email"; break;

            default: $input_type="text"; break;
        }
        return $input_type;
    }

    public function returnCleanField($field)
    {
        $field = str_replace("yummy", " ", $field) ;
        $field = ucwords(str_replace("_", " ", $field)) ;

        return $field;
    }






    function my_admin_menu() {
    	add_menu_page( 'Scheduler', 'Scheduler', 'manage_options', 'yummy/includes/scheduler-admin-page.php', array($this,'add_scheduler_in_booking_list'), 'dashicons-calendar',30  );
    }



    public function add_scheduler_in_booking_list()  {

        // sample code, handy for custom post types
        global $post_type;

        if ($post_type!='yummy-booking') {
            //return;
        } ?>


		<div id="wpwrap-scheduler" >
			<div id="wpcontent-scheduler">
				<div id="wpcontent-inner">

					<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:700px;'>
						<div class="dhx_cal_navline">
							<div class="dhx_cal_prev_button">&nbsp;</div>
							<div class="dhx_cal_next_button">&nbsp;</div>
							<div class="dhx_cal_today_button"></div>
							<div class="dhx_cal_date"></div>
							<div class="dhx_cal_tab" name="day_tab" style="right:204px;"></div>
							<div class="dhx_cal_tab" name="week_tab" style="right:140px;"></div>
							<div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
						</div>
						<div class="dhx_cal_header">
						</div>
						<div class="dhx_cal_data">
						</div>
					</div>



				</div>
			</div>
		</div>

	<?php
    }
}
