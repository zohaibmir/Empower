<?php
/*
  Template Name: Blog
 */



get_header();
?>

<section id="content" >


    <?php if (have_posts()) :;
        ?>



        <?php
        $num_of_images = get_count_group('page_group_slider_image');
        $num_of_puffers = get_count_group('page_puffer_description');
        // Start the Loop.
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
                    <div class="main-text blog">

                        <?php
                        $currentId = get_the_ID();
                        $args = array(
                            'posts_per_page' => '5000',
                            'offset' => 0,
                            'category' => '15',
                            'include' => '',
                            'exclude' => "",
                            'post_type' => 'post',
                            'post_mime_type' => '',
                            'post_parent' => '',
                            'post_status' => 'publish',
                            'suppress_filters' => true,
                            'meta_key' => '',
                            'orderby' => '',
                            'order' => 'DESC'
                        );

                        $myposts = get_posts($args);

                        global $more;

                        foreach ($myposts as $post) : setup_postdata($post);

                            $more = 0;
                            ?>
                            <div class="row">
                                <div class="small-2 columns">
                                    <span class="pubdate"><?php the_date("d.m.Y", "", "", true);?>&nbsp;</span>
                                </div>
                                <div class="small-10 columns">
                                    <p>
                                        <a href="<?php the_permalink() ?>" style="text-decoration: none"><?php the_title() ?> </a><br />
                                        <!--span class="publisher">PUBLISERET AF <?php the_author() ?></span-->
                                    </p>
                                </div>

                                <hr />
                            </div>

                            <?php
                        endforeach;

                        wp_reset_postdata();
                        ?> 


                    </div>
                </div>


            </div>

            <?php
        endwhile;
// Previous/next page navigation.
//twentyfourteen_paging_nav();

    else :


    endif;
    ?>

</section><!-- #primary -->

<?php
get_footer();
