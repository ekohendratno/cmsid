<?php get_header();?>

            <!-- Blog Post Content Column -->
            <div class="col-lg-8">

                <!-- Blog Post -->
				<?php while( have_posts() ):the_post();?>
                <!-- Title -->
                <h1><?php the_title();?></h1>

                <!-- Author -->
                <p class="lead">
                    by <a href="<?php the_permalink_author();?>"><?php the_author_name();?></a>
                </p>

                <hr>

                <!-- Date/Time -->
                <p><span class="glyphicon glyphicon-time"></span> Posted on <?php startbootstrap_date( the_posted(false) );?></p>

                <hr>

                <!-- Preview Image -->
                <img class="img-responsive" src="<?php the_thumbnail( get_template_directory_uri() . '/images/900x300.png' );?>" alt="">

                <hr>

                <!-- Post Content -->
                <?php the_content();?>
                <hr>
                <?php echo the_tags();?>
                <hr>

                <!-- Blog Comments -->
	  			<?php comments_template(); ?>

			<?php endwhile;?>
            </div>
<?php get_sidebar();?>
<?php get_footer();?>