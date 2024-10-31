<?php
if (isset($_POST['butimport_res']))
{
    $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
    if (!empty($_FILES['import_file']['name']) && $extension == 'csv')
    {
        $totalInserted = 0;
        $csvFile = fopen($_FILES['import_file']['tmp_name'], 'r');
        fgetcsv($csvFile);
        while(($csvData = fgetcsv($csvFile)) !== false)
        {
            $line = array_map("utf8_encode", $csvData);
            $wp_shop_title = trim($line[0]);
            $wp_shop_description = trim($line[1]);
            $wp_shop_number = trim($line[2]);
            $wp_telephone_number = trim($line[3]);
            $wp_email = trim($line[4]);
            $wp_website = trim($line[5]);
            $wp_trading_hours = trim($line[6]);
            $wp_seo_meta_keywords = trim($line[7]);
            $wp_seo_meta_description = trim($line[8]);
            $wp_shop_category = trim($line[10]);
            $my_post = array(
                'post_type' => 'wp_restaurants',
                'post_title' => wp_strip_all_tags($wp_shop_title),
                'post_content' => $wp_shop_description,
                'post_status' => 'publish',
                );
            $wp_error = false;
            $post_id = wp_insert_post($my_post, $wp_error);
            /* Category Management */
            if (isset($wp_shop_category) && isset($post_id))
            {
                $list_category = explode(',', $wp_shop_category);
                $post_id_array = array();
                foreach($list_category as $category)
                {
                    $cat_name = get_term_by('name', $category, 'restaurant_categories');
                    if (isset($cat_name->term_id))
                    {
                        wp_set_post_terms($post_id, array(), 'restaurant_categories');
                        array_push($post_id_array, $cat_name->term_id);
                    }
                    else
                    {
                        $wpdocs_cat = array('cat_name' => $category, 'taxonomy' => 'restaurant_categories');
                        $wpdocs_cat_id = wp_insert_category($wpdocs_cat);
                        array_push($post_id_array, $wpdocs_cat_id);
                    }
                }
                if (isset($post_id_array))
                {
                    wp_set_post_terms($post_id, $post_id_array, 'restaurant_categories');
                }
            }
            if (isset($post_id))
            {
                update_post_meta($post_id, 'wp_shop_number', $wp_shop_number);
                update_post_meta($post_id, 'wp_telephone_number', $wp_telephone_number);
                update_post_meta($post_id, 'wp_email', $wp_email);
                update_post_meta($post_id, 'wp_website', $wp_website);
                update_post_meta($post_id, 'wp_trading_hours', $wp_trading_hours);
                update_post_meta($post_id, 'wp_seo_meta_keywords', $wp_seo_meta_keywords);
                update_post_meta($post_id, 'wp_seo_meta_description', $wp_seo_meta_description);
                
                $days = array('MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN');
                $key_index_from = 11;
                foreach ($days as $day) {
                    $key_from = 'WMP_SHOP_TH_FROM_' . $day;
                    $key_to = 'WMP_SHOP_TH_TO_' . $day;
                    update_post_meta($post_id, $key_from, (isset($line[$key_index_from]) ? sanitize_text_field($line[$key_index_from]) : ''));
                    $key_index_from++;
                    update_post_meta($post_id, $key_to, (isset($line[$key_index_from]) ? sanitize_text_field($line[$key_index_from]) : ''));
                    $key_index_from++;
                }

                $totalInserted++;
            }
        }
        echo "<h3 style='color: green;'>Total record Inserted : ".esc_html($totalInserted)."</h3>";
    }
    else
    {
        echo "<h3 style='color: red;'>Invalid Extension</h3>";
    }
}
?>
<div class="wrap">
    <div class="row">
	    <div class="col-md-12 head">
            <h1 class="wp-heading-inline">Import / Export Shops</h1>
			<p>Import and export your shop listings in a .csv format.<br> <span style="font-size: 12px; color: #006799;">Notice/s : Images are not exported/imported. Please import new stores only in .csv as existing shops will be duplicated.</span></p>
	        <div class="">				        	
	        	<a href="<?php echo esc_url( plugins_url( "sample.csv", __FILE__ ) )?>" class="button button-primary button-large" >Sample csv file</a>
	            <a href="javascript:void(0);" class="button button-primary button-large" onclick="formToggle('importFrm');">Import</a>
                <form style="display: inline;" method='post' action='<?php echo esc_url($_SERVER['REQUEST_URI']); ?>' enctype='multipart/form-data' >
                    <input class="button button-primary button-large" type="submit" name="butexport_res" value="Export">
                </form>
	        </div>
	    </div>
	    <div class="col-md-12"  id="importFrm" style="display: none;margin-top:10px;">
			<div style="background: #ffffff; border: 1px #ccc solid; padding: 10px; border-radius: 3px;">
	        <form method='post' action='<?php echo esc_url($_SERVER['REQUEST_URI']); ?>' enctype='multipart/form-data'>
			  <input type="file" name="import_file" class="btn btn-primary" >
			  <input type="submit" name="butimport_res" value="Import" class="button button-primary button-large" >
			</form></div>
	    </div>
	</div>
	<script>
		function formToggle(ID){
		    var element = document.getElementById(ID);
		    if(element.style.display === "none"){
		        element.style.display = "block";
		    }else{
		        element.style.display = "none";
		    }
		}
	</script>
</div>