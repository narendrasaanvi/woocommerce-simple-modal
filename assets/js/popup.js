document.addEventListener('DOMContentLoaded', function () {

    const popups = document.querySelectorAll('.wsmp-overlay');

    if (!popups.length) {
        return;
    }

    popups.forEach(function (popup) {

        const closeButton =
            popup.querySelector('.wsmp-close');

        function openPopup() {

            popup.classList.add('wsmp-open');

            popup.setAttribute(
                'aria-hidden',
                'false'
            );

            document.body.classList.add(
                'wsmp-popup-open'
            );

            setTimeout(function () {

                if (closeButton) {
                    closeButton.focus();
                }

            }, 100);

        }

        function closePopup() {

            popup.classList.remove('wsmp-open');

            popup.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.classList.remove(
                'wsmp-popup-open'
            );

        }

        if (closeButton) {

            closeButton.addEventListener(
                'click',
                function () {
                    closePopup();
                }
            );

        }

        popup.addEventListener(
            'click',
            function (event) {

                if (event.target === popup) {
                    closePopup();
                }

            }
        );

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    popup.classList.contains(
                        'wsmp-open'
                    )
                ) {
                    closePopup();
                }

            }
        );

        window.wsmpOpenPopup = openPopup;
        window.wsmpClosePopup = closePopup;

    });

});