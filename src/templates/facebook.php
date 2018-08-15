<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
    <head>
        <title>Facebook Share</title>
        <meta http-equiv="content-type\" content="text/html; charset=utf-8">
        <meta property="og:url" content="<?= $this->e($meta['og:url']) ?>">
        <meta property="og:title" content="<?= $this->e($meta['og:title']) ?>">
        <meta property="og:description" content="<?= $this->e($meta['og:description']) ?>">
<?php if (isset($meta['og:image'])) : ?>
        <meta property="og:image" content="<?= $this->e($meta['og:image']) ?>">
        <meta property="og:image:width" content="<?= $this->e($meta['og:image:width']) ?>">
        <meta property="og:image:height" content="<?= $this->e($meta['og:image:height']) ?>">
<?php endif ?>
    </head>
    <body>
        <p>
            This page is only for Facebook shares ...
        </p>
    </body>
</html>
