<div class="panel panel-default">
    <?php if ($data['title']) : ?>
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $data['title']; ?></h3>
        </div>
    <?php endif; ?>
    <div class="panel-body">
        <?php if ($data['section_additional_html']) : ?>
            <?php echo $data['section_additional_html']; ?>
        <?php endif; ?>
        <?php if (isset($data['field_html'])) : ?>
            <table class="form-table">
                <?php echo $data['field_html']; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
