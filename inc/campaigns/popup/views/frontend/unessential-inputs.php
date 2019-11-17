<?php

use function SKYT\is_premium;

$count = 0;
$has_name = $campaign->setting('is_input_name_enabled') == 1 ? true : false;
$has_mobile = $campaign->setting('is_mobile_input_enabled') == 1 ? true : false;

$name_placeholder = esc_attr( $campaign->setting('name_input_text') );
$mobile_placeholder = esc_attr( $campaign->setting('input_mobile_text') );

if( $has_name ){
    $count += 1;
}

if( $has_mobile ){
    $count += 1;
}

if( $is_editor_mode ): ?>

    <div class="skytake-form-more" data-count="<?php echo $count; ?>">

        <input  
            type="text"
            class="skytake-name <?php if( $has_name ) echo '__show'; ?>"
            value="<?php echo $name_placeholder; ?>" 
            onblur="if(this.value === '') this.value='<?php echo $name_placeholder; ?>'" 
            onfocus="if (this.value === '<?php echo $name_placeholder; ?>') this.value=''"
            >

        <input  
            type="text"
            class="skytake-mobile <?php if( $has_mobile ) echo '__show'; ?>"
            value="<?php echo $mobile_placeholder; ?>" 
            onblur="if(this.value === '') this.value='<?php echo $mobile_placeholder; ?>'" 
            onfocus="if (this.value === '<?php echo $mobile_placeholder; ?>') this.value=''"
            >

    </div>

<?php elseif( is_premium() && $count > 0 ): ?>

    <div class="skytake-form-more" data-count="<?php echo $count; ?>">

        <?php if( $has_name ): ?>

            <input  
                type="text"
                class="skytake-name __show"
                value="<?php echo $name_placeholder; ?>" 
                onblur="if(this.value === '') this.value='<?php echo $name_placeholder; ?>'" 
                onfocus="if (this.value === '<?php echo $name_placeholder; ?>') this.value=''"
                >

        <?php endif; ?>

        <?php if( $has_mobile ): ?>

            <input 
                type="text"
                class="skytake-mobile __show"
                value="<?php echo $mobile_placeholder; ?>" 
                onblur="if(this.value === '') this.value='<?php echo $mobile_placeholder; ?>'" 
                onfocus="if (this.value === '<?php echo $mobile_placeholder; ?>') this.value=''"
                >

        <?php endif; ?>

    </div>

<?php endif; ?>