<?php namespace common\components\screaming_crawl;

use common\components\screaming_crawl\models\CrawlA;
use common\components\screaming_crawl\models\CrawlDiffs;
use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;

class ScreamingCrawlAction extends Action
{
    public $view = '@common/components/screaming_crawl/views/index';

    private function prepareComparing() {
        $fA = Yii::getAlias('@webroot') . '/compared.csv';
        $fB = Yii::getAlias('@webroot') . '/comparing.csv';
        $sc = new ScreamingCrawl();
        $sc->prepare($fA, $fB);
        Yii::$app->session->setFlash('success','Preparing had been completed.');
    }

    public function run()
    {
        $request = \Yii::$app->request;
        $data = [];

        /** ACTION MARK ROW */
        if ($row_id = $request->get('row_id')) {
            if ($r = CrawlA::findOne($row_id)) {
                if ($request->get('solve')) $r->sstatus = $_GET['solve'];
                if ($request->get('match')) $r->sstatus = $_GET['match'];
                $r->save(false);
                return $this->controller->redirect('/site/screaming-crawl');
            }
        }

        /** ACTION EXPORT TABLE DIFFS */
        if ($request->post('export-diffs')) {
            self::exportDiffs([
                'Title1',
                'MetaDescription1',
                'H11',
            ]);
        }

        /** ACTION REFRESH TABLES */
        if ($request->post('update-data')) {
            self::prepareComparing();
        }

        $data['dataProvider'] = $this->getDataProvider(
            $request->get('hide-without-couple') == 'true',
            $request->get('hide-redirect') == 'true',
            $request->get('hide-matches') == 'true'
        );

        return $this->controller->render($this->view ?: $this->id, $data);
    }

    private function getDataProvider($hideWithoutCouple = null, $hideRedirect = null, $hideMatches = null, $pageSize = null)
    {
        $query =  CrawlA::find()->where('1')
            ->andWhere('crawl_a.CanonicalLinkElement1 <> ""')
        ;

        /*** FILTER WITHOUT COUPLE */
        if ($hideWithoutCouple) {
            $query->innerJoin('crawl_b', '`crawl_b`.`CanonicalLinkElement1` = `crawl_a`.`CanonicalLinkElement1`');
        }

        /*** FILTER HIDE REDIRECT */
        if ($hideRedirect) {
            $query->andWhere('NOT EXISTS (SELECT NULL FROM redirect b WHERE b.from = crawl_a.CanonicalLinkElement1)');
        }

        /*** FILTER HIDE MATCHES */
        if ($hideMatches) {
            $query->andWhere('NOT EXISTS (
                SELECT NULL FROM crawl_b b 
                WHERE b.CanonicalLinkElement1 = crawl_a.CanonicalLinkElement1
                AND b.StatusCode = crawl_a.StatusCode
                AND b.Title1 = crawl_a.Title1
                AND b.Title1Length = crawl_a.Title1Length
                AND b.MetaDescription1 = crawl_a.MetaDescription1
                AND b.MetaDescription1Length = crawl_a.MetaDescription1Length
                AND b.H11 = crawl_a.H11
            )');
        }

        return new ActiveDataProvider([
            'query' => $query->groupBy('CanonicalLinkElement1'),
            'pagination' => ['pageSize' => $pageSize > 0 ? $pageSize : 100]
        ]);
    }

    private function exportDiffs($columns)
    {
        $query = $this->getDataProvider(true, true, true, 9000);

        foreach ($query->models as $row) {
            $r = CrawlDiffs::find()->where(['CanonicalLinkElement1' => $row->CanonicalLinkElement1])->one();
            if (!$r) {
                $r = new CrawlDiffs();
                $r->CanonicalLinkElement1 = $row->CanonicalLinkElement1;
            }
            foreach ($columns as $c) {
                $r->$c = $row->$c;
            }
            $r->save(false);
        }
        Yii::$app->session->set('success', 'export complete');
    }
}
