(function ($) {

    var NewsPlus = {
        onReady: function () {
            this.showNewsInModal();
            this.initInfiniteScroll();
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
        },
        initInfiniteScroll: function(){
            var arrInfiniteElem = $(document).find('[class^=jscroll_element]'),
                loadHtml = "<div class='loading'><div class='inside'><div class='spinner'><div class='rect1'></div><div class='rect2'></div><div class='rect3'></div><div class='rect4'></div><div class='rect5'></div></div>Daten werden geladen.</div></div>";

            $.each(arrInfiniteElem, function(){
                var infiniteElementSelector = $(this).attr('class'),
                    autoTrigger = $(this).attr('data-autotrigger'),
                    html = '<script>';
                    html += '$(".'+infiniteElementSelector+'").jscroll({'
                         +  'debug: false,'
                         +  'loadingHtml: "'+loadHtml+'",'
                         +  'nextSelector: ".pagination a.next",'
                         +  'autoTrigger: '+autoTrigger+','
                         +  'contentSelector: ".'+infiniteElementSelector+'"});';
                    html += '</script>';
                $(this).prepend(html);
            });

        }
    };
    $(document).ready(function () {
        NewsPlus.onReady();
    });


})(jQuery);

