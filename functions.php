<?php
// Admin Introduction
add_action('wp_dashboard_setup', 'ndsd_dashboard_widgets');
function ndsd_dashboard_widgets()
{
    global $wp_meta_boxes;
    wp_add_dashboard_widget('custom_help_widget', 'Store Directory',
        'ndsd_custom_dashboard_help');
}
function ndsd_custom_dashboard_help()
{
    echo get_bloginfo('description');
    echo '
<table class="striped widefat" style="margin-top:10px;">
<tbody>
<tr>
<th><div class="dashicons-before dashicons-cart"> Shops</div></th>
<th></th>
</tr>
<tr>
<td><a href="'.admin_url('edit.php?post_type=wp_shops').'"> View Shops</a></td>
<td><a href="'.admin_url('post-new.php?post_type=wp_shops').'"> Add Shop</a></td>
</tr>
<th><div class="dashicons-before dashicons-food"> Restaurants</div></th>
<th></th>
</tr>
<tr>
<td><a href="'.admin_url('edit.php?post_type=wp_restaurants').'"> View Restaurants</a></td>
<td><a href="'.admin_url('post-new.php?post_type=wp_restaurants').'"> Add Restaurant</a></td>
</tr>
</tbody>
</table>
    
<div class="jw">
<p>By <a href="https://www.joshuawolfe.co.uk" target="blank">Joshua Wolfe</a><a href="https://buy.stripe.com/aEUbMv4DD04M0nKeUU" target="blank"><span class="donater" style="float:right;">Donate</span></a></p></div>';
}
add_action('admin_head', 'ndsd_my_custom_css');
function ndsd_my_custom_css()
{
    echo '<style>.jw {padding:10px; border-spacing: 0; clear: both; margin: -12px;} .donater::before {content: "\f155"; padding: 0 5px 0 0; display: inline-block; color: #646970;
font: normal 20px/1 dashicons; text-decoration: none !important;vertical-align: top;} </style>';
}


?>