<?php if (count($data['slides']) > 0) : ?>
    <?php
    $counter = 0;
    $plugin = $blackbaud->get_plugin('blackbaud_carousel');
    $is_image_backgrounds = (isset($data['image_backgrounds']) && $data['image_backgrounds'] === "on");
    ?>



    <?php
    /**
     * Transition effects.
     */
    ?>
    <?php if ($data['transition_type'] === "fade") : ?>
        <style>
            .carousel-fade > .carousel-inner > .item {
                opacity: 0;
                -webkit-transition: <?php echo $data['transition_speed']; ?>ms linear;
                   -moz-transition: <?php echo $data['transition_speed']; ?>ms linear;
                     -o-transition: <?php echo $data['transition_speed']; ?>ms linear;
                        transition: <?php echo $data['transition_speed']; ?>ms linear;
                -webkit-transition-property: opacity;
                   -moz-transition-property: opacity;
                     -o-transition-property: opacity;
                        transition-property: opacity;
            }
            .carousel-fade > .carousel-inner > .active,
            .carousel-fade > .carousel-inner > .next.left,
            .carousel-fade > .carousel-inner > .prev.right {
                opacity: 1;
            }
            .carousel-fade > .carousel-inner > .active.left,
            .carousel-fade > .carousel-inner > .active.right {
                left: 0;
                opacity: 0;
                z-index: 1;
            }
            .carousel-fade > .carousel-control {
                z-index: 2;
            }
            .carousel div[data-href] {
                cursor: pointer;
            }
        </style>
    <?php endif; ?>

    <div id="simple-carousel-<?php echo $data['id']; ?>" class="carousel slide<?php echo ($data['transition_type'] === "fade") ? ' carousel-fade' : ''; ?><?php echo ($is_image_backgrounds) ? ' carousel-image-backgrounds' : ''; ?>">



        <?php
        /**
         * Indicators.
         */
        ?>
        <ol class="carousel-indicators">
            <?php foreach ($data['slides'] as $i => $slide) : ?>
                <li data-target="#simple-carousel-<?php echo $data['id']; ?>" data-slide-to="<?php echo $i; ?>"<?php echo ($i == $data['starting_index']) ? ' class="active"' : ''; ?>></li>
            <?php endforeach; ?>
        </ol>


        <div class="carousel-inner">
            <?php foreach ($data['slides'] as $i => $slide) : ?>
                <?php
                $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($slide->ID), 'single-post-thumbnail');
                $slide->thumbnail = $thumbnail[0];
                ?>



                <?php
                /**
                 * Multiple items per iteration (uses Thumbnail Template).
                 */
                ?>
                <?php if ($data['num_slides_per_iteration'] > 1) : ?>

                    <?php
                    $counter++;
                    $css_class  = $slide->fields['css_class'][0] . " item";
                    $css_class .= ($counter == 1) ? ' active' : '';
                    ?>

                    <?php if ($counter == 1) : ?>
                        <div class="<?php echo $css_class; ?>">
                            <div class="row">
                    <?php endif; ?>
                                <div class="col-sm-<?php echo $data['column_width']; ?>">
                                    <?php echo $plugin->get_template('thumbnail.blackbaud-carousel.php', array("post" => $slide)); ?>
                                </div>
                    <?php if ($counter == $data['num_slides_per_iteration'] || $counter === $data['num_slides']) : ?>
                        <?php $counter = 0; ?>
                            </div>
                        </div>
                    <?php endif; ?>



                <?php
                /**
                 * Only one item, normal slide (uses Slide Template).
                 */
                ?>
                <?php else : ?>

                    <?php
                    $css_class  = $slide->fields['css_class'][0] . " item";
                    $css_class .= ($i == $data['starting_index']) ? ' active' : '';
                    $css_class .= ($is_image_backgrounds) ? ' item-background-image' : '';
                    $slide->css_class = $css_class;
                    $slide->image_is_background = $is_image_backgrounds;
                    echo $plugin->get_template('slide.blackbaud-carousel.php', array("post" => $slide));
                    ?>

                <?php endif; ?>
            <?php endforeach; ?>
        </div>



        <?php
        /**
         * Navigation buttons.
         */
        ?>
        <a class="left carousel-control" href="#simple-carousel-<?php echo $data['id']; ?>" data-slide="prev">
            <?php echo stripslashes($data['navigation_previous']); ?>
        </a>
        <a class="right carousel-control" href="#simple-carousel-<?php echo $data['id']; ?>" data-slide="next">
            <?php echo stripslashes($data['navigation_next']); ?>
        </a>
    </div>



    <?php
    /**
     * Initialize carousel.
     */
    ?>
    <script>
        (function ($) {
            $(function () {
                if ($.fn.carousel) {
                    $('#simple-carousel-<?php echo $data['id']; ?>').carousel({
                        interval: <?php echo (isset($data['auto_play']) && $data['auto_play'] == 'on') ? $data['interval'] : 'false'; ?>,
                        pause: "<?php echo (isset($data['pause']) && $data['pause'] == 'on') ? 'hover' : 'false'; ?>",
                        wrap: <?php echo (isset($data['loop']) && $data['loop'] == 'on') ? 'true' : 'false'; ?>,
                        keyboard: true
                    });
                }
            });
        }(jQuery));
    </script>
<?php endif; ?>
