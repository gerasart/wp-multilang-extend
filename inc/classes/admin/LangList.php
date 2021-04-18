<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: skipin
 * Date: 31.10.18
 * Time: 10:34
 */

namespace WplExtend\admin;


use WplExtend\helpers\AjaxHelper;

class LangList extends AjaxHelper
{
    static $tabs = ['list' => 'Languages list'];
    
    static $option_lang = 'wpm_languages';
    
    static $languages;
    
    public function __construct()
    {
        add_action('admin_head', array(__CLASS__, 'localAdminVars'));
        add_action('wp_head', array(__CLASS__, 'localFrontVars'));
        
        add_filter('wpm_settings_tabs_array', array(__CLASS__, 'addNewTab'), 22);
        add_action('wpm_sections_list', array(__CLASS__, 'outputList'));
        
        self::declaration_ajax();
        
        self::getLang();
    }
    
    public static function addNewTab($pages)
    {
        foreach (self::$tabs as $name => $title) {
            $pages[$name] = $title;
        }
        
        return $pages;
    }
    
    public static function getLang() : void
    {
        $languages = get_option(self::$option_lang, array());
        
        self::$admin_vars['languages'] = $languages;
    }
    
    public static function outputList() : void
    {
        echo '<h2>Languages List</h2>';
        echo '<div id="app"></div>';
    }
    
    public function ajax_send_lang() : void
    {
        $data = $_POST['data'];
        
        $languages = get_option(self::$option_lang, array());
        
        $exclude = ['code', 'status'];
        
        if ($data && ! array_key_exists($data['code'], $languages)) {
            $code             = $data['code'];
            $languages[$code] = [];
            
            foreach ($data as $key => $value) {
                if ( ! in_array($key, $exclude)) {
                    $languages[$code][$key] = $value;
                }
            }
        }
        
        update_option(self::$option_lang, $languages);
        
        wp_send_json_success(['languages' => $languages]);
    }
    
    public function ajax_view_lang() : void
    {
        $data = $_POST['data'];
        $view = $_POST['view'];
        
        $languages = get_option(self::$option_lang, array());
        
        $code                     = $data['code'];
        $languages[$code]['view'] = $view;
        
        update_option(self::$option_lang, $languages);
        
        wp_send_json_success($languages[$code]);
    }
    
    public static function ajax_remove_lang() : void
    {
        $data      = $_POST['data'];
        $languages = get_option(self::$option_lang, array());
        $code      = $data['code'];
        
        unset($languages[$code]);
        
        update_option(self::$option_lang, $languages);
        
        wp_send_json_success();
    }
}