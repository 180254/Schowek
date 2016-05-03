(function () {
    'use strict';

    $(document).ready(function () {
        $('#files').dataTable({
            'paging': false,
            'info': false,
            'order': [[1, 'desc']]
        });


        $('form[name="delete"]').submit(function () {
            if ($('form[name="delete"] input:checkbox:checked').length === 0) {
                window.alert('Błąd! Proszę wybrać jakieś pliki do usunięcia.');
                return false;
            }
            askAboutPassword($(this));
            return true;
        });

        $('form[name="add"]').submit(function () {
            if ($('form[name="add"] input[type="file"]').val() === '') {
                window.alert('Błąd! Proszę wskazać plik do wysłania.');
                return false;
            }
            askAboutPassword($(this));
            return true;
        });

        $('.dl').click(function () {
            var $form = createDownloadForm($(this).attr('href'));
            askAboutPassword($form);
            $form.appendTo('body'); // necessary. only chrome can submit in-memory form.
            $form.submit();
            $('body form[name="download"]').remove(); // appended, submitted, so remove now.
            return false;
        });
    });

    var askAboutPassword = function ($form) {
        var password = window.prompt('Operacja wymaga hasła. Proszę podać hasło: ');

        $('<input>', {
            'type': 'hidden',
            'name': 'password',
            'value': password
        }).appendTo($form);
    };

    var createDownloadForm = function ($action) {
        return $('<form>', {
            'method': 'post',
            'name': 'download',
            'target': '_blank',
            'action': $action
        });
    };

})();