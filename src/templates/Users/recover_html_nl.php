<?php $this->layout('root::layout', ['title' => $website['name'] ?? '']) ?>
<h2>Paswoord Herstellen</h2>
<p>
    Beste <strong><?= $this->e($name) ?>,</strong>
</p>
<p>
    Jouw paswoord vergeten? Geen probleem. Met een klik op onderstaande knop kan je een nieuw
    paswoord instellen.
</p>
<p style="text-align: center">
    <a href="<?= $this->e($website['url']) ?>/auth/reset?uuid=<?= $this->e($uuid) ?>"
       class="button"
    >
        Wijzig mijn paswoord
    </a>
</p>
<p>
    Wanneer de knop niet werkt, of u wilt het paswoord herstellen via onze
    <a href="<?= $this->e($website['url']) ?>">website</a> zonder gebruik te maken
    van deze knop, gebruik dan de volgende code:
</p>
<p class="center">
    <strong><?= $this->e($uuid) ?></strong>
</p>
<p>
    Heeft u hulp nodig, of heeft u dit verzoek niet gemaakt, gelieve dan contact op nemen
    met onze webmaster <a href="mailto:<?= $this->e($website['email']) ?>"><?= $this->e($website['email']) ?></a>.
</p>
<p>
    Let op: Deze code vervalt binnen <?= $this->e($expires) ?> uren.
</p>
<p>
    Met vriendelijke groeten, <br />
    <?= $this->e($website['name']) ?>
</p>
