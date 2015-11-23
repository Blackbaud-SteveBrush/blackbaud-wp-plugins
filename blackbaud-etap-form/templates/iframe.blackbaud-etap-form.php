<iframe class="etap-iframe" style="width: 100%; border: none;" src="<?php echo $data["src"]; ?>"></iframe>
<script>
;(function ($) {
    $(function () {
        $('.etap-iframe').responsiveIframe({ xdomain: '*' });
    });
})(jQuery);
</script>
