<p>
    Beste <?= $this->e($name) ?>,<br />
    <br />
    Je werd uitgenodigd om lid te worden van onze website!<br /><br />
    Klik op deze <a href="<?= $this->e($website['url']) ?>#/users/invitation/<?= $this->e($uuid) ?>">link</a> om deze uitnodiging
    te activeren.
</p>
<p>
    Let op! Deze uitnodiging blijft maar geldig voor <?= $this->e($expires) ?> dagen.
</p>
<p>
    Heb je vragen over deze uitnodiging? Neem dan contact op met onze <a href="mailto:<?= $this->e($website['email']) ?>">webmaster</a>.
</p>
<p>
    Met vriendelijke groeten,<br />
    Judokwai Kemzeke.
</p>
