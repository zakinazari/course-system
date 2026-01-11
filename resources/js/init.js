document.addEventListener('DOMContentLoaded', function () {
    if (typeof TemplateCustomizer === 'undefined') {
        console.error('TemplateCustomizer not found!');
        return;
    }

    window.templateCustomizer = new TemplateCustomizer({
        defaultShowDropdownOnHover: true,
        displayCustomizer: true,
        defaultStyle: 'light',
        lang: document.documentElement.lang || 'en',
        controls: {}
    });

    if (typeof window.templateCustomizer.init === 'function') {
        window.templateCustomizer.init();
    }

    window.templateName = 'frest';
});

