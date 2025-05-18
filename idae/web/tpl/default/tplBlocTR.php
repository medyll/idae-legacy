<table width="560" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" align="center">
    <tr>
        <td width="<?= $size1[0] ?>" valign="top">
            <?php if ($image1) { ?>
            <a href="<?= SITE_URL ?>"><img src="<?= SITE_URL ?>/newsletters/images/<?= $image_prefix . '_1.jpg?t=' . $mtime1 ?>" <?= $size1[3] ?> border="0"></a>
            <?php } ?>
        </td>
        <td width="<?= (560 - $size1[0]) ?>" height="<?= $size1[1] ?>" valign="top">
        <table border="0" width="<?= (560 - $size1[0]) ?>" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td valign="top" height="<?= $size1[1] - $size2[1] ?>" style="padding: 12px 16px">
            <?php if (!empty($block->title)) { ?>
            <img alt="<?= htmlspecialchars($block->title) ?>" src="<?= SITE_URL ?>/newsletters/images/<?= $title_image ?>?t=<?= $title_mtime ?>" <?= $title_size[3] ?> border="0">
            <?php } ?>

            <?php if (!empty($block->content)) { ?>
            <p style="width: 200px; font-family: Arial, sans-serif; font-size: 12px; color: #000000; line-height: 15px; margin: 0; padding-left: 4px; padding-top: 6px;">
            <?= nl2br(htmlspecialchars($block->content)) ?>
            </p>
            <?php } ?>
            </td>
        </tr>
        <tr>
            <td valign="bottom">
                <?php if ($image2) { ?>
                <a href="<?= SITE_URL ?>/"><img src="<?= SITE_URL ?>/newsletters/images/<?= $image_prefix . '_2.jpg?t=' . $mtime2 ?>" <?= $size2[3] ?> border="0"></a>
                <?php } ?>
            </td>
        </tr>
        </table>
        </td>
    </tr>
    </table>