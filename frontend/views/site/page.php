<?php

/* @var $this \yii\web\View */
/* @var $page \common\models\Page */

use yii\helpers\Url;

$this->title = $page->title;
$this->registerMetaTag(['name' => 'description', 'content' => $page->meta_desc]);
?>

<div class="page">
    <div class="container">
        <?= $page->body; ?>
    </div>
</div>
