<?php

namespace WBZXTDL\App\Core\Shortcode;

class Shortcode
{
    public static function init() {
        add_shortcode('size_converter_search', [self::class, 'renderSearchForm']);
    }

    public static function renderSearchForm(): string {
        // Nonce AJAX
        $nonce = wp_create_nonce('size_converter_search_nonce');

        ob_start(); ?>
        <div id="size-converter-search">
            <form id="size-converter-search-form" method="POST">
                <input type="text" id="search-original" name="original_size" placeholder="Enter size (a-z)" pattern="[a-zA-Z]+" required>
                <button type="submit">Search</button>
            </form>
            <div id="search-result" class="wbzx-search-result">
                <span class="wbzx-search-result__text">You result is here</span>
            </div>
        </div>
        <script>
            const ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            const sizeConverterNonce = "<?php echo $nonce; ?>";
        </script>
        <?php
        return ob_get_clean();
    }
}