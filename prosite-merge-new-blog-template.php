<?php
/*
Plugin Name: Prosite Merge New Blog Template
Plugin URI: http://www.voltairemakeit.com
Description: Automatically assigned a template (New Blog Templates) to a level (Pro Sites)
Author: Voltaire Make It
Version: 1.1
Author URI: http://www.voltairemakeit.com
Network: true
License: GPLv2 or later
*/

/*
Copyright 2014-2015 Voltaire Make It (http://www.voltairemakeit.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function pmnbt_admin() {
    include('prosite-merge-new-blog-template-admin.php');
}

function pmnbt_admin_actions() {
add_menu_page("Prosite Merge New Blog Template", "Prosite Merge New Blog Template", 1, "Prosite Merge New Blog Template", "pmnbt_admin");
}
 
add_action('network_admin_menu', 'pmnbt_admin_actions');

register_activation_hook( __FILE__, 'pmnbt_create_db' );
function pmnbt_create_db() {
// Create DB Here
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = $wpdb->prefix . 'pmnbt';
$sql = "CREATE TABLE $table_name (
id mediumint(9) NOT NULL AUTO_INCREMENT,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
level bigint(20) NOT NULL,
template bigint(20) NOT NULL,
UNIQUE KEY id (id)
);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}

add_action( 'wp_footer', 'custom_js_script' );

function custom_js_script() {
?>
<script>
jQuery(function ($) {
var i = setInterval(function () {
var level = $('#prosites-checkout-table').attr('data-level');
var form = $('#prosites-user-register');
var html = '<div class="level_id"><label>Level ID:</label><span>' + level + '</span></div>';
if (form.find('div.level_id').size() > 0) {
form.find('div.level_id').replaceWith($(html));
} else {
$(html).insertAfter(form.find('div.username').first());
}

}, 500);
})
</script>
<?php
}

add_filter( 'blog_templates-blog_template', 'nbt_default_theme_ex', 10, 3 );
function nbt_default_theme_ex( $template, $blog_id, $user_id ) {
if ( $template == '' ) {
//user still haven't select any template, so we do our default here
$model = nbt_get_model();
global $wpdb;
$table_name = $wpdb->prefix . 'pmnbt';
$row = $wpdb->get_row( $wpdb->prepare( "SELECT * from $table_name WHERE level = %d", $_POST['level'] ) );
$template = $model->get_template( $row->template );
}
return $template;
}

?>