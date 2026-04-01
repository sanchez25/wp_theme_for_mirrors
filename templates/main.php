<?php
    /* Template name: Main */
?>

<?php get_header(); ?>

<?php
    get_template_part('templates/banner');
?>
<?php
    $offer_link = get_field('offer_link', 'option');
?>

<div id="content-block" class="content-block wrapper" itemid="/" itemscope itemtype="https://schema.org/Article">
    <div class="left-block">
        <?php
            get_template_part('content');
        ?>
    </div>
</div>

<?php get_footer(); ?>





