jQuery(document).ready(function ($) {

    let mediaFrame = null;

    $('#wsmp-upload-image').on(
        'click',
        function (event) {

            event.preventDefault();

            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            mediaFrame = wp.media({
                title: 'Select Popup Image',
                button: {
                    text: 'Use This Image'
                },
                multiple: false
            });

            mediaFrame.on(
                'select',
                function () {

                    const attachment =
                        mediaFrame.state()
                            .get('selection')
                            .first()
                            .toJSON();

                    $('#wsmp_image_id').val(
                        attachment.id
                    );

                    let imageUrl =
                        attachment.url;

                    if (
                        attachment.sizes &&
                        attachment.sizes.medium
                    ) {
                        imageUrl =
                            attachment
                                .sizes
                                .medium
                                .url;
                    }

                    $('#wsmp-image-preview').html(
                        '<img src="' +
                        imageUrl +
                        '" alt="">'
                    );

                }
            );

            mediaFrame.open();

        }
    );

    $('#wsmp-remove-image').on(
        'click',
        function () {

            $('#wsmp_image_id').val('');

            $('#wsmp-image-preview').html('');

        }
    );

});