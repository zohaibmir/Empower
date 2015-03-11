<?php
$theme = $wp_query->post->ID;
if (!session_id())
    session_start();

if ($theme == 1396) {
    $_SESSION["cssfile"] = "2";
    $_SESSION["imagefolder"] = "/erhverv";
    $_SESSION["themeName"] = "Erhverv";
}
else if($theme == 1390) {
    $_SESSION["cssfile"] = "";
    $_SESSION["imagefolder"] = "";
    $_SESSION["themeName"] = "Privat";
}
?>

<!doctype html>
<html class="no-js" lang="en">
    <head>        
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0 minimum-scale=1, maximum-scale=1"  />
        <meta http-equiv="x-ua-compatible" content="IE=10" >
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  

        <title><?php wp_title('|', true, 'right'); ?> Empower</title>

        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/app<?php echo $_SESSION["cssfile"] ?>.css" />   
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/slicknav<?php echo $_SESSION["cssfile"] ?>.css" />   
        <script src="<?php echo get_template_directory_uri(); ?>/bower_components/modernizr/modernizr.js"></script>

        <?php wp_head() ?>
        
         <style>
            html, body {
                margin: 0 !important;
                padding: 0 !important;               
            }
            .appleLinks a {color:#000000;}
            .appleLinksWhite a {color:#ffffff;}
        </style>
    </head>
    <body>
        <header>
            <div class="row collapse">
                <div class="large-4 small-12 columns">
                    <?php if ($theme != 1396 && $theme != 1390) { ?>
                    <a href="http://paludanempower.dk/om-paludan-empower/" class="welcome-link">Velkommen to PaludanEmpower</a>
                    <?php } ?>
                    <div class="topnav-section">
                        <ul class="topnav">
                            <li class="has-dropdown">Menu
                                <?php
                                wp_nav_menu(array('theme_location' => $_SESSION["themeName"], 'menu_class' => 'nav', 'walker' => new My_Walker_Nav_Menu()));
                                ?>                                


                            </li>
                        </ul>

                         
                    </div>
                </div>
                <div class="large-4 small-12 columns">
                    <div class="logo">
                        <a href="http://paludanempower.dk/"> <img src="<?php echo get_template_directory_uri(); ?>/images<?php echo $_SESSION["imagefolder"] ?>/small-logo.png" alt="Logo" /></a><br />
                        <span class="themename"><?php echo $_SESSION["themeName"]; ?></span>

                    </div>
                </div>
                <div class="large-4 small-12 columns header-right">
                    <a href="/nyhedsbrev" class="button-em">TILMELD NYHEDSBREV</a>

                </div>
            </div>
        </header>


