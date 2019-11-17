<?php
use function SKYT\is_premium;
?>

<div
    id="skytake-<?php echo $campaign->id() ?>"
    class="skytake skytake-widget-form"
    data-background="<?php echo $campaign->setting('body_bg_image') === "" ? 0 : 1; ?>"
    data-template="<?php echo esc_attr( $campaign->setting('template') ); ?>"
    data-mainimage="<?php echo $campaign->setting('main_image') === '' ? 0 : 1; ?>"
>

    <div class="skytake-container">

        <div class="skytake-content">

            <div class="skytake-box-one">
                <?php if( $campaign->setting('main_image') !== '' ): ?> 
                    <img src="<?php echo $campaign->setting('main_image'); ?>" alt="Skytake popup main image" >
                <?php endif; ?>
            </div>

            <div class="skytake-box-two">

                <div class="skytake-view __request">

                    <div class="skytake-title">
                        <?php echo esc_html( $campaign->setting('title') ); ?>
                    </div>

                    <?php
                        if( is_premium() && file_exists(__DIR__ . '/urgency__premium_only.php') ){
                            require __DIR__ . '/urgency__premium_only.php';
                        }
                    ?>

                    <div class="skytake-message">
                        <?php echo esc_html( $campaign->setting('message') ); ?>
                    </div>
                    
                    <?php require __DIR__ . '/form.php'; ?>

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

</div>
