<?php
/**
 * Created by PhpStorm.
 * User: gerasart
 * Date: 9/18/2019
 * Time: 3:16 PM
 */

namespace WplExtend;


class LangFilters {

    public function __construct() {
        self::adjacentLangSettingFilter();
    }

    public static function adjacentLangSettingFilter() {
        $adjacents = [ 'next', 'previous' ];
        foreach ( $adjacents as $adjacent ) {
            add_filter( "get_{$adjacent}_post_join", [ __CLASS__, 'adjacentJoinByLanguage' ] );
            add_filter( "get_{$adjacent}_post_where", [ __CLASS__, 'adjacentWhereByLanguage' ] );
        }
    }

    public static function adjacentJoinByLanguage( $join ) {
        $langQuery = self::getLangMetaQuery();

        return $join . $langQuery['join'];
    }

    public static function adjacentWhereByLanguage( $where ) {
        $langQuery = self::getLangMetaQuery();

        return $where . $langQuery['where'];
    }

    public static function getLangMetaQuery() {
        global $wpdb;
        $lang = wpm_get_language();

        $lang_meta_query = array(
            array(
                'relation' => 'OR',
                array(
                    'key'     => '_languages',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => '_languages',
                    'value'   => serialize( $lang ),
                    'compare' => 'LIKE',
                ),
            ),
        );

        $mq = new \WP_Meta_Query( $lang_meta_query );

//      return $mq->get_sql('post', $wpdb->posts . ' as p', 'ID');
        return $mq->get_sql( 'post', 'p', 'ID' );
    }
}