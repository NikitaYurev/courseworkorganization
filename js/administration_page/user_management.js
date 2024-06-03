let oldUsername = '';
let oldEmail = '';

document.addEventListener('DOMContentLoaded', function() {
    const userRows = document.querySelectorAll('.user-list-container tr[data-user-id]');
    userRows.forEach(row => {
        row.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const username = this.children[0].textContent;
            const email = this.children[1].textContent;
            const role = this.getAttribute('data-role'); // Get role from data attribute

            document.getElementById('id').value = userId;
            document.getElementById('username').value = username;
            document.getElementById('email').value = email;
            document.getElementById('role').value = role; // Set role value in hidden field

            oldUsername = username;
            oldEmail = email;
            document.getElementById('old_username').value = oldUsername;
            document.getElementById('old_email').value = oldEmail;

            // Check if the user is an admin
            if (role.trim() === 'admin') {
                // Set input fields as readonly and disable the update button
                document.getElementById('username').readOnly = true;
                document.getElementById('email').readOnly = true;
                document.querySelector('button[type="submit"]').disabled = true;
                document.querySelector('button[type="submit"]').classList.add('disabled');
            } else {
                // Set input fields as editable and enable the update button
                document.getElementById('username').readOnly = false;
                document.getElementById('email').readOnly = false;
                document.querySelector('button[type="submit"]').disabled = false;
                document.querySelector('button[type="submit"]').classList.remove('disabled');
            }
        });
    });

    document.getElementById('username').addEventListener('change', function() {
        const currentUsername = this.value;
        if (oldUsername !== currentUsername) {
            document.getElementById('old_username').value = oldUsername;
        }
    });

    document.getElementById('email').addEventListener('change', function() {
        const currentEmail = this.value;
        if (oldEmail !== currentEmail) {
            document.getElementById('old_email').value = oldEmail;
        }
    });

    document.getElementById('user-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        const userId = document.getElementById('id').value;
        const currentUsername = document.getElementById('username').value;
        const currentEmail = document.getElementById('email').value;

        if (userId === '') {
            alert('User ID not found');
            return;
        }

        if (oldUsername !== currentUsername || oldEmail !== currentEmail) {
            // Username or email has changed, send AJAX request to update_user.php
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_user.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        if (xhr.responseText.includes("User updated successfully")) {
                            alert('User updated successfully!');
                            // Update the table row with new username and email
                            const selectedRow = document.querySelector(`tr[data-user-id='${userId}']`);
                            if (selectedRow) {
                                selectedRow.children[0].textContent = currentUsername;
                                selectedRow.children[1].textContent = currentEmail;
                            }
                            // Clear the form fields
                            document.getElementById('id').value = '';
                            document.getElementById('username').value = '';
                            document.getElementById('email').value = '';
                            oldUsername = '';
                            oldEmail = '';
                            document.getElementById('old_username').value = '';
                            document.getElementById('old_email').value = '';
                        } else {
                            alert('Error updating user: ' + xhr.responseText);
                        }
                    } else {
                        alert('Error updating user: ' + xhr.responseText);
                    }
                }
            };

            const params = `id=${userId}&username=${currentUsername}&email=${currentEmail}&old_username=${oldUsername}&old_email=${oldEmail}`;
            xhr.send(params);
        } else {
            alert('No changes detected in username or email');
        }
    });
});
