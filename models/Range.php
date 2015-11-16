<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip\models;

use yii\behaviors\TimestampBehavior;
use yii\base\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "{{%geoip_range}}".
 *
 * @property integer $ip_start
 * @property integer $ip_end
 * @property string $ip_range
 * @property string $ip_country
 * @property integer $city_id
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property City $city
 */
class Range extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%geoip_range}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['ip_start', 'ip_end'], 'required'],
                [['ip_start', 'ip_end', 'city_id', 'created_at', 'updated_at'], 'integer'],
                [['ip_range'], 'string', 'max' => 255],
                ['ip_country', 'string', 'max' => 2]
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
    public function getCity()
    {
        return $this->hasOne(City::className(), ['city_id' => 'city_id']);
    }

    /**
     * 
     * @param array $attributes
     * @return static
     * @throws Exception
     */
    public static function upsert($attributes)
    {
        $model = static::findOne([
                'ip_start' => $attributes['ip_start'],
                'ip_end' => $attributes['ip_end'],
        ]);
        if (!$model) {
            $model = new static($attributes);
        } else {
            $model->setAttributes($attributes);
        }
        if (!$model->save()) {
            throw new Exception(VarDumper::dumpAsString($model->errors));
        }
        return $model;
    }
    
    /**
     * 
     * @param mixed $ip
     * @return static
     */
    public static function findByIp($ip)
    {
        if(is_string($ip) && preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip)) {
            $ip = ip2long($ip);
        } 
        if (is_numeric($ip)) {
            return static::find()
                ->with('city', 'city.region')
                ->where(':ip BETWEEN ip_start AND ip_end', compact('ip'))
                ->one();
        }
        return null;
    }   
}