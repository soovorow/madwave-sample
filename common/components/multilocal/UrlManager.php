<?php

namespace common\components\multilocal;

class UrlManager extends \yii\web\UrlManager {

    public function init()
    {
        $rules = Local::choose([
            Local::EU => [
                'site/payment-response' => 'site/payment-response',
                'site/paytrail' => 'site/paytrail',
                'robots.txt' => 'site/robots',
                'sitemap.xml' => 'site/sitemap',
                'look/<slug>' => 'look/index',
                '/' => 'site/index',
                'site/index' => 'site/index',
                'site/contacts' => 'site/contacts',
                'site/redirect' => 'site/redirect',
                'site/delivery' => 'site/delivery',
                'catalog/streamline-with-lens' => 'catalog/streamline-with-lens',
                'catalog/streamline-rainbow-with-lens' => 'catalog/streamline-rainbow-with-lens',

                'site/accept-cookies-policy' => 'site/accept-cookies-policy',
                'site/cookies-policy' => 'site/cookies-policy',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'signup' => 'site/signup',
                '/login/reset-password'                         => 'site/reset-password',
                '/login/request-password-reset'                 => 'site/request-password-reset',
                'collection/<slug>' => 'site/collections',
                'catalog/search' => 'catalog/search',
                'distribution' => 'site/distribution',
                'cart' => 'checkout/cart/index',
                'ajax/<action>' => 'ajax/<action>',
                'catalog/<slug>' => 'catalog/list-eu',
                'site/size-guide' => 'site/size-guide',
                'site/<slug>' => 'site/page',

                'generate-site-map' => 'site/generate-site-map', // http://madwave.eu/generate-site-map
                'about' => 'site/about',
                ['class' => 'common\components\rules\RedirectRule'],
                '<slug>' => 'catalog/view',
            ],
            Local::RU => [
                'morozov-prigoda' => 'site/morozov-prigoda',
                '23-february' => 'site/february-twenty-three',
                '8-marta' => 'site/eight-march',
                '14-february' => 'site/valentine-day',
                'playground' => 'site/playground',
                'cyber-monday' => 'site/cyber-monday',
                'site/payment-response' => 'site/payment-response',
                'site/accept-cookies-policy' => 'site/accept-cookies-policy',
                'sales/new-year-2017-2018' => 'site/winter-sale',
                'robots.txt' => 'site/robots',
                'site/winter-subscriber' => 'site/winter-subscriber',
                'sitemap.xml' => 'site/sitemap',
                'google/merchant' => 'google/merchant',
                'look/<slug>' => 'look/index',
                'catalog/streamline-with-lens' => 'catalog/streamline-with-lens',
                'catalog/streamline-rainbow-with-lens' => 'catalog/streamline-rainbow-with-lens',
                'site/redirect' => 'site/redirect',
                'collection/<slug>' => 'site/collections',
                'a1' => 'site/a1',
                '/' => 'site/index',
                'ajax/<action>' => 'ajax/<action>',

                '/load-mc-cart' => 'checkout/cart/load-mc-cart',
                'subscription-success' => 'site/mail-subscription-success',

                [
                    'pattern' => '/letter_ape/track-open/<key>',
                    'route' => '/letter_ape/action/track-open'
                ],

                'promo/textile-50-off' => 'catalog/promo-page',

                '/sites/default/files/yml/yandex.yml' => 'site/yandex-market',


                'stock' => 'catalog/stock',

                'challenge' => 'site/challenge',
                'vladimir-morozov' => 'site/vladimir-morozov',

                'world/searh' => 'world/search',

                'site/auth' => 'site/auth',

                'site/<slug>' => 'site/page',

                '/login/reset-password'                         => 'site/reset-password',
                '/login/request-password-reset'                 => 'site/request-password-reset',
                '/about/franchise'                              => 'site/franchise',
                '/about/info/returns-exchange'                  => 'site/returns-exchange',
                '/about/info/contacts'                          => 'site/contacts',
                '/about/info-panel/discount-offers'             => 'site/discount-offers',
                '/about/info/sizes'                             => 'site/size-guide',
                '/about/magazini'                               => 'site/stores',
                '/about/info/delivery'                          => 'site/delivery',
                '/kak-poluchit-kartu-postoyannogo-pokupatelya'  => 'site/order-card',

                'signup' => 'site/signup',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'about' => 'site/about',
                'catalog/search' => 'catalog/search',

                'cart' => 'checkout/cart/index',

                ['class' => 'common\components\rules\RedirectRule'],
                ['class' => 'common\components\rules\BlogRule'],
                ['class' => 'common\components\rules\CatalogRule'],
                ['pattern' => '<slug>','route' => 'catalog/view','suffix' => '.html',],
            ],
        ]);

        $this->addRules($rules);

        parent::init();
    }
}