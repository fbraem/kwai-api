<html>
    <head>
        <?php foreach($meta as $property => $content): ?>
            <meta property="<?= $this->e($property) ?>" content="<?= $this->e($content) ?>" />
        <?php endforeach ?>
        <title></title>
    </head>
    <body>
        <h1>Kwai Facebook Crawler Page</h1>
    </body>
</html>
