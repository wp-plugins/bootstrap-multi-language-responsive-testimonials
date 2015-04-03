<?php

add_shortcode('testimonial', 'testimonialShortcode');

function testimonialShortcode($atts, $content = null) 
{	
	extract(shortcode_atts(array(
		'count' => '4',
		'order' => 'DESC',
		'orderby' => 'menu_order',
        'category' => '',
    ), $atts)); 
	
	
	$args=array(
		'post_type' => 'testimonial',
		'posts_per_page' => intval($count),
		'order' => $order,
		'orderby' => $orderby,

	);
	
	if( $category ){
		$args['testimonial-types'] = $category;
	}
	
	$query = new WP_Query($args);

    if ($query->have_posts()){ 
	
	
		$testimonial_title = get_option('testimonial_title');
		$testimonial_content = get_option('testimonial_content');
		$testimonial_content = stripslashes ( $testimonial_content );
	
		
	    	$html .='<section class="exp-section">';
		        $html .='<div class="title-area">';
		            $html .='<h2 class="section-title">' .$testimonial_title.'</h2>';
		            $html .='<div class="section-divider divider-inside-top"></div>';
		            $html .= $testimonial_content ? '<p class="section-sub-text">'.$testimonial_content.'</p>' : '';
		            
		        	$html .='</div>';
					
					$html .='<section class="testimonials-slider">';
					
					while($query->have_posts()){
						
						$query->the_post();
					    $testimonial_imgArray = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ));
	                    $testimonial_imgURL = $testimonial_imgArray[0];
						$html .='<article class="slide">';
							$html .='<div class="testimonial-image">';
								$html .='<div class="bubble-image">';
							
							    	if(!empty($testimonial_imgURL)){  
							    	$html .='<img alt="'. __('black','wpt') .'" class="attachment-small wp-post-image" src="' .$testimonial_imgURL.'" title="'. __('exp','wpt') .'" />';
							    	} 
							    	else{ 
							    	$html .='<img alt="'. __('black','wpt') .'" class="attachment-small wp-post-image" src="' .TESTI_PLUGIN_URL. 'images/no-img-testimonial.jpg'. '" style="width:87px;height:88px;" title="'. __('exp','wpt') .'" />';
							    	} 
									$html .='<div class="substrate">';
					                    $html .='<img alt="" src="' .TESTI_PLUGIN_URL. 'images/testimonial_bg.png'. '">';
					            	$html .='</div>';
							    $html .='</div>';
							$html .='</div>';
							               		
							$html .='<div class="testimonials-carousel-context">';
							    $html .='<div class="testimonials-carousel-content">';
							       $html .=' <p>'.get_the_content(). '</p>';
							       $html .=' <div class="testimonials-name">-' .get_the_title().'</div>';
							    $html .='</div>';
							$html .='</div>';
							
						$html .='</article>';
			
					}
			
	}
    
	wp_reset_query();
	
	$html .='</section>';	
    $html .='</section>';
    
	return $html;
}
?>