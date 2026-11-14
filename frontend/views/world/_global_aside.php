<?php
use yii\helpers\Url;
?>
<form action="<?= Url::to(['world/index']) ?>" class="search-in-blog">
    <label for="search_in_blog">Поиск в блогах</label>
    <div class="row">
        <div class="col-xs-9">
            <input name="BlogPostSearch[keywords]" id="search_in_blog" type="text" class="form-control rad0 w100" value="<?= $_GET['BlogPostSearch']['keywords'] ?>" placeholder="Введите запрос">
        </div>

        <div class="col-xs-3 pl0">
            <input class="btn btn-primary w100" type="submit" value="Поиск">
        </div>
    </div>
</form>

<div class="pinned-post">
    <header class="zigzag">
        <div class="label label-primary">
            Пост месяца
        </div>
        <div class="inline-block">
            <b><div class="fa fa-eye color-primary"></div> <?= $pinnedPost->views ?></b>
            <br><small>просмотров</small>
        </div>
    </header>
    <div class="content">
        <div class="attachment"><?= !empty($pinnedPost->cover) ? $pinnedPost->cover : '' ?></div>
        <h3><?= \yii\helpers\Html::a($pinnedPost->title, $pinnedPost->url) ?></h3>
        <p><?= !empty($pinnedPost->preview) ? $pinnedPost->preview : substr(strip_tags($pinnedPost->content), 0, 255) ?></p>
    </div>
</div>

<div class="aside-widget">
    <h4 class="zigzag">Популярные теги</h4>
    <p>
        <?php foreach ($categories as $c) { ?>
            <a href="<?= $c->getUrl() ?>"><?= $c->title ?></a>
        <?php } ?>
    </p>
</div>

<div class="aside-widget">
    <h4 class="zigzag">Описание блога</h4>
    <p>
        У нас вы найдете полезные статьи по обучению плаванию детей и взрослых, упражнения по усовершенствованию техники плавания, советы по развитию силы и выносливости для пловцов всех уровней, а также информацию о наших инновационных продуктах и технологиях.
    </p>
</div>