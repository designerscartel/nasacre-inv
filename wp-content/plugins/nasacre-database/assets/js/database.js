document.addEventListener('DOMContentLoaded', function () {
    const results = document.querySelectorAll('.nasacre-database-results .result');

    const setOpen = function (result, isOpen) {
        const header = result.querySelector('.result-header');
        const details = result.querySelector('.result-body');

        if (!header || !details) return;

        result.classList.toggle('open', isOpen);
        header.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        details.hidden = !isOpen;
    };

    const toggle = function (result) {
        const isOpen = result.classList.contains('open');
        results.forEach(function (item) {
            setOpen(item, false);
        });

        if (!isOpen) setOpen(result, true);
    };

    results.forEach(function (result) {
        const header = result.querySelector('.result-header');
        const details = result.querySelector('.result-body');

        if (!header || !details) return;

        header.addEventListener('click', function () {
            toggle(result);
        });
        header.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') return;

            event.preventDefault();
            toggle(result);
        });
    });
});
