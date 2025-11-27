document.addEventListener('DOMContentLoaded', function () {
    // Portfolio functionality
    console.log('Portfolio manager loaded');

    // Add event listeners for edit/delete buttons if needed
    const editButtons = document.querySelectorAll('.btn-outline-light');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            // TODO: Implement edit functionality
            console.log('Edit clicked');
        });
    });

    const deleteButtons = document.querySelectorAll('.btn-outline-danger');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            // TODO: Implement delete functionality
            console.log('Delete clicked');
        });
    });
});
