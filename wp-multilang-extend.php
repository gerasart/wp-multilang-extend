<?php
/*
 * Plugin Name: Multilang Extend
 * Version: 3.0
 * Plugin URI: https://cs50.com.ua/resheniya/wordpress-plugins/multilang-extend
 * Description: developer plugin.
 * Author: Gerasart
 * Author URI: https://www.facebook.com/gerasymenkoart
 */

if ( !defined( 'ABSPATH' ) ) exit;

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

use HaydenPierce\ClassFinder\ClassFinder;

define('MULTI_PATH', plugin_dir_path( __FILE__ ));
define('MULTI_URL', plugin_dir_url( __FILE__ ));

class MultilangExtend {

    private static $basedir;
    private static $namespace = 'WplExtend';

    public function __construct() {

        self::$basedir = plugin_dir_path( __FILE__ ) . 'inc/classes/';

        self::cc_autoload();
    }

    private static function cc_autoload() {

	    $namespaces = self::getDefinedNamespaces();
        foreach ($namespaces as $namespace => $path) {

	        $clear = substr($namespace, 0, strlen($namespace) - 1);

	        ClassFinder::setAppRoot( MULTI_PATH);
            $level = error_reporting(E_ERROR);
            $classes = ClassFinder::getClassesInNamespace( $clear );
            error_reporting($level);

            foreach ( $classes as $class ) {
                new $class();
            }
        }
    }

    private static function getDefinedNamespaces()
    {
        $composerJsonPath = dirname( __FILE__ ) . '/composer.json';

	    $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array) $composerConfig->autoload->$psr4;
    }
}

new MultilangExtend();

/** @class github updater */

//require 'vendor/updater/plugin-update-checker.php';
//$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/gerasart/developer-helper/',__FILE__,'developer-helper');
//$myUpdateChecker->setAuthentication('a283aeca2b507dd9d43b8e5b0cf8f6a3e8be50ad');
//$myUpdateChecker->setBranch('master');
//$myUpdateChecker->getVcsApi()->enableReleaseAssets();