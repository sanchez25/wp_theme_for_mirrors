<?php get_header(); ?>

<div id="content-block" class="content-block wrapper" itemid="/<?php echo get_page_uri(); ?>" itemscope itemtype="https://schema.org/Article">
    <div class="left-block">    
        <?php
            get_template_part('content');
        ?>
    </div>
</div>

<?php get_footer(); ?>





