<?php

class RulesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAllRules()
    {
        $this->assertInternalType('array', Mascame\Katina\Rules::getRules());
        $this->assertNotEmpty(Mascame\Katina\Rules::getRules());
    }

    public function testGetRule()
    {
        $this->assertTrue(is_callable(Mascame\Katina\Rules::get('any')));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRuleException()
    {
        Mascame\Katina\Rules::get('not_found');
    }

    public function testAddNewRules()
    {
        Mascame\Katina\Rules::setRules(['number_five' => 5]);

        $rules = Mascame\Katina\Rules::getRules();

        $this->assertInternalType('array', $rules);
        $this->assertArrayHasKey('number_five', $rules);
    }
}
