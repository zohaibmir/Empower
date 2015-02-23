<?php
/*  
 * Template Name Posts: Post
 */

get_header();
?>

<?php if (have_posts()) : ?>
    <section id="content">
        <?php
        $num_of_images = get_count_group('page_group_slider_image');
        $num_of_puffers = get_count_group('page_puffer_description');
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
                <div class="large-12 small-12 columns">
                    <div class="main-text">
                        <?php
                        if (function_exists('bcn_display')) {
                            bcn_display();
                        }
                        ?>
                        <h1><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    </div>
                </div>


            </div>
        <?php endwhile; /* rewind or continue if all posts have been fetched */ ?>
    </section>
    <?php
endif;
?>
<?php get_footer(); ?>