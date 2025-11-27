document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.registration-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const button = form.querySelector('button[type="submit"]');
            button.textContent = 'Processing...';
            button.disabled = true;

            // Add animation to simulate loading (optional)
            setTimeout(function () {
                form.classList.add('submitted');
            }, 1000);
        });
    });
});
