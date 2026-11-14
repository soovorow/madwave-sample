<?php namespace common\components\screaming_crawl;

use common\components\screaming_crawl\models\CrawlA;
use common\components\screaming_crawl\models\CrawlB;
use Yii;

class ScreamingCrawl {

    public function prepare($file_a, $file_b) {
//        $this->import($file_a, CrawlA::className());
        $this->import($file_b, CrawlB::className());
    }

    private function import($file, $class) {
        if (Yii::$app->db->schema->getTableSchema($class::getTableName()) !== null) {
            Yii::$app->db->createCommand()->truncateTable($class::getTableName())->execute();
        }

        $file = fopen($file, 'r');
        $index = 0;
        $row = [];
        while (($line = fgetcsv($file)) !== FALSE) {

            // compose $key => $value template
            if ($index == 1) {
                foreach ($line as $key => $value) {
                    $row[str_replace([' ', '-'], '', $value)] = $key;
                }
            }

            if ($index > 1) {
                $crawl_table = new $class($this->wrapRow($line, $row));
                $crawl_table->save(false);
            }
            $index++;
        }
        fclose($file);
        return 1;
    }

    public function renderComparedValues($attr, $a, $b) {
        $ie = self::isEqual($a->$attr, $b->$attr);

        $result = '';

        $result .= '<span class="screaming-crawl-val '. ($ie ? 'color-primary' : '') .'">'. (!empty($a->$attr) ? $a->$attr : 'NULL') .'</span>';
        $result .= '<span class="screaming-crawl-val '. ($ie ? 'color-primary' : 'color-red') .'">'. (!empty($b->$attr) ? $b->$attr : 'NULL') .'</span>';

        return $result;
    }

    public function isEqual($strA, $strB) {
        if ($strA === $strB) {
            return true;
        }
        return false;
    }


    public static function cutUrl($url) {
        $address = explode('/', explode('//', $url)[1]);
        unset($address[0]);
        return '/'.implode('/', $address);
    }

    public function wrapRow($row, $template) {
        $params = [];
        foreach ($template as $attr => $key) {
            if (substr($row[$key], 0, 4) === 'http') {
                $value = $this->cutUrl($row[$key]);
            } else {
                $value = $row[$key];
            }
            $params[$attr] = $value;
        }
        return $params;
    }

}