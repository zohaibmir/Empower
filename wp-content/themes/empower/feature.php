<?php
/*
  Template Name: Featured Events
 */


get_header();
?>
<?php if (have_posts()) : ?>
    <section id="content">
        <?php
        $num_of_images = get_count_group('page_group_slider_image');
        $num_of_puffers = get_count_group('post_puffer_description');
        while (have_posts()) : the_post();
            ?>

            <div class="row collapse">
                <div class="small-12 columns">
                    <div class="orbit-section">
                        <!--img src="images/slider.png" alt="slider" /-->

                        <ul class="example-orbit" data-orbit
                            data-options="animation:slide;
                            pause_on_hover:true;
                            timer_speed: 2500;
                            animation_speed:500;
                            navigation_arrows:true;
                            bullets:false;">
                                <?php
                                for ($i = 1; $i <= $num_of_images; $i++) {

                                    if (get('page_group_slider_image', $i) != null) {
                                        ?>



                                    <li>
                                        <img src="<?php echo get('page_group_slider_image', $i); ?>" alt="slide 1" />
                                        <div class="orbit-caption">
                                            <?php echo __(get('page_group_slider_caption', $i)); ?>
                                        </div>
                                    </li>



                                    <?php
                                }
                            }
                            ?>


                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="large-8 small-12 columns">
                    <div class="main-text">
                        <h1><?php the_title() ?></h1>
                        <?php the_content() ?>

                      
                    </div>
                </div>

                <div class="large-4 small-12 columns">
                    <div class="home-right retreat-right">                        

                        <?php if (!dynamic_sidebar('sidebar3')) : ?>

                        <?php endif; //end of right-sidebar  ?>
                      
                    </div>
                </div>
            </div>


    <?php endwhile; /* rewind or continue if all posts have been fetched */ ?>

        <?php
    endif;
    ?>
</section>

<?php get_footer(); ?>
