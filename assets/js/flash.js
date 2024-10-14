document.addEventListener('DOMContentLoaded', function () {
    // Scroll to top if there are flash messages
    if (document.querySelector('.alert')) {
        window.scrollTo(0, 0);
    }

    document.querySelectorAll('.add-to-favorite').forEach(function (element) {
        element.addEventListener('click', function (event) {
            event.preventDefault();
            const trackId = this.getAttribute('data-favorite');
            const url = this.getAttribute('data-url');

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ trackId: trackId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});