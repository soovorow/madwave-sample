<?php

namespace console\controllers;

use common\components\sitemap\SiteMap;
use common\components\DPDService;
use common\components\importer\Importer;
use common\components\yandex\Market;
use common\models\OutOfStock;
use common\models\sale\Winter17_18;
use frontend\controllers\GoogleController;
use PDOException;
use Yii;
use yii\httpclient\Client;
use yii\web\Controller;

class CronController extends Controller {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    private function logError() {
        // TODO write error at database
    }

    // TODO move method to the model
    public function actionUpdateDpdList() {

        $dpd_city_list = (new DPDService())->getCityList();

        $values = [];

        foreach ($dpd_city_list as $city) {

            $values[] = [
                $city['cityId'],
                $city['countryCode'],
                $city['countryName'],
                $city['regionCode'],
                $city['regionName'],
                $city['cityCode'],
                $city['cityName'],
                $city['abbreviation'],
                (isset($city['indexMin']) ? $city['indexMin'] : null),
                (isset($city['indexMax']) ? $city['indexMax'] : null),
            ];

        }

        $columns = [
            'cityId'      ,
            'countryCode' ,
            'countryName' ,
            'regionCode'  ,
            'regionName'  ,
            'cityCode'    ,
            'cityName'    ,
            'abbreviation',
            'indexMin'    ,
            'indexMax'    ,
        ];

        $db = Yii::$app->db;

        $sql = $db->queryBuilder->batchInsert(
            'dpd_city_list',
            $columns,
            $values
        );

        try {
            $n = $db->createCommand($sql . ' ON DUPLICATE KEY UPDATE '
                .'cityId= VALUES (cityId),'
                .'countryCode= VALUES (countryCode),'
                .'countryName= VALUES (countryName),'
                .'regionCode= VALUES (regionCode),'
                .'regionName= VALUES (regionName),'
                .'cityCode= VALUES (cityCode),'
                .'cityName= VALUES (cityName),'
                .'abbreviation= VALUES (abbreviation),'
                .'indexMin= VALUES (indexMin),'
                .'indexMax= VALUES (indexMax),'
            )->execute();
        }
        catch (PDOException $e)
        {
            echo "\n";
            echo '# '.__CLASS__.'::'.__FUNCTION__.' - fail; Method has some shit, check it out.'."\n";
            echo "\n";

            return false;
        }


        if ($n > 0) {
            $message = '# '.__CLASS__.'::'.__FUNCTION__.' - success; '.$n.' rows was affected.'."\n";
        } else {
            $message = '# '.__CLASS__.'::'.__FUNCTION__.' - success; No one rows was affected. '."\n";
        }

        echo "\n";
        echo $message;
        echo "\n";

        return true;

    }

    public function actionUpdateSiteMapRu() {
        $this->layout = 'empty';
        \Yii::$app->language = 'ru-RU';
        return SiteMap::generate(SiteMap::FILE_RU, 'https://www.madwave.ru');
    }

    public function actionUpdateSiteMapEu() {
        $this->layout = 'empty';
        \Yii::$app->language = 'en-EU';
        return SiteMap::generate(SiteMap::FILE_EU, 'https://madwave.eu');
    }

    public function actionSyncThumbnails() {
        $dirs = ['small', 'medium', 'large'];

        foreach ($dirs as $size) {
            for ($n = 1; $n < 5; $n++) {
                $dir = '/var/www/admin/www/stash.madwave.ru/images/'.$size.'/pic_'.$n;
                $files = scandir($dir);
                unset($files[0]);
                unset($files[1]);
                foreach ($files as $f) {
                    $original = '/var/www/admin/www/stash.madwave.ru/images/original/pic_' . $n . '/' . $f;
                    $current = $dir . '/' . $f;
                    if (file_exists($original)) {
                        if (filemtime($original) > filemtime($current)) {
                            (new Client())->createRequest()
                                ->setMethod('post')
                                ->setUrl('http://stash.madwave.ru/corp.php')
                                ->setData([
                                    'variant_id' => $f,
                                    'n' => $n
                                ])
                                ->send();
                        }
                    } else {
                        unlink($current);
                    };
                }
            }
        }

        echo '# '.__CLASS__.'::'.__FUNCTION__.' - success;'."\n";
    }
    
    public function actionUpdateRelatedProducts() {
        if(GoogleController::actionRelatedProducts()) {
            echo "\n";
            echo '# '.__CLASS__.'::'.__FUNCTION__.' - success;'."\n";
            echo "\n";
        } else {
            echo "\n";
            echo '# '.__CLASS__.'::'.__FUNCTION__.' - fail; Method has some shit, check it out.'."\n";
            echo "\n";
        };
        return true;
    }
    
    public function actionUpdateOrderMetrics() {
        if(GoogleController::actionOrderMetrics()) {
            echo "\n";
            echo '# '.__CLASS__.'::'.__FUNCTION__.' - success;'."\n";
            echo "\n";
        } else {
            echo "\n";
            echo '# '.__CLASS__.'::'.__FUNCTION__.' - fail; Method has some shit, check it out.'."\n";
            echo "\n";
        };
        return true;
    }

    public function actionInitImport()
    {
        $mem0 = memory_get_usage();
        Importer::startUp();
        echo "\nScript memori: ".(memory_get_usage()-$mem0-sizeof($mem0))." bytes\n";
        return true;
    }

    public function actionWinterLightings()
    {
        Winter17_18::cronJob();
    }

    public function actionExportYandexMarket()
    {
        Market::generateYml();
    }

    public function actionGenerateGoogleMerchantFeed()
    {
        GoogleController::actionMerchant();
    }

    public function actionSendDailyReport()
    {
        \Yii::$app->mailer
            ->compose(['html' => 'daily-report-html'])
            ->setTo('servicemad@yandex.ru')
            ->setFrom('managerinternet@madwave.ru')
            ->setSubject('Ежедневный отчет, '.date('d M Y',  strtotime('-1 day')))
            ->send();
    }

    public function actionSaveOutOfStock()
    {
        return OutOfStock::run();
    }

    public function actionWatchPrices() {
        return PriceList::cronJob();
    }

    public function actionDumpDatabase()
    {

        $dir = dirname(dirname(__DIR__)).'/extras/';

        $databases = [
            'yii2-madwave',
            'products',
        ];

        foreach ($databases as $db) {
            exec('mysqldump --defaults-file='.$dir.$db.'.cnf '.$db.' > '.$dir.$db.'.sql');
        }

        $message = \Yii::$app->mailer->compose()
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo(\Yii::$app->params['adminEmail'])
            ->setSubject('Databse dump #'.date('d/m/Y H:s:i'))
            ->setTextBody('Hi! There is your backup!')
            ->setHtmlBody('<b>Hi! There is your backup!</b>');

        foreach ($databases as $db) {
            $message->attach($dir.$db.'.sql');
        }

        $message->send();

        echo $message ? 'success' : 'fail';
    }

    public function actionCheckExportOverdue()
    {
        $fileName = 'balance';
        $fileTime = filectime(Importer::getImportDir().'/'.$fileName.'.xml');
        $time = time();
        $overDuetime = ($time - $fileTime) / 60;
        $message = 'BALANCE.XML overdue by '.number_format($overDuetime).' minutes';

        if (($fileTime + (60 * 15)) < $time) {
            echo 'message sent'."\n";
            $message = \Yii::$app->mailer->compose()
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->setTo('servicemad@yandex.ru')
                ->setSubject($message)
                ->setTextBody($message)
                ->setHtmlBody($message)
                ->send()
            ;
        }
        echo 'message did not sent'."\n";
    }
}
