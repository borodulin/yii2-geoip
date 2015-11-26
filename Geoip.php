<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip;

use conquer\geoip\console\GeoipController;

class Geoip extends \yii\base\Component implements \yii\base\BootstrapInterface
{
    public $cityTable = '{{%city}}';
    
    public $districtTable = '{{%district}}';
    
    public $rangeTable = '{{%range}}';
    
    public $regionTable = '{{%region}}';
    
    
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $app->controllerMap['geoip'] = [
                    'class' => GeoipController::className(),
            ];
        }
    }
}