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
 * This is the model class for table "{{%region}}".
 *
 * @property integer $region_id
 * @property integer $district_id
 * @property string $region_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property District $district
 * @property City $cities
 */
class Region extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $geoip = Yii::$app->get('geoip');
        return $geoip->regionTable;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['district_id', 'region_name'], 'required'],
                [['region_id', 'district_id', 'created_at', 'updated_at'], 'integer'],
                [['region_name'], 'string', 'max' => 255],
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
    public function getDistrict()
    {
        return $this->hasOne(District::className(), ['district_id' => 'district_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::className(), ['region_id' => 'region_id']);
    }
    
    /**
     * @param array $attributes
     * @throws Exception
     * @return static
     */
    public static function getsert(array $attributes)
    {
        $model = static::findOne($attributes);
        if (!$model) {
            $model = new static($attributes);
            $model->save(false);
        }
        return $model;
    }
}