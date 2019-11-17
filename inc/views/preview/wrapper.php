<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $wp_version;

$body_classes = [
	'skytake-preview-active',
	'wp-version-' . str_replace( '.', '-', $wp_version ),
];

if ( is_rtl() ) {
	$body_classes[] = 'rtl';
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
	
    <title><?php echo __( 'Skytake Preview', 'skytake' ) ?></title>

    <?php wp_head(); ?>

    <script>
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
	</script>
</head>

<body class="<?php echo implode( ' ', $body_classes ); ?>">
	
	<div id="skytake_preview">
		<?php echo $output; ?>
	</div>

<?php
	wp_footer();
	/** This action is documented in wp-admin/admin-footer.php */
	do_action( 'admin_print_footer_scripts' );
?>

</body>
</html>
