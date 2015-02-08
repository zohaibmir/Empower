<?php
/* Developer: Zohaib
 * CompanyName: Vista Studio
 * Date: 02-11-2014 
 */

add_filter('admin_footer_text', 'empower_custom_admin_footer');

function empower_custom_admin_footer() {
    echo '<span id="footer-thankyou">Developed by <a href="http://www.zohaib.se/" target="_blank">Zohaib Mir</a></span>.';
}

/* * *********** THUMBNAIL SIZE OPTIONS ************ */

if (function_exists('add_theme_support')) {
    add_theme_support('post-thumbnails');
}
set_post_thumbnail_size(768, 400);
// Set content width
if (!isset($content_width))
    $content_width = 580;
// Thumbnail sizes
add_image_size('hg-desktop-slider', 768, 9999);
add_image_size('hg-phone-slider', 480, 9999);

function frii_register_sidebars() {
    register_sidebar(array(
        'name' => __('Search Widget', 'frii'),
        'id' => 'sidebar1',
        'description' => __('', 'frii'),
        'before_widget' => '',
        'after_widget' => "",
        'before_title' => '',
        'after_title' => '',
    ));

    register_sidebar(array(
        'id' => 'sidebar2',
        'name' => 'Map',
        'description' => '',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
    ));
    /*
      to add more sidebars or widgetized areas, just copy
      and edit the above sidebar code. In order to call
      your new sidebar just use the following code:

      Just change the name to whatever your new
      sidebar's id is, for example:



      To call the sidebar in your template, you can just copy
      the sidebar.php file and rename it to your sidebar's name.
      So using the above example, it would be:
      sidebar-sidebar2.php

     */
}

add_action('widgets_init', 'frii_register_sidebars');

/*
 *  Add Meta tags in the header
 *  Date: 2013-05-24
 *  Developer: Zohaib
 */

function meta_tags_in_head() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
    echo '<link rel="profile" href="http://gmpg.org/xfn/11" />';
}

//add_action('wp_head', 'meta_tags_in_head');


/*
 *  Add Style sheets
 *  Date: 2013-11-13
 *  Developer: Zohaib
 */

function theme_styles() {
    wp_register_style('style', get_template_directory_uri() . '/style.css', array(), '1.0', 'all');
    wp_register_style('app', get_template_directory_uri() . '/css/app.css', array(), '1.0', 'all');
    wp_enqueue_style('style');
    wp_enqueue_style('app');
}

//add_action('wp_enqueue_scripts', 'theme_styles');

function theme_js() {

    wp_deregister_script('jquery'); // initiate the function    
    wp_register_script('modernizr', get_template_directory_uri() . '/bower_components/modernizr/modernizr.js');
    wp_enqueue_script('modernizr');
}

//add_action('wp_enqueue_scripts', 'theme_js');


/*
 *  Add Favicon
 *  Date: 2013-03-07
 *  Developer: Zohaib
 */

function blog_favicon() {
    echo '<link rel="Shortcut Icon" type="image/x-icon" href="' . get_template_directory_uri() . '/favicon.ico" />';
}

add_action('wp_head', 'blog_favicon');


/*
 *  Stop Update Notifications on admin side
 *  Date: 2013-03-07
 *  Developer: Zohaib
 */

function no_update_notification() {
    remove_action('load-update-core.php', 'wp_update_plugins');
    add_filter('pre_site_transient_update_plugins', create_function('$a', "return null;"));
    if (!current_user_can('activate_plugins'))
        remove_action('admin_notices', 'update_nag', 3);
}

add_action('admin_notices', 'no_update_notification', 1);

/*
 *  Remove unnecessary dashboard widgets
 *  Date: 2013-03-07
 *  Developer: Zohaib
 */

function remove_dashboard_widgets() {
    global $wp_meta_boxes;
// do not remove "Right Now" for administrators
    if (!current_user_can('activate_plugins')) {
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    }
// remove widgets for everyone
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

/*
 *  This Function return the Breadcumb
 *  Date: 2013-03-07
 *  Developer: Zohaib
 */

function get_breadcrumb() {

    global $post;

    $trail = '';
    $page_title = get_the_title($post->ID);

    if ($post->post_parent) {
        $parent_id = $post->post_parent;

        while ($parent_id) {
            $page = get_page($parent_id);
            $breadcrumbs[] = '<li><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
            $parent_id = $page->post_parent;
        }

        $breadcrumbs = array_reverse($breadcrumbs);
        foreach ($breadcrumbs as $crumb)
            $trail .= $crumb;
    }

    $trail .= '<li class="unavailable">' . $page_title . '</li>';
    $trail .= '';

    return $trail;
}

/*
 *  Register the Menu on the site (Primary Navigation is the site main top navigation)
 *  Date: 2013-03-07
 *  Developer: Zohaib
 */
register_nav_menus(array(
    'Privat' => __('Privat Navigation', 'frii'),
));

// mainnav is used in all content pages

register_nav_menus(array(
    'Erhverv' => __('Erhverv Navigation', 'frii'),
));

function new_excerpt_more($more) {
    return ' 	<a href="' . get_permalink($post->ID) . '">' . 'Lees de rest van deze pagina...' . '</a>';
}

add_filter('excerpt_more', 'new_excerpt_more');



class My_Walker_Nav_Menu extends Walker_Nav_Menu {

    function start_lvl(&$output, $depth) {
        $indent = str_repeat("\t", $depth);
        //$output .= "\n$indent<ul class=\"dropdown\"><span class=\"arrow\"></span>\n"; For Arrow in the menu
        $output .= "\n$indent<ul class=\"dropdown\">\n";
    }

}


//Gets post cat slug and looks for single-[cat slug].php and applies it
add_filter('single_template', create_function(
	'$the_template',
	'foreach( (array) get_the_category() as $cat ) {
		if ( file_exists(TEMPLATEPATH . "/single-{$cat->slug}.php") )
		return TEMPLATEPATH . "/single-{$cat->slug}.php"; }
	return $the_template;' )
);
?>