<?php if (is_amp()):

    $amp_class = 'content_amp';

endif; ?>

<?php
    $offer_link = get_field('offer_link', 'option');
?>
<footer>
    <div class="footer wrapper <?php echo $amp_class; ?>">
        <div class="footer__bga">
            <img class="aff-img" src="<?php echo get_home_url(); ?>/wp-content/uploads/2025/08/aff-logo.svg" alt="7Aff">
            <div class="eighteen"></div>
        </div>
        <div class="footer__copyright">
            <span>© <?php echo date('Y');?> 7SLOTS Casino. Tüm hakları saklıdır.</span>
        </div>
        <?php if (is_amp()):?>
            <div class="scroll-top <?php echo $amp_class; ?>" on="tap:scroll.scrollTo(duration=200)">
                <div class="scroll-top-bg">    
                    <img src="<?php echo get_template_directory_uri() ?>/img/up-icon.svg" alt="scroll to top">
                </div>
            </div>
        <?php else: ?>
            <div class="scroll-top">
                <div class="scroll-top-bg"> 
                    <img src="<?php echo get_template_directory_uri() ?>/img/up-icon.svg" alt="scroll to top">
                </div>
            </div>
        <?php endif; ?>
    </div>
</footer>
<div class="fixed_buttons">
    <?php if (is_amp()): ?>
        <button class="btn reg" on="tap:AMP.navigateTo(url='<?php echo $offer_link; ?>')" >
    <?php else: ?>
        <button class="btn reg" onclick="location.href='<?php echo $offer_link; ?>'">
    <?php endif; ?>
        <span>Kayıt</span>    
    </button>
    <?php if (is_amp()): ?>
        <button class="btn log" on="tap:AMP.navigateTo(url='<?php echo $offer_link; ?>')" >
    <?php else: ?>
        <button class="btn log" onclick="location.href='<?php echo $offer_link; ?>'">
    <?php endif; ?>
        <span>Giriş</span>
    </button>
</div>

<?php wp_footer(); ?>

<script src="<?php echo get_template_directory_uri() ?>/js/main.js"></script>

</body>
</html>

