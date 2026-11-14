<?php

namespace frontend\controllers;

use common\components\AdmitAd;
use common\components\AuthHandler;
use common\components\Criteo;
use common\components\MailChimp;
use common\components\multilocal\Local;
use common\components\sitemap\SiteMap;
use common\models\AppString;
use common\models\BlogPost;
use common\models\Collection;
use common\models\content\ChallengeSteps;
use common\models\content\HomepageBanner;
use common\models\content\PinnedItems;
use common\models\Order;
use common\models\Page;
use common\models\Product;
use common\models\ProductVariant;
use common\models\sale\CyberMonday;
use common\models\sale\February23;
use common\models\sale\ValentineDay;
use common\models\sale\Winter17_18;
use common\models\SelfPickUpPoints;
use common\models\User;
use frontend\models\A1;
use frontend\models\Subscribe;
use frontend\models\user\LoginForm;
use frontend\models\user\PasswordResetRequestForm;
use frontend\models\user\SignupForm;
use frontend\models\user\ResetPasswordForm;
use frontend\models\ProductSearch;
use frontend\models\WinterSaleSubscirbers;
use frontend\modules\checkout\ru\models\PaymentTerminal;
use frontend\modules\checkout\ru\models\pricing\ClientCard;
use frontend\widgets\AsideProduct;
use frontend\modules\citydetector\CityDetector;
use frontend\widgets\Paytrail;
use Nette\Utils\Html;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\View;

