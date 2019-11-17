<?php
use function SKYT\is_premium;
?>

<div
    id="skytake-<?php echo $campaign->id() ?>"
    class="skytake skytake-floating-bar" 
    data-animation="<?php echo esc_attr( $campaign->setting('animation') ); ?>" 
    data-background="<?php echo $campaign->setting('body_bg_image') === "" ? 0 : 1; ?>"
    data-position="<?php echo $campaign->setting('floating_bar_position'); ?>"
    data-template="<?php echo esc_attr( $campaign->setting('template') ); ?>"
>

    <div class="skytake-container">

        <div class="skytake-content">

            <div class="skytake-view __request">

                <div class="skytake-title">
                    <?php echo esc_html( $campaign->setting('title') ); ?>
                </div>

                <?php
                    if( is_premium() && file_exists(__DIR__ . '/urgency__premium_only.php') ){
                        require __DIR__ . '/urgency__premium_only.php';
                    }
                ?>

                <?php require __DIR__ . '/form.php'; ?>

            </div><!--request-->

            <div class="skytake-view __response">

                <div class="skytake-title">
                    <?php echo esc_html( $campaign->setting('title_after_sub') ); ?>
                </div>    

            </div><!--response-->

        </div><!--content-->

    </div><!--container-->

    <button class="skytake-close" title="<?php _e('Hide','skytake'); ?>">
        <?php echo skytake()->icons['close']; ?>
    </button>

</div>