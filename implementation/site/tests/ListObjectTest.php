<?php
// Call ListObjectTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ListObjectTest::main');
}

require_once('PHPUnit/Framework.php');

require_once('template_engine/ListObject.php');

require_once('set_path.php');


class ListObjectSample extends ListObject
{
	/**
	 * Creates the list of objects. Sets the parameter $p_hasNextElements to
	 * true if this list is limited and elements still exist in the original
	 * list (from which this was truncated) after the last element of this
	 * list.
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param bool $p_hasNextElements
	 * @return array
	 */
	public function createList($p_start, $p_limit, &$p_hasNextElements)
	{
		$objects = array('element 1', 'element 2', 'element 3', 'element 4',
						 'element 5', 'element 6', 'element 7', 'element 8');
		if ($p_start >= count($objects)) {
			$p_hasNextElements = false;
			return array();
		}

		if (!is_numeric($p_start)) {
			$p_start = 0;
		}
		if ($p_start < 0) {
			$p_start = 0;
		}

		if ($p_limit < 0) {
			$p_limit = 0;
		}
		if (!is_numeric($p_limit)) {
			$p_limit = 0;
		}

		$lastElement = $p_start + $p_limit;
		$p_hasNextElements = $lastElement < count($objects) && $lastElement != 0;
		if ($p_limit > 0) {
			return array_slice($objects, $p_start, $p_limit);
		}
		return array_slice($objects, $p_start);
	}

	/**
	 * Processes list constraints passed in a string.
	 *
	 * @param string $p_constraintsStr
	 * @return array
	 */
	public function processConstraints($p_constraintsStr)
	{
		return array();
	}

	/**
	 * Processes order constraints passed in a string.
	 *
	 * @param string $p_orderStr
	 * @return array
	 */
	public function processOrderString($p_orderStr)
	{
		return array();
	}
}

/**
 * Test class for ListObject.
 * Generated by PHPUnit on 2007-07-18 at 18:12:15.
 */
class ListObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once('PHPUnit/TextUI/TestRunner.php');

        $suite  = new PHPUnit_Framework_TestSuite('ListObjectTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testDefaultName().
     */
    public function testDefaultName()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(sha1(time()), $sampleListObject->defaultName());
    }

    /**
     * @todo Implement testIterator().
     */
    public function testIterator()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertTrue(is_a($sampleListObject->iterator(), 'ArrayIterator'));
    }

    /**
     * @todo Implement testIsLimited().
     */
    public function testIsLimited()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertFalse($sampleListObject->isLimited());

    	$sampleListObject = new ListObjectSample(0, 10);
    	$this->assertTrue($sampleListObject->isLimited());

    	$sampleListObject = new ListObjectSample(5, 5);
    	$this->assertTrue($sampleListObject->isLimited());
    }

    /**
     * @todo Implement testGetLimit().
     */
    public function testGetLimit()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(0, $sampleListObject->getLimit());

    	$sampleListObject = new ListObjectSample(0, 10);
    	$this->assertEquals(10, $sampleListObject->getLimit());

    	$sampleListObject = new ListObjectSample(0, 'invalid value');
    	$this->assertEquals(0, $sampleListObject->getLimit());
    }

    /**
     * @todo Implement testGetStart().
     */
    public function testGetStart()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(0, $sampleListObject->getStart());

    	$sampleListObject = new ListObjectSample(10);
    	$this->assertEquals(10, $sampleListObject->getStart());

    	$sampleListObject = new ListObjectSample('invalid value');
    	$this->assertEquals(0, $sampleListObject->getStart());
    }

    /**
     * @todo Implement testGetEnd().
     */
    public function testGetEnd()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(8, $sampleListObject->getEnd());

    	$sampleListObject = new ListObjectSample(5);
    	$this->assertEquals(8, $sampleListObject->getEnd());

    	$sampleListObject = new ListObjectSample(10);
    	$this->assertEquals(10, $sampleListObject->getEnd());

    	$sampleListObject = new ListObjectSample(4, 2);
    	$this->assertEquals(6, $sampleListObject->getEnd());
    }

    /**
     * @todo Implement testHasNextElements().
     */
    public function testHasNextElements()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertFalse($sampleListObject->hasNextElements());

    	$sampleListObject = new ListObjectSample(10);
    	$this->assertFalse($sampleListObject->hasNextElements());

    	$sampleListObject = new ListObjectSample(5, 5);
    	$this->assertFalse($sampleListObject->hasNextElements());

    	$sampleListObject = new ListObjectSample(4, 2);
    	$this->assertTrue($sampleListObject->hasNextElements());
    }

    /**
     * @todo Implement testGetColumn().
     */
    public function testGetColumn()
    {
    	$sampleListObject = new ListObjectSample(0, 0, null, null, 3);

    	$iterator = $sampleListObject->iterator();
    	$this->assertEquals(0, $sampleListObject->getColumn($iterator));

    	$iterator->next();
    	$this->assertEquals(1, $sampleListObject->getColumn($iterator));

    	$iterator->next();
    	$iterator->next();
    	$this->assertEquals(0, $sampleListObject->getColumn($iterator));
    }

    /**
     * @todo Implement testColumns().
     */
    public function testColumns()
    {
    	$sampleListObject = new ListObjectSample();
    	$this->assertEquals(0, $sampleListObject->columns());

    	$sampleListObject = new ListObjectSample(0, 0, null, null, 3);
    	$this->assertEquals(3, $sampleListObject->columns());
    }
}

// Call ListObjectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'ListObjectTest::main') {
    ListObjectTest::main();
}
?>