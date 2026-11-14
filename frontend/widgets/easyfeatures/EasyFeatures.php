<?php

namespace frontend\widgets\easyfeatures;

use frontend\widgets\easyfeatures\assets\EasyFeaturesAsset;
use yii\base\Widget;

/**
 * Class EasyFeatures
 *
 * Widget to show float buttons with dropdown feature descriptions inside
 *
 * @package frontend\widgets\easyfeatures
 *
 * @author Vyacheslav Elchin <lexscite@gmail.com>
 */
class EasyFeatures extends Widget
{
    public $image;
    public $items;

    /**
     * Generates widget HTML string
     *
     * @inheritdoc
     */
    public function run()
    {
        EasyFeaturesAsset::register($this->getView());

        $content = '<div class="featwidget">';

        $content .= '<img src="'.$this->image.'" class="featwidget-image">';

        if (isset($this->items) && is_array($this->items))
        {
            foreach($this->items as $item)
            {
                $content .= $this->generateItem($item);
            }
        }

        $content .= '</div>';

        return $content;
    }

    /**
     * Generates one feature HTML string
     *
     * @param $item
     * @return bool|string
     */
    private function generateItem($item)
    {
        if (!is_array($item) || !isset($item['title'], $item['x'], $item['y']))
        {
            return false;
        }

        $title = $item['title'];
        $description = $item['description'];
        $x = $item['x'];
        $y = $item['y'];

        $content = '';
        $content .= '<div class="featwidget-item" data-featwidget-x="'.$x.'" data-featwidget-y="'.$y.'">';

        $content .= '<div class="featwidget-btn"><span class="fa fa-plus"></span>';
        $content .= '<div class="featwidget-arrow"></div>';
        $content .= '</div>';

        $content .= '<div class="featwidget-info">';
        $content .= '<div class="featwidget-title">'.$title.'</div>';
        $content .= '<div class="featwidget-desc">'.$description.'</div>';
        $content .= '</div>';

        $content .= '</div>';

        return $content;
    }
}