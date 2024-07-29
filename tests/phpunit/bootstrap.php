<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package GP_Toolbox
 */


if ( ! defined( 'GP_TESTS_PERMALINK_STRUCTURE' ) ) {
	define( 'GP_TESTS_PERMALINK_STRUCTURE', '/%postname%' );
}

$_tests_dir    = getenv( 'WP_TESTS_DIR' );
$_gp_tests_dir = getenv( 'GP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! $_gp_tests_dir ) {
	$_gp_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress/wp-content/plugins/glotpress/tests/phpunit';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path ); // phpcs:ignore
}

if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	exit( 1 );
}

if ( ! file_exists( $_gp_tests_dir . '/bootstrap.php' ) ) {
	die( "GlotPress test suite could not be found. Have you run bin/install-wp-tests.sh ?\n" );
}

// Give access to tests_add_filter() function.
require_once "{$_tests_dir}/includes/functions.php";


/**
 * Manually load the plugin being tested.
 */
function gp_toolbox_manually_load_plugin() {

	// Load GlotPress.
	require dirname( dirname( __DIR__ ) ) . '/../glotpress/glotpress.php';

	// Load GP-Toolbox.
	require dirname( dirname( __DIR__ ) ) . '/gp-toolbox.php';
}

tests_add_filter( 'muplugins_loaded', 'gp_toolbox_manually_load_plugin' );

global $wp_tests_options;

// So GlotPress doesn't bail early, see https://github.com/GlotPress/GlotPress-WP/blob/43bb5383e114835b09fc47c727d06e6d3ca8114e/glotpress.php#L142-L152.
$wp_tests_options['permalink_structure'] = '/%postname%';

// Start up the WP testing environment.
require "{$_tests_dir}/includes/bootstrap.php";

require_once $_gp_tests_dir . '/lib/testcase.php';
require_once $_gp_tests_dir . '/lib/testcase-route.php';
require_once $_gp_tests_dir . '/lib/testcase-request.php';

/**
 * Installs GlotPress tables.
 */
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once $_gp_tests_dir . '/../../gp-includes/schema.php';
require_once $_gp_tests_dir . '/../../gp-includes/install-upgrade.php';
gp_upgrade_db();
