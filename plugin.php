<?php 
/**
 * Plugin Name: Super Simple Google Analytics
 * Description: Google adsense and analytics integration, made easiest.
 */

function init() {
}

function ssga_add_activation_hook() {
    init();
}

register_activation_hook( __FILE__, "ssga_add_activation_hook" );

function ssga_add_options_page() {
    register_setting("ssga","ssga_gtag");
    register_setting("ssga","ssga_adtag");
    register_setting("ssga","ssga_admessage");
    add_options_page( "SSGA", "SSGA", "manage_options", "ssga",  "ssga_options_page_html");
    add_settings_section("ssga_settings_main", "SSGA Options", "ssga_settings_main_cb", "ssga" );
    add_settings_field("ssga_settings_ua", "Gtag", "ssga_gtag_cb", "ssga", "ssga_settings_main");
    add_settings_field("ssga_settings_ga", "Adsense code", "ssga_adtag_cb","ssga","ssga_settings_main");
    add_settings_field("ssga_settings_admessage","Ad message","ssga_admessage_cb","ssga","ssga_settings_main");
}

function ssga_settings_main_cb() {

}

function ssga_admessage_cb() {
    ?>
        <textarea name="ssga_admessage" cols="30" rows="10" placeholder="Ad message" ><?php echo get_option("ssga_admessage",""); ?></textarea>
    <?php
}

function ssga_gtag_cb() {
    ?>
    <input type="text" name="ssga_gtag" placeholder="GTag" value="<?php echo get_option( "ssga_gtag", "" ); ?>">
    <?php
}

function ssga_adtag_cb() {
    ?>
    <input type="text" name="ssga_adtag" placeholder="Ad tag" value="<?php echo get_option("ssga_adtag", ""); ?>">
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
        <?php
        if(!isset($_COOKIE["cookie_opt_in"])):
        ?>
        <?php endif; ?>
        function gtag(){dataLayer.push(arguments);}
        gtag('set', 'anonymizeIp', true);
        gtag('set', 'allowAdFeatures', <?php echo isset($_COOKIE["cookie_opt_in"]) ? "true" : "false"; ?>);
        gtag('js', new Date());
        gtag('config', '<?php echo $option; ?>');
        </script>
    <?php
    endif;
    $option = get_option("ssga_adtag");
    if(!empty($option)):
    ?>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "<?php echo $option; ?>",
            enable_page_level_ads: true
        });
        (adsbygoogle=window.adsbygoogle||[]).requestNonPersonalizedAds=<?php echo isset($_COOKIE["cookie_opt_in"]) ? 0 : 1; ?>
    </script>
    <?php
    endif;
}

function hook_footer() {
    $text = get_option("ssga_admessage");
    if(empty ($text))
        $text = "This site relies on cookies to serve you the full experience. Please agree to the use of cookies on this site";
    if(!isset($_COOKIE["cookie_opt_in"])):
    ?>
        <form class="cookie_opt_in">
            <p><?php echo $text; ?></p>
            <input type="hidden" name="accept_cookie" value="true">
            <button>Aksepter</button>
        </form>
    <?php
    endif;
}

function load_css() {
    wp_enqueue_style("style",plugins_url("style.css",__FILE__));
}

function check_cookie() {
    if(isset($_GET["accept_cookie"]) && $_GET["accept_cookie"] == "true") {
        echo "HELLO";
        setcookie("cookie_opt_in","true",time()+60*60*30*6,"/");
        header("location: ".$_SERVER["PHP_SELF"]);
        exit();
    }
}

add_action("wp_head","hook_header");
add_action("wp_footer","hook_footer");
add_action("wp_enqueue_scripts","load_css");
add_action("send_headers","check_cookie");
?>