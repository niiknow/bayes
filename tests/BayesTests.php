<?php
namespace Tests;
use niiknow\Bayes;

class BayesTest extends \PHPUnit_Framework_TestCase
{
    public function test_sample_class()
    {
        $this->assertTrue(is_object(new Bayes()));
    }
    public function test_not_implement()
    {
        //todo
        $this->markTestIncomplete();
    }
}
