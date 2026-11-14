<div class="col-xs-12">
    <div class="blog-post">
        <div class="attachment">
            <?= !empty($post->cover) ? $post->cover : '' ?>
        </div>
        <div class="content">
            <div class="clearfix">
                <a href="<?= $post->category->url ?>"
                   class="label label-default"><?= $post->category->title ?></a>
                <b><div class="fa fa-eye color-primary"></div> <?= $post->views ?></b>
            </div>
            <h3><?= \yii\helpers\Html::a($post->title, $post->url) ?></h3>
            <p>
                <?= !empty($post->preview) ? $post->preview : substr(strip_tags($post->content), 0, 255) ?>
            </p>
            <div class="clearfix">
                <div class="pull-left"><?= date('d-m-Y', $post->created_at) ?></div>
                <div class="pull-right">
                    <div class="ya-share2" data-services="vkontakte,facebook,gplus,twitter"
                         data-counter=""
                         data-url="<?= $post->url ?>"
                         data-title="<?= $post->title ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>