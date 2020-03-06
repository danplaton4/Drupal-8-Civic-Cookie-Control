(function($) {
    $.fn.loadLicense = function(data) {
        $(this).attr('checked', 'checked');
    };

    $.fn.reloadApiKey = function(data) {
        console.log(data);
        console.log($(this));
        $(this).val(data);
    };


})(jQuery);