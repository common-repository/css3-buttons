<?php
/*
Plugin Name: CSS3 Buttons
Plugin URI: http://TuniLa.me/css3-buttons
Description: This plugin will help you adding coloured CSS3 buttons on your posts using simply a shortcode.
Author: TuniLame
Version: 0.1
Author URI: http://TuniLa.me
*/
/*  Copyright 2010  TuniLame  (email : tunilame@gmail.com)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
global $css3_default_button,$css3_default_vals;
	$css3_default_button = 'css3_default_button_set';
	$css3_default_vals = get_option( $css3_default_button );
// Enable internationalisation
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'css3_buttons', 'wp-content/plugins/' . $plugin_dir, $plugin_dir.'/lang' );
function css3_button_db (){
global $css3_default_button;
	if (get_option( $css3_default_button )==false) {
		$valeurs= array (
			'css3_opt_name_val' => 'custom',
			'css3_opt_color_val' => '#91bd09',
			'css3_opt_size_val' => 'large',
			'css3_opt_link_val' => '#',
			'css3_opt_target_val' => 'self'
		);
		update_option( $css3_default_button, $valeurs );
	}
}
register_activation_hook(__FILE__,'css3_button_db');

function sc_button($atts, $content = null) {
	global $css3_default_button,$css3_default_vals;
	extract(shortcode_atts(array(
		"color" => $css3_default_vals ['css3_opt_name_val'],
		"size" => $css3_default_vals ['css3_opt_size_val'],
		"link" => $css3_default_vals ['css3_opt_link_val'],
		"target" => $css3_default_vals ['css3_opt_target_val']
	), $atts));
	$colors= array("pink","magenta","green","red","orange","blue","yellow");
	if (in_array($color, $colors)) { $tmp=""; }
	else { $no= $css3_default_vals ['css3_opt_color_val'];
	$tmp=' style="background-color:'.$no.';"';}
	return '<a href="'.$link.'" target="_'.$target.'"><div class="css3-button '.$color.' '.$size.'"'.$tmp.'>'.$content.'</div></a>';
}

function insert_css_in_head () {
	echo '<link type="text/css" id="css3-buttons" rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/css3-buttons/css/css3-buttons.css">';
}
add_shortcode("button", "sc_button");
add_filter('the_posts', 'button_is_used'); // the_posts gets triggered before wp_head
function button_is_used($posts){
	if (empty($posts)) return $posts;
 
	$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
	foreach ($posts as $post) {
		if (stripos($post->post_content, '[/button]')) {
			$shortcode_found = true; // bingo!
			break;
		}
	}
 
	if ($shortcode_found) {
		wp_enqueue_style('css3-buttons', WP_PLUGIN_URL . '/css3-buttons/css/css3-buttons.css');

	}
 
	return $posts;
}


add_action('admin_menu', 'css3_buttons_menu');
 
function css3_buttons_menu() {
  // cette fonction rajoute une entrée dans le menu "Réglages"
  add_options_page('CSS3 Buttons Options', __( 'CSS3 Buttons', 'css3_buttons' ), 'manage_options', __FILE__, 'css3_buttons_options');
  // pour voir les niveaux des users: http://codex.wordpress.org/User_Levels
}
function checkMe($val,$opt) {
	$re="";
	if ( $opt==$val ) {
		$re='checked="checked"';
	}
	return $re;
}
function add_js_to_admin() {
    $url = get_option('siteurl');
    $url = $url . '/wp-content/plugins/css3-buttons/js/free-color-picker/201a.js';
    echo '<script src="' . $url . '" type="text/javascript"></script>';
}

add_action('admin_head', 'add_js_to_admin');

// votre fonction qui sera appelée lorsque l'administrateur cliquera sur votre lien d'admin
function css3_buttons_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

    // variables for the field and option names 
global $css3_default_button,$css3_default_vals;
    $hidden_field_name = 'css3_submit_hidden';
    $css3_field_name = 'css3_name';
	$css3_field_color = 'css3_color';
	$css3_field_size = 'css3_size';
	$css3_field_link = 'css3_link';
	$css3_field_link_target = 'css3_target';

    // Read in existing option value from database
    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
			$css3_default_vals= array (
			'css3_opt_name_val' => $_POST[ $css3_field_name ],
			'css3_opt_color_val' => $_POST[ $css3_field_color ],
			'css3_opt_size_val' =>  $_POST[ $css3_field_size ],
			'css3_opt_link_val' => $_POST[ $css3_field_link ],
			'css3_opt_target_val' => $_POST[ $css3_field_link_target ]
		);
        // Save the posted value in the database
		if (update_option( $css3_default_button, $css3_default_vals ) ) {
			$css3_default_vals = get_option( $css3_default_button );
        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'css3_buttons' ); ?></strong></p></div>
<?php
		} else {
			?>
            <div class="error"><p><strong><?php _e('error saving data.', 'css3_buttons' ); ?></strong></p></div>
            <?php
		}
    }

    // Now display the settings editing screen
    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'CSS3 Buttons', 'css3_buttons' ) . "</h2>";
    // settings form
    ?>


<form name="form1" method="post" action="">
<div id="poststuff" class="metabox-holder has-right-sidebar"> 

<div style="float: left; width: 60%;">

<div class="postbox">
<h3 style="cursor: pointer;"><span><?php _e("CSS3 Buttons informations", 'css3_buttons' ); ?></span></h3>
<div>
<table class="form-table">
<tbody>
		<tr class="alternate" valign="top"> 
			<th scope="row" style="width: 20%;"><label for="css3_default_button[info1]"><?php _e("How to use it:", 'css3_buttons' ); ?> </label></th>
        	<td><?php _e("To add a customised button for each post, add the following attributes with the shortcode in you post content:", 'css3_buttons' ); ?><br />
        	<code>[button color= size= link= target=]<?php _e("text that will be displayed as the value on the button", 'css3_buttons' ); ?>[/button]</code><br />
        	<ul><strong><?php _e("Attributes that can be used: ", 'css3_buttons' ); ?></strong>
        		<li style="margin-left:20px; list-style:circle;"><strong>color</strong>: <?php _e("There is 7 different colors", 'css3_buttons' ); ?>: <em>pink</em>, <em>magenta</em>, <em>green</em>, <em>red</em>, <em>orange</em>, <em>blue</em> <?php _e("and", 'css3_buttons' ); ?> <em>yellow</em></li>
        		<li style="margin-left:20px; list-style:circle;"><strong>size</strong>: <?php _e("There is 4 different sizes", 'css3_buttons' ); ?>: <em>small</em>, <em>medium</em>, <em>large</em> <?php _e("and", 'css3_buttons' ); ?> <em>super</em></li>
        		<li style="margin-left:20px; list-style:circle;"><strong>link</strong>: <?php _e("The link of the button", 'css3_buttons' ); ?>. <?php _e("Example", 'css3_buttons' ); ?>: <?php _e("Your site's URL", 'css3_buttons' ); ?> <em><?php echo get_option('siteurl'); ?></em></li>
        		<li style="margin-left:20px; list-style:circle;"><strong>target</strong>: <?php _e("The link's targeted window, can be", 'css3_buttons' ); ?>: <em>blank</em>, <em>new</em>, <em>parent</em>, <em>self</em> <?php _e("or", 'css3_buttons' ); ?> <em>top</em></li>
        	</ul>
        </td></tr>
        <tr class="alternate" valign="top"> 
			<th scope="row" style="width: 20%;"><label for="css3_default_button[info1]"><?php _e("Tip", 'css3_buttons' ); ?>: </label></th>
        	<td><?php _e("If you don't add one of the attributes shown above, the defaut attribute will be used (see below).", 'css3_buttons' ); ?><br />
            </td>
        </tr>

</tbody></table></div></div>

<div id="colorpicker201" class="colorpicker201"></div>

<div class="postbox">
<h3 style="cursor: pointer;"><span><?php _e("Default CSS3 button settings", 'css3_buttons' ); ?></span></h3>
<div>
<table class="form-table">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<tbody><tr class="alternate" valign="top"> 
		<th scope="row" style="width: 40%;"><label for="css3_default_button[color]"><font color="#FF0000">BETA</font> <?php _e("Default CSS3 button color:", 'css3_buttons' ); ?></label></th>
        
<td><input type="text" name="<?php echo $css3_field_color; ?>" value="<?php echo $css3_default_vals ['css3_opt_color_val']; ?>" size="10" id="css3_color_code" onclick="showColorGrid2('css3_color_code','css3_color_sample');"> <input type="text" size="1" id="css3_color_sample" disabled="disabled" style="background-color:<?php echo $css3_default_vals ['css3_opt_color_val']; ?>;" /></td></tr>
<tr class="alternate" valign="top"> 
		<th scope="row" style="width: 40%;"><label for="css3_default_button[name]"><?php _e("Default CSS3 button name:", 'css3_buttons' ); ?> </label></th>
        <td><input type="text" name="<?php echo $css3_field_name; ?>" value="<?php echo $css3_default_vals ['css3_opt_name_val']; ?>" size="20"></td></tr>
<tr class="alternate" valign="top"> 
		<th scope="row" style="width: 40%;"><label for="css3_default_button[size]"><?php _e("Default CSS3 button size:", 'css3_buttons' ); ?> </label></th>
<td><input type="radio" name="<?php echo $css3_field_size; ?>" value="small" size="20" <?php echo checkMe("small",$css3_default_vals ['css3_opt_size_val']); ?>><?php _e("Small", 'css3_buttons' ); ?>
&nbsp;<input type="radio" name="<?php echo $css3_field_size; ?>" value="medium" size="20" <?php echo checkMe("medium",$css3_default_vals ['css3_opt_size_val']); ?>><?php _e("Medium", 'css3_buttons' ); ?>
&nbsp;<input type="radio" name="<?php echo $css3_field_size; ?>" value="large" size="20"<?php echo checkMe("large",$css3_default_vals ['css3_opt_size_val']); ?>><?php _e("Large", 'css3_buttons' ); ?>
&nbsp;<input type="radio" name="<?php echo $css3_field_size; ?>" value="super" size="20"<?php echo checkMe("super",$css3_default_vals ['css3_opt_size_val']); ?>><?php _e("Super", 'css3_buttons' ); ?>
</td></tr>
<tr class="alternate" valign="top"> 
		<th scope="row" style="width: 40%;"><label for="css3_default_button[link]"><?php _e("Default CSS3 button link:", 'css3_buttons' ); ?> </label></th>
        <td><input type="text" name="<?php echo $css3_field_link; ?>" value="<?php echo $css3_default_vals ['css3_opt_link_val']; ?>" size="20"></td></tr>
<tr class="alternate" valign="top"> 
		<th scope="row" style="width: 40%;"><label for="css3_default_button[target]"><?php _e("Default CSS3 button's link target:", 'css3_buttons' ); ?> </label></th>
<td>
<input type="radio" name="<?php echo $css3_field_link_target ?>" value="blank" size="20" <?php echo checkMe("blank",$css3_default_vals ['css3_opt_target_val']); ?>><?php _e("Blank page", 'css3_buttons' ); ?> <em>(_blank)</em> <br />
<input type="radio" name="<?php echo $css3_field_link_target ?>" value="new" size="20" <?php echo checkMe("new",$css3_default_vals ['css3_opt_target_val']); ?>><?php _e("New page", 'css3_buttons' ); ?> <em>(_new)</em> <br />
<input type="radio" name="<?php echo $css3_field_link_target ?>" value="parent" size="20"<?php echo checkMe("parent",$css3_default_vals ['css3_opt_target_val']); ?>><?php _e("Parent page", 'css3_buttons' ); ?> <em>(_parent)</em> <br /><input type="radio" name="<?php echo $css3_field_link_target ?>" value="self" size="20"<?php echo checkMe("self",$css3_default_vals ['css3_opt_target_val']); ?>><?php _e("Same page", 'css3_buttons' ); ?> <em>(_self)</em> <br />
<input type="radio" name="<?php echo $css3_field_link_target ?>" value="top" size="20"<?php echo checkMe("top",$css3_default_vals ['css3_opt_target_val']); ?>><?php _e("Top page", 'css3_buttons' ); ?> <em>(_top)</em> <br />
</td></tr>
<tr class="alternate" valign="top"> 
		<th scope="row" style="width: 40%;"><label for="css3_default_button[info2]"><?php _e("How to add the default button:", 'css3_buttons' ); ?> </label></th>
        <td><?php  _e("Simply add this shortcode in you posts: ", 'css3_buttons' ); ?><code>[button]</code><br />
        <?php  _e("Example", 'css3_buttons' ); ?>: <code>[button]<?php _e("Demo", 'css3_buttons' ); ?>[/button]</code><br /></td></tr>
</tbody></table></div></div>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>"  />
</p>
</div></div>
</form>
</div>

<?php


}

?>