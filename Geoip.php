<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip;

class Geoip extends \yii\base\Component
{
    public $cityTable = '{{%city}}';
    
    public $districtTable = '{{%region}}';
    
    public $rangeTable = '{{%range}}';
    
    public $regionTable = '{{%region}}';
}