<?php
/*
 * Plugin Name: WP Responsive Video Gallery With Lightbox 
 * Plugin URI:http://www.i13websolution.com 
 * Author URI:http://www.i13websolution.com 
 * Description:This is beautiful responsive video gallery with responsive lightbox.Add any number of video from admin panel. 
 * Author:I Thirteen Web Solution 
 * Version:1.0
 */
error_reporting(0);
add_filter('widget_text', 'do_shortcode');
add_action('admin_menu', 'responsive_video_gallery_plus_lightbox_add_admin_menu');
add_action('admin_init', 'responsive_video_gallery_plus_lightbox_add_admin_init');
register_activation_hook(__FILE__, 'install_responsive_video_gallery_plus_lightbox');
add_action('wp_enqueue_scripts', 'responsive_video_gallery_plus_lightbox_load_styles_and_js');
add_shortcode('print_responsive_video_gallery_plus_lightbox', 'print_responsive_video_gallery_plus_lightbox_func');
add_action('admin_notices', 'responsive_video_gallery_plus_lightbox_admin_notices');

add_action('wp_ajax_check_file_exist', 'check_file_exist_callback');

function check_file_exist_callback() {

    if (isset($_POST) and is_array($_POST) and isset($_POST['url'])) {

        $handle = curl_init($_POST['url']);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($handle);

        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        echo trim((string) $httpCode);
        die;
    }
    //echo die;
}

function responsive_video_gallery_plus_lightbox_admin_notices() {
    if (is_plugin_active('wp-responsive-video-gallery-with-lightbox/wp-responsive-video-gallery-with-lightbox.php')) {

        $uploads = wp_upload_dir();
        $baseDir = $uploads ['basedir'];
        $baseDir = str_replace("\\", "/", $baseDir);
        $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';

        if (file_exists($pathToImagesFolder) and is_dir($pathToImagesFolder)) {

            if (!is_writable($pathToImagesFolder)) {
                echo "<div class='updated'><p>Responsive video gallery with lightbox is active but does not have write permission on</p><p><b>" . $pathToImagesFolder . "</b> directory.Please allow write permission.</p></div> ";
            }
        } else {

            wp_mkdir_p($pathToImagesFolder);
            if (!file_exists($pathToImagesFolder) and !is_dir($pathToImagesFolder)) {
                echo "<div class='updated'><p>Responsive video gallery with lightbox is active but plugin does not have permission to create directory</p><p><b>" . $pathToImagesFolder . "</b> .Please create wp-responsive-video-gallery-with-lightbox directory inside upload directory and allow write permission.</p></div> ";
            }
        }
    }
}

function responsive_video_gallery_plus_lightbox_load_styles_and_js() {
    if (!is_admin()) {

        wp_enqueue_style('wp-video-gallery-lighbox-style', plugins_url('/css/wp-video-gallery-lighbox-style.css', __FILE__));
        wp_enqueue_style('vl-box-css', plugins_url('/css/vl-box-css.css', __FILE__));
        wp_enqueue_script('jquery');
        wp_enqueue_script('video-gallery-jc', plugins_url('/js/video-gallery-jc.js', __FILE__));
        wp_enqueue_script('vl-box-js', plugins_url('/js/vl-box-js.js', __FILE__));
    }
}

function install_responsive_video_gallery_plus_lightbox() {
    global $wpdb;
    $table_name = $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox";

    $sql = "CREATE TABLE " . $table_name . " (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `vtype` varchar(50) COLLATE utf8_general_ci NOT NULL,
        `vid` varchar(500) COLLATE utf8_general_ci NOT NULL,
        `video_url` varchar(1000) COLLATE utf8_general_ci DEFAULT NULL,
        `embed_url` varchar(300) COLLATE utf8_general_ci NOT NULL,
        `HdnMediaSelection` varchar(500) COLLATE utf8_general_ci NOT NULL,
        `image_name` varchar(500) COLLATE utf8_general_ci NOT NULL,
        `videotitle` varchar(1000) COLLATE utf8_general_ci NOT NULL,
        `videotitleurl` varchar(1000) COLLATE utf8_general_ci DEFAULT NULL,
         `video_description` text COLLATE utf8_general_ci DEFAULT NULL,
        `video_order` int(11) NOT NULL DEFAULT '0',
        `open_link_in` tinyint(1) NOT NULL DEFAULT '1',
        `enable_light_box_video_desc` tinyint(1) NOT NULL DEFAULT '1',
        `createdon` datetime NOT NULL,
        `slider_id` int(10) unsigned NOT NULL DEFAULT '0',
         PRIMARY KEY (`id`)
        );
        ";

    $responsive_video_gallery_slider_settings = array(
        'pauseonmouseover' => '1',
        'auto' => '',
        'speed' => '1000',
        'pause' => 1000,
        'circular' => '1',
        'imageheight' => '120',
        'imagewidth' => '120',
        'imageMargin' => '15',
        'visible' => '3',
        'min_visible' => '1',
        'scroll' => '1',
        'resizeImages' => '1',
        'scollerBackground' => '#FFFFFF'
    );

    if (!get_option('responsive_video_gallery_slider_settings')) {

        update_option('responsive_video_gallery_slider_settings', $responsive_video_gallery_slider_settings);
    }

    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    $uploads = wp_upload_dir();
    $baseDir = $uploads ['basedir'];
    $baseDir = str_replace("\\", "/", $baseDir);
    $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';
    wp_mkdir_p($pathToImagesFolder);
}

function responsive_video_gallery_plus_lightbox_add_admin_menu() {
    add_menu_page(__('Responsive Video Gallery Plus Lightbox'), __('Video Gallery with Lightbox'), 'administrator', 'responsive_video_gallery_with_lightbox', 'responsive_video_gallery_with_lightbox_admin_options_func');
    add_submenu_page('responsive_video_gallery_with_lightbox', __('Gallery Settings'), __('Gallery Settings'), 'administrator', 'responsive_video_gallery_with_lightbox', 'responsive_video_gallery_with_lightbox_admin_options_func');
    add_submenu_page('responsive_video_gallery_with_lightbox', __('Manage Videos'), __('Manage Videos'), 'administrator', 'responsive_video_gallery_with_lightbox_video_management', 'responsive_video_gallery_with_lightbox_video_management_func');
    add_submenu_page('responsive_video_gallery_with_lightbox', __('Preview Gallery'), __('Preview Gallery'), 'administrator', 'responsive_video_gallery_with_lightbox_video_preview', 'responsive_video_gallery_with_lightbox_video_preview_func');
}

function responsive_video_gallery_plus_lightbox_add_admin_init() {
    $url = plugin_dir_url(__FILE__);

    wp_enqueue_style('wp-video-gallery-lighbox-style', plugins_url('/css/wp-video-gallery-lighbox-style.css', __FILE__));
    wp_enqueue_style('vl-box-css', plugins_url('/css/vl-box-css.css', __FILE__));
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery.validate', $url . 'js/jquery.validate.js');
    wp_enqueue_script('video-gallery-jc', plugins_url('/js/video-gallery-jc.js', __FILE__));
    wp_enqueue_script('vl-box-js', plugins_url('/js/vl-box-js.js', __FILE__));

    responsive_video_gallery_plus_lightbox_admin_scripts_init();
}

