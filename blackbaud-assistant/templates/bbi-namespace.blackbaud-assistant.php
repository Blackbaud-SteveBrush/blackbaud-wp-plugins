    <!-- BBI NAMESPACE INIT -->
    <script>
    (function (o) {
        window.bbiAsyncInit = function (bbi) {
            bbi.yield(o).init({
                pageEditorUrlRegex: 'wp-admin'
            });
        }
    })(window.bbiAsyncInit);
    </script>

    <!-- BBI NAMESPACE -->
    <script>
    (function(a,p,i,s) {
     a.getElementById(i)||(s=a.createElement(p),
     s.id=i,s.src="//api.blackbaud.com/bbi?v=1.1.0",
     a.getElementsByTagName("head")[0].appendChild(s)
    )}(document, "script", "bbi-namespace", null));
    </script>

<?php if (isset($data['scripts']) && !empty($data['scripts'])) : ?>
    <!-- CUSTOM SCRIPTS -->
    <?php foreach ($data['scripts'] as $script) : ?>
    <meta data-bbi-src="<?php echo $script['src']; ?>">
    <?php endforeach; ?>
<?php endif; ?>
