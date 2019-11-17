<?php
    $skyt_open_links = $campaign->setting('social_open_link_new_tab') == 1 ? 'target="_blank"' : '';
    $skyt_icon_design = esc_attr( str_replace('dlbicons-facebook-', '', $campaign->setting('social_icon_icon') ) );
    $skyt_icon_color_type = esc_attr( $campaign->setting('social_color_type') );

    $counter = 0;

    if( $campaign->setting('social_icon_facebook') !== '' ){
        $counter += 1;
    }
    if( $campaign->setting('social_icon_twitter') !== '' ){
        $counter += 1;
    }
    if( $campaign->setting('social_icon_instagram') !== '' ){
        $counter += 1;
    }
    if( $campaign->setting('social_icon_linkedin') !== '' ){
        $counter += 1;
    }
    if( $campaign->setting('social_icon_pinterest') !== '' ){
        $counter += 1;
    }
    if( $campaign->setting('social_icon_youtube') !== '' ){
        $counter += 1;
    }
?>

<div class="skytake-social-icons __color_type_<?php echo $skyt_icon_color_type; ?>" data-count="<?php echo $counter; ?>">

    <!-- facebook -->
    <?php if( $is_editor_mode ): ?>
        <a
            class="skytake-social-icon-facebook <?php if( $campaign->setting('social_icon_facebook') == '' ) echo '__hidden'; ?>" 
            href="https://facebook.com/<?php echo esc_attr( $campaign->setting('social_icon_facebook') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-facebook" class="dlbicons-facebook-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php elseif( $campaign->setting('social_icon_facebook') !== '' ): ?>
        <a
            href="https://facebook.com/<?php echo esc_attr( $campaign->setting('social_icon_facebook') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-facebook" class="dlbicons-facebook-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php endif; ?>
    <!-- endd facebook -->

    <!-- twitter -->
    <?php if( $is_editor_mode ): ?>
        <a
            class="skytake-social-icon-twitter <?php if( $campaign->setting('social_icon_twitter') == '' ) echo '__hidden'; ?>" 
            href="https://twitter.com/<?php echo esc_attr( $campaign->setting('social_icon_twitter') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-twitter" class="dlbicons-twitter-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php elseif( $campaign->setting('social_icon_twitter') !== '' ): ?>
        <a 
            href="https://twitter.com/<?php echo esc_attr( $campaign->setting('social_icon_twitter') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-twitter" class="dlbicons-twitter-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php endif; ?>
    <!-- endd twitter -->

    <!-- instagram -->
    <?php if( $is_editor_mode ): ?>
        <a 
            class="skytake-social-icon-instagram <?php if( $campaign->setting('social_icon_instagram') == '' ) echo '__hidden'; ?>" 
            href="https://www.instagram.com/<?php echo esc_attr( $campaign->setting('social_icon_instagram') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-instagram" class="dlbicons-instagram-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php elseif( $campaign->setting('social_icon_instagram') !== '' ): ?>
        <a 
            href="https://www.instagram.com/<?php echo esc_attr( $campaign->setting('social_icon_instagram') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-instagram" class="dlbicons-instagram-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php endif; ?>
    <!-- endd instagram -->

    <!-- linkedin -->
    <?php if( $is_editor_mode ): ?>
        <a 
            class="skytake-social-icon-linkedin <?php if( $campaign->setting('social_icon_linkedin') == '' ) echo '__hidden'; ?>" 
            href="https://www.linkedin.com/in/<?php echo esc_attr( $campaign->setting('social_icon_linkedin') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-linkedin" class="dlbicons-linkedin-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php elseif( $campaign->setting('social_icon_linkedin') !== '' ): ?>
        <a 
            href="https://www.linkedin.com/in/<?php echo esc_attr( $campaign->setting('social_icon_linkedin') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-linkedin" class="dlbicons-linkedin-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php endif; ?>
    <!-- endd linkedin -->

    <!-- pinterest -->
    <?php if( $is_editor_mode ): ?>
        <a 
            class="skytake-social-icon-pinterest <?php if( $campaign->setting('social_icon_pinterest') == '' ) echo '__hidden'; ?>" 
            href="https://www.pinterest.com/<?php echo esc_attr( $campaign->setting('social_icon_pinterest') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-pinterest" class="dlbicons-pinterest-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php elseif( $campaign->setting('social_icon_pinterest') !== '' ): ?>
        <a 
            href="https://www.pinterest.com/<?php echo esc_attr( $campaign->setting('social_icon_pinterest') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-pinterest" class="dlbicons-pinterest-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php endif; ?>
    <!-- endd pinterest -->

    <!-- youtube -->
    <?php if( $is_editor_mode ): ?>
        <a 
            class="skytake-social-icon-youtube <?php if( $campaign->setting('social_icon_youtube') == '' ) echo '__hidden'; ?>" 
            href="<?php echo esc_attr( $campaign->setting('social_icon_youtube') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-youtube" class="dlbicons-youtube-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php elseif( $campaign->setting('social_icon_youtube') !== '' ): ?>
        <a 
            href="<?php echo esc_attr( $campaign->setting('social_icon_youtube') ); ?>" 
            <?php echo $skyt_open_links; ?> 
        >
            <span data-icon="dlbicons-youtube" class="dlbicons-youtube-<?php echo $skyt_icon_design; ?>"></span>
        </a>
    <?php endif; ?>
    <!-- endd youtube -->

</div>