<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'wsmp_admin_menu');
add_action('admin_enqueue_scripts', 'wsmp_admin_assets');

function wsmp_admin_menu()
{
    add_menu_page(
        'Modal Popup',
        'Modal Popup',
        'manage_options',
        'wsmp-modal-popup',
        'wsmp_admin_page',
        'dashicons-format-image',
        26
    );
}

function wsmp_admin_assets($hook)
{
    if ($hook !== 'toplevel_page_wsmp-modal-popup') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'wsmp-admin-js',
        WSMP_URL . 'assets/js/admin.js',
        array('jquery'),
        WSMP_VERSION,
        true
    );

    wp_enqueue_style(
        'wsmp-admin-css',
        WSMP_URL . 'assets/css/admin.css',
        array(),
        WSMP_VERSION
    );
}

function wsmp_admin_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;

    $table_name = $wpdb->prefix . 'wsmp_popup';

    $popup = $wpdb->get_row(
        "SELECT * FROM {$table_name} ORDER BY id ASC LIMIT 1"
    );

    if (!$popup) {

        $wpdb->insert(
            $table_name,
            array(
                'title' => '',
                'image_id' => 0,
                'content' => '',
                'button_text' => '',
                'button_url' => '',
                'open_new_tab' => 0,
                'status' => 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            )
        );

        $popup = $wpdb->get_row(
            "SELECT * FROM {$table_name} ORDER BY id ASC LIMIT 1"
        );
    }

    if (isset($_POST['wsmp_save_popup'])) {

        check_admin_referer('wsmp_save_popup');

        $title = sanitize_text_field(
            $_POST['title'] ?? ''
        );

        $image_id = absint(
            $_POST['image_id'] ?? 0
        );

        $content = wp_kses_post(
            $_POST['content'] ?? ''
        );

        $button_text = sanitize_text_field(
            $_POST['button_text'] ?? ''
        );

        $button_url = esc_url_raw(
            $_POST['button_url'] ?? ''
        );

        $open_new_tab = isset(
            $_POST['open_new_tab']
        ) ? 1 : 0;

        $status = isset(
            $_POST['status']
        ) ? 1 : 0;

        $wpdb->update(
            $table_name,
            array(
                'title' => $title,
                'image_id' => $image_id,
                'content' => $content,
                'button_text' => $button_text,
                'button_url' => $button_url,
                'open_new_tab' => $open_new_tab,
                'status' => $status,
                'updated_at' => current_time('mysql'),
            ),
            array(
                'id' => $popup->id,
            ),
            array(
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
            ),
            array(
                '%d',
            )
        );

        $popup = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE id = %d",
                $popup->id
            )
        );

        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>Modal popup settings saved successfully.</p>';
        echo '</div>';
    }

    ?>

    <div class="wrap wsmp-admin">

        <h1>WooCommerce Modal Popup</h1>

        <p class="wsmp-description">
            Manage your single website modal popup.
        </p>

        <div class="wsmp-card">

            <form method="post">

                <?php wp_nonce_field('wsmp_save_popup'); ?>

                <table class="form-table">

                    <tr>

                        <th>
                            <label for="title">
                                Popup Title
                            </label>
                        </th>

                        <td>

                            <input
                                type="text"
                                id="title"
                                name="title"
                                class="regular-text"
                                value="<?php echo esc_attr(
                                    $popup->title
                                ); ?>"
                                placeholder="Special Offer"
                            >

                        </td>

                    </tr>

                    <tr>

                        <th>
                            <label>
                                Popup Image
                            </label>
                        </th>

                        <td>

                            <input
                                type="hidden"
                                name="image_id"
                                id="wsmp_image_id"
                                value="<?php echo esc_attr(
                                    $popup->image_id
                                ); ?>"
                            >

                            <div
                                id="wsmp-image-preview"
                                class="wsmp-image-preview"
                            >

                                <?php

                                if ($popup->image_id) {

                                    echo wp_get_attachment_image(
                                        $popup->image_id,
                                        'medium'
                                    );
                                }

                                ?>

                            </div>

                            <button
                                type="button"
                                class="button button-primary"
                                id="wsmp-upload-image"
                            >
                                Upload / Select Image
                            </button>

                            <button
                                type="button"
                                class="button"
                                id="wsmp-remove-image"
                            >
                                Remove Image
                            </button>

                        </td>

                    </tr>

                    <tr>

                        <th>
                            <label>
                                Content
                            </label>
                        </th>

                        <td>

                            <?php

                            wp_editor(
                                $popup->content,
                                'wsmp_content',
                                array(
                                    'textarea_name' => 'content',
                                    'textarea_rows' => 8,
                                    'media_buttons' => true,
                                    'teeny' => false,
                                )
                            );

                            ?>

                        </td>

                    </tr>

                    <tr>

                        <th>
                            <label for="button_text">
                                Button Text
                            </label>
                        </th>

                        <td>

                            <input
                                type="text"
                                id="button_text"
                                name="button_text"
                                class="regular-text"
                                value="<?php echo esc_attr(
                                    $popup->button_text
                                ); ?>"
                                placeholder="Shop Now"
                            >

                        </td>

                    </tr>

                    <tr>

                        <th>
                            <label for="button_url">
                                Button URL
                            </label>
                        </th>

                        <td>

                            <input
                                type="url"
                                id="button_url"
                                name="button_url"
                                class="large-text"
                                value="<?php echo esc_attr(
                                    $popup->button_url
                                ); ?>"
                                placeholder="https://example.com/shop/"
                            >

                        </td>

                    </tr>

                    <tr>

                        <th>
                            <label>
                                Button Target
                            </label>
                        </th>

                        <td>

                            <label>

                                <input
                                    type="checkbox"
                                    name="open_new_tab"
                                    value="1"
                                    <?php checked(
                                        $popup->open_new_tab,
                                        1
                                    ); ?>
                                >

                                Open button link in new tab

                            </label>

                        </td>

                    </tr>

                    <tr>

                        <th>
                            <label>
                                Popup Status
                            </label>
                        </th>

                        <td>

                            <label class="wsmp-status-toggle">

                                <input
                                    type="checkbox"
                                    name="status"
                                    value="1"
                                    <?php checked(
                                        $popup->status,
                                        1
                                    ); ?>
                                >

                                <span>
                                    Active
                                </span>

                            </label>

                            <p class="description">
                                Active = shortcode displays the popup.
                                Deactive = shortcode displays nothing.
                            </p>

                        </td>

                    </tr>

                </table>

                <p>

                    <button
                        type="submit"
                        name="wsmp_save_popup"
                        class="button button-primary button-large"
                    >
                        Save Popup
                    </button>

                </p>

            </form>

        </div>

        <div class="wsmp-shortcode-card">

            <h2>Elementor Free Shortcode</h2>

            <p>
                Add Elementor's <strong>Shortcode</strong> widget
                and paste:
            </p>

            <code>[wc_modal_popup]</code>

            <p>
                The popup will only render when its status is
                <strong>Active</strong>.
            </p>

        </div>

        <div class="wsmp-info-card">

            <h2>How it works</h2>

            <ol>

                <li>
                    Upload your popup image.
                </li>

                <li>
                    Add title and content.
                </li>

                <li>
                    Add button and URL if required.
                </li>

                <li>
                    Set status to Active.
                </li>

                <li>
                    Copy
                    <code>[wc_modal_popup]</code>
                    into Elementor Free.
                </li>

                <li>
                    Update your page.
                </li>

            </ol>

        </div>

    </div>

    <?php
}