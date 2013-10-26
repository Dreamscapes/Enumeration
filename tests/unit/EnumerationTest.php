<?php
use Codeception\Util\Stub;

// Prepare a test Enumeration
class TestEnum extends Enumeration\Enumeration
{
  const TestMember = 0;
  const OtherMember = 1;
  const FalseMember = false;
}

class AnotherEnum extends Enumeration\Enumeration
{
  const DifferentMember = 'some value';
}


class EnumerationTest extends \Codeception\TestCase\Test
{
  protected $codeGuy;

  protected function _before() {}
  protected function _after() {}


  public function testEnumerationsCannotBeInstantiated()
  {
    $this->setExpectedException( 'Exception' );

    $enum = new TestEnum;
  }

  public function testEachEnumerationsMustBeIsolated()
  {
    $this->assertSame( 'some value', AnotherEnum::getValue( 'DifferentMember') );
    $this->assertSame( 1, TestEnum::getValue( 'OtherMember' ) );

    $this->assertNull( TestEnum::getValue( 'DifferentMember' ) );
  }

  public function testEnumerationCanTranslateValueToMemberName()
  {
    $this->assertEquals( 'TestMember', TestEnum::getName( 0 ) );
  }

  public function testGetNameMethodIsTypeSensitive()
  {
    $this->assertSame( 'TestMember', TestEnum::getName( 0 ) );
    $this->assertSame( 'FalseMember', TestEnum::getName( false ) );

    $this->assertNull( TestEnum::getName( '0'  ) );
  }

  public function testEnumerationCanReturnMembersViaMethodCall()
  {
    $this->assertSame( 1, TestEnum::getValue( 'OtherMember' ) );
    $this->assertSame( false, TestEnum::getValue( 'FalseMember' ) );
  }

  public function testMemberExistenceMethod()
  {
    $this->assertTrue( TestEnum::isDefined( 'TestMember' ) );
    $this->assertTrue( TestEnum::isDefined( 'FalseMember' ) );

    $this->assertFalse( TestEnum::isDefined( 'ImaginaryMember' ) );
  }

  public function testRetrievingAllMembersShouldReturnOrderedListOfMembers()
  {
    $expected = [
      'TestMember',
      'OtherMember',
      'FalseMember',
    ];

    $this->assertSame( $expected, TestEnum::allMembers() );
  }

  public function testGetTypeReturnsOnlyClassName()
  {
    $this->assertSame( 'TestEnum', TestEnum::getType() );
    $this->assertSame( 'AnotherEnum', AnotherEnum::getType() );
  }
}
