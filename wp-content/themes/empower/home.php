<?php
get_header();
?>

<section id="content">

    <?php while (have_posts()) : the_post(); ?>

        <div class="row collapse">
            <div class="small-12 columns">
                <?php the_post_thumbnail() ?>  
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


        <?php //get_template_part( 'content', get_post_format() ); ?>

        <!--nav class="nav-single">
                <h3 class="assistive-text"><?php _e('Post navigation', 'twentytwelve'); ?></h3>
                <span class="nav-previous"><?php previous_post_link('%link', '<span class="meta-nav">' . _x('&larr;', 'Previous post link', 'twentytwelve') . '</span> %title'); ?></span>
                <span class="nav-next"><?php next_post_link('%link', '%title <span class="meta-nav">' . _x('&rarr;', 'Next post link', 'twentytwelve') . '</span>'); ?></span>
        </nav><!-- .nav-single -->

        <?php comments_template('', true); ?>

    <?php endwhile; // end of the loop. ?>
</section>

<?php get_sidebar(); ?>
<?php get_footer(); ?>