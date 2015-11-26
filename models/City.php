<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%city}}".
 *
 * @property integer $city_id
 * @property integer $region_id
 * @property string $city_name
 * @property double $lat
 * @property double $lng
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property City $city
 * 
 * @author Andrey Borodulin
 */
class City extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        $geoip = Yii::$app->get('geoip');
        return $geoip->cityTable;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['region_id', 'city_name'], 'required'],
                [['city_id', 'region_id', 'created_at', 'updated_at'], 'integer'],
                [['city_name'], 'string', 'max' => 255],
                [['lat', 'lng', 'number']],
        ];
    }
    
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['region_id' => 'region_id']);
    }
    
    /**
     * @param array $attributes
     * @throws Exception
     * @return static
     */
    public static function upsert(array $attributes)
    {
        $model = static::findOne(['city_id' => $attributes['city_id']]);
        if (!$model) {
            $model = new static($attributes);
            $model->save(false);
        }
        return $model;
    }
    
}