<form class="skytake-form" <?php if( $is_editor_mode ) { echo 'onsubmit="return false;"'; } ?>>

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

</form>