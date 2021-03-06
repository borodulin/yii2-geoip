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
            $tran = \Yii::$app->db->beginTransaction();

            // Rewrite all
            Range::deleteAll();
            City::deleteAll();
            
            if (($handle = fopen('zip://'.$zipArchive.'#cities.txt', "r")) !== FALSE)
            {
                stream_filter_append($handle, 'convert.iconv.Windows-1251/UTF-8');
                while (($data = fgetcsv($handle, 4096, "\t")) !== FALSE)
                {
                    $district = District::getsert($data[3]);
                    	
                    $region = Region::getsert($district->district_id, $data[2]);
                    
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
                
                $rows = [];
                $time = time();
                while (($data = fgetcsv($handle, 4096, "\t")) !== FALSE)
                {
                    $rows[] = [
                            'ip_start' => $data[0],
                            'ip_end' => $data[1],
                            'ip_range' => ($data[2] == '-') ? null : $data[2],
                            'ip_country' => $data[3],
                            'city_id' => ($data[4] == '-') ? null : $data[4],
                            'created_at' => $time,
                            'updated_at' => $time,
                    ];
                    if (count($rows) == 1000) {
                        Range::batchInsert($rows);
                        $rows = [];
                    }
                }
                if (count($rows) > 0) {
                    Range::batchInsert($rows);
                }
                fclose($handle);
            }
            $tran->commit();
        }
        
    }
}