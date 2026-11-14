<?php

namespace frontend\models\filter;

use common\models\LocalCategory;
use yii\base\Model;

/**
 * Filter model
 *
 * @property LocalCategory $category
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */

class Filter extends Model
{
    public function __construct($category, $options)
    {
        foreach ($options as $option) {
            $option->category = $category;
        }
    }
}