function responsive_video_gallery_with_lightbox_admin_options_func() {

    if (isset($_POST['btnsave'])) {

        $auto = trim($_POST['isauto']);

        if ($auto == 'auto')
            $auto = true;
        else
            $auto = false;

        $speed = (int) trim($_POST['speed']);
        $pause = (int) trim($_POST['pause']);

        if (isset($_POST['circular']))
            $circular = true;
        else
            $circular = false;

        //$scrollerwidth=$_POST['scrollerwidth'];

        $visible = trim($_POST['visible']);

        $min_visible = trim($_POST['min_visible']);


        if (isset($_POST['pauseonmouseover']))
            $pauseonmouseover = true;
        else
            $pauseonmouseover = false;

        if (isset($_POST['linkimage']))
            $linkimage = true;
        else
            $linkimage = false;

        $scroll = trim($_POST['scroll']);

        if ($scroll == "")
            $scroll = 1;

        $imageMargin = (int) trim($_POST['imageMargin']);
        $imageheight = (int) trim($_POST['imageheight']);
        $imagewidth = (int) trim($_POST['imagewidth']);

        $scollerBackground = trim($_POST['scollerBackground']);

        $options = array();
        $options['pauseonmouseover'] = $pauseonmouseover;
        $options['auto'] = $auto;
        $options['speed'] = $speed;
        $options['pause'] = $pause;
        $options['circular'] = $circular;
        //$options['scrollerwidth']=$scrollerwidth;  
        $options['imageMargin'] = $imageMargin;
        $options['imageheight'] = $imageheight;
        $options['imagewidth'] = $imagewidth;
        $options['visible'] = $visible;
        $options['min_visible'] = $min_visible;
        $options['scroll'] = $scroll;
        $options['resizeImages'] = 1;
        $options['scollerBackground'] = $scollerBackground;


        $settings = update_option('responsive_video_gallery_slider_settings', $options);
        $responsive_video_gallery_plus_lightbox_messages = array();
        $responsive_video_gallery_plus_lightbox_messages['type'] = 'succ';
        $responsive_video_gallery_plus_lightbox_messages['message'] = 'Settings saved successfully.';
        update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
    }
    $settings = get_option('responsive_video_gallery_slider_settings');
    ?>      
    <div id="poststuff" > 
        <div id="post-body" class="metabox-holder columns-2" >  
            <div id="post-body-content">
                <div class="wrap">
                    <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                            <td>
                                <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                    <img id="help us for free plugin" height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                                </a>
                            </td>
                        </tr>
                    </table>
                    <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-video-gallery-with-lightbox-pro.html">UPGRADE TO PRO VERSION</a></h3></span>
                    <?php
                    $messages = get_option('responsive_video_gallery_plus_lightbox_messages');
                    $type = '';
                    $message = '';
                    if (isset($messages['type']) and $messages['type'] != "") {

                        $type = $messages['type'];
                        $message = $messages['message'];
                    }


                    if ($type == 'err') {
                        echo "<div class='errMsg'>";
                        echo $message;
                        echo "</div>";
                    } else if ($type == 'succ') {
                        echo "<div class='succMsg'>";
                        echo $message;
                        echo "</div>";
                    }


                    update_option('responsive_video_gallery_plus_lightbox_messages', array());
                    ?>      

                    <h2>Gallery Slider Settings</h2>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <form method="post" action="" id="scrollersettiings" name="scrollersettiings" >


                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Auto Scroll ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input style="width:20px;" type='radio' <?php
                if ($settings['auto'] == true) {
                    echo "checked='checked'";
                }
                    ?>  name='isauto' value='auto' >Auto &nbsp;<input style="width:20px;" type='radio' name='isauto' <?php
                                                           if ($settings['auto'] == false) {
                                                               echo "checked='checked'";
                                                           }
                                                           ?> value='manuall' >Scroll By Left & Right Arrow
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label >Speed</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="speed" size="30" name="speed" value="<?php echo $settings['speed']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label >Pause</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="pause" size="30" name="pause" value="<?php echo $settings['pause']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both">The amount of time (in ms) between each auto transition</div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label >Circular Slider ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="circular" size="30" name="circular" value="" <?php
                                                           if ($settings['circular'] == true) {
                                                               echo "checked='checked'";
                                                           }
                                                           ?> style="width:20px;">&nbsp;Circular Slider ? 
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Slider Background color</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="scollerBackground" size="30" name="scollerBackground" value="<?php echo $settings['scollerBackground']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Max Visible</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="visible" size="30" name="visible" value="<?php echo $settings['visible']; ?>" style="width:100px;">
                                                        <div style="clear:both">This will decide your slider width automatically</div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            specifies the number of items visible at all times within the slider.
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Min Visible</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="min_visible" size="30" name="min_visible" value="<?php echo $settings['min_visible']; ?>" style="width:100px;">
                                                        <div style="clear:both">This will decide your slider width in responsive layout</div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            The responsive layout decide by slider itself using min visible.
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Scroll</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="scroll" size="30" name="scroll" value="<?php echo $settings['scroll']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            You can specify the number of items to scroll when you click the next or prev buttons.
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Pause On Mouse Over ?</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" id="pauseonmouseover" size="30" name="pauseonmouseover" value="" <?php
                                                           if ($settings['pauseonmouseover'] == true) {
                                                               echo "checked='checked'";
                                                           }
                                                           ?> style="width:20px;">&nbsp;Pause On Mouse Over ? 
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>
                                        </div>
                                    </div>

                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Image Height</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="imageheight" size="30" name="imageheight" value="<?php echo $settings['imageheight']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Image Width</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="imagewidth" size="30" name="imagewidth" value="<?php echo $settings['imagewidth']; ?>" style="width:100px;">
                                                        <div style="clear:both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%;">
                                        <h3><label>Image Margin</label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <input type="text" id="imageMargin" size="30" name="imageMargin" value="<?php echo $settings['imageMargin']; ?>" style="width:100px;">
                                                        <div style="clear:both;padding-top:5px">Gap between two images </div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>

                                            <div style="clear:both"></div>
                                        </div>
                                    </div>

                                    <input type="submit"  name="btnsave" id="btnsave" value="Save Changes" class="button-primary">&nbsp;&nbsp;<input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=responsive_video_gallery_with_lightbox_video_management'">

                                </form> 
                                <script type="text/javascript">

                                    var $n = jQuery.noConflict();  
                                    $n(document).ready(function() {

                                        $n("#scrollersettiings").validate({
                                            rules: {
                                                isauto: {
                                                    required:true
                                                },speed: {
                                                    required:true, 
                                                    number:true, 
                                                    maxlength:15
                                                },pause: {
                                                    required:true, 
                                                    number:true, 
                                                    maxlength:15
                                                },
                                                visible:{
                                                    required:true,  
                                                    number:true,
                                                    maxlength:15

                                                },
                                                min_visible:{
                                                    required:true,  
                                                    number:true,
                                                    maxlength:15

                                                },
                                                scroll:{
                                                    required:true,
                                                    number:true,
                                                    maxlength:15  
                                                },
                                                scollerBackground:{
                                                    required:true,
                                                    maxlength:7  
                                                },
                                                /*scrollerwidth:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:15    
                                                        },*/imageheight:{
                                                    required:true,
                                                    number:true,
                                                    maxlength:15    
                                                },
                                                imagewidth:{
                                                    required:true,
                                                    number:true,
                                                    maxlength:15    
                                                },imageMargin:{
                                                    required:true,
                                                    number:true,
                                                    maxlength:15    
                                                }

                                            },
                                            errorClass: "image_error",
                                            errorPlacement: function(error, element) {
                                                error.appendTo( element.next().next());
                                            } 


                                        })
                                    });

                                </script> 

                            </div>
                        </div>
                    </div>  
                </div>      
            </div>
            <div id="postbox-container-1" class="postbox-container" > 

                <div class="postbox"> 
                    <h3 class="hndle"><span></span>Access All Themes In One Price</h3> 
                    <div class="inside">
                        <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                        <div style="margin:10px 5px">

                        </div>
                    </div></div>
                <div class="postbox"> 
                    <h3 class="hndle"><span></span>Recommended WordPress Hostings</h3> 
                    <div class="inside">
                        <center><a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=nik00726-hs-wp"><img src="http://tracking.hostgator.com/img/WordPress_Hosting/300x250-animated.gif" width="250" height="250" border="0"></a></center>
                        <div style="margin:10px 5px">
                        </div>
                    </div></div>

            </div>      

            <div class="clear"></div>
        </div>  
    </div> 
    <?php
}

