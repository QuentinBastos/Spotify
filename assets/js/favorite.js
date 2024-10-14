$(document).ready(function() {
    $('a[data-favorite]').on('click', function(e) {
        e.preventDefault();
        const entityId = $(this).data('favorite');
        const url = $(this).data('url');
        const entityName = $(this).data('value');
        $.ajax({
            url: url,
            method: 'POST',
            data: { entityId: entityId, entityName: entityName },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    console.log(response.message);
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error(`Error favoriting track ID: ${entityId}`);
            }
        });
    });
});