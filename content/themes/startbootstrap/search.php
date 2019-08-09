<?php get_header();?>
            <!-- Blog Entries Column -->
            <div class="col-md-8">
                <p class="lead">
                    Search of word : <?php the_search();?>
                </p>

                <!-- First Blog Post -->
                <?php while( have_posts() ): the_post();?>
                <a href="<?php the_permalink();?>"><?php the_title();?></a>
                <hr>
                <?php endwhile;?>
                <?php //get_paging_nav('<ul class="pager">','</ul>');?>

            </div>


<?php get_sidebar();?>
<?php get_footer();?>