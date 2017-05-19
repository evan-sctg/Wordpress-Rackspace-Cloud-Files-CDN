<?php

/**
* RACKSPACE_CDN
*
* @since 0.0.1
*/

class RACKSPACE_CDN
{


	/**
	* pseudo-constructor
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	public static function instance() {
		new self();
	}


	/**
	* constructor
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	public function __construct() {

        /* CDN rewriter hook */
        add_action(
            'template_redirect',
            array(
                __CLASS__,
                'handle_rewrite_hook'
            )
        );

		/* Hooks */
		add_action(
			'admin_init',
			array(
				__CLASS__,
				'register_textdomain'
			)
		);
		add_action(
			'admin_init',
			array(
				'RACKSPACE_CDN_SETTINGS',
				'register_settings'
			)
		);
		add_action(
			'admin_menu',
			array(
				'RACKSPACE_CDN_SETTINGS',
				'add_settings_page'
			)
		);
        add_filter(
            'plugin_action_links_' .RACKSPACE_CDN_BASE,
            array(
                __CLASS__,
                'add_action_link'
            )
        );

        /* admin notices */
        add_action(
            'all_admin_notices',
            array(
                __CLASS__,
                'RACKSPACE_CDN_requirements_check'
            )
        );

	}



	/**
	* add action links
	*
	* @since   0.0.1
	* @change  0.0.1
	*
	* @param   array  $data  alreay existing links
	* @return  array  $data  extended array with links
	*/

	public static function add_action_link($data) {
		// check permission
		if ( ! current_user_can('manage_options') ) {
			return $data;
		}

		return array_merge(
			$data,
			array(
				sprintf(
					'<a href="%s">%s</a>',
					add_query_arg(
						array(
							'page' => 'RACKSPACE_CDN'
						),
						admin_url('options-general.php')
					),
					__("Settings")
				)
			)
		);
	}


	/**
	* run uninstall hook
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	public static function handle_uninstall_hook() {
        delete_option('RACKSPACE_CDN');
	}


	/**
	* run activation hook
	*
	* @since   0.0.1
	* @change  1.0.3
	*/

	public static function handle_activation_hook() {
        add_option(
            'RACKSPACE_CDN',
            array(
                'url' => get_option('home'),
                'username' => '',
                'apikey' => '',
                'container' => '',
                'dirs' => 'wp-content/uploads',
                'excludes' => '.php',
                'localurls' => '1',
                'https' => ''
            )
        );
	}


	/**
	* check plugin requirements
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	public static function RACKSPACE_CDN_requirements_check() {
		// WordPress version check
		if ( version_compare($GLOBALS['wp_version'], RACKSPACE_CDN_MIN_WP.'alpha', '<') ) {
			show_message(
				sprintf(
					'<div class="error"><p>%s</p></div>',
					sprintf(
						__("Rackspace CDN is built for WordPress %s. Please upgrade your WordPress installation.", "cdn-cloudfiles-rackspace"),
						RACKSPACE_CDN_MIN_WP
					)
				)
			);
		}
	}


	/**
	* register textdomain
	*
	* @since   1.0.3
	* @change  1.0.3
	*/

	public static function register_textdomain() {

		load_plugin_textdomain(
			'cdn-cloudfiles-rackspace',
			false,
			'cdn-cloudfiles-rackspace/lang'
		);
	}


	/**
	* return plugin options
	*
	* @since   0.0.1
	* @change  1.0.3
	*
	* @return  array  $diff  data pairs
	*/

	public static function get_options() {
		return wp_parse_args(
			get_option('RACKSPACE_CDN'),
			array(
                'url' => get_option('home'),
                'username' => '',
                'apikey' => '',
                'container' => '',
                'dirs' => 'wp-content/uploads',
                'excludes' => '.php',
                'localurls' => 1,
                'https' => 1
			)
		);
	}


    /**
	* run rewrite hook
	*
	* @since   0.0.1
	* @change  1.0.3
	*/

    public static function handle_rewrite_hook() {
        $options = self::get_options();

        // check if origin equals cdn url
        if (get_option('home') == $options['url']) {
    		return;
    	}

        $excludes = array_map('trim', explode(',', $options['excludes']));

    	$rewriter = new RACKSPACE_CDN_REWRITER(
    		get_option('home'),
    		$options['url'],
    		$options['apikey'],
    		$options['username'],
    		$options['container'],
    		$options['dirs'],
    		$excludes,
    		$options['localurls'],
    		$options['https']
    	);
    	ob_start(
            array(&$rewriter, 'rewrite')
        );
    }

}
