<?php namespace frontend\controllers;

use common\components\MailChimp;
use common\components\multilocal\Local;
use common\models\AvailabilitySubscriber;
use common\models\LocalCategory;
use common\models\Product;
use common\models\ProductVariant;
use common\models\SelfPickUpPoints;
use common\models\Testimonials;
use frontend\models\filter\CatalogFilter;
use frontend\models\ProductSearch;
use frontend\modules\citydetector\CityDetector;
use Yii;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\widgets\LinkPager;

/**
 * Ajax controller
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class AjaxController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $this->layout = 'empty';
            return true;
        }
        return false;
    }

    public function actionCatalogResponse()
    {
        if (!$category = LocalCategory::findOne($_POST['new_category_id'])) {
            $category = LocalCategory::findOne(96);
        };

        $filter = new CatalogFilter(['category' => $category]);

        $dataProvider = (new ProductSearch())
            ->search($_POST, $category->id == 96 ? null : $category->id);

        $dataProvider->pagination->pageSize = 15;

        if ($_POST['category_id'] == $_POST['new_category_id'] || !isset($_POST['new_category_id'])) {
            $filter_start_min_price = (int) isset($_POST['price_lower']) ? $_POST['price_lower'] :  $filter->min_price;
            $filter_start_max_price = (int) isset($_POST['price_higher']) ? $_POST['price_higher'] :  $filter->max_price;
        } else {
            $filter_start_min_price = (int) $filter->min_price;
            $filter_start_max_price = (int) $filter->max_price;
            $dataProvider->pagination->setPage(0);
        }

        $_POST['price_lower'] = $filter_start_min_price;
        $_POST['price_higher'] = $filter_start_max_price;

        $_GET = $_POST;

        $items = '';



        $n = isset($_GET['page']) ? (($_GET['per-page'] * ($_GET['page'] - 1)) + 1) : 1;
        $list = $category->getTitle();


        if ($dataProvider->totalCount > 0) {
            foreach ($dataProvider->models as $model) {
                /* @var $model \common\models\ProductVariant */
                $items .= $model->preview($list, $n, null);
                $n++;
            }
        } else {
            $items = $this->renderPartial('../catalog/_empty_state', [
                'url' => $category->getUrl()
            ]);
        }

        $getParams = [
            'page' => $dataProvider->getPagination()->getPage(),
            'sort' => $_POST['sort'],
            'available' => $_POST['available'],
            'size' => $_POST['size'],
            'color' => $_POST['color'],
            'price_lower' => $filter_start_min_price,
            'price_higher' => $filter_start_max_price,
            '1c_categories' => $_POST['1c_categories'],
            'feature' => $_POST['feature']
        ];

        $pageUrl = $category->getUrl().'?'.http_build_query($getParams);

        $urlParams = [
            'url_path' => $category->url_path,
            'url_path_2' => $category->url_path_2,
            'url_path_3' => $category->url_path_3,
            'url_path_4' => $category->url_path_4,
        ];

        $dataProvider->getPagination()->route = 'catalog/list';
        $dataProvider->getPagination()->params = array_merge($getParams, $urlParams);

        $response = [];
        $response['post'] = $_POST;
        $response['response'] = [
            'totalCount' => Yii::t('app', '{n} products', [
                'n' => $dataProvider->getTotalCount()
            ]),
            'total' => $dataProvider->getCount(),
            'items' => $items,
            'pagination' => LinkPager::widget(['pagination' => $dataProvider->pagination]),
            'url' => $pageUrl,
            'h1' => $category->getTitle(),
            'header' => $category->getHeader(),
            'description' => $category->getDesc(),
        ];

        $response['filters']['start_min'] = $filter_start_min_price;
        $response['filters']['start_max'] = $filter_start_max_price;

        $response['filters']['min_price'] = (int) $filter->min_price;
        $response['filters']['max_price'] = (int) $filter->max_price;

        if (isset($_POST['1c_categories'])) {
            $response['filters']['1c_categories'] = $_POST['1c_categories'];
        }



        $response['filters']['html'] = $this->renderPartial('../catalog/_filter_form', ['category' => $category]);

        return Json::encode($response);
    }


    public function actionLoadStoreList()
    {
        $passphrase = \Yii::$app->request->post('keyphrase');

        $city = CityDetector::getCity();
        $stores = SelfPickUpPoints::find()->where(['city' => $city]);

        if (!empty($passphrase)) {
            $stores->andWhere("(
                `title` LIKE '%$passphrase%' 
                OR `metro_title` LIKE '%$passphrase%' 
                OR `address` LIKE '%$passphrase%' 
            )");
        }

        if ($stores->count() > 0) {
            return $this->render('../site/stores__result', [
                'stores' => $stores,
            ]);
        }

        return $this->render('../site/stores__empty-state');

    }

    public function actionSearch()
    {
        $key = Yii::$app->request->post('key');

        if (empty($key)) {
            return $this->renderPartial('../layouts/search-empty-state');
        }

        $catQuery = LocalCategory::find()
            ->where(['like', Local::choose([Local::RU => 'title',Local::EU => 'title_en',]), $key])
            ->indexBy('id')
            ->limit(3)
            ->all()
        ;

        return $this->renderPartial('search', [
            'catQuery' => $catQuery,
            'productQuery' => (new ProductSearch())->searchByKeyword($key, true)->limit(10)->all(),
            'key' => $key
        ]);
    }

    /**
     * Home page map location action
     */
    public function actionLoadMetro()
    {

        /**
         * @TODO load info and map position by first metro || city id if metro empty, on change metro call this function
         */

        $city_id = $_POST['city_id'];
        $metro_id = $_POST['metro_id'];
        if ($city_id || $metro_id) {

            if ($metro_id > 0) {
                $metros[0] = SelfPickUpPoints::findOne($_POST['metro_id']);
            } else {

                $city = SelfPickUpPoints::findOne($_POST['city_id']);

                $metros = SelfPickUpPoints::find()
                    ->where(['city' => $city->city])
                    ->groupBy('metro_title')->all();
            }

            if (count($metros) > 0) {

                $options = '';
                foreach ($metros as $metro) {
                    if (strlen($metro->metro_title) > 1) {
                        $options .= '<option value="' . $metro->id . '">' . $metro->metro_title . '</option>';
                    }
                }

                if (strlen($options) > 1) {
                    $SELECT = '<select name="city_id" class="contact_metro">' . $options . '</select>';
                } else {
                    $SELECT = '';
                }

                $response = [
                    'select' => $SELECT,
                    'address' => $metros[0]->city . ', ' . $metros[0]->address,
                    'time' => $metros[0]->working_hours,
                    'coordinates' => [$metros[0]->latitude, $metros[0]->longitude],
                    'phone' => $metros[0]->phone,
                ];

                return Json::encode($response);
            }
        }
        return '';
    }




    /**
     * Add product to cart action
     */
    public function actionAddToCart()
    {
        Yii::error('123');


        $request = Yii::$app->request;
        if ($sku = $request->post('sku')) {
            if (!is_array($sku)) {
                if (!$product = ProductVariant::findOne(['variant_id' => $sku])) {
                    throw new BadRequestHttpException();

                }
                $quantity = $request->post('quantity');
                if ($quantity > 0) {
                    $cart = Yii::$app->cart;
                    if ($cart->hasPosition($sku)) {
                        $cart->update($product, ($cart->getPositionById($sku)->getQuantity() + $quantity));
                    } else {
                        $cart->put($product);
                        $cart->update($product, $quantity);
                    }
                    (new MailChimp())->createCart();
                } elseif ($_POST['subscriber']) {
                    $as = AvailabilitySubscriber::findOne(['email' => $_POST['subscriber'], 'sku' => $_POST['sku']]);
                    if (!$as) {
                        $as = new AvailabilitySubscriber();
                        $as->email = $_POST['subscriber'];
                        $as->sku = $_POST['sku'];
                        $as->save(false);
                    }
                    return 'subscribed!';
                }
            }

            if (is_array($sku)) {
                foreach ($sku as $s) {
                    if (!$product = ProductVariant::findOne(['variant_id' => $s])) {
                        throw new BadRequestHttpException();

                    }
                    $quantity = $request->post('quantity');
                    if ($quantity > 0) {
                        $cart = Yii::$app->cart;
                        if ($cart->hasPosition($s)) {
                            $cart->update($product, ($cart->getPositionById($s)->getQuantity() + $quantity));
                        } else {
                            $cart->put($product);
                            $cart->update($product, $quantity);
                        }
                        (new MailChimp())->createCart();
                    } elseif ($_POST['subscriber']) {
                        $as = AvailabilitySubscriber::findOne(['email' => $_POST['subscriber'], 'sku' => $_POST['sku']]);
                        if (!$as) {
                            $as = new AvailabilitySubscriber();
                            $as->email = $_POST['subscriber'];
                            $as->sku = $_POST['sku'];
                            $as->save(false);
                        }
                        return 'subscribed!';
                    }
                }
            }

        } else {
            throw new BadRequestHttpException();
        }
    }

    /**
     * Reload add to cart button
     */
    public function actionReloadAddtocart()
    {
        if (isset($_POST['sku']) && !empty($_POST['sku'])) {
            $sku = $_POST['sku'];
            $model = ProductVariant::findOne($sku);
            return $model->renderAddToCartButton();
        } else {
            return '';
        }
    }

    /**
     * Refresh filter color values
     */
    public function actionLoadFilterColors()
    {
        return $this->render('../catalog/_filter_form_colors', [
            'category_id' => $_POST['category_id'],
            'alpha_min_prive' => $_POST['min_price'],
            'alpha_max_prive' => $_POST['max_price'],
            'stock' => $_POST['stock'] == 'true' ? true : false
        ]);
    }

    /**
     * Refresh filter sizes values
     */
    public function actionLoadFilterSizes()
    {
        return $this->render('../catalog/_filter_form_sizes', [
            'category_id' => $_POST['category_id'],
            'alpha_min_prive' => $_POST['min_price'],
            'alpha_max_prive' => $_POST['max_price'],
            'stock' => $_POST['stock'] == 'true' ? true : false
        ]);
    }


    /**
     * Load product view by another sku
     * @throws BadRequestHttpException
     */
    public function actionLoadNewView()
    {
        /** @var $v ProductVariant */

        if (isset($_POST['sku']) && $v = ProductVariant::findOne(['variant_id' => $_POST['sku']])) {
            return Json::encode([
                'sku' => $v->getId(),
                'view' => $this->render('../catalog/_view_new', [
                    'model' => $v->product,
                    'v' => $v
                ])
            ]);
        }

        throw new BadRequestHttpException;
    }


    /**
     * Load product view by another sku
     * @throws BadRequestHttpException
     */
    public function actionLoadView()
    {
        if (isset($_POST['sku']) && $v = ProductVariant::findOne(['variant_id' => $_POST['sku']])) {
            return Json::encode([
                'sku' => $v->getId(),
                'view' => $this->render('../catalog/_view', [
                    'model' => $v->product,
                    'v' => $v
                ])
            ]);
        }

        throw new BadRequestHttpException;
    }


    /**
     * Review voting
     * @return int
     * @throws BadRequestHttpException
     */
    public function actionVoteComment()
    {
        if (\Yii::$app->user->isGuest) {
            return 'guest';
        }

        if (!isset($_POST['id'], $_POST['bool']) || !$t = Testimonials::findOne($_POST['id'])) {
            throw new BadRequestHttpException();
        }

        return $t->vote($_POST['bool'] == 'true');
    }

    /**
     * Review editing form
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionLoadReviewEditing()
    {
        if (!isset($_POST['review_id'])) {
            throw new BadRequestHttpException;
        }

        $review = Testimonials::findOne($_POST['review_id']);

        if (!$review || $review->user_id !== \Yii::$app->user->identity->getId()) {
            throw new BadRequestHttpException;
        }

        return $this->render('../catalog/_new_testimonials_form', [
            'testimonialsForm' => $review,
            'model' => Product::findOne($review->product_id),
            'force' => true
        ]);
    }


    public function actionLoadModal()
    {
        return $this->renderPartial('modal', [
            'view' => $_POST['type'],
            'params' => $_POST['params']
        ]);
    }

}

