(function ($) {

    var NewsPlus = {
        onReady: function () {
            this.showNewsInModal();
        },
        showNewsInModal: function () {
            $('body').on('click', '[data-news="modal"]', function (e) {
                e.preventDefault();
                var $modal = $($(this).data('target'));

                // change history base
                if (!$modal.hasClass('in')) {
                    $modal.data('history-base-filtered', window.location.href);
                }
            });
        }
    }


    $(document).ready(function () {
        NewsPlus.onReady()
    });


})(jQuery);
