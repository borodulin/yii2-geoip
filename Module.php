<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip;

use conquer\geoip\console\GeoipController;

/**
 * 
 * @author Andrey Borodulin
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                    $this->id => $this->id . '/default/index',
                    $this->id . '/<id:\w+>' => $this->id . '/default/view',
                    $this->id . '/<controller:[\w\-]+>/<action:[\w\-]+>' => $this->id . '/<controller>/<action>',
            ], false);
        } elseif ($app instanceof \yii\console\Application) {
            $app->controllerMap[$this->id] = [
                    'class' => GeoipController::className(),
            ];
        }
    }
}