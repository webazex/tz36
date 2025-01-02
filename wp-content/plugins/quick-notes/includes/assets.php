<?php
//plugin adminpage css
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'toplevel_page_wbzx-tdl') {
        if(is_rtl()){
            wp_enqueue_style(
                'wbzx-tdl-bootstrap-min-rtl-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap.rtl.min.css',
                [],
                '1.0.0'
            );

            wp_enqueue_style(
                'wbzx-tdl-bootstrap-grid-rtl-min-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap-grid.rtl.min.css',
                ['wbzx-tdl-bootstrap-min-rtl-admin'],
                '1.0.0'
            );
            wp_enqueue_style(
                'wbzx-tdl-admin',
                WBZX_TDL_URL . 'assets/css/admin.css',
                ['wbzx-tdl-bootstrap-min-rtl-admin', 'wbzx-tdl-bootstrap-grid-rtl-min-admin'],
                '1.0.0'
            );
        }else{
            wp_enqueue_style(
                'wbzx-tdl-bootstrap-min-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap.min.css',
                [],
                '1.0.0'
            );

            wp_enqueue_style(
                'wbzx-tdl-bootstrap-grid-min-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap-grid.min.css',
                ['wbzx-tdl-bootstrap-min-admin'],
                '1.0.0'
            );
            wp_enqueue_style(
                'wbzx-tdl-admin',
                WBZX_TDL_URL . 'assets/css/admin.css',
                ['wbzx-tdl-bootstrap-min-admin', 'wbzx-tdl-bootstrap-grid-min-admin'],
                '1.0.0'
            );
        }
	    wp_enqueue_editor();

    }
});

//plugin adminpage js
add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'toplevel_page_wbzx-tdl') {
        wp_enqueue_script('wbzx-tdl-admin', WBZX_TDL_URL . 'assets/js/admin.js', ['jquery'], '1.0', true);
        wp_localize_script('wbzx-tdl-admin', 'wbzx_tdl_nonce', wp_create_nonce('wbzx-tdl_nonce'));
    }
});
//frontend css and js
function size_converter_enqueue_scripts() {
    // add CSS
        if(is_rtl()){
            wp_enqueue_style(
                'wbzx-tdl-bootstrap-min-rtl-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap.rtl.min.css',
                [],
                '1.0.0'
            );

            wp_enqueue_style(
                'wbzx-tdl-bootstrap-grid-rtl-min-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap-grid.rtl.min.css',
                ['wbzx-tdl-bootstrap-min-rtl-admin'],
                '1.0.0'
            );
            wp_enqueue_style(
                'wbzx-tdl-admin',
                WBZX_TDL_URL . 'assets/css/admin.css',
                ['wbzx-tdl-bootstrap-min-rtl-admin', 'wbzx-tdl-bootstrap-grid-rtl-min-admin'],
                '1.0.0'
            );
        }else{
            wp_enqueue_style(
                'wbzx-tdl-bootstrap-min-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap.min.css',
                [],
                '1.0.0'
            );

            wp_enqueue_style(
                'wbzx-tdl-bootstrap-grid-min-admin',
                WBZX_TDL_URL . 'assets/css/bootstrap-grid.min.css',
                ['wbzx-tdl-bootstrap-min-admin'],
                '1.0.0'
            );
            wp_enqueue_style(
                'wbzx-tdl-admin',
                WBZX_TDL_URL . 'assets/css/admin.css',
                ['wbzx-tdl-bootstrap-min-admin', 'wbzx-tdl-bootstrap-grid-min-admin'],
                '1.0.0'
            );
        }

    // add JS
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', WBZX_TDL_URL . 'assets/js/jQuery.js', false, null, ['in_footer' => true] );
    wp_enqueue_script(
        'size-converter-script',
        WBZX_TDL_URL . 'assets/js/main.js',
        ['jquery'],
        '1.0.0',
        ['in_footer' => true] //in footer
    );

    // Передача переменных в JavaScript
    wp_localize_script('size-converter-script', 'sizeConverterData', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('size_converter_search_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'size_converter_enqueue_scripts');