<div class="wrap wrap-blackbaud">
    <div class="page-header">
        <h2>
            <span class="dashicons dashicons-admin-settings"></span> <?php echo $data["page_title"]; ?>
        </h2>
    </div>
    <form method="post" action="options.php" enctype="multipart/form-data">
        <?php settings_fields($data['page_id']); ?>
        <?php echo $data['form_html']; ?>
        <?php submit_button(); ?>
    </form>
</div>
