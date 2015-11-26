<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

use yii\db\Migration;

/**
 * 
 * @author Andrey Borodulin
 *
 */
class m151116_162817_geoip extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {   
        /* @var $geoip \conquer\geoip\Geoip */
        $geoip = Yii::$app->get('geoip');
        
        $this->createTable($geoip->districtTable, [
                'district_id' => $this->primaryKey(),
                'district_name' => $this->string()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
        
        $this->createIndex('uix_geoip_district', $geoip->districtTable, 'district_name', true);
        
        $this->createTable($geoip->regionTable, [
                'region_id' => $this->primaryKey(),
                'district_id' => $this->integer()->notNull(),
                'region_name' => $this->string()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
  
        $this->createIndex('uix_geoip_region', $geoip->regionTable, ['district_id', 'region_name'], true);
        $this->addForeignKey('fk_geoip_region_district', $geoip->regionTable, 'district_id', $geoip->districtTable, 'district_id','cascade','cascade');
        
        $this->createTable($geoip->cityTable, [
                'city_id' => $this->integer()->notNull(),
                'city_name' => $this->string()->notNull(),
                'region_id' => $this->integer()->notNull(),
                'lat' => $this->decimal(9,6),
                'lng' => $this->decimal(9,6),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_geoip_city', $geoip->cityTable, 'city_id');
        $this->addForeignKey('fk_geoip_city_region', $geoip->cityTable, 'region_id', $geoip->regionTable, 'region_id','cascade','cascade');
        
        $this->createTable($geoip->rangeTable, [
                'ip_start' =>  $this->bigInteger()->notNull(),
                'ip_end' => $this->bigInteger()->notNull(),
                'ip_range' => $this->string(),
                'ip_country' => $this->string(),
                'city_id' => $this->integer(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_geoip_range', $geoip->rangeTable, ['ip_start', 'ip_end']);
        $this->addForeignKey('fk_geoip_range_city', $geoip->rangeTable, 'city_id', $geoip->cityTable, 'city_id', 'set null', 'cascade');
    }
    
    public function safeDown()
    {
        $geoip = Yii::$app->get('geoip');
        
        $this->dropTable($geoip->rangeTable);
        $this->dropTable($geoip->cityTable);
        $this->dropTable($geoip->regionTable);
        $this->dropTable($geoip->districtTable);
    }
}
