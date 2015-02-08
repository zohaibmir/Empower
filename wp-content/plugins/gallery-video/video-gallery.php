<?php

/*
Plugin Name: Huge IT Video Gallery
Plugin URI: http://huge-it.com/video-allery/
Description: Video Gallery plugin was created and specifically designed to show your video files in unusual splendid ways.
Version: 1.0.9
Author: http://huge-it.com/
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/




add_action('media_buttons_context', 'add_videogallery_my_custom_button');


add_action('admin_footer', 'add_videogallery_inline_popup_content');


function add_videogallery_my_custom_button($context) {
  

  $img = plugins_url( '/images/post.button.png' , __FILE__ );
  

  $container_id = 'huge_it_videogallery';
  

  $title = 'Select Huge IT Video Gallery to insert into post';

  $context .= '<a class="button thickbox" title="Select Video Gallery to insert into post"    href="#TB_inline?width=400&inlineId='.$container_id.'">
		<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
	Add Video Gallery
	</a>';
  
  return $context;
}

function add_videogallery_inline_popup_content() {
?>
<script type="text/javascript">
				jQuery(document).ready(function() {
				  jQuery('#hugeitvideogalleryinsert').on('click', function() {
				  	var id = jQuery('#huge_it_videogallery-select option:selected').val();
			
				  	window.send_to_editor('[huge_it_videogallery id="' + id + '"]');
					tb_remove();
				  })
				});
</script>

<div id="huge_it_videogallery" style="display:none;">
  <h3>Select Huge IT Video Gallery to insert into post</h3>
  <?php 
  	  global $wpdb;
	  $query="SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries order by id ASC";
			   $shortcodevideogallerys=$wpdb->get_results($query);
			   ?>

 <?php 	if (count($shortcodevideogallerys)) {
							echo "<select id='huge_it_videogallery-select'>";
							foreach ($shortcodevideogallerys as $shortcodevideogallery) {
								echo "<option value='".$shortcodevideogallery->id."'>".$shortcodevideogallery->name."</option>";
							}
							echo "</select>";
							echo "<button class='button primary' id='hugeitvideogalleryinsert'>Insert Video Gallery</button>";
						} else {
							echo "No slideshows found", "huge_it_videogallery";
						}
						?>
	
</div>
<?php
}
///////////////////////////////////shortcode update/////////////////////////////////////////////


add_action('init', 'hugesl_videogallery_do_output_buffer');
function hugesl_videogallery_do_output_buffer() {
        ob_start();
}
add_action('init', 'videogallery_lang_load');

function videogallery_lang_load()
{
    load_plugin_textdomain('sp_videogallery', false, basename(dirname(__FILE__)) . '/Languages');

}


function huge_it_videogallery_images_list_shotrcode($atts)
{
    extract(shortcode_atts(array(
        'id' => 'no huge_it videogallery',
    
    ), $atts));




    return huge_it_videogallery_images_list($atts['id']);

}


/////////////// Filter videogallery


function videogallery_after_search_results($query)
{
    global $wpdb;
    if (isset($_REQUEST['s']) && $_REQUEST['s']) {
        $serch_word = htmlspecialchars(($_REQUEST['s']));
        $query = str_replace($wpdb->prefix . "posts.post_content", gen_string_videogallery_search($serch_word, $wpdb->prefix . 'posts.post_content') . " " . $wpdb->prefix . "posts.post_content", $query);
    }
    return $query;

}

add_filter('posts_request', 'videogallery_after_search_results');


function gen_string_videogallery_search($serch_word, $wordpress_query_post)
{
    $string_search = '';

    global $wpdb;
    if ($serch_word) {
        $rows_videogallery = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_it_videogallery_galleries WHERE (description LIKE %s) OR (name LIKE %s)", '%' . $serch_word . '%', "%" . $serch_word . "%"));

        $count_cat_rows = count($rows_videogallery);

        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_videogallery id="' . $rows_videogallery[$i]->id . '" details="1" %\' OR ' . $wordpress_query_post . ' LIKE \'%[huge_it_videogallery id="' . $rows_videogallery[$i]->id . '" details="1"%\' OR ';
        }
		
        $rows_videogallery = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_it_videogallery_galleries WHERE (name LIKE %s)","'%" . $serch_word . "%'"));
        $count_cat_rows = count($rows_videogallery);
        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_videogallery id="' . $rows_videogallery[$i]->id . '" details="0"%\' OR ' . $wordpress_query_post . ' LIKE \'%[huge_it_videogallery id="' . $rows_videogallery[$i]->id . '" details="0"%\' OR ';
        }

        $rows_single = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_it_videogallery_videos WHERE name LIKE %s","'%" . $serch_word . "%'"));

        $count_sing_rows = count($rows_single);
        if ($count_sing_rows) {
            for ($i = 0; $i < $count_sing_rows; $i++) {
                $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_videogallery_Product id="' . $rows_single[$i]->id . '"]%\' OR ';
            }

        }
    }
    return $string_search;
}


///////////////////// end filter


add_shortcode('huge_it_videogallery', 'huge_it_videogallery_images_list_shotrcode');




function   huge_it_videogallery_images_list($id)
{

    require_once("Front_end/video_gallery_front_end_view.php");
    require_once("Front_end/video_gallery_front_end_func.php");
    if (isset($_GET['product_id'])) {
        if (isset($_GET['view'])) {
            if ($_GET['view'] == 'huge_itvideogallery') {
                return showPublishedvideogallery_1($id);
            } else {
                return front_end_single_product($_GET['product_id']);
            }
        } else {
            return front_end_single_product($_GET['product_id']);
        }
    } else {
        return showPublishedvideogallery_1($id);
    }
}




add_filter('admin_head', 'huge_it_videogallery_ShowTinyMCE');
function huge_it_videogallery_ShowTinyMCE()
{
    // conditions here
    wp_enqueue_script('common');
    wp_enqueue_script('jquery-color');
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) add_thickbox();
    wp_print_scripts('media-upload');
    if (version_compare(get_bloginfo('version'), 3.3) < 0) {
        if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    }
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
}


function all_videogallery_frontend_scripts_and_styles() {
    wp_register_script('videogallery_jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', __FILE__ ); 
    wp_enqueue_script('videogallery_jquery');
//    wp_register_script('colorbox-js', plugins_url('/js/jquery.colorbox.js', __FILE__)); 
//    wp_enqueue_script('colorbox-js');
//    wp_register_script('hugeitmicro-js', plugins_url('/js/jquery.hugeitmicro.min.js', __FILE__)); 
//    wp_enqueue_script('hugeitmicro-js');
    wp_register_script('video_gallery-all-js', plugins_url('/js/video_gallery-all.js', __FILE__)); 
    wp_enqueue_script('video_gallery-all-js' );
    
    wp_register_style( 'style2-os-css', plugins_url('/style/style2-os.css', __FILE__) );
    wp_enqueue_style( 'style2-os-css' );
    wp_register_style( 'lightbox-css', plugins_url('/style/lightbox.css', __FILE__) );   
    wp_enqueue_style( 'lightbox-css' );
    wp_register_style( 'videogallery-all-css', plugins_url('/style/videogallery-all.css', __FILE__) );   
    wp_enqueue_style( 'videogallery-all-css');
}
add_action('wp_enqueue_scripts', 'all_videogallery_frontend_scripts_and_styles');

add_action('admin_menu', 'huge_it_videogallery_options_panel');
function huge_it_videogallery_options_panel()
{
    $page_cat = add_menu_page('Theme page title', 'Video Gallery', 'manage_options', 'videogallerys_huge_it_videogallery', 'videogallerys_huge_it_videogallery', plugins_url('images/video_gallery_icon.png', __FILE__));
    $page_option = add_submenu_page('videogallerys_huge_it_videogallery', 'General Options', 'General Options', 'manage_options', 'Options_videogallery_styles', 'Options_videogallery_styles');
    $lightbox_options = add_submenu_page('videogallerys_huge_it_videogallery', 'Lightbox Options', 'Lightbox Options', 'manage_options', 'Options_videogallery_lightbox_styles', 'Options_videogallery_lightbox_styles');
	add_submenu_page( 'videogallerys_huge_it_videogallery', 'Licensing', 'Licensing', 'manage_options', 'huge_it_video_gallery_Licensing', 'huge_it_video_gallery_Licensing');
	
	add_submenu_page('videogallerys_huge_it_videogallery', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'huge_it__videogallery_featured_plugins', 'huge_it__videogallery_featured_plugins');

	add_action('admin_print_styles-' . $page_cat, 'huge_it_videogallery_admin_script');
    add_action('admin_print_styles-' . $page_option, 'huge_it_videogallery_option_admin_script');
    add_action('admin_print_styles-' . $lightbox_options, 'huge_it_videogallery_option_admin_script');
}

function huge_it__videogallery_featured_plugins()
{
	include_once("admin/huge_it_featured_plugins.php");
}

function huge_it_video_gallery_Licensing(){

	?>
    <div style="width:95%">
    <p>
	This plugin is the non-commercial version of the Huge IT Video Gallery. If you want to customize to the styles and colors of your website,than you need to buy a license.
Purchasing a license will add possibility to customize the general options and lightbox of the Huge IT Video Gallery. 

 </p>
<br /><br />
<a href="http://huge-it.com/video-gallery/" class="button-primary" target="_blank">Purchase a License</a>
<br /><br /><br />
<p>After the purchasing the commercial version follow this steps:</p>
<ol>
	<li>Deactivate Huge IT Video Gallery Plugin</li>
	<li>Delete Huge IT Video Gallery Plugin</li>
	<li>Install the downloaded commercial version of the plugin</li>
</ol>
</div>
<?php
	}

function huge_it_videogallery_admin_script()
{
	wp_enqueue_media();
	wp_enqueue_style("jquery_ui", plugins_url("style/jquery-ui.css", __FILE__), FALSE);
	wp_enqueue_style("admin_css", plugins_url("style/admin.style.css", __FILE__), FALSE);
	wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__), FALSE);
}


function huge_it_videogallery_option_admin_script()
{
	wp_enqueue_media();
	wp_enqueue_script("simple_slider_js",  plugins_url("js/simple-slider.js", __FILE__), FALSE);
	wp_enqueue_style("simple_slider_css", plugins_url("style/simple-slider_sl.css", __FILE__), FALSE);
	wp_enqueue_style("admin_css", plugins_url("style/admin.style.css", __FILE__), FALSE);
	wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__), FALSE);
	wp_enqueue_script('param_block2', plugins_url("elements/jscolor/jscolor.js", __FILE__));
}


function videogallerys_huge_it_videogallery()
{

    require_once("admin/video_gallery_func.php");
    require_once("admin/video_gallery_view.php");
    if (!function_exists('print_html_nav'))
        require_once("videogallery_function/html_videogallery_func.php");


    if (isset($_GET["task"]))
        $task = $_GET["task"]; 
    else
        $task = '';
    if (isset($_GET["id"]))
        $id = $_GET["id"];
    else
        $id = 0;
    global $wpdb;
    switch ($task) {

        case 'add_cat':
            add_videogallery();
            break;

		case 'popup_posts':
            if ($id)
                popup_posts($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_it_videogallery_galleries");
                popup_posts($id);
            }
            break;
		case 'videogallery_video':
            if ($id)
                videogallery_video($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_it_videogallery_galleries");
                videogallery_video($id);
            }
            break;
        case 'edit_cat':
            if ($id)
                editvideogallery($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_it_videogallery_galleries");
                editvideogallery($id);
            }
            break;

        case 'save':
            if ($id)
                apply_cat($id);
        case 'apply':
            if ($id) {
                apply_cat($id);
                editvideogallery($id);
            } 
            break;
        case 'remove_cat':
            removevideogallery($id);
            showvideogallery();
            break;
        default:
            showvideogallery();
            break;
    }


}
do_action('toplevel_page_videogallerys_huge_it_videogallery');

function Options_videogallery_styles()
{
    require_once("admin/video_gallery_Options_func.php");
    require_once("admin/video_gallery_Options_view.php");
    if (isset($_GET['task']))
        if ($_GET['task'] == 'save')
            save_styles_options();
    showStyles();
}
function Options_videogallery_lightbox_styles()
{
    require_once("admin/video_gallery_lightbox_func.php");
    require_once("admin/video_gallery_lightbox_view.php");
    if (isset($_GET['task']))
        if ($_GET['task'] == 'save')
            save_styles_options();
    showStyles();
}



/**
 * Huge IT Widget
 */
