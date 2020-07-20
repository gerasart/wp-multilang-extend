<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 1/23/2019
 * Time: 2:01 PM
 */

namespace WplExtend;


class setupPlugin {

    static $fields = [ 'text', 'textarea' ];

    public function __construct() {

        if ( self::isSettingPage() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin' ) );
        } else {
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'themeAssets' ) );
        }

        add_filter( 'wpm_admin_pages', array( __CLASS__, 'addAcfOptionsPage' ) );


        foreach ( self::$fields as $field_type ) {
            add_action( "acf/render_field_settings/type={$field_type}", array( __CLASS__, 'addTranslateOption' ) );
            add_filter( "wpm_acf_{$field_type}_config", array( __CLASS__, 'allowFieldTranslate' ), 11, 4 );
        }

        self::cptUiRegister();
    }

    public function enqueue_admin() {
        wp_enqueue_script( 'WplExtend', MULTI_URL . 'inc/js/bundle.js', '', '33' );
    }

    public static function themeAssets() {
        wp_enqueue_script( 'multilang/extand', MULTI_URL . 'inc/js/front.js', [ 'jquery' ], null, true );
    }
    
    public static function isSettingPage() {
        if ( $_SERVER['QUERY_STRING'] === 'page=wpm-settings&tab=list' ) {
            return true;
        } else {
            return false;
        }
    }


    public static function cptUiRegister() {
        if ( function_exists( 'cptui_get_post_type_data' ) ) {
            $post_types = cptui_get_post_type_data();
            foreach ( array_keys( $post_types ) as $post_type ) {
                add_filter( "wpm_post_{$post_type}_config", array( __CLASS__, 'allowTranslate' ) );
            }
        }

        if ( function_exists( 'cptui_get_taxonomy_data' ) ) {
            $taxonomies = cptui_get_taxonomy_data();
            foreach ( array_keys( $taxonomies ) as $taxonomy ) {
                add_filter( "wpm_taxonomy_{$taxonomy}_config", array( __CLASS__, 'allowTranslate' ) );
            }
        }
    }

    public static function allowTranslate() {
        return array();
    }

    public static function allowFieldTranslate( $acf_field_config, $value, $post_id, $field ) {
        if ( isset( $field["disallow_translate"] ) && $field["disallow_translate"] ) {
            return null;
        } else {
            return $acf_field_config;
        }
    }

    public static function addAcfOptionsPage( $admin_pages ) {
        if ( function_exists( 'acf_options_page' ) ) {
            $pages = acf_options_page()->get_pages();

            if ( $pages ) {
                foreach ( $pages as $key => $page ) {
                    if ( empty( $page['parent_slug'] ) ) {
                        $page_id = 'toplevel_page_' . $key;
                    } else {
                        $page_id = $page['parent_slug'] . '_page_' . $key;
                    }

                    $admin_pages[] = $page_id;
                }
            }
        }

        return $admin_pages;
    }

    public static function addTranslateOption( $field ) {
        acf_render_field_setting( $field, array(
            'label'        => __( 'Disallow translation', 'Theme' ),
            'instructions' => '',
            'type'         => 'true_false',
            'name'         => 'disallow_translate',
            'ui'           => 1,
        ) );
    }


    /* Debug functions */
    public static function debug( $var ) {
        echo "<pre>";
        var_dump( $var );
        echo "</pre>";
    }

}