<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip\models;

use yii\base\Exception;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%region}}".
 *
 * @property integer $district_id
 * @property string $district_name
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Region $regions
 *
 * @author Andrey Borodulin
 */
class District extends \yii\db\ActiveRecord
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
                [['district_name'], 'required'],
                [['district_id', 'created_at', 'updated_at'], 'integer'],
                [['district_name'], 'string', 'max' => 255],
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
    public function getRegions()
    {
        return $this->hasMany(Region::className(), ['district_id' => 'district_id']);
    }

    /**
     * @param array $attributes
     * @throws Exception
     * @return static
     */
    public static function getsert($attributes)
    {
        $model = static::findOne($attributes);
        if (!$model) {
            $model = new static($attributes);
            $model->save(false);
        }
        return $model;
    }
}