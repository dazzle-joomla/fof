<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

class BooleanDataprovider
{
    public static function getTestIsEmpty()
    {
        $data[] = array(
            array(
                'value' => ''
            ),
            array(
                'case'   => 'Empty string',
                'result' => true
            )
        );

        $data[] = array(
            array(
                'value' => 123
            ),
            array(
                'case'   => 'Integers',
                'result' => false
            )
        );

        $data[] = array(
            array(
                'value' => array()
            ),
            array(
                'case'   => 'Empty array',
                'result' => false
            )
        );

        $data[] = array(
            array(
                'value' => new stdClass()
            ),
            array(
                'case'   => 'Object',
                'result' => false
            )
        );

        $data[] = array(
            array(
                'value' => null
            ),
            array(
                'case'   => 'Null value',
                'result' => true
            )
        );

        return $data;
    }
}
