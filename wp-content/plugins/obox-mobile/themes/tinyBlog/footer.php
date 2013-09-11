    <div id="footer-container">
    	<?php mobile_advert("footer"); ?>
        <div id="footer">
            <p><?php _e("Copyright", "obox-mobile"); ?> <?php bloginfo("blogname"); ?></p>
            <?php mobile_switch(); ?>
            <p class="obox"><?php echo stripslashes(get_option("mobile_custom_footer")); ?></p>
        </div>
        <?php wp_footer(); ?>
    </div>
</body>
</html>