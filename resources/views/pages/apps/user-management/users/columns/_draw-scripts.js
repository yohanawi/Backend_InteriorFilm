// Initialize KTMenu
KTMenu.init();

// Add click event listener to delete buttons
document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
    element.addEventListener('click', function (e) {
        e.preventDefault();
        const userId = this.getAttribute('data-kt-user-id');

        Swal.fire({
            text: 'Are you sure you want to delete this user?',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'No, cancel',
            customClass: {
                confirmButton: 'btn fw-bold btn-danger',
                cancelButton: 'btn fw-bold btn-active-light-primary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/user-management/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                text: data.message,
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn fw-bold btn-primary',
                                }
                            }).then(() => {
                                LaravelDataTables['users-table'].ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                text: data.message,
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn fw-bold btn-primary',
                                }
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            text: 'Sorry, an error occurred. Please try again.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn fw-bold btn-primary',
                            }
                        });
                    });
            }
        });
    });
});
