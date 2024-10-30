<?php

/*
Plugin Name: Sign-Up Form for CleverReach Newsletter
Plugin URI: https://wordpress.org/plugins/cleverreach/
Description: Easily integrate a CleverReach Sign-Up Form in your website.
Version: 2.3.5
Author: CleverReach GmbH & Co. KG
Author URI: https://www.cleverreach.com
Text Domain: cleverreach
License: GPLv2 or later
*/

use CleverReach\Newsletter\CleverReach;

require_once plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

CleverReach::init( __FILE__ );
