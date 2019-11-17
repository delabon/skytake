<form class="skytake-form" <?php if( $is_editor_mode ) { echo 'onsubmit="return false;"'; } ?>>

    <?php require __DIR__ . '/unessential-inputs.php'; ?>

    <input class="skytake-email" 
        type="email" 
        value="<?php echo esc_attr( $campaign->setting('email_input_text') ); ?>" 
        onblur="if(this.value === '') { this.value='<?php echo esc_attr( $campaign->setting('email_input_text') ); ?>'}" 
        onfocus="if (this.value === '<?php echo esc_attr( $campaign->setting('email_input_text') ); ?>') {this.value=''}"
        required>

    <button class="skytake-submit" type="submit">
        
        <span class="__text">
            <?php echo esc_html( $campaign->setting('submit_button_text') ); ?>
        </span>

        <div class="__loader">
            <div></div>
        </div>

    </button>

    <div class="skytake-spam"><?php echo esc_html( $campaign->setting('spam_text') ); ?></div>

    <!-- gdpr -->
    <?php if( $is_editor_mode ): ?>

        <div class="skytake-gdpr-container <?php if( $campaign->setting('is_gdpr_enabled') == 0 ) echo '__hidden' ?>">
            <input class="skytake-gdpr" type="checkbox" required>
            <span class="skytake-gdpr-text"><?php echo wp_kses_post( $campaign->setting('gdpr_text') ); ?></span>
        </div>

    <?php elseif( $campaign->setting('is_gdpr_enabled') == 1 ): ?>

        <div class="skytake-gdpr-container">
            <input class="skytake-gdpr" type="checkbox" required>
            <span class="skytake-gdpr-text"><?php echo wp_kses_post( $campaign->setting('gdpr_text') ); ?></span>
        </div>

    <?php endif; ?>
    <!-- end gdpr -->

</form>
