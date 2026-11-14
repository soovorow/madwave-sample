<?php
namespace frontend\controllers;

use common\components\multilocal\Local;
use common\models\OrderMetrics;
use common\models\ProductVariant;
use common\models\RelatedProducts;
use frontend\modules\checkout\eu\Module;
use Google_Client;
use Google_Service_Analytics;
use Yii;
use yii\helpers\BaseHtml;
use yii\helpers\Html;
use yii\web\Controller;

class GoogleController extends \yii\web\Controller {
    
    private function getAuthConfig() {
        return __DIR__.'/../views/google/MadWave-9c555fc9ca9b.json';
    }

    public function actionAnalytics() {
        return $this->render('analytics');
    }

    public static function actionRelatedProducts() {
        $client = new Google_Client();
        $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
        $client->setAuthConfig(self::getAuthConfig());
        $analytics = new Google_Service_Analytics($client);
        $relatedProducts = $analytics
            ->data_ga
            ->get(
                'ga:123550906',
                date('Y-m-d', strtotime("-1 month")),
                date('Y-m-d'),
                'ga:correlationScore',
                [
                    'dimensions' => 'ga:queryProductId,ga:relatedProductId',
                    'max-results' => 3000
                ]
            );

        if (!empty($relatedProducts['rows'])) {
            RelatedProducts::tableName();
            Yii::$app->madwave_rpoducts->createCommand()->truncateTable('related_products')->execute();
            foreach ($relatedProducts['rows'] as $row) {

                $pid = ProductVariant::find()->where(['like', 'vendore_code', $row[0]])->one();
                $rpid = ProductVariant::find()->where(['like', 'vendore_code', $row[1]])->one();
                if ($pid && $rpid) {
                    $rp = new RelatedProducts();
                    $rp->product_id = $pid->product->id;
                    $rp->related_product_id = $rpid->product->id;
                    $rp->correlation_score = $row[2];
                    $rp->save(false);
                }
            }
        }

        return true;
    }

