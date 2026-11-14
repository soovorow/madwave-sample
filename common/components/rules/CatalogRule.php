<?php

namespace common\components\rules;

use common\models\Log;
use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\UrlRuleInterface;

/**
 * Catalog controller url creating rules
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class CatalogRule implements UrlRuleInterface
{
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        $pathInfoArr = explode('/', $pathInfo);

        if (substr($pathInfo, -1, 1) === '/') {
            return ['site/redirect', ['path' => '/'.substr($pathInfo, 0, -1)]];
        }

        if ($pathInfoArr[0] === 'catalog') {
            $slug = array_reverse($pathInfoArr)[0];
            if ($product = Product::findOne(['slug' => $slug])) {
                return ['catalog/view', ['slug' => $slug]];
            }
            return ['catalog/list', [
                'url_path' => $pathInfoArr[1],
                'url_path_2' => $pathInfoArr[2],
                'url_path_3' => $pathInfoArr[3],
                'url_path_4' => $pathInfoArr[4]
            ]];
        }

        if ($pathInfoArr[0] === 'stock') {
            return ['catalog/list', [
                'url_path' => $pathInfoArr[0],
            ]];
        }
        return false;
    }

    public function createUrl($manager, $route, $params)
    {
        $firstAttr = explode('/', $route)[0];

        if ($firstAttr !== 'catalog' && $firstAttr !== 'stock') {
            return false;
        }

        $_SERVER['HTTPS']='on';

        $attributes = self::getAttributes($params);

        if(isset($params['slug']['slug'])) {
            if ($product = Product::findOne(['slug' => $params['slug']['slug']])) {
                return $product->getUrl().$attributes.'#review';
            }
        }

        if ($firstAttr === 'catalog') {

            $labels = [];
            if (isset($params['url_path'])) {
                $labels[] = $params['url_path'];
            }
            if (isset($params['url_path_2'])) {
                $labels[] = $params['url_path_2'];
            }
            if (isset($params['url_path_3'])) {
                $labels[] = $params['url_path_3'];
            }
            if (isset($params['url_path_4'])) {
                $labels[] = $params['url_path_4'];
            }
            foreach ($labels as $i => $l) {
                if (empty($l)) unset($labels[$i]);
            }
            return 'catalog/'.implode('/', $labels).$attributes;
        }

        if ($firstAttr === 'stock') {
            return 'stock/'.$attributes;
        }

        return false;
    }

    private function getAttributes($params)
    {
        $get = [];
        $paramsArr = [
            'sku',
            'page',
            'per-page',
            'sort',
            'price_lower',
            'price_higher',
            'size',
        ];
        foreach ($paramsArr as $getKey) {
            if (!empty($params[$getKey]))
                $get[] = $getKey.'='.Html::encode($params[$getKey]);
        }

        if (!empty($params['color'])) {
            foreach ($params['color'] as $k => $value) {
                $get[] = 'color['.Html::encode($k).']='.Html::encode($value);
            }
        };

        return empty($get) ? '' : ('?'.implode('&', $get));
    }


}