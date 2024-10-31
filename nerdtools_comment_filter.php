<?php
/*
Plugin Name: NerdTools Comment Filter
Plugin URI: http://www.nerdtools.co.uk/badbots/
Description: Designed to work alongside Bad Bots plugins, this plugin will read all comments posted to detect common spam elements and proceed to mark the comment as "spam" automatically if a match is found. More information can be found in the readme.txt file.
Version: 1.1
Author: NerdTools
Author URI: http://www.nerdtools.co.uk/
License: GPL2
*/

// core script //
// get settings //
$enabled = get_option('enable_nerdtools_comment_filter');
$links = get_option('links_nerdtools_comment_filter');

// check if enabled //
function nerdtools_comment_filter_not_enabled() {
    ?>
    <div class="error">
        <p><?php _e( 'NerdTools Comment Filter is installed but not enabled - click  <a href="/wp-admin/options-general.php?page=nerdtools_comment_filter.php">here</a> to adjust the plugin settings', 'nerdtools_comment_filter_not_enabled' ); ?>.</p>
    </div>
    <?php
}
// call above function if enabled //
if ($enabled!="1"){
add_action( 'admin_notices', 'nerdtools_comment_filter_not_enabled' );
}

function nerdtools_comment_filter($comment_ID, $status) {
// get variables // 
$vars = get_comment($comment_ID); 
$content = $vars->comment_content;
$url = $vars->comment_author_url;
$blogname = bloginfo('name');

// links //
$total_links = substr_count($content,"http://");
if (isset($url)) { $total_links = $total_links + 1; }
if ($total_links>$links) { $spam = "1"; }

// set comment as spam //
if ($spam=="1") {
$comment = array();
$comment['comment_ID'] = $comment_ID;
$comment['comment_approved'] = "spam";
wp_update_comment($comment);
}
}

// call above function if enabled //
if ($enabled=="1"){
add_action('comment_post', 'nerdtools_comment_filter');
}
// core script //

// settings page //
function nerdtools_comment_filter_menu() {
add_options_page('NerdTools Comment Filter', 'NT Comment Filter', 'manage_options', 'nerdtools_comment_filter.php', 'nerdtools_comment_filter_settings');
add_action( 'admin_init', 'register_nerdtools_comment_filter_settings' );
}

function register_nerdtools_comment_filter_settings() {
register_setting('nerdtools_comment_filter_group', 'enable_nerdtools_comment_filter');
register_setting('nerdtools_comment_filter_group', 'links_nerdtools_comment_filter');
} 

function nerdtools_comment_filter_settings() {
?>
<div class="wrap">
<h2>NerdTools Comment Filter</h2>
<p>Created for <a target="_blank" href="http://www.nerdtools.co.uk/">
NerdTools.co.uk</a> by <a target="_blank" href="http://www.nerdkey.co.uk/">
NerdKey</a>, this plugin automatically marks comments as spam that contain 
typical spam elements.<br><br>
Several other plugins have been created to fight spam, 
please consider installing
<a target="_blank" href="http://www.wordpress.org/plugins/nerdtools-bad-bots-spam-defender/">NerdTools Bad Bots Spam Defender</a> or 
<a target="_blank" href="http://www.wordpress.org/plugins/nerdtools-bad-bots-spam-reporter/">NerdTools Bad Bots Spam Reporter</a>.</p>
<h3>Settings</h3>
<form method="post" action="options.php">
    <?php settings_fields('nerdtools_comment_filter_group'); ?>
    <?php do_settings_sections('nerdtools_comment_filter_group'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Enable Comment Filtering?</th>
        <td><input type="checkbox" name="enable_nerdtools_comment_filter" value="1" <?php $enabled = get_option('enable_nerdtools_comment_filter'); if ($enabled=="1") { echo "checked"; } ?> /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Website links to allow per comment</th>
        <td><input type="text" name="links_nerdtools_comment_filter" size="4" value="<?php $links = get_option('links_nerdtools_comment_filter'); if (isset($links)) { echo $links; } else { echo "2"; } ?>"> (Default is 2, to filter all comments enter 0)</td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>
</div>
<?php
}
// call above function //
add_action('admin_menu', 'nerdtools_comment_filter_menu');
// settings page //
?>