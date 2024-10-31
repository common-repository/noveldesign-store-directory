<?php
/*
 * Template Name: Restaurant Search
 * Description: A Page template for the Store Directory plugin.
 */
get_header();
?>
<!-- Main Content -->
<div class="main">
<div class="container">
<div class="row">
<div class="col-md-12">
<?php if (have_posts()): while (have_posts()) : the_post(); ?>
<!-- article -->
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php the_content(); ?>
<br class="clear">
<?php edit_post_link(); ?>
</article>
<!-- /article -->
<?php endwhile; ?>
<?php else: ?>
<!-- article -->
<article>
<h2><?php _e( 'Sorry, nothing to display.', 'midlands' ); ?></h2>
</article>
<!-- /article -->
<?php endif; ?>	
</div>
			<?php 
				$page_url = get_permalink(get_page_by_path("restaurant-list"));
				if(isset($page_url)):
			?>
			    <div class="col-md-12">			
			    	<form method="get"  action="<?php echo esc_url($page_url);?>">
			    			<input name="search" class="search-input" type="text" placeholder="Bon appétit ..." aria-label="Search">
						    <button type="submit" class="search-submit" value="Search" placeholder="To search, type and hit enter.">Search Restaurant</button>
						</form>
						</div>
			    <div class="col-md-12">	
			    	<div class="letter_display">
						<p>Select an Alphabetical Character:</p>
                        
			    <?php
                    foreach (range('A', 'Z') as $char) {
                    global $wpdb;
                    $tesxt = sanitize_text_field($char);
					$postids = $wpdb->get_col($wpdb->prepare("
						SELECT      ID
						FROM        $wpdb->posts
						WHERE        SUBSTR($wpdb->posts.post_title,1,1) = %s
						AND 		$wpdb->posts.post_type = 'wp_restaurants'
						ORDER BY    $wpdb->posts.post_title",$tesxt));
                ?>
	 					   <a href="<?php echo (count($postids)?esc_url($page_url.'?letter='.$char):'#');?>" class="btn btn-info ml-1<?php echo (count($postids)?' have-restaurants':' no-restaurants'); ?>" role="button">
	 					   		<?php echo esc_html($char); ?> 
	 					   </a>
					<?php }
                    
                    	$tesxt = sanitize_text_field('@');
                        $postids = $wpdb->get_col($wpdb->prepare("
						SELECT      ID
						FROM        $wpdb->posts
						WHERE        SUBSTR($wpdb->posts.post_title,1,1) = %s
						AND 		$wpdb->posts.post_type = 'wp_restaurants'
						ORDER BY    $wpdb->posts.post_title",$tesxt));
				?>
                    <a href="<?php echo (count($postids)?esc_url($page_url.'?letter=@'):'#');?>" class="btn btn-info ml-1<?php echo (count($postids)?' have-restaurants':' no-restaurants'); ?>" role="button">@</a>
                    <?php
                        $postids = $wpdb->get_col("
						SELECT      ID
						FROM        $wpdb->posts
						WHERE        SUBSTR($wpdb->posts.post_title,1,1) IN('0','1','2','3','4','5','6','7','8','9')
						AND 		$wpdb->posts.post_type = 'wp_restaurants'
						ORDER BY    $wpdb->posts.post_title");
                    ?>
                    <a href="<?php echo (count($postids)?esc_url($page_url.'?letter=num'):'#');?>" class="btn btn-info ml-1<?php echo (count($postids)?' have-restaurants':' no-restaurants'); ?>" role="button">0-9</a>
					</div>
			    </div>
                <?php if(get_option('WPM_CAROUSEL_STATUS')): ?>
                    <div class="col-md-12 shop_carousel">
                        <hr />
                        <h2><?php echo _e('OUR RESTAURANTS'); ?></h2>
                        <?php
                            $images = ndsd_shop::getImagesForMainPage(1, 'wp_restaurants');
                        ?>
                        <div class="flexslider carousel" data-item="2">
                          <div class="slides">
                          <?php
$day = strtoupper(date('D'));
$from = get_option('WMP_TH_'.$day.'_FROM');
$to = get_option('WMP_TH_'.$day.'_TO');
$show_trading_hours = get_option('WPM_TRADING_HOURS');
$WMP_PUBLIC_HOLIDAY = get_option('WMP_PUBLIC_HOLIDAY');
$WMP_PUBLIC_HOLIDAY_MSG = get_option('WMP_PUBLIC_HOLIDAY_MSG');
?>

<?php foreach($images as $img) : ?>
    <div class="item">
        <a href="<?php echo esc_url($img['url']); ?>"><img src="<?php echo esc_url($img['image']); ?>" />
            <?php
            $day = strtoupper(date('D'));
            $key_from = 'WMP_SHOP_TH_FROM_'.$day;
            $key_to = 'WMP_SHOP_TH_TO_'.$day;      
            $wp_trading_hours_from = get_post_meta($img['id_post'], $key_from, true);
            $wp_trading_hours_to = get_post_meta($img['id_post'], $key_to, true);
            $key_closed = 'WMP_CLOSED_' . $day;
            $is_closed = get_post_meta($img['id_post'], $key_closed, true);

            if ($wp_trading_hours_from && $wp_trading_hours_to && !$is_closed):
            ?>
                <div class="trade-hours"><p>From <?php echo esc_html($wp_trading_hours_from); ?> to <?php echo esc_html($wp_trading_hours_to); ?></p></div>
            <?php 
            else:
                if ($show_trading_hours && !$is_closed): ?>
                    <div class="trade-hours">
                        <p>
                            <?php if ($WMP_PUBLIC_HOLIDAY == date('d-m-Y')): ?>
                                <?php echo esc_html($WMP_PUBLIC_HOLIDAY_MSG); ?>
                            <?php else: ?>
                                From <?php echo esc_html($from); ?> to <?php echo esc_html($to); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php elseif ($is_closed): ?>
                    <div class="trade-hours">
                        <p>Closed</p>
                    </div>
                <?php endif;
            endif;
            ?>
        </a>
    </div>
<?php endforeach; ?>

                            <!-- items mirrored twice, total of 12 -->
                          </div>
                        </div>
                    </div>
                <?php endif; ?>
			     <div class="col-md-12">
				<hr>
				<h2>Restaurants by Category</h2>
			     <div class="category_display">	
			    <?php 
			    	$categories = get_categories( array(
					    'taxonomy'   => 'restaurant_categories', 
					    'orderby'    => 'name',
					    'parent'     => 0,
					    'hide_empty' => 0, // change to 1 to hide categores not having a single post
					) );
					if(isset($categories)){
						foreach ( $categories as $category ) { ?>
	 					   <a href="<?php echo esc_url($page_url.'?category='.$category->slug);?>" class="btn btn-info ml-1" role="button">
	 					   		<?php echo esc_html($category->name); ?> 
	 					   </a>
						<?php }
					}
				?>
			    	</div>		
			    </div>
	</div>
			  </div>
			</div>
		<?php endif; ?>
<?php 
	get_footer();
?>