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
        $this->createTable('{{%geoip_district}}', [
                'district_id' => $this->primaryKey(),
                'district_name' => $this->string()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
        
        $this->createIndex('uix_geoip_district', '{{%geoip_district}}', 'district_name', true);
        
        $this->createTable('{{%geoip_region}}', [
                'region_id' => $this->primaryKey(),
                'district_id' => $this->integer()->notNull(),
                'region_name' => $this->string()->notNull(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
  
        $this->createIndex('uix_geoip_region', '{{%geoip_region}}', ['district_id', 'region_name'], true);
        $this->addForeignKey('fk_geoip_region_district', '{{%geoip_region}}', 'district_id', '{{%geoip_district}}', 'district_id','cascade','cascade');
        
        $this->createTable('{{%geoip_city}}', [
                'city_id' => $this->integer()->notNull(),
                'city_name' => $this->string()->notNull(),
                'region_id' => $this->integer()->notNull(),
                'lat' => $this->decimal(9,6),
                'lng' => $this->decimal(9,6),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_geoip_city', '{{%geoip_city}}', 'city_id');
        $this->addForeignKey('fk_geoip_city_region', '{{%geoip_city}}', 'region_id', '{{%geoip_region}}', 'region_id','cascade','cascade');
        
        $this->createTable('{{%geoip_range}}', [
                'ip_start' => $this->integer()->notNull(),
                'ip_end' => $this->integer()->notNull(),
                'ip_range' => $this->string(),
                'ip_country' => $this->string(),
                'city_id' => $this->integer(),
                'created_at' => $this->integer()->notNull(),
                'updated_at' => $this->integer()->notNull(),
        ]);
        $this->addPrimaryKey('pk_geoip_range', '{{%geoip_range}}', ['ip_start', 'ip_end']);
        $this->addForeignKey('fk_geoip_range_city', '{{%geoip_range}}', 'city_id', '{{%geoip_city}}', 'city_id', 'set null', 'cascade');
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%geoip_range}}');
        $this->dropTable('{{%geoip_city}}');
        $this->dropTable('{{%geoip_region}}');
        $this->dropTable('{{%geoip_district}}');
    }
}
