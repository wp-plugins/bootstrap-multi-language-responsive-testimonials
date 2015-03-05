<?php
/*
Plugin Name: Testimonial

Plugin URI: http://www.augustinfotech.com

Description: Testimonial is a simple WordPress plugin to showcase your testimonials on your website using shortcode.

Version: 1.0

Text Domain: wpt

Author: August Infotech

Author URI: http://www.augustinfotech.com
*/

define( 'TESTI_PLUGIN_URL', 			plugin_dir_url( __FILE__) );
define( 'TESTI_PLUGIN_PATH',			plugin_dir_path( __FILE__ ) );
define( 'TESTI_PLUGIN_BASENAME', 		plugin_basename( __FILE__ ) );
define( 'TESTI_PLUGIN_VERSION', 	    '1.0' );

/* ---------------------------------------------------------------------------
 * Load the plugin required files
 * --------------------------------------------------------------------------- */

if ( ! function_exists( 'testimonial_plugin_load_function' ) ) :
function testimonial_plugin_load_function(){
	
	global $post;
	
	require_once( 'wpt-posttype-testimonial.php' );
	
	require_once('testimonial-shortcode.php');
	
    // Add a filter to the attributes metabox to inject template into the cache.
    add_filter(
		'page_attributes_dropdown_pages_args','wpt_register_testimonial_project_templates'
	);


    // Add a filter to the save post to inject out template into the page cache
    add_filter(
		'wp_insert_post_data', 
		'wpt_register_testimonial_project_templates' 
	);

    	
	// Add a filter to the template include to determine if the page has our 
	// template assigned and return it's path
    add_filter(
		'template_include', 
		'wpt_view_testimonial_project_template'
	);
	
	
}
endif; //testimonial_plugin_load_function
add_action( 'plugins_loaded','testimonial_plugin_load_function' );


/* ---------------------------------------------------------------------------
 * Required fucntions and hooks for Plugin
 * --------------------------------------------------------------------------- */

if ( ! function_exists( 'wpt_register_testimonial_project_templates' ) ) :
function wpt_register_testimonial_project_templates( $atts ) {
	

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();

        if ( empty( $templates ) ) {
                $templates = array();
        } 
        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');
        
        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, array('template-testimonial.php'     => 'Testimonial'));
        
        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

} 
endif; //wpt_register_testimonial_project_templates

/**
 * Checks if the template is assigned to the page
 */
 if ( ! function_exists( 'wpt_view_testimonial_project_template' ) ) :
 function wpt_view_testimonial_project_template( $template ) {

        global $post;
        
        $filename = get_post_meta( 
			$post->ID, '_wp_page_template', true 
		);
		
        if( !empty($filename) ){
			
	        $file = plugin_dir_path(__FILE__).$filename;
	      
	        // Just to be safe, we check if the file exist first
	        if( file_exists( $file ) ) {
	                return $file;
	        } 
       }
        
        return $template;

} 
endif; //wpt_view_testimonial_project_template
        	
/* ---------------------------------------------------------------------------
 * Activate Hook Plugin
 * --------------------------------------------------------------------------- */
 
if ( function_exists('register_activation_hook') )
register_activation_hook(__FILE__,'testimonial_plugin_activate');

if ( ! function_exists( 'testimonial_plugin_activate' ) ) :
function testimonial_plugin_activate() {
	
	add_option('testimonial_post_count','8','', 'yes');	
	add_option('testimonial_title','Testimonial','', 'yes');
	add_option('testimonial_content','Lorem ipsum dolor sit amet, consectetur adipiscing elit.','', 'yes');
    
}
endif; //testimonial_plugin_activate

/* ---------------------------------------------------------------------------
 * Uninstall Hook Plugin
 * --------------------------------------------------------------------------- */

if ( function_exists('register_uninstall_hook') )
register_uninstall_hook(__FILE__,'wpt_testimonial_plugin_droped'); 

if ( ! function_exists( 'wpt_testimonial_plugin_droped' ) ) :

function wpt_testimonial_plugin_droped() { 

	delete_option('testimonial_post_count');
	delete_option('testimonial_title');
	delete_option('testimonial_content');

}
endif; //wpt_testimonial_plugin_droped

/* ---------------------------------------------------------------------------
 * Pagination functions Plugin
 * --------------------------------------------------------------------------- */

if ( ! function_exists( 'wpt_testimonial_pagination' ) ):  
function wpt_testimonial_pagination() {
   
	global $wp_query;
	
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;  
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	
	if( empty( $paged ) ) $paged = 1;
	$prev = $paged - 1;							
	$next = $paged + 1;
	
	$end_size = 1;
	$mid_size = 2;
	$show_all = true;
	$dots = false;	
	if( ! $total = $wp_query->max_num_pages ) $total = 1;
	
	if( $total > 1 )
	{
		
		if( $paged >1 ){
			echo '<li><a class="prev_page" href="'. get_pagenum_link($current-1) .'">'. __('&lsaquo;','wpt') .'</a></li>';
		}

		for( $i=1; $i <= $total; $i++ ){
			
			if ( $i == $current ){
				echo '<li class="active">';
					echo '<a href="'. get_pagenum_link($i) .'">'. $i .'</a>&nbsp;';
				echo '</li>';
				$dots = true;
			} else {
				if ( $show_all || ( $i <= $end_size || ( $current && $i >= $current - $mid_size && $i <= $current + $mid_size ) || $i > $total - $end_size ) ){
					echo '<li>';
					   echo '<a href="'. get_pagenum_link($i) .'">'. $i .'</a>&nbsp;';
				    echo '</li>';
					$dots = true;
				} elseif ( $dots && ! $show_all ) {
					echo '<span class="page">...</span>&nbsp;';
					$dots = false;
				}
			}
		}
		
		if( $paged < $total ){
			echo '<li><a class="next_page" href="'. get_pagenum_link($page+1) .'">'. __('&rsaquo;','wpt') .'</a></li>';
		}

	}	
}
endif; //wpt_testimonial_pagination

?>