<?php

use Delabon\WP\HTML;

use function SKYT\is_premium;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_version;

$body_classes = [
	'skytake-editor-active',
	'wp-version-' . str_replace( '.', '-', $wp_version ),
];

if ( is_rtl() ) {
	$body_classes[] = 'rtl';
}

if ( is_premium() ) {
	$body_classes[] = 'premium';
}
else{
	$body_classes[] = 'free';
}

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<meta http-equiv="Pragma" content="no-cache" />
	<meta http-equiv="Expires" content="0" />
	
    <title><?php echo __( 'Skytake Editor', 'skytake' ) ?></title>

    <?php wp_head(); ?>

    <script>
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
	</script>
</head>

<body class="<?php echo implode( ' ', $body_classes ); ?>">
	
	<div id="skytake_editor" 
		data-template="<?php echo $campaign->setting('template'); ?>" 
		data-type="<?php echo $campaign->type(); ?>"
	>

		<div id="skt_editor_bar" data-level="1">
		
			<input name="skytake_nonce" type="hidden" value="<?php echo wp_create_nonce('skytake'); ?>" >
			<input name="skytake_campaign_id" type="hidden" value="<?php echo $campaign->id(); ?>" >
			<input name="skytake_campaign_type" type="hidden" value="<?php echo $campaign->type(); ?>" >
	
			<header>	
				<h1 data-title="<?php echo $campaign->name() ?>">
					<?php echo __('Campaign', 'skytake') .'<span class="skt__name">' . $campaign->name() . '</span><span>('.skytake()->defaultConfig['campaign_types'][$campaign->type()].')</span>'; ?>
				</h1>
			</header>

			<div class="skt__controls">

				<?php if( in_array( $campaign->type(), ['popup', 'floating-bar', 'scroll-box'] ) ){
					echo HTML::checkbox([
						'value' => $campaign->status(),
						'name' => 'campaign_status',
						'wrapper' => false,
						'text' => __('Live?', 'skytake'),
					]); 
				} ?>

				<button class="skt__btn_save button" disabled="disabled" type="submit" ><?php _e('Save', 'skytake') ?></button>
				<a class="skt__btn_exit" href="<?php echo admin_url('admin.php?page=skytake'); ?>">&times;</a>
			</div>
			
		</div>
		
		<?php $panel->render_panel(); ?>

		<div id="skytake_editor_iframe">
			<iframe src="<?php echo admin_url('admin.php?action=skytake_preview&campaign_id='.$campaign->id() ); ?>"></iframe>
		</div>

	</div>

	<div id="skytake_loader">
		<div class="__loader">
            <div></div>
        </div>
	</div>

<?php
	wp_footer();
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
?>

</body>
</html>
