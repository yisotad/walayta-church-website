
document.addEventListener('DOMContentLoaded', function () {
    const deleteForms = document.querySelectorAll('.confirm-delete');

    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const confirmed = window.confirm(
                'Are you sure you want to delete this event?'
            );

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});
