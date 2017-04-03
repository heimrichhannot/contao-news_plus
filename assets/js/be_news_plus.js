function CharacterCounter(field, options) {

    var parent = field,
        length = typeof field.value != 'undefined' ? field.value.length : 0;

    if (options.tinyMCE) {
        field = field.getElement();
        var span = document.createElement('span');
        span.innerHTML = field.value;
        length = (span.textContent || span.innerText).length; // do not count html tags
    }

    var info = field.parentNode.querySelector('.tl_tip'),
        maxLength = field.getAttribute('maxlength');

    if (maxLength < 1 || typeof info == 'undefined') return;

    var remaining = maxLength - length < 0 ? 0 : maxLength - length;

    info.innerHTML = '<span class="character-counter" style="font-weight: bold; color: #000;">[<span class="count">' + remaining + '</span> Zeichen verbleibend]</span> - ' + info.innerHTML;

    if (options.tinyMCE) {
        parent.on('keyup', function () {
            var span = document.createElement('span');
            span.innerHTML = this.getContent();
            length = (span.textContent || span.innerText).length; // do not count html tags
            remaining = maxLength - length < 0 ? 0 : maxLength - length;
            info.querySelector('.character-counter .count').innerHTML = remaining;
        });
    }
    else {
        field.addEventListener("keyup", function () {
            length = this.value.length;
            remaining = maxLength - length < 0 ? 0 : maxLength - length;
            info.querySelector('.character-counter .count').innerHTML = remaining;
        });
    }
}

(function () {

    var NewsPlus = {
        init: function () {
            this.registerCountCharacterFields();
        },
        registerCountCharacterFields: function () {

            var fields = document.querySelectorAll('[data-count-characters]');

            for (var i = 0, len = fields.length; i < len; i++) {
                var field = fields[i];

                // do not register tinymce textareas, they are handled within config/tinyMCE.php
                if (field.type == 'textarea' && field.classList.contains('noresize')) {
                    continue;
                }

                // do not attach again
                if (typeof field.counterCharacters != 'undefined') continue;

                var config = {};

                new CharacterCounter(field, config);

                field.countCharacters = true;
            }
        }
    };

    document.addEventListener("DOMContentLoaded", function (event) {
        NewsPlus.init();
    });

}).call(this);