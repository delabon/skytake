<?php 
    if( empty( $this->tabs )) return;

    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->tabs[0]['slug'];

    if( ! $this->find_tab( $current_tab ) ) return;
?>

<?php do_action( $this->slug . '_before_panel_render'); ?>

<form method="POST" novalidate>
    <div class="dlb_panel">

        <?php 
            require_once __DIR__ . '/menu.php';
            require_once $this->tabs_path.'/'.$current_tab.'.php';
        ?>

        <input type="hidden" name="dlb_nonce" value="<?php echo wp_create_nonce( $this->nonce ) ?>" >

    </div>
</form>

<?php do_action( $this->slug . '_after_panel_render'); ?>

<?php if( $this->discover_path ): ?>
    <div class="dlb_discover">  
        <h1><?php _e('You may need!', 'skytake'); ?></h1>
    
        <div class="dlb_discover_grid">  
            <?php include $this->discover_path; ?>
        </div>
    </div>
<?php endif; ?>

