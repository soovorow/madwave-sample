<?php

namespace frontend\modules\checkout\eu\models\delivery_methods;

use common\models\SelfPickUpPoints;
use frontend\modules\citydetector\CityDetector;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

class MethodPickup extends DeliveryMethod
{
    public $label = 'Самовывоз';
    public $description = 'Забрать в нашем магазине, бесплатно';
    public $code = '"ТГ504642"';
    public $view = 'checkout___delivery__pickup';

    public $_search;
    public $picking_up_point;
    public $picking_up_point_fake;
    public $pick_up_points_count;
    public $pick_up_points;

    public $_city;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->_city = CityDetector::getCity();

        $this->pick_up_points = SelfPickUpPoints::find()
            ->where(['is_pick_up_point' => 1, 'city' => $this->getCity()])
            ->all();
        $this->pick_up_points_count = self::getPickUpPointsCount();
    }

    public function getJsInitFunction() {
        return 'methodPickupInit';
    }

    public function rules()
    {
        return [
            ['picking_up_point', 'isPointValid'],
            [
                [
                    'picking_up_point',
                ],
                'required',
                'whenClient' => $this->requiredWhenClient()
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'picking_up_point' => 'Пункт самовывоза'
        ];
    }

    public function getCity() {
        return $this->_city;
    }

    public function getPostalCode()
    {
        return null;
    }

    public function getAddress()
    {
        $point = SelfPickUpPoints::findOne($this->picking_up_point);
        return $point->address .' - '.$point->title;
    }

    public function isPointValid() {
        if (!SelfPickUpPoints::findOne(['id' => $this->picking_up_point, 'is_pick_up_point' => 1])) {
            $this->addError('picking_up_point');
        }
    }

    function getCost()
    {
        return 0;
    }

    /**
     * @param \yii\web\Controller $controller
     * @return string
     */
    public static function LoadPickUpRadioList($controller) {
        $data = SelfPickUpPoints::find()->where(['is_pick_up_point' => 1, 'city' => CityDetector::getCity()]);

        if ($passphrase = \Yii::$app->request->post('keyphrase')) {
            $data->andWhere("(
                `title` LIKE '%$passphrase%' 
                OR `metro_title` LIKE '%$passphrase%' 
                OR `address` LIKE '%$passphrase%' 
            )");
        } else {
            $passphrase = ' ';
        }

        $data = $data->all();

        if (count($data) > 0)
            return $controller->render('../cart/checkout___delivery__pickup__list', [
                'data' => $data,
                'model' => new MethodPickup(),
                'form' => new ActiveForm(),
            ]);

        return $controller->render('../cart/checkout___delivery__pickup__list__empty', [
            'passphrase' => $passphrase,
        ]);
    }

    public function getPickUpPointsCount()
    {
        return SelfPickUpPoints::find()->where(['is_pick_up_point' => 1, 'city' => CityDetector::getCity()])->count();
    }

    public function getJsonLocationsArray($form) {
        $data = [];
        foreach ($this->pick_up_points as $p) {
            /**
             * @var $p SelfPickUpPoints;
             */

            $header = '<h4 class="mb20">'.$p->title.'</h4>';

            $metro = !empty($p->metro_title) ? ('<h5>Метро:</h5><p>'.$p->metro_title.'</p>') : '';
            $address = !empty($p->address) ? ('<h5>Адрес:</h5><p>'.$p->address.'</p>') : '';
            $hours = !empty($p->working_hours) ? ('<h5>Время работы:</h5><p>'.$p->working_hours.'</p>') : '';
            $phone = !empty($p->phone) ? ('<h5>Phone:</h5><p>'.$p->phone.'</p>') : '';

            $h2 = '<h5>Обратите внимание</h5>';
            $text = '<p>Наш менеджер свяжется с вами по телефону и уточнит возможную дату самовывоза</p>';
            $text2 = '<p><small>При получении заказа в фирменной розничной сети Mad Wave примерка обязательна, в противном случае, мы не можем гарантировать обмен и возврат товара.</small></p>';

            $submit_button = '<p>'.$form->field($this, 'picking_up_point_fake')->radio([
                    'value' => $p->id,
                    'label' => Html::tag('span', '', ['class' => 'btn btn-danger w100 on-map'])
                ]).'</p>';

            $left_side = '<div class="col-md-6 p0">'.$metro.$address.$hours.$phone.'</div>';

            $right_side = '<div class="col-md-6 p0">'.$h2.$text.$submit_button.$text2.'</div>';

            $content = '<div class="picking-up-modal">'.$header.$left_side.$right_side.'</div>';

            $data[] = [
                'id' => $p->id,
                'title' => $p->title,
                'coordinates' => ['lat' => floatval($p->latitude), 'lng' => floatval($p->longitude)],
                'content' => $content
            ];
        }
        return Json::encode($data);
    }

    public function getFirstName()
    {
        return null;
    }

    public function getLastName()
    {
        return null;
    }

    public function getSecondName()
    {
        return null;
    }
}