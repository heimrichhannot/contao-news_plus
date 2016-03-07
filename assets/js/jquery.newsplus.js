(function ($) {

    var NewsPlus = {
        onReady: function () {
            //this.showNewsInModal();
            //this.closeNewsModal();
            this.changeUrl();
        },
        showNewsInModal: function () {
            $('body').on('click', '[data-news="modal"]', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $modal = $($this.data('target')),
                    $replace = $modal.find('.modal-dialog');

                // change history base
                if (!$modal.hasClass('in')) {
                    $modal.data('history-base-filtered', window.location);
                }

                $replace.load($this.attr('href'), function (responseText, textStatus, jqXHR) {
                    history.pushState(null, null, $this.attr('href'));
                });
            });
        },
        changeUrl: function () {
        },
        closeNewsModal: function () {
            $('.mod_newsreader_plus').on('hide.bs.modal', function (e) {

                var $this = $(this);

                // back
                if($this.data('history-base-filtered')){
                    window.history.go(-1);
                } else {
                    history.pushState(null, null, $this.data('history-base-filtered') ? $this.data('history-base-filtered') : $this.data('history-base'));
                }
            });
        }
    }


    $(document).ready(function () {
        NewsPlus.onReady()
    });


})(jQuery);

