<?php

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode(
    'wc_modal_popup',
    'wsmp_render_shortcode_popup'
);

function wsmp_render_shortcode_popup($atts = array())
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'wsmp_popup';

    $popup = $wpdb->get_row(
        "SELECT * FROM {$table_name}
        WHERE status = 1
        ORDER BY id ASC
        LIMIT 1"
    );

    if (!$popup) {
        return '';
    }

    wp_enqueue_style(
        'wsmp-popup-css',
        WSMP_URL . 'assets/css/popup.css',
        array(),
        WSMP_VERSION
    );

    wp_enqueue_script(
        'wsmp-popup-js',
        WSMP_URL . 'assets/js/popup.js',
        array(),
        WSMP_VERSION,
        true
    );

    $image_url = '';

    if ($popup->image_id) {

        $image_url = wp_get_attachment_image_url(
            $popup->image_id,
            'large'
        );
    }

    $popup_id = 'wsmp-modal-' . absint($popup->id);

    ob_start();

    ?>

    <div
        id="<?php echo esc_attr($popup_id); ?>"
        class="wsmp-overlay"
        aria-hidden="true"
    >

        <div
            class="wsmp-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="<?php echo esc_attr(
                $popup_id
            ); ?>-title"
        >

            <button
                type="button"
                class="wsmp-close"
                aria-label="Close popup"
            >
                &times;
            </button>

            <?php if ($image_url): ?>

                <div class="wsmp-image">

                    <img
                        src="<?php echo esc_url($image_url); ?>"
                        alt="<?php echo esc_attr(
                            $popup->title
                        ); ?>"
                    >

                </div>

            <?php endif; ?>

            <div class="wsmp-content">

                <?php if ($popup->title): ?>

                    <h2
                        id="<?php echo esc_attr(
                            $popup_id
                        ); ?>-title"
                    >
                        <?php echo esc_html(
                            $popup->title
                        ); ?>
                    </h2>

                <?php endif; ?>

                <?php if ($popup->content): ?>

                    <div class="wsmp-text">

                        <?php
                        echo wp_kses_post(
                            wpautop(
                                $popup->content
                            )
                        );
                        ?>

                    </div>

                <?php endif; ?>

                <?php if (
                    $popup->button_text &&
                    $popup->button_url
                ): ?>

                    <div class="wsmp-button-wrapper">

                        <a
                            href="<?php echo esc_url(
                                $popup->button_url
                            ); ?>"
                            class="wsmp-button"
                            <?php if (
                                $popup->open_new_tab
                            ): ?>
                                target="_blank"
                                rel="noopener noreferrer"
                            <?php endif; ?>
                        >
                            <?php echo esc_html(
                                $popup->button_text
                            ); ?>
                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <?php

    return ob_get_clean();
}