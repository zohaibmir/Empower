<?php
/*
  Template Name: landing
 */
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>        
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0 minimum-scale=1, maximum-scale=1"  />
        <meta http-equiv="x-ua-compatible" content="IE=10" >
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  

        <title><?php wp_title('|', true, 'right'); ?> Empower</title>

        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/app.css" />        
        <script src="<?php echo get_template_directory_uri(); ?>/bower_components/modernizr/modernizr.js"></script>


    </head>
    <body>
        <section class="landingpage">
            <?php if (have_posts()) : ?>

                <?php
                while (have_posts()) : the_post();
                    ?>

                    <div class="landing-content">
                        <div class="row">
                            <div class="small-12 columns">
                                <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Logo" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="small-12 columns">
                                <div class="landing-btns">
                                    <a href="?p=1&theme=1" class="ebutton">PRIVAT</a>&nbsp;&nbsp;<a href="?p=1&theme=2" class="ebutton">ERHVERV</a>
                                </div>
                            </div>



                        </div>
                        <div class="row">
                            <div class="small-12 columns text-center">
                                 <a href="/nyhedsbrev" class="ebutton">TILMELD NYHEDSBREV</a>
                            </div>
                        </div>


                    </div>

                <?php endwhile; /* rewind or continue if all posts have been fetched */ ?>

                <?php
            endif;
            ?>
        </section>

        <script src="<?php echo get_template_directory_uri(); ?>/bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/bower_components/foundation/js/foundation.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/app.js"></script>
    </body>
</html>