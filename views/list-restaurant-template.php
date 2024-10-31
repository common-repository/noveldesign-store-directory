<?php
/*
 * Template Name: Restaurants List
 * Description: A Page template for the Store Directory plugin.
 */
get_header();
?>
<!-- Main Content -->
<div class="main">
<div class="container">
<div class="row">
<div class="col-md-12">
<div class="breadcrumb">
<p><a href="javascript://" onclick="history.back();">Back</a> | <?php 
$page2 = get_page_by_path( 'search-restaurants' , OBJECT );
if(isset($page2->ID)){ ?><a href="<?php echo esc_url( get_permalink($page2) ); ?>" role="button"> Store Directory</a><?php }?></p>		
</div>
</div>
			    <div class="col-md-12">		
					<?php
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
					if(isset($_GET['search'])){
						$wpqury_args = array(
					    'post_type' => 'wp_restaurants', //use any post type like post,or any custom post type also.
					    'posts_per_page' => 18,
					    'paged' => $paged,
					    's' => sanitize_text_field($_GET['search']),
					    'post_status' => 'publish',
					    'orderby'     => 'title', 
					    'order'       => 'ASC'
						);
					}elseif (isset($_GET['letter'])) {
    					global $wpdb;
                        if($_GET['letter'] == 'num')
                        {
                            //$tesxt = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10');
        					$postids = $wpdb->get_col("
        						SELECT      ID
        						FROM        $wpdb->posts
        						WHERE        SUBSTR($wpdb->posts.post_title,1,1) IN('0','1','2','3','4','5','6','7','8','9')
        						AND 		$wpdb->posts.post_type = 'wp_restaurants'
        						ORDER BY    $wpdb->posts.post_title");
                        }
                        else
                        {
                            $tesxt = sanitize_text_field($_GET['letter']);
        					$postids = $wpdb->get_col($wpdb->prepare("
        						SELECT      ID
        						FROM        $wpdb->posts
        						WHERE        SUBSTR($wpdb->posts.post_title,1,1) = %s
        						AND 		$wpdb->posts.post_type = 'wp_restaurants'
        						ORDER BY    $wpdb->posts.post_title",$tesxt));
                        }
					if(empty($postids)){
						$postids = array(0);
					}
						$wpqury_args = array(
					    'post_type' => 'wp_restaurants', //use any post type like post,or any custom post type also.
					    'posts_per_page' => 18,
					    'paged' => $paged,
					    'post__in' => $postids,
					    'post_status' => 'publish',
					    'orderby'     => 'title', 
					    'order'       => 'ASC'
						);
					}elseif (isset($_GET['category'])) {
						$wpqury_args = array(
					    'post_type' => 'wp_restaurants', //use any post type like post,or any custom post type also.
					    'posts_per_page' => 18,
					    'paged' => $paged,
					    'taxonomy' => 'restaurant_categories',
					    'term' => sanitize_text_field($_GET['category']), 
					    'post_status' => 'publish',
					    'orderby'     => 'title', 
					    'order'       => 'ASC'
						);
					}else{
						$wpqury_args = array(
						    'post_type' => 'wp_restaurants', //use any post type like post,or any custom post type also.
						    'posts_per_page' => 18,
						    'paged' => $paged,				  
						    'post_status' => 'publish',
						    'orderby'     => 'title', 
						    'order'       => 'ASC'
						);
			}
			$loop = new WP_Query($wpqury_args); 
			 if ( $loop->have_posts() ){ ?>
			 	<div>
				<table id="shop_table" class="table table-striped">
				<thead>
					<tr class="dark">
						<td>Store Name</td>
						<td>Store No.</td>
						<td>Contact Number</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
			    <?php while ( $loop->have_posts() ) : $loop->the_post(); 
			    	$wp_shop_name = get_the_title();
			    	$wp_shop_number = get_post_meta( get_the_ID(), 'wp_shop_number', true );
			    	$wp_telephone_number = get_post_meta( get_the_ID(), 'wp_telephone_number', true );
			    	$wp_email = get_post_meta( get_the_ID(), 'wp_email', true );
			    	$wp_website = get_post_meta( get_the_ID(), 'wp_website', true );
			    	?>
				    	<tr>
						<td><?php echo (isset($wp_shop_name)?esc_html($wp_shop_name):''); ?></td>
						<td><?php echo (isset($wp_shop_number)?esc_html($wp_shop_number):''); ?></td>
						<td><a href="tel:<?php echo esc_html($wp_telephone_number); ?>"><?php echo esc_html($wp_telephone_number); ?></a></td>
						<td><a class="btn btn-info" href="<?php echo esc_url( get_permalink($loop->ID) ); ?>">View</a></td>
						</tr>
			    <?php endwhile; ?>
				</tbody>
				</table>	
			</div>	
			<nav class="pagination">
        		 <?php do_action('pagination_bar_fun', $loop);  ?>
    		</nav>
			<?php wp_reset_postdata();
			}else{ ?>
				<div class="text-center mb-5">	
					<p>There are no stores to display.</p>
				</div>
			<?php }
			?>
			    </div>
			  </div>
			</div>
</div>
<?php 
	get_footer();
?>