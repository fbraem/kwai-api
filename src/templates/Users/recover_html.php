<?php $this->layout('root::layout', ['title' => $website['name'] ?? '']) ?>
<h2>Password Reset Request</h2>
<p>
    Dear <strong><?= $this->e($name) ?>,</strong>
</p>
<p>
    Did you forget your password? No problem. With a click on the button below, you can
    reset your password.
</p>
<p style="text-align: center">
    <a href="<?= $this->e($website['url']) ?>/auth/reset?uuid=/<?= $this->e($uuid) ?>"
       class="button"
    >
        Reset My Password
    </a>
</p>
<p>
    When the button is not working, or you would like to reset your password on
    our <a href="<?= $this->e($website['url']) ?>/auth">website</a> without using the button, use the
    following code:
</p>
<p class="center">
    <strong><?= $this->e($uuid) ?></strong>
</p>
<p>
    If you need assistance, or you did not make this request, please contact our webmaster
    <a href="mailto:<?= $this->e($website['email']) ?>"><?= $this->e($website['email']) ?></a>
</p>
<p>
    Note: This code will expire within <?= $this->e($expires) ?> hours.
</p>
<p>
    Best regards,<br />
    <?= $this->e($website['name']) ?>
</p>