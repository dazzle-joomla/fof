<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Tests\DataModel\AbstracFilter;

use FOF30\Model\DataModel\Filter\AbstractFilter;
use FOF30\Tests\Helpers\DatabaseTest;
use FOF30\Tests\Helpers\ReflectionHelper;
use FOF30\Tests\Stubs\Model\DataModel\Filter\FilterStub;

require_once 'AbstractFilterDataprovider.php';
/**
 * @covers      FOF30\Model\DataModel\Filter\AbstractFilter::<protected>
 * @covers      FOF30\Model\DataModel\Filter\AbstractFilter::<private>
 * @package     FOF30\Tests\DataModel\Filter\AbstractFilter
 */
class AbstractFilterTest extends DatabaseTest
{
    /**
     * @group       AbstractFilter
     * @group       AbstractFilterConstruct
     * @covers      FOF30\Model\DataModel\Filter\AbstractFilter::__construct
     */
    public function test__construct()
    {
        $db = \JFactory::getDbo();
        $field = (object) array(
            'name' => 'test',
            'type' => 'test'
        );

        $filter = new FilterStub($db, $field);

        $this->assertEquals('test', ReflectionHelper::getValue($filter, 'name'), 'AbstractFilter::__construct Failed to set the field name');
        $this->assertEquals('test', ReflectionHelper::getValue($filter, 'type'), 'AbstractFilter::__construct Failed to set the fiel type');
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterConstruct
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::__construct
     * @dataProvider    AbstractFilterDataprovider::getTest__constructException
     */
    public function test__constructException($test)
    {
        $this->setExpectedException('InvalidArgumentException');

        $db = \JFactory::getDbo();

        new FilterStub($db, $test['field']);
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterIsEmpty
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::isEmpty
     * @dataProvider    AbstractFilterDataprovider::getTestIsEmpty
     */
    public function testIsEmpty($test, $check)
    {
        $msg = 'AbstractFilter::isEmpty %s - Case: '.$check['case'];

        $filter = new FilterStub(\JFactory::getDbo(), (object)array('name' => 'test', 'type' => 'test'));
        $filter->null_value = $test['null'];

        $result = $filter->isEmpty($test['value']);

        $this->assertSame($check['result'], $result, sprintf($msg, 'Failed to return the correct value'));
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterSearchMethods
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::getSearchMethods
     */
    public function testGetSearchMethod()
    {
        $filter = new FilterStub(\JFactory::getDbo(), (object)array('name' => 'test', 'type' => 'test'));

        $result = $filter->getSearchMethods();
        $result = array_values($result);

        $check = array('between', 'exact', 'partial', 'outside', 'interval', 'search', 'modulo', 'range');

        sort($result);
        sort($check);

        $this->assertEquals($check, $result, 'AbstractFilter::getSearchMethods Failed to detect the correct methods');
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterExact
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::exact
     * @dataProvider    AbstractFilterDataprovider::getTestExact
     */
    public function testExact($test, $check)
    {
        $msg = 'AbstractFilter::exact %s - Case: '.$check['case'];

        $field  = (object)array('name' => 'test', 'type' => 'varchar');

        $filter = $this->getMockBuilder('\FOF30\Tests\Stubs\Model\DataModel\Filter\FilterStub')
            ->setMethods(array('isEmpty', 'getFieldName', 'search'))
            ->setConstructorArgs(array(\JFactory::getDbo(), $field))
            ->getMock();

        $filter->method('isEmpty')->willReturn($test['mock']['isEmpty']);
        $filter->expects($check['name'] ? $this->once() : $this->never())->method('getFieldName')->willReturn('`test`');
        $filter->expects($check['search'] ? $this->once() : $this->never())->method('search')->willReturn('search');

        $result = $filter->exact($test['value']);

        $this->assertEquals($check['result'], $result, sprintf($msg, 'Return the wrong value'));
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterSearch
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::search
     * @dataProvider    AbstractFilterDataprovider::getTestSearch
     */
    public function testSearch($test, $check)
    {
        $msg = 'AbstractFilter::search %s - Case: '.$check['case'];

        $field  = (object)array('name' => 'test', 'type' => 'varchar');

        $filter = $this->getMockBuilder('\FOF30\Tests\Stubs\Model\DataModel\Filter\FilterStub')
            ->setMethods(array('isEmpty', 'getFieldName'))
            ->setConstructorArgs(array(\JFactory::getDbo(), $field))
            ->getMock();

        $filter->method('isEmpty')->willReturn($test['mock']['isEmpty']);
        $filter->method('getFieldName')->willReturn('`test`');

        $result = $filter->search($test['value'], $test['operator']);

        $this->assertEquals($check['result'], $result, sprintf($msg, 'Return the wrong value'));
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterGetFieldName
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::getFieldName
     */
    public function testGetFieldName()
    {
        $filter = new FilterStub(\JFactory::getDbo(), (object)array('name' => 'test', 'type' => 'test'));

        $result = $filter->getFieldName();

        $this->assertEquals('`test`', $result, 'AbstractFilter::getFieldName Failed to return the correct field name');
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterGetField
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::getField
     */
    public function testGetField()
    {
        $field = (object)array('name' => 'test', 'type' => 'int (10)');

        $result = AbstractFilter::getField($field, array('dbo' => \JFactory::getDbo()));

        $this->assertInstanceOf('\FOF30\Model\DataModel\Filter\AbstractFilter', $result, 'AbstractFilter::getField Failed to return the correct filter');
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterGetField
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::getField
     * @dataProvider    AbstractFilterDataprovider::getTestGetFieldException
     */
    public function testGetFieldException($test)
    {
        $this->setExpectedException('InvalidArgumentException');

        AbstractFilter::getField($test['field'], array());
    }

    /**
     * @group           AbstractFilter
     * @group           AbstractFilterGetFieldType
     * @covers          FOF30\Model\DataModel\Filter\AbstractFilter::getFieldType
     * @dataProvider    AbstractFilterDataprovider::getTestGetFieldType
     */
    public function testGetFieldType($test, $check)
    {
        $msg = 'AbstractFilter::getFieldType %s - Case: '.$check['case'];

        $result = AbstractFilter::getFieldType($test['type']);

        $this->assertEquals($check['result'], $result, sprintf($msg, 'Failed to get the correct field type'));
    }
}
