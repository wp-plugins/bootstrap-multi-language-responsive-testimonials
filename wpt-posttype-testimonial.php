<?php
/* ---------------------------------------------------------------------------
* Testimonial Custom Post type
* --------------------------------------------------------------------------- */

if ( ! class_exists( 'Testimonial_Post_Type' ) ) :

	class Testimonial_Post_Type {

		function __construct() {

			// Runs when the plugin is activated
			register_activation_hook( __FILE__, array( &$this, 'plugin_activation' ) );
			
			add_action( 'admin_menu', array( $this, 'testimonial_setting_admin_menu' ) );

			// Add support for translations
			load_plugin_textdomain( 'wpt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Adds the testimonial post type and taxonomies
			add_action( 'init',array( &$this, 'plugin_activation' ) );

			// Thumbnail support for testimonial posts
			add_image_size( 'cpt-logo-thumbnail',100,100 ); // Admin listing thumbnail
			

			// Adds thumbnails to column view
			add_filter( 'manage_edit-testimonial_columns', array( &$this, 'add_testimonial_thumbnail_column'), 10, 1 );
			add_action( 'manage_posts_custom_column', array( &$this, 'display_testimonial_thumbnail' ), 10, 1 );

			
			// Allows filtering of posts by taxonomy in the admin view
			add_action( 'restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );
			
			// Show testimonial post counts in the dashboard
			add_action( 'dashboard_glance_items', array( &$this, 'add_testimonial_counts' ) );

			// Give the testimonial menu item a unique icon
			add_action( 'admin_head', array( &$this, 'testimonial_icon' ) );
			
			add_action( 'wp_enqueue_scripts', array( &$this, 'plugin_frontside_scripts' ), 0 );
		}
		
		/* ---------------------------------------------------------------------------
		* Flushes rewrite rules on plugin activation to ensure testimonial posts don't 404
		* http://codex.wordpress.org/Function_Reference/flush_rewrite_rules
		* --------------------------------------------------------------------------- */

		function plugin_frontside_scripts() {
			
		        /* included javascript section */
		        
		        wp_enqueue_script('jquery');
		        
		        wp_enqueue_script( 'jquery-bootstrap-js', TESTI_PLUGIN_URL .'js/bootstrap.min.js', false, TESTI_PLUGIN_VERSION, true );
		        
				wp_enqueue_script( 'jquery-jquery.bxslider-js', TESTI_PLUGIN_URL. 'js/jquery.bxslider.min.js', false, TESTI_PLUGIN_VERSION, true );
				
				 
				wp_enqueue_script( 'jquery-testimonial-init', TESTI_PLUGIN_URL. 'js/testimonial-init.js', false, TESTI_PLUGIN_VERSION, true );
				
				
		        /* included javascript section end */
		        
		        /* css section  */
		        
		        wp_enqueue_style('jquery.bootstrap', TESTI_PLUGIN_URL.'css/bootstrap.css', array(), TESTI_PLUGIN_VERSION);
		        
		        wp_enqueue_style('jquery.font-awesome', TESTI_PLUGIN_URL.'css/font-awesome.min.css', array(), TESTI_PLUGIN_VERSION);
		        
		        wp_enqueue_style('jquery.jquery.bxslider', TESTI_PLUGIN_URL.'css/jquery.bxslider.css', array(), TESTI_PLUGIN_VERSION);
		        	
		        wp_enqueue_style('jquery.testimonial', TESTI_PLUGIN_URL.'css/testimonial.css', array(), TESTI_PLUGIN_VERSION);  
                
                /* css section end  */                    
		}
		
		function plugin_activation() {
			
			$this->testimonial_init();
			flush_rewrite_rules();
		}

		function testimonial_init() {
			
			/* ---------------------------------------------------------------------------
			* Enable the Testimonial custom post type
			* http://codex.wordpress.org/Function_Reference/register_post_type
			* --------------------------------------------------------------------------- */
			
			$labels = array(
				'name' => __( 'Testimonial', 'wpt' ),
				'singular_name' => __( 'Testimonial Item', 'wpt' ),
				'add_new' => __( 'Add New Item', 'wpt' ),
				'add_new_item' => __( 'Add New Testimonial Item', 'wpt' ),
				'edit_item' => __( 'Edit Testimonial Item', 'wpt' ),
				'new_item' => __( 'Add New Testimonial Item', 'wpt' ),
				'view_item' => __( 'View Item', 'wpt' ),
				'search_items' => __( 'Search Testimonial', 'wpt' ),
				'not_found' => __( 'No testimonial items found', 'wpt' ),
				'not_found_in_trash' => __( 'No testimonial items found in trash', 'wpt' )
			);

			$args = array(
				'labels' => $labels,
				'public' => true,
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments' ),
				'capability_type' => 'post',
				'rewrite' => array("slug" => "testimonials"), // Permalinks format
                'menu_position' => 5,
                'menu_icon' => 'dashicons-editor-quote',
				'has_archive' => true
			);

			$args = apply_filters('wpt_args', $args);

			register_post_type( 'testimonial', $args );
            
            flush_rewrite_rules();
            
            /*---------------------------------------------------------------------------
			 * Register a taxonomy for Testimonial Categories
			 * http://codex.wordpress.org/Function_Reference/register_taxonomy
			 *---------------------------------------------------------------------------*/

			$taxonomy_testimonial_category_labels = array(
				'name' => __( 'Testimonial Categories', 'wpt' ),
				'singular_name' => __( 'Testimonial Category', 'wpt' ),
				'search_items' => __( 'Search Testimonial Categories', 'wpt' ),
				'popular_items' => __( 'Popular Testimonial Categories', 'wpt' ),
				'all_items' => __( 'All Testimonial Categories', 'wpt' ),
				'parent_item' => __( 'Parent Testimonial Category', 'wpt' ),
				'parent_item_colon' => __( 'Parent Testimonial Category:', 'wpt' ),
				'edit_item' => __( 'Edit Testimonial Category', 'wpt' ),
				'update_item' => __( 'Update Testimonial Category', 'wpt' ),
				'add_new_item' => __( 'Add New Testimonial Category', 'wpt' ),
				'new_item_name' => __( 'New Testimonial Category Name', 'wpt' ),
				'separate_items_with_commas' => __( 'Separate testimonial categories with commas', 'wpt' ),
				'add_or_remove_items' => __( 'Add or remove testimonial categories', 'wpt' ),
				'choose_from_most_used' => __( 'Choose from the most used testimonial categories', 'wpt' ),
				'menu_name' => __( 'Testimonial Categories', 'wpt' ),
			);

			$taxonomy_testimonial_category_args = array(
				'labels' => $taxonomy_testimonial_category_labels,
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_admin_column' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => array( 'with_front' => false, 'slug' => 'testimonial-types' ),
				'query_var' => true
			);

			
			register_taxonomy( 'testimonial-types', array( 'testimonial' ), $taxonomy_testimonial_category_args );
			
			register_taxonomy_for_object_type( 'testimonial-types', 'testimonial' );
			
			flush_rewrite_rules();
	
		}

			/*---------------------------------------------------------------------------
			 * Add Columns to Testimonial Edit Screen
			 * http://wptheming.com/2010/07/column-edit-pages/
			 *---------------------------------------------------------------------------*/

	           // Add thumbnail to custom column
           
    		function add_testimonial_thumbnail_column( $testimonial_columns ) {

    			$column_testimonial_thumbnail = array( 'testimonial_featured_image' => __('Testimonial Thumbnail','wpt' ) );
    			$testimonial_columns = array_slice( $testimonial_columns, 0, 1, true ) + $column_testimonial_thumbnail + array_slice( $testimonial_columns, 1, NULL, true );
    			return $testimonial_columns;
    		}

            
            function display_testimonial_thumbnail($testimonial_columns) {
                global $post;
                $testimonial_image_thumbnail = get_the_post_thumbnail( $post->ID, 'cpt-logo-thumbnail' );
                if ($testimonial_columns == 'testimonial_featured_image') {
                	
                	if(!empty($testimonial_image_thumbnail)) {
						echo $testimonial_image_thumbnail;
					}
            		else {
            			
						echo '<img src="'.TESTI_PLUGIN_URL. 'images/no-img-testimonial.jpg'.'" alt="" style="width:100px;height:100px;"/>';
					}
                 
                }
            }

			/**---------------------------------------------------------------------------
			 * Adds taxonomy filters to the testimonial admin page
			 * 
			 ---------------------------------------------------------------------------*/

			function add_taxonomy_filters() {
				global $typenow;
	            
				// An array of all the taxonomies you want to display. Use the taxonomy name or slug
				$taxonomies = array( 'testimonial_category');

				// must set this to the post type you want the filter(s) displayed on
				if ( $typenow == 'testimonial' ) {

					foreach ( $taxonomies as $tax_slug ) {
						$current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
						$tax_obj = get_taxonomy( $tax_slug );
	                    
						$tax_name = $tax_obj->labels->name;
	                    
						$terms = get_terms($tax_slug);
						if ( count( $terms ) > 0) {
							echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
							echo "<option value=''>$tax_name</option>";
							foreach ( $terms as $term ) {
	                           
								echo '<option value=' . $term->slug, $current_tax_slug == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
							}
							echo "</select>";
						}
					}
				}
			}
			
			
		
			/**---------------------------------------------------------------------------
			* Added submenu setting page in menu of testimonial.
			*
			* Function Name: testimonial_setting_admin_menu.
			*
			*
			*---------------------------------------------------------------------------*/
			
			function testimonial_setting_admin_menu() {
								
				add_submenu_page( 'edit.php?post_type=testimonial', __( 'Testimonial Settings', 'wpt' ), __( 'Testimonial Settings', 'wpt' ), 'manage_options', 'testimonial-settings', array( $this, 'testimonial_settings_page' ) );
			}
			
			function testimonial_settings_page() { 
			
				if(isset($_REQUEST['update_testimonial_settings']))
				{
					if ( !isset($_POST['wpt_testimonial_nonce']) || !wp_verify_nonce($_POST['wpt_testimonial_nonce'],'testimonial_general_setting') )
					{
					    _e('Sorry, your nonce did not verify.', 'wpt');
					   exit;
					} 
					
					else
					{
						
						$testimonial_post_count = !empty($_POST['testimonial_post_count']) ? $_POST['testimonial_post_count'] : '8';
				  		update_option('testimonial_post_count',$testimonial_post_count);
						
						$testimonial_title= !empty($_POST['testimonial_title']) ? $_POST['testimonial_title'] : 'Portfolio';
					  	update_option('testimonial_title',$testimonial_title);
					  	
					  	$testimonial_content = !empty($_POST['testimonial_content']) ? $_POST['testimonial_content'] : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
					  	update_option('testimonial_content',$testimonial_content);
					}
				}
			
			?>
				<form id="testimonial-setting" method="post" action="" enctype="multipart/form-data" >
				
					<h2 style='margin-bottom: 10px;' ><?php _e( 'Testimonial General Settings', 'wpt' ); ?></h2>
						
						<table id="testimonial-table" cellpadding="20">
						 	
						 	<tr>
							 	<?php
							 	$check_testimonial_post_number = get_option('testimonial_post_count');
							 	$testimonial_post_count = !empty($check_testimonial_post_number) ? $check_testimonial_post_number : '8';
							 	?>
							 		<th><?php _e('Number of Posts', 'wpt'); ?><br/>
							 			<i><?php _e('(Specify the number of posts to be displayed per page)', 'wpt'); ?></i>
							 		</th>
							 		
							 		<td>
							 			<input type="text" id="testimonial_post_count" name="testimonial_post_count" value="<?php _e($testimonial_post_count, 'wpt'); ?>" /><br/><br/>
							 		</td>
					 		
					 		</tr>
					 		
						 	<tr>
					 		<th>
					 			<h2><?php _e('To display Title and Content for Testimonial Shortcode','wpt'); ?></h2>
					 		</th>
					 		
					 		</tr>
						 	
						 	<tr>
						 	<?php
						 	$check_testimonial_title = get_option('testimonial_title');
						 	$testimonial_title = !empty($check_testimonial_title) ? $check_testimonial_title : 'Testimonial';
						 	?>
						 		<th><?php _e('Title  :','wpt');?><br/>
						 			<i><?php _e('(Specify the title to be displayed)', 'wpt'); ?></i>
						 		</th>
						 		
						 		<td>
						 			<input type="text" id="testimonial_title" name="testimonial_title" value="<?php _e( $testimonial_title, 'wpt'); ?>" /><br/><br/>
						 		</td>
						 		
						 	</tr>
						 	
						 	<tr>
						 	<?php
						 	$check_testimonial_content = get_option('testimonial_content');
						 	$testimonial_content = !empty($check_testimonial_content) ? $check_testimonial_content : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
						 	$testimonial_content = stripslashes ( $testimonial_content );
						 	?>
						 		<th><?php _e('Content :', 'wpt'); ?><br/>
						 			<i><?php _e('(Specify the content to be displayed)','wpt'); ?></i>
						 		</th>
						 		
						 		<td>
						 			<textarea rows="7" cols="49" style="resize:none" id="testimonial_content" name="testimonial_content" ><?php _e($testimonial_content,'wpt'); ?>									
						 			</textarea><br/><br/>
						 		</td>
						 		
						 	</tr>
						 	
						</table>
						 
						<?php wp_nonce_field( 'testimonial_general_setting', 'wpt_testimonial_nonce' ); ?>
					    <p class="submit">
					        <input id="wpt-submit" class="button-primary" type="submit" name="update_testimonial_settings" value="<?php _e( 'Save Settings', 'wpt' ) ?>" />
					    </p> 
				
					 <tr>
				    
				    	<td colspan="3" align="center">
				    	<p><strong><?php _e('Note:','wpt'); ?></strong></p>
				    		<p><?php _e('You can add the testimonial shortcode using [testimonial] in any page.','wpt'); ?></p>
				    		<p><?php _e('Attributes such as count, orderby, order and category can be passed in the shortcode.','wpt'); ?></p>
				    		<p><?php _e('Eg: [testimonial count="2" orderby ="title" order="asc" category="category1" ]','wpt'); ?></p>
				    	</td>
				    </tr>
					
				</form>
				
			
				
				<?php 
				}
			
			/**---------------------------------------------------------------------------
			 * Add testimonial count to "Right Now" Dashboard Widget
			 ---------------------------------------------------------------------------*/

		    function add_testimonial_counts() {
				if ( ! post_type_exists( 'testimonial' ) ) {
					return;
				}

				$num_posts = wp_count_posts( 'testimonial' );
				$num = number_format_i18n( $num_posts->publish );
				$text = _n( 'Testimonial Item', 'Testimonial Items', intval($num_posts->publish) );
				if ( current_user_can( 'edit_posts' ) ) {
					
					$output = "<a href='edit.php?post_type=testimonial'>$num $text</a>";
					
				}
				echo '<li class="post-count testimonial-count">' . $output . '</li>';

				if ($num_posts->pending > 0) {
					$num = number_format_i18n( $num_posts->pending );
					$text = _n( 'Testimonial Item Pending', 'Testimonial Items Pending', intval($num_posts->pending) );
					if ( current_user_can( 'edit_posts' ) ) {
						$num = "<a href='edit.php?post_status=pending&post_type=testimonial'>$num</a>";
						
					}
					echo '<li class="post-count testimonial-count">' . $output . '</li>';
				}
			}


    		/**---------------------------------------------------------------------------
    		 * Displays the custom post type icon in the dashboard
    		 ---------------------------------------------------------------------------*/

		    function testimonial_icon() { ?>
            <style type="text/css" media="screen">
	           .testimonial-count a:before{content:"\f122"!important}
	        </style>
		<?php }
		
		

	}

	new Testimonial_Post_Type;

endif;

?>