class Huge_it_videogallery_Widget extends WP_Widget {


	public function __construct() {
		parent::__construct(
	 		'Huge_it_videogallery_Widget', 
			'Huge IT Video Gallery', 
			array( 'description' => __( 'Huge IT Video Gallery', 'huge_it_videogallery' ), ) 
		);
	}

	
	public function widget( $args, $instance ) {
		extract($args);

		if (isset($instance['videogallery_id'])) {
			$videogallery_id = $instance['videogallery_id'];

			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			echo do_shortcode("[huge_it_videogallery id={$videogallery_id}]");
			echo $after_widget;
		}
	}


	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['videogallery_id'] = strip_tags( $new_instance['videogallery_id'] );
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}


	public function form( $instance ) {
		$selected_videogallery = 0;
		$title = "";
		$videogallerys = false;

		if (isset($instance['videogallery_id'])) {
			$selected_videogallery = $instance['videogallery_id'];
		}

		if (isset($instance['title'])) {
			$title = $instance['title'];
		}

        

        
		?>
		<p>
			
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<label for="<?php echo $this->get_field_id('videogallery_id'); ?>"><?php _e('Select videogallery:', 'huge_it_videogallery'); ?></label> 
				<select id="<?php echo $this->get_field_id('videogallery_id'); ?>" name="<?php echo $this->get_field_name('videogallery_id'); ?>">
				
				<?php
				 global $wpdb;
				$query="SELECT * FROM ".$wpdb->prefix."huge_it_videogallery_galleries ";
				$rowwidget=$wpdb->get_results($query);
				foreach($rowwidget as $rowwidgetecho){
				
				
				?>
					<option <?php if($rowwidgetecho->id == $instance['videogallery_id']){ echo 'selected'; } ?> value="<?php echo $rowwidgetecho->id; ?>"><?php echo $rowwidgetecho->name; ?></option>

					<?php } ?>
				</select>

		</p>
		<?php 
	}
}

