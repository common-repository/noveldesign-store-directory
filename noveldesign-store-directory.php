<?php
/*
Plugin Name: The Novel Design Store Directory
Plugin URI:  www.storedirectory.co.za
Description: A store directory plugin to manage shopping centre’s tenants.
Version:     4.3.0
Author:      Joshua Wolfe
Author URI:  www.joshuawolfe.co.uk
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (file_exists(__DIR__ . '/functions.php'))
	include_once(__DIR__ . '/functions.php');

if (isset($_POST['activatelicence'])) {
	update_option('WPM_LICENCE', sanitize_text_field($_POST['licence_key']));
}
if (isset($_POST['savecarousel']) || isset($_POST['save_discover_feature']) || isset($_POST['save_show_trading_hours'])) {
	update_option('WPM_CAROUSEL_STATUS', sanitize_text_field($_POST['carouselstatus']));
	update_option('WPM_DISCOVER_FEATURE', sanitize_text_field($_POST['discover_feature']));
	update_option('WPM_TRADING_HOURS', sanitize_text_field($_POST['show_trading_hours']));
}

if (isset($_POST['save_trading_hours'])) {
	$days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN');
	foreach ($days as $day) {
		update_option('WMP_TH_' . $day . '_TO', sanitize_text_field($_POST['WMP_TH_' . $day . '_TO']));
		update_option('WMP_TH_' . $day . '_FROM', sanitize_text_field($_POST['WMP_TH_' . $day . '_FROM']));
	}

	update_option('WMP_PUBLIC_HOLIDAY', sanitize_text_field($_POST['WMP_PUBLIC_HOLIDAY']));
	update_option('WMP_PUBLIC_HOLIDAY_MSG', sanitize_text_field($_POST['WMP_PUBLIC_HOLIDAY_MSG']));
}
if (isset($_POST['btn_default_shop_image'])) {
	if (isset($_FILES['default_shop_image']['tmp_name']) && $_FILES['default_shop_image']['tmp_name']) {
		$o_name = sanitize_text_field($_FILES['default_shop_image']['name']);
		$parts = explode('.', $o_name);
		$ext = end($parts);
		$name = md5(date('YmdHis') . rand(1000, 9999)) . '.' . $ext;
		$destination = __DIR__ . '/images/' . $name;
		if (move_uploaded_file($_FILES['default_shop_image']['tmp_name'], $destination)) {
			update_option('WMP_DEFAULT_IMAGE', sanitize_text_field($name));
		}
	}
}
if (!class_exists('ndsd_shop')) {
	class ndsd_shop
	{
		public $curl_url = 'https://storedirectory.co.za/module/wpmanager/info';

		public function __construct()
		{
			/* Create Page template*/
			add_action('plugins_loaded', array('NdsdPageTemplater', 'get_instance'));
			/* Create Post type shop and category and set image size*/
			add_action('init', array($this, 'register_shop_content_type')); //register shop content type
			add_action('admin_menu', array($this, 'csv_sub_menu'));
			add_action('admin_menu', array($this, 'licence_sub_menu'));
			/* Add metabox for shop post */
			add_action('add_meta_boxes', array($this, 'add_shop_meta_boxes')); //add meta boxes
			/* Add metabox for trading hours */
			add_action('add_meta_boxes', array($this, 'add_shop_meta_boxes_trading_hours'));
			/* Add metabox for SEO Shop */
			add_action('add_meta_boxes', array($this, 'add_shop_meta_boxes_seo')); //add meta boxes
			/* Save shop meta records and added scripts */
			add_action('save_post_wp_shops', array($this, 'save_shop')); //save shop
			add_action('save_post_wp_restaurants', array($this, 'save_shop')); //save shop
			add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_and_styles')); //admin scripts and styles
			add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts_and_styles'), 99); //public scripts and styles
			add_action('wp_head', array($this, 'add_meta_tags'));
			register_activation_hook(__FILE__, array($this, 'plugin_activate')); //activate hook
			register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate')); //deactivate hook
			/* Create single shop page template */
			add_filter('single_template',  array($this, 'load_single_shop_template'));
			/* Pagination function */
			add_action('pagination_bar_fun', array($this, 'pagination_bar')); //public scripts and styles
			/* start */
			add_action('admin_enqueue_scripts', array($this, 'enqueue_media'));
			add_action('admin_head', 'ndsd_include_js_code_for_uploader');
			/* end */
		}
		/* Upload gallery image */
		public function enqueue_media()
		{
			wp_enqueue_media();
		}
		/* Load single shop page template */
		public function load_single_shop_template($template)
		{

			global $post;
			if ('wp_shops' === $post->post_type) {
				$template =  plugin_dir_path(__FILE__) . 'views/single-wp_shops.php';
			}
			if ('wp_restaurants' === $post->post_type) {
				$template =  plugin_dir_path(__FILE__) . 'views/single-wp_restaurants.php';
			}
			return $template;
		}
		/* shop list pagination */
		public function pagination_bar($custom_query)
		{
			$total_pages = $custom_query->max_num_pages;
			$big = 999999999; // need an unlikely integer
			if ($total_pages > 1) {
				$current_page = max(1, get_query_var('paged'));
				echo paginate_links(array(
					'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
					'format' => '?paged=%#%',
					'current' => $current_page,
					'total' => $total_pages,
				));
			}
		}
		//register the shop content type
		public function register_shop_content_type()
		{
			//Labels for post type
			$labels = array(
				'name'               => 'Shop',
				'singular_name'      => 'Shop',
				'menu_name'          => 'Shops',
				'name_admin_bar'     => 'Shop',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Shop',
				'new_item'           => 'New Shop',
				'edit_item'          => 'Edit Shop',
				'view_item'          => 'View Shop',
				'all_items'          => 'All Shops',
				'search_items'       => 'Search Shop',
				'parent_item_colon'  => 'Parent Shop:',
				'not_found'          => 'No Shops found.',
				'not_found_in_trash' => 'No Shops found in Trash.',
			);


			$args = array(
				'labels'            => $labels,
				'public'            => true,
				'publicly_queryable' => true,
				'show_ui'           => true,
				'show_in_nav'       => true,
				'query_var'         => true,
				'hierarchical'      => false,
				'supports'          => array('title', 'thumbnail', 'editor'),
				'has_archive'       => true,
				'menu_position'     => 20,
				'show_in_admin_bar' => true,
				'menu_icon'         => 'dashicons-cart',
				'rewrite'			=> array('slug' => 'shops', 'with_front' => 'true')
			);
			$category_flag = true;


			//register post type
			register_post_type('wp_shops', $args);
			$labels = array(
				'name' => _x('Category', 'taxonomy general name'),
				'singular_name' => _x('Category', 'taxonomy singular name'),
				'search_items' =>  __('Search Categories'),
				'popular_items' => __('Popular Categories'),
				'all_items' => __('All Categories'),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __('Edit Category'),
				'update_item' => __('Update Category'),
				'add_new_item' => __('Add New Category'),
				'new_item_name' => __('New Category Name'),
				'separate_items_with_commas' => __('Separate Categories with commas'),
				'add_or_remove_items' => __('Add or remove category'),
				'choose_from_most_used' => __('Choose from the most used categories'),
				'menu_name' => __('Categories'),
			);
			// Now register the non-hierarchical taxonomy like tag
			register_taxonomy('categories', 'wp_shops', array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array('slug' => 'category'),
				'show_in_menu' => $category_flag
			));
			/* for shop page*/
			add_image_size('wp_shop_thumbnail', 1000, 700, false);


			//Labels for post type
			$labels = array(
				'name'               => 'Restaurant',
				'singular_name'      => 'Restaurant',
				'menu_name'          => 'Restaurants',
				'name_admin_bar'     => 'Restaurant',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Restaurant',
				'new_item'           => 'New Restaurant',
				'edit_item'          => 'Edit Restaurant',
				'view_item'          => 'View Restaurant',
				'all_items'          => 'All Restaurants',
				'search_items'       => 'Search Restaurant',
				'parent_item_colon'  => 'Parent Restaurant:',
				'not_found'          => 'No Restaurants found.',
				'not_found_in_trash' => 'No Restaurants found in Trash.',
			);


			$args = array(
				'labels'            => $labels,
				'public'            => true,
				'publicly_queryable' => true,
				'show_ui'           => true,
				'show_in_nav'       => true,
				'query_var'         => true,
				'hierarchical'      => false,
				'supports'          => array('title', 'thumbnail', 'editor'),
				'has_archive'       => true,
				'menu_position'     => 20,
				'show_in_admin_bar' => true,
				'menu_icon'         => 'dashicons-food',
				'rewrite'			=> array('slug' => 'restaurant', 'with_front' => 'true')
			);
			$category_flag = true;


			//register post type
			register_post_type('wp_restaurants', $args);
			$labels = array(
				'name' => _x('Category', 'taxonomy general name'),
				'singular_name' => _x('Category', 'taxonomy singular name'),
				'search_items' =>  __('Search Categories'),
				'popular_items' => __('Popular Categories'),
				'all_items' => __('All Categories'),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __('Edit Category'),
				'update_item' => __('Update Category'),
				'add_new_item' => __('Add New Category'),
				'new_item_name' => __('New Category Name'),
				'separate_items_with_commas' => __('Separate Categories with commas'),
				'add_or_remove_items' => __('Add or remove category'),
				'choose_from_most_used' => __('Choose from the most used categories'),
				'menu_name' => __('Categories'),
			);
			// Now register the non-hierarchical taxonomy like tag
			register_taxonomy('restaurant_categories', 'wp_restaurants', array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'show_admin_column' => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var' => true,
				'rewrite' => array('slug' => 'category'),
				'show_in_menu' => $category_flag
			));
			/* for shop page*/
			add_image_size('wp_shop_thumbnail', 1000, 700, false);

			if (isset($_REQUEST['butexport'])) {
				if (isset($_REQUEST['butexport'])) {
					ob_end_clean();
					$filename = "shops_" . date('Y-m-d') . ".csv";

					// Set column headers
					$fields = array(
						'Title',
						'Description',
						'Shop Number',
						'Telephone Number',
						'Alt. Telephone Number',
						'Email',
						'Website',
						'Trading Hours',
						'Meta Keywords',
						'Meta Description',
						'Shop Categories',
						'Monday From',
						'Monday To',
						'Tuesday From',
						'Tuesday To',
						'Wednesday From',
						'Wednesday To',
						'Thursday From',
						'Thursday To',
						'Friday From',
						'Friday To',
						'Saturday From',
						'Saturday To',
						'Sunday From',
						'Sunday To'
					);
					$arg = array(
						'post_type' => 'wp_shops',
						'post_status' => 'publish',
						'posts_per_page' => -1,
					);
					global $post;
					$arr_post = get_posts($arg);
					if (isset($arr_post)) {
						header('Content-type: text/csv');
						header('Content-Disposition: attachment; filename="' . $filename . '";');
						header('Pragma: no-cache');
						header('Expires: 0');
						$file = fopen('php://output', 'w');
						fputcsv($file, $fields);
						foreach ($arr_post as $post) :
							setup_postdata($post);
							$wp_shop_title = $post->post_title;
							$wp_shop_description = $post->post_content;
							$wp_shop_number = get_post_meta($post->ID, 'wp_shop_number', true);
							$wp_telephone_number = get_post_meta($post->ID, 'wp_telephone_number', true);
							$wp_alt_telephone_number = get_post_meta($post->ID, 'wp_alt_telephone_number', true);
							$wp_email = get_post_meta($post->ID, 'wp_email', true);
							$wp_website = get_post_meta($post->ID, 'wp_website', true);
							$wp_trading_hours = get_post_meta($post->ID, 'wp_trading_hours', true);
							$wp_seo_meta_keywords = get_post_meta($post->ID, 'wp_seo_meta_keywords', true);
							$wp_seo_meta_description = get_post_meta($post->ID, 'wp_seo_meta_description', true);
							$post_categories = get_the_terms($post->ID, 'categories');

							$days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN');
							$trading_hours = array();
							foreach ($days as $day) {
								$key_from = 'WMP_SHOP_TH_FROM_' . $day;
								$key_to = 'WMP_SHOP_TH_TO_' . $day;
								$trading_hours[] = get_post_meta($post->ID, $key_from, true);
								$trading_hours[] = get_post_meta($post->ID, $key_to, true);
							}
							if ($post_categories) {
								$category_string = '';
								foreach ($post_categories as $cat) {
									if ($category_string == '') {
										$pre_comma = '';
									} else {
										$pre_comma = ',';
									}
									$category_string .= $pre_comma . $cat->name;
								}
							}
							if (isset($category_string)) {
								$wp_shop_categories = $category_string;
							} else {
								$wp_shop_categories = '';
							}
							$lineData = array(
								$wp_shop_title,
								$wp_shop_description,
								$wp_shop_number,
								$wp_telephone_number,
								$wp_alt_telephone_number,
								$wp_email,
								$wp_website,
								$wp_trading_hours,
								$wp_seo_meta_keywords,
								$wp_seo_meta_description,
								$wp_shop_categories
							);
							$lineData = array_merge($lineData, $trading_hours);
							foreach ($lineData as &$ld) {
								$ld = $this->ndwpsd_escape_csv_special_chars($ld);
							}
							fputcsv($file, $lineData);
						endforeach;
						exit();
					}
				}
			}

			if (isset($_REQUEST['butexport_res'])) {
				ob_end_clean();
				$filename = "restaurants_" . date('Y-m-d') . ".csv";

				// Set column headers
				$fields = array(
					'Title',
					'Description',
					'Shop Number',
					'Telephone Number',
					'Alt. Telephone Number',
					'Email',
					'Website',
					'Trading Hours',
					'Meta Keywords',
					'Meta Description',
					'Shop Categories',
					'Monday From',
					'Monday To',
					'Tuesday From',
					'Tuesday To',
					'Wednesday From',
					'Wednesday To',
					'Thursday From',
					'Thursday To',
					'Friday From',
					'Friday To',
					'Saturday From',
					'Saturday To',
					'Sunday From',
					'Sunday To'
				);
				$arg = array(
					'post_type' => 'wp_restaurants',
					'post_status' => 'publish',
					'posts_per_page' => -1,
				);
				global $post;
				$arr_post = get_posts($arg);
				if (isset($arr_post)) {
					header('Content-type: text/csv');
					header('Content-Disposition: attachment; filename="' . $filename . '";');
					header('Pragma: no-cache');
					header('Expires: 0');
					$file = fopen('php://output', 'w');
					fputcsv($file, $fields);
					foreach ($arr_post as $post) :
						setup_postdata($post);
						$wp_shop_title = $post->post_title;
						$wp_shop_description = $post->post_content;
						$wp_shop_number = get_post_meta($post->ID, 'wp_shop_number', true);
						$wp_telephone_number = get_post_meta($post->ID, 'wp_telephone_number', true);
						$wp_alt_telephone_number = get_post_meta($post->ID, 'wp_alt_telephone_number', true);
						$wp_email = get_post_meta($post->ID, 'wp_email', true);
						$wp_website = get_post_meta($post->ID, 'wp_website', true);
						$wp_trading_hours = get_post_meta($post->ID, 'wp_trading_hours', true);
						$wp_seo_meta_keywords = get_post_meta($post->ID, 'wp_seo_meta_keywords', true);
						$wp_seo_meta_description = get_post_meta($post->ID, 'wp_seo_meta_description', true);
						$post_categories = get_the_terms($post->ID, 'restaurant_categories');

						$days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN');
						$trading_hours = array();
						foreach ($days as $day) {
							$key_from = 'WMP_SHOP_TH_FROM_' . $day;
							$key_to = 'WMP_SHOP_TH_TO_' . $day;
							$trading_hours[] = get_post_meta($post->ID, $key_from, true);
							$trading_hours[] = get_post_meta($post->ID, $key_to, true);
						}
						if ($post_categories) {
							$category_string = '';
							foreach ($post_categories as $cat) {
								if ($category_string == '') {
									$pre_comma = '';
								} else {
									$pre_comma = ',';
								}
								$category_string .= $pre_comma . $cat->name;
							}
						}
						if (isset($category_string)) {
							$wp_shop_categories = $category_string;
						} else {
							$wp_shop_categories = '';
						}
						$lineData = array(
							$wp_shop_title,
							$wp_shop_description,
							$wp_shop_number,
							$wp_telephone_number,
							$wp_alt_telephone_number,
							$wp_email,
							$wp_website,
							$wp_trading_hours,
							$wp_seo_meta_keywords,
							$wp_seo_meta_description,
							$wp_shop_categories
						);
						$lineData = array_merge($lineData, $trading_hours);
						foreach ($lineData as &$ld) {
							$ld = $this->ndwpsd_escape_csv_special_chars($ld);
						}
						fputcsv($file, $lineData);
					endforeach;
					exit();
				}
			}
		}
		public function csv_sub_menu()
		{
			add_submenu_page(
				'edit.php?post_type=wp_shops',
				__('CSV Import Export', 'shop_directory'),
				__('Import / Export', 'shop_directory'),
				'manage_options',
				'shop-csv',
				'shop_csv_callback_function'
			);
			function shop_csv_callback_function()
			{
				include_once('views/admin-csv.php');
			}
			add_submenu_page(
				'edit.php?post_type=wp_restaurants',
				__('CSV Import Export', 'shop_directory'),
				__('Import / Export', 'shop_directory'),
				'manage_options',
				'restaurant-csv',
				'restaurant_csv_callback_function'
			);
			function restaurant_csv_callback_function()
			{
				include_once('views/admin-restaurant-csv.php');
			}
		}
		public function licence_sub_menu()
		{
			add_submenu_page(
				'options-general.php',
				__('Shoplist Licence', 'shop_directory'),
				__('Store Directory Settings', 'shop_directory'),
				'manage_options',
				'licence',
				'shop_licence_callback_function'
			);
			function shop_licence_callback_function()
			{
				include_once('views/licence.php');
			}
		}
		public function add_shop_meta_boxes()
		{
			add_meta_box(
				'wp_shop_meta_box', //id
				'Shop Information', //name
				array($this, 'shop_meta_box_display'), //display function
				'wp_shops', //post type
				'normal', //shop
				'default' //priority
			);
			add_meta_box(
				'wp_shop_meta_box', //id
				'Restaurant Information', //name
				array($this, 'shop_meta_box_display'), //display function
				'wp_restaurants', //post type
				'normal', //shop
				'default' //priority
			);
		}
		public function add_shop_meta_boxes_trading_hours()
		{
			add_meta_box(
				'wp_shop_meta_box_trading_hours', //id
				'Trading Hours', //name
				array($this, 'shop_meta_box_trading_hours_display'), //display function
				'wp_shops', //post type
				'normal', //shop
				'default' //priority
			);
			add_meta_box(
				'wp_shop_meta_box_trading_hours', //id
				'Trading Hours', //name
				array($this, 'shop_meta_box_trading_hours_display'), //display function
				'wp_restaurants', //post type
				'normal', //shop
				'default' //priority
			);
		}
		public function add_shop_meta_boxes_seo()
		{
			add_meta_box(
				'wp_shop_meta_box_seo', //id
				'Search Engine Optimization', //name
				array($this, 'shop_meta_box_seo_display'), //display function
				'wp_shops', //post type
				'normal', //shop
				'default' //priority
			);
			add_meta_box(
				'wp_shop_meta_box_seo', //id
				'Search Engine Optimization', //name
				array($this, 'shop_meta_box_seo_display'), //display function
				'wp_restaurants', //post type
				'normal', //shop
				'default' //priority
			);
		}
		//display function used for our custom shop meta box*/
		public function shop_meta_box_trading_hours_display($post)
		{
			wp_nonce_field('wp_shop_nonce', 'wp_shop_nonce_field');
			global $post;
			$days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN');
?>
			<table>
				<tr>
					<td colspan="2">
						<label for="WPM_HIDE_TRADING_HOURS">
							<input type="checkbox" name="WPM_HIDE_TRADING_HOURS" id="WPM_HIDE_TRADING_HOURS" <?php echo esc_html(get_post_meta($post->ID, 'WPM_HIDE_TRADING_HOURS', true) ? 'checked' : ''); ?> /><?php echo  __('Hide trading hours'); ?>
						</label>
					</td>
				</tr>
				<?php foreach ($days as $day) : ?>
					<tr>
						<td colspan="1">
							<label for="WMP_SHOP_TH_FROM_<?php echo esc_html($day); ?>"><?php echo date('l', strtotime($day)); ?></label>
						</td>
						<td>
							<label for="WMP_CLOSED_<?php echo esc_html($day); ?>">
								<input type="checkbox" name="WMP_CLOSED_<?php echo esc_html($day); ?>" id="WMP_CLOSED_<?php echo esc_html($day); ?>" <?php echo esc_html(get_post_meta($post->ID, 'WMP_CLOSED_' . $day, true) ? 'checked' : ''); ?> /><?php echo __('Closed'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<input type="text" value="<?php echo esc_html(get_post_meta($post->ID, 'WMP_SHOP_TH_FROM_' . $day, true)); ?>" value="" name="WMP_SHOP_TH_FROM_<?php echo esc_html($day); ?>" id="WMP_SHOP_TH_FROM_<?php echo esc_html($day); ?>" />
						</td>
						<td>
							<input type="text" value="<?php echo esc_html(get_post_meta($post->ID, 'WMP_SHOP_TH_TO_' . $day, true)); ?>" name="WMP_SHOP_TH_TO_<?php echo esc_html($day); ?>" id="WMP_SHOP_TH_FROM_<?php echo esc_html($day); ?>" />
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php
			do_action('wp_shop_admin_form_end');
		}
		//display function used for our custom shop meta box*/
		public function shop_meta_box_display($post)
		{
			wp_nonce_field('wp_shop_nonce', 'wp_shop_nonce_field');
			$wp_shop_number = get_post_meta($post->ID, 'wp_shop_number', true);
			$wp_telephone_number = get_post_meta($post->ID, 'wp_telephone_number', true);
			$wp_alt_telephone_number = get_post_meta($post->ID, 'wp_alt_telephone_number', true);
			$wp_email = get_post_meta($post->ID, 'wp_email', true);
			$wp_website = get_post_meta($post->ID, 'wp_website', true);
			$wp_trading_hours = get_post_meta($post->ID, 'wp_trading_hours', true);
			$wp_seo_meta_keywords = get_post_meta($post->ID, 'wp_seo_meta_keywords', true);
			$wp_seo_meta_description = get_post_meta($post->ID, 'wp_seo_meta_description', true);
			$image = 'Upload Image';
			$button = 'button';
			$image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
			$display = 'none'; // display state of the "Remove image" button
			/*------------*/
			global $post;
			// Get WordPress' media upload URL
			$upload_link = esc_url(get_upload_iframe_src());
			// See if there's a media id already saved as post meta
			$your_img_id = get_post_meta(get_the_ID(), '_your_img_id', true);
			// Get the image src
			$your_img_src = wp_get_attachment_image_src($your_img_id, 'full');
			// For convenience, see if the array is valid
			$you_have_img = is_array($your_img_src);
			/*------------*/
		?>
			<p><?php _e('Enter additional information about your shop', 'shop_directory'); ?></p>
			<div class="field-container">
				<?php do_action('wp_shop_admin_form_start'); ?>
				<!-- Start https://www.regur.net/decode/custom-image-loader-wordpress-admin-panel/-->
				<div id="custom-images">
					<div class="custom-img-container">
						<?php
						$meta_values = get_post_meta(get_the_ID(), 'image_src', false);
						foreach ($meta_values as $value) {
						?>
							<div class="image-wrapper">
								<input type="text" name="image_src[]" value="<?php echo esc_html($value); ?>">
								<img width="100px" src="<?php echo esc_html($value); ?>">
								<a class="shop_remove_button delete-custom-img" href="#">Remove</a>
							</div>
						<?php } ?>
					</div>
				</div>
				<p>
					<a class="shop_button upload-custom-img <?php if ($you_have_img) {
																echo 'hidden';
															} ?>" href="<?php echo esc_url($upload_link); ?>">
						<?php _e('Upload Images'); ?>
					</a>
				</p>
				<div class="field">
					<label for="wp_shop_number"><?php _e('Shop Number', 'shop_directory'); ?></label>
					<input type="text" name="wp_shop_number" id="wp_shop_number" value="<?php echo esc_html($wp_shop_number); ?>" />
				</div>
				<div class="field">
					<label for="wp_telephone_number"><?php _e('Telephone Number', 'shop_directory'); ?></label>
					<input type="tel" name="wp_telephone_number" id="wp_telephone_number" value="<?php echo esc_html($wp_telephone_number); ?>" />
				</div>
				<div class="field">
					<label for="wp_alt_telephone_number"><?php _e('Alt. Telephone Number', 'shop_directory'); ?></label>
					<input type="tel" name="wp_alt_telephone_number" id="wp_alt_telephone_number" value="<?php echo esc_html($wp_alt_telephone_number); ?>" />
				</div>
				<div class="field">
					<label for="wp_email"><?php _e('Email', 'shop_directory'); ?></label>
					<input type="email" name="wp_email" id="wp_email" value="<?php echo esc_html($wp_email); ?>" />
				</div>
				<div class="field">
					<label for="wp_website"><?php _e('Website', 'shop_directory'); ?></label>
					<input type="text" name="wp_website" id="wp_website" value="<?php echo esc_url($wp_website); ?>" placeholder="https://example.com" />
				</div>
				<?php do_action('wp_shop_admin_form_end');  ?>
			</div>
		<?php
		}
		//display function used for our custom shop meta box*/
		public function shop_meta_box_seo_display($post)
		{
			wp_nonce_field('wp_shop_nonce', 'wp_shop_nonce_field');
			$wp_seo_meta_keywords = get_post_meta($post->ID, 'wp_seo_meta_keywords', true);
			$wp_seo_meta_description = get_post_meta($post->ID, 'wp_seo_meta_description', true);
			/*------------*/
			global $post;
		?>
			<div class="field-container">
				<?php do_action('wp_shop_admin_form_start'); ?>
				<div class="field">
					<label for="wp_seo_meta_keywords"><?php _e('SEO Meta Keywords', 'shop_directory'); ?></label>
					<input type="text" name="wp_seo_meta_keywords" id="wp_seo_meta_keywords" value="<?php echo esc_html($wp_seo_meta_keywords); ?>" />
				</div>
				<div class="field">
					<label for="wp_seo_meta_description"><?php _e('SEO Meta Description', 'shop_directory'); ?></label>
					<input type="text" name="wp_seo_meta_description" id="wp_seo_meta_description" value="<?php echo esc_html($wp_seo_meta_description); ?>" />
				</div>
				<?php do_action('wp_shop_admin_form_end');  ?>
			</div>
			<?php
		}
		public function ndwpsd_escape_csv_special_chars($string)
		{
			$exclude = array('=', '+', '-', '@');
			$character = @$string[0];
			if (in_array($character, $exclude))
				return ' ' . $string;
			return $string;
		}
		public function save_shop($post_id)
		{
			//check for nonce
			if (!isset($_POST['wp_shop_nonce_field'])) {
				return $post_id;
			}
			//verify nonce
			if (!wp_verify_nonce($_POST['wp_shop_nonce_field'], 'wp_shop_nonce')) {
				return $post_id;
			}
			//check for autosave
			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return $post_id;
			}
			$wp_shop_number = isset($_POST['wp_shop_number']) ? sanitize_text_field($_POST['wp_shop_number']) : '';
			$wp_telephone_number = isset($_POST['wp_telephone_number']) ? sanitize_text_field($_POST['wp_telephone_number']) : '';
			$wp_alt_telephone_number = isset($_POST['wp_alt_telephone_number']) ? sanitize_text_field($_POST['wp_alt_telephone_number']) : '';
			$wp_email = isset($_POST['wp_email']) ? sanitize_email($_POST['wp_email']) : '';
			$wp_website = isset($_POST['wp_website']) ? sanitize_text_field($_POST['wp_website']) : '';
			$wp_trading_hours = isset($_POST['wp_trading_hours']) ? sanitize_textarea_field($_POST['wp_trading_hours']) : '';
			$wp_seo_meta_keywords = isset($_POST['wp_seo_meta_keywords']) ? sanitize_text_field($_POST['wp_seo_meta_keywords']) : '';
			$wp_seo_meta_description = isset($_POST['wp_seo_meta_description']) ? sanitize_text_field($_POST['wp_seo_meta_description']) : '';
			$WPM_HIDE_TRADING_HOURS = (isset($_POST['WPM_HIDE_TRADING_HOURS']) ? 1 : 0);
			//update records
			update_post_meta($post_id, 'wp_shop_number', $wp_shop_number);
			update_post_meta($post_id, 'wp_telephone_number', $wp_telephone_number);
			update_post_meta($post_id, 'wp_alt_telephone_number', $wp_alt_telephone_number);
			update_post_meta($post_id, 'wp_email', $wp_email);
			update_post_meta($post_id, 'wp_website', $wp_website);
			update_post_meta($post_id, 'wp_trading_hours', $wp_trading_hours);
			update_post_meta($post_id, 'wp_seo_meta_keywords', $wp_seo_meta_keywords);
			update_post_meta($post_id, 'wp_seo_meta_description', $wp_seo_meta_description);
			update_post_meta($post_id, 'WPM_HIDE_TRADING_HOURS', $WPM_HIDE_TRADING_HOURS);

			$days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN');
			foreach ($days as $day) {
				$key_from = 'WMP_SHOP_TH_FROM_' . $day;
				$key_to = 'WMP_SHOP_TH_TO_' . $day;
				$key_closed = 'WMP_CLOSED_' . $day;
				$closed_value = isset($_POST[$key_closed]) ? 1 : 0;
				update_post_meta($post_id, $key_from, (isset($_POST[$key_from]) ? sanitize_text_field($_POST[$key_from]) : ''));
				update_post_meta($post_id, $key_to, (isset($_POST[$key_from]) ? sanitize_text_field($_POST[$key_to]) : ''));
				update_post_meta($post_id, $key_closed, $closed_value);
			}

			/* Get the meta key. */
			$meta_key = 'image_src';
			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta($post_id, $meta_key, false);
			/* For looping all meta values */
			foreach ($meta_value as $value) {
				delete_post_meta($post_id, $meta_key, $value);
			}
			/* Get the posted data and sanitize it for use as an HTML class. */
			foreach ($_POST['image_src'] as $value) {
				add_post_meta($post_id, $meta_key, sanitize_text_field($value), false);
			}
			do_action('wp_shop_admin_save', $post_id);
		}
		//triggered on activation of the plugin (called only once)
		public function plugin_activate()
		{
			//call our custom content type function
			$this->register_shop_content_type();
			if (!is_page(array('shop-list', 'search-shop'))) {
				$shop_page_1 = wp_insert_post(array(
					'post_title'     => 'Shop List',
					'post_type'      => 'page',
					'post_name'      => 'shop-list',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_content'   => '',
					'post_status'    => 'publish',
					'page_template'  => 'list-store-template.php'
				));
				$shop_page_2 = wp_insert_post(array(
					'post_title'     => 'Search Shop',
					'post_type'      => 'page',
					'post_name'      => 'search-shop',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_content'   => '',
					'post_status'    => 'publish',
					'page_template'  => 'search-store-template.php'
				));
				$shop_page_3 = wp_insert_post(array(
					'post_title'     => 'Search Restaurants',
					'post_type'      => 'page',
					'post_name'      => 'search-restaurants',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_content'   => '',
					'post_status'    => 'publish',
					'page_template'  => 'search-restaurants-template.php'
				));
				$shop_page_4 = wp_insert_post(array(
					'post_title'     => 'Restaurant List',
					'post_type'      => 'page',
					'post_name'      => 'restaurant-list',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_content'   => '',
					'post_status'    => 'publish',
					'page_template'  => 'list-restaurant-template.php'
				));
			}
			update_post_meta($shop_page_1, '_wp_page_template', 'views/list-store-template.php');
			update_post_meta($shop_page_2, '_wp_page_template', 'views/search-store-template.php');
			update_post_meta($shop_page_3, '_wp_page_template', 'views/search-restaurants-template.php');
			update_post_meta($shop_page_4, '_wp_page_template', 'views/list-restaurant-template.php');
			//flush permalinks
			flush_rewrite_rules();
		}
		//trigered on deactivation of the plugin (called only once)
		public function plugin_deactivate()
		{
			$page1 = get_page_by_path('shop-list', OBJECT);
			$page2 = get_page_by_path('search-shop', OBJECT);
			$page3 = get_page_by_path('search-restaurants', OBJECT);
			$page4 = get_page_by_path('shop-list', OBJECT);
			if (isset($page1->ID)) {
				wp_delete_post($page1->ID, true);
				delete_post_meta($page1->ID, '_wp_page_template');
			}
			if (isset($page2->ID)) {
				wp_delete_post($page2->ID, true);
				delete_post_meta($page2->ID, '_wp_page_template');
			}
			if (isset($page3->ID)) {
				wp_delete_post($page3->ID, true);
				delete_post_meta($page3->ID, '_wp_page_template');
			}
			if (isset($page4->ID)) {
				wp_delete_post($page4->ID, true);
				delete_post_meta($page4->ID, '_wp_page_template');
			}
			//flush permalinks
			flush_rewrite_rules();
		}
		//enqueus scripts and stles on the back end
		public function enqueue_admin_scripts_and_styles()
		{
			wp_enqueue_style('jquery-ui-datepicker');
			wp_enqueue_style('jquery-style', plugin_dir_url(__FILE__) . '/css/jquery-ui.css?ran=' . rand(1000, 9999));
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_style('wp_shop_admin_styles', plugin_dir_url(__FILE__) . '/css/wp_shop_admin_styles.css?ran=' . rand(1000, 9999));
			wp_enqueue_script('wpm_admin_script', plugin_dir_url(__FILE__) . '/js/admin.js', array('jquery'), rand(1000, 9999), true);
		}
		//enqueues scripts and styled on the front end
		public function enqueue_public_scripts_and_styles()
		{
			wp_enqueue_style('wp_shop_public_styles', plugin_dir_url(__FILE__) . '/css/wp_shop_public_styles.css?rand=' . rand(1000, 9999));
			wp_enqueue_style('flexslider_css', plugin_dir_url(__FILE__) . '/css/flex/flexslider.css');
			wp_enqueue_script('flexslider_js', plugin_dir_url(__FILE__) . '/js/flex/jquery.flexslider.js', array('jquery'), 1.1, true);
			if (get_option('WPM_CAROUSEL_STATUS') || get_option('WPM_DISCOVER_FEATURE'))
				wp_enqueue_script('mainpage_js', plugin_dir_url(__FILE__) . '/js/mainpage.js', array('flexslider_js'));
			if (is_singular('wp_shops')) {
				global $wp_styles;
				$has_bootstrap = false;
				foreach ($wp_styles->registered as $css) {
					if (strpos($css->src, 'bootstrap') !== false) {
						$has_bootstrap = true;
						break;
					}
				}
				if (!$has_bootstrap) {
					wp_enqueue_style('wsd_bootstrap', plugin_dir_url(__FILE__) . '/css/bootstrap.min.css');
				}
			}
		}
		//Add SEO meta tags and description
		public function add_meta_tags()
		{
			if (is_single() && ('wp_shops' == get_post_type() || get_post_type() == 'wp_restaurants')) {
				$post_id = get_the_ID();
				$wp_seo_meta_keywords = get_post_meta($post_id, 'wp_seo_meta_keywords', true);
				$wp_seo_meta_description = get_post_meta($post_id, 'wp_seo_meta_description', true);
			?>
				<meta name="keywords" content="<?php echo (isset($wp_seo_meta_keywords)) ? esc_html($wp_seo_meta_keywords) : ''; ?>">
				<meta name="description" content="<?php echo (isset($wp_seo_meta_description)) ? esc_html($wp_seo_meta_description) : ''; ?>">
	<?php }
		}

		public static function getImagesForMainPage($paged = 1, $post_type = 'wp_shops')
		{
			$wpqury_args = array(
				'post_type' => $post_type, //use any post type like post,or any custom post type also.
				'posts_per_page' => 100,
				'paged' => $paged,
				'post_status' => 'publish',
				'orderby'     => 'rand',
				'order'       => 'ASC'
			);
			$images = array();
			$loop = new WP_Query($wpqury_args);
			while ($loop->have_posts()) {
				$loop->the_post();
				$gallery_images = get_post_meta(get_the_ID(), 'image_src', false);
				if (isset($gallery_images[0]) && $gallery_images[0])
					$images[] = array(
						'url' => esc_url(get_permalink(get_the_ID())),
						'image' => $gallery_images[0],
						'id_post' => get_the_ID()
					);
			}
			return array_slice($images, 0, 20);
		}
	}
}
if (!class_exists('NdsdPageTemplater')) {
	class NdsdPageTemplater
	{
		/**
		 * A reference to an instance of this class.
		 */
		private static $instance;
		/**
		 * The array of templates that this plugin tracks.
		 */
		protected $templates;
		/**
		 * Returns an instance of this class.
		 */
		public static function get_instance()
		{
			if (null == self::$instance) {
				self::$instance = new NdsdPageTemplater();
			}
			return self::$instance;
		}
		/**
		 * Initializes the plugin by setting filters and administration functions.
		 */
		private function __construct()
		{
			$this->templates = array();
			// Add a filter to the attributes metabox to inject template into the cache.
			if (version_compare(floatval(get_bloginfo('version')), '4.7', '<')) {
				// 4.6 and older
				add_filter(
					'page_attributes_dropdown_pages_args',
					array($this, 'register_project_templates')
				);
			} else {
				// Add a filter to the wp 4.7 version attributes metabox
				add_filter(
					'theme_page_templates',
					array($this, 'add_new_template')
				);
			}
			// Add a filter to the save post to inject out template into the page cache
			add_filter(
				'wp_insert_post_data',
				array($this, 'register_project_templates')
			);
			// Add a filter to the template include to determine if the page has our
			// template assigned and return it's path
			add_filter(
				'template_include',
				array($this, 'view_project_template')
			);
			// Add your templates to this array.
			$this->templates = array(
				'views/list-store-template.php' => 'Store List',
				'views/search-store-template.php' => ' Search Store',
				'views/search-restaurants-template.php' => ' Search Restaurants',
				'views/list-restaurant-template.php' => 'Restaurants List',
			);
		}
		/**
		 * Adds our template to the page dropdown for v4.7+
		 *
		 */
		public function add_new_template($posts_templates)
		{
			$posts_templates = array_merge($posts_templates, $this->templates);
			return $posts_templates;
		}
		/**
		 * Adds our template to the pages cache in order to trick WordPress
		 * into thinking the template file exists where it doens't really exist.
		 */
		public function register_project_templates($atts)
		{
			// Create the key used for the themes cache
			$cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());
			// Retrieve the cache list.
			// If it doesn't exist, or it's empty prepare an array
			$templates = wp_get_theme()->get_page_templates();
			if (empty($templates)) {
				$templates = array();
			}
			// New cache, therefore remove the old one
			wp_cache_delete($cache_key, 'themes');
			// Now add our template to the list of templates by merging our templates
			// with the existing templates array from the cache.
			$templates = array_merge($templates, $this->templates);
			// Add the modified cache to allow WordPress to pick it up for listing
			// available templates
			wp_cache_add($cache_key, $templates, 'themes', 1800);
			return $atts;
		}
		/**
		 * Checks if the template is assigned to the page
		 */
		public function view_project_template($template)
		{
			// Return the search template if we're searching (instead of the template for the first result)
			if (is_search()) {
				return $template;
			}
			// Get global post
			global $post;
			// Return template if post is empty
			if (!$post) {
				return $template;
			}
			// Return default template if we don't have a custom one defined
			if (!isset($this->templates[get_post_meta(
				$post->ID,
				'_wp_page_template',
				true
			)])) {
				return $template;
			}
			// Allows filtering of file path
			$filepath = apply_filters('page_templater_plugin_dir_path', plugin_dir_path(__FILE__));
			$file =  $filepath . get_post_meta(
				$post->ID,
				'_wp_page_template',
				true
			);
			// Just to be safe, we check if the file exist first
			if (file_exists($file)) {
				return esc_html($file);
			} else {
				echo esc_html($file);
			}
			// Return template
			return $template;
		}
	}
}
//JavaScript Code for opening uploader and copying the link of the uploaded image to a textbox
function ndsd_include_js_code_for_uploader()
{
	if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'wp_shops') {
		//wp_enqueue_style( 'style1', plugins_url( '/css/bootstrap.min.css', __FILE__ ) );	
	}
	?>
	<!-- ****** JS CODE ******  -->
	<script>
		jQuery(function($) {
			// Set all variables to be used in scope
			var frame,
				metaBox = $('#wp_shop_meta_box.postbox'); // Your meta box id here
			addImgLink = metaBox.find('.upload-custom-img');
			imgContainer = metaBox.find('.custom-img-container');
			imgIdInput = metaBox.find('.custom-img-id');
			customImgDiv = metaBox.find('#custom-images');
			// ADD IMAGE LINK
			addImgLink.on('click', function(event) {
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if (frame) {
					frame.open();
					return;
				}
				// Create a new media frame
				frame = wp.media({
					title: 'Select or Upload Media Of Your Chosen Persuasion',
					button: {
						text: 'Use this media'
					},
					multiple: false // Set to true to allow multiple files to be selected
				});
				// When an image is selected in the media frame...
				frame.on('select', function() {
					// Get media attachment details from the frame state
					var attachment = frame.state().get('selection').first().toJSON();
					// Send the attachment URL to our custom image input field.
					imgContainer.append('<div class="image-wrapper"><input type="text" name="image_src[]" value="' + attachment.url + '"> <img width="100px" src="' + attachment.url + '"><a class="shop_remove_button delete-custom-img" href="#">Remove</a></div>');
				});
				// Finally, open the modal on click
				frame.open();
			});
			customImgDiv.on('click', '.delete-custom-img', function(event) {
				event.preventDefault();
				jQuery(event.target).parent().remove();
			});
		});
	</script>
<?php }
function ndsd_display_trading_hours()
{
	$day = strtoupper(date('D'));
	$WMP_TH_FROM = get_option('WMP_TH_' . $day . '_FROM');
	$WMP_TH_TO = get_option('WMP_TH_' . $day . '_TO');
	return '<div>
        <table id="tradinghours-sc">
            <tr>
                <td rowspan="2" style="width:40px">
                    <img src="' . plugins_url('', __FILE__) . '/images/time.svg" style="max-height:38px" alt="" title="" />
                </td>
                <td>
                    Open Today: ' . date("l") . '
                </td>
            </tr>
            <tr>
                <td>From ' . $WMP_TH_FROM . ' to ' . $WMP_TH_TO . '</td>
            </tr>
        </table>
    </div>
    <style type="text/css">#tradinghours-sc td{padding:0px}</style>';
}
add_shortcode('tradinghours', 'ndsd_display_trading_hours');
$wp_shops = new ndsd_shop;
?>