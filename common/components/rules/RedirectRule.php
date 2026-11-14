<?php

namespace common\components\rules;

use common\components\screaming_crawl\models\Redirect;
use yii\web\UrlRuleInterface;

class RedirectRule implements UrlRuleInterface
{
    public function parseRequest($manager, $request)
    {
        if ($r = Redirect::findOne(['from' => '/'.$request->getPathInfo()])) {
            return ['site/redirect', ['path' => $r->to]];
        }

        $reject = [
            'ru/',
            'eu/',
            'en/',
            'fi/',
            'wp-',
        ];

        if (in_array(substr($request->getPathInfo(), 0, 3), $reject)) {
            return ['site/redirect', ['path' => '/']];
        }

        $reject = [
            'get_c',
            'imag/',
            'flag/',
            'node/',
        ];

        if (in_array(substr($request->getPathInfo(), 0, 5), $reject)) {
            return ['site/redirect', ['path' => '/']];
        }

        $reject = [
            'product-search',
            'product_quick_v',
            'debug/default/',
            'flag/flag/wait',
            'product_quick_',
        ];

        if (in_array(substr($request->getPathInfo(), 0, 15), $reject)) {
            return ['site/redirect', ['path' => '/']];
        }

        if (substr($request->getPathInfo(), -1, 1) === '/') {
            return ['site/redirect', ['path' => '/'.substr($request->getPathInfo(), 0, -1)]];
        }

        if (substr($request->getPathInfo(), -5, 5) === '.html') {
            return ['site/redirect', ['path' => '/'.substr($request->getPathInfo(), 0, -5)]];
        }

        return false;
    }

    public function createUrl($manager, $route, $params)
    {
        return false;
    }
}