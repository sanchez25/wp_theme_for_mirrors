<?php

remove_action( 'wp_head', 'wp_shortlink_wp_head' );

remove_action('wp_head', 'wp_generator');

remove_action ('wp_head', 'rsd_link');

remove_action('wp_head', 'rest_output_link_wp_head', 10);

add_action( 'init', 'wpkama_disable_embed_route', 99 );
function wpkama_disable_embed_route(){

	remove_action( 'rest_api_init', 'wp_oembed_register_route' );

	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	add_filter( 'rewrite_rules_array', function ( $rules ){

		foreach( $rules as $rule => $rewrite ){
			if( false !== strpos( $rewrite, 'embed=true' ) ){
				unset( $rules[$rule] );
			}
		}

		return $rules;
	} );
}

add_filter( 'rank_math/frontend/canonical', function( $canonical ) {
    return false;
});

/* htaccess */

add_action( 'acf/save_post', 'redirect_in_htaccess', 20 );
function redirect_in_htaccess( $post_id ) {

    if ( $post_id !== 'options' ) {
        return;
    }

    if ( ! function_exists( 'get_field' ) ) {
        return;
    }

    $add_redirect = get_field( 'add_redirect', 'option' );
    $site = trim( (string) get_field( 'site', 'option' ) );
    $com = trim( (string) get_field( 'com', 'option' ) );
    $destination_site = trim( (string) get_field( 'destination_site', 'option' ) );

    if ( ! function_exists( 'get_home_path' ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $htaccess_file = get_home_path() . '.htaccess';

    if ( ! file_exists( $htaccess_file ) || ! is_writable( $htaccess_file ) ) {
        return;
    }

    $contents = file_get_contents( $htaccess_file );
    if ( $contents === false ) {
        return;
    }

    $begin_marker = "# BEGIN 301 redirect\n";
    $end_marker   = "# END 301 redirect\n";

    $pattern = '~' . preg_quote( $begin_marker, '~' ) . '.*?' . preg_quote( $end_marker, '~' ) . "\n?~s";
    $contents = preg_replace( $pattern, '', $contents );

    if ( $add_redirect && $site && $com && $destination_site ) {

		$site_clean_raw = preg_replace( '~[^A-Za-z0-9.-]~', '', $site );
        $site_clean = str_replace( '.', '\\.', $site_clean_raw );
        $com_clean  = preg_replace( '~[^A-Za-z0-9]~', '', $com );

        // rules block
        $rules  = $begin_marker;
        $rules .= "RewriteEngine On\n";
        $rules .= "RewriteRule ^\\.well-known/ - [L]\n";
        $rules .= "RewriteCond %{HTTP_HOST} ^(?:www\\.)?" . $site_clean . "\\." . $com_clean . "$ [NC]\n";
        $rules .= "RewriteRule ^ " . $destination_site . "%{REQUEST_URI} [R=301,L,NE]\n";
        $rules .= $end_marker;

        $contents = $rules . "\n\n" . $contents;
    }

    file_put_contents( $htaccess_file, $contents );
}

/* Canonical */

function custom_canonical_link() {
    if ( function_exists('get_field') ) {
		$main_domain = get_field('main_domain', 'option');
        $canonical = get_field('canonical', 'option');
		if ( empty($canonical) ) {
            $canonical = $main_domain;
        }
		$uri = $_SERVER['REQUEST_URI'];
		$uri = strtok($uri, '?');
		$canonical_url = rtrim($canonical, '/') . $uri;
		echo '<link rel="canonical" href="'. esc_url($canonical_url) .'">' . "\n";
    }
}
add_action('wp_head', 'custom_canonical_link', 5);

add_theme_support(
	'custom-logo',
	array(
		'height'      => 250,
		'width'       => 250,
		'flex-width'  => true,
		'flex-height' => true,
	)
);

if( function_exists('acf_add_options_page') ) {
	acf_add_options_page(array(
		'page_title' 	=> 'Page options',
		'menu_title'	=> 'Options theme',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

add_action( 'after_setup_theme', function(){
	register_nav_menus( [
		'main' => 'Меню в шапке',
		'footer' => 'Меню в подвале'
	] );
} );

add_theme_support( 'post-thumbnails' );

function delete_intermediate_image_sizes( $sizes ){
	return array_diff( $sizes, [
		'medium_large',
		'large',
		'1536x1536',
		'2048x2048',
	] );
}

add_filter( 'intermediate_image_sizes', 'delete_intermediate_image_sizes' );

function remove_image_size_attributes($html) {
	return str_replace('size-full', '', $html);
}

add_filter( 'the_content', 'remove_image_size_attributes' );

function wpassist_remove_block_library_css(){
	wp_dequeue_style('wp-block-library');
}

add_action( 'wp_enqueue_scripts', 'wpassist_remove_block_library_css' );

function my_init() {
    if ( !is_admin() ) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', false);
    }
}
add_action('init', 'my_init');

add_action( 'wp_enqueue_scripts', 'style_theme' );

function style_theme() {
    wp_enqueue_style( 'style', get_stylesheet_uri() );
}

function is_amp() {
	return ($_GET['amp']) ? true : false;
}

add_filter( 'upload_mimes', 'svg_upload_allow' );

function svg_upload_allow( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';

	return $mimes;
}

add_filter( 'wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5 );

function fix_svg_mime_type( $data, $file, $filename, $mimes, $real_mime = '' ){

	if ( version_compare( $GLOBALS['wp_version'], '5.1.0', '>=' ) ) {
		$dosvg = in_array( $real_mime, ['image/svg', 'image/svg+xml'] );
	}
	else {
		$dosvg = ( '.svg' === strtolower( substr($filename, -4) ) );
	}

	if ($dosvg) {
		if ( current_user_can('manage_options') ) {
			$data['ext']  = 'svg';
			$data['type'] = 'image/svg+xml';
		} else {
			$data['ext']  = false;
			$data['type'] = false;
		}
	}
	return $data;
}

function wrap_content($content){

	$result = str_replace(
		array( '<h2' ), 
		array( '</section><section class="section-block"><h2' ), 
	
	$content);

	$section__counter = 1;
	$header__counter = 1;

	$result = preg_replace_callback('|<section(.*)>|Uis', function($matches) {

		global $section__counter;
		$section__counter++;

		return '<section class="section-block" id="section__'. $section__counter .'">';
	
	}, $result);

	$content = preg_replace_callback('|<h2(.*)</h2>|Uis', function($matches) use (&$headers) {

		$match = trim(strip_tags($matches[1]));
		$match = strstr($match, '>');
		$match = str_replace('>', '', $match);
		$heading = strtolower($match);
		$heading = str_replace(
			array(' ', ',', '!', '?', ':', '.','&nbsp;','(',')','¿'), 
			array('-', '','','','','','','','',''),
			$heading
		);
		
		$dash = ( is_numeric($match[0]) ? '_' : '' );

		return '<h2 id="'.$dash.$heading.'">' . $match . '</h2>';
	
	}, $result);

	$content = str_replace('</section><section class="section-block" id="section__1">', '<section class="section-block" id="section__1">', $content );
	$content .= '<div class="section-content"></div>';
	$content = str_replace('<div class="section-content"></div>', '<div class="section-content"></div></section>', $content );
	$content = str_replace('frameborder="0"', '', $content);
	return $content;

}

add_filter('the_content', 'wrap_content');

function content_banner($atts){

	$atts = shortcode_atts( array(
		'link' => '#',
		'img' => '',
		'alt' => 'احصل على مكافأة'
	), $atts );

	$output = '<div class="banner-link">';
				if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
					$output .= '<button class="banner-img" on="tap:AMP.navigateTo(url=\''.$atts['link'].'\')" aria-label="'.$atts['alt'].'">';
				else:
					$output .= '<button class="banner-img" onclick="location.href=\''.$atts['link'].'\'" aria-label="'.$atts['alt'].'">';
				endif;
					$output .= '
						<img src="'.$atts['img'].'" alt="'.$atts['alt'].'">
					</button>
			</div>';

	return $output;

}
add_shortcode( 'content-banner', 'content_banner' );

function content_btn($atts){

	$atts = shortcode_atts( array(
		'link' => '#',
		'text' => 'تحميل',
	), $atts );

	$output = '<div class="btn-content">';
				if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
					$output .= '<button class="btn banner_button" on="tap:AMP.navigateTo(url=\''.$atts['link'].'\')">';
				else:
					$output .= '<button class="btn banner_button" onclick="location.href=\''.$atts['link'].'\'">';
				endif;
					$output .= '
						<span>'.$atts['text'].'</span>
					</button>
			</div>';

	return $output;

}
add_shortcode( 'content-btn', 'content_btn' );

function content_bonus($atts){

	$atts = shortcode_atts( array(
		'link' => '#'
	), $atts );

	$output = '<div class="bonus">
				<div class="bonus__row">
					<div class="bonus__row-item">
						<div class="bonus__row-title">
							<span>Teklif otomatik olarak uygulanır</span>
						</div>
						<div class="bonus__row-text">
							<span><strong>375%</strong> hoş geldin bonusu</span>
						</div>
					</div>
					<div class="bonus__row-btn">';
						if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
							$output .= '<button class="btn btn-bonus" on="tap:AMP.navigateTo(url=\''.$atts['link'].'\')">';
						else:
							$output .= '<button class="btn btn-bonus" onclick="location.href=\''.$atts['link'].'\'">';
						endif;
							$output .= '
								Bonusu al
							</button>
					</div>
				</div>
				<div class="bonus__text">
					<span>Hesabını doğrula ve 10€ yatırımla gerektirmeyen ek bonus kazan. 18 yaşında veya daha büyük olmalısın. Şartlar ve koşullar geçerlidir.</span>
				</div>
			</div>';

	return $output;

}
add_shortcode( 'content-bonus', 'content_bonus' );

function content_promo($atts){

	$atts = shortcode_atts( array(
		'link-amp' => '#',
		'promocode' => 'PROMO',
	), $atts );

	$output = '<div class="promo">
				<div class="promo__content">
					<div class="promo__content-buttons">
						<div class="promo__content-code">
							<span id="promo-code">'.$atts['promocode'].'</span>
						</div>';
						if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
							$output .= ' <button class="btn btn-promo" on="tap:AMP.navigateTo(url=\''.$atts['link-amp'].'\')">Copiar</button> ';
						else:
							$output .= '<button id="btn-copy" class="btn btn-promo">
							Kopyala</button>';
						endif;
							$output .= '
					</div>
					<div class="promo__content-text">
						<strong>Sweet Bonanza</strong> slotunda <strong>VEGASAGEL250</strong> kodunu gir ve <strong>5TLlik 50 FS</strong>  AL!
					</div>
					<img src="/wp-content/uploads/2026/02/promo-img.webp" alt="Promosyon kodu">
				</div>
			</div>';

	return $output;

}
add_shortcode( 'content-promo', 'content_promo' );

function apk_banner($atts) {

	$atts = shortcode_atts( array(
		'link-android' => '#link',
		'link-ios' => '#link',
	), $atts );

	$output = '<div class="apk">
				<div class="apk__banner">
					<div class="apk__banner-col">
						<div class="apk__items">
							<div class="apk__items-item one">
								<span>7slots.casino’da “Kayıt Ol” butonuna tıklayarak ve bilgilerini girerek bir hesap oluştur.</span>
							</div>
							<div class="apk__items-item two">
								<span>İlk para yatırma işlemini kabul edilen bir ödeme yöntemi aracılığıyla gerçekleştir. Promosyon kodunu etkinleştirmek için, 7Slots’in şartlar ve koşullarında belirtilen minimum tutara eşdeğer bir miktar yatırman gerekir.</span>
							</div>
						</div>
					</div>
					<div class="apk__banner-col">
						<div class="apk__items">
							<div class="apk__items-item notify">
								<span>Uygulama test edilmiştir ve güvenli kurulum garanti edilir</span>
							</div>
							<div class="apk__items-item version">
								<span>Sürüm: 1.01</span>
							</div>
							<div class="apk__items-item size">
								<span>Oyuncu puanı: <strong></strong> 4.9/5</span>
							</div>
						</div>
						<div class="apk__buttons">';
							if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
								$output .= '<button class="btn apk-btn" on="tap:AMP.navigateTo(url=\''.$atts['link-android'].'\')">';
							else:
								$output .= '<button class="btn apk-btn" onclick="location.href=\''.$atts['link-android'].'\'">';
							endif;
								$output .= '
									<span>APK indir</span>
								</button>';
							if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
								$output .= '<button class="btn apk-btn" on="tap:AMP.navigateTo(url=\''.$atts['link-ios'].'\')">';
							else:
								$output .= '<button class="btn apk-btn" onclick="location.href=\''.$atts['link-ios'].'\'">';
							endif;
								$output .= '
									<span>APK indir</span>
								</button>
						</div>
					</div>
					<img src="/wp-content/uploads/2026/02/apk-banner.webp" alt="APK indir">
				</div>
			</div>';

	return $output;

}
add_shortcode( 'apk-banner', 'apk_banner' );

function content_reg($atts){

	$atts = shortcode_atts( array(
		'link' => '#'
	), $atts );

	$output = '<div class="registration">
				<div class="registration__col">
					<span><strong>375%</strong> hoş geldin bonusu!</span>
					<div class="steps">
						<div class="step one">
							<span>Kayıt yöntemini seç</span>
						</div>
						<div class="step two">
							<span>Gerekli bilgileri doldur</span>
						</div>
						<div class="step three">
							<span>Hesabını doğrula</span>
						</div>
						<div class="step four">
							<span>İlk para yatırma işlemini gerçekleştir</span>
						</div>
						<div class="step five">
							<span>Hoş geldin bonusunu talep et</span>
						</div>
						<div class="step six">
							<span>Oynamaya başla</span>
						</div>
					</div>';
					if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) :
						$output .= '<button class="btn btn-reg" on="tap:AMP.navigateTo(url=\''.$atts['link'].'\')">';
					else:
						$output .= '<button class="btn btn-reg" onclick="location.href=\''.$atts['link'].'\'">';
					endif;
						$output .= '
							Kayıt ol
						</button>
				</div>
				<div class="registration__img">
					<img src="/wp-content/uploads/2026/02/reg-img.webp" alt="Kayıt ol">
				</div>
			</div>';

	return $output;

}
add_shortcode( 'content-reg', 'content_reg' );