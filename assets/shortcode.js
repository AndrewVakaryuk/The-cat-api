jQuery(document).ready(function ($) {
    $('#the_cat_breeds').on('change', function (e) {
        var self = $(this);
        var breed = $('#av-the-cat-api-breed');
        console.log(self.val());

        breed.empty();
        breed.addClass('av-the-cat-api-breed-loader');

        $.post(thecat_ajaxurl, {
            action: 'av-thecatapi-get-breed',
            breed_id: self.val(),
            nonce: thecat_nonce
        }, function (response) {
            if (response && response.success) {
                console.log(response.data);

                avImgElement = document.createElement('img');
                avDescElement = document.createElement('p');
                avImgElement.src = response.data.image;
                avDescElement.innerText = response.data.description;
                breed.removeClass('av-the-cat-api-breed-loader');
                breed.append(avImgElement);
                breed.append(avDescElement);
            }
        });
    })

    $('#the_cat_breeds').trigger('change');
});