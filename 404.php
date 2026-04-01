<!DOCTYPE html>
<html lang="tr">
    <head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name='viewport' content='width=device-width,initial-scale=1'/>
		<?php wp_head(); ?>
		<title><?php wp_title(); ?></title>
    </head>
    <body class="error">
		<div class="error__block">
			<div class="error__block-content">
				<div class="error__block-title">
					<span>404</span>
					<img src="<?php echo get_home_url(); ?>/wp-content/uploads/2025/08/error-img.svg" alt="404">
				</div>
				<div class="error__block-text">
					<p>Sayfa bulunamadı</p>
					<a class="btn" href="/">Ana sayfaya git</a>
				</div>
			</div>
		</div>
    </body>
</html>
