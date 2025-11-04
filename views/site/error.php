<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Произошла ошибка при обработке вашего запроса.
    </p>
    <p>
        <?= Html::a('Вернуться на главную', ['pool/index'], ['class' => 'btn btn-primary']) ?>
    </p>
</div>

