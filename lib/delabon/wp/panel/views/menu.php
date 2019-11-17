<ul class="dlb_panel_menu">
    <?php 
        foreach ( $this->tabs as $tab ) {

            $class = $current_tab === $tab['slug'] ? '__active' : '';

            ?>
                <li class="<?php echo $class; ?>">
                    <a href="<?php echo $this->panel_url . '&tab=' .$tab['slug']; ?>"><?php echo $tab['title']; ?></a>
                </li>
            <?php 
        }
    ?>
</ul>