function responsive_video_gallery_with_lightbox_video_management_func() {
    $action = 'gridview';
    global $wpdb;



    if (isset($_GET ['action']) and $_GET ['action'] != '') {

        $action = trim($_GET ['action']);
    }
    ?>

    <?php
    if (strtolower($action) == strtolower('gridview')) {

        $wpcurrentdir = dirname(__FILE__);
        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);

        $uploads = wp_upload_dir();
        $baseurl = $uploads ['baseurl'];
        $baseurl .= '/wp-responsive-video-gallery-with-lightbox/';
        ?> 
        <div class="wrap">
            <style type="text/css">
                .pagination {
                    clear: both;
                    padding: 20px 0;
                    position: relative;
                    font-size: 11px;
                    line-height: 13px;
                }

                .pagination span, .pagination a {
                    display: block;
                    float: left;
                    margin: 2px 2px 2px 0;
                    padding: 6px 9px 5px 9px;
                    text-decoration: none;
                    width: auto;
                    color: #fff;
                    background: #555;
                }

                .pagination a:hover {
                    color: #fff;
                    background: #3279BB;
                }

                .pagination .current {
                    padding: 6px 9px 5px 9px;
                    background: #3279BB;
                    color: #fff;
                }
            </style>
            <!--[if !IE]><!-->
            <style type="text/css">
                @media only screen and (max-width: 800px) {
                    /* Force table to not be like tables anymore */
                    #no-more-tables table, #no-more-tables thead, #no-more-tables tbody,
                    #no-more-tables th, #no-more-tables td, #no-more-tables tr {
                        display: block;
                    }

                    /* Hide table headers (but not display: none;, for accessibility) */
                    #no-more-tables thead tr {
                        position: absolute;
                        top: -9999px;
                        left: -9999px;
                    }
                    #no-more-tables tr {
                        border: 1px solid #ccc;
                    }
                    #no-more-tables td {
                        /* Behave  like a "row" */
                        border: none;
                        border-bottom: 1px solid #eee;
                        position: relative;
                        padding-left: 50%;
                        white-space: normal;
                        text-align: left;
                    }
                    #no-more-tables td:before {
                        /* Now like a table header */
                        position: absolute;
                        /* Top/left values mimic padding */
                        top: 6px;
                        left: 6px;
                        width: 45%;
                        padding-right: 10px;
                        white-space: nowrap;
                        text-align: left;
                        font-weight: bold;
                    }

                    /*
                                    Label the data
                    */
                    #no-more-tables td:before {
                        content: attr(data-title);
                    }
                }
            </style>
            <!--<![endif]-->
            <?php
            $messages = get_option('responsive_video_gallery_plus_lightbox_messages');
            $type = '';
            $message = '';
            if (isset($messages ['type']) and $messages ['type'] != "") {

                $type = $messages ['type'];
                $message = $messages ['message'];
            }

            if ($type == 'err') {
                echo "<div class='errMsg'>";
                echo $message;
                echo "</div>";
            } else if ($type == 'succ') {
                echo "<div class='succMsg'>";
                echo $message;
                echo "</div>";
            }

            update_option('responsive_video_gallery_plus_lightbox_messages', array());
            ?>
            <div id="poststuff" > 
                <div id="post-body" class="metabox-holder columns-2" >  
                    <div id="post-body-content">
                        <div class="wrap">
                            <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-video-gallery-with-lightbox-pro.html">UPGRADE TO PRO VERSION</a></h3></span>
                            <div style="width: 100%;">
                                <div style="float: left; width: 100%;">
                                    <div class="icon32 icon32-posts-post" id="icon-edit">
                                        <br>
                                    </div>
                                    <h2>
                                        Videos<a class="button add-new-h2"
                                                 href="admin.php?page=responsive_video_gallery_with_lightbox_video_management&action=addedit">Add
                                            New</a>
                                    </h2>
                                    <br />

                                    <form method="POST"
                                          action="admin.php?page=responsive_video_gallery_with_lightbox_video_management&action=deleteselected"
                                          id="posts-filter">
                                        <div class="alignleft actions">
                                            <select name="action_upper">
                                                <option selected="selected" value="-1">Bulk Actions</option>
                                                <option value="delete">delete</option>
                                            </select> <input type="submit" value="Apply"
                                                             class="button-secondary action" id="deleteselected"
                                                             name="deleteselected">
                                        </div>
                                        <br class="clear">
                                        <?php
                                        global $wpdb;
                                        $settings = get_option('responsive_video_gallery_slider_settings');

                                        $visibleImages = $settings ['visible'];
                                        $query = "SELECT * FROM " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox  order by video_order,createdon desc";
                                        $rows = $wpdb->get_results($query, 'ARRAY_A');
                                        $rowCount = sizeof($rows);
                                        ?>
        <?php if ($rowCount < $visibleImages) { ?>
                                            <h4 style="color: green"> Current slider setting - Total visible Videos <?php echo $visibleImages; ?></h4>
                                            <h4 style="color: green">Please add atleast <?php echo $visibleImages; ?> videos</h4>
            <?php
        } else {
            echo "<br/>";
        }
        ?>
                                        <div id="no-more-tables">
                                            <table cellspacing="0" id="gridTbl"
                                                   class="table-bordered table-striped table-condensed cf">
                                                <thead>
                                                    <tr>
                                                        <th class="manage-column column-cb check-column" scope="col"><input
                                                                type="checkbox"></th>
                                                        <th>Id</th>
                                                        <th><span>Video Type</span></th>
                                                        <th><span>Title</span></th>
                                                        <th><span></span></th>
                                                        <th><span>Published On</span></th>
                                                        <th><span>Edit</span></th>
                                                        <th><span>Delete</span></th>
                                                    </tr>
                                                </thead>

                                                <tbody id="the-list">
                                                    <?php
                                                    if (count($rows) > 0) {

                                                        global $wp_rewrite;
                                                        $rows_per_page = 15;

                                                        $current = (isset($_GET ['paged'])) ? ($_GET ['paged']) : 1;
                                                        $pagination_args = array(
                                                            'base' => @add_query_arg('paged', '%#%'),
                                                            'format' => '',
                                                            'total' => ceil(sizeof($rows) / $rows_per_page),
                                                            'current' => $current,
                                                            'show_all' => false,
                                                            'type' => 'plain'
                                                        );

                                                        $start = ($current - 1) * $rows_per_page;
                                                        $end = $start + $rows_per_page;
                                                        $end = (sizeof($rows) < $end) ? sizeof($rows) : $end;

                                                        for ($i = $start; $i < $end; ++$i) {

                                                            $row = $rows [$i];

                                                            $id = $row ['id'];
                                                            $editlink = "admin.php?page=responsive_video_gallery_with_lightbox_video_management&action=addedit&id=$id";
                                                            $deletelink = "admin.php?page=responsive_video_gallery_with_lightbox_video_management&action=delete&id=$id";

                                                            $outputimgmain = $baseurl . $row ['image_name'] . '?rand=' . rand(0, 5000);
                                                            ?>
                                                            <tr valign="top">
                                                                <td class="alignCenter check-column" data-title="Select Record"><input
                                                                        type="checkbox" value="<?php echo $row['id'] ?>"
                                                                        name="thumbnails[]"></td>
                                                                <td data-title="Id" class="alignCenter"><?php echo stripslashes($row['id']) ?></td>
                                                                <td data-title="Video Type" class="alignCenter"><div>
                                                                        <strong><?php echo stripslashes($row['vtype']) ?></strong>
                                                                    </div></td>
                                                                <td data-title="Title" class="alignCenter">
                                                                    <div>
                                                                        <strong><?php echo stripslashes($row['videotitle']) ?></strong>
                                                                    </div></td>
                                                                <td class="alignCenter"><img
                                                                        src="<?php echo $outputimgmain; ?>" style="width: 50px"
                                                                        height="50px" /></td>
                                                                <td data-title="Published On" class="alignCenter"><?php echo $row['createdon'] ?></td>
                                                                <td data-title="Edit" class="alignCenter"><strong><a
                                                                            href='<?php echo $editlink; ?>' title="edit">Edit</a></strong></td>
                                                                <td data-title="Delete" class="alignCenter"><strong><a
                                                                            href='<?php echo $deletelink; ?>'
                                                                            onclick="return confirmDelete();" title="delete">Delete</a> </strong></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <tr valign="top" class=""
                                                            id="">
                                                            <td colspan="8" data-title="No Record" align="center"><strong>No
                                                                    Videos Found</strong></td>
                                                        </tr>
                                            <?php
                                        }
                                        ?>      
                                                </tbody>
                                            </table>
                                        </div>
        <?php
        if (sizeof($rows) > 0) {
            echo "<div class='pagination' style='padding-top:10px'>";
            echo paginate_links($pagination_args);
            echo "</div>";
        }
        ?>
                                        <br />
                                        <div class="alignleft actions">
                                            <select name="action">
                                                <option selected="selected" value="-1">Bulk Actions</option>
                                                <option value="delete">delete</option>
                                            </select> <input type="submit" value="Apply"
                                                             class="button-secondary action" id="deleteselected"
                                                             name="deleteselected">
                                        </div>

                                    </form>
                                    <script type="text/JavaScript">

                                        function  confirmDelete(){
                                            var agree=confirm("Are you sure you want to delete this video ?");
                                            if (agree)
                                                return true ;
                                            else
                                                return false;
                                        }
                                    </script>

                                    <br class="clear">
                                </div>
                                <div style="clear: both;"></div>
        <?php $url = plugin_dir_url(__FILE__); ?>


                            </div>
                            <h3>To print this video gallery into WordPress Post/Page use below code</h3>
                            <input type="text"
                                   value='[print_responsive_video_gallery_plus_lightbox] '
                                   style="width: 400px; height: 30px"
                                   onclick="this.focus(); this.select()" />
                            <div class="clear"></div>
                            <h3>To print this video gallery into WordPress theme/template PHP files use
                                below code</h3>
        <?php
        $shortcode = '[print_responsive_video_gallery_plus_lightbox]';
        ?>
                            <input type="text"
                                   value="&lt;?php echo do_shortcode('<?php echo htmlentities($shortcode, ENT_QUOTES); ?>'); ?&gt;"
                                   style="width: 400px; height: 30px"
                                   onclick="this.focus(); this.select()" />
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container" > 

                        <div class="postbox"> 
                            <h3 class="hndle"><span></span>Access All Themes In One Price</h3> 
                            <div class="inside">
                                <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                                <div style="margin:10px 5px">

                                </div>
                            </div></div>
                        <div class="postbox"> 
                            <h3 class="hndle"><span></span>Recommended WordPress Hostings</h3> 
                            <div class="inside">
                                <center><a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=nik00726-hs-wp"><img src="http://tracking.hostgator.com/img/WordPress_Hosting/300x250-animated.gif" width="250" height="250" border="0"></a></center>
                                <div style="margin:10px 5px">
                                </div>
                            </div></div>

                    </div> 
                </div>
            </div>                <?php
    } else if (strtolower($action) == strtolower('addedit')) {
        $url = plugin_dir_url(__FILE__);
        ?><?php
        if (isset($_POST ['btnsave'])) {

            $uploads = wp_upload_dir();
            $baseDir = $uploads ['basedir'];
            $baseDir = str_replace("\\", "/", $baseDir);
            $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';

            $vtype = trim(addslashes($_POST ['vtype']));
            $videourl = trim($_POST ['videourl']);
            // echo $videourl;die;
            $vid = uniqid('vid_');
            $embed_url = '';
            if ($vtype == 'youtube') {
                // parse

                $parseUrl = @parse_url($videourl);
                if (is_array($parseUrl)) {

                    $queryStr = $parseUrl ['query'];
                    parse_str($queryStr, $array);
                    if (is_array($array) and isset($array ['v'])) {

                        $vid = $array ['v'];
                    }
                }

                $embed_url = "//www.youtube.com/embed/$vid";
            } else if ($vtype == 'dailymotion') {

                $pattern = "#(?<=video/).*?(?=_)#";
                preg_match($pattern, $videourl, $matches, PREG_OFFSET_CAPTURE, 3);
                $vid = 0;
                if ($matches and is_array($matches)) {

                    $vid = $matches[0][0];
                }

                $embed_url = "//www.dailymotion.com/embed/video/$vid";
            }


            $HdnMediaSelection = trim($_POST ['HdnMediaSelection']);
            $videotitle = trim($_POST ['videotitle']);
            $videotitleurl = trim($_POST ['videotitleurl']);
            $video_order = 0;

            $video_description = '';

            $videotitle = str_replace("'", "’", $videotitle);
            $videotitle = str_replace('"', '&quot;', $videotitle);

            $open_link_in = 0;

            $enable_light_box_video_desc = 0;

            $location = "admin.php?page=responsive_video_gallery_with_lightbox_video_management";
            // edit save
            if (isset($_POST ['videoid'])) {

                try {

                    $videoidEdit = $_POST ['videoid'];
                    if (trim($_POST ['HdnMediaSelection']) != '') {
                        $pInfo = pathinfo($HdnMediaSelection);
                        $ext = $pInfo ['extension'];
                        $imagename = $vid . '_big.' . $ext;
                        $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                        @copy($HdnMediaSelection, $imageUploadTo);
                        $settings = get_option('responsive_video_gallery_slider_settings');
                        $imageheight = $settings ['imageheight'];
                        $imagewidth = $settings ['imagewidth'];
                        @unlink($pathToImagesFolder . '/' . $vid . '_big_' . $imageheight . '_' . $imagewidth . '.' . $ext);
                    }

                    $query = "update " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox
						set vtype='$vtype',vid='$vid',video_url='$videourl',embed_url='$embed_url',image_name='$imagename',HdnMediaSelection='$HdnMediaSelection',
						videotitle='$videotitle',videotitleurl='$videotitleurl',video_description='$video_description',video_order=$video_order,
						open_link_in=$open_link_in,enable_light_box_video_desc=$enable_light_box_video_desc where id=$videoidEdit";


                    $wpdb->query($query);

                    $responsive_video_gallery_plus_lightbox_messages = array();
                    $responsive_video_gallery_plus_lightbox_messages ['type'] = 'succ';
                    $responsive_video_gallery_plus_lightbox_messages ['message'] = 'Video updated successfully.';
                    update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                } catch (Exception $e) {

                    $responsive_video_gallery_plus_lightbox_messages = array();
                    $responsive_video_gallery_plus_lightbox_messages ['type'] = 'err';
                    $responsive_video_gallery_plus_lightbox_messages ['message'] = 'Error while adding video';
                    update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                }



                echo "<script type='text/javascript'> location.href='$location';</script>";
                exit();
            } else {

                // add new

                $createdOn = current_time('Y-m-d h:i:s');

                try {

                    if (trim($_POST ['HdnMediaSelection']) != '') {
                        $pInfo = pathinfo($HdnMediaSelection);
                        $ext = $pInfo ['extension'];
                        $imagename = $vid . '_big.' . $ext;
                        $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                        @copy($HdnMediaSelection, $imageUploadTo);
                    }

                    $query = "INSERT INTO " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox 
                                		(vtype, vid,video_url,embed_url,image_name,HdnMediaSelection,videotitle,videotitleurl,video_description,video_order,open_link_in,
                            			enable_light_box_video_desc,createdon) 
                           				 VALUES ('$vtype','$vid','$videourl','$embed_url','$imagename','$HdnMediaSelection','$videotitle','$videotitleurl','$video_description',
                                		$video_order,$open_link_in,$enable_light_box_video_desc,'$createdOn')";

                    //echo $query;die;
                    $wpdb->query($query);

                    $responsive_video_gallery_plus_lightbox_messages = array();
                    $responsive_video_gallery_plus_lightbox_messages ['type'] = 'succ';
                    $responsive_video_gallery_plus_lightbox_messages ['message'] = 'New video added successfully.';
                    update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                } catch (Exception $e) {

                    $responsive_video_gallery_plus_lightbox_messages = array();
                    $responsive_video_gallery_plus_lightbox_messages ['type'] = 'err';
                    $responsive_video_gallery_plus_lightbox_messages ['message'] = 'Error while adding video';
                    update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                }

                echo "<script type='text/javascript'> location.href='$location';</script>";
                exit();
            }
        } else {

            $uploads = wp_upload_dir();
            $baseurl = $uploads ['baseurl'];
            $baseurl .= '/wp-responsive-video-gallery-with-lightbox/';
            ?>
                <div id="poststuff" > 
                    <div id="post-body" class="metabox-holder columns-2" >  
                        <div id="post-body-content">
                            <div class="wrap">
                                <div style="float: left; width: 100%;">
                                    <div class="wrap">
                                        <?php
                                        if (isset($_GET ['id']) and $_GET ['id'] > 0) {

                                            $id = $_GET ['id'];
                                            $query = "SELECT * FROM " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox WHERE id=$id";

                                            $myrow = $wpdb->get_row($query);

                                            if (is_object($myrow)) {

                                                $vtype = stripslashes($myrow->vtype);
                                                $title = stripslashes($myrow->title);
                                                $image_name = $myrow->image_name;
                                                $video_url = stripslashes($myrow->video_url);
                                                $HdnMediaSelection = $myrow->HdnMediaSelection;
                                                $videotitle = $myrow->videotitle;
                                                $videotitleurl = $myrow->videotitleurl;
                                                $video_order = stripslashes($myrow->video_order);
                                                $video_description = stripslashes($myrow->video_description);
                                                $open_link_in = stripslashes($myrow->open_link_in);
                                                $enable_light_box_video_desc = stripslashes($myrow->enable_light_box_video_desc);
                                            }
                                            ?>
                                            <h2>Update Video</h2><?php
                        } else {

                            $vtype = '';
                            $title = '';
                            $videotitle = '';
                            $HdnMediaSelection = '';
                            $video_url = '';
                            $image_link = '';
                            $image_name = '';
                            $video_order = '';
                            $video_description = '';
                            $open_link_in = true;
                            $enable_light_box_video_desc = true;
                            ?>

                                            <div style="clear:both">
                                                <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-responsive-video-gallery-with-lightbox-pro.html">UPGRADE TO PRO VERSION</a></h3></span>
                                            </div>  
                                            <h2>Add Video</h2>
            <?php } ?>
                                        <br />
                                        <div id="poststuff">
                                            <div id="post-body" class="metabox-holder columns-2">
                                                <div id="post-body-content">
                                                    <form method="post" action="" id="addimage" name="addimage"
                                                          enctype="multipart/form-data">
                                                        <div class="stuffbox" id="namediv" style="width: 100%">
                                                            <h3>
                                                                <label for="link_name">Video Information (<span
                                                                        style="font-size: 11px; font-weight: normal"><?php _e(' Choose Video Site'); ?></span>)
                                                                </label>
                                                            </h3>
                                                            <div class="inside">
                                                                <div>
                                                                    <input type="radio" value="youtube" name="vtype"
            <?php if ($vtype == 'youtube'): ?> checked='checked' <?php endif; ?> style="width: 15px" id="type_youtube" />Youtube&nbsp;&nbsp;
                                                                    <input <?php if ($vtype == 'dailymotion'): ?> checked='checked' <?php endif; ?> type="radio" value="dailymotion" name="vtype"
                                                                                                                  style="width: 15px" id="type_DailyMotion" />DailyMotion&nbsp;&nbsp;
                                                                </div>
                                                                <div style="clear: both"></div>
                                                                <div></div>
                                                                <div style="clear: both"></div>
                                                                <br />
                                                                <div>
                                                                    <b>Video Url</b> <input type="text" id="videourl"
                                                                                            class="url" tabindex="1" size="30" name="videourl"
                                                                                            value="<?php echo $video_url; ?>">
                                                                </div>
                                                                <div style="clear: both"></div>
                                                                <div></div>
                                                                <div style="clear: both"></div>
                                                            </div>
                                                        </div>
                                                        <div class="stuffbox" id="namediv" style="width: 100%">
                                                            <h3>
                                                                <label for="link_name">Video Thumbnail Information</label>
                                                            </h3>
                                                            <div class="inside" id="fileuploaddiv">
            <?php if ($image_name != "") { ?>
                                                                    <div>
                                                                        <b>Current Image : </b>
                                                                        <br/>
                                                                        <img id="img_disp" name="img_disp"
                                                                             src="<?php echo $baseurl . $image_name; ?>" />
                                                                    </div>
            <?php } else { ?>      
                                                                    <img
                                                                        src="<?php echo plugins_url('/images/no-img.jpeg', __FILE__); ?>"
                                                                        id="img_disp" name="img_disp" />

            <?php } ?>
                                                                <br /> <a
                                                                    href="javascript:;" class="niks_media"
                                                                    id="videoFromExternalSite"  ><b>Click Here to get video
                                                                        information and thumbnail<span id='fromval'> From <?php echo $vtype; ?></span>
                                                                    </b></a>&nbsp;<img
                                                                    src="<?php echo plugins_url('/images/ajax-loader.gif', __FILE__); ?>"
                                                                    style="display: none" id="loading_img" name="loading_img" />
                                                                <div style="clear: both"></div>
                                                                <div></div>
                                                                <div class="uploader">
                                                                    <br /> <b style="margin-left: 50px;">OR</b>
                                                                    <div style="clear: both; margin-top: 15px;"></div>
            <?php if (responsive_video_gallery_plus_responsive_lightbox_get_wp_version() >= 3.5) { ?>
                                                                        <a
                                                                            href="javascript:;" class="niks_media" id="myMediaUploader"><b>Click
                                                                                Here to upload custom video thumbnail</b></a>
            <?php } ?>  
                                                                    <br /> <br />
                                                                    <div>
                                                                        <input id="HdnMediaSelection" name="HdnMediaSelection"
                                                                               type="hidden" value="<?php echo $HdnMediaSelection; ?>" />
                                                                    </div>
                                                                    <div style="clear: both"></div>
                                                                    <div></div>
                                                                    <div style="clear: both"></div>

                                                                    <br />
                                                                </div>
                                                                <script>

                                                                    function GetParameterValues(param,str) {
                                                                        var return_p='';  
                                                                        var url = str.slice(str.indexOf('?') + 1).split('&');
                                                                        for (var i = 0; i < url.length; i++) {
                                                                            var urlparam = url[i].split('=');
                                                                            if (urlparam[0] == param) {
                                                                                return_p= urlparam[1];
                                                                            }
                                                                        }
                                                                        return return_p;
                                                                    }

                                                                    var $n = jQuery.noConflict();

                                                                    function UrlExists(url, cb){
                                                                        $n.ajax({
                                                                            url:      url,
                                                                            dataType: 'text',
                                                                            type:     'GET',
                                                                            complete:  function(xhr){
                                                                                if(typeof cb === 'function')
                                                                                    cb.apply(this, [xhr.status]);
                                                                            }
                                                                        });
                                                                    }
                                                                                    
                                                                    function getDailyMotionId(url) {
                                                                        var m = url.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/);
                                                                        if (m !== null) {
                                                                            if(m[4] !== undefined) {
                                                                                return m[4];
                                                                            }
                                                                            return m[2];
                                                                        }
                                                                        return null;
                                                                    }
                                                                                    															
                                                                                                                                              
                                                                    $n(document).ready(function() {

                                                                                         	
                                                                        $n("input:radio[name=vtype]").click(function() {

                                                                                           
                                                                            var value = $n(this).val();
                                                                            $n("#fromval").html(" from " + value);
                                                                        });
                                                                                  
                                                                        $n("#videoFromExternalSite").click(function() {

                                                                                    
                                                                            var videoService = $n('input[name="vtype"]:checked').length;
                                                                            var videourlVal = $n.trim($n("#videourl").val());
                                                                            var flag = true;
                                                                            if (videourlVal == '' && videoService == 0){

                                                                                alert('Please select video site.\nPlease enter video url.');
                                                                                $n("input:radio[name=vtype]").focus();
                                                                                flag = false;
                                                                                            
                                                                            }
                                                                            else if (videoService == 0){

                                                                                alert('Please select video site.');
                                                                                $n("input:radio[name=vtype]").focus();
                                                                                flag = false;
                                                                            }
                                                                            else if (videourlVal == ''){

                                                                                alert('Please enter video url.');
                                                                                $n("#videourl").focus();
                                                                                flag = false;
                                                                            }

                                                                            if (flag){

                                                                                setTimeout(function() {
                                                                                    $n("#loading_img").show();   
                                                                                }, 100);

                                                                                var selectedRadio = $n('input[name=vtype]');
                                                                                var checkedValueRadio = selectedRadio.filter(':checked').val();
                                                                                if (checkedValueRadio == 'youtube') {
                                                                                    var vId = GetParameterValues('v', videourlVal);
                                                                                    if(vId!=''){

                                                                                                                                                                    
                                                                                        var tumbnailImg='http://img.youtube.com/vi/'+vId+'/maxresdefault.jpg';

                                                                                        var data = {
                                                                                            'action': 'check_file_exist',
                                                                                            'url': tumbnailImg
                                                                                        };

                                                                                        $n.post(ajaxurl, data, function(response) {

                                                                                            																				
                                                                                              		  
                                                                                            var youtubeJsonUri='http://gdata.youtube.com/feeds/api/videos/'+vId+'?v=2&alt=jsonc';
                                                                                            $n.getJSON( youtubeJsonUri, function( data ) {
                                                                                                         
                                                                                                if(typeof data =='object'){    
                                                                                                    if(typeof data.data =='object'){ 
                                                                                                                
                                                                                                        if(data.data.title!='' && data.data.title!=''){
                                                                                                            $n("#videotitle").val(data.data.title); 
                                                                                                        }
                                                                                                        $n("#videotitleurl").val(videourlVal);
                                                                                                                   
                                                                                                        if(response=='404' && data.data.thumbnail.hqDefault!='' && data.data.thumbnail.hqDefault!=''){
                                                                                                            tumbnailImg=data.data.thumbnail.hqDefault;
                                                                                                        }
                                                                                                        else{
                                                                                                            tumbnailImg='http://img.youtube.com/vi/'+vId+'/0.jpg';
                                                                                                        }
                                                                                                                    
                                                                                                        $n("#img_disp").attr('src', tumbnailImg);
                                                                                                        $n("#HdnMediaSelection").val(tumbnailImg);
                                                                                                        $n("#loading_img").hide();
                                                                                                                    
                                                                                                    }
                                                                                                                                                                                                       
                                                                                                }
                                                                                                $n("#loading_img").hide();
                                                                                            })  
                                                                                     			
                                                                                            			
                                                                                        });
                                                                                           
                                                                                    }
                                                                                    else{
                                                                                        alert('Could not found such video');
                                                                                        $n("#loading_img").hide();
                                                                                    }
                                                                                }
                                                                                else if(checkedValueRadio == 'dailymotion'){

                                                                                    var vid=getDailyMotionId(videourlVal);	
                                                                                    var apiUrl='https://api.dailymotion.com/video/'+vid+'?fields=description,id,thumbnail_720_url,title';
                                                                                    $n.getJSON( apiUrl, function( data ) {
                                                                                        if(typeof data =='object'){    


                                                                                            $n("#HdnMediaSelection").val(data.thumbnail_720_url);	
                                                                                            $n("#videotitle").val($n.trim(data.title));
                                                                                            $n("#videotitleurl").val(videourlVal);
                                                                                            $n("#img_disp").attr('src', data.thumbnail_720_url);
                                                                                            $n("#loading_img").hide();
                                                                                        }	 
                                                                                        $n("#loading_img").hide(); 
                                                                                    })	


                                                                                    $n("#loading_img").hide();
                                                                                }          

                                                                                $n("#loading_img").hide();
                                                                            }
                                                                                                            
                                                                            setTimeout(function() {
                                                                                $n("#loading_img").hide();   
                                                                            }, 2000);    
                                                                                        
                                                                        });
                                                                        //uploading files variable
                                                                        var custom_file_frame;
                                                                        $n("#myMediaUploader").click(function(event) {
                                                                            event.preventDefault();
                                                                            //If the frame already exists, reopen it
                                                                            if (typeof (custom_file_frame) !== "undefined") {
                                                                                custom_file_frame.close();
                                                                            }

                                                                            //Create WP media frame.
                                                                            custom_file_frame = wp.media.frames.customHeader = wp.media({
                                                                                //Title of media manager frame
                                                                                title: "WP Media Uploader",
                                                                                library: {
                                                                                    type: 'image'
                                                                                },
                                                                                button: {
                                                                                    //Button text
                                                                                    text: "Set Image"
                                                                                },
                                                                                //Do not allow multiple files, if you want multiple, set true
                                                                                multiple: false
                                                                            });
                                                                            //callback for selected image
                                                                            custom_file_frame.on('select', function() {

                                                                                var attachment = custom_file_frame.state().get('selection').first().toJSON();
                                                                                var validExtensions = new Array();
                                                                                validExtensions[0] = 'jpg';
                                                                                validExtensions[1] = 'jpeg';
                                                                                validExtensions[2] = 'png';
                                                                                validExtensions[3] = 'gif';
                                                                                validExtensions[4] = 'bmp';
                                                                                validExtensions[5] = 'tif';
                                                                                var inarr = parseInt($n.inArray(attachment.subtype, validExtensions));
                                                                                if (inarr > 0 && attachment.type.toLowerCase() == 'image'){

                                                                                    var titleTouse = "";
                                                                                    var imageDescriptionTouse = "";
                                                                                    if ($n.trim(attachment.title) != ''){

                                                                                        titleTouse = $n.trim(attachment.title);
                                                                                    }
                                                                                    else if ($n.trim(attachment.caption) != ''){

                                                                                        titleTouse = $n.trim(attachment.caption);
                                                                                    }

                                                                                    if ($n.trim(attachment.description) != ''){

                                                                                        imageDescriptionTouse = $n.trim(attachment.description);
                                                                                    }
                                                                                    else if ($n.trim(attachment.caption) != ''){

                                                                                        imageDescriptionTouse = $n.trim(attachment.caption);
                                                                                    }

                                                                                    // $n("#videotitle").val(titleTouse);
                                                                                    //  $n("#video_description").val(imageDescriptionTouse);
                                                                                            
                                                                                    if (attachment.id != ''){
                                                                                               
                                                                                        $n("#HdnMediaSelection").val(attachment.url);
                                                                                        $n("#img_disp").attr('src', attachment.url);
                                                                                            
                                                                                    }

                                                                                }
                                                                                else{

                                                                                    alert('Invalid image selection.');
                                                                                }
                                                                                //do something with attachment variable, for example attachment.filename
                                                                                //Object:
                                                                                //attachment.alt - image alt
                                                                                //attachment.author - author id
                                                                                //attachment.caption
                                                                                //attachment.dateFormatted - date of image uploaded
                                                                                //attachment.description
                                                                                //attachment.editLink - edit link of media
                                                                                //attachment.filename
                                                                                //attachment.height
                                                                                //attachment.icon - don't know WTF?))
                                                                                //attachment.id - id of attachment
                                                                                //attachment.link - public link of attachment, for example ""http://site.com/?attachment_id=115""
                                                                                //attachment.menuOrder
                                                                                //attachment.mime - mime type, for example image/jpeg"
                                                                                //attachment.name - name of attachment file, for example "my-image"
                                                                                //attachment.status - usual is "inherit"
                                                                                //attachment.subtype - "jpeg" if is "jpg"
                                                                                //attachment.title
                                                                                //attachment.type - "image"
                                                                                //attachment.uploadedTo
                                                                                //attachment.url - http url of image, for example "http://site.com/wp-content/uploads/2012/12/my-image.jpg"
                                                                                //attachment.width
                                                                            });
                                                                            //Open modal
                                                                            custom_file_frame.open();
                                                                        });
                                                                    })
                                                                </script>
                                                            </div>
                                                        </div>

                                                        <div class="stuffbox" id="namediv" style="width: 100%">
                                                            <h3>
                                                                <label for="link_name">Video Title (<span
                                                                        style="font-size: 11px; font-weight: normal"><?php _e('Used into lightbox'); ?></span>)
                                                                </label>
                                                            </h3>
                                                            <div class="inside">
                                                                <div>
                                                                    <input type="text" id="videotitle" tabindex="1" size="30"
                                                                           name="videotitle" value="<?php echo $videotitle; ?>">
                                                                </div>
                                                                <div style="clear: both"></div>
                                                                <div></div>
                                                                <div style="clear: both"></div>
                                                            </div>
                                                        </div>
                                                        <div class="stuffbox" id="namediv" style="width: 100%">
                                                            <h3>
                                                                <label for="link_name">Video Title Url (<span
                                                                        style="font-size: 11px; font-weight: normal"><?php _e(' click on title redirect to this url.Used in lightbox for video title'); ?></span>)
                                                                </label>
                                                            </h3>
                                                            <div class="inside">
                                                                <div>
                                                                    <input type="text" id="videotitleurl" class="url"
                                                                           tabindex="1" size="30" name="videotitleurl"
                                                                           value="<?php echo $videotitleurl; ?>">
                                                                </div>
                                                                <div style="clear: both"></div>
                                                                <div></div>
                                                                <div style="clear: both"></div>

                                                            </div>
                                                        </div>


            <?php if (isset($_GET['id']) and $_GET['id'] > 0) { ?> 
                                                            <input type="hidden" name="videoid" id="videoid" value="<?php echo $_GET['id']; ?>">
                <?php
            }
            ?>
                                                        <input type="submit"
                                                               onclick="" name="btnsave" id="btnsave" value="Save Changes"
                                                               class="button-primary">&nbsp;&nbsp;<input type="button"
                                                               name="cancle" id="cancle" value="Cancel"
                                                               class="button-primary"
                                                               onclick="location.href = 'admin.php?page=responsive_video_gallery_with_lightbox_video_management'">

                                                    </form>
                                                    <script type="text/javascript">

                                                        var $n = jQuery.noConflict();
                                                        $n(document).ready(function() {

                                                            $n.validator.setDefaults({ 
                                                                ignore: [],
                                                                // any other default options and/or rules
                                                            });

                                                            $n("#addimage").validate({
                                                                rules: {
                                                                    videotitle: {
                                                                        required:true,
                                                                        maxlength: 200
                                                                    },
                                                                    vtype: {
                                                                        required:true

                                                                    },
                                                                    videourl: {
                                                                        required:true,
                                                                        url:true,
                                                                        maxlength: 500
                                                                    },
                                                                    HdnMediaSelection:{
                                                                        required:true  
                                                                    },
                                                                    videotitleurl: {

                                                                        url:true,
                                                                        maxlength: 500
                                                                    }
                                                                               
                                                                },
                                                                errorClass: "image_error",
                                                                errorPlacement: function(error, element) {
                                                                    error.appendTo(element.parent().next().next());
                                                                }, messages: {
                                                                    HdnMediaSelection: "Please select video thumbnail or Upload by wordpress media uploader.",
                                                                                    
                                                                }
                                                                     
                                                            })
                                                        });
                                                        function validateFile(){

                                                            var $n = jQuery.noConflict();
                                                            if ($n('#currImg').length > 0 || $n.trim($n("#HdnMediaSelection").val()) != ""){
                                                                return true;
                                                            }
                                                            var fragment = $n("#image_name").val();
                                                            var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");
                                                            var videoid = $n("#image_name").val();
                                                            if (videoid == ""){

                                                                if (filename != "")
                                                                    return true;
                                                                else
                                                                {
                                                                    $n("#err_daynamic").remove();
                                                                    $n("#image_name").after('<label class="image_error" id="err_daynamic">Please select file or use media manager to select file.</label>');
                                                                    return false;
                                                                }
                                                            }
                                                            else{
                                                                return true;
                                                            }
                                                        }
                                                        function reloadfileupload(){

                                                            var $n = jQuery.noConflict();
                                                            var fragment = $n("#image_name").val();
                                                            var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");
                                                            var validExtensions = new Array();
                                                            validExtensions[0] = 'jpg';
                                                            validExtensions[1] = 'jpeg';
                                                            validExtensions[2] = 'png';
                                                            validExtensions[3] = 'gif';
                                                            validExtensions[4] = 'bmp';
                                                            validExtensions[5] = 'tif';
                                                            var extension = filename.substr((filename.lastIndexOf('.') + 1)).toLowerCase();
                                                            var inarr = parseInt($n.inArray(extension, validExtensions));
                                                            if (inarr < 0){

                                                                $n("#err_daynamic").remove();
                                                                $n('#fileuploaddiv').html($n('#fileuploaddiv').html());
                                                                $n("#image_name").after('<label class="image_error" id="err_daynamic">Invalid file extension</label>');
                                                            }
                                                            else{
                                                                $n("#err_daynamic").remove();
                                                            }


                                                        }
                                                    </script>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>     
                        <div id="postbox-container-1" class="postbox-container" > 

                            <div class="postbox"> 
                                <h3 class="hndle"><span></span>Access All Themes In One Price</h3> 
                                <div class="inside">
                                    <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                                    <div style="margin:10px 5px">

                                    </div>
                                </div></div>
                            <div class="postbox"> 
                                <h3 class="hndle"><span></span>Recommended WordPress Hostings</h3> 
                                <div class="inside">
                                    <center><a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=nik00726-hs-wp"><img src="http://tracking.hostgator.com/img/WordPress_Hosting/300x250-animated.gif" width="250" height="250" border="0"></a></center>
                                    <div style="margin:10px 5px">
                                    </div>
                                </div></div>

                        </div> 
                    </div>
                </div>                 
                <?php
            }
        } else if (strtolower($action) == strtolower('delete')) {

            $uploads = wp_upload_dir();
            $baseDir = $uploads ['basedir'];
            $baseDir = str_replace("\\", "/", $baseDir);
            $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';


            $location = "admin.php?page=responsive_video_gallery_with_lightbox_video_management";
            $deleteId = (int) $_GET ['id'];

            try {

                $query = "SELECT * FROM " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox WHERE id=$deleteId";
                $myrow = $wpdb->get_row($query);

                if (is_object($myrow)) {

                    $image_name = stripslashes($myrow->image_name);
                    $wpcurrentdir = dirname(__FILE__);
                    $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                    $imagetoDel = $pathToImagesFolder . '/' . $image_name;
                    $settings = get_option('responsive_video_gallery_slider_settings');
                    $imageheight = $settings ['imageheight'];
                    $imagewidth = $settings ['imagewidth'];

                    $pInfo = pathinfo($myrow->HdnMediaSelection);
                    $ext = $pInfo ['extension'];

                    @unlink($imagetoDel);
                    @unlink($pathToImagesFolder . '/' . $myrow->vid . '_big_' . $imageheight . '_' . $imagewidth . '.' . $ext);


                    $query = "delete from  " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox where id=$deleteId";
                    $wpdb->query($query);

                    $responsive_video_gallery_plus_lightbox_messages = array();
                    $responsive_video_gallery_plus_lightbox_messages ['type'] = 'succ';
                    $responsive_video_gallery_plus_lightbox_messages ['message'] = 'Video deleted successfully.';
                    update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                }
            } catch (Exception $e) {

                $responsive_video_gallery_plus_lightbox_messages = array();
                $responsive_video_gallery_plus_lightbox_messages ['type'] = 'err';
                $responsive_video_gallery_plus_lightbox_messages ['message'] = 'Error while deleting video.';
                update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
            }

            echo "<script type='text/javascript'> location.href='$location';</script>";
            exit();
        } else if (strtolower($action) == strtolower('deleteselected')) {


            $location = "admin.php?page=responsive_video_gallery_with_lightbox_video_management";

            if (isset($_POST) and isset($_POST ['deleteselected']) and ($_POST ['action'] == 'delete' or $_POST ['action_upper'] == 'delete')) {

                $uploads = wp_upload_dir();
                $baseDir = $uploads ['basedir'];
                $baseDir = str_replace("\\", "/", $baseDir);
                $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';

                if (sizeof($_POST ['thumbnails']) > 0) {

                    $deleteto = $_POST ['thumbnails'];
                    $implode = implode(',', $deleteto);

                    try {

                        $settings = get_option('responsive_video_gallery_slider_settings');
                        $imageheight = $settings ['imageheight'];
                        $imagewidth = $settings ['imagewidth'];

                        foreach ($deleteto as $img) {

                            $query = "SELECT * FROM " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox WHERE id=$img";
                            $myrow = $wpdb->get_row($query);

                            if (is_object($myrow)) {

                                $image_name = stripslashes($myrow->image_name);
                                $wpcurrentdir = dirname(__FILE__);
                                $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
                                $imagetoDel = $pathToImagesFolder . '/' . $image_name;

                                $pInfo = pathinfo($myrow->HdnMediaSelection);
                                $ext = $pInfo ['extension'];

                                @unlink($imagetoDel);
                                @unlink($pathToImagesFolder . '/' . $myrow->vid . '_big_' . $imageheight . '_' . $imagewidth . '.' . $ext);



                                $query = "delete from  " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox where id=$img";
                                $wpdb->query($query);

                                $responsive_video_gallery_plus_lightbox_messages = array();
                                $responsive_video_gallery_plus_lightbox_messages ['type'] = 'succ';
                                $responsive_video_gallery_plus_lightbox_messages ['message'] = 'selected videos deleted successfully.';
                                update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                            }
                        }
                    } catch (Exception $e) {

                        $responsive_video_gallery_plus_lightbox_messages = array();
                        $responsive_video_gallery_plus_lightbox_messages ['type'] = 'err';
                        $responsive_video_gallery_plus_lightbox_messages ['message'] = 'Error while deleting videos.';
                        update_option('responsive_video_gallery_plus_lightbox_messages', $responsive_video_gallery_plus_lightbox_messages);
                    }

                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit();
                } else {

                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit();
                }
            } else {

                echo "<script type='text/javascript'> location.href='$location';</script>";
                exit();
            }
        }
    }

    function responsive_video_gallery_with_lightbox_video_preview_func() {
        global $wpdb;


        $settings = get_option('responsive_video_gallery_slider_settings');

        $rand_Numb = uniqid('thumnail_slider');
        $rand_Num_td = uniqid('divSliderMain');
        $rand_var_name = uniqid('rand_');


        $wpcurrentdir = dirname(__FILE__);
        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
        // $settings=get_option('thumbnail_slider_settings');

        $uploads = wp_upload_dir();
        $baseDir = $uploads ['basedir'];
        $baseDir = str_replace("\\", "/", $baseDir);
        $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';
        $baseurl = $uploads ['baseurl'];
        $baseurl .= '/wp-responsive-video-gallery-with-lightbox/';
        ?>      
        <style type='text/css'>
            #<?php echo $rand_Num_td; ?> .bx-wrapper .bx-viewport {background: none repeat scroll 0 0<?php echo $settings ['scollerBackground']; ?> ! important;
                                                                   border: 0px none !important;
                                                                   box-shadow: 0 0 0 0 !important;
                                                                   /*padding:<?php echo $settings['imageMargin']; ?>px !important;*/
            }
            #poststuff #post-body.columns-2{margin-right: 0px}
        </style>
    <?php
    $wpcurrentdir = dirname(__FILE__);
    $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
    $randOmeAlbName = uniqid('alb_');
    $randOmeRel = uniqid('rel_');
    ?>
        <div style="width: 100%;">
            <div style="float: left; width: 100%;">
                <div class="wrap">
                    <h2>Slider Preview</h2>

                                        <?php if (is_array($settings)) { ?>
                        <div id="poststuff">
                            <div id="post-body" class="metabox-holder columns-2">
                                <div id="post-body-content">
                                    <div style="clear: both;"></div>
                                            <?php $url = plugin_dir_url(__FILE__); ?>           

                                    <div style="width: auto; postion: relative" id="<?php echo $rand_Num_td; ?>">
                                        <div id="<?php echo $rand_Numb; ?>" class="responsiveSlider" style="margin-top: 2px !important; visibility: hidden;">
                                            <?php
                                            global $wpdb;
                                            $imageheight = $settings ['imageheight'];
                                            $imagewidth = $settings ['imagewidth'];
                                            $query = "SELECT * FROM " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox  order by createdon desc";
                                            $rows = $wpdb->get_results($query, 'ARRAY_A');

                                            if (count($rows) > 0) {

                                                foreach ($rows as $row) {

                                                    $imagename = $row ['image_name'];
                                                    $video_url = $row ['video_url'];
                                                    $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                                    $imageUploadTo = str_replace("\\", "/", $imageUploadTo);
                                                    $pathinfo = pathinfo($imageUploadTo);
                                                    $filenamewithoutextension = $pathinfo ['filename'];
                                                    $outputimg = "";

                                                    $outputimgmain = $baseurl . $row ['image_name'];
                                                    if ($settings ['resizeImages'] == 0) {

                                                        $outputimg = $baseurl . $row ['image_name'];
                                                    } else {
                                                        $imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];

                                                        if (file_exists($imagetoCheck)) {
                                                            $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                        } else {

                                                            if (function_exists('wp_get_image_editor')) {

                                                                $image = wp_get_image_editor($pathToImagesFolder . "/" . $row ['image_name']);

                                                                if (!is_wp_error($image)) {
                                                                    $image->resize($imagewidth, $imageheight, true);
                                                                    $image->save($imagetoCheck);
                                                                    $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                } else {
                                                                    $outputimg = $baseurl . $row ['image_name'];
                                                                }
                                                            } else if (function_exists('image_resize')) {

                                                                $return = image_resize($pathToImagesFolder . "/" . $row ['image_name'], $imagewidth, $imageheight);
                                                                if (!is_wp_error($return)) {

                                                                    $isrenamed = rename($return, $imagetoCheck);
                                                                    if ($isrenamed) {
                                                                        $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                                    } else {
                                                                        $outputimg = $baseurl . $row ['image_name'];
                                                                    }
                                                                } else {
                                                                    $outputimg = $baseurl . $row ['image_name'];
                                                                }
                                                            } else {

                                                                $outputimg = $baseurl . $row ['image_name'];
                                                            }

                                                            // $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                                        }
                                                    }
                                                    $embed_url = $row['embed_url'];

                                                    $title = "";
                                                    $rowTitle = stripslashes($row['videotitle']);
                                                    $rowTitle = str_replace("'", "’", $rowTitle);
                                                    $rowTitle = str_replace('"', '”', $rowTitle);

                                                    $rowDescrption = stripslashes($row['video_description']);
                                                    $rowDescrption = str_replace("'", "’", $rowDescrption);
                                                    $rowDescrption = str_replace('"', '”', $rowDescrption);
                                                    $rowDescrption = strip_tags($rowDescrption);

                                                    if (strlen($rowDescrption) > 300) {

                                                        $rowDescrption = substr($rowDescrption, 0, 300) . "...";
                                                    }
                                                    //$openImageInNewTab='_blank';
                                                    $open_link_in = $row['open_link_in'];
                                                    // if($open_link_in==0){
                                                    $openImageInNewTab = '_self';
                                                    //}

                                                    if (trim($row['videotitle']) != '' and trim($row['videotitleurl']) != '') {

                                                        $title = "<a class='Imglink' target='$openImageInNewTab' href='{$row['videotitleurl']}'>{$rowTitle}</a>";
                                                        if ($row['video_description'] != '') {
                                                            $title.="<div class='clear_description_'>{$rowDescrption}</div>";
                                                        }
                                                    } else if (trim($row['videotitle']) != '' and trim($row['videotitleurl']) == '') {

                                                        $title = "<a class='Imglink' href='#'>{$rowTitle}</a>";
                                                        if ($row['video_description'] != '') {
                                                            $title.="<div class='clear_description_'>{$rowDescrption}</div>";
                                                        }
                                                    } else {

                                                        if ($row['video_description'] != '')
                                                            $title = "<div class='clear_description_'>{$row['video_description']}</div>";
                                                    }
                                                    ?>
                                                    <div class="video">
                                                        <a rel="<?php echo $randOmeRel; ?>" data-overlay="1" data-title="<?php echo $title; ?>" class="video_lbox" href="<?php echo $embed_url; ?>">
                                                            <img    src="<?php echo $outputimg; ?>" alt="<?php echo $rowTitle; ?>" title="<?php
                                                    if (trim($rowDescrption) != '') {
                                                        echo $rowDescrption;
                                                    } else {
                                                        echo $rowTitle;
                                                    };
                                                    ?>" />
                                                            <span class="playbtnCss"></span>  

                                                        </a> 
                                                        <div class="video-title"><?php echo $rowTitle; ?></div>

                                                    </div>

            <?php } ?>   
        <?php } ?>   
                                        </div>
                                    </div>
                                    <script>
                                        var $n = jQuery.noConflict();
                                        var uniqObj=$n("a[rel='<?php echo $randOmeRel; ?>']");
                                                                           
                                        $n(document).ready(function(){
                                            var <?php echo $rand_var_name; ?> = $n('#<?php echo $rand_Num_td; ?>').html();
                                            $n('#<?php echo $rand_Numb; ?>').bxSlider({
        <?php if ($settings['visible'] == 1): ?>
                        mode:'fade',
        <?php endif; ?>
                    slideWidth: <?php echo $settings['imagewidth']; ?>,
                    minSlides: <?php echo $settings['min_visible']; ?>,
                    maxSlides: <?php echo $settings['visible']; ?>,
                    moveSlides: <?php echo $settings['scroll']; ?>,
                    slideMargin:<?php echo $settings['imageMargin']; ?>,
                    speed:<?php echo $settings['speed']; ?>,
                    pause:<?php echo $settings['pause']; ?>,
        <?php if ($settings['pauseonmouseover'] and $settings['auto']) { ?>
                        autoHover: true,
            <?php
        } else {
            if ($settings ['auto']) {
                ?>
                                    autoHover:false,
                <?php
            }
        }
        ?>
        <?php if ($settings['auto']): ?>
                        controls:false,
        <?php else: ?>
                        controls:true,
        <?php endif; ?>
                    pager:false,
                    useCSS:false,
        <?php if ($settings['auto']): ?>
                        autoStart:true,
                        autoDelay:200,
                        auto:true,
        <?php endif; ?>
        <?php if ($settings['circular']): ?>
                        infiniteLoop: true,
        <?php else: ?>
                        infiniteLoop: false,
        <?php endif; ?>
        <?php if ($settings['show_caption']): ?>
                        captions:true,
        <?php else: ?>
                        captions:false,
        <?php endif; ?>
        <?php if ($settings['show_pager']): ?>
                        pager:true,
        <?php else: ?>
                        pager:false,
        <?php endif; ?>
                    easing: '<?php echo ($settings['easing']); ?>',
                    onSliderLoad: function(){
                                                   
                        $n("#<?php echo $rand_Numb; ?>").css("visibility", "visible");
                        $n(".video_lbox").fancybox({
                            'type'    : "iframe",
                            'overlayColor':'#000000',
                            'padding': 10,
                            'autoScale': true,
                            'autoDimensions':true,
                            'transitionIn': 'none',
                            'uniqObj':uniqObj,
                            'transitionOut': 'none',
                            'titlePosition': 'outside',
        <?php if ($settings['circular']): ?>
                                'cyclic':true,
        <?php else: ?>
                                'cyclic':false,
        <?php endif; ?>
                            'hideOnContentClick':false,
                            'width' : 650,
                            'height' : 400,
                            'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                var currtElem = $n('#<?php echo $rand_Numb; ?> a[href="'+currentOpts.href+'"]');

                                var isoverlay = $n(currtElem).attr('data-overlay')

                                if(isoverlay=="1" && $n.trim(title)!=""){
                                    return '<span id="fancybox-title-over">' + title  + '</span>';
                                }
                                else{
                                    return '';
                                }

                            },

                        });
                                                                     
                                                     
                                                  
                                                     
                    }               

                });
                $n("#<?php echo $rand_Numb; ?>").show();
        <?php if ($settings['auto']) { ?>
            <?php $newrand = rand(0, 1111111111); ?>
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > - 1;
                        var is_android = navigator.userAgent.toLowerCase().indexOf('android') > - 1;
                        var is_iphone = navigator.userAgent.toLowerCase().indexOf('iphone') > - 1;
                        var width = $n(window).width();
                        if (is_firefox && (is_android || is_iphone)){

                        } else{
                            var timer;
                            $n(window).bind('resize', function(){
                                if ($n(window).width() != width){
                        		
                                    width = $n(window).width();
                                    timer && clearTimeout(timer);
                                    timer = setTimeout(onResize<?php echo $newrand; ?>, 600);
                                }
                            });
                        }

                        function onResize<?php echo $newrand; ?>(){
                            $n('#<?php echo $rand_Num_td; ?>').html('');
                            $n('#<?php echo $rand_Num_td; ?>').html(<?php echo $rand_var_name; ?>);
                            $n('#<?php echo $rand_Numb; ?>').bxSlider({

            <?php if ($settings['visible'] == 1): ?>
                                    mode:'fade',
            <?php endif; ?>
                                slideWidth: <?php echo $settings['imagewidth']; ?>,
                                minSlides: <?php echo $settings['min_visible']; ?>,
                                maxSlides: <?php echo $settings['visible']; ?>,
                                moveSlides: <?php echo $settings['scroll']; ?>,
                                slideMargin:<?php echo $settings['imageMargin']; ?>,
                                speed:<?php echo $settings['speed']; ?>,
                                pause:<?php echo $settings['pause']; ?>,
            <?php if ($settings['pauseonmouseover'] and $settings['auto']) { ?>
                                    autoHover: true,
                <?php
            } else {
                if ($settings ['auto']) {
                    ?>
                                                autoHover:false,
                    <?php
                }
            }
            ?>
            <?php if ($settings['auto']): ?>
                                    controls:false,
            <?php else: ?>
                                    controls:true,
            <?php endif; ?>
                                pager:false,
                                useCSS:false,
            <?php if ($settings['auto']): ?>
                                    autoStart:true,
                                    autoDelay:200,
                                    auto:true,
            <?php endif; ?>
            <?php if ($settings['circular']): ?>
                                    infiniteLoop: true,
            <?php else: ?>
                                    infiniteLoop: false,
            <?php endif; ?>
            <?php if ($settings['show_caption']): ?>
                                    captions:true,
            <?php else: ?>
                                    captions:false,
            <?php endif; ?>
            <?php if ($settings['show_pager']): ?>
                                    pager:true,
            <?php else: ?>
                                    pager:false,
            <?php endif; ?>
                                easing: '<?php echo ($settings['easing']); ?>',
                                onSliderLoad: function(){
                                                           
                                    $n("#<?php echo $rand_Numb; ?>").css("visibility", "visible");
                                    $n(".video_lbox").fancybox({
                                        'type'    : "iframe",
                                        'overlayColor':'#000000',
                                        'padding': 10,
                                        'autoScale': true,
                                        'autoDimensions':true,
                                        'transitionIn': 'none',
                                        'uniqObj':uniqObj,
                                        'transitionOut': 'none',
                                        'titlePosition': 'outside',
            <?php if ($settings['circular']): ?>
                                            'cyclic':true,
            <?php else: ?>
                                            'cyclic':false,
            <?php endif; ?>
                                        'hideOnContentClick':false,
                                        'width' : 650,
                                        'height' : 400,
                                        'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                            var currtElem = $n('#<?php echo $rand_Numb; ?> a[href="'+currentOpts.href+'"]');

                                            var isoverlay = $n(currtElem).attr('data-overlay')

                                            if(isoverlay=="1" && $n.trim(title)!=""){
                                                return '<span id="fancybox-title-over">' + title  + '</span>';
                                            }
                                            else{
                                                return '';
                                            }

                                        },

                                    });




                                } 

                            });
                                                                                    
                        }

                        <?php } ?>

                			        		 
                		
            });
                                    </script>

                                </div>
                            </div>
                        </div>  
    <?php } ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <?php if (is_array($settings)) { ?>

            <h3>To print this video gallery into WordPress Post/Page use below code</h3>
            <input type="text" value='[print_responsive_video_gallery_plus_lightbox] '
                   style="width: 400px; height: 30px"
                   onclick="this.focus(); this.select()" />
            <div class="clear"></div>
            <h3>To print this video gallery into WordPress theme/template PHP files use below code</h3>
            <?php
            $shortcode = '[print_responsive_video_gallery_plus_lightbox]';
            ?>
            <input type="text" value="&lt;?php echo do_shortcode('<?php echo htmlentities($shortcode, ENT_QUOTES); ?>'); ?&gt;" style="width: 400px; height: 30px" onclick="this.focus(); this.select()" />
        <?php } ?>
        <div class="clear"></div>
        <?php
    }

    function print_responsive_video_gallery_plus_lightbox_func($atts) {
        ob_start();

        global $wpdb;

        $settings = get_option('responsive_video_gallery_slider_settings');
        $rand_Numb = uniqid('thumnail_slider');
        $rand_Num_td = uniqid('divSliderMain');
        $rand_var_name = uniqid('rand_');


        $wpcurrentdir = dirname(__FILE__);
        $wpcurrentdir = str_replace("\\", "/", $wpcurrentdir);
        // $settings=get_option('thumbnail_slider_settings');

        $uploads = wp_upload_dir();
        $baseDir = $uploads ['basedir'];
        $baseDir = str_replace("\\", "/", $baseDir);
        $pathToImagesFolder = $baseDir . '/wp-responsive-video-gallery-with-lightbox';
        $baseurl = $uploads ['baseurl'];
        $baseurl .= '/wp-responsive-video-gallery-with-lightbox/';
        $randOmeRel = uniqid('rel_');
        $randOmVlBox = uniqid('video_lbox_');
        ?>      
        <style type='text/css'>
            #<?php echo $rand_Num_td; ?> .bx-wrapper .bx-viewport {
                background: none repeat scroll 0 0<?php echo $settings ['scollerBackground']; ?> ! important;
                border: 0px none !important;
                box-shadow: 0 0 0 0 !important;
                /*padding:<?php echo $settings['imageMargin']; ?>px !important;*/
            }
        </style>	
                <?php
                if (is_array($settings)) {
                    ?>
            <div style="clear: both;"></div>
                    <?php $url = plugin_dir_url(__FILE__); ?>           

            <div style="width: auto; postion: relative" id="<?php echo $rand_Num_td; ?>">
                <div id="<?php echo $rand_Numb; ?>" class="responsiveSlider" style="margin-top: 2px !important; visibility: hidden;">
                        <?php
                        global $wpdb;
                        $imageheight = $settings ['imageheight'];
                        $imagewidth = $settings ['imagewidth'];
                        $query = "SELECT * FROM " . $wpdb->prefix . "responsive_video_gallery_plus_responsive_lightbox order by createdon desc";
                        $rows = $wpdb->get_results($query, 'ARRAY_A');
                        $totalCount = 0;
                        if (count($rows) > 0) {
                            foreach ($rows as $row) {
                                if ($totalCount == 0 || $totalCount % 3 == 0)
                                    echo '<div class="row video-row">';
                                ?>
                    
                    
                    
                            <div class="large-4 small-12 columns">
                                <?php
                                
                                $imagename = $row ['image_name'];
                                $video_url = $row ['video_url'];
                                $imageUploadTo = $pathToImagesFolder . '/' . $imagename;
                                $imageUploadTo = str_replace("\\", "/", $imageUploadTo);
                                $pathinfo = pathinfo($imageUploadTo);
                                $filenamewithoutextension = $pathinfo ['filename'];
                                $outputimg = "";

                                $outputimgmain = $baseurl . $row ['image_name'];
                                if ($settings ['resizeImages'] == 0) {

                                    $outputimg = $baseurl . $row ['image_name'];
                                } else {
                                    $imagetoCheck = $pathToImagesFolder . '/' . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];

                                    if (file_exists($imagetoCheck)) {
                                        $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                    } else {

                                        if (function_exists('wp_get_image_editor')) {

                                            $image = wp_get_image_editor($pathToImagesFolder . "/" . $row ['image_name']);

                                            if (!is_wp_error($image)) {
                                                $image->resize($imagewidth, $imageheight, true);
                                                $image->save($imagetoCheck);
                                                $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                            } else {
                                                $outputimg = $baseurl . $row ['image_name'];
                                            }
                                        } else if (function_exists('image_resize')) {

                                            $return = image_resize($pathToImagesFolder . "/" . $row ['image_name'], $imagewidth, $imageheight);
                                            if (!is_wp_error($return)) {

                                                $isrenamed = rename($return, $imagetoCheck);
                                                if ($isrenamed) {
                                                    $outputimg = $baseurl . $filenamewithoutextension . '_' . $imageheight . '_' . $imagewidth . '.' . $pathinfo ['extension'];
                                                } else {
                                                    $outputimg = $baseurl . $row ['image_name'];
                                                }
                                            } else {
                                                $outputimg = $baseurl . $row ['image_name'];
                                            }
                                        } else {

                                            $outputimg = $baseurl . $row ['image_name'];
                                        }

                                        // $url = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                                    }
                                }
                                $embed_url = $row['embed_url'];

                                $title = "";
                                $rowTitle = stripslashes($row['videotitle']);
                                $rowTitle = str_replace("'", "’", $rowTitle);
                                $rowTitle = str_replace('"', '”', $rowTitle);

                                $rowDescrption = stripslashes($row['video_description']);
                                $rowDescrption = str_replace("'", "’", $rowDescrption);
                                $rowDescrption = str_replace('"', '”', $rowDescrption);
                                $rowDescrption = strip_tags($rowDescrption);

                                if (strlen($rowDescrption) > 300) {

                                    $rowDescrption = substr($rowDescrption, 0, 300) . "...";
                                }
                                // $openImageInNewTab='_blank';
                                $open_link_in = $row['open_link_in'];
                                //if($open_link_in==0){
                                $openImageInNewTab = '_self';
                                //}

                                if (trim($row['videotitle']) != '' and trim($row['videotitleurl']) != '') {

                                    $title = "<a class='Imglink' target='$openImageInNewTab' href='{$row['videotitleurl']}'>{$rowTitle}</a>";
                                    if ($row['video_description'] != '') {
                                        $title.="<div class='clear_description_'>{$rowDescrption}</div>";
                                    }
                                } else if (trim($row['videotitle']) != '' and trim($row['videotitleurl']) == '') {

                                    $title = "<a class='Imglink' href='#'>{$rowTitle}</a>";
                                    if ($row['video_description'] != '') {
                                        $title.="<div class='clear_description_'>{$rowDescrption}</div>";
                                    }
                                } else {

                                    if ($row['video_description'] != '')
                                        $title = "<div class='clear_description_'>{$row['video_description']}</div>";
                                }
                                ?>
                                <div class="video">
                                    <a rel="<?php echo $randOmeRel; ?>" data-overlay="1" data-title="<?php echo $title; ?>" class="<?php echo $randOmVlBox; ?>" href="<?php echo $embed_url; ?>">
                                        <img    src="<?php echo $outputimg; ?>" alt="<?php echo $rowTitle; ?>" title="<?php
                                if (trim($rowDescrption) != '') {
                                    echo $rowDescrption;
                                } else {
                                    echo $rowTitle;
                                };
                                ?>" />
                                        <span class="playbtnCss">
                                        </span> 

                                    </a> 
                                    <div class="video-title"><?php echo $rowTitle; ?></div>
                                </div>
                            </div> 
                        
                        

            <?php 
            $totalCount++;
            if($totalCount == 3 || $totalCount == $count)
            echo '</div>';
            
            } ?>   
        <?php } ?>   
                </div>
            </div>
            <script>
                var $n = jQuery.noConflict();
        <?php $uniqId = uniqid(); ?>
            var uniqObj<?php echo $uniqId ?>=$n("a[rel='<?php echo $randOmeRel; ?>']");
            $n(document).ready(function(){
                var <?php echo $rand_var_name; ?> = $n('#<?php echo $rand_Num_td; ?>').html();
                $n('#<?php echo $rand_Numb; ?>').bxSlider({
        <?php if ($settings['visible'] == 1): ?>
                        mode:'fade',
        <?php endif; ?>
                    slideWidth: <?php echo $settings['imagewidth']; ?>,
                    minSlides: <?php echo $settings['min_visible']; ?>,
                    maxSlides: <?php echo $settings['visible']; ?>,
                    moveSlides: <?php echo $settings['scroll']; ?>,
                    slideMargin:<?php echo $settings['imageMargin']; ?>,
                    speed:<?php echo $settings['speed']; ?>,
                    pause:<?php echo $settings['pause']; ?>,
        <?php if ($settings['pauseonmouseover'] and $settings['auto']) { ?>
                        autoHover: true,
            <?php
        } else {
            if ($settings ['auto']) {
                ?>
                                    autoHover:false,
                <?php
            }
        }
        ?>
        <?php if ($settings['auto']): ?>
                        controls:false,
        <?php else: ?>
                        controls:true,
        <?php endif; ?>
                    pager:false,
                    useCSS:false,
        <?php if ($settings['auto']): ?>
                        autoStart:true,
                        autoDelay:200,
                        auto:true,
        <?php endif; ?>
        <?php if ($settings['circular']): ?>
                        infiniteLoop: true,
        <?php else: ?>
                        infiniteLoop: false,
        <?php endif; ?>
        <?php if ($settings['show_caption']): ?>
                        captions:true,
        <?php else: ?>
                        captions:false,
        <?php endif; ?>
        <?php if ($settings['show_pager']): ?>
                        pager:true,
        <?php else: ?>
                        pager:false,
        <?php endif; ?>
                    easing: '<?php echo ($settings['easing']); ?>',
                                                                     
                    onSliderLoad: function(){
                                                   
                        $n("#<?php echo $rand_Numb; ?>").css("visibility", "visible");
                                                     
                        $n(".<?php echo $randOmVlBox; ?>").fancybox({
                            'type'    : "iframe",
                            'overlayColor':'#000000',
                            'padding': 10,
                            'autoScale': true,
                            'autoDimensions':true,
                            'uniqObj':uniqObj<?php echo $uniqId; ?>,
                            'transitionIn': 'none',
                            'transitionOut': 'none',
                            'titlePosition': 'outside',
        <?php if ($settings['circular']): ?>
                                'cyclic':true,
        <?php else: ?>
                                'cyclic':false,
        <?php endif; ?>
                            'hideOnContentClick':false,
                            'width' : 650,
                            'height' : 400,
                            'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                var currtElem = $n('#<?php echo $rand_Numb; ?> a[href="'+currentOpts.href+'"]');

                                var isoverlay = $n(currtElem).attr('data-overlay')

                                if(isoverlay=="1" && $n.trim(title)!=""){
                                    return '<span id="fancybox-title-over">' + title  + '</span>';
                                }
                                else{
                                    return '';
                                }

                            },

                        }); 
                                                     
                    }                                                         
                                                  
                                                 
                });
                                                 
        <?php if ($settings['auto']) { ?>
            <?php $newrand = rand(0, 1111111111); ?>
                        var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > - 1;
                        var is_android = navigator.userAgent.toLowerCase().indexOf('android') > - 1;
                        var is_iphone = navigator.userAgent.toLowerCase().indexOf('iphone') > - 1;
                        var width = $n(window).width();
                        if (is_firefox && (is_android || is_iphone)){

                        } else{
                            var timer;
                            $n(window).bind('resize', function(){
                                if ($n(window).width() != width){

                                    width = $n(window).width();
                                    timer && clearTimeout(timer);
                                    timer = setTimeout(onResize<?php echo $newrand; ?>, 600);
                                }
                            });
                        }

                        function onResize<?php echo $newrand; ?>(){
                            $n('#<?php echo $rand_Num_td; ?>').html('');
                            $n('#<?php echo $rand_Num_td; ?>').html(<?php echo $rand_var_name; ?>);
                            $n('#<?php echo $rand_Numb; ?>').bxSlider({

            <?php if ($settings['visible'] == 1): ?>
                                    mode:'fade',
            <?php endif; ?>
                                slideWidth: <?php echo $settings['imagewidth']; ?>,
                                minSlides: <?php echo $settings['min_visible']; ?>,
                                maxSlides: <?php echo $settings['visible']; ?>,
                                moveSlides: <?php echo $settings['scroll']; ?>,
                                slideMargin:<?php echo $settings['imageMargin']; ?>,
                                speed:<?php echo $settings['speed']; ?>,
                                pause:<?php echo $settings['pause']; ?>,
            <?php if ($settings['pauseonmouseover'] and $settings['auto']) { ?>
                                    autoHover: true,
                <?php
            } else {
                if ($settings ['auto']) {
                    ?>
                                                autoHover:false,
                    <?php
                }
            }
            ?>
            <?php if ($settings['auto']): ?>
                                    controls:false,
            <?php else: ?>
                                    controls:true,
            <?php endif; ?>
                                pager:false,
                                useCSS:false,
            <?php if ($settings['auto']): ?>
                                    autoStart:true,
                                    autoDelay:200,
                                    auto:true,
            <?php endif; ?>
            <?php if ($settings['circular']): ?>
                                    infiniteLoop: true,
            <?php else: ?>
                                    infiniteLoop: false,
            <?php endif; ?>
            <?php if ($settings['show_caption']): ?>
                                    captions:true,
            <?php else: ?>
                                    captions:false,
            <?php endif; ?>
            <?php if ($settings['show_pager']): ?>
                                    pager:true,
            <?php else: ?>
                                    pager:false,
            <?php endif; ?>
                                easing: '<?php echo ($settings['easing']); ?>',
                                onSliderLoad: function(){
                                                           
                                    $n("#<?php echo $rand_Numb; ?>").css("visibility", "visible");

                                    $n(".<?php echo $randOmVlBox; ?>").fancybox({
                                        'type'    : "iframe",
                                        'overlayColor':'#000000',
                                        'padding': 10,
                                        'autoScale': true,
                                        'autoDimensions':true,
                                        'uniqObj':uniqObj<?php echo $uniqId; ?>,
                                        'transitionIn': 'none',
                                        'transitionOut': 'none',
                                        'titlePosition': 'outside',
            <?php if ($settings['circular']): ?>
                                            'cyclic':true,
            <?php else: ?>
                                            'cyclic':false,
            <?php endif; ?>
                                        'hideOnContentClick':false,
                                        'width' : 650,
                                        'height' : 400,
                                        'titleFormat': function(title, currentArray, currentIndex, currentOpts) {

                                            var currtElem = $n('#<?php echo $rand_Numb; ?> a[href="'+currentOpts.href+'"]');

                                            var isoverlay = $n(currtElem).attr('data-overlay')

                                            if(isoverlay=="1" && $n.trim(title)!=""){
                                                return '<span id="fancybox-title-over">' + title  + '</span>';
                                            }
                                            else{
                                                return '';
                                            }

                                        },

                                    }); 

                                }        

                            });
                            $n("#<?php echo $rand_Numb; ?>").css("visibility", "visible");
                        }

            <?php } ?>

                                                  

            });
                                                      
                                                      
            </script>


            <?php
        }
        $output = ob_get_clean();
        return $output;
    }

    function responsive_video_gallery_plus_responsive_lightbox_get_wp_version() {
        global $wp_version;
        return $wp_version;
    }

// also we will add an option function that will check for plugin admin page or not
    function responsive_video_gallery_plus_lightbox_is_plugin_page() {
        $server_uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

        foreach (array(
    'responsive_video_gallery_with_lightbox_video_management'
        ) as $allowURI) {
            if (stristr($server_uri, $allowURI))
                return true;
        }
        return false;
    }

// add media WP scripts
    function responsive_video_gallery_plus_lightbox_admin_scripts_init() {
        if (responsive_video_gallery_plus_lightbox_is_plugin_page()) {
            // double check for WordPress version and function exists
            if (function_exists('wp_enqueue_media') && version_compare(responsive_video_gallery_plus_responsive_lightbox_get_wp_version(), '3.5', '>=')) {
                // call for new media manager
                wp_enqueue_media();
            }
            wp_enqueue_style('media');
        }
    }

    add_action('wp_print_styles', 'my_deregister_styles', 100);
    ?>