(function () {
    'use strict';
    var root = document.documentElement;
    var key = 'elasticsearch-monitoring-theme';
    var allowed = ['light', 'dark', 'system'];
    var preference = 'system';
    try {
        var stored = window.localStorage.getItem(key);
        if (allowed.indexOf(stored) !== -1) preference = stored;
    } catch (error) {}
    function systemTheme() {
        return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    function applyTheme() {
        var theme = preference === 'system' ? systemTheme() : preference;
        root.setAttribute('data-theme', theme);
        root.setAttribute('data-bs-theme', theme);
        root.style.colorScheme = theme;
    }
    function mountThemePicker() {
        applyTheme();
        if (document.getElementById('elasticsearch-monitoring-theme-picker')) return;
        var wrapper = document.createElement('label');
        wrapper.id = 'elasticsearch-monitoring-theme-picker';
        wrapper.innerHTML = '<span aria-hidden="true">◐</span><select aria-label="Theme"><option value="light">Light</option><option value="dark">Dark</option><option value="system">System</option></select>';
        var select = wrapper.querySelector('select');
        select.value = preference;
        select.addEventListener('change', function () {
            preference = select.value;
            try { window.localStorage.setItem(key, preference); } catch (error) {}
            applyTheme();
        });
        document.body.appendChild(wrapper);
    }
    document.addEventListener('change', function (event) {
        var select = event.target;
        if (!select || select.name !== 'locale' || !select.hasAttribute('data-em-locale-picker')) return;
        var secure = window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = 'elasticsearch_monitoring_locale=' + encodeURIComponent(select.value) + '; path=/; max-age=31536000; SameSite=Lax' + secure;
        if (select.form) select.form.submit();
    });
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', mountThemePicker, { once: true });
    } else {
        mountThemePicker();
    }
    if (window.matchMedia) {
        var media = window.matchMedia('(prefers-color-scheme: dark)');
        var onSystemThemeChange = function () { if (preference === 'system') applyTheme(); };
        if (media.addEventListener) media.addEventListener('change', onSystemThemeChange);
        else if (media.addListener) media.addListener(onSystemThemeChange);
    }
})();
