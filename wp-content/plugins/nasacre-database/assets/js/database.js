document.addEventListener('DOMContentLoaded', function () {
    const results = document.querySelectorAll('.nasacre-database-results .result');

    results.forEach(result => {
        const header = result.querySelector('.result-header');
        const details = result.querySelector('.result-body');

        if (!header || !details) return;

        header.addEventListener('click', () => {
            const isOpen = result.classList.contains('open');

            // Optional: close all other results (accordion behaviour)
            results.forEach(r => r.classList.remove('open'));

            // Toggle current one
            if (!isOpen) {
                result.classList.add('open');
            }
        });
    });
});