/**
 * Site controller
 * @author Dmitry Suvorov <soovorow@gmail.com>
 * @author Dmitry Alferov <dima@wdda.pro>
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    ['actions' => ['signup'],'allow' => true,'roles' => ['?'],],
                    ['actions' => ['logout'],'allow' => true,'roles' => ['@'],],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     * @todo disable csrf validatioin only for actions that realy need it
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;


        if (parent::beforeAction($action)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
                'successUrl' => Url::to(['/account/orders']),
            ],
        ];
    }

    public function actionWinterSale()
    {
        Url::remember();
        if (isset($_GET['rotate'])) {
            Winter17_18::cronJob();
        }
        return $this->render('winter_17_18');
    }

    public function actionRobots()
    {
        $response = Yii::$app->response;

        $response->format = yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'text/plain');

        $response->data = implode("\n", [
            'User-agent: *',
            'Disallow:',
            'sitemap: '.Yii::$app->params['baseUrl'].'/sitemap.xml',
        ]);

        return $response;

    }

    public function actionSitemap()
    {
        $response = Yii::$app->response;

        $response->format = yii\web\Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'application/xml');

        $response->data = file_get_contents(Yii::getAlias('@extras').'/'.SiteMap::getFileName());

        return $response;
    }

    /**
     * Success callback for oauth authorization
     * @param $client
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function onAuthSuccess($client)
    {
        (new AuthHandler($client))->handle();
    }

    /**
     * Paytrail response action
     * @return bool|Response
     * @author Dmitry Alferov <dima@wdda.pro>
     */
    public function actionPaytrail()
    {
        $status = Paytrail::confirmPayment();
        return $status;
    }

    /**
     * Payment response action
     * @return bool|Response
     * @author Dmitry Alferov <dima@wdda.pro>
     */
    public function actionPaymentResponse()
    {
        $postData = \Yii::$app->request->post();
        $getData = \Yii::$app->request->get();

        $terminal = new PaymentTerminal();
        $response = $terminal->response($getData, $postData);

        if(!$response){
            return $this->goHome();
        }

        return $response;
    }

    /**
     * Redirect action
     * @param $path string
     * @todo move to another class and include in actions()
     * @return Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionRedirect($path)
    {
        return $this->redirect($path, 301);
    }

    /**
     * Redirect to trail slash page
     * @return Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionAddSuffixSlash()
    {
        return $this->redirect(Yii::$app->getRequest()->url.'/', 301);
    }

    /**
     * Redirect to trail .html page
     * @return Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionAddSuffixHtml()
    {
        return $this->redirect(Yii::$app->getRequest()->url.'.html', 301);
    }

    /**
     * Logout action
     * @return Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionLogout()
    {
        \Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * A1 page
     * @return bool|string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionA1()
    {
        $model = new A1();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $mc = new MailChimp();
            $mc::setUserEmail($model->email);
            $mc->addListMember($model->email);
            $mc->addInterest($model->email, 'a1', true);
            Yii::$app->session->setFlash('success', 'Спасибо за регистрацию');
            return $this->redirect('a1');
        }
        return $this->render('a1', [
            'model' => $model
        ]);
    }

    /**
     * Home page
     * @return bool|string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @author Dmitry Alferov <dima@wdda.pro>
     */
    public function actionIndex()
    {
        if (Local::detectLocal() == Local::RU) {
            AdmitAd::setIndexPage();
            Criteo::setIndexPage();
        }

        //TODO: полностью удалить после 15.06.2017 (после того как сменятся URL на Yandex Kassa)
        if(!empty($_GET['ypayment'])){
            $postData = \Yii::$app->request->post();
            $getData = \Yii::$app->request->get();
            $getData['pcode'] = 'ТГ505444';

            $terminal = new PaymentTerminal();
            $response = $terminal->response($getData, $postData);

            return $response;
        }

        $view = $this->view;

        $view->title = Local::choose([
            Local::RU => 'Mad Wave - очки, купальники, шапочки и другие товары для плавания и бассейна.',
            Local::EU => 'Mad Wave swimming goggles, racing suits and swimwear | Mad Wave'
        ]);

        $view->registerMetaTag([
            'name' => 'description',
            'content' => Local::choose([
                Local::RU => 'Добро пожаловать в интернет-магазин Mad Wave! Широкий выбор товаров для плавания и бассейна: купальники, плавки, очки, шапочки и аксессуары!',
                Local::EU => 'Discover Mad Wave’s range of swimwear, goggles, swim trunks, swimsuits, swimming equipment, gear, accessories and lots more!'
            ])
        ]);

        $hitProducts = (new ProductSearch())->search([
            'pageSize' => 9,
        ])->models;

        $hitProductsPreview = '';

        foreach ($hitProducts as $i => $product) {
            /* @var $product ProductVariant */
            $hitProductsPreview .= $product->preview('Home', $i+1);
        }

        $popularArticles = BlogPost::find()->orderBy('id DESC')->limit(4)->all();

        return $this->render('index', [
            'homepageBanner' => HomepageBanner::widget(),
            'pinnedItems' => PinnedItems::widget(),
            'aside_cc_desc' => AppString::getString('aside_cc_desc'),
            'shotr_about' => AppString::getString('shotr_about'),
            'asideProduct' => AsideProduct::widget(['model' => ProductVariant::findOne(10022333)]),
            'hitProductsPreview' => $hitProductsPreview,
            'popularArticles' => $popularArticles,
        ]);
    }

    public function actionWinterSubscriber()
    {
        $model = new WinterSaleSubscirbers();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->submit();
            Yii::$app->session->setFlash('success', 'Вы подписаны на уведомления!');
        }

        return $this->redirect('/sales/new-year-2017-2018');
    }

    public function actionPlayground()
    {
        $order = Order::findOne(75637);
        $order->initDelivery();
//        Product::getDb()
//            ->createCommand()
//            ->update(
//                Product::tableName(),
//                ['discount_card_percent' => 10],
//                'discount_card_percent = '.'25'
//            )
//            ->execute();
    }

    /**
     * Footer subscription form
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionMailScribe()
    {
        $model = new Subscribe();
        if ($model ->load(Yii::$app->request->post()) && $model ->validate()) {
            $model ->submit();
            return $this->redirect(['site/mail-subscription-success']);
        }
        \Yii::$app->session->setFlash('error', 'Ошибка! Попробуйте еще раз');
        return $this->goHome();
    }

    /**
     * Footer subscription success page
     * This action was separated to set up google analytics goal tracking by url
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionMailSubscriptionSuccess()
    {
        return $this->render('subscription-success');
    }

    /**
     * SignUp page, signUp action
     * @return string|Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                $mc = new MailChimp();
                $mc->addListMember($model->email);
                $mc->addInterest($model->email, 'reg', true);
                if (Yii::$app->getUser()->login($user)) {
                    ClientCard::apply('30000-60000');
                    return $this->goBack();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionOffline()
    {
        return $this->renderPartial('offline');
    }

    /**
     * Login page, login action
     * @return string|Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            ClientCard::apply('30000-60000');
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    
    public function actionCyberMonday()
    {
        return $this->render('cyber-monday');
    }

    public function actionValentineDay()
    {
        return $this->render('valentine-day');
    }

    public function actionFebruaryTwentyThree()
    {
        return $this->render('february-twenty-three');
    }

    public function actionEightMarch()
    {
        return $this->render('eight-march');
    }

    /**
     * Request password reset page
     * @return string|Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                \Yii::$app->getSession()->setFlash('success', 'На ваш e-mail было выслано письмо с инструкциями по восстановлению вашего пароля.');

                return $this->goBack();
            } else {
                \Yii::$app->getSession()->setFlash('error', 'К сожалению мы не можем восстановить пароль для этого e-mail адреса.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Reset password page
     * @param $token
     * @return string|Response
     * @throws BadRequestHttpException
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(\Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            \Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goBack();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Confirm e-mail changing
     * @param $token
     * @return string
     * @throws BadRequestHttpException
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionConfirmEmailChange($token) {

        try {

            if (empty($token) || !is_string($token)) {
                throw new InvalidParamException('Email confirm token cannot be blank.');
            }

            $user = User::findByEmailConfirmToken($token);

            if (!$user) {
                throw new InvalidParamException('Wrong email confirm token.');
            }

        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $new_email = $user->new_email;
        $old_email = $user->email;

        $user->email = $new_email;
        $user->new_email = null;
        $user->email_change_token = null;
        if ($user->save()) {
            $orders = Order::find()->where(['email' => $old_email])->all();
            foreach ($orders as $order) {
                $order->email = $new_email;
                $order->save(false);
            }
        };

        return $this->render('confirm-success');
    }

    /**
     * MW Challenge landing page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionChallenge() {
        $this->layout = 'empty';

        return $this->render('challenge', [
            'data' => ChallengeSteps::findAll(['status' => 1]),
            'products' => ProductVariant::findAll(['variant_id' => [
                10017448,
                10017825,
                10013297,
                10017089,
            ]]),
            'souvenirs' => ProductVariant::findAll(['variant_id' => [
                10019853,
                10019855,
                10019852,
                10019854,
            ]]),
        ]);
    }

    /**
     * About page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionAbout() {
        $view = Local::choose([
            Local::RU => 'about-ru',
            Local::EU => 'about-eu',
        ]);

        if (Local::detectLocal() == Local::RU) {
            Criteo::setIndexPage();
        }

        return $this->render($view);
    }

    /**
     * Morozov page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionVladimirMorozov() {
        if (Local::detectLocal() == Local::RU) {
            Criteo::setIndexPage();
        }

        return $this->render('morozov');
    }

    /**
     * Delivery page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionDelivery()
    {
        if (Local::detectLocal() == Local::RU) {
            Criteo::setIndexPage();
        }

        return $this->render(Local::choose([
            Local::RU => 'delivery',
            Local::EU => 'delivery_eu'
        ]));
    }

    public function actionAcceptCookiesPolicy()
    {
        Yii::$app->session->set('accept_cookies_policy', true);
        return true;
    }

    public function actionCookiesPolicy()
    {
        return $this->render('cookies-policy');
    }

    /**
     * Stores page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionStores()
    {
        $city = CityDetector::getCity();

        $stores_all = SelfPickUpPoints::find();
        $stores_city = SelfPickUpPoints::find()->where(['city' => $city]);

        $stores_result_view = $this->view->render('/site/stores__result', [
            'stores' => $stores_city,
        ]);

        $stores_all_view = $this->view->render('/site/stores__all', [
            'stores' => $stores_all,
            'stores_array' => $stores_all->asArray()->orderBy('city')->all(),
        ]);

        if (Local::detectLocal() == Local::RU) {
            Criteo::setIndexPage();
        }

        return $this->render('stores', [
            'city' => $city,
            'stores' => $stores_city,
            'stores_result_view' => $stores_result_view,
            'stores_all_view' => $stores_all_view,
        ]);
    }

    /**
     * Contacts page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionContacts()
    {
        if (Local::detectLocal() == Local::RU) {
            Criteo::setIndexPage();
        }

        return $this->render(Local::choose([
            Local::RU => 'contacts-ru',
            Local::EU => 'contacts-eu'
        ]));
    }

    /**
     * Franchise page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionFranchise() {
        return $this->render('franchise');
    }

    /**
     * Discounts page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionDiscountOffers() {
        return $this->render('discount-offers');
    }

    /**
     * Distribution page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionDistribution() {
        return $this->render('distribution');
    }

    /**
     * Returns and exchange page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionReturnsExchange() {
        return $this->render(Local::choose([
            Local::RU => 'returns-exchange-ru',
            Local::EU => 'returns-exchange-eu'
        ]));
    }

    /**
     * Size-Guide page
     * @return string
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionSizeGuide() {
        return $this->render('size-guide');
    }

    /**
     * Collection view page
     * @param $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCollections($slug)
    {
        if (!$model = Collection::findOne(['slug' => $slug])) {
            throw new NotFoundHttpException();
        }

        return $this->render('collection', [
            'model' => $model
        ]);
    }

    /**
     * Yandex market export file
     * @return Response
     * @author Dmitry Suvorov <soovorow@gmail.com>
     */
    public function actionYandexMarket()
    {
        $file = \Yii::getAlias('@webroot').'/yandex.yml';
        return \Yii::$app->response->xSendFile($file);
    }

    /**
     * Displays Page specified by it's slug
     *
     * @param $slug string Page slug
     * @return string
     * @author Vyacheslav Elchin <lexscite@gmail.com>
     */
    public function actionPage($slug)
    {
        $page = Page::findBySlug($slug);

        return $this->render('page', [
            'page' => $page,
        ]);
    }

    public function actionMorozovPrigoda()
    {
        $products = [
            'morozov' => [
                [501 => 10021527],
                [1126 => 10017444],
                [1496 => 10022754],
                [992 => 10022336],
            ],
            'prigoda' => [
                [1497 => 10022588],
                [1126 => 10017446],
                [1496 => 10022753],
                [1277 => 10022334],
            ],
        ];

        $morozov = [];
        foreach ($products['morozov'] as $id => $sku) {
            $morozov[] = ProductVariant::findOne(['variant_id' => $sku]);
        }

        $prigoda = [];
        foreach ($products['prigoda'] as $id => $sku) {
            $prigoda[] = ProductVariant::findOne(['variant_id' => $sku]);
        }

        return $this->render('morozov-prigoda', [
            'morozov' => $morozov,
            'prigoda' => $prigoda,
        ]);
    }
}