add_action('widgets_init', 'register_Huge_it_videogallery_Widget');  

function register_Huge_it_videogallery_Widget() {  
    register_widget('Huge_it_videogallery_Widget'); 
}



//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////               Activate videogallery                     ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////


function huge_it_videogallery_activate()
{
    global $wpdb;

/// creat database tables



    $sql_huge_it_videogallery_params = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_it_videogallery_params`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `value` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ";


    $sql_huge_it_videogallery_videos = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_it_videogallery_videos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `videogallery_id` varchar(200) DEFAULT NULL,
  `description` text,
  `image_url` text,
  `sl_url` varchar(128) DEFAULT NULL,
  `sl_type` text NOT NULL,
  `link_target` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  `published_in_sl_width` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5";

    $sql_huge_it_videogallery_galleries = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_it_videogallery_galleries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sl_height` int(11) unsigned DEFAULT NULL,
  `sl_width` int(11) unsigned DEFAULT NULL,
  `pause_on_hover` text,
  `videogallery_list_effects_s` text,
  `description` text,
  `param` text,
  `sl_position` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` text,
   `huge_it_sl_effects` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ";



    $table_name = $wpdb->prefix . "huge_it_videogallery_params";
    $sql_1 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES

/*############################## VIEW 0 Popup #####################################*/

('ht_view2_element_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view2_element_show_linkbutton', 'Show Link Button On Element', 'Show Link Button On Element', 'on'),
('ht_view2_element_linkbutton_color', 'Element Link Button Font Color', 'Element Link Button Font Color', 'ffffff'),
('ht_view2_element_linkbutton_font_size', 'Element Link Button Font Size', 'Element Link Button Font Size', '14'),
('ht_view2_element_linkbutton_background_color', 'Element Link Button Background Color', 'Element Link Button Background Color', 'FF2C2C'),
('ht_view2_show_popup_linkbutton', 'Show Link Button On Popup', 'Show Link Button On Popup', 'on'),
('ht_view2_popup_linkbutton_text', 'Popup Link Button Text', 'Link Button Text', 'View More'),
('ht_view2_popup_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', 'c02121'),
('ht_view2_popup_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', 'FF2C2C'),
('ht_view2_popup_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view2_popup_linkbutton_color', 'Element Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view2_popup_linkbutton_font_size', 'Element Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view2_description_color', 'Description Font Color', 'Description Font Color', '222222'),
('ht_view2_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view2_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view2_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view2_thumbs_height', 'Thumbnails Height', 'Thumbnails Height', '75'),
('ht_view2_thumbs_position', 'Thumbnails Position', 'Thumbnails Position', 'before'),
('ht_view2_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view2_popup_background_color', 'Popup Background Color', 'Popup Background Color', 'FFFFFF'),
('ht_view2_popup_overlay_color', 'Popup Overlay Color', 'Popup Overlay Color', '000000'),
('ht_view2_popup_overlay_transparency_color', 'Popup Overlay Transparency', 'Popup Overlay Transparency ', '70'),
('ht_view2_popup_closebutton_style', 'Popup Close Button Style', 'Popup Close Button Style', 'dark'),
('ht_view2_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view2_show_popup_title', 'Show Popup Title', 'Show Popup Title','on'),
('ht_view2_element_title_font_size', 'Element Title Font Size', 'Element Title Font Size', '18'),
('ht_view2_element_title_font_color', 'Element Title Font Color', 'Element Title Font Color', '222222'),
('ht_view2_popup_title_font_size', 'Popup Title Font Size', 'Popup Title Font Size', '18'),
('ht_view2_popup_title_font_color', 'Popup Title Font Color', 'Popup Title Font Color', '222222'),
('ht_view2_element_overlay_color', 'Element Overlay Color', 'Element Overlay Color', 'FFFFFF'),
('ht_view2_element_overlay_transparency', 'Element Overlay Transparency', 'Element Overlay Transparency ', '70'),
('ht_view2_zoombutton_style', 'Zoom Button Style', 'Zoom Button Style','light'),
('ht_view2_element_border_width', 'Element Border Width', 'Element Border Width', '1'),
('ht_view2_element_border_color', 'Element Border Color', 'Element Border Color', 'dedede'),
('ht_view2_element_background_color', 'Element Background Color', 'Element Background Color', 'f9f9f9'),
('ht_view2_element_width', 'Block Width', 'Block Width', '275'),
('ht_view2_element_height', 'Block Height', 'Block Height', '160'),


/*############################## VIEW 1 SLIDER #####################################*/

('ht_view5_icons_style', 'Icons Style', 'Icons Style','dark'),
('ht_view5_show_separator_lines', 'Show Separator Lines', 'Show Separator Lines','on'),
('ht_view5_linkbutton_text', 'Link Button Text', 'Link Button Text', 'View More'),
('ht_view5_show_linkbutton', 'Show Link Button', 'Show Link Button', 'on'),
('ht_view5_linkbutton_background_hover_color', 'Link Button Background Hover Color', 'Link Button Background Hover Color', 'c02121'),
('ht_view5_linkbutton_background_color', 'Link Button Background Color', 'Link Button Background Color', 'FF2C2C'),
('ht_view5_linkbutton_font_hover_color', 'Link Button Font Hover Color', 'Link Button Font Hover Color', 'ffffff'),
('ht_view5_linkbutton_color', 'Link Button Font Color', 'Link Button Font Color', 'ffffff'),
('ht_view5_linkbutton_font_size', 'Link Button Font Size', 'Link Button Font Size', '14'),
('ht_view5_description_color', 'Description Font Color', 'Description Font Color', '555555'),
('ht_view5_description_font_size', 'Description Font Size', 'Description Font Size', '14'),
('ht_view5_show_description', 'Show Description', 'Show Description', 'on'),
('ht_view5_thumbs_width', 'Thumbnails Width', 'Thumbnails Width', '75'),
('ht_view5_thumbs_height', 'Thumbnails Height', 'Thumbnails Hight', '75'),
('ht_view5_show_thumbs', 'Show Thumbnails', 'Show Thumbnails', 'on'),
('ht_view5_title_font_size', 'Title Font Size', 'Title Font Size', '16'),
('ht_view5_title_font_color', 'Title Font Color', 'Title Font Color', 'c02121'),
('ht_view5_main_image_width', 'Main Image Width', 'Main Image Width', '275'),
('ht_view5_slider_tabs_font_color', 'Slider Tabs Font Color', 'Slider Tabs Font Color', 'd9d99'),
('ht_view5_slider_tabs_background_color', 'Slider Tabs Background Color', 'Slider Tabs Background Color', '555555'),
('ht_view5_slider_background_color', 'Slider Background Color', 'Slider Background Color', 'f9f9f9'),

/*############################## VIEW 2 Lightbox-videogallery #####################################*/

('ht_view6_title_font_size', 'Title Font Size', 'Title Font Size', '16'),
('ht_view6_title_font_color', 'Title Font Color', 'Title Font Color', 'c02121'),
('ht_view6_title_font_hover_color', 'Title Font Hover Color', 'Title Font Hover Color', 'FF2C2C'),
('ht_view6_title_background_color', 'Title Background Color', 'Title Background Color', '000000'),
('ht_view6_title_background_transparency', 'Title Background Transparency', 'Title Background Transparency', '80'),
('ht_view6_border_radius', 'Image Border Radius', 'Image Border Radius', '3'),
('ht_view6_border_width', 'Image Border Width', 'Image Border Width', '0'),
('ht_view6_border_color', 'Image Border Color', 'Image Border Color', 'eeeeee'),
('ht_view6_width', 'Image Width', 'Image Width', '275'),

/*############################## Lightbox #####################################*/

('light_box_size', 'Light box size', 'Light box size', '17'),
('light_box_width', 'Light Box width', 'Light Box width', '800'),
('light_box_transition', 'Light Box Transition', 'Light Box Transition', 'elastic'),
('light_box_speed', 'Light box speed', 'Light box speed', '800'),
('light_box_href', 'Light box href', 'Light box href', 'False'),
('light_box_title', 'Light box Title', 'Light box Title', 'false'),
('light_box_scalephotos', 'Light box scalePhotos', 'Light box scalePhotos', 'true'),
('light_box_rel', 'Light Box rel', 'Light Box rel', 'false'),
('light_box_scrolling', 'Light box Scrollin', 'Light box Scrollin', 'false'),
('light_box_opacity', 'Light box Opacity', 'Light box Opacity', '20'),
('light_box_open', 'Light box Open', 'Light box Open', 'false'),
('light_box_overlayclose', 'Light box overlayClose', 'Light box overlayClose', 'true'),
('light_box_esckey', 'Light box escKey', 'Light box escKey', 'false'),
('light_box_arrowkey', 'Light box arrowKey', 'Light box arrowKey', 'false'),
('light_box_loop', 'Light box loop', 'Light box loop', 'true'),
('light_box_data', 'Light box data', 'Light box data', 'false'),
('light_box_classname', 'Light box className', 'Light box className', 'false'),
('light_box_fadeout', 'Light box fadeOut', 'Light box fadeOut', '300'),
('light_box_closebutton', 'Light box closeButton', 'Light box closeButton', 'false'),
('light_box_current', 'Light box current', 'Light box current', 'image'),
('light_box_previous', 'Light box previous', 'Light box previous', 'previous'),
('light_box_next', 'Light box next', 'Light box next', 'next'),
('light_box_close', 'Light box close', 'Light box close', 'close'),
('light_box_iframe', 'Light box iframe', 'Light box iframe', 'false'),
('light_box_inline', 'Light box inline', 'Light box inline', 'false'),
('light_box_html', 'Light box html', 'Light box html', 'false'),
('light_box_photo', 'Light box photo', 'Light box photo', 'false'),
('light_box_height', 'Light box height', 'Light box height', '600'),
('light_box_innerwidth', 'Light box innerWidth', 'Light box innerWidth', 'false'),
('light_box_innerheight', 'Light box innerHeight', 'Light box innerHeight', 'false'),
('light_box_initialwidth', 'Light box initialWidth', 'Light box initialWidth', '300'),
('light_box_initialheight', 'Light box initialHeight', 'Light box initialHeight', '100'),
('light_box_maxwidth', 'Light box maxWidth', 'Light box maxWidth', ''),
('light_box_maxheight', 'Light box maxHeight', 'Light box maxHeight', ''),
('light_box_slideshow', 'Light box slideshow', 'Light box slideshow', 'false'),
('light_box_slideshowspeed', 'Light box slideshowSpeed', 'Light box slideshowSpeed', '2500'),
('light_box_slideshowauto', 'Light box slideshowAuto', 'Light box slideshowAuto', 'true'),
('light_box_slideshowstart', 'Light box slideshowStart', 'Light box slideshowStart', 'start slideshow'),
('light_box_slideshowstop', 'Light box slideshowStop', 'Light box slideshowStop', 'stop slideshow'),
('light_box_fixed', 'Light box fixed', 'Light box fixed', 'true'),
('light_box_top', 'Light box top', 'Light box top', 'false'),
('light_box_bottom', 'Light box bottom', 'Light box bottom', 'false'),
('light_box_left', 'Light box left', 'Light box left', 'false'),
('light_box_right', 'Light box right', 'Light box right', 'false'),
('light_box_reposition', 'Light box reposition', 'Light box reposition', 'false'),
('light_box_retinaimage', 'Light box retinaImage', 'Light box retinaImage', 'true'),
('light_box_retinaurl', 'Light box retinaUrl', 'Light box retinaUrl', 'false'),
('light_box_retinasuffix', 'Light box retinaSuffix', 'Light box retinaSuffix', '@2x.$1'),
('light_box_returnfocus', 'Light box returnFocus', 'Light box returnFocus', 'true'),
('light_box_trapfocus', 'Light box trapFocus', 'Light box trapFocus', 'true'),
('light_box_fastiframe', 'Light box fastIframe', 'Light box fastIframe', 'true'),
('light_box_preloading', 'Light box preloading', 'Light box preloading', 'true'),
('lightbox_open_position', 'Lightbox open position', 'Lightbox open position', '5'),
('light_box_style', 'Light Box style', 'Light Box style', '1'),
('light_box_size_fix', 'Light Box size fix style', 'Light Box size fix style', 'false'),

/*############################## Huge IT Slider #####################################*/

('slider_crop_image', 'Slider crop image', 'Slider crop image', 'crop'),
('slider_title_color', 'Slider title color', 'Slider title color', '000000'),
('slider_title_font_size', 'Slider title font size', 'Slider title font size', '13'),
('slider_description_color', 'Slider description color', 'Slider description color', 'ffffff'),
('slider_description_font_size', 'Slider description font size', 'Slider description font size', '12'),
('slider_title_position', 'Slider title position', 'Slider title position', 'right-top'),
('slider_description_position', 'Slider description position', 'Slider description position', 'right-bottom'),
('slider_title_border_size', 'Slider Title border size', 'Slider Title border size', '0'),
('slider_title_border_color', 'Slider title border color', 'Slider title border color', 'ffffff'),
('slider_title_border_radius', 'Slider title border radius', 'Slider title border radius', '4'),
('slider_description_border_size', 'Slider description border size', 'Slider description border size', '0'),
('slider_description_border_color', 'Slider description border color', 'Slider description border color', 'ffffff'),
('slider_description_border_radius', 'Slider description border radius', 'Slider description border radius', '0'),
('slider_slideshow_border_size', 'Slider border size', 'Slider border size', '0'),
('slider_slideshow_border_color', 'Slider border color', 'Slider border color', 'ffffff'),
('slider_slideshow_border_radius', 'Slider border radius', 'Slider border radius', '0'),
('slider_navigation_type', 'Slider navigation type', 'Slider navigation type', '1'),
('slider_navigation_position', 'Slider navigation position', 'Slider navigation position', 'bottom'),
('slider_title_background_color', 'Slider title background color', 'Slider title background color', 'ffffff'),
('slider_description_background_color', 'Slider description background color', 'Slider description background color', '000000'),
('slider_title_transparent', 'Slider title has background', 'Slider title has background', 'on'),
('slider_description_transparent', 'Slider description has background', 'Slider description has background', 'on'),
('slider_slider_background_color', 'Slider slider background color', 'Slider slider background color', 'ffffff'),
('slider_dots_position', 'slider dots position', 'slider dots position', 'none'),
('slider_active_dot_color', 'slider active dot color', '', 'ffffff'),
('slider_dots_color', 'slider dots color', '', '000000'),
('slider_description_width', 'Slider description width', 'Slider description width', '70'),
('slider_description_height', 'Slider description height', 'Slider description height', '50'),
('slider_description_background_transparency', 'slider description background transparency', 'slider description background transparency', '70'),
('slider_description_text_align', 'description text-align', 'description text-align', 'justify'),
('slider_title_width', 'slider title width', 'slider title width', '30'),
('slider_title_height', 'slider title height', 'slider title height', '50'),
('slider_title_background_transparency', 'slider title background transparency', 'slider title background transparency', '70'),
('slider_title_text_align', 'title text-align', 'title text-align', 'right'),
('slider_title_has_margin', 'title has margin', 'title has margin', 'off'),
('slider_description_has_margin', 'description has margin', 'description has margin', 'off'),
('slider_show_arrows', 'Slider show left right arrows', 'Slider show left right arrows', 'on'),

/*############################## Thumbnail view #####################################*/

('thumb_image_behavior', 'Image Behavior', 'Image Behavior', 'on'),
('thumb_image_width', 'Image widht', 'Image widht', '240'),
('thumb_image_height', 'Image height', 'Image height', '150'),
('thumb_image_border_width', 'Image border width', 'Image border width', '1'),
('thumb_image_border_color', 'Image border color', 'Image border color', '444444'),
('thumb_image_border_radius', 'Image border Radius', 'Image border Radius', '5'),
('thumb_margin_image', 'Margin image', 'Margin image', '1'),
('thumb_title_font_size', 'Title font size', 'Title font size', '16'),
('thumb_title_font_color', 'Title font color', 'Title font color', 'FFFFFF'),
('thumb_title_background_color', 'Title background color', 'Title background color', 'CCCCCC'),
('thumb_title_background_transparency', 'Title Background Transparency', 'Title Background Transparency', '80'),
('thumb_box_padding', 'Box padding', 'Box padding', '28'),
('thumb_box_background', 'Box background', 'Box background', '333333'),
('thumb_box_use_shadow', 'Box use shadow', 'Box use shadow', 'on'),
('thumb_box_has_background', 'Box has background', 'Box has background', 'on'),
('thumb_view_text', 'View Image Text', 'View Image Text', 'Watch Video');


query1;

    $table_name = $wpdb->prefix . "huge_it_videogallery_videos";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` (`id`, `name`, `videogallery_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `ordering`, `published`, `published_in_sl_width`) VALUES
(1, 'People Are Awesome', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 'https://www.youtube.com/watch?v=y_eLZr24iMQ', 'http://huge-it.com', 'video', 'on', 0, 1, NULL),
(2, 'Africa Race', '1', '<ul><li>lorem ipsumdolor sit amet</li><li>lorem ipsum dolor sit amet</li></ul><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 'http://player.vimeo.com/video/62604342', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 1, 1, NULL),
(3, 'London City In Motion', '1', '<h6>Lorem Ipsum </h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><ul><li>lorem ipsum</li><li>dolor sit amet</li><li>lorem ipsum</li><li>dolor sit amet</li></ul>', 'http://player.vimeo.com/video/99310168', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 2, 1, NULL),
(4, 'Dubai City As You have Never Seen It Before', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><h7>Dolor sit amet</h7><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 'https://www.youtube.com/embed/t5vta25jHQI', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 3, 1, NULL),
(5, 'Never say no to a Panda !', '1', '<h6>Lorem Ipsum</h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 'http://player.vimeo.com/video/15371143', 'http://huge-it.com/', 'video', 'on', 4, 1, NULL),
(6, 'INDO-FLU', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p>', 'http://player.vimeo.com/video/103151169', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 5, 1, NULL),
(7, 'People Are Awesome Womens Edition', '1', '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><h6>Lorem Ipsum</h6><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>', 'https://www.youtube.com/embed/tNhzssozKuU', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 6, 1, NULL),
(8, 'Norwey', '1', '<h6>Lorem Ipsum </h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><ul><li>lorem ipsum</li><li>dolor sit amet</li><li>lorem ipsum</li><li>dolor sit amet</li></ul>', 'http://player.vimeo.com/video/31022539', 'http://huge-it.com/fields/order-website-maintenance/', 'video', 'on', 7, 1, NULL),
(9, 'Slow Motion', '1', '<h6>Lorem Ipsum </h6><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. </p><p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p><ul><li>lorem ipsum</li><li>dolor sit amet</li><li>lorem ipsum</li><li>dolor sit amet</li></ul>', 'https://www.youtube.com/embed/gb69WX82Hvs', 'http://huge-it.com/', 'video', 'on', 7, 1, NULL)";


    $table_name = $wpdb->prefix . "huge_it_videogallery_galleries";


    $sql_3 = "

INSERT INTO `$table_name` (`id`, `name`, `sl_height`, `sl_width`, `pause_on_hover`, `videogallery_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`, `huge_it_sl_effects`) VALUES
(1, 'My First Video Gallery', 375, 600, 'on', 'random', '4000', '1000', 'center', 1, '300', '5')";


    $wpdb->query($sql_huge_it_videogallery_params);
    $wpdb->query($sql_huge_it_videogallery_videos);
    $wpdb->query($sql_huge_it_videogallery_galleries);


    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_it_videogallery_params")) {
        $wpdb->query($sql_1);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_it_videogallery_videos")) {
      $wpdb->query($sql_2);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_it_videogallery_galleries")) {
      $wpdb->query($sql_3);
    }
}

register_activation_hook(__FILE__, 'huge_it_videogallery_activate');