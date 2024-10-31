<?php
get_header();
?>
<!-- Main Content -->
<div class="main">
<div class="container">
	<div class="row">
<div class="col-md-12">
<div class="breadcrumb">
<p><a href="javascript://" onclick="history.back();">Back</a> | <?php 
$page2 = get_page_by_path( 'search-shop' , OBJECT );
if(isset($page2->ID)){ ?><a href="<?php echo esc_url( get_permalink($page2) ); ?>" role="button">Store Directory</a><?php }?></p>		
</div>
</div>
			    <div class="col-md-12">
			 <?php
			/* Start the Loop */
            $show_trading_hours = get_option('WPM_TRADING_HOURS');
            $day = strtoupper(date('D'));
            $from = get_option('WMP_TH_'.$day.'_FROM');
            $to = get_option('WMP_TH_'.$day.'_TO');
            
            $WMP_PUBLIC_HOLIDAY = get_option('WMP_PUBLIC_HOLIDAY');
            $WMP_PUBLIC_HOLIDAY_MSG = get_option('WMP_PUBLIC_HOLIDAY_MSG');
            
			while ( have_posts() ) : 
				the_post();
				$featured_img_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
				$id = get_the_ID();
				$wp_shop_name = get_the_title();
				$the_content = apply_filters('the_content', get_the_content());
		    	$wp_shop_number = get_post_meta( get_the_ID(), 'wp_shop_number', true );
		    	$wp_telephone_number = get_post_meta( get_the_ID(), 'wp_telephone_number', true );
                $wp_alt_telephone_number = get_post_meta( get_the_ID(), 'wp_alt_telephone_number', true );
		    	$gallery_images = get_post_meta( get_the_ID(), 'image_src', false );
		    	$wp_email = get_post_meta($post->ID,'wp_email',true);
				$wp_website = get_post_meta($post->ID,'wp_website',true);
				$wp_trading_hours = get_post_meta($post->ID,'wp_trading_hours',true);
                $shop_categories = get_the_terms(get_the_ID(), 'categories');
                $this_shop_id = get_the_ID();
                $WPM_HIDE_TRADING_HOURS = get_post_meta($post->ID,'WPM_HIDE_TRADING_HOURS', true);
                $day = strtoupper(date('D'));
                $key_from = 'WMP_SHOP_TH_FROM_'.$day;
                $key_to = 'WMP_SHOP_TH_TO_'.$day;      
                $wp_trading_hours_from = get_post_meta($post->ID, $key_from, true);
                $wp_trading_hours_to = get_post_meta($post->ID, $key_to, true);
                
                if(count($shop_categories))
                {
                    $first_category = $shop_categories[0];
                }
                else
                {
                    $first_category = false;
                }
                //echo '<pre>';print_r($shop_categories);die;
		    	$feature_image_array = array();
		    	$gallery_image_array = array();
		    	if(isset($featured_img_url) && !empty($featured_img_url)){
		    		$feature_image_array = array($featured_img_url[0]);
		    	}
		    	if(isset($gallery_images) && !empty($gallery_images)){
		    		$gallery_image_array = $gallery_images;
		    	}
		    	$final_gallery_array = array_merge($feature_image_array,$gallery_image_array);
				?>
				      <div class="col-lg-6" style="with:100%!important;">
				      	<?php 
				      	if(isset($final_gallery_array) && !empty($final_gallery_array)){ ?>
				      		<section class="slider">
						        <div class="flexslider">
						          <ul class="slides">
						          		<?php  foreach($final_gallery_array as $final_gallery_img){ ?>
						            	<li data-thumb="<?php echo esc_url($final_gallery_img); ?>">
						  	    	    	<img src="<?php echo esc_url($final_gallery_img); ?>" />
						  	    		</li>
						  	    	<?php } ?>
						          </ul>
						        </div>
						      </section>
				      	<?php }else{ ?>
				      			   <?php if(get_option('WMP_DEFAULT_IMAGE')): ?>
                                        <img style="max-width: 100%;" class="rounded mx-auto d-block" src="<?php echo esc_url(plugins_url('/images/'.get_option('WMP_DEFAULT_IMAGE'), dirname(__FILE__))); ?>" />
                                    <?php else: ?>
                                        <img style="max-width: 100%;" class="rounded mx-auto d-block" src="<?php echo esc_url(plugins_url('/images/default-shop.jpg', dirname(__FILE__))); ?>" />
                                    <?php endif; ?>
				      		<?php } ?>
				      </div>
				      <div class="col-lg-6">
						<h1><strong><?php echo esc_html($wp_shop_name); ?></strong></h1>
				      	<?php if(isset($the_content) && $the_content!='' ): ?>
				      	<div class="shop_dis">
					       <!-- <h1 class="font-weight-light"><strong><?php echo esc_html($wp_shop_name); ?></strong></h1>-->
					        <p><?php echo wp_kses_post($the_content); ?></p>
				    	</div>
				    	<?php endif; ?>
				    	<?php if(isset($wp_telephone_number) && $wp_telephone_number!=''): ?>
				    	<div class="shop_tel">
				        <h2 class="font-weight-light"><?php _e('Telephone Number'); ?></h2>
				        <p><a href="tel:<?php echo esc_html($wp_telephone_number); ?>"><?php echo esc_html($wp_telephone_number); ?></a></p>
				    	</div>
				    	<?php endif; ?>
                         <?php if(isset($wp_alt_telephone_number) && $wp_alt_telephone_number!=''): ?>
				    	<div class="shop_tel">
				        <h2 class="font-weight-light"><?php _e('Alt. Telephone Number'); ?></h2>
				        <p><a href="tel:<?php echo esc_html($wp_alt_telephone_number); ?>"><?php echo esc_html($wp_alt_telephone_number); ?></a></p>
				    	</div>
				    	<?php endif; ?>
				        <?php if(isset($wp_shop_number) && $wp_shop_number!= ''): ?>
				        <div class="shop_no">
				        <h2 class="font-weight-light"><?php _e('Shop Number'); ?></h2>
				        <p><?php echo esc_html($wp_shop_number); ?></p>
				    	</div>
				    	<?php endif; ?>
				    	<?php if(isset($wp_email) && $wp_email!='' ): ?>
				    	<div class="shop_no">
				        <h2 class="font-weight-light"><?php _e('Email'); ?></h2>
				        <p><a href="mailto:<?php echo esc_html($wp_email); ?>"><?php echo esc_html($wp_email); ?></a></p>
				    	</div>
				    	<?php endif; ?>
				    	
                        <?php if(!$WPM_HIDE_TRADING_HOURS): ?>
                            <?php if($wp_trading_hours_from && $wp_trading_hours_to): ?>
                                <div class="shop_no">
        				      		<h2 class="font-weight-light"><?php _e('Trading Hours'); ?></h2>
        					        <strong>Open Today: <?php echo date("l") . "<br>";?></strong>
                                    <p><?php echo 'From '.esc_html($wp_trading_hours_from).' to '.esc_html($wp_trading_hours_to); ?></p>
        				    	</div>
                            <?php else: ?>
                                <?php if(isset($show_trading_hours) && $show_trading_hours): ?>
                                    <div class="shop_no">
    <h2 class="font-weight-light"><?php _e('Trading Hours'); ?></h2>
    <?php
    $current_day = date('D');
    $key_closed = 'WMP_CLOSED_' . strtoupper($current_day);
    $is_closed = get_post_meta($post->ID, $key_closed, true);

    if (!$is_closed) {
        echo '<strong>Open Today: ' . date("l") . '</strong>';
    }

    if (!$is_closed && $WMP_PUBLIC_HOLIDAY == date('d-m-Y')) {
        echo '<p>' . esc_html($WMP_PUBLIC_HOLIDAY_MSG) . '</p>';
    } elseif (!$is_closed) {
        echo '<p>From ' . esc_html($from) . ' to ' . esc_html($to) . '</p>';
    } else {
        // Display "Closed" message
        echo '<p>' . esc_html__('Closed') . '</p>';
    }
    ?>
