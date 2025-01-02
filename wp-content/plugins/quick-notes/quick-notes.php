<?php

/**
 * Plugin Name: Quick Notes
 * Description: Quick Notes base functionality
 *
 * Plugin URI:  https://latul.website
 * Author URI:  https://latul.website
 * Author:      Anton
 *
 * Text Domain: wbzx-tdl
 * Domain Path: /lang
 *
 * Requires at least: 6.7
 * Requires PHP: 8.0
 *
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Network:     true
 * Update URI:  https://example.com/link_to_update
 *
 *
 * Version:     1.0
 */
defined( 'ABSPATH' ) OR exit;
define( 'WBZX_TDL_PATH', plugin_dir_path( __FILE__ ) );
define( 'WBZX_TDL_URL', plugin_dir_url( __FILE__ ) );
//autoload
require_once "autoload.php";
require_once WBZX_TDL_PATH.DS.'includes'.DS.'assets.php';
use WBZXTDL\App\Core\DB\DB as DB;
use WBZXTDL\App\Core\Logger\Logger as Logger;
use WBZXTDL\App\Core\AdminPanel\AdminPanel as AdminPanel;

register_activation_hook(   __FILE__, array( 'WBZXTDL', 'activation' ) );
register_deactivation_hook( __FILE__, array( 'WBZXTDL', 'deactivation' ) );
register_uninstall_hook(    __FILE__, array( 'WBZXTDL', 'uninstall' ) );

class WBZXTDL {
    protected static $instance;
    public static function init() {
        is_null(self::$instance) && self::$instance = new self;
        return self::$instance;
    }

    public function __construct() {
        add_action('admin_menu', [$this, 'addAdminMenu']);
    }
    public static function activation() {
        $logger = new Logger();
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            DB::init($pdo, $logger);

            DB::createTable();

        } catch (Exception $e) {
            $logger->error(__('Error plugin activated: ', 'wbzx-tdl') . $e->getMessage());
            wp_die(__('Plugin database not inited.', 'wbzx-tdl'));
        }
    }

    public static function deactivation(){

    }

    public static function uninstall(){

    }

    public function addAdminMenu() {
        $adminPanel = new AdminPanel();
        add_menu_page(
            'Your quick notes',
            'Quick Notes',
            'manage_options',
            'wbzx-tdl',
            [$adminPanel, 'renderPage'],
            'dashicons-randomize',
            30
        );
    }
}
$plugin = new WBZXTDL();