<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name='viewport' content='width=device-width,initial-scale=1'/>
    <?php wp_head(); ?>
    <title><?php wp_title(); ?></title>
</head>
<body>
    <?php if ( is_amp() ):?>
        <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
		<script async custom-element="amp-bind" src="https://cdn.ampproject.org/v0/amp-bind-0.1.js"></script>
        <amp-sidebar id="sidebar" class="sample-sidebar" layout="nodisplay" side="right">
            <div class="close">
                <button on="tap:sidebar.toggle" class="closeButton">
                    <img src="<?php echo get_template_directory_uri() ?>/img/close.svg" alt="close">
                </button>
            </div>
            <div class="menu_amp">
                <?php 
					if (has_nav_menu('main')) {
						wp_nav_menu( array(
							'menu_class'=>'menu',
							'theme_location'=>'main'
						));
					} else {
						echo '';
					}
				?>
            </div>
        </amp-sidebar>
    <?php endif; ?>
    <div id="scroll"></div>
	<?php
		$offer_link = get_field('offer_link', 'option');
	?>
    <?php if ( !is_amp() ):?>
	<header class="header">
		<div class="row wrapper">
			<?php
				$custom_logo = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
				if ($custom_logo) {
					echo '<a href="/" class="logo">
							<img src="' . esc_url($custom_logo[0]) . '" alt="' . esc_attr(get_bloginfo('name')) . '">
						</a>';
				} else { ?>
					<a href="/" class="logo-text"><?php echo bloginfo('name') ?></a>
			<?php } ?>
			<?php 
				if (has_nav_menu('main')) { ?>
					<div class="main_menu">
						<?php 
							wp_nav_menu( array(
								'menu_class'=>'menu',
								'theme_location'=>'main'
							));
						?>
					</div>
				<?php } 
				else {
					echo '';
			} ?>
			<?php 
				if (has_nav_menu('main')) { ?>
			<div class="burger">
				<div class="burger__img"></div>
			</div>
			<?php } 
				else {
					echo '';
			} ?>
		</div>
	</header>
<?php else: ?>
	<header class="header content_amp">
		<div class="row wrapper">
			<?php
				$custom_logo = wp_get_attachment_image_src(get_theme_mod('custom_logo'), 'full');
				if ($custom_logo) {
					echo '<a href="/" class="logo">
							<img src="' . esc_url($custom_logo[0]) . '" alt="' . esc_attr(get_bloginfo('name')) . '">
						</a>';
				} else { ?>
					<a href="/" class="logo-text"><?php echo bloginfo('name') ?></a>
			<?php } ?>
			<?php 
				if (has_nav_menu('main')) { ?>
					<button on="tap:sidebar.toggle" class="burger">
						<div class="burger__img"></div>
					</button>
				<?php } ?>
		</div>
	</header>
<?php endif; ?>
