$(document).ready(function() {
    $('a[data-favorite]').on('click', function(e) {
        e.preventDefault();
        const trackId = $(this).data('favorite');
        const url = $(this).data('url');

        $.ajax({
            url: url,
            method: 'POST',
            data: { trackId: trackId },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error(`Error favoriting track ID: ${trackId}`);
            }
        });
    });
});