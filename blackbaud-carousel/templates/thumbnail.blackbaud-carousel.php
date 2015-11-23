<?php $slide = $data['post']; ?>
<div class="thumbnail">



    <?php
    /**
     * Slide Image.
     */
    ?>
    <img src="<?php echo $slide->thumbnail; ?>" alt="">

    <div class="caption">



        <?php
        /**
         * Title.
         */
        ?>
        <?php if (isset($slide->post_title)) : ?>
            <h1><?php echo $slide->post_title; ?></h1>
        <?php endif; ?>



        <?php
        /**
         * Content.
         */
        ?>
        <?php if (!empty($slide->post_content)) : ?>
            <div class="carousel-caption-blurb">
                <?php echo $slide->post_content; ?>
            </div>
        <?php endif; ?>



        <?php
        /**
         * Subtitle 1.
         */
        ?>
        <?php if (!empty($slide->fields['subtitle_1'][0])) : ?>
            <h2><?php echo $slide->fields['subtitle_1'][0]; ?></h2>
        <?php endif; ?>



        <?php
        /**
         * Subtitle 2.
         */
        ?>
        <?php if (!empty($slide->fields['subtitle_2'][0])) : ?>
            <h3><?php echo $slide->fields['subtitle_2'][0]; ?></h3>
        <?php endif; ?>



        <?php
        /**
         * Button Primary.
         */
        ?>
        <?php if (!empty($slide->fields['primary_button_label'][0])) : ?>
            <span class="carousel-call-to-action">
                <a class="btn btn-lg btn-primary" href="<?php echo $slide->fields['primary_button_link'][0]; ?>">
                    <?php echo $slide->fields['primary_button_label'][0]; ?>
                </a>
            </span>
        <?php endif; ?>



        <?php
        /**
         * Button Secondary.
         */
        ?>
        <?php if (!empty($slide->fields['secondary_button_label'][0])) : ?>
            <span class="carousel-call-to-action">
                <a class="btn btn-lg btn-default" href="<?php echo $slide->fields['secondary_button_link'][0]; ?>">
                    <?php echo $slide->fields['secondary_button_label'][0]; ?>
                </a>
            </span>
        <?php endif; ?>
    </div>
</div>
