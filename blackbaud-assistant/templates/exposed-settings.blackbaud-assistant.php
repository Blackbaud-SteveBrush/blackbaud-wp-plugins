<script id="blackbaud-settings-<?php echo $data['alias']; ?>">
    window.BBWP = window.BBWP || {};
    window.BBWP.plugins = window.BBWP.plugins || {};
    window.BBWP.plugins["<?php echo $data['alias']; ?>"] = <?php echo json_encode($data['settings'], true); ?>;
</script>
