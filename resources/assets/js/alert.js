(function ($) {
    $(document).ready(function () {
        setTimeout(function () {
            $('.alert .close').not('.alert.alert-danger .close').trigger('click');
        }, 1000);
    });
} ($ = window.$ || window.jQuery || {}));
