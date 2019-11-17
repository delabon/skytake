<?php
use function SKYT\is_premium;
?>

<div
    id="skytake-<?php echo $campaign->id() ?>"
    class="skytake skytake-scroll-box" 
    data-animation="<?php echo esc_attr( $campaign->setting('animation') ); ?>" 
    data-background="<?php echo $campaign->setting('body_bg_image') === "" ? 0 : 1; ?>"
    data-position="<?php echo $campaign->setting('scroll_box_position'); ?>"
    data-template="<?php echo esc_attr( $campaign->setting('template') ); ?>"
    data-sideimage="<?php echo $campaign->setting('main_image') === '' ? 0 : 1; ?>"
>

    <div class="skytake-container">

        <div class="skytake-content">

            <div class="skytake-box-one" style="<?php echo $campaign->setting('main_image') === '' ? '' : 'background-image:url('.$campaign->setting('main_image').');'; ?>"></div>

            <div class="skytake-box-two">

                <div class="skytake-view __request">

                    <div class="skytake-title">
                        <?php echo esc_html( $campaign->setting('title') ); ?>
                    </div>

                    <?php
                        if( is_premium() && file_exists(SKYTAKE_PATH . '/inc/campaigns/popup/views/frontend/urgency__premium_only.php') ){
                            require SKYTAKE_PATH . '/inc/campaigns/popup/views/frontend/urgency__premium_only.php';
                        }
                    ?>

                    <div class="skytake-message">
                        <?php echo esc_html( $campaign->setting('message') ); ?>
                    </div>

                    <?php require SKYTAKE_PATH . '/inc/campaigns/popup/views/frontend/form.php'; ?>

                </div><!--request-->

                <div class="skytake-view __response">

                    <div class="skytake-title">
                        <?php echo esc_html( $campaign->setting('title_after_sub') ); ?>
                    </div>    

                    <div class="skytake-message">
                        <?php echo esc_html( $campaign->setting('message_after_sub') ); ?>
                    </div>

                </div><!--response-->

                <?php require __DIR__ . '/social-icons.php'; ?>

            </div>
            
        </div><!--content-->

    </div><!--container-->

    <button class="skytake-close" title="<?php _e('Hide','skytake'); ?>">
        <?php echo skytake()->icons['close']; ?>
    </button>

</div>