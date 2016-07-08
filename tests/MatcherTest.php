<?php

/**
 * Changes matcher method visibility
 * 
 * Class MatcherExtension
 */
class MatcherExtension extends \Mascame\Katina\Matcher {

    public static function matcher($value, $pattern) {
        return parent::matcher($value, $pattern);
    }

}

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchConstant()
    {
        $this->assertTrue(MatcherExtension::matcher(1, 1));
        $this->assertTrue(MatcherExtension::matcher('string', 'string'));
        $this->assertTrue(MatcherExtension::matcher(true, true));
        $this->assertFalse(MatcherExtension::matcher(1, 2));
        $this->assertFalse(MatcherExtension::matcher('string', 'other_string'));
        $this->assertFalse(MatcherExtension::matcher('string', 'other_string'));
    }

    public function testMatcher()
    {
        $this->assertTrue(MatcherExtension::matcher(1, ':integer'));
        $this->assertFalse(MatcherExtension::matcher('not_integer', ':integer'));
    }

    public function testMatcherMulti()
    {
        $this->assertTrue(MatcherExtension::matcher('1', ':string number'));
        $this->assertFalse(MatcherExtension::matcher('string', ':string number'));
    }

    public function testMatcherWithArgs()
    {
        $this->assertTrue(MatcherExtension::matcher(6, ':integer gt(5)'));
        $this->assertFalse(MatcherExtension::matcher(4, ':integer gt(5)'));
        $this->assertTrue(MatcherExtension::matcher(4, ':integer between(1,5)'));
        $this->assertFalse(MatcherExtension::matcher(7, ':integer between(1,5)'));
    }

    public function testMatchEmptyString()
    {
        $this->assertTrue(MatcherExtension::matcher('any_value', ''));
    }
}
