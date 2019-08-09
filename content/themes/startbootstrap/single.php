<?php get_header();?>

            <!-- Blog Post Content Column -->
            <div class="col-lg-8">

                <!-- Blog Post -->
				<?php while( have_posts() ):the_post();?>
                <!-- Title -->
                <h1><?php the_title();?></h1>

                <hr>

                <!-- Post Content -->
                <?php the_content();?>
                <hr>

                <!-- Blog Comments -->
	  			<?php comments_template(); ?>

				<?php endwhile;?>
            </div>
<?php get_sidebar();?>
<?php get_footer();?>