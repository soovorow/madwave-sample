<?php
namespace frontend\controllers;

use common\components\AdmitAd;
use common\components\Criteo;
use common\components\multilocal\Local;
use common\widgets\product\page\ViewedProducts;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use common\models\LocalCategory;
use common\models\Product;
use common\models\ProductVariant;
use frontend\models\ProductSearch;

/**
 * Catalog controller
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class CatalogController extends \yii\web\Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            Url::remember();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Product page
     *
     * @param string $slug product slug
     * @param int $sku
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($slug = null, $sku = null) {

        if ($slug == null && $sku == null) {
            throw new NotFoundHttpException('Page not found.');
        }

        if ($sku == null) {

            if (!$model = Product::findOne(['slug' => $slug])) {
                throw new NotFoundHttpException('Page not found.');
            }

            if (!$variant = $model->getVariantsByColor(1)[0]) {
                throw new NotFoundHttpException('Page not found.');
            }

        } else {

            if (!$variant = ProductVariant::findOne(['variant_id' => $sku])) {
                throw new NotFoundHttpException('Page not found.');
            }

            $model = $variant->product;
        }

        if ($variant->status == 1) {
            throw new NotFoundHttpException('Page not found.');
        }

        $model->addView();
        ViewedProducts::addViewInSession($model->id);

        return $this->render('view', [
            'model' => $model,
            'v' => $variant,
        ]);
    }

    /**
     * Search page
     *
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @param string $keywords search keywords
     * @return string
     */
    public function actionSearch($keywords)
    {
        return $this->render('search', [
            'productsDataProvider' => (new ProductSearch())->searchByKeyword($keywords),
        ]);
    }

    public function actionStreamlineWithLens()
    {
        return $this->render('streamline');
    }
    public function actionStreamlineRainbowWithLens()
    {
        return $this->render('streamline-rainbow');
    }

    public function actionPromoPage($products = [])
    {
        return $this->render('promo-april');
    }


    /**
     * Category page
     *
     * params - jedi force implementing irrational category slugs for save old links for SEO
     *
     * @param string $url_path
     * @param string|null $url_path_2
     * @param string|null $url_path_3
     * @param string|null $url_path_4
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionList($url_path, $url_path_2 = null, $url_path_3 = null, $url_path_4 = null)
    {
        Url::remember();

        /** @var $category LocalCategory */
        $category = LocalCategory::find()
            ->where(['url_path' => $url_path])
            ->andWhere(['url_path_2' => $url_path_2])
            ->andWhere(['url_path_3' => $url_path_3])
            ->andWhere(['url_path_4' => $url_path_4])
            ->one();

        if (!$category) {
            throw new NotFoundHttpException('Такой страницы не существует.');
        }

        $category->registerMeta($this->view);
        $productsDataProvider = (new ProductSearch())->search(\Yii::$app->request->queryParams, $category->id);

        if (Local::detectLocal() == Local::RU) {
            AdmitAd::setCategoryPage($category->id);
            Criteo::setCategoryPage($productsDataProvider);
        }

        return $this->render('list', [
            'productsDataProvider' => $productsDataProvider,
            'category' => $category,
            'stock' => false
        ]);
    }

    /**
     * @param string $slug
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionListEu($slug = null)
    {
        if (!$slug || !$category = LocalCategory::findBySlug($slug)) {
            throw new NotFoundHttpException('Page not found.');
        }

        Url::remember();

        $category->registerMeta($this->view);

        return $this->render('list', [
            'productsDataProvider' => (new ProductSearch())->search(\Yii::$app->request->queryParams, $category->id),
            'category' => $category,
            'stock' => false
        ]);
    }

    /**
     * Special deals page
     *
     * @return string
     */
    public function actionStock()
    {
        $category = LocalCategory::find()->where(['url_path' => 'stock'])->one();
        $category->registerMeta($this->view);
        $productsDataProvider = (new ProductSearch())
            ->search(\Yii::$app->request->queryParams, ProductSearch::IS_STOCK);

        if (Local::detectLocal() == Local::RU) {
            AdmitAd::setCategoryPage($category->id);
            Criteo::setCategoryPage($productsDataProvider);
        }

        return $this->render('list', [
            'productsDataProvider' => $productsDataProvider,
            'category' => $category,
            'stock' => true,
        ]);
    }

}
