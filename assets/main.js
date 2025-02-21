jQuery(document).ready(function($) {
    var frame;

    // Abrir a biblioteca de mídia para selecionar várias imagens
    $('.flipper_upload_images').on('click', function(e) {
        e.preventDefault();
        
        if (frame) {
            frame.open();
            return;
        }
        
        frame = wp.media({
            title: 'Select Images',
            button: { text: 'Use Images' },
            multiple: true
        });

        frame.on('select', function() {
            var selection = frame.state().get('selection');
            var imageIDs = $('#flipper_images').val().split(',').filter(Boolean);

            selection.each(function(attachment) {
                imageIDs.push(attachment.id);
                $('#flipper_images_preview').append(
                    '<div class="flipper-image-item" data-id="'+attachment.id+'">' +
                        '<img src="'+attachment.url+'" style="max-width: 100px; height: auto;">' +
                        '<button type="button" class="button flipper_remove_image" data-id="'+attachment.id+'">✖</button>' +
                    '</div>'
                );
            });

            $('#flipper_images').val(imageIDs.join(','));
        });

        frame.open();
    });

    // Remover imagem individual
    $(document).on('click', '.flipper_remove_image', function() {
        var imageID = $(this).data('id');
        var imageIDs = $('#flipper_images').val().split(',').filter(Boolean);
        imageIDs = imageIDs.filter(id => id !== imageID.toString());

        $('#flipper_images').val(imageIDs.join(','));
        $(this).parent().remove();
    });
});