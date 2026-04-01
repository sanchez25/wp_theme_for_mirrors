<?php
    $offer_link = get_field('offer_link', 'option');
?>
<div class="banner">
    <div class="banner__content wrapper">
        <div class="banner__content-text">
            <span class="banner-title">375%</span>
            <span class="banner-subtitle">hoş geldin bonusu!</span>
            <div class="banner-buttons">
                <?php if (is_amp()): ?>
                    <button class="btn banner_button" on="tap:AMP.navigateTo(url='<?php echo $offer_link; ?>')" >
                <?php else: ?>
                    <button class="btn banner_button"  onclick="location.href='<?php echo $offer_link; ?>'">
                <?php endif; ?>
                    <span>Bonus al</span>
                </button>
            </div>
        </div>
    </div>
</div>