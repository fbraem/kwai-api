<html>
<head>
    <?php $this->insert('root::styles') ?>
</head>
<body>
<?php if ($title): ?>
    <h1>
        <?= $this->e($title) ?>
    </h1>
<?php endif; ?>
<?= $this->section('content') ?>
</body>
</html>
