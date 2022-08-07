<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error container">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        L'erreur ci-dessus est survenue pendant que le serveur traitait votre demande.
    </p>
    <p>
        Veuillez nous contacter pour résoudre ce problème. Merci.
    </p>

</div>
