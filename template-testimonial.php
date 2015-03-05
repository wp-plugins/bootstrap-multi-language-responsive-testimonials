<?php
/**
 * Template Name: Testimonial
 * Description: A Page Template that display testimonial items.
 *
 * @package Portfolio
 * @author August Infotech
 */

get_header(); 
global $wp_query;
$post_id = $wp_query->get_queried_object_id();
$testimonial_post_count = get_option( 'testimonial_post_count' );

switch ( get_post_meta($post_id, 'Layout', true) ) {
	case 'left_sidebar':
		$class = 'left';
	    break;
	case 'right_sidebar':
		$class = 'right';
		break;
	
	default:
		$class = '';
		break;
}

 
if( $class == 'left' ){
  
    $right_class = 'col-xs-12 col-sm-8 col-md-8 pull-right';
    $left_class = 'col-xs-12 col-sm-4 col-md-4 pull-left';
    $class = 'left';
}     
elseif( $class == 'right' ){
    
    $right_class = 'col-xs-12 col-sm-8 col-md-8';
    $left_class = 'col-xs-12 col-sm-4 col-md-4';
    $class = 'right';
}
else {
	$class = '';
}
?>


<div class="container">
    <section class="news-section">
        <?php
            if( $class ) echo'<article class="' .$right_class.'">'; 
    			
        		$testimonial_args = array( 
                    		'post_type' => 'testimonial',
                    		'posts_per_page' =>( ! empty($testimonial_post_count)) ? $testimonial_post_count : '8' ,
                    		'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
                    		'order' => 'ASC' ,
                    		'orderby' => 'date',
                );
                                
                $temp = $wp_query;
                $wp_query = null;
                $wp_query = new WP_Query();
                $wp_query->query( $testimonial_args );  
                
		       if ( $wp_query->have_posts() )
		       {
                   echo '<div class="comment-box">';        
		               echo '<ul class="clean-list comments-loop">'; 
                            while ( have_posts() )
                            {
                                the_post();
                                include( plugin_dir_path(__FILE__).'content-testimonial.php' );
                            }
		            	               
						echo '</ul>';
    				echo '</div>';
    			
        			echo '<article class="col-xs-12 col-sm-12 col-md-12 text-right">';
                            echo '<ul class="pagination">';
                            	     wpt_testimonial_pagination();
                            echo '</ul>';
        			echo '</article>';
    			} 
                $wp_query = $temp;
                wp_reset_query(); 
                the_post();	
                
                if( $class )	echo '</article>';  
    	?>
        </section>
    
</div>
<?php  get_footer(); ?>