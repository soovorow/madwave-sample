<?php

namespace common\models;

use common\components\multilocal\Local;
use common\components\screaming_crawl\models\CrawlDiffs;
use common\components\screaming_crawl\ScreamingCrawl;
use common\models\content\OverriddenMeta;
use common\models\content\ProductLabel;
use common\models\content\TopBanner;
use common\models\content\TopBannerProducts;
use common\models\products\Category;
use frontend\modules\checkout\ru\models\pricing\ClientCard;
use yii\base\Exception;
use yii\behaviors\SluggableBehavior;
use yii\bootstrap\Html;
use yii\helpers\BaseInflector;

/**
 * Product model
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $url_slug_prefix
 * @property string $unique_url_path
 * @property string $redirect_from_old
 * @property int $status
 * @property int $discount_card
 * @property int $discount_code
 * @property string $desc_ru
 * @property string $desc_en
 * @property string $vendor_id
 * @property int $activity_id
 * @property string $brand
 * @property string $video
 * @property string $unit
 * @property int $bos_capacity
 * @property int $min_boxes
 * @property int $min_pcs
 * @property string $comment
 * @property string $TNVED
 * @property int $is_on_sale
 * @property int $is_new
 * @property int $is_bestseller
 * @property string $type
 * @property string $compositions
 * @property string $compositions_en
 * @property string $discount_card_percent
 * @property mixed globalCategory
 * @property mixed variants
 * @property TopBanner topBanner
 * @property int todayViewsCount
 * @property mixed label
 * @property mixed target_color
 *
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class Product extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->madwave_rpoducts;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'ensureUnique' => true
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['category_id'], 'integer'],
            [['price'], 'number'],
            [
                [
                    'discount_card',
                    'discount_code',
                    'slug',
                    'url_slug_prefix',
                    'unique_url_path',
                    'redirect_from_old',
                    'target_color',
                ],
                'default'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'price' => 'Price',
        ];
    }

    public function getClientCardDiscountPercent() {
        return $this->discount_card_percent > 0 ? $this->discount_card_percent : ClientCard::DISCOUNT;
    }

    /**
     * Get product id
     *
     * @return int
     */
    public function getId()
    {
        return $this->product_id;
    }

    /**
     * Get product url
     *
     * @todo think how to avoid jedi switcher
     * @param bool $local
     * @return string
     * @internal param bool $lang
     */
    public function getUrl($local = false)
    {

        $domain = \Yii::$app->params['baseUrl'];

        if (\Yii::$app->language == 'ru-RU') {

        } else {
            $url = '/'.$this->slug;
            if ($local) {
                return $url;
            }
            return $domain.$url;
        }


        $globalCategory = $this->globalCategory;

        if (!$globalCategory) return null;

        $globalCategory = $globalCategory->id;


        if (!empty($this->unique_url_path)) {
            return $domain . '/catalog' . $this->unique_url_path . $this->slug;
        }

        switch ($globalCategory) {
            case 61: // Очки для плавания
                $path = '/ochki-dlya-plavaniya/trenirovochnye-ochki/';
                break;
            case 11: // Очки для плавания
                $path = '/vodnoe-polo/inventar-dlya-vodnogo-polo/myach/';
                break;
            case 35: // Ласты
                $path = '/inventar-dlya-plavaniya/lasty/lasty/';
                break;
            case 81: // Ласты
                $path = '/snorkelling/lasty/';
                break;
            case 39: // Ласты для брасса
                $path = '/inventar-dlya-plavaniya/lasty/lasty-dlya-brassa/';
                break;
            case 12: // Моноласта
                $path = '/inventar-dlya-plavaniya/lasty/monolasta/';
                break;
            case 2: // Доска для плавания
                $path = '/inventar-dlya-plavaniya/doski-kalabashki/doska-dlya-plavaniya/';
                break;
            case 172: // Детский купальник
                $path = '/tekstil-sportivnoe-plavanie/detskie-sportivnye-kupalniki-i-plavki/detskiy-kupalnik/';
                break;
            case 215: // Мужские плавки-шорты
                $path = '/muzhskie-sportivnye-plavki/muzhskie-plavki-shorty/';
                break;
            case 284: // Мужские плавки-шорты антихлор
                $path = '/muzhskie-sportivnye-plavki/muzhskie-plavki-shorty-antihlor/';
                break;
            case 130: // ПУ шапочка
                $path = '/shapochki-dlya-plavaniya/tekstilnaya-shapochka/pu-shapochka/';
                break;
            case 165: // Мужские джаммеры антихлор
                $path = '/muzhskie-sportivnye-plavki/muzhskie-dzhammery/muzhskie-dzhammery-antihlor/';
                break;
            case 224: // Очки для плавания детские
                $path = '/ochki-dlya-plavaniya/ochki-dlya-plavaniya-detskie/';
                break;
            case 13: // Стартовые очки
                $path = '/ochki-dlya-plavaniya/startovye-ochki/';
                break;
            case 109: // Сумка
                $path = '/inventar-dlya-plavaniya/sumki/sumka/';
                break;
            case 158: // Перчатки для фитнеса
                $path = '/inventar-dlya-fitnesa/perchatki-dlya-fitnesa/';
                break;
            case 250: // Юниорские плавки антихлор
                $path = '/muzhskie-sportivnye-plavki/plavki-sportivnye-yuniorskie/yuniorskie-plavki-antihlor/';
                break;
            case 84: // Игрушка
                $path = '/inventar-dlya-plavaniya/suvenirnaya-produkciya/igrushka/';
                break;
            case 111: // Тренажер
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/trenazher/';
                break;
            case 134: // Гребной тренажер
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/grebnoy-trenazher/';
                break;
            case 108: // Трос латексный
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/tros-lateksnyy/';
                break;
            case 291: // Сменный ремешок
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/smennyy-remeshok/';
                break;
            case 73: // Тормозной парашют
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/tormoznoy-parashyut/';
                break;
            case 296: // Фиксатор лодыжек
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/fiksator-lodyzhek/';
                break;
            case 230: // Шапочка для душа
                $path = '/shapochki-dlya-plavaniya/shapochka-dlya-dusha/';
                break;
            case 231: // Юниорская силиконовая шапочка
                $path = '/shapochki-dlya-plavaniya/yuniorskaya-pu-shapochka/';
                break;
            case 119: // Турник
                $path = '/inventar-dlya-fitnesa/turnik/';
                break;
            case 144: // Аквапалка
                $path = '/inventar-dlya-plavaniya/akvafitnes/akvapalka/';
                break;
            case 129: // Лопатки
                $path = '/lopatki/lopatki/';
                break;
            case 161: // Поддержка колена
                $path = '/inventar-dlya-fitnesa/podderzhka-kolena/';
                break;
            case 163: // Поддержка лодыжки
                $path = '/inventar-dlya-fitnesa/podderzhka-lodyzhki/';
                break;
            case 99: // Поддержка лодыжки
                $path = '/inventar-dlya-plavaniya/akvafitnes/akvaperchatki/';
                break;
            case 63: // Очки для плавания с диоптриями
                $path = '/ochki-dlya-plavaniya/ochki-dlya-plavaniya-s-dioptriyami/';
                break;
            case 140: // Жилет спасательный
                $path = '/inventar-dlya-plavaniya/dlya-obucheniya-plavaniyu/zhilet-spasatelnyy/';
                break;
            case 170: // Женский купальник спортивный
                $path = '/tekstil-sportivnoe-plavanie/zhenskie-kupalniki-sportivnye/';
                break;
            case 171: // Женский купальник спортивный антихлор
                $path = '/tekstil-sportivnoe-plavanie/zhenskie-kupalniki-sportivnye/';
                break;
            case 173: // Купальник пляжный
                $path = '/tekstil-plyazh/zhenskiy-plyazhnyy-tekstil/kupalnik-plyazhnyy/';
                break;
            case 102: // Силиконовая шапочка
                $path = '/shapochki-dlya-plavaniya/rezinovaya-shapochka/silikonovaya-shapochka/';
                break;
            case 195: // Резиновая шапочка
                $path = '/shapochki-dlya-plavaniya/rezinovaya-shapochka/';
                break;
            case 168: // Спортивная куртка
                $path = '/tekstil-sportivnaya-odezhda/muzhskoy-sportivnyy-zhaket/sportivnaya-kurtka-yuniorskaya/';
                break;
            case 225: // Очки для плавания юниорские
                $path = '/ochki-dlya-plavaniya/ochki-dlya-plavaniya-yuniorskie/';
                break;
            case 87: // Мешок
                $path = '/inventar-dlya-plavaniya/sumki/meshok/';
                break;
            case 294: // Юниорская текстильная шапочка
                $path = '/shapochki-dlya-plavaniya/yuniorskaya-pu-shapochka/yuniorskaya-tekstilnaya-shapochka/';
                break;
            case 138: // Беруши плунжерные
                $path = '/inventar-dlya-plavaniya/berushi-nosovye-zazhimy-antifogi/berushi-plunzhernye/';
                break;
            case 6: // Беруши силиконовые
                $path = '/inventar-dlya-plavaniya/berushi-nosovye-zazhimy/berushi-silikonovye/';
                break;
            case 136: // Носовой зажим
                $path = '/inventar-dlya-plavaniya/berushi-nosovye-zazhimy-antifogi/nosovoy-zazhim/';
                break;
            case 132: // Рюкзак
                $path = '/inventar-dlya-plavaniya/sumki/ryukzak/';
                break;
            case 101: // Акваманжеты
                $path = '/inventar-dlya-plavaniya/akvafitnes/akvamanzhety/';
                break;
            case 100: // Аквапояс
                $path = '/inventar-dlya-plavaniya/akvafitnes/akvapoyas/';
                break;
            case 162: // Поддержка локтя
                $path = '/inventar-dlya-fitnesa/podderzhka-loktya/';
                break;
            case 213: // Плавки
                $path = '/tekstil-plyazh/zhenskiy-plyazhnyy-tekstil/plavki/';
                break;
            case 187: // Купальник для беременных
                $path = '/tekstil-sportivnoe-plavanie/zhenskie-kupalniki-sportivnye/kupalnik-dlya-beremennyh/';
                break;
            case 174: // Купальник пляжный детский
                $path = '/tekstil-plyazh/detskiy-plyazhnyy-tekstil/kupalnik-plyazhnyy-detskiy/';
                break;
            case 74: // Антилопатки
                $path = '/lopatki/antilopatki/';
                break;
            case 154: // Лопатки на пальцы
                $path = '/lopatki/lopatki-na-palcy/';
                break;
            case 289: // Линза с диоптриями для плавательных очков
                $path = '/ochki-dlya-plavaniya/aksessuary-dlya-ochkov/';
                break;
            case 149: // Утяжелитель запястья
                $path = '/inventar-dlya-fitnesa/utyazhelitel-zapyastya/';
                break;
            case 116: // Полотенце
                $path = '/inventar-dlya-plavaniya/polotenca/polotence/';
                break;
            case 197: // Значок
                $path = '/inventar-dlya-plavaniya/suvenirnaya-produkciya/znachok/';
                break;
            case 201: // Тренажер
                $path = '/inventar-dlya-fitnesa/trenazher/';
                break;
            case 244: // Спортивные брюки юниорские
                $path = '/tekstil-sportivnaya-odezhda/muzhskie-sportivnye-bryuki/sportivnye-bryuki-yuniorskie/';
                break;
            case 137: // Набор шапочек для водного поло
                $path = '/vodnoe-polo/shapochki-dlya-vodnogo-polo/nabor-shapochek-dlya-vodnogo-polo/';
                break;
            case 19: // Мяч овальный для фитнеса
                $path = '/inventar-dlya-fitnesa/myach-ovalnyy-dlya-fitnesa/';
                break;
            case 326: // Футболка
                $path = '/suvenirnaya-produkciya/mad-wave-challenge/futbolka/';
                break;
            case 188: // Пояс неопреновый
                $path = '/inventar-dlya-fitnesa/poyas-neoprenovyy/';
                break;
            case 156: // Мужские тапки
                $path = '/obuv/muzhskie-tapki/';
                break;
            case 209: // Женский купальник моделирующий
                $path = '/tekstil-sportivnoe-plavanie/zhenskiy-kupalnik-modeliruyushchiy/';
                break;
            case 143: // Купальник для водного поло антихлор
                $path = '/vodnoe-polo/kupalnik-dlya-vodnogo-polo-antihlor/';
                break;
            case 297: // Шейкер
                $path = '/inventar-dlya-fitnesa/sheyker/';
                break;
            case 17: // Мяч для фитнеса
                $path = '/inventar-dlya-fitnesa/myach-dlya-fitnesa/';
                break;
            case 302: // Детское пончо
                $path = '/inventar-dlya-plavaniya/polotenca/detskoe-poncho/';
                break;
            case 251: // Юниорские плавки-шорты
                $path = '/muzhskie-sportivnye-plavki/plavki-sportivnye-yuniorskie/yuniorskie-plavki-shorty/';
                break;
            case 113: // Поясной тренажер
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/grebnoy-trenazher/';
                break;
            case 159: // Скакалка с неопреновыми ручками
                $path = '/inventar-dlya-fitnesa/skakalka-s-neoprenovymi-ruchkami/';
                break;
            case 120: // Эспандер
                $path = '/inventar-dlya-fitnesa/espander/';
                break;
            case 190: // Ролик
                $path = '/inventar-dlya-fitnesa/rolik/';
                break;
            case 146: // Перчатки с утяжелителями
                $path = '/inventar-dlya-fitnesa/perchatki-s-utyazhelitelyami/';
                break;
            case 47: // Мяч-эспандер
                $path = '/inventar-dlya-fitnesa/myach-espander/';
                break;
            case 196: // Латексная шапочка
                $path = '/shapochki-dlya-plavaniya/rezinovaya-shapochka/lateksnaya-shapochka/';
                break;
            case 248: // Юниорский купальник спортивный антихлор
                $path = '/tekstil-sportivnoe-plavanie/detskie-sportivnye-kupalniki-i-plavki/';
                break;
            case 20: // Женские тапки
                $path = '/obuv/zhenskie-tapki/';
                break;
            case 194: // Женские тапки
                $path = '/inventar-dlya-plavaniya/noski/noski-neoprenovye/';
                break;
            case 228: // Маска для плавания юниорская
                $path = '/ochki-dlya-plavaniya/maska-dlya-plavaniya-yuniorskaya/';
                break;
            case 62: // Маска для плавания
                $path = '/ochki-dlya-plavaniya/maska-dlya-plavaniya/';
                break;
            case 4: // Гель против запотевания
                $path = '/ochki-dlya-plavaniya/aksessuary-dlya-ochkov/sprey-protiv-zapotevaniya/';
                break;
            case 65: // Пояс для обучения плаванию
                $path = '/inventar-dlya-plavaniya/dlya-obucheniya-plavaniyu/poyas-dlya-obucheniya-plavaniyu/';
                break;
            case 126: // Водонепроницаемые наушники
                $path = '/inventar-dlya-plavaniya/elektronnye-ustroystva/vodonepronicaemye-naushniki/';
                break;
            case 118: // Аквадиск
                $path = '/inventar-dlya-plavaniya/akvafitnes/akvadisk/';
                break;
            case 26: // Текстильная шапочка
                $path = '/shapochki-dlya-plavaniya/tekstilnaya-shapochka/';
                break;
            case 96: // Секундомер
                $path = '/inventar-dlya-plavaniya/elektronnye-ustroystva/sekundomer/';
                break;
            case 160: // Скакалка с деревянными ручками
                $path = '/inventar-dlya-fitnesa/skakalka-s-derevyannymi-ruchkami/';
                break;
            case 117: // Скакалка
                $path = '/inventar-dlya-fitnesa/skakalka/';
                break;
            case 249: // Юниорские плавки-шорты антихлор
                $path = '/muzhskie-sportivnye-plavki/plavki-sportivnye-yuniorskie/';
                break;
            case 177: // Топ
                $path = '/tekstil-plyazh/zhenskiy-plyazhnyy-tekstil/top/';
                break;
            case 247: // Юниорский купальник спортивный
                $path = '/tekstil-sportivnoe-plavanie/detskie-sportivnye-kupalniki-i-plavki/';
                break;
            case 123: // Аксессуар для очков
                $path = '/ochki-dlya-plavaniya/aksessuary-dlya-ochkov/aksessuar-dlya-ochkov/';
                break;
            case 216: // Детские плавки-шорты
                $path = '/muzhskie-sportivnye-plavki/detskie-plavki-shorty/';
                break;
            case 105: // Дыхательный тренажер
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/dyhatelnye-trenazhery/';
                break;
            case 106: // Эспандер
                $path = '/inventar-dlya-plavaniya/trenazhery-dlya-plavaniya/espander/';
                break;
            case 246: // Пояс для плавания
                $path = '/inventar-dlya-plavaniya/akvafitnes/poyas-dlya-plavaniya/';
                break;
            case 5: // Доска-калабашка
                $path = '/inventar-dlya-plavaniya/doski-kalabashki/doska-kalabashka/';
                break;
            case 317: // Утяжелитель
                $path = '/inventar-dlya-fitnesa/utyazhelitel/';
                break;
            case 115: // Полотенце из микрофибры
                $path = '/inventar-dlya-plavaniya/polotenca/polotence-iz-mikrofibry/';
                break;
            case 41: // Полотенце мокрое
                $path = '/inventar-dlya-plavaniya/polotenca/polotence-mokroe/';
                break;
            case 148: // Спрей против запотевания
                $path = '/ochki-dlya-plavaniya/aksessuary-dlya-ochkov/sprey-protiv-zapotevaniya/';
                break;
            case 10: // Женский спортивный жакет
                $path = '/tekstil-sportivnaya-odezhda/zhenskiy-sportivnyy-zhaket/';
                break;
            case 67: // Шапочка для водного поло
                $path = '/vodnoe-polo/shapochki-dlya-vodnogo-polo/shapochka-dlya-vodnogo-polo/';
                break;
            case 89: // Калабашка
                $path = '/inventar-dlya-plavaniya/doski-kalabashki/kalabashka/';
                break;
            case 51: // Мужские плавки
                $path = '/muzhskie-sportivnye-plavki/muzhskie-plavki/';
                break;
            case 21: // Коннектор
                $path = '/inventar-dlya-plavaniya/akvafitnes/konnektor/';
                break;
            case 290: // Футляр для плавательных очков
                $path = '/ochki-dlya-plavaniya/aksessuary-dlya-ochkov/futlyar-dlya-plavatelnyh-ochkov/';
                break;
            case 245: // Юниорские джаммеры антихлор
                $path = '/muzhskie-sportivnye-plavki/plavki-sportivnye-yuniorskie/yuniorskie-dzhammery-antihlor/';
                break;
            case 318: // Массажер
                $path = '/inventar-dlya-fitnesa/massazher/';
                break;
            case 179: // Бутылка для воды
                $path = '/inventar-dlya-fitnesa/butylka-dlya-vody/';
                break;
            case 189: // Упоры для отжимания
                $path = '/inventar-dlya-fitnesa/upory-dlya-otzhimaniya/';
                break;
            case 98: // Аквагантели
                $path = '/inventar-dlya-plavaniya/akvafitnes/akvaganteli/';
                break;
            case 45: // Аквагантели
                $path = '/tekstil-sportivnaya-odezhda/zhenskie-sportivnye-bryuki/';
                break;
            case 306: // Кепка
                $path = '/tekstil-sportivnaya-odezhda/kepka/';
                break;
            case 309: // Подвеска
                $path = '/inventar-dlya-plavaniya/suvenirnaya-produkciya/podveska/';
                break;
            case 322: // Флажки
                $path = '/inventar-dlya-plavaniya/suvenirnaya-produkciya/flazhki/';
                break;
            case 307: // Бейдж
                $path = '/inventar-dlya-plavaniya/suvenirnaya-produkciya/akkreditacionnaya-kartochka/';
                break;
            case 308: // Брелок
                $path = '/inventar-dlya-plavaniya/suvenirnaya-produkciya/brelok/';
                break;
            default:
                if (!$category = $this->getLocalCategory()) {
                    return null;
                };
                $category_path = [
                    $category->url_path,
                    $category->url_path_2,
                    $category->url_path_3,
                    $category->url_path_4,
                ];

                foreach ($category_path as $i => $item) {
                    if (empty($item)) {
                        unset($category_path[$i]);
                    }
                }
                $path = '/' . implode('/', $category_path) . '/';
        }

//        $a = [
//            $this->globalCategory->name_ru                         ,
//            $this->globalCategory->parent->name_ru                 ,
//            $this->globalCategory->parent->parent->name_ru         ,
//            $this->globalCategory->parent->parent->parent->name_ru ,
//        ];
//
//        foreach ($a as $i => $item) {
//            if (empty($item)) {
//                unset($a[$i]);
//            }
//        }
//
//        return $domain.'/catalog/'.strtolower(\common\components\CH::transliterate( implode('/', str_replace(' ', '-', array_reverse($a))))).'/'.$this->slug;

        $slug_prefix = '';
        if (!empty($this->url_slug_prefix)) {
            $slug_prefix = $this->url_slug_prefix;
        }

        $url = '/catalog' . $path . $slug_prefix . $this->slug;;

        if ($local) {
            return $url;
        }
        return $domain . $url;
    }

    /**
     * Get all available colors
     *
     * @return array
     */
    public function getColors()
    {
        $data = [];

        if ($variants = $this->getVariantsByColor()) {
            foreach ($variants as $v) {
                if ($v->color) {
                    $data[] = [
                        'sku' => $v->variant_id,
                        'color' => $v->color,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * Get all available sizes by color
     *
     * @param string $color_id Color model id
     * @return array
     */
    public function getSizes($color_id)
    {
        $data = [];
        foreach ($this->getVariantsBySizes(null, $color_id) as $v) {
            $data[] = [
                'sku' => $v->variant_id,
                'size' => $v->size,
                'quantity' => $v->hasInStock()
            ];
        }
        return $data;
    }

    /**
     * Get count of user reviews
     *
     * @return double
     */
    public function getRatingVotes() {
        $session = \Yii::$app->session;
        $session_key = 'testimonials';
        $session_access = $session->has($session_key) ? $session->get($session_key) : [];

        $t1 = Testimonials::find()->where(['product_id' => $this->id])
            ->andWhere(['status' => Testimonials::STATUS_POSTED])
            ->count();

        $t2 = null;
        if (count($session_access) > 0) {
            $t2 = Testimonials::find()->where(['product_id' => $this->id])
                ->andWhere(['id' => $session_access])
                ->andWhere(['status' => Testimonials::STATUS_NEW])
                ->count();
        }

        $votesCount = $t1 + $t2;
        return $votesCount;
    }

    public function getLabel()
    {
        return $this->hasOne(ProductLabel::className(), ['product_id' => 'id']);
    }

    /**
     * Get product rating by user reviews
     *
     * @return double
     */
    public function getRating() {
        $session = \Yii::$app->session;
        $session_key = 'testimonials';
        $session_access = $session->has($session_key) ? $session->get($session_key) : [];

        $t1 = Testimonials::find()->where(['product_id' => $this->id])
            ->andWhere(['status' => Testimonials::STATUS_POSTED])
            ->sum('rating');

        $t2 = null;
        if (count($session_access) > 0) {
            $t2 = Testimonials::find()->where(['product_id' => $this->id])
                ->andWhere(['id' => $session_access])
                ->andWhere(['status' => Testimonials::STATUS_NEW])
                ->sum('rating');
        }
        $tSum = $t1 + $t2;
        $count = $this->getRatingVotes();
        $rating = $tSum / ($count > 0 ? $count : 1);
        return number_format($rating, 1);
    }

    public function getQualityRating()
    {
        $session = \Yii::$app->session;
        $session_key = 'testimonials';
        $session_access = $session->has($session_key) ? $session->get($session_key) : [];

        $t1 = Testimonials::find()->where(['product_id' => $this->id])
            ->andWhere(['status' => Testimonials::STATUS_POSTED])
            ->sum('quantity');

        $t2 = null;
        if (count($session_access) > 0) {
            $t2 = Testimonials::find()->where(['product_id' => $this->id])
                ->andWhere(['id' => $session_access])
                ->andWhere(['status' => Testimonials::STATUS_NEW])
                ->sum('quantity');
        }
        $tSum = $t1 + $t2;
        $count = $this->getRatingVotes();
        $rating = $tSum / ($count > 0 ? $count : 1);
        return number_format($rating, 1);
    }

    public function getSizeMatchRating()
    {
        $session = \Yii::$app->session;
        $session_key = 'testimonials';
        $session_access = $session->has($session_key) ? $session->get($session_key) : [];

        $t1 = Testimonials::find()->where(['product_id' => $this->id])
            ->andWhere(['status' => Testimonials::STATUS_POSTED])
            ->sum('size_match');

        $t2 = null;
        if (count($session_access) > 0) {
            $t2 = Testimonials::find()->where(['product_id' => $this->id])
                ->andWhere(['id' => $session_access])
                ->andWhere(['status' => Testimonials::STATUS_NEW])
                ->sum('size_match');
        }
        $tSum = $t1 + $t2;
        $count = $this->getRatingVotes();
        $rating = $tSum / ($count > 0 ? $count : 1);
        return number_format($rating, 1);
    }

    /**
     * Get global category model
     * @return \yii\db\ActiveQuery
     */
    public function getGlobalCategory()
    {
        return $this->hasOne(Category::className(), ['category_id' => 'category_id']);
    }

    /**
     * Get local category model
     *
     * @return array|\yii\db\ActiveRecord
     */
    public function getLocalCategory()
    {
        if ($lc = Category::findOne(['category_id' => $this->category_id])) {
            return $lc->getLocalCategory();
        };

        return null;
    }

    /**
     * Get product variants models
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVariants()
    {
        return $this->hasMany(ProductVariant::className(), ['product_id' => 'product_id']);
    }


    /**
     * Get all variants grouped by color
     *
     * @todo looks very wired but it is very important method, review this
     * @param int $limit limit for sql query
     * @return ProductVariant
     */
    public function getVariantsByColor($limit = null)
    {

        $data = ProductVariant::find()
            ->select('z.*')
            ->from('(
                SELECT pv.*, `vs`.`quantity`, `vs`.`store_id`
                FROM  product_variant pv
                RIGHT JOIN variant_stock vs ON `vs`.`variant_id` = `pv`.`variant_id`
                WHERE `pv`.`product_id` = ' . $this->product_id . '
                ORDER BY `vs`.`store_id`
            ) as z')
            ->groupBy('z.color_id')
            ->limit($limit)
            ->all();;

        return $data;
    }

    /**
     * Get all variants grouped by size
     *
     * @todo looks very wired but it is very important method, review this
     * @param int $limit limit for sql query
     * @param null $color_id
     * @return ProductVariant
     */
    public function getVariantsBySizes($limit = null, $color_id = null)
    {

        $data = ProductVariant::find()
            ->where(['status' => 0])
            ->innerJoin('price', '`price`.`variant_id` = `product_variant`.`variant_id`')
            ->andWhere(['>', 'price.' . ProductVariant::getPriceColumn(), 0])
            ->andWhere(['product_id' => $this->product_id])
            ;

        if ($color_id !== null) {
            $data->andWhere(['color_id' => $color_id]);
        }

        return $data->groupBy('size_id')->limit($limit)->all();
    }


    /**
     * Wired method for generating product slug
     *
     * @todo think about avoid it also this is must be encapsulated
     * @param int $n iteration key for avoid duplicated slugs
     * @return string
     */
    public function createSlug($n = 0)
    {
        $slug = BaseInflector::slug($this->name);
        if ($n !== 0) {
            $slug .= '-' . $n;
        }
        $is_taken = Product::find()->where(['slug' => $slug])->one();
        if ($is_taken) {
            return $this->createSlug($n + 1);
        }
        return $slug;
    }

    /**
     * Wired method for saving product slug
     *
     * @todo think about avoid it also this is must be encapsulated
     * @return string
     */
    public function generateSlug()
    {
        if (empty($this->slug)) {
            $this->slug = $this->createSlug();
            $this->save(false);
        }
        return $this->slug;
    }

    public function addView()
    {
        $view = new ProductViews();
        $view->product_id = $this->id;
        $view->created_at = time();
        return $view->save(false);
    }

    private function getViews()
    {
        return $this->hasMany(ProductViews::className(), ['product_id' => 'id']);
    }

    public function getTodayViewsCount()
    {
        return $this->getViews()->where(['>', 'created_at', mktime(0,1,0)])->count();
    }

    public function getWeekViewsCount()
    {
        return $this->getViews()->where(['>', 'created_at', strtotime('-7 days', mktime(0,1,0))])->count();
    }

    /**
     * Method for rendering available colors
     *
     * @todo rewrite or avoid $place param implementation
     * @todo move into another class?
     * @param string $current this variant color id
     * @param string $place jedi force implemented key for class which affect view styles
     * @param int $size jedi force key for modal grid size of preview block
     * @return string
     */
//    public function renderColors($current, $place = null, $size = null)
//    {
//        $colorsData = $this->getColors();
//
//        $block = '';
//        foreach ($colorsData as $c) {
//
//            $v = ProductVariant::findOne(['variant_id' => $c['sku']]);
//
//
//            if ($v->status == 0) {
//                if ($place) {
//                    $href = '';
//                } else {
//                    $href = 'href=' . $v->url;
//                }
//
//                $active = '';
//                if ($current == $c['color']['color_id']) {
//                    $active = 'active';
//                }
//
//                $block .= '<span
//                            class="' . 'colorpick c' . $c['color']['color_id'] . ' ' . $place . ' ' . $active . '"
//                            ' . $href . '
//                            title="' . $c['color']['en'] . '"
//                            data-sku="' . $c['sku'] . '"
//                            data-size="' . $size . '"
//                            ></span>';
//            }
//
//
//        }
//        return $block;
//    }

    public function renderColors($current, $product_id)
    {
        $colorsData = $this->getColors();
        $items = [];

        foreach ($colorsData as $c) {
            $v = ProductVariant::findOne(['variant_id' => $c['sku']]);
            if ($v->status == 0) {
                $items[$c['color']['color_id']] = $c['color']['ru'];
            }
        }

        return Html::dropDownList('color', $current, $items, [
            'class' => 'form-control',
            'style' => 'width: auto',
            'id' => 'color-picker',
            'data-product_id' => $product_id
        ]);
    }

    /**
     * Method for rendering available sizes by color_id
     *
     * @todo move into another class?
     * @param string $color_id color model id
     * @param string $current current size id
     * @param null $prompt
     * @return string | array
     */
    public function renderSizes($color_id, $current = null, $prompt = null, $items_only = false, $product_id = null)
    {
        $sizeData = $this->getSizes($color_id);
        $items = [];
        $outOfStock = [];

        foreach ($sizeData as $s) {
            $items[$s['size']['size_id']] = $s['size']['name'];
            if (!$s['quantity']) {
                $outOfStock[$s['size']['size_id']] = ['style' => 'background: #ccc'];
            }
        }

        if ($items_only) {
            return $items;
        }

        return Html::dropDownList('size', $prompt !== null ? $prompt : $current, $items, [
            'prompt' => $prompt,
            'class' => 'form-control',
            'id' => 'size-picker',
            'style' => 'width: auto',
            'options' => $outOfStock,
            'data-product_id' => $product_id
        ]);

    }

    public function getDescription($hideDetails = false) {
        $description = Local::choose([
            Local::RU => $this->desc_ru,
            Local::EU => $this->desc_en
        ]);

        if ($hideDetails)
        {
            $dom = new \DOMDocument;
            libxml_use_internal_errors(true);
            $html = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');
            if (!empty($html) && $html != '') {
                $dom->loadHTML($html);
                $xPath = new \DOMXPath($dom);
                $nodes = $xPath->query('//div');
                if($nodes->item(0)) {
                    $nodes->item(0)->parentNode->removeChild($nodes->item(0));
                }
                $nodes = $xPath->query('//ul');
                if($nodes->item(0)) {
                    $nodes->item(0)->parentNode->removeChild($nodes->item(0));
                }
                $description = $dom->saveHTML();
            }
        }

        return $description;
    }

    public function getFeaturesDescription()
    {
        $features = [];

        $description = Local::choose([
            Local::RU => $this->desc_ru,
            Local::EU => $this->desc_en
        ]);

        $dom = new \DOMDocument;
        $html = mb_convert_encoding($description, 'HTML-ENTITIES', 'UTF-8');

        if (!empty($html) && $html != '') {
            $dom->loadHTML($html);
            $xPath = new \DOMXPath($dom);
            $nodes = $xPath->query('//ul/li');
            foreach ($nodes as $node)
            {
                $string = $node->nodeValue;

                $string = str_replace('–', '-', $string);

                $stringParts = explode('-', $string, 2);

                $stringParts[0] = '<strong>'.$stringParts[0].'</strong>';

                $features[] = $stringParts[0].' - '.$stringParts[1];
            }
        }

        return $features;
    }

    public function getOverriddenMeta()
    {
        return $this->hasOne(OverriddenMeta::className(), ['product_id' => 'product_id']);
    }

    /**
     * @author Vyacheslav Elchin <lexscite@gmail.com>
     * @param $value string
     * @return $this Product
     */
    public function setDescEn($value)
    {
        $this->desc_en = $this->closeUnclosedTags($value);
        return $this;
    }

    /**
     * @author Vyacheslav Elchin <lexscite@gmail.com>
     * @param $value string
     * @return $this Product
     */
    public function setDescRu($value)
    {
        $this->desc_ru = $this->closeUnclosedTags($value);
        return $this;
    }

    /**
     * Add closing html tag for each unclosed one.
     *
     * @param $content string to work with
     * @return string same string but with every tags closed
     * @author Vyacheslav Elchin <lexscite@gmail.com>
     */
    public function closeUnclosedTags($content)
    {
        $position = 0;
        $open_tags = [];

        $ignored_tags = ['br', 'hr', 'img'];

        while (($position = strpos($content, '<', $position)) !== FALSE)
        {
            if (preg_match("|^<(/?)([a-z\d]+)\b[^>]*>|i", substr($content, $position), $match))
            {
                $tag = strtolower($match[2]);
                if (in_array($tag, $ignored_tags) == FALSE)
                {
                    if (isset($match[1]) AND $match[1] == '')
                    {
                        if (isset($open_tags[$tag]))
                        {
                            $open_tags[$tag]++;
                        }
                        else
                        {
                            $open_tags[$tag] = 1;
                        }
                    }
                    if (isset($match[1]) AND $match[1] == '/')
                    {
                        if (isset($open_tags[$tag]))
                            $open_tags[$tag]--;
                    }
                }
                $position += strlen($match[0]);
            }
            else
            {
                $position++;
            }
        }
        foreach ($open_tags as $tag => $count_not_closed)
        {
            if ($count_not_closed >= 0) {
                $content .= str_repeat("</{$tag}>", $count_not_closed);
            }
        }

        return $content;
    }

    public function getComposition() {
        return Local::choose([
            Local::RU => $this->compositions,
            Local::EU => $this->compositions_en
        ]);
    }

    public function getMetaDescription()
    {
        $mDesc = Local::choose([
            Local::RU => $this->desc_ru,
            Local::EU => $this->desc_en
        ]);

        if (Local::detectLocal() == Local::EU) {
            if ($diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => ScreamingCrawl::cutUrl($this->getUrl())])->one()) {
                $mDesc = $diff->MetaDescription1;
            };
        }

        return $mDesc;
    }

    public function getPageTitle()
    {
        $title = $this->name . ' | ' .$this->globalCategory->getName() . ' | ' . 'Mad Wave';

        if ($diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => ScreamingCrawl::cutUrl($this->getUrl())])->one()) {
            $title = $diff->Title1;
        };
        return $title;
    }

    public function getH1()
    {
        $h1 = $this->name;

        if ($diff = CrawlDiffs::find()->where(['CanonicalLinkElement1' => ScreamingCrawl::cutUrl($this->getUrl())])->one()) {
            $h1 = $diff->H11;
        };
        return $h1;
    }

    public function getTargetVariant() {
        return $this->hasOne(ProductVariant::className(), ['product_id' => 'product_id'])
            ->where(['status' => 0])
            ->andWhere(['color_id' => $this->target_color])
        ;
    }

    public function getColorSizePickerData()
    {
        $skus = ProductVariant::find()
            ->joinWith('color c')
            ->joinWith('size s')
            ->joinWith('stock b')
            ->joinWith('priceList p')
            ->where(['product_id' => $this->getId()])
            ->andWhere(['>', 'p.price_rub', 0])
            ->andWhere(['product_variant.status' => 0])
            ->all();


        $colors = [];

        foreach ($skus as $sku) {
            /**
             * @var $sku ProductVariant
             */
            $c_id = $sku->color_id;
            $colors[$c_id]['label'] = $sku->color->name;
            $colors[$c_id]['sku'] = $sku->variant_id;
            $colors[$c_id]['image_url'] = $sku->getImage(1, $sku::IMG_SMALL, true, true);
            $colors[$c_id]['sizes'][$sku->size_id]['label'] = $sku->size->name;
            $colors[$c_id]['sizes'][$sku->size_id]['available'] = $sku->getStock()->one()->quantity > 0;
            $colors[$c_id]['sizes'][$sku->size_id]['sku'] = $sku->variant_id;
        }

        $isOneColorOneSize = true;
        foreach ($colors as $c_id => $c) {
            $available = false;
            foreach ($c['sizes'] as $s_id => $size) {
                if ($c['sizes'][$s_id]['available']) {
                    $available = true;
                }
            }
            $colors[$c_id]['available'] = $available;
            if (count($c['sizes']) !== 1) {
                $isOneColorOneSize = false;
            }
        }

        return [
            'colors' => $colors,
            'isOneColorOneSize' => $isOneColorOneSize
        ];
    }

    public function getImageAlt()
    {
        return $this->globalCategory->getName(). ' ' . $this->name;
    }

    public function getTopBannerProducts()
    {
        return $this->hasOne(TopBannerProducts::className(), ['product_id' => 'id']);
    }

    public function getTopBanner()
    {
        return $this->hasOne(TopBanner::className(), ['id' => 'banner_id'])
            ->via('topBannerProducts')
            ->where([TopBanner::tableName().'.status' => TopBanner::STATUS_POSTED]);
    }
}

