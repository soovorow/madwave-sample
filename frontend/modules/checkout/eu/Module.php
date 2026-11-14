<?php

namespace frontend\modules\checkout\eu;

/**
 * City detector module
 *
 * Add following code into your `config\main.php` for start using it
 *
 * 'modules' => [
 *     'checkout' => [
 *         'class' => 'frontend\modules\checkout\eu\Module',
 *     ],
 * ],
 *
 * add libraries=places into your google api maps script
 * <script async defer src="https://maps.googleapis.com/maps/api/js?key=********&callback=*******&libraries=places"></script>
 *
 * @author Dmitry Suvorov <soovorow@gmail.com>
 *
 */
class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
    }
}