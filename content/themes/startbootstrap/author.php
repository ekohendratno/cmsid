<?php get_header();?>
            <!-- Blog Entries Column -->
            <div class="col-md-8">
                <p class="lead">
                    Author : <?php the_author_name();?>
                </p>

                <!-- First Blog Post -->
                <?php while( have_posts() ): the_post();?>
                <h2>
                    <a href="<?php the_permalink();?>"><?php the_title();?></a>
                </h2>
                <p class="lead">
                    by <a href="<?php the_permalink_author();?>"><?php the_author_name();?></a>
                </p>
                <p><span class="glyphicon glyphicon-time"></span> Posted on <?php startbootstrap_date( the_posted(false) );?></p>
                <hr>
                <img class="img-responsive" src="<?php the_thumbnail( get_template_directory_uri() . '/images/900x300.png' );?>" alt="">
                <hr>
                
                <?php the_content();?>
                <hr>
                <?php endwhile;?>
                <?php //get_paging_nav('<ul class="pager">','</ul>');?>

            </div>


<?php get_sidebar();?>
<?php get_footer();?>