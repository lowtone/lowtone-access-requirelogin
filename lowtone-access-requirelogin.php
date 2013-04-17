<?php
/*
 * Plugin Name: Require Login
 * Plugin URI: http://wordpress.lowtone.nl/plugins/access-requirelogin/
 * Description: Make the blog private by requiring users to log in.
 * Version: 1.0
 * Author: Lowtone <info@lowtone.nl>
 * Author URI: http://lowtone.nl
 * License: http://wordpress.lowtone.nl/license
 */
/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2011-2012, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\plugins\lowtone\access\requirelogin
 */

namespace lowtone\access\requirelogin {

	use lowtone\ui\forms\Form,
		lowtone\ui\forms\Input,
		lowtone\content\packages\Package;

	// Includes
	
	if (!include_once WP_PLUGIN_DIR . "/lowtone-content/lowtone-content.php") 
		return trigger_error("Lowtone Content plugin is required", E_USER_ERROR) && false;

	// Init

	Package::init(array(
			Package::INIT_PACKAGES => array("lowtone"),
			Package::INIT_MERGED_PATH => __NAMESPACE__,
			Package::INIT_SUCCESS => function() {

				add_action("template_redirect", function() {
					if (is_user_logged_in() || !requireLogin())
						return;

					auth_redirect();

					exit;
				});

				add_action("admin_init", function() {

					register_setting("reading", "require_login");

					add_settings_section("lowtone_access_requirelogin", __("Require login", "lowtone_access_requirelogin"), function() {
						echo '<p>' . __("Require users to log in to access the blog.", "lowtone_access_requirelogin") . '</p>';
					}, "reading");

					$form = new Form();

					add_settings_field("require_login", __("Require login", "lowtone_access_requirelogin"), function() use ($form) {
						
						$form
							->createInput(Input::TYPE_CHECKBOX, array(
								Input::PROPERTY_NAME => "require_login",
								Input::PROPERTY_VALUE => "1",
								Input::PROPERTY_SELECTED => requireLogin()
							))
							->addClass("setting")
							->out();

					}, "reading", "lowtone_access_requirelogin");
					
				});
				
				add_action("plugins_loaded", function() {
					load_plugin_textdomain("lowtone_access_requirelogin", false, basename(__DIR__) . "/assets/languages");
				});

			}
		));

	// Functions
	
	function requireLogin() {
		return (bool) get_option("require_login");
	}

}