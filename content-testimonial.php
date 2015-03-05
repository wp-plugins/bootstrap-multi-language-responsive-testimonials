<?php
/**
 * The template for displaying content in the template-testimonial.php template
 *
 * @package Portfolio
 * @author Purplethemes
 */

$testimonial_listing_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'cpt-logo-thumbnail');
$testimonial_listing_imgURL = $testimonial_listing_imgArray[0];
$terms = get_the_terms($post->ID, 'testimonial-types' );
$testi_name = array();
if( !empty ( $terms ) ):
	foreach( (array) $terms as $term ){		    	
	    $testi_name[] = $term->name;          
	} 
endif;	
?>
<li>
	<div class="comment-post clearfix">
		<figure>
		
			<?php if( !empty($testimonial_listing_imgURL) ) { ?>
				<img src="<?php echo $testimonial_listing_imgURL; ?>" alt="<?php _e('avatar','wpt'); ?>">
			<?php } 
			 else { ?>
				<img class="img-responsive" src="<?php echo TESTI_PLUGIN_URL. 'images/no-img-testimonial.jpg'; ?>" alt="<?php _e('avatar', 'wpt'); ?>"/>  
			<?php } ?>
		
		</figure>
		<div class="comment-content">
        	<div class="meta-comments">
				<ol class="inline-list">
					<li><?php the_title(); ?></li>
					
					<?php if( !empty( $testi_name ) ){
						      $i=0;
					          foreach ( $testi_name as $key => $val ){ 
					            if( $i == 0 )
					              echo '<li><span>' .$val. '</span></li>';
					            else
					              echo ", ".'<li><span>' .$val. '</span></li>';
					              
					            $i++;
					        }
					}?>
				</ol>
			</div>
			<p><?php the_content(); ?></p>
		</div>
	</div>
</li>


