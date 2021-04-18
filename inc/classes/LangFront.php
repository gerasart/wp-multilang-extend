<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 9/17/2019
 * Time: 2:46 PM
 */

namespace WplExtend;

use WplExtend\helpers\AjaxHelper;

class LangFront extends AjaxHelper
{
    static $option_lang = 'wpm_languages';

    public function __construct()
    {
        add_action('template_redirect', [__CLASS__, 'RedirectLang'], 5);
    }

    public static function RedirectLang() : void
    {
        $langs                          = self::removeLangFromList();
        self::$front_vars['hide_langs'] = $langs;

        $current_link = $_SERVER['REQUEST_URI'];
        $redirect     = '';

        if (count($langs)) {
            foreach ($langs as $lang) {
                if ($lang === wpm_get_language()) {
                    $redirect = str_replace($lang.'/', '', $current_link);
                    break;
                }
            }
            if ($redirect) {
                wp_redirect($redirect);
            }
        }
    }
    
    public static function removeLangFromList()
    {
        $list      = [];
        $languages = get_option(self::$option_lang, array());

        foreach ($languages as $key => $language) {
            if (isset($language['view']) && $language['view'] === '0' && ! is_user_logged_in()) {
                $list[] = $key;
            }
        }

        return $list;
    }
}