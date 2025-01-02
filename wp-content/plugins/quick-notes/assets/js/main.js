$(document).ready(function (){
    console.log("Size converter activated");
    $('#size-converter-search-form').submit(function (e){
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'size_converter_action',
                security: sizeConverterNonce,
                action_type: 'search_pair',
                original_size: $('#search-original').val()
            },
            success: function (response) {
                if(response.success === true){
                    $('#search-result span.wbzx-search-result__text').text(response.data);
                }else{
                    $('#search-result span.wbzx-search-result__text').text("Error request");
                }

                $('#size-converter-search-form')[0].reset();
            },
            error: function () {
                alert('Error request.');
                $('#size-converter-search-form')[0].reset();
            }
        });
    });
});