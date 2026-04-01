<?php if (is_amp()):
	$amp_class = 'content_amp';
endif; ?>

<meta itemprop="author" content="<?php the_author(); ?>">
<meta itemprop="datePublished" content="<?php echo get_the_date('Y-n-j'); ?>">
<meta itemprop="dateModified" content="<?php echo get_the_modified_date('Y-n-j'); ?>">
<meta itemprop="image" content="<?php echo get_home_url(); ?>/wp-content/uploads/2025/08/logo-7.svg">
<?php if( !is_front_page() ) {
	if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs();
}?>
<h1 itemprop="headline"><?php the_title(); ?></h1>
<div class="content_page <?php echo $amp_class; ?>" itemprop="articleBody">
	<?php the_content();?>
</div>
<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
	<meta itemprop="name" content="<?php echo get_bloginfo(); ?>">
	<meta itemprop="description" content="<?php echo get_bloginfo(); ?>">
	<div itemprop="logo" itemscope="" itemtype="https://www.schema.org/ImageObject">
		<link itemprop="url" href="<?php echo get_home_url(); ?>/wp-content/uploads/2025/08/logo-7.svg">
		<link itemprop="contentUrl" href="<?php echo get_home_url(); ?>/wp-content/uploads/2025/08/logo-7.svg">
	</div>
</div>