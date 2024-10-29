<?php
/*
Plugin Name: Alobaidi Timeline
Plugin URI: http://wp-plugins.in/Alobaidi_Timeline
Description: Make timeline page in 1 minute! Just one shortcode, pagination support with text translation, custom list type, easy to use.
Version: 1.0.0
Author: Alobaidi
Author URI: http://wp-plugins.in
License: GPLv2 or later
*/

/*  Copyright 2016 Alobaidi (email: wp-plugins@outlook.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function Alobaidi_Timeline_plugin_row_meta( $links, $file ) {

    if ( strpos( $file, 'alobaidi-timeline.php' ) !== false ) {
        
        $new_links = array(
                        '<a href="http://wp-plugins.in/Alobaidi_Timeline" target="_blank">Explanation of Use</a>',
                        '<a href="https://profiles.wordpress.org/alobaidi#content-plugins" target="_blank">More Plugins</a>',
                        '<a href="http://j.mp/ET_WPTime_ref_pl" target="_blank">Elegant Themes</a>'
                    );
        
        $links = array_merge( $links, $new_links );
        
    }
    
    return $links;
    
}
add_filter( 'plugin_row_meta', 'Alobaidi_Timeline_plugin_row_meta', 10, 2 );


// Get date of first post in the blog
function Alobaidi_Timeline_get_first_post_year(){
    $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'order' => 'ASC',
                'posts_per_page' => 1
        );

    $posts = get_posts( $args );

    foreach ($posts as $post) {
        return get_post_time('Y', false, $post->ID, false);
    }
}


// Alobaidi Timeline Shortcode
function Alobaidi_Timeline($atts){

    ob_start();

    if( !empty($atts['number']) ){
        $number = $atts['number'];
    }else{
        $number = 100; // Default number posts per page
    }

    if( !empty($atts['list']) ){
        $list = $atts['list'];
    }else{
        $list = 'ul'; // Default list
    }

    if( !empty($atts['prev']) ){
        $prev = $atts['prev'];
    }else{
        $prev = 'Previous Page'; // Default text
    }

    if( !empty($atts['next']) ){
        $next = $atts['next'];
    }else{
        $next = 'Next Page'; // Default text
    }

    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

    $from = Alobaidi_Timeline_get_first_post_year();

    $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => $number,
                'paged' => $paged,
                'date_query' => array(
                                    array( 'year' => array( $from, date('Y') ), 'compare' => 'BETWEEN' )
                                )
            );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ){

        echo '<'.$list.' class="alobaidi_timeline_list">';
        while ( $query->have_posts() ) {

            $query->the_post();

            $post_id = $query->post->ID;
            $post_title = get_the_title($post_id);

            $post_year = get_post_time('Y', false, $post_id, false);
            $post_link = get_permalink($post_id);

            $comma = " - ";

            echo '<li><a title="'.esc_attr($post_title.$comma.$post_year).'" href="'.$post_link.'">'.$post_title.'</a>'.$comma.$post_year.'</li>';

        }
        echo "</$list>";

    }

    if( get_previous_posts_link() or get_next_posts_link('', $query->max_num_pages) ){

        if( get_previous_posts_link() ){
            $prev = get_previous_posts_link($prev);
        }else{
            $prev = null;
        }

        if( get_next_posts_link('', $query->max_num_pages) ){
            $next = get_next_posts_link($next, $query->max_num_pages);
        }else{
            $next = null;
        }

        if( get_previous_posts_link() and get_next_posts_link('', $query->max_num_pages) ){
            $line = " - ";
        }else{
            $line = null;
        }

        echo '<p class="alobaidi_timeline_nav">'.$prev.$line.$next.'</p>';

    }

    wp_reset_postdata();

    return ob_get_clean();

}
add_shortcode('alobaidi_timeline', 'Alobaidi_Timeline');

?>