<?php namespace common\components\rules;

use common\models\BlogPost;
use common\models\Log;
use common\models\Product;
use common\models\Settings;
use Yii;
use yii\helpers\Json;
use yii\web\UrlRuleInterface;

class BlogRule implements UrlRuleInterface
{

    public function parseRequest($manager, $request)
    {

        $pathInfo = $request->getPathInfo();

        $pathInfoArr = explode('/', $pathInfo);

        if ($pathInfoArr[0] === 'world') {
            $post = BlogPost::findOne(['slug' => array_reverse($pathInfoArr)[0]]);

            if ($post) {
                return ['world/view', ['slug' => $post->slug]];
            } else {
                return ['world/index', [
                    'url_path' => $pathInfoArr[1],
                    'url_path_2' => $pathInfoArr[2],
                ]];
            }
        }

        return false;
    }

    public function createUrl($manager, $route, $params)
    {
        if (explode('/', $route)[0] !== 'world') {
            return false;
        }

        if (isset($params['url_path'], $params['url_path_2'])) {
            $labels = [
                $params['url_path'],
                $params['url_path_2'],
            ];

            foreach ($labels as $i => $l) {
                if (empty($l)) {
                    unset($labels[$i]);
                }
            }
            $labels = implode('/', $labels);

            if (!empty($labels)) {
                $labels = '/'.$labels;
            }
        }



        $get = [];

        if (!empty($params['page'])) {
            $get[] = 'page='.$params['page'];
        };

        if (!empty($params['per-page'])) {
            $get[] = 'per-page='.$params['per-page'];
        }

        if (!empty($get)) {
            $attributes = '?'.implode('&', $get);
        } else {
            $attributes = '';
        }

        if (!isset($labels)) {
            $labels = '';
        }


        return Yii::$app->params['baseUrl'].'/world'.$labels.$attributes;
    }


}