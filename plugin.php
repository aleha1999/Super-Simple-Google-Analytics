<?php 
/**
 * Plugin Name: Super Simple Google Analytics
 * Description: Google analytics integration, made easiest.
 */

function init() {
}

function ssga_add_activation_hook() {
    init();
}

register_activation_hook( __FILE__, "ssga_add_activation_hook" );

function ssga_add_options_page() {
    register_setting("ssga","ssga_gtag");
    add_options_page( "SSGA", "SSGA", "manage_options", "ssga",  "ssga_options_page_html");
    add_settings_section( "ssga_settings_main", "SSGA Options", "ssga_settings_main_cb", "ssga" );
    add_settings_field( "ssga_settings_ua", "SSGA Gtag", "ssga_gtag_cb", "ssga", "ssga_settings_main");

}

function ssga_settings_main_cb() {

}

function ssga_gtag_cb() {
    ?>
    <input type="text" name="ssga_gtag" placeholder="GTag" value="<?php echo get_option( "ssga_gtag", "" ); ?>">
    <?php
}

function ssga_settings_main() {
    
}

function ssga_options_page_html() {
    ?>
    <div class="wrap">
        <form action="options.php" method="post">
        <?php settings_fields("ssga"); ?>
            <?php do_settings_sections( "ssga" ); ?>
            <?php submit_button("Save setting");?>
        </form>
    </div>
    <?php
}

add_action("admin_menu","ssga_add_options_page");

function hook_header() {
    $option = get_option("ssga_gtag");
    if(!empty($option)):
    ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $option; ?>"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', '<?php echo $option; ?>');
        </script>
    <?php
    endif;
}

add_action("wp_head","hook_header");
?>