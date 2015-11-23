<?php
$markup_id = "blackbaud-modal-" . $data['ID'];
$thumbnail_exists = (!empty($data['meta']['thumbnail']) && $thumbnail = $data['meta']['thumbnail']);
$thumbnail_is_background = (!empty($data['meta']['thumbnail_is_background']) && $data['meta']['thumbnail_is_background'] == 'true');
?>
<div id="<?php echo $markup_id; ?>-container">
    <?php if ($thumbnail_exists && $thumbnail_is_background) : ?>
        <style>
            #<?php echo $markup_id; ?> .modal-content {
                background-image: url(<?php echo $thumbnail; ?>);
                background-repeat: no-repeat;
            }
        </style>
    <?php endif; ?>
    <?php if (isset($data['meta']) && !empty($data['meta']['launcher_label'])) : ?>
        <button type="button" class="btn btn-primary btn-lg btn-blackbaud-flyer btn-launcher<?php echo (!empty($data['meta']['css_class'])) ? ' btn-launcher' . $data['meta']['css_class'] : ''; ?>" data-toggle="modal" data-target="#<?php echo $markup_id; ?>"><?php echo $data['meta']['launcher_label']; ?></button>
    <?php endif; ?>
    <div class="modal modal-blackbaud-flyer fade<?php echo (!empty($data['meta']['css_class'])) ? ' ' . $data['meta']['css_class'] : ''; ?>" id="<?php echo $markup_id; ?>">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title"><?php echo $data ['post_title']; ?></h3>
                </div>
                <div class="modal-body">
                    <?php if (!empty($data['meta']['html_before'])) : ?>
                        <div class="modal-html-before"><?php echo $data['meta']['html_before']; ?></div>
                    <?php endif; ?>
                    <?php if ($thumbnail_exists && !$thumbnail_is_background) : ?>
                        <div class="thumbnail"><img src="<?php echo $thumbnail; ?>"></div>
                    <?php endif; ?>
                    <?php if (!empty($data['post_excerpt'])) : ?>
                        <h4 class="modal-subtitle"><?php echo $data['post_excerpt']; ?></h4>
                    <?php endif; ?>
                    <div class="modal-body-content"><?php echo $data['post_content']; ?></div>
                    <?php if (!empty($data['meta']['button_label'])) : ?>
                        <div class="modal-call-to-action"><a class="btn btn-primary" target="_blank" href="<?php echo $data['meta']['button_url']; ?>"><?php echo $data['meta']['button_label']; ?></a></div>
                    <?php endif; ?>
                    <?php if (!empty($data['meta']['html_after'])) : ?>
                        <div class="modal-html-after"><?php echo $data['meta']['html_after']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    /**
     * Auto-launch the modal?
     */
    ?>
    <?php if (!empty($data['meta']['auto_launch']) && $data['meta']['auto_launch'] == 'true') : ?>
        <div data-bbi-app="BlackbaudFlyer"
            data-bbi-action="LaunchModal"
            data-flyer-id="<?php echo $markup_id; ?>"
            <?php echo (!empty($data['meta']['show_once']) && $data['meta']['show_once'] == 'true') ? ' data-show-once="true"' : ''; ?>
            ></div>
    <?php endif; ?>
</div>
