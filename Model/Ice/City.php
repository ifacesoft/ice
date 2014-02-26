<?php

namespace ice\model\ice;

use ice\core\Model;


/**
 * Class City
 *
 * @property string $city_translit
 * @property string $city_name
 * @property int    $code
 */
class City extends Model
{
    const DEFAULT_TRANSLIT = 'moskva';
    /**
     * @var City|null
     */
    private static $_city = null;

    /**
     * Устанавливаем текущий город
     *
     * @param City $city
     */
    private static function setCurrent(City $city)
    {
        Session::getCurrent()->switchCity($city);
        self::$_city = $city;
    }

    /**
     * Получить город по умолчанию
     *
     * @return Model|null
     */
    public static function getDefault()
    {
        return City::getQuery()
            ->eq('/translit', City::DEFAULT_TRANSLIT)
            ->execute()
            ->getModel();
    }

    /**
     * Получить текущий город
     *
     * @throws Page_Not_Found_Exception
     * @return City|null
     */
    public static function getCurrent()
    {
        if (self::$_city) {
            return self::$_city;
        }

        $session = Session::getCurrent();

        if ($session->city__fk) {
            self::$_city = $session->City;

            if (self::$_city) {
                return self::$_city;
            }
        }

        $city = null;

        // Новокузнецк
//        $geoIpCity = geoip_record_by_name('77.238.106.217');
        $geoIpCity['city'] = 'Novokuznetsk';
        $query = Query::instance('from', 'Location')
            ->eq('Location.city', $geoIpCity['city']);

        $locations = Model_Collection_Manager::byQuery($query)->raw();

        if (!empty($locations)) {
            $query = Query::instance('from', 'City')
                ->select('City.*')
                ->whereIn('City.Location__fk', array_column($locations, 'location_pk'));

            $city = Model_Manager::byQuery($query);

            if (!$city) {
                $fields = array(
                    'city_translit' => $geoIpCity['city'],
                    'location__fk' => reset($locations)['location_pk']
                );

                $city = City::create($fields)->save();
            }
        }

        if (!$city) {
            $city = City::getDefault();
        }

        if (!$city) {
            throw new Page_Not_Found_Exception('Город не определен');
        }

        City::setCurrent($city);

        return self::$_city;
    }


//    /**
//     * Установить в качестве текущего города произвольный город
//     *
//     * @param Model $city
//     */
//    public static function setCurrent(Model $city)
//    {
//        self::$currentCity = $city;
//    }
//
//    /**
//     * Сгенерить текущий урл с заменой города на текущий
//     *
//     * @return string
//     */
//    public function generateUrl()
//    {
//        $route = trim(Router::getRoute()->route, '/');
//        $uri = trim(Request::uri(true), '/');
//        $patternParameter = '{$cityTranslitName}';
//        $routeParts = explode('/', $route);
//        $uriParts = explode('/', $uri);
//        if (count($routeParts) != count($uriParts)) {
//            return $uri;
//        }
//        $allParts = array_combine($routeParts, $uriParts);
//        foreach ($allParts as $routeParam => &$uriParam) {
//            if (false !== strpos($routeParam, $patternParameter)) {
//                $uriParam = $this->translitName();
//            }
//        }
//        $resUri = implode('/', $allParts);
//
//        return '/' . $resUri . '/';
//    }
//
//    /**
//     * Вернуть параметер модели отвечающий
//     * за идентификацию города
//     *
//     * @return string
//     */
//    public function translitName()
//    {
//        return $this->city_translit;
//    }
//
    /**
     * Вернуть название города
     */
    public function getName()
    {
        return $this->city_name ? $this->city_name : $this->city_name_en;
    }
}