jQuery(document).ready(function($) {
    $('form').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();  // Собираем данные формы

        // Добавляем параметр action и nonce (если нужен)
        formData += '&action=save_lead_ajax'; // Указываем название action
        formData += '&nonce=' + ajax_object.nonce; // Если необходимо, добавьте nonce

        $.ajax({
            url: ajax_object.ajax_url, // URL для admin-ajax.php
            type: 'POST',              // Метод POST
            data: formData,            // Данные формы + action
            dataType: 'json',          // Ожидаемый формат данных
            success: function(response) {
                if (response.success) {
                    $('#pop-up-success').addClass('active');

                    setTimeout(function() {
                    $('#pop-up-success').removeClass('active');
                    }, 3000); // 3000 миллисекунд = 3 секунды

                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX ошибка:', textStatus, errorThrown);
            }
        });
    });
});
