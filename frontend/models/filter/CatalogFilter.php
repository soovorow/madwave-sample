<?php

namespace frontend\models\filter;

use common\components\multilocal\Local;
use common\models\LocalCategory;
use common\models\Product;
use common\models\products\Color;
use soovorow\product_features\models\Feature;
use soovorow\product_features\models\Group;
use soovorow\product_features\models\ProductFeatures;
use soovorow\product_features\models\Type;
use Yii;
use yii\base\Model;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\db\Query;

/**
 * Filter model
 *
 * @property LocalCategory $category
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class CatalogFilter extends Model
{
    public $category;

    public $price;

    public $min_price;
    public $max_price;

    public $formFields;

    public $request;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->request = Yii::$app->request;

        $this->setPrices();

        $this->setOptions([
            $this->addSubcategories([
                $this->addPrices([
                    Local::choose([
                        Local::RU => $this->addFeatures()
                    ]),
                    $this->addColors(),
                    $this->addSizes()
                ]),
            ]),
            $this->addAvailability()
        ]);
    }

    public function setOptions(array $options)
    {
        $this->formFields = $options;
    }

    public function render()
    {
        if (empty($this->formFields)) {
            return null;
        }

        $form = ActiveForm::begin([
            'id' => 'filter_form',
            'method' => 'get',
            'action' => null,
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]);

        $request = \Yii::$app->request;

        echo Html::hiddenInput('page', Html::encode($request->get('page')));
        echo Html::hiddenInput('sort', $request->get('sort'));
        echo Html::hiddenInput('category_id', $this->category->id);

        $this->renderFields($this->formFields);

        $form::end();
    }

    private function renderFields($array)
    {
        foreach ($array as $field) {
            if (is_array($field)) {
                echo '<div>';
                $this->renderFields($field);
                echo '</div>';
            } else {
                echo $field;
            }
        }
    }



    public function addSubcategories($dependFilters = [])
    {

        $isHidden = $this->category->hidden_in_menu === 1;

        $hasHidden = LocalCategory::find()
            ->where(['parent_id' => $isHidden ? $this->category->parent_id : $this->category->id])
            ->andWhere(['>', 'hidden_in_menu', 0])
            ->all();

        $isStock = $this->category->isStock();
        if (!$isHidden && !$hasHidden && !$isStock && $subCategories = self::getSubCategoriesItems()) {

                $input = Html::dropDownList('new_category_id', $this->category->id, $subCategories, [
                    'id' => 'filter-action',
                    'class'=>'filter-ajax-input form-control rad0 text-center',
                    'data-label' => 'sub_category'
                ]);


            }


        if (!isset($input)) {
            $option = null;
        } else {
            $option = $this->wrapFilter(Yii::t('app', 'Product type'), $input, ['isOpen' => 'true']);
        }

        if (!empty($dependFilters)) {
            return [
                $option,
                $dependFilters
            ];
        }

        return $option;

    }
    private function getSubCategoriesItems()
    {
        $subCategories = LocalCategory::find()->where(['parent_id' => $this->category->id])->all();
        if (!$subCategories) {
            $subCategories = LocalCategory::find()->where(['parent_id' => $this->category->parent_id])->all();
        }
        if ($subCategories)
        {
            $items = [];

            if ($parent = $this->category->getParent()) {
                $first_key = $parent->id;
            } else {
                $first_key = $this->category->id;
            }
            $items[$first_key] = \Yii::t('app', 'All products');

            foreach ($subCategories as $subCategory)
            {
                /** @var $subCategory LocalCategory */
                $items[$subCategory->id] = $subCategory->getTitle();
            }
            return $items;
        }
        return null;
    }


    public function addPrices($dependFilters = [])
    {
        $this->addPricesAssets();

        $option = $this::wrapFilter(
            \Yii::t('app', 'Price'),
            Html::tag('div', null, ['id' => 'price-picker'])
            . Html::tag('div', null, ['id' => 'user_price_low', 'class' => 'hidden'])
            . Html::tag('div', null, ['id' => 'user_price_high', 'class' => 'hidden'])
            . Html::tag('div', Html::textInput('price_lower', Html::encode($_GET['price_lower']), [
                'id' => 'price_picker_low',
                'class'=>'filter-ajax-input form-control text-bold rad0 text-center',
                'data-label' => 'price_lower'
            ]), ['class' => 'col-xs-6'])
            . Html::tag('div', Html::textInput('price_higher', Html::encode($_GET['price_higher']), [
                'id' => 'price_picker_high',
                'class'=>'filter-ajax-input form-control text-bold rad0 text-center',
                'data-label' => 'price_higher'
            ]), ['class' => 'col-xs-6'])
            ,
            ['isOpen' => true]
        );

        if ($this->min_price == $this->max_price) {
            $option = null;
        }

        if (!empty($dependFilters)) {
            return [
                $option,
                $dependFilters
            ];
        }
        return $option;
    }

    public function addFeatures()
    {
        if ($this->category->parent_id == null || $this->category->parent_id !== 2) {
            return null;
        }

        $groups = Group::find()
            ->innerJoin(Type::tableName().' t', 't.group_id = '.Group::tableName().'.id')
            ->innerJoin(Feature::tableName().' f', 'f.type_id = t.id')
            ->rightJoin(ProductFeatures::tableName().' pf', 'pf.feature_id = f.id')
            ->innerJoin(Product::tableName().' p', 'p.product_id = pf.product_id')
            ->andWhere(['p.category_id' => LocalCategory::getCategoryIds(
                LocalCategory::find()->indexBy('id')->orderBy('id')->all(), $this->category->id
            )])
            ->innerJoin('product_variant pv', 'pv.product_id = p.product_id')
            ->innerJoin('price', 'price.variant_id = pv.variant_id')
            ->andWhere(['>', 'price.price_rub', 0])
            ->andFilterWhere(['>=', 'price.price_rub', $this->request->get('price_lower')])
            ->andFilterWhere(['<=', 'price.price_rub', $this->request->get('price_higher')])
            ->groupBy('id')
            ->all()
        ;

        $filter = '';

        foreach ($groups as $i => $g) {

            $f = '';

            if ($types = Type::findAll(['group_id' => $g->id])) {

                foreach ($types as $t) {

                    $name = 'feature['.$g->id.']';
                    $checked = isset($_GET['feature'][$g->id]) && $_GET['feature'][$g->id] == $t->id ;
                    $disabled = false;

                    $checkbox = Html::radio($name, $checked, [
                            'class' => 'checkbox filter-ajax-input',
                            'data-label' => 'feature('.$t->title.')',
                            'value' => $t->id,
                            'disabled' => $disabled
                        ])
                        .'<span></span>'.$t->title;

                    $f .= $label = Html::tag('label', $checkbox, [
                        'class' => 'clearfix '.($disabled ? 'disabled' : '' )
                    ]);
                }

                $filter .= $this::wrapFilter($g->title, $f, ['isOpen' => true]);
            }

        }

        return $filter;
    }

    /**
     *
     */
    private function addPricesAssets()
    {
        $view = \Yii::$app->view;

        $html5SliderId = 'price-picker';

        $start_min = isset($_GET['price_lower']) ? Html::encode($_GET['price_lower']) :  $this->min_price;
        $start_max = isset($_GET['price_higher']) ? Html::encode($_GET['price_higher']) : $this->max_price;

        /** @var CatalogFilter $this */
        $view->registerJs(<<<JS
            var body = $('body');

            var radioButtons = $("[type='radio']");
            var radioStates = {};
            $.each(radioButtons, function(index, rd) {
                radioStates[rd.value] = $(rd).is(':checked');
            });
        
            function updateCatalogList() {
                var list_container = $('.product_list_container');
                list_container.addClass('loading');
                var filter_form = $('#filter_form');
                ajaxit('post', '/ajax/catalog-response', list_container, filter_form.serialize())
                    .done(function (response) {
                        var r = JSON.parse(response);
                        console.log(r);
                        $('.product_list').html(r.response.items);
                        $('#product_list_pagination').html(r.response.pagination);
                        $('#category-description').html(r.response.description);
                        $('#category_title').html(r.response.h1);
                        $('.breadcrumb li.active').html(r.response.h1);
                        document.title = r.response.h1;
                        $('#category_header').html(r.response.header);
                        $('#totalListCount').html(r.response.totalCount);
                        $('#filter__container').html(r.filters.html);
                        initPriceSlider(r.filters.min_price, r.filters.max_price, r.filters.start_min, r.filters.start_max);
                        window.history.pushState(null, 'catalog', r.response.url);
                        radioButtons = $("[type='radio']");
                        $.each(radioButtons, function(index, rd) {
                            radioStates[rd.value] = $(rd).is(':checked');
                        });
                    })
                    .always(function() {
                        list_container.removeClass('loading')
                    })
                ;
            }

            
            body.on('click', '[type="radio"]', function() {
                var val = $(this).val();  
                $(this).attr('checked', (radioStates[val] = !radioStates[val]));    
                
                $.each(radioButtons, function(index, rd) {
                    if(rd.value !== val) {
                        radioStates[rd.value] = false; 
                    }
                });
                updateCatalogList();
                initPriceSlider();
                
            });
            
            body.on('click', '#filter_form h4', function() {
                $(this).toggleClass('_open');
                $(this).next().slideToggle()
            });
            
            body.on('click', '.reset-colors', function() {
                $('input.color-checkbox').attr('checked', false);
                updateCatalogList()
            });
            
            body.on('change', '.filter-ajax-input', function() {
                updateCatalogList();
                initPriceSlider();
                ga('send', 'event', 'category', 'filter', $(this).data('label'));
            });
            
            var min_price = $this->min_price;
            var max_price = $this->max_price;
            var start_min = $start_min;
            var start_max = $start_max;
            
            initPriceSlider(min_price, max_price, start_min, start_max);
            
            function initPriceSlider(min_price, max_price, start_min, start_max) {
                var low = document.getElementById('price_picker_low');
                var high = document.getElementById('price_picker_high');
                var user_high = document.getElementById('user_price_high');
                var user_low = document.getElementById('user_price_low');
                var html5Slider = document.getElementById('$html5SliderId');
                
                if (html5Slider && min_price > 0) {
                    noUiSlider.create(html5Slider, {
                        start: [start_min, start_max],
                        connect: true,
                        step: 1,
                        range: {'min': min_price, 'max': max_price}
                    });
                    html5Slider.noUiSlider.on('update', function(values,handle) {
                        var value = values[handle];
                        if (handle) {
                            high.value = Math.round(value);
                            user_high.value  = Math.round(value);
                        } else {
                            low.value = Math.round(value);
                            user_low.value  = Math.round(value);
                        }
                    });
                    html5Slider.noUiSlider.on('end', function(values,handle) {
                        updateCatalogList()
                    });
                    low.addEventListener('change', function(){
                        html5Slider.noUiSlider.set([this.value, null]);
                    });
                    high.addEventListener('change', function(){
                        html5Slider.noUiSlider.set([null, this.value]);
                    });
                }
                
            }
JS
        );
    }

    public function addColors($dependFilters = [])
    {
        $colors = self::getColorsFromQuery($this->getQuery());

        if (empty($colors)) {
            return null;
        }

        $available = $this->getAvailableColors();

        $diff = array_diff($colors, $available);

        $input = '';
        $input .= '<span class="reset-colors"><small>'.\Yii::t('app', 'Reset').'</small></span><br><br>';

        foreach ( $colors as $i => $c ) {
            if ($color = Color::findOne($c)) {

                /** @var $color Color */
                $checked = $_GET['color'][$c] == 1;
                $disabled = !$checked && in_array($c, $diff);

                $colorName = $color->getName();

                $checkbox = Html::checkbox('color['.$c.']', $checked, [
                        'class' => 'color-checkbox checkbox filter-ajax-input',
                        'data-label' => 'color('.$colorName.')',
                        'disabled' => $disabled
                    ])
                    .'<span></span><span class="colorbox color--'.$color->color_id.'"></span>'.$colorName;

                $input .= $label = Html::tag('label', $checkbox, [
                    'clase' => 'clearfix '.($disabled ? 'disabled' : '' )
                ]);
            }
        }

        return $this->wrapFilter(\Yii::t('app', 'Color'), $input, [
                'class' => ' colorpick-form ',
                'isOpen' => isset($_GET['color']) && count($_GET['color']) > 0
            ]);
    }
    private function getColorsFromQuery($productsQuery)
    {
        $colors = [];
        $productsQuery->groupBy('pv.variant_id')
            ->all(\Yii::$app->madwave_rpoducts);
        ;
        $colors_result = (new Query())->select('color_id')->distinct()->from(['u' => $productsQuery])->all(\Yii::$app->madwave_rpoducts);
        foreach ($colors_result as $c) {
            if (!empty($c['color_id'])) {
                $colors[] = $c['color_id'];
            }
        }

        $sameValues = [['0004', '0012'], ['0025', '0023']];

        foreach ($sameValues as $values) {
            $v1 = $values[0];
            $v2 = $values[2];
            $key1 = self::recursiveArraySearch($v1, $colors);
            $key2 = self::recursiveArraySearch($v2, $colors);
            if (!empty($key1)) {
                $colors[$key1] = $v2;
            }
            if (!empty($key2)) {
                $colors[$key2] = $v2;
            }
            if (!empty($key1) && !empty($key2)) {
                unset($colors[$key1]);
            }
        }

        foreach ($colors as $k => $c) {
            if (empty($c)) {
                unset($colors[$k]);
            }
        }
        return $colors;
    }

    public function addSizes($dependsFilters = [])
    {
        if (!$this->category->getParent()) {
            return null;
        }

        if (!$sizes = self::getSizesFromQuery($this->getQuery())) {
            return null;
        }

        $c = self::getAvailableSizes($this->category->id, $this->min_price, $this->max_price);


        $diff = array_diff($sizes, $c);
        $items = [];
        $options = [];

        foreach ($sizes as $size) {
            if ($s = \common\models\products\Size::findOne($size)) {
                $items[$s->size_id] = $s->name;
            }
        }
        foreach ($items as $k => $v) {
            if (in_array($k, $diff)) {
                $options[$k] = ['disabled' => true];
            }

        }
        $selection = in_array($_GET['size'], $diff) ? null : $_GET['size'];

        $input = Html::dropDownList('size', $selection, $items, [
            'class'=>'form-control rad0 filter-ajax-input',
            'data-label' => 'size',
            'prompt' => 'Все',
            'options' => $options
        ]);

        return $this->wrapFilter('Размер', $input, [
            'class' => ' colorpick-form ',
            'isOpen' => isset($_GET['size']) && !empty($_GET['size'])
        ]);
    }

    public function addAvailability($dependFilters = [])
    {
        $input = \yii\helpers\Html::checkbox('available', $_GET['available'], [
            'id' => 'availability-checkbox',
            'class' => 'checkbox filter-ajax-input',
            'data-label' => 'availability',
            'label' => '<span></span>'.\Yii::t('app', 'Only in stock'),
            'labelOptions' => ['style' => 'margin-right: 20px']
        ]);
        $option = $this->wrapFilter(null, $input, [
            'isOpen' => 'true',
            'class' => ' without-header '
        ]);
        if (!empty($dependFilters)) {
            return [
                $option,
                $dependFilters
            ];
        }
        return $option;
    }



    private function getQuery()
    {
        $query = (new Query())
            ->addSelect('pv.variant_id')
            ->addSelect('pv.color_id')
            ->addSelect('pv.size_id')
            ->addSelect('product.discount_card_percent')
            ->addSelect('price.'.$this->price)
            ->from(['pv' => 'product_variant'])
            ->innerJoin('product', 'product.product_id = pv.product_id')
            ->innerJoin('price', 'price.variant_id = pv.variant_id')
            ->andWhere(['>', 'price.'.$this->price, 0])
            ->andWhere(['product.status' => 0])
            ->andWhere(['product.category_id' => LocalCategory::getCategoryIds(
                LocalCategory::find()->indexBy('id')->orderBy('id')->all(), $this->category->id
            )])
        ;

        return $query;
    }

    private function setPrices()
    {
        if (Yii::$app->language == 'ru-RU') {
            $this->price = $this->category->isStock() ? 'price_rub_sale' : Local::choose([
                Local::RU => 'price_rub',
                Local::EU => 'price_eur_rrp'
            ]);
        } else {
            $this->price = Local::choose([
                Local::RU => 'price_rub',
                Local::EU => 'price_eur_rrp'
            ]);
        }


        if ($this->price !== 'price_rub_sale') {
            $priceColumn = '(
                '.$this->price.' * ((100 - case when discount_card_percent > 10 then discount_card_percent else 10 end) / 100)
            )';
        } else {
            $priceColumn = $this->price;
        }

        $prices = (new Query())
            ->addSelect('min('.$priceColumn.') as min_price')
            ->addSelect('max('.$priceColumn.') as max_price')
            ->from(['u' => $this->getQuery()])
            ->all(\Yii::$app->madwave_rpoducts)[0];

        $this->min_price = $prices['min_price'];
        $this->max_price = $prices['max_price'];
    }



    private function wrapFilter($h4, $filter, $containerOptions = [])
    {
        $isOpen = isset($containerOptions['isOpen']) ? $containerOptions['isOpen'] : false;
        $h4isOpenClass = $isOpen ? '_open' : '';
        $filterConteinerStyle = $isOpen ? 'display: block' : '';

        $content = '';

        if (!empty($h4)) {
            $arrow = Html::tag('span', null, ['class' => 'filter-arrow']);
            $title = Html::tag('h4', $arrow.$h4, ['class' => $h4isOpenClass]);
            $content .= $title;
        }

        $filterCol = Html::tag('div', $filter, ['class' => 'col-xs-12']);
        $filterCallRow = Html::tag('div', $filterCol, ['class' => 'row']);

        $content .= Html::tag('div', $filterCallRow, [
            'class' => 'clearfix',
            'style' => $filterConteinerStyle
        ]);

        if (empty($containerOptions['class'])) {
            $containerOptions['class'] = '';
        }
        $containerOptions['class'] .= 'filter_container clearfix';

        $component = Html::tag('div', $content, $containerOptions);

        return $component;
    }



    /**
     * Get only available colors for filtering
     *
     * @return array
     */
    public function getAvailableColors()
    {
        $productsQuery = $this->getQuery();
        $productsQuery->andWhere(['>=', 'price.'.$this->price, isset($_GET['price_lower']) ? $_GET['price_lower'] : 0]);
        if (isset($_GET['price_higher'])) {
            $productsQuery->andWhere(['<=', 'price.'.$this->price, Html::encode($_GET['price_higher'])]);
        }
        $colors = self::getColorsFromQuery($productsQuery);

        return $colors;
    }


    /**
     * Get only available sizes for filtering
     *
     * @param int $category_id id of current page category
     * @param int $min_price
     * @param int $max_price
     * @param array $colors picked colors for mixed filtering
     * @param bool $sale key for filtering special deals page
     * @return array
     */
    public function getAvailableSizes($category_id, $min_price, $max_price, $colors = [], $sale = false)
    {
        $price = 'price_rub';

        $productsQuery = $this->getQuery();

        if ($sale) {
            $productsQuery->andWhere(['>=', 'price.price_rub_sale', $min_price !== null ? $min_price : 0]);
            if ($min_price !== null) {
                $productsQuery->andWhere(['<=', 'price.price_rub_sale', $max_price]);
            }
        } else {
            $productsQuery->andWhere(['>=', 'price.' . $price, $min_price !== null ? $min_price : 0]);
            if ($min_price !== null) {
                $productsQuery->andWhere(['<=', 'price.' . $price, $max_price]);
            }
        }

        $sizes = self::getSizesFromQuery($productsQuery);

        return $sizes;
    }

    /**
     * Format sizes from query
     *
     * Helper method for formatting sizes for filtering from query
     *
     * @param $productsQuery
     * @return array
     */
    private function getSizesFromQuery($productsQuery)
    {

        $sizes = [];

        $productsQuery
            ->groupBy('pv.variant_id')
            ->all(\Yii::$app->madwave_rpoducts);


        $sizes_result = (new Query())->select('size_id')->distinct()->from(['u' => $productsQuery])->all(\Yii::$app->madwave_rpoducts);
        foreach ($sizes_result as $c) {
            if (!empty($c['size_id'])) {
                $sizes[] = $c['size_id'];
            }
        }


        return $sizes;
    }

    /**
     * @author Dmitry Suvorov <soovorow@gmail.com>
     * @param $needle
     * @param $haystack
     * @return bool|int|string
     */
    private function recursiveArraySearch($needle, $haystack) {
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && self::recursiveArraySearch($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }
}
