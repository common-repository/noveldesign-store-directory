<?php
get_header();
?>
<div class="pagetop">
<?php if ( is_active_sidebar( 'pagetop' ) ) : ?>
<?php dynamic_sidebar( 'pagetop' ); ?>
<?php endif; ?> 
</div>
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
						            	<li data-thumb="<?php echo esc_html($final_gallery_img); ?>">
						  	    	    	<img src="<?php echo esc_html($final_gallery_img); ?>" />
						  	    		</li>
						  	    	<?php } ?>
						          </ul>
						        </div>
						      </section>
				      	<?php }else{ ?>
				      			<img style="max-width: 100%;" class="rounded mx-auto d-block" src="<?php echo plugin_dir_url( __DIR__ ) . 'images/default-shop.jpg'; ?>">
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
				    	
				    	<?php if(isset($wp_trading_hours) && $wp_trading_hours!='' ): ?>
				      	<div class="shop_no">
				      		<h2 class="font-weight-light"><?php _e('Trading Hours'); ?></h2>
					        <p><?php echo esc_html(str_replace("\n", '<br />', $wp_trading_hours)); ?></p>
				    	</div>
				    	<?php endif; ?>


				    	<?php if(isset($wp_website) && $wp_website!='' ): ?>
				    	<div class="shop_no">
    				        <h2 class="font-weight-light"><?php _e('Website'); ?></h2>
    				        <p>
                                <a href="<?php
                                    if(strpos($wp_website, 'https://') === false && strpos($wp_website, 'http://') === false)
                                        echo esc_url('http://'.$wp_website);
                                    else
                                        echo esc_url($wp_website);
                                ?>" target="_blank">
                                    <?php echo esc_url($wp_website); ?>
                                </a>
                            </p>
    				    	</div>
				    	<?php endif; ?>
				      </div>
				<?php endwhile; ?>
			    </div>
			  </div>
			</div>
</div>
	<script type="text/javascript">
    jQuery(function(){
      SyntaxHighlighter.all();
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