    public static function actionOrderMetrics() {
        $client = new Google_Client();
        $client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));
        $client->setAuthConfig(self::getAuthConfig());
        $analytics = new Google_Service_Analytics($client);
        $metrics = $analytics
            ->data_ga
            ->get(
                'ga:123550906',
                date('Y-m-d', strtotime("-3 month")),
                date('Y-m-d'),
                'ga:uniquePurchases',
                [
                    'dimensions' => 'ga:productSku',
                    'sort' => '-ga:uniquePurchases',
                    'max-results' => 3000
                ]
            );

        if (!empty($metrics['rows'])) {
            OrderMetrics::tableName();
            Yii::$app->madwave_rpoducts->createCommand()->truncateTable('product_order_metrics')->execute();
            foreach ($metrics['rows'] as $row) {
                $pid = ProductVariant::find()->where(['like', 'vendore_code', $row[0]])->one();
                if ($pid) {
                    $om = new OrderMetrics();
                    $om->product_id = $pid->product->id;
                    $om->ctr = $row[1];
                    $om->save(false);
                }
            }
        }

        // TODO use for sort in search model

        return true;
    }

    public function actionGiveAShitAboutJob()
    {
        $manager = new Manager();

        $worker = new Worker();

        $worker->makeAppearenceOfWorkinOn(function($I) {
            $I->learn(Android());
            $I->remove($I->code(new StupidThing));
            $I->goTo($I->smokeSigarent);

            if (time() >= $I->getTimeToGoHome()) {
                $I->working = false;
            }
        });

        $worker->campAndWait();
        $worker->continueLearningAndorid();


        $worker->continueToGiveATotalShit();




    }

    public static function actionMerchant()
    {
        $filename = 'google_merchant.xml';
        $file = Yii::getAlias('@frontend/web').'/'.$filename;

        $title = 'Mad Wave RU - Online Store';
        $link = 'https://www.madwave.ru/';
        $description = '';

        $channelContent = [
            Html::tag('title', $title),
            "<link>$link</link>",
            Html::tag('description', $description),
        ];

        $products = ProductVariant::find()->where(1)
            ->innerJoin('product', 'product.product_id = product_variant.product_id')
            ->andWhere(['product.status' => 0])
            ->innerJoin('price', 'price.variant_id = product_variant.variant_id')
            ->andWhere(['>', 'price.price_rub', 0])
            ->rightJoin('variant_stock vs', 'vs.variant_id = product_variant.variant_id')
            ->andWhere(['vs.store_id' => Local::choose([
                Local::RU => '00005',
                Local::EU => '00049',
            ])])
            ->andWhere(['>', 'vs.quantity', 0])
            ->groupBy('product_variant.variant_id')
            ->all();

        if ($products) {
            foreach ($products as $key => $product) {

                /**
                 * @var $product ProductVariant
                 */

                $prices = $product->getPrices();

                $price = $prices['new'];
                $oldPrice = $prices['old'];

                $description = strip_tags($product->product->desc_ru);
                $description = str_replace("&nbsp;", " ", $description);

                $category = $product->product->getLocalCategory();

                $item = [
                    Html::tag('g:id', $product->variant_id),
                    Html::tag('g:title', $category->yandex_market_title .' '. $product->name),
                    Html::tag('g:description', $description),
                    Html::tag('g:link', $product->getUrl()),
                    Html::tag('g:image_link', $product->getImage(1, $product::IMG_ORIGINAL, true, true)),
                    Html::tag('g:condition', 'new'),
                    Html::tag('g:availability', 'in stock'),
                    Html::tag('g:brand ', 'Mad Wave'),
                    Html::tag('g:identifier_exists ', 'no'),
                    Html::tag('g:google_product_category ', $category->google_taxanomy_key),
                ];

                $price = Html::tag('g:price', $price.' RUB');
//
//                if($oldPrice > $price) {
//                    $discount = 100 - ($price / ($oldPrice / 100));
//                    if ($discount < 95 && $discount > 5) {
//                        $price = Html::tag('g:price', $oldPrice.' RUB');
//                        $salePrice = Html::tag('g:sale_​price ', $price.' RUB');
//                    }
//                }

                $item[] = $price;
//                $item[] = $salePrice;

                if ($product->color) {
                    $item[] = Html::tag('g:color', $product->color->name);
                }

                if ($product->size) {
                    $item[] = Html::tag('g:size', $product->size->name);
                }

                $channelContent[] = Html::tag('item', implode($item));
            }
        }

        $channel = Html::tag('channel', implode($channelContent));

        $rss = Html::tag('rss', $channel, [
            'xmlns:g' => 'http://base.google.com/ns/1.0',
            'version' => '2.0'
        ]);

        file_put_contents($file, "\xEF\xBB\xBF" . $rss);
//        return \Yii::$app->response->sendFile($file);
    }

    public static function taxonomy() {
        return [
            166	    => 'Apparel & Accessories',
            1604	=> 'Apparel & Accessories	Clothing',
            5322	=> 'Apparel & Accessories	Clothing	Activewear',
            182 	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing',
            5408	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Bottoms',
            5549	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Diaper Covers',
            5424	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Dresses',
            5425	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Outerwear',
            5622	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Outfits',
            5412	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Sleepwear',
            5423	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Socks & Tights',
            5409	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Swimwear',
            5410	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby & Toddler Tops',
            5411	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Baby One-Pieces',
            5621	=> 'Apparel & Accessories	Clothing	Baby & Toddler Clothing	Toddler Underwear',
            2271	=> 'Apparel & Accessories	Clothing	Dresses',
            5182	=> 'Apparel & Accessories	Clothing	One-Pieces',
            5250	=> 'Apparel & Accessories	Clothing	One-Pieces	Jumpsuits & Rompers',
            5490	=> 'Apparel & Accessories	Clothing	One-Pieces	Leotards & Unitards',
            7132	=> 'Apparel & Accessories	Clothing	One-Pieces	Overalls',
            203	    => 'Apparel & Accessories	Clothing	Outerwear',
            5506	=> 'Apparel & Accessories	Clothing	Outerwear	Chaps',
            5598	=> 'Apparel & Accessories	Clothing	Outerwear	Coats & Jackets',
            5514	=> 'Apparel & Accessories	Clothing	Outerwear	Rain Pants',
            3066	=> 'Apparel & Accessories	Clothing	Outerwear	Rain Suits',
            5909	=> 'Apparel & Accessories	Clothing	Outerwear	Snow Pants & Suits',
            1831	=> 'Apparel & Accessories	Clothing	Outerwear	Vests',
            7313	=> 'Apparel & Accessories	Clothing	Outfit Sets',
            204	    => 'Apparel & Accessories	Clothing	Pants',
            212	    => 'Apparel & Accessories	Clothing	Shirts & Tops',
            207	    => 'Apparel & Accessories	Clothing	Shorts',
            1581	=> 'Apparel & Accessories	Clothing	Skirts',
            5344	=> 'Apparel & Accessories	Clothing	Skorts',
            1594	=> 'Apparel & Accessories	Clothing	Suits',
            5183	=> 'Apparel & Accessories	Clothing	Suits	Pant Suits',
            1516	=> 'Apparel & Accessories	Clothing	Suits	Skirt Suits',
            1580	=> 'Apparel & Accessories	Clothing	Suits	Tuxedos',
            211	    => 'Apparel & Accessories	Clothing	Swimwear',
            213	    => 'Apparel & Accessories	Clothing	Underwear & Socks',
            7207	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Bra Accessories',
            7208	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Bra Accessories	Bra Strap Pads',
            7211	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Bra Accessories	Bra Straps & Extenders',
            7210	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Bra Accessories	Breast Enhancing Inserts',
            7209	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Bra Accessories	Breast Petals & Concealers',
            214	    => 'Apparel & Accessories	Clothing	Underwear & Socks	Bras',
            215	    => 'Apparel & Accessories	Clothing	Underwear & Socks	Hosiery',
            5327	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Jock Straps',
            1772	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Lingerie',
            2563	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Lingerie Accessories',
            2160	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Lingerie Accessories	Garter Belts',
            1675	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Lingerie Accessories	Garters',
            1807	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Long Johns',
            2963	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Petticoats & Pettipants',
            1578	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Shapewear',
            209	    => 'Apparel & Accessories	Clothing	Underwear & Socks	Socks',
            2745	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Undershirts',
            2562	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Underwear',
            5834	=> 'Apparel & Accessories	Clothing	Underwear & Socks	Underwear Slips',
            2306	=> 'Apparel & Accessories	Clothing	Uniforms',
            3598	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms',
            3683	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Cheerleading Uniforms',
            3724	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Cricket Uniforms		',
            3888	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Football Uniforms		',
            3958	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Hockey Uniforms		',
            4003	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Martial Arts Uniforms		',
            3253	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Officiating Uniforms		',
            5564	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Soccer Uniforms		',
            3379	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Softball Uniforms		',
            3852	=> 'Apparel & Accessories	Clothing	Uniforms	Sports Uniforms	Wrestling Uniforms		',
            2292	=> 'Apparel & Accessories	Clothing	Uniforms	White Coats			',
            5441	=> 'Apparel & Accessories	Clothing	Wedding & Bridal Party Dresses				',
            5330	=> 'Apparel & Accessories	Clothing	Wedding & Bridal Party Dresses	Bridal Party Dresses			',
            5329	=> 'Apparel & Accessories	Clothing	Wedding & Bridal Party Dresses	Wedding Dresses			',
            167	    => 'Apparel & Accessories	Clothing Accessories					',
            5942	=> 'Apparel & Accessories	Clothing Accessories	Arm Warmers & Sleeves				',
            5422	=> 'Apparel & Accessories	Clothing Accessories	Baby & Toddler Clothing Accessories				',
            5623	=> 'Apparel & Accessories	Clothing Accessories	Baby & Toddler Clothing Accessories	Baby & Toddler Belts			',
            5624	=> 'Apparel & Accessories	Clothing Accessories	Baby & Toddler Clothing Accessories	Baby & Toddler Gloves & Mittens			',
            5625	=> 'Apparel & Accessories	Clothing Accessories	Baby & Toddler Clothing Accessories	Baby & Toddler Hats			',
            5626	=> 'Apparel & Accessories	Clothing Accessories	Baby & Toddler Clothing Accessories	Baby Protective Wear			',
            1786	=> 'Apparel & Accessories	Clothing Accessories	Balaclavas				',
            168	    => 'Apparel & Accessories	Clothing Accessories	Bandanas & Headties				',
            3913	=> 'Apparel & Accessories	Clothing Accessories	Belt Buckles				',
            169	    => 'Apparel & Accessories	Clothing Accessories	Belts				',
            5443	=> 'Apparel & Accessories	Clothing Accessories	Bridal Accessories				',
            5446	=> 'Apparel & Accessories	Clothing Accessories	Bridal Accessories	Bridal Veils			',
            6985	=> 'Apparel & Accessories	Clothing Accessories	Button Studs				',
            6984	=> 'Apparel & Accessories	Clothing Accessories	Collar Stays				',
            193	    => 'Apparel & Accessories	Clothing Accessories	Cufflinks				',
            5114	=> 'Apparel & Accessories	Clothing Accessories	Decorative Fans				',
            6238	=> 'Apparel & Accessories	Clothing Accessories	Earmuffs				',
            170	    => 'Apparel & Accessories	Clothing Accessories	Gloves & Mittens				',
            171	    => 'Apparel & Accessories	Clothing Accessories	Hair Accessories				',
            8451	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Bun & Volume Shapers			',
            2477	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Combs			',
            4057	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Extensions			',
            1948	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Forks & Sticks			',
            6183	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Nets			',
            502988	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Pins, Claws & Clips			',
            5915	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Hair Wreaths			',
            1662	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Headbands			',
            1483	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Ponytail Holders			',
            5914	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Tiaras			',
            7305	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Wig Accessories			',
            7307	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Wig Accessories	Wig Caps		',
            7306	=> 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Wig Accessories	Wig Glue & Tape		',
            181	    => 'Apparel & Accessories	Clothing Accessories	Hair Accessories	Wigs			',
            7133	=> 'Apparel & Accessories	Clothing Accessories	Hand Muffs				',
            5207	=> 'Apparel & Accessories	Clothing Accessories	Handkerchiefs				',
            173	    => 'Apparel & Accessories	Clothing Accessories	Hats				',
            2020	=> 'Apparel & Accessories	Clothing Accessories	Headwear				',
            7054	=> 'Apparel & Accessories	Clothing Accessories	Headwear	Fascinators			',
            1922	=> 'Apparel & Accessories	Clothing Accessories	Headwear	Headdresses			',
            5939	=> 'Apparel & Accessories	Clothing Accessories	Headwear	Turbans			',
            5941	=> 'Apparel & Accessories	Clothing Accessories	Leg Warmers				',
            6268	=> 'Apparel & Accessories	Clothing Accessories	Leis				',
            502987	=> 'Apparel & Accessories	Clothing Accessories	Maternity Belts & Support Bands				',
            7230	=> 'Apparel & Accessories	Clothing Accessories	Neck Gaiters				',
            176	    => 'Apparel & Accessories	Clothing Accessories	Neckties				',
            4179	=> 'Apparel & Accessories	Clothing Accessories	Pinback Buttons				',
            499972	=> 'Apparel & Accessories	Clothing Accessories	Sashes				',
            177	    => 'Apparel & Accessories	Clothing Accessories	Scarves & Shawls				',
            178	    => 'Apparel & Accessories	Clothing Accessories	Sunglasses				',
            179	    => 'Apparel & Accessories	Clothing Accessories	Suspenders				',
            180	    => 'Apparel & Accessories	Clothing Accessories	Tie Clips				',
            5390	=> 'Apparel & Accessories	Clothing Accessories	Traditional Clothing Accessories				',
            5687	=> 'Apparel & Accessories	Clothing Accessories	Traditional Clothing Accessories	Obis			',
            5685	=> 'Apparel & Accessories	Clothing Accessories	Traditional Clothing Accessories	Tabi Socks			',
            1893	=> 'Apparel & Accessories	Clothing Accessories	Wristbands				',
            184     => 'Apparel & Accessories	Costumes & Accessories					',
            5192	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories				',
            7304	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Bald Caps			',
            8017	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Costume Accessory Sets			',
            5907	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Costume Capes			',
            8200	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Costume Gloves			',
            5426	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Costume Hats			',
            500118	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Costume Special Effects			',
            500008	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Costume Tobacco Products			',
            8018	=> 'Apparel & Accessories	Costumes & Accessories	Costume Accessories	Pretend Jewelry			',
            5387	=> 'Apparel & Accessories	Costumes & Accessories	Costume Shoes				',
            5193	=> 'Apparel & Accessories	Costumes & Accessories	Costumes				',
            5194	=> 'Apparel & Accessories	Costumes & Accessories	Masks				',
            6552	=> 'Apparel & Accessories	Handbag & Wallet Accessories					',
            6460	=> 'Apparel & Accessories	Handbag & Wallet Accessories	Checkbook Covers				',
            175	    => 'Apparel & Accessories	Handbag & Wallet Accessories	Keychains				',
            6277	=> 'Apparel & Accessories	Handbag & Wallet Accessories	Lanyards				',
            5841	=> 'Apparel & Accessories	Handbag & Wallet Accessories	Wallet Chains				',
            6551	=> 'Apparel & Accessories	Handbags, Wallets & Cases					',
            6170	=> 'Apparel & Accessories	Handbags, Wallets & Cases	Badge & Pass Holders				',
            6169	=> 'Apparel & Accessories	Handbags, Wallets & Cases	Business Card Cases				',
            3032	=> 'Apparel & Accessories	Handbags, Wallets & Cases	Handbags				',
            2668	=> 'Apparel & Accessories	Handbags, Wallets & Cases	Wallets & Money Clips	',
            1933	=> 'Apparel & Accessories	Shoe Accessories					',
            5567	=> 'Apparel & Accessories	Shoe Accessories	Boot Liners				',
            7078	=> 'Apparel & Accessories	Shoe Accessories	Gaiters				',
            5385	=> 'Apparel & Accessories	Shoe Accessories	Shoe Covers				',
            1856	=> 'Apparel & Accessories	Shoe Accessories	Shoelaces				',
            2427	=> 'Apparel & Accessories	Shoe Accessories	Spurs				',
            187	    => 'Apparel & Accessories	Shoes	',
            537	    => 'Baby & Toddler',
            4678	=> 'Baby & Toddler	Baby Bathing',
            4679	=> 'Baby & Toddler	Baby Bathing	Baby Bathtubs & Bath Seats',
            7082	=> 'Baby & Toddler	Baby Bathing	Shower Visors',
            5859	=> 'Baby & Toddler	Baby Gift Sets',
            5252	=> 'Baby & Toddler	Baby Health',
            6290	=> 'Baby & Toddler	Baby Health	Baby Health & Grooming Kits',
            5253	=> 'Baby & Toddler	Baby Health	Nasal Aspirators',
            7016	=> 'Baby & Toddler	Baby Health	Pacifier Clips & Holders',
            7309	=> 'Baby & Toddler	Baby Health	Pacifier Wipes',
            566	    => 'Baby & Toddler	Baby Health	Pacifiers & Teethers',
            540	    => 'Baby & Toddler	Baby Safety',
            6869	=> 'Baby & Toddler	Baby Safety	Baby & Pet Gate Accessories',
            542	    => 'Baby & Toddler	Baby Safety	Baby & Pet Gates',
            541	    => 'Baby & Toddler	Baby Safety	Baby Monitors',
            5049	=> 'Baby & Toddler	Baby Safety	Baby Safety Harnesses & Leashes',
            543	    => 'Baby & Toddler	Baby Safety	Baby Safety Locks & Guards',
            544	    => 'Baby & Toddler	Baby Safety	Baby Safety Rails',
            2847	=> 'Baby & Toddler	Baby Toys & Activity Equipment',
            3661	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Alphabet Toys',
            7198	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Activity Toys',
            555	    => 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Bouncers & Rockers',
            560	    => 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Jumpers & Swings',
            7191	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Mobile Accessories',
            1242	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Mobiles',
            7360	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Soothers',
            1241	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Baby Walkers & Entertainers',
            1243	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Play Mats & Gyms',
            539	    => 'Baby & Toddler	Baby Toys & Activity Equipment	Play Yards',
            3459	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Push & Pull Toys',
            1244	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Rattles',
            3860	=> 'Baby & Toddler	Baby Toys & Activity Equipment	Sorting & Stacking Toys',
            2764	=> 'Baby & Toddler	Baby Transport',
            547	    => 'Baby & Toddler	Baby Transport	Baby & Toddler Car Seats',
            538	    => 'Baby & Toddler	Baby Transport	Baby Carriers',
            568	    => 'Baby & Toddler	Baby Transport	Baby Strollers',
            4386	=> 'Baby & Toddler	Baby Transport Accessories',
            4486	=> 'Baby & Toddler	Baby Transport Accessories	Baby & Toddler Car Seat Accessories',
            4916	=> 'Baby & Toddler	Baby Transport Accessories	Baby Carrier Accessories',
            4387	=> 'Baby & Toddler	Baby Transport Accessories	Baby Stroller Accessories',
            8537	=> 'Baby & Toddler	Baby Transport Accessories	Baby Transport Liners & Sacks',
            5845	=> 'Baby & Toddler	Baby Transport Accessories	Shopping Cart & High Chair Covers',
            548	    => 'Baby & Toddler	Diapering',
            7200	=> 'Baby & Toddler	Diapering	Baby Wipe Dispensers & Warmers',
            553	    => 'Baby & Toddler	Diapering	Baby Wipes',
            502999	=> 'Baby & Toddler	Diapering	Changing Mat & Tray Covers',
            5628	=> 'Baby & Toddler	Diapering	Changing Mats & Trays',
            7014	=> 'Baby & Toddler	Diapering	Diaper Kits',
            6949	=> 'Baby & Toddler	Diapering	Diaper Liners',
            6883	=> 'Baby & Toddler	Diapering	Diaper Organizers',
            7001	=> 'Baby & Toddler	Diapering	Diaper Pail Accessories',
            550	    => 'Baby & Toddler	Diapering	Diaper Pails',
            2949	=> 'Baby & Toddler	Diapering	Diaper Rash Treatments',
            6971	=> 'Baby & Toddler	Diapering	Diaper Wet Bags',
            551	    => 'Baby & Toddler	Diapering	Diapers',
            561	    => 'Baby & Toddler	Nursing & Feeding',
            562	    => 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food',
            5721	=> 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food	Baby Cereal',
            5718	=> 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food	Baby Drinks',
            5719	=> 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food	Baby Food',
            563	    => 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food	Baby Formula',
            5720	=> 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food	Baby Snacks',
            8436	=> 'Baby & Toddler	Nursing & Feeding	Baby & Toddler Food	Toddler Nutrition Drinks & Shakes',
            5630	=> 'Baby & Toddler	Nursing & Feeding	Baby Bottle Nipples & Liners',
            564	    => 'Baby & Toddler	Nursing & Feeding	Baby Bottles',
            4768	=> 'Baby & Toddler	Nursing & Feeding	Baby Care Timers',
            2125	=> 'Baby & Toddler	Nursing & Feeding	Bibs',
            5296	=> 'Baby & Toddler	Nursing & Feeding	Bottle Warmers & Sterilizers',
            7234	=> 'Baby & Toddler	Nursing & Feeding	Breast Milk Storage Containers',
            505366	=> 'Baby & Toddler	Nursing & Feeding	Breast Pump Accessories',
            565	    => 'Baby & Toddler	Nursing & Feeding	Breast Pumps',
            5629	=> 'Baby & Toddler	Nursing & Feeding	Burp Cloths',
            5843	=> 'Baby & Toddler	Nursing & Feeding	Nursing Covers',
            503762	=> 'Baby & Toddler	Nursing & Feeding	Nursing Pads & Shields',
            8075	=> 'Baby & Toddler	Nursing & Feeding	Nursing Pillow Covers',
            5298	=> 'Baby & Toddler	Nursing & Feeding	Nursing Pillows',
            6950	=> 'Baby & Toddler	Nursing & Feeding	Sippy Cups',
            6952	=> 'Baby & Toddler	Potty Training',
            552	    => 'Baby & Toddler	Potty Training	Potty Seats',
            6953	=> 'Baby & Toddler	Potty Training	Potty Training Kits',
            6899	=> 'Baby & Toddler	Swaddling & Receiving Blankets	        ',
            222	    => 'Electronics						',
            3356	=> 'Electronics	Arcade Equipment					',
            8085	=> 'Electronics	Arcade Equipment	Basketball Arcade Games				',
            3946	=> 'Electronics	Arcade Equipment	Pinball Machine Accessories				',
            3140	=> 'Electronics	Arcade Equipment	Pinball Machines				',
            3681	=> 'Electronics	Arcade Equipment	Skee-Ball Machines				',
            3676	=> 'Electronics	Arcade Equipment	Video Game Arcade Cabinet Accessories				',
            3117	=> 'Electronics	Arcade Equipment	Video Game Arcade Cabinets				',
            223	    => 'Electronics	Audio					',
            1420	=> 'Electronics	Audio	Audio Accessories				',
            503008	=> 'Electronics	Audio	Audio Accessories	Audio & Video Receiver Accessories			',
            505797	=> 'Electronics	Audio	Audio Accessories	Headphone & Headset Accessories			',
            503004	=> 'Electronics	Audio	Audio Accessories	Headphone & Headset Accessories	Headphone Cushions & Tips		',
            5395	=> 'Electronics	Audio	Audio Accessories	Karaoke System Accessories			',
            5396	=> 'Electronics	Audio	Audio Accessories	Karaoke System Accessories	Karaoke Chips		',
            232	    => 'Electronics	Audio	Audio Accessories	MP3 Player Accessories			',
            7566	=> 'Electronics	Audio	Audio Accessories	MP3 Player Accessories	MP3 Player & Mobile Phone Accessory Sets		',
            3055	=> 'Electronics	Audio	Audio Accessories	MP3 Player Accessories	MP3 Player Cases		',
            3306	=> 'Electronics	Audio	Audio Accessories	Microphone Accessories			',
            3912	=> 'Electronics	Audio	Audio Accessories	Microphone Stands			',
            239	    => 'Electronics	Audio	Audio Accessories	Satellite Radio Accessories			',
            7163	=> 'Electronics	Audio	Audio Accessories	Speaker Accessories			',
            500112	=> 'Electronics	Audio	Audio Accessories	Speaker Accessories	Speaker Bags, Covers & Cases		',
            500120	=> 'Electronics	Audio	Audio Accessories	Speaker Accessories	Speaker Components & Kits		',
            8047	=> 'Electronics	Audio	Audio Accessories	Speaker Accessories	Speaker Stand Bags		',
            8049	=> 'Electronics	Audio	Audio Accessories	Speaker Accessories	Speaker Stands & Mounts		',
            500119	=> 'Electronics	Audio	Audio Accessories	Speaker Accessories	Tactile Transducers		',
            2372	=> 'Electronics	Audio	Audio Accessories	Turntable Accessories			',
            2165	=> 'Electronics	Audio	Audio Components				',
            241	    => 'Electronics	Audio	Audio Components	Audio & Video Receivers			',
            224	    => 'Electronics	Audio	Audio Components	Audio Amplifiers			',
            4493	=> 'Electronics	Audio	Audio Components	Audio Amplifiers	Headphone Amplifiers		',
            5381	=> 'Electronics	Audio	Audio Components	Audio Amplifiers	Power Amplifiers		',
            236	    => 'Electronics	Audio	Audio Components	Audio Mixers			',
            5129	=> 'Electronics	Audio	Audio Components	Audio Transmitters			',
            5130	=> 'Electronics	Audio	Audio Components	Audio Transmitters	Bluetooth Transmitters		',
            4035	=> 'Electronics	Audio	Audio Components	Audio Transmitters	FM Transmitters		',
            6545	=> 'Electronics	Audio	Audio Components	Channel Strips			',
            6546	=> 'Electronics	Audio	Audio Components	Direct Boxes			',
            505771	=> 'Electronics	Audio	Audio Components	Headphones & Headsets			',
            234	    => 'Electronics	Audio	Audio Components	Microphones			',
            246	    => 'Electronics	Audio	Audio Components	Signal Processors			',
            5435	=> 'Electronics	Audio	Audio Components	Signal Processors	Crossovers		',
            247	    => 'Electronics	Audio	Audio Components	Signal Processors	Effects Processors		',
            248	    => 'Electronics	Audio	Audio Components	Signal Processors	Equalizers		',
            5597	=> 'Electronics	Audio	Audio Components	Signal Processors	Loudspeaker Management Systems		',
            3945	=> 'Electronics	Audio	Audio Components	Signal Processors	Microphone Preamps		',
            5596	=> 'Electronics	Audio	Audio Components	Signal Processors	Noise Gates & Compressors		',
            5369	=> 'Electronics	Audio	Audio Components	Signal Processors	Phono Preamps		',
            249	    => 'Electronics	Audio	Audio Components	Speakers			',
            505298	=> 'Electronics	Audio	Audio Components	Studio Recording Bundles			',
            242	    => 'Electronics	Audio	Audio Players & Recorders				',
            225	    => 'Electronics	Audio	Audio Players & Recorders	Boomboxes			',
            226	    => 'Electronics	Audio	Audio Players & Recorders	CD Players & Recorders			',
            243	    => 'Electronics	Audio	Audio Players & Recorders	Cassette Players & Recorders			',
            252	    => 'Electronics	Audio	Audio Players & Recorders	Home Theater Systems			',
            4652	=> 'Electronics	Audio	Audio Players & Recorders	Jukeboxes			',
            230	    => 'Electronics	Audio	Audio Players & Recorders	Karaoke Systems			',
            233	    => 'Electronics	Audio	Audio Players & Recorders	MP3 Players			',
            235	    => 'Electronics	Audio	Audio Players & Recorders	MiniDisc Players & Recorders			',
            5434	=> 'Electronics	Audio	Audio Players & Recorders	Multitrack Recorders			',
            6886	=> 'Electronics	Audio	Audio Players & Recorders	Radios			',
            8271	=> 'Electronics	Audio	Audio Players & Recorders	Reel-to-Reel Tape Players & Recorders			',
            251	    => 'Electronics	Audio	Audio Players & Recorders	Stereo Systems			',
            256	    => 'Electronics	Audio	Audio Players & Recorders	Turntables & Record Players			',
            244	    => 'Electronics	Audio	Audio Players & Recorders	Voice Recorders			',
            8159	=> 'Electronics	Audio	Bullhorns				',
            4921	=> 'Electronics	Audio	DJ & Specialty Audio				',
            4922	=> 'Electronics	Audio	DJ & Specialty Audio	DJ CD Players			',
            4923	=> 'Electronics	Audio	DJ & Specialty Audio	DJ Systems			',
            2154	=> 'Electronics	Audio	Public Address Systems					',
            2082	=> 'Electronics	Electronics Accessories					',
            258	    => 'Electronics	Electronics Accessories	Adapters				',
            4463	=> 'Electronics	Electronics Accessories	Adapters	Audio & Video Cable Adapters & Couplers			',
            146	    => 'Electronics	Electronics Accessories	Adapters	Memory Card Adapters	',
            4617	=> 'Electronics	Electronics Accessories	Electronics Cleaners				',
            6544	=> 'Electronics	GPS Tracking Devices					',
            340	    => 'Electronics	Marine Electronics						',
            284	    => 'Electronics	Print, Copy, Scan & Fax	Scanner Accessories				',
            306	    => 'Electronics	Print, Copy, Scan & Fax	Scanners				',
            912	    => 'Electronics	Radar Detectors					',
            500091	=> 'Electronics	Speed Radars					',
            4488	=> 'Electronics	Toll Collection Devices						',
            632	    => 'Hardware							',
            469	    => 'Health & Beauty						',
            491	    => 'Health & Beauty	Health Care					',
            5849	=> 'Health & Beauty	Health Care	Acupuncture				',
            5850	=> 'Health & Beauty	Health Care	Acupuncture	Acupuncture Models			',
            5851	=> 'Health & Beauty	Health Care	Acupuncture	Acupuncture Needles			',
            7220	=> 'Health & Beauty	Health Care	Bed Pans				',
            5071	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories				',
            505819	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Activity Monitor Accessories			',
            3688	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Blood Glucose Meter Accessories			',
            6323	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Blood Glucose Meter Accessories	Blood Glucose Control Solution		',
            3905	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Blood Glucose Meter Accessories	Blood Glucose Test Strips		',
            3111	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Blood Glucose Meter Accessories	Lancing Devices		',
            6284	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Blood Pressure Monitor Accessories			',
            6285	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Blood Pressure Monitor Accessories	Blood Pressure Monitor Cuffs		',
            5072	=> 'Health & Beauty	Health Care	Biometric Monitor Accessories	Body Weight Scale Accessories			',
            494	    => 'Health & Beauty	Health Care	Biometric Monitors				',
            500009	=> 'Health & Beauty	Health Care	Biometric Monitors	Activity Monitors			',
            2246	=> 'Health & Beauty	Health Care	Biometric Monitors	Blood Glucose Meters			',
            495	    => 'Health & Beauty	Health Care	Biometric Monitors	Blood Pressure Monitors			',
            496	    => 'Health & Beauty	Health Care	Biometric Monitors	Body Fat Analyzers			',
            500	    => 'Health & Beauty	Health Care	Biometric Monitors	Body Weight Scales			',
            2633	=> 'Health & Beauty	Health Care	Biometric Monitors	Breathalyzers			',
            497	    => 'Health & Beauty	Health Care	Biometric Monitors	Cholesterol Analyzers			',
            505822	=> 'Health & Beauty	Health Care	Biometric Monitors	Fertility Monitors and Ovulation Tests			',
            501	    => 'Health & Beauty	Health Care	Biometric Monitors	Medical Thermometers			',
            4767	=> 'Health & Beauty	Health Care	Biometric Monitors	Prenatal Heart Rate Monitors			',
            5551	=> 'Health & Beauty	Health Care	Biometric Monitors	Pulse Oximeters			',
            775	    => 'Health & Beauty	Health Care	Condoms				',
            505820	=> 'Health & Beauty	Health Care	Conductivity Gels & Lotions				',
            7002	=> 'Health & Beauty	Health Care	Contraceptive Cases				',
            508	    => 'Health & Beauty	Health Care	First Aid				',
            2954	=> 'Health & Beauty	Health Care	First Aid	Antiseptics & Cleaning Supplies			',
            6206	=> 'Health & Beauty	Health Care	First Aid	Cast & Bandage Protectors			',
            4527	=> 'Health & Beauty	Health Care	First Aid	Eye Wash Supplies			',
            510	    => 'Health & Beauty	Health Care	First Aid	First Aid Kits			',
            516	    => 'Health & Beauty	Health Care	First Aid	Hot & Cold Therapies			',
            5848	=> 'Health & Beauty	Health Care	First Aid	Hot & Cold Therapies	Heat Rubs		',
            6205	=> 'Health & Beauty	Health Care	First Aid	Hot & Cold Therapies	Heating Pads		',
            4753	=> 'Health & Beauty	Health Care	First Aid	Hot & Cold Therapies	Ice Packs		',
            509	    => 'Health & Beauty	Health Care	First Aid	Medical Tape & Bandages			',
            2890	=> 'Health & Beauty	Health Care	Fitness & Nutrition				',
            2984	=> 'Health & Beauty	Health Care	Fitness & Nutrition	Nutrition Bars			',
            5702	=> 'Health & Beauty	Health Care	Fitness & Nutrition	Nutrition Drinks & Shakes			',
            6242	=> 'Health & Beauty	Health Care	Fitness & Nutrition	Nutrition Gels & Chews			',
            6871	=> 'Health & Beauty	Health Care	Fitness & Nutrition	Nutritional Food Purées			',
            7413	=> 'Health & Beauty	Health Care	Fitness & Nutrition	Tube Feeding Supplements			',
            525	    => 'Health & Beauty	Health Care	Fitness & Nutrition	Vitamins & Supplements			',
            5690	=> 'Health & Beauty	Health Care	Hearing Aids				',
            517 	=> 'Health & Beauty	Health Care	Incontinence Aids				',
            518 	=> 'Health & Beauty	Health Care	Medicine & Drugs				',
            519 	=> 'Health & Beauty	Health Care	Mobility & Accessibility				',
            520 	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Equipment			',
            3512	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Equipment	Mobility Scooters		',
            7138	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Equipment	Stair Lifts		',
            502969	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Equipment	Transfer Boards & Sheets		',
            3364	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Equipment	Wheelchairs		',
            521	    => 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Equipment Accessories			',
            5488	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Furniture & Fixtures			',
            7243	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Accessibility Furniture & Fixtures	Shower Benches & Seats		',
            6929	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Walking Aid Accessories			',
            5164	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Walking Aids			',
            5165	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Walking Aids	Canes & Walking Sticks		',
            4248	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Walking Aids	Crutches		',
            5166	=> 'Health & Beauty	Health Care	Mobility & Accessibility	Walking Aids	Walkers		',
            5870	=> 'Health & Beauty	Health Care	Occupational & Physical Therapy Equipment				',
            8541	=> 'Health & Beauty	Health Care	Occupational & Physical Therapy Equipment	Electrical Muscle Stimulators			',
            505352	=> 'Health & Beauty	Health Care	Occupational & Physical Therapy Equipment	Therapeutic Swings			',
            3777	=> 'Health & Beauty	Health Care	Pillboxes				',
            4551	=> 'Health & Beauty	Health Care	Respiratory Care				',
            4552	=> 'Health & Beauty	Health Care	Respiratory Care	Nebulizers			',
            499692	=> 'Health & Beauty	Health Care	Respiratory Care	Oxygen Tanks			',
            7317	=> 'Health & Beauty	Health Care	Respiratory Care	PAP Machines			',
            7316	=> 'Health & Beauty	Health Care	Respiratory Care	PAP Masks			',
            505669	=> 'Health & Beauty	Health Care	Respiratory Care	Steam Inhalers			',
            8082	=> 'Health & Beauty	Health Care	Specimen Cups				',
            7186	=> 'Health & Beauty	Health Care	Spermicides				',
            8105	=> 'Health & Beauty	Health Care	Stump Shrinkers				',
            523	    => 'Health & Beauty	Health Care	Supports & Braces				',
            5923	=> 'Health & Beauty	Health Care	Surgical Lubricants			',
            2915	=> 'Health & Beauty	Personal Care					',
            493	    => 'Health & Beauty	Personal Care	Back Care				',
            7404	=> 'Health & Beauty	Personal Care	Back Care	Back & Lumbar Support Cushions	',
            4929	=> 'Health & Beauty	Personal Care	Cotton Balls				',
            2934	=> 'Health & Beauty	Personal Care	Cotton Swabs				',
            484	    => 'Health & Beauty	Personal Care	Deodorant & Anti-Perspirant				',
            506	    => 'Health & Beauty	Personal Care	Ear Care				',
            5706	=> 'Health & Beauty	Personal Care	Ear Care	Ear Candles			',
            6559	=> 'Health & Beauty	Personal Care	Ear Care	Ear Drops			',
            6560	=> 'Health & Beauty	Personal Care	Ear Care	Ear Dryers			',
            500024	=> 'Health & Beauty	Personal Care	Ear Care	Ear Picks & Spoons			',
            6561	=> 'Health & Beauty	Personal Care	Ear Care	Ear Syringes			',
            6562	=> 'Health & Beauty	Personal Care	Ear Care	Ear Wax Removal Kits			',
            7542	=> 'Health & Beauty	Personal Care	Ear Care	Earplug Dispensers			',
            2596	=> 'Health & Beauty	Personal Care	Ear Care	Earplugs			',
            7134	=> 'Health & Beauty	Personal Care	Enema Kits & Supplies				',
            485	    => 'Health & Beauty	Personal Care	Feminine Sanitary Supplies				',
            6862	=> 'Health & Beauty	Personal Care	Feminine Sanitary Supplies	Feminine Deodorant			',
            5821	=> 'Health & Beauty	Personal Care	Feminine Sanitary Supplies	Feminine Douches & Creams			',
            2387	=> 'Health & Beauty	Personal Care	Feminine Sanitary Supplies	Feminine Pads & Protectors			',
            8122	=> 'Health & Beauty	Personal Care	Feminine Sanitary Supplies	Menstrual Cups			',
            2564	=> 'Health & Beauty	Personal Care	Feminine Sanitary Supplies	Tampons			',
            515	    => 'Health & Beauty	Personal Care	Foot Care				',
            2992	=> 'Health & Beauty	Personal Care	Foot Care	Bunion Care Supplies			',
            3022	=> 'Health & Beauty	Personal Care	Foot Care	Corn & Callus Care Supplies			',
            3049	=> 'Health & Beauty	Personal Care	Foot Care	Foot Odor Removers			',
            2801	=> 'Health & Beauty	Personal Care	Foot Care	Insoles & Inserts			',
            7495	=> 'Health & Beauty	Personal Care	Foot Care	Toe Spacers			',
            486	    => 'Health & Beauty	Personal Care	Hair Care				',
            8452	=> 'Health & Beauty	Personal Care	Hair Care	Hair Care Kits			',
            2814	=> 'Health & Beauty	Personal Care	Hair Care	Hair Color			',
            6053	=> 'Health & Beauty	Personal Care	Hair Care	Hair Color Removers			',
            5977	=> 'Health & Beauty	Personal Care	Hair Care	Hair Coloring Accessories			',
            6099	=> 'Health & Beauty	Personal Care	Hair Care	Hair Loss Concealers			',
            4766	=> 'Health & Beauty	Personal Care	Hair Care	Hair Loss Treatments			',
            6052	=> 'Health & Beauty	Personal Care	Hair Care	Hair Permanents & Straighteners			',
            3013	=> 'Health & Beauty	Personal Care	Hair Care	Hair Shears			',
            6429	=> 'Health & Beauty	Personal Care	Hair Care	Hair Steamers & Heat Caps			',
            1901	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Products			',
            6018	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tool Accessories			',
            5317	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tool Accessories	Hair Curler Clips & Pins		',
            4475	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tool Accessories	Hair Dryer Accessories		',
            4569	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tool Accessories	Hair Iron Accessories		',
            6019	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools			',
            487	    => 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools	Combs & Brushes		',
            489	    => 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools	Curling Irons		',
            488	    => 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools	Hair Curlers		',
            490	    => 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools	Hair Dryers		',
            3407	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools	Hair Straighteners		',
            499992	=> 'Health & Beauty	Personal Care	Hair Care	Hair Styling Tools	Hair Styling Tool Sets		',
            2441	=> 'Health & Beauty	Personal Care	Hair Care	Shampoo & Conditioner			',
            5663	=> 'Health & Beauty	Personal Care	Massage & Relaxation				',
            500060	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Back Scratchers			',
            233420	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Eye Pillows			',
            1442	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Massage Chairs			',
            5664	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Massage Oil			',
            8530	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Massage Stone Warmers			',
            8135	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Massage Stones			',
            2074	=> 'Health & Beauty	Personal Care	Massage & Relaxation	Massage Tables			',
            471	    => 'Health & Beauty	Personal Care	Massage & Relaxation	Massagers			',
            4076	=> 'Health & Beauty	Personal Care	Sleeping Aids				',
            4313	=> 'Health & Beauty	Personal Care	Sleeping Aids	Eye Masks			',
            6017	=> 'Health & Beauty	Personal Care	Sleeping Aids	Snoring & Sleep Apnea Aids			',
            4211	=> 'Health & Beauty	Personal Care	Sleeping Aids	Travel Pillows			',
            4056	=> 'Health & Beauty	Personal Care	Sleeping Aids	White Noise Machines			',
            6921	=> 'Health & Beauty	Personal Care	Spray Tanning Tents				',
            472	    => 'Health & Beauty	Personal Care	Tanning Beds				',
            2656	=> 'Health & Beauty	Personal Care	Tweezers				',
            1380	=> 'Health & Beauty	Personal Care	Vision Care				',
            3011	=> 'Health & Beauty	Personal Care	Vision Care	Contact Lens Care			',
            7363	=> 'Health & Beauty	Personal Care	Vision Care	Contact Lens Care	Contact Lens Care Kits		',
            6510	=> 'Health & Beauty	Personal Care	Vision Care	Contact Lens Care	Contact Lens Cases		',
            6509	=> 'Health & Beauty	Personal Care	Vision Care	Contact Lens Care	Contact Lens Solution		',
            2923	=> 'Health & Beauty	Personal Care	Vision Care	Contact Lenses			',
            2922	=> 'Health & Beauty	Personal Care	Vision Care	Eye Drops & Lubricants			',
            2733	=> 'Health & Beauty	Personal Care	Vision Care	Eyeglass Lenses			',
            524	    => 'Health & Beauty	Personal Care	Vision Care	Eyeglasses			',
            2521	=> 'Health & Beauty	Personal Care	Vision Care	Eyewear Accessories			',
            5507	=> 'Health & Beauty	Personal Care	Vision Care	Eyewear Accessories	Eyewear Cases & Holders		',
            352853	=> 'Health & Beauty	Personal Care	Vision Care	Eyewear Accessories	Eyewear Lens Cleaning Solutions		',
            8204	=> 'Health & Beauty	Personal Care	Vision Care	Eyewear Accessories	Eyewear Straps & Chains		',
            6977	=> 'Health & Beauty	Personal Care	Vision Care	Sunglass Lenses					',
            5181	=> 'Luggage & Bags						',
            100	    => 'Luggage & Bags	Backpacks					',
            101	    => 'Luggage & Bags	Briefcases					',
            108	    => 'Luggage & Bags	Cosmetic & Toiletry Bags					',
            549	    => 'Luggage & Bags	Diaper Bags					',
            502974	=> 'Luggage & Bags	Dry Boxes					',
            103     => 'Luggage & Bags	Duffel Bags					',
            104     => 'Luggage & Bags	Fanny Packs					',
            105     => 'Luggage & Bags	Garment Bags					',
            110     => 'Luggage & Bags	Luggage Accessories					',
            503014	=> 'Luggage & Bags	Luggage Accessories	Dry Box Liners & Inserts				',
            7521	=> 'Luggage & Bags	Luggage Accessories	Luggage Covers				',
            499691	=> 'Luggage & Bags	Luggage Accessories	Luggage Racks & Stands				',
            5652	=> 'Luggage & Bags	Luggage Accessories	Luggage Straps				',
            5651	=> 'Luggage & Bags	Luggage Accessories	Luggage Tags				',
            5620	=> 'Luggage & Bags	Luggage Accessories	Packing Organizers				',
            6919	=> 'Luggage & Bags	Luggage Accessories	Travel Bottles & Containers				',
            5650	=> 'Luggage & Bags	Luggage Accessories	Travel Pouches				',
            106	    => 'Luggage & Bags	Messenger Bags					',
            5608	=> 'Luggage & Bags	Shopping Totes					',
            107	    => 'Luggage & Bags	Suitcases					',
            6553	=> 'Luggage & Bags	Train Cases				',
            783	    => 'Media',
            784	    => 'Media	Books',
            499995	=> 'Media	Carpentry & Woodworking Project Plans					',
            839	    => 'Media	DVDs & Videos',
            886	    => 'Media	Magazines & Newspapers',
            855	    => 'Media	Music & Sound Recordings',
            887	    => 'Media	Sheet Music',
            988	    => 'Sporting Goods',
            499713	=> 'Sporting Goods	Athletics						',
            6734	=> 'Sporting Goods	Athletics	Broomball Equipment				',
            3354	=> 'Sporting Goods	Athletics	Cheerleading				',
            3953	=> 'Sporting Goods	Athletics	Cheerleading	Cheerleading Pom Poms			',
            6739	=> 'Sporting Goods	Athletics	Coaching & Officiating				',
            499729	=> 'Sporting Goods	Athletics	Coaching & Officiating	Captains Armbands			',
            505813	=> 'Sporting Goods	Athletics	Coaching & Officiating	Field & Court Boundary Markers			',
            499732	=> 'Sporting Goods	Athletics	Coaching & Officiating	Flip Coins & Discs			',
            6731	=> 'Sporting Goods	Athletics	Coaching & Officiating	Linesman Flags			',
            6729	=> 'Sporting Goods	Athletics	Coaching & Officiating	Penalty Cards & Flags			',
            499731	=> 'Sporting Goods	Athletics	Coaching & Officiating	Pitch Counters			',
            499733	=> 'Sporting Goods	Athletics	Coaching & Officiating	Referee Stands & Chairs			',
            499727	=> 'Sporting Goods	Athletics	Coaching & Officiating	Referee Wallets			',
            8505	=> 'Sporting Goods	Athletics	Coaching & Officiating	Scoreboards			',
            6730	=> 'Sporting Goods	Athletics	Coaching & Officiating	Sport & Safety Whistles			',
            499730	=> 'Sporting Goods	Athletics	Coaching & Officiating	Umpire Indicators				',
            499799	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment				',
            8222	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Altitude Training Masks			',
            499800	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Athletic Cups			',
            7397	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Ball Carrying Bags & Carts			',
            7433	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Ball Pump Accessories			',
            7435	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Ball Pump Accessories	Ball Pump Needles		',
            7434	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Ball Pumps			',
            499903	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Exercise & Gym Mat Storage Racks & Carts			',
            3971	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Grip Spray & Chalk			',
            499803	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Gym Mats			',
            8077	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Practice Nets & Screens			',
            499802	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Speed & Agility Ladders & Hurdles			',
            8319	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Sports & Agility Cones			',
            3877	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Sports Megaphones			',
            499801	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Sports Mouthguards			',
            6344	=> 'Sporting Goods	Athletics	General Purpose Athletic Equipment	Stadium Seats & Cushions			',
            1000	=> 'Sporting Goods	Athletics	Gymnastics				',
            503763	=> 'Sporting Goods	Athletics	Gymnastics	Gymnastics Bars & Balance Beams			',
            3808	=> 'Sporting Goods	Athletics	Gymnastics	Gymnastics Protective Gear			',
            499781	=> 'Sporting Goods	Athletics	Gymnastics	Gymnastics Protective Gear	Gymnastics Grips		',
            3774	=> 'Sporting Goods	Athletics	Gymnastics	Gymnastics Rings			',
            3123	=> 'Sporting Goods	Athletics	Gymnastics	Gymnastics Springboards			',
            3182	=> 'Sporting Goods	Athletics	Gymnastics	Pommel Horses			',
            3779	=> 'Sporting Goods	Athletics	Gymnastics	Vaulting Horses			',
            1145	=> 'Sporting Goods	Athletics	Water Polo				',
            3794	=> 'Sporting Goods	Athletics	Water Polo	Water Polo Balls			',
            3575	=> 'Sporting Goods	Athletics	Water Polo	Water Polo Caps			',
            3678	=> 'Sporting Goods	Athletics	Water Polo	Water Polo Goals			',
            990	    => 'Sporting Goods	Exercise & Fitness',
            499797	=> 'Sporting Goods	Exercise & Fitness	Ab Wheels & Rollers				',
            237166	=> 'Sporting Goods	Exercise & Fitness	Aerobic Steps				',
            499796	=> 'Sporting Goods	Exercise & Fitness	Balance Trainers				',
            499792	=> 'Sporting Goods	Exercise & Fitness	Cardio				',
            4598	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machine Accessories			',
            499703	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machine Accessories	Elliptical Trainer Accessories		',
            499702	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machine Accessories	Exercise Bike Accessories		',
            499701	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machine Accessories	Rowing Machine Accessories		',
            499700	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machine Accessories	Stair Climber & Stepper Accessories		',
            499699	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machine Accessories	Treadmill Accessories		',
            4589	=> 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machines			',
            992     => 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machines	Elliptical Trainers',
            994     => 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machines	Exercise Bikes',
            995     => 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machines	Rowing Machines',
            996     => 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machines	Stair Climbers & Steppers',
            997     => 'Sporting Goods	Exercise & Fitness	Cardio	Cardio Machines	Treadmills',
            2614	=> 'Sporting Goods	Exercise & Fitness	Cardio	Jump Ropes			',
            993	    => 'Sporting Goods	Exercise & Fitness	Exercise Balls',
            5869	=> 'Sporting Goods	Exercise & Fitness	Exercise Bands				',
            499795	=> 'Sporting Goods	Exercise & Fitness	Exercise Benches				',
            4669	=> 'Sporting Goods	Exercise & Fitness	Exercise Equipment Mats				',
            499978	=> 'Sporting Goods	Exercise & Fitness	Exercise Machine & Equipment Sets				',
            8471	=> 'Sporting Goods	Exercise & Fitness	Exercise Wedges				',
            6337	=> 'Sporting Goods	Exercise & Fitness	Foam Roller Accessories				',
            6338	=> 'Sporting Goods	Exercise & Fitness	Foam Roller Accessories	Foam Roller Storage Bags			',
            5319	=> 'Sporting Goods	Exercise & Fitness	Foam Rollers				',
            6867	=> 'Sporting Goods	Exercise & Fitness	Hand Exercisers				',
            355576	=> 'Sporting Goods	Exercise & Fitness	Inversion Tables & Systems				',
            3938	=> 'Sporting Goods	Exercise & Fitness	Medicine Balls				',
            499912	=> 'Sporting Goods	Exercise & Fitness	Power Towers				',
            8215	=> 'Sporting Goods	Exercise & Fitness	Push Up & Pull Up Bars				',
            7174	=> 'Sporting Goods	Exercise & Fitness	Reaction Balls				',
            8062	=> 'Sporting Goods	Exercise & Fitness	Speed & Resistance Parachutes				',
            505302	=> 'Sporting Goods	Exercise & Fitness	Sport Safety Lights & Reflectors				',
            5693	=> 'Sporting Goods	Exercise & Fitness	Stopwatches				',
            499798	=> 'Sporting Goods	Exercise & Fitness	Suspension Trainers				',
            8066	=> 'Sporting Goods	Exercise & Fitness	Vibration Exercise Machines				',
            499793	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting				',
            6452	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Free Weight Accessories			',
            8083	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Free Weight Accessories	Free Weight Storage Racks		',
            499794	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Free Weight Accessories	Weight Bar Collars		',
            3271	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Free Weight Accessories	Weight Bars		',
            3164	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Free Weights			',
            3654	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Weight Lifting Belts			',
            3858	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Weight Lifting Gloves & Hand Supports			',
            3217	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Weight Lifting Machine & Exercise Bench Accessories			',
            3542	=> 'Sporting Goods	Exercise & Fitness	Weight Lifting	Weight Lifting Machines & Racks			',
            6103	=> 'Sporting Goods	Exercise & Fitness	Weighted Clothing			',
            1011	=> 'Sporting Goods	Outdoor Recreation					',
            499811	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports				',
            1120	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting			',
            7449	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Boating Gloves		',
            6314	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Canoe Accessories		',
            1124	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Canoes		',
            6312	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Kayak Accessories		',
            1127	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Kayaks		',
            499964	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Paddle Leashes		',
            1129	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Paddles & Oars		',
            6097	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Pedal Boats		',
            3406	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Row Boats		',
            3476	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Rafting	Whitewater Rafts		',
            499813	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel			',
            1138	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Drysuits		',
            6496	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Life Jacket Accessories		',
            1128	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Life Jackets		',
            3376	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Rash Guards & Swim Shirts		',
            499687	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Water Sport Helmets		',
            499814	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Wetsuit Pieces		',
            5400	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Wetsuit Pieces	Wetsuit Bottoms	',
            5399	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Wetsuit Pieces	Wetsuit Hoods, Gloves & Boots',
            5401	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Wetsuit Pieces	Wetsuit Tops	',
            1147	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Boating & Water Sport Apparel	Wetsuits		',
            1135	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling			',
            1136	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Buoyancy Compensators		',
            1137	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Dive Computers		',
            499867	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Diving & Snorkeling Equipment Sets		',
            1139	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Diving & Snorkeling Fins		',
            1140	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Diving & Snorkeling Masks		',
            6514	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Diving Belts		',
            5312	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Diving Knives & Shears		',
            1141	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Diving Regulators		',
            1142	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Diving & Snorkeling	Snorkels		',
            5579	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Kitesurfing			',
            5584	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Kitesurfing	Kiteboard Cases		',
            5581	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Kitesurfing	Kiteboard Parts		',
            5580	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Kitesurfing	Kiteboards		',
            5583	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Kitesurfing	Kitesurfing & Windsurfing Harnesses		',
            5582	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Kitesurfing	Kitesurfing Kites		',
            1143	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing			',
            6287	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Bodyboards		',
            6288	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Paddleboards		',
            6286	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Skimboards		',
            3649	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surf Leashes		',
            3579	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surfboard Cases & Bags		',
            3525	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surfboard Fins		',
            3801	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surfboard Wax		',
            3320	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surfboards		',
            7451	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surfing Gloves		',
            3762	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Surfing	Surfing Tail Pads		',
            1144	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming			',
            7104	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Child Swimming Aids		',
            6473	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Hand Paddles		',
            2966	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Kickboards		',
            3595	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Pull Buoys		',
            6513	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swim Belts		',
            3807	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swim Caps		',
            3304	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swim Gloves		',
            6330	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swim Goggle & Mask Accessories		',
            3360	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swim Goggles & Masks		',
            6550	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swim Weights		',
            6511	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swimming Fins		',
            6512	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swimming Fins	Monofins	',
            2512	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swimming Fins	Training Fins	',
            3596	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swimming Machines		',
            6515	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Swimming	Swimming Nose Clips		',
            3195	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports			',
            3370	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Kneeboarding		',
            3101	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Kneeboarding	Kneeboards	',
            6301	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Towable Rafts & Tubes		',
            7452	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Towed Water Sport Gloves		',
            3282	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Wakeboarding		',
            505317	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Wakeboarding	Kiteboard & Wakeboard Bindings	',
            505291	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Wakeboarding	Wakeboard Parts	',
            3353	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Wakeboarding	Wakeboards	',
            1146	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Water Skiing		',
            3289	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Water Skiing	Sit-Down Hydrofoils	',
            6302	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Water Skiing	Water Ski Bindings	',
            6296	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Water Skiing	Water Ski Cases & Bags	',
            3350	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Water Skiing	Water Skis	',
            3636	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Towed Water Sports	Water Sport Tow Cables		',
            7178	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Watercraft Storage Racks			',
            8172	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Watercraft Storage Racks	Boat Storage Racks		',
            8173	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Watercraft Storage Racks	Water Sport Board Storage Racks		',
            1148	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Windsurfing			',
            3624	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Windsurfing	Windsurfing Board Parts		',
            3908	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Windsurfing	Windsurfing Board Parts	Windsurfing Board Fins	',
            3285	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Windsurfing	Windsurfing Board Parts	Windsurfing Board Masts	',
            3894	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Windsurfing	Windsurfing Boards		',
            3413	=> 'Sporting Goods	Outdoor Recreation	Boating & Water Sports	Windsurfing	Windsurfing Sails	',
            5998	=> 'Sporting Goods	Outdoor Recreation	Hydration System Accessories				',
            5635	=> 'Sporting Goods	Outdoor Recreation	Hydration Systems					',
            1239	=> 'Toys & Games						',
            4648	=> 'Toys & Games	Game Timers					',
            3793	=> 'Toys & Games	Games					',
            6794	=> 'Toys & Games	Games	Battle Top Accessories				',
            6329	=> 'Toys & Games	Games	Battle Tops				',
            3749	=> 'Toys & Games	Games	Bingo Sets				',
            7411	=> 'Toys & Games	Games	Blackjack & Craps Sets				',
            1246	=> 'Toys & Games	Games	Board Games				',
            6853	=> 'Toys & Games	Games	Card Game Accessories				',
            1247	=> 'Toys & Games	Games	Card Games				',
            6054	=> 'Toys & Games	Games	Dexterity Games				',
            6037	=> 'Toys & Games	Games	Dice Sets & Games				',
            7383	=> 'Toys & Games	Games	Poker Chip Accessories				',
            7384	=> 'Toys & Games	Games	Poker Chip Accessories	Poker Chip Carriers & Trays			',
            5403	=> 'Toys & Games	Games	Poker Chips & Sets				',
            4554	=> 'Toys & Games	Games	Portable Electronic Games				',
            7412	=> 'Toys & Games	Games	Roulette Wheels & Sets				',
            8472	=> 'Toys & Games	Games	Slot Machines				',
            1249	=> 'Toys & Games	Outdoor Play Equipment',
            7219	=> 'Toys & Games	Outdoor Play Equipment	Inflatable Bouncer Accessories				',
            6396	=> 'Toys & Games	Outdoor Play Equipment	Inflatable Bouncers				',
            6270	=> 'Toys & Games	Outdoor Play Equipment	Play Swings				',
            6397	=> 'Toys & Games	Outdoor Play Equipment	Play Tents & Tunnels				',
            1251	=> 'Toys & Games	Outdoor Play Equipment	Playhouses				',
            1863	=> 'Toys & Games	Outdoor Play Equipment	Pogo Sticks				',
            2743	=> 'Toys & Games	Outdoor Play Equipment	Sandboxes				',
            6450	=> 'Toys & Games	Outdoor Play Equipment	See Saws				',
            2867	=> 'Toys & Games	Outdoor Play Equipment	Slides				',
            3948	=> 'Toys & Games	Outdoor Play Equipment	Stilts				',
            6269	=> 'Toys & Games	Outdoor Play Equipment	Swing Set & Playset Accessories				',
            6271	=> 'Toys & Games	Outdoor Play Equipment	Swing Sets & Playsets				',
            5524	=> 'Toys & Games	Outdoor Play Equipment	Trampoline Accessories				',
            1738	=> 'Toys & Games	Outdoor Play Equipment	Trampolines				',
            6464	=> 'Toys & Games	Outdoor Play Equipment	Water Play Equipment				',
            6465	=> 'Toys & Games	Outdoor Play Equipment	Water Play Equipment	Play Sprinkers			',
            500095	=> 'Toys & Games	Outdoor Play Equipment	Water Play Equipment	Water Parks & Slides			',
            3556	=> 'Toys & Games	Outdoor Play Equipment	Water Play Equipment	Water Tables					',
            1253	=> 'Toys & Games	Toys								',
            7311	=> 'Toys & Games	Toys	Ball Pit Accessories				',
            7312	=> 'Toys & Games	Toys	Ball Pit Accessories	Ball Pit Balls			',
            3207	=> 'Toys & Games	Toys	Ball Pits				',
            3911	=> 'Toys & Games	Toys	Bath Toys				',
            1268	=> 'Toys & Games	Toys	Beach & Sand Toys',
            8127	=> 'Toys & Games	Toys	Sports Toy Accessories				',
            8129	=> 'Toys & Games	Toys	Sports Toy Accessories	Fitness Toy Accessories			',
            8128	=> 'Toys & Games	Toys	Sports Toy Accessories	Fitness Toy Accessories	Hula Hoop Accessories		',
            1266	=> 'Toys & Games	Toys	Sports Toys				',
            8529	=> 'Toys & Games	Toys	Sports Toys	Fishing Toys',
            3199	=> 'Toys & Games	Toys	Sports Toys	Fitness Toys			',
            3215	=> 'Toys & Games	Toys	Sports Toys	Fitness Toys	Hula Hoops		',
            499965	=> 'Toys & Games	Toys	Sports Toys	Playground Balls',
            499712	=> 'Toys & Games	Toys	Toy Gift Baskets',
            2953	=> 'Toys & Games	Toys	Wind-Up Toys				',
        ];
    }
}
