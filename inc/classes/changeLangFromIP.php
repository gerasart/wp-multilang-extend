<?php
    /**
     * Created by PhpStorm.
     * User: gerasart
     * Date: 24.04.2020
     * Time: 12:00
     */
    
    namespace WplExtend;
    
    use ParagonIE\Sodium\Core\Curve25519\Ge\P1p1;
    use WplExtend\helpers\AjaxHelper;
    
    class changeLangFromIP extends AjaxHelper {
        
        public function __construct() {
            self::declaration_ajax();
            
            add_action( 'wp', array( $this, 'wp_init' ) );
            add_action( 'wpm_init', array( $this, 'wpm_init' ) );
        }
        
        public static function addOption() {
            
            $check_from_ip   = get_option( 'check_from_ip' );
            $check_from_site = get_option( 'check_from_site' );
            if ( !$check_from_ip ) {
                add_option( 'check_from_ip', '0' );
            }
            
            if ( !$check_from_site ) {
                add_option( 'check_from_site', '0' );
            }
            
            self::$admin_vars['check_from_ip']   = get_option( 'check_from_ip' );
            self::$admin_vars['check_from_site'] = get_option( 'check_from_site' );
            
        }
        
        public function wp_init() {
            self::switchFrontLang();
        }
        
        public function wpm_init() {
            self::changeLangFromSite();
            self::addOption();
            self::getCurrentLangs();
        }
        
        public function ajax_check_from_ip() {
            $data = $_POST['data'];
            
            update_option( 'check_from_ip', $data );
            
            wp_send_json_success();
        }
        
        public function ajax_check_from_site() {
            $data = $_POST['data'];
            
            update_option( 'check_from_site', $data );
            
            wp_send_json_success();
        }
        
        public static function switchFrontLang() {
            $is_lang       = get_option( 'check_from_ip' );
            $is_session_id = session_id();
            $session       = $is_session_id ? $_SESSION['lang_redirect'] : '0';
            if ( $is_lang === "1" && $session === '0' ) {
                self::changeLang();
                if ( $is_session_id ) {
                    $_SESSION['lang_redirect'] = '1';
                }
            }
        }
        
        public static function changeLangFromSite() {
            $session_id = session_id();
            
            if ( !$session_id ) {
                session_start();
                $session_id = '1';
            }
            
            $check_from_site = get_option( 'check_from_site' );
            
            if ( $check_from_site === '1' && $session_id ) {
                $session = isset( $_SESSION['lang_redirect'] ) ? $_SESSION['lang_redirect'] : '';
                if ( empty( $session ) ) {
                    $_SESSION['lang_redirect'] = '0';
                }
            } else {
                session_destroy();
            }
        }
        
        public static function changeLang() {
            $curr_langs       = get_option( 'wpm_languages', array() );
            $current_ip_lang  = self::checkUserIP();
            $current_url      = wpm_get_current_url();
            $active_lang      = wpm_get_user_language();
            $url              = '';
            $current_geo_lang = '';
            if ( isset($current_ip_lang->geoplugin_countryCode) && $current_ip_lang->geoplugin_countryCode !== NULL ) {
                foreach ( $curr_langs as $key => $lang ) {
                    $current_geo_lang = strtolower( $current_ip_lang->geoplugin_countryCode );
                    if ( $key === $current_geo_lang && isset( $lang['view'] ) && $lang['view'] === '1' ) {
                        $url = wpm_translate_url( $current_url, $key );
                        break;
                    }
                }
            }
            
            if ( $url ) {
                if ( $active_lang != $current_geo_lang ) {
                    wp_redirect( $url, 301 );
                }
            }
        }
        
        public static function getCurrentLangs() {
            $lang      = wpm_get_languages();
            $lang_keys = [];
            foreach ( $lang as $key => $value ) {
                $lang_keys[] = $key;
            }
            
            return $lang_keys;
        }
        
        public static function checkUserIP() {
            if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            if ( $ip ) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip={$ip}");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $get_content = curl_exec($ch);
//                $get_content = file_get_contents( "http://www.geoplugin.net/json.gp?ip={$ip}" );
                if ( $get_content ) {
                    $details = json_decode( $get_content );
                }
            } else {
                $details = [];
            }
            
            return (object)$details;
        }
        
        
    }