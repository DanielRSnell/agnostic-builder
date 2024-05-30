// to enable the enqueue of this optional JS file, 
// you'll have to uncomment a row in the functions.php file
// just read the comments in there mate


document.addEventListener('DOMContentLoaded', function() {
    initializeFormSubmission();
});

function handleFormSubmission(event) {
        event.preventDefault();
        const form = event.target;
        const formId = form.getAttribute('data-form-id');
        const actionUrl = `/wp-json/contact-form-7/v1/contact-forms/${formId}/feedback`;
        const formData = new FormData(form);
        formData.append('_wpcf7_unit_tag', formId);

        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'mail_sent') {
                alert('Form submitted successfully');
            } else {
                alert('Form submission failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the form');
        });
    }

    const forms = document.querySelectorAll('form[data-form-id]');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmission);
    });

    function initializeFormSubmission() {
    const forms = document.querySelectorAll('form[data-form-id]');
    forms.forEach(form => {
        form.addEventListener('submit', handleFormSubmission);
    });
}