</div>                                 
                                <?php endif; ?>  
                            <?php endif; ?>
                        <?php endif; ?>
				    	<?php if(isset($wp_website) && $wp_website!='' ): ?>
				    	<div class="shop_no">
    				        <h2 class="font-weight-light"><?php _e('Website'); ?></h2>
    				        <p><a href="<?php
                                    $updated_url = '';
                                 if(strpos($wp_website, 'https://') === false && strpos($wp_website, 'http://') === false) {
                                     $updated_url = 'https://' . $wp_website;
                                 } else {
                                     $updated_url = $wp_website;
                                 }
                                 echo esc_url($updated_url);
                                 ?>" target="_blank">
    <?php echo esc_url($updated_url); ?>
                            </a></p>
    				    	</div>
				    	<?php endif; ?>
				      </div>
				<?php endwhile; ?>
                <div class="clear clearfix"></div>
                <?php
                    if (get_option('WPM_DISCOVER_FEATURE') && isset($first_category) && $first_category):
                        $args = array(
                            'post_type' => 'wp_shops',
                            'posts_per_page' => 20,
                            'orderby'   => 'rand',
                            'post__not_in' => array($this_shop_id),
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'categories',
                                    'field'    => 'slug',
                                    'terms'    => $first_category->slug,
                                ),
                            ),
                        );
                        $similar_shops = new WP_Query( $args );
                        $have_shops = 0;
                        $html = '';
                        while ( $similar_shops->have_posts() ) {
                            $similar_shops->the_post();
                            $gallery_images = get_post_meta( get_the_ID(), 'image_src', false );
                            if(isset($gallery_images[0]) && $gallery_images[0])
                            {
                                $html .= '<div class="items">
                                    <a href="'.get_permalink().'">
                                        <img src="'.$gallery_images[0].'" alt="'.get_the_title().'" title="'.get_the_title().'" class="img-responsive" />
                                    </a>
                                </div>';
                                $have_shops++;
                            }
                        }
                        if($have_shops):
                ?>
                        <hr />
                        <h2 class="font-weight-light"> <?php _e('Discover other stores'); ?> - <?php echo esc_html($first_category->name); ?></h2>
                        <div class="row">
                            <?php
                            echo '<div class="flexslider carousel similar-shops" data-item="1"><div class="slides">'.wp_kses_post($html).'</div></div>';
                            ?>
                            <div class="clear clearfix"></div>
                        </div>
                    <?php endif;
                    endif; ?>
			    </div>
                
			  </div>
			</div>
</div>
	<script type="text/javascript">
    jQuery(function(){
      //SyntaxHighlighter.all();
    });
    jQuery(window).load(function(){
      jQuery('.flexslider').flexslider({
        animation: "slide",
        controlNav: "thumbnails",
    	directionNav: false,
        start: function(slider){
          jQuery('body').removeClass('loading');
        }
      });
    });
  </script>
<?php 
	get_footer();
?>