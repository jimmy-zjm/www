<?php
/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1  起点纬度
 * @param  Decimal $longitude2 终点经度 
 * @param  Decimal $latitude2  终点纬度
 * @param  Int     $unit       单位 1:米 2:公里
 * @param  Int     $decimal    精度 保留小数位数
 * @return Decimal
 */
function getDistance($longitude1, $latitude1, $longitude2, $latitude2, $unit=2, $decimal=2){

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;

    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;

    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI /180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    if($unit==2){
        $distance = $distance / 1000;
    }

    return round($distance, $decimal);

}

// 起点坐标
$longitude1 = 121.5298985180;
$latitude1 = 31.0865197296;

// 终点坐标
$longitude2 = 121.4999290699;
$latitude2 = 31.1653439592;

$distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 1);
echo $distance.'m'; // 2342.38m

$distance = getDistance($longitude1, $latitude1, $longitude2, $latitude2, 2);
echo $distance.'km'; // 2.34km

?>

http://api.map.baidu.com/direction?origin=latlng:31.0865197296,121.5298985180|name:我家&destination=思浦小区&mode=driving&region=中国&output=html&src=yourCompanyName|yourAppName
 
//调起百度PC或Web地图，展示"西安市"从（lat:34.264642646862,lng:108.95108518068 ）"我家"到"大雁塔"的驾车路线。