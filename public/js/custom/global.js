jQuery.noConflict();

jQuery(document).ready(function () {

    jQuery(window).load(function () {
        jQuery("#loader, .loader").hide();
    });

    /* GLOBAL SCRIPTS */

    jQuery("#slides").slidesjs({
        width: 1100,
        height: 250,
        play: {
            auto: true,
            pauseOnHover: true,
            interval: 10000,
        }
    });

    jQuery(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        firstDay: 1
    });

    jQuery(".datepicker-registration").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "1960:2000",
        dateFormat: "yy-mm-dd",
        firstDay: 1
    });

    jQuery("#report-bug").click(function () {
        jQuery("#dialog").load('/setting/reportBug').dialog({
            title: "Report Bug",
            width: "550px",
            modal: true,
            position: {my: "center", at: "top", of: window},
            buttons: {
                Cancel: function () {
                    jQuery(this).dialog("close");
                }
            }
        });
    });

    jQuery("button.ajax-button").click(function () {
        var href = jQuery(this).attr("href");
        var val = jQuery(this).val();
        jQuery("#dialog").load(href).dialog({
            title: val,
            width: "550px",
            modal: true,
            position: {my: "center", at: "top", of: window},
            buttons: {
                Cancel: function () {
                    jQuery(this).dialog("close");
                }
            }
        });
    });

    jQuery("a.showReplyForm").click(function (e) {
        e.preventDefault();
        jQuery(this).parents(".messageWrapper").find(".replyForm").toggle(500);
        jQuery(".replyForm:visible textarea.mediuminput").focus();
    });

    jQuery('a#delImg').click(function () {
        event.preventDefault();
        var url = jQuery(this).attr('href');
        var tk = jQuery('#tk').val();

        jQuery.post(url, {tk: tk}, function (msg) {
            if (msg == 'success') {
                jQuery('#currentLogo').hide(500);
                jQuery('#uploadLogo').removeClass('nodisplay');
            } else {
                jQuery('#currentLogo').append("<label class='error'>" + msg + "</label>")
            }
        });

        return false;
    });

});