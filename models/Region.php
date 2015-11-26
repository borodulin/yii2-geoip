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
    private static $_models;
    
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
     * @param integer $district_id
     * @param string $region_name
     * @throws Exception
     * @return static
     */
    public static function getsert($district_id, $region_name)
    {
        if (is_null(self::$_models)) {
            self::$_models = static::find()->indexBy(function($row) { 
                return $row['district_id'] .'-'. $row['region_name'];
            })->all();
        }
    
        $id = $district_id .'-'. $region_name;
        if (isset(self::$_models[$id])) {
            return self::$_models[$id];
        }
    
        $model = new static(['district_id' => $district_id, 'region_name' => $region_name]);
        $model->save(false);
    
        return self::$_models[$id] = $model;
    }
}