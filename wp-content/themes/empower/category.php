<?php
get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

			

			<?php
					// Start the Loop.
					while ( have_posts() ) : the_post();

                                         the_title();
                                         the_content();
                                            
					endwhile;
					// Previous/next page navigation.
					//twentyfourteen_paging_nav();

				else :
					

				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_footer();
