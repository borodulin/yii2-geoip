<?php
/**
 * @link https://github.com/borodulin/yii2-geoip
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-geoip/blob/master/LICENSE
 */

namespace conquer\geoip\console;

use conquer\helpers\Temporary;
use conquer\geoip\models\District;
use conquer\geoip\models\Region;
use conquer\geoip\models\City;
use conquer\geoip\models\Range;
/**
 * 
 * @author Andrey Borodulin
 *
 */
class GeoipController extends \yii\console\Controller
{
    public function actionIndex()
    {
        $temp = new Temporary('geoip');
        $zipArchive = $temp->file();
        file_put_contents($zipArchive, fopen('http://ipgeobase.ru/files/db/Main/geo_files.zip', 'r'));
        if(file_exists($zipArchive))
        {
            if (($handle = fopen('zip://'.$zipArchive.'#cities.txt', "r")) !== FALSE)
            {
                stream_filter_append($handle, 'convert.iconv.Windows-1251/UTF-8');
                while (($data = fgetcsv($handle, 4096, "\t")) !== FALSE)
                {
                    $district = District::getsert([
                            'district_name' => $data[3],
                    ]);
                    	
                    $region = Region::getsert([
                            'district_id' => $district->district_id,
                            'region_name' => $data[2],
                    ]);
                    
                    $city = City::upsert([
                            'city_id' => $data[0],
                            'city_name' => $data[1],
                            'region_id' => $region->region_id,
                            'lat' => $data[4],
                            'lng' => $data[5],
                    ]);
                }
                fclose($handle);
            }
            if (($handle = fopen('zip://'.$zipArchive.'#cidr_optim.txt', "r")) !== FALSE)
            {
                stream_filter_append($handle, 'convert.iconv.Windows-1251/UTF-8');
                
                $db = Range::getDb();
                
                $tran = $db->beginTransaction();                
                // Rewrite all
                Range::deleteAll();

                $rows = [];
                $time = time();
                while (($data = fgetcsv($handle, 4096, "\t")) !== FALSE)
                {
                    $rows[] = [
                            'ip_start' => $data[2],
                            'ip_end' => $data[3],
                            'ip_range' => ($data[4]=='-') ? null : $data[4],
                            'ip_country' => $data[5],
                            'city_id' => $data[6],
                            'created_at' => $time,
                            'updated_at' => $time,
                    ];
                    if (count($rows) == 1000) {
                        $db->createCommand()
                            ->batchInsert(Range::tableName(), [
                                    'ip_start',
                                    'ip_end',
                                    'ip_range',
                                    'ip_country',
                                    'city_id',
                                    'created_at',
                                    'updated_at'
                            ], $rows)
                            ->execute();
                    }
                }
                if (count($rows) > 0) {
                    $db->createCommand()
                        ->batchInsert(Range::tableName(), [
                                'ip_start',
                                'ip_end',
                                'ip_range',
                                'ip_country',
                                'city_id',
                                'created_at',
                                'updated_at'
                        ], $rows)
                        ->execute();
                }
                $tran->commit();
                
                fclose($handle);
            }
        }
        
    }
}