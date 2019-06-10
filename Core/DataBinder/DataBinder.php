<?php


namespace Core\DataBinder;


class DataBinder
{

    public function bindData($data, $class)
    {
        $explodeData = [];
        foreach ($data as $key => $value) {
            $explodeValue = explode('_', $key);

            if ( count($explodeValue) > 1 ) {
                $string = '';
                foreach ($explodeValue as $key => $val) {
                    $string = $string . ucfirst($val);
                }
                $explodeData[$string] = $value;
            }else {
                $explodeData[ucfirst($key)] = $value;
            }
        }

        print_r($explodeData);
        $classFunctions = get_class_methods($class);

        foreach ( $explodeData as $key => $value ) {
            $function = 'set' . $key;
            if ( in_array($function, $classFunctions) ) {
                $class->$function($value);
            }
        }

        return $class;
    }
}