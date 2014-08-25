<?php
namespace Dreamscapes;

use Codeception\Util\Stub;

// @codingStandardsIgnoreStart

// Prepare a test Enumeration
class TestEnum extends Enumeration
{
    const TestMember  = 0;
    const OtherMember = 1;
    const FalseMember = false;
    const TrueMember  = true;
}

class AnotherEnum extends Enumeration
{
    const DifferentMember = 'some value';
}

// @codingStandardsIgnoreEnd


class EnumerationTest extends \Codeception\TestCase\Test
{
    protected $tester;


    public function testEnumerationsCannotBeInstantiated()
    {
        $refl  = new \ReflectionClass('Dreamscapes\TestEnum');

        $this->assertFalse($refl->getConstructor()->isPublic());
        $this->assertTrue($refl->getConstructor()->isFinal());
    }

    public function testEachEnumerationMustBeIsolated()
    {
        $this->assertSame('some value', AnotherEnum::getValue('DifferentMember'));
        $this->assertSame(1, TestEnum::getValue('OtherMember'));

        $this->setExpectedException('ErrorException');
        TestEnum::getValue('DifferentMember');
    }

    public function testAccessingUndefinedEnumerationMemberShouldTriggerError()
    {
        $this->setExpectedException('ErrorException');
        TestEnum::NonExistent();
    }

    public function testEnumerationCanTranslateValueToMemberName()
    {
        $this->assertEquals('TestMember', TestEnum::getName(0));
    }

    public function testGetNameMethodIsTypeSensitive()
    {
        $this->assertSame('TestMember', TestEnum::getName(0));
        $this->assertSame('FalseMember', TestEnum::getName(false));
    }

    public function testEnumerationCanReturnMembersViaMethodCall()
    {
        $this->assertSame(1, TestEnum::getValue('OtherMember'));
        $this->assertSame(false, TestEnum::getValue('FalseMember'));
    }

    public function testMemberExistenceMethod()
    {
        $this->assertTrue(TestEnum::isDefined('TestMember'));
        $this->assertTrue(TestEnum::isDefined('FalseMember'));

        $this->assertFalse(TestEnum::isDefined('ImaginaryMember'));
    }

    public function testRetrievingAllMembersShouldReturnOrderedListOfMembers()
    {
        $expected = [
            'TestMember',
            'OtherMember',
            'FalseMember',
            'TrueMember',
        ];

        $this->assertSame($expected, TestEnum::allMembers());
    }

    public function testGetTypeReturnsOnlyClassName()
    {
        $this->assertSame('TestEnum', TestEnum::getType());
        $this->assertSame('AnotherEnum', AnotherEnum::getType());
    }

    public function testEnumerationMembersAreActuallyCallable()
    {
        $this->assertTrue(is_callable('TestEnum', 'TestMember'));
    }

    public function testCallingEnumerationMemberReturnsInstanceOfTheEnumeration()
    {
        $this->assertInstanceOf('Dreamscapes\TestEnum', TestEnum::TestMember());
    }

    public function testOnlySingleInstanceForEachEnumeratedMemberExists()
    {
        $first  = TestEnum::TestMember();
        $second = TestEnum::TestMember();

        $this->assertSame($first, $second);
    }

    public function testInstancesOfEnumerationMembersCanBeUsedAsStrings()
    {
        $this->assertEquals('TrueMember', (string)TestEnum::TrueMember());
        $this->assertEquals('FalseMember', (string)TestEnum::FalseMember());
        $this->assertEquals('TestMember', (string)TestEnum::TestMember());
    }

    public function testGetValueMethodAcceptsInstanceOfEnumeration()
    {
        $instance = TestEnum::TestMember();

        $this->assertSame(0, TestEnum::getValue($instance));
    }

    public function testIsDefinedMethodAcceptsInstanceOfEnumeration()
    {
        $instance = TestEnum::TestMember();

        $this->assertSame(true, TestEnum::isDefined($instance));
    }

    public function testInstancesHaveValueMethod()
    {
        $instance = TestEnum::TestMember();
        $this->assertSame(TestEnum::TestMember, $instance->value());
    }
}
