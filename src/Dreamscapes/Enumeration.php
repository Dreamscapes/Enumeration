<?php

/**
 * Enumerations
 *
 * Licensed under the BSD (3-Clause) license
 * For full copyright and license information, please see the LICENSE file
 *
 * @copyright   2013 Robert Rossmann
 * @author      Robert Rossmann <rr.rossmann@me.com>
 * @link        https://github.com/Dreamscapes/Enumeration
 * @license     http://choosealicense.com/licenses/bsd-3-clause   BSD (3-Clause) License
 */


namespace Dreamscapes;

/**
 * The Enumeration class
 *
 * All enumerations should extend this class. Enumerated members
 * should be defined as class constants.
 *
 * @package     Enumeration
 */
class Enumeration
{

  private static $instances = [];   // Instances of enumeration members are cached here


  private $value;                   // Each instance of an Enumeration holds the member's value here


  /**
   * Instances are not allowed to be created by users
   */
  final private function __construct( $name, $value )
  {
    $this->name   = $name;
    $this->value  = $value;
  }

  /**
   * Get the name of the member that holds given value
   *
   * <p class="alert">This method is type-sensitive - see the example below.</p>
   * <h3>Use case</h3>
   * You have a value that is defined in an enumeration and you would like
   * to know the name of the enumeration member that holds this value.
   *
   * <h3>Example</h3>
   * <code>
   * class Animal extends Dreamscapes\Enumeration
   * {
   *   const Horse = 0;
   *   const Dog = 1;
   * }
   *
   * echo Animal::getName( 0 ); // Prints 'Horse'
   * echo Animal::getName( '0' ); // Returns null, method is type-sensitive
   * </code>
   *
   * @param     string      $value     The member's expected value. <b>The
   *                                   value must be of the same type as defined
   *                                   in the Enumeration.</b>
   *
   * @return    string      The name of the member that holds this value, null
   *                        if no such member exists
   */
  public static function getName( $value )
  {
    $key = array_search( $value, static::toArray(), true );  // Search using strict comparison
    if ( $key === false ) static::triggerUndefinedConstantError( $value );

    return $key;
  }

  /**
   * Get the value of a given member's name
   *
   * <h3>Use case</h3>
   * You have a string representation of the Enumeration member and you would
   * like to know the value that member holds.
   *
   * <h3>Example</h3>
   * <code>
   * class Animal extends Dreamscapes\Enumeration
   * {
   *   const Horse = 0;
   *   const Dog = 1;
   * }
   *
   * echo Animal::getValue( 'Dog' ); // Prints an integer, 1
   * </code>
   *
   * @param     string|Enumeration      $member     The member's expected name
   *
   * @return    bool                                The value of the member
   */
  public static function getValue( $member )
  {
    // Typecast to string (we could be getting either a string or an instance of Enumeration in $member)
    $member = (string)$member;
    ( ! static::isDefined( $member ) ) && static::triggerUndefinedConstantError( $member );

    return static::toArray()[$member];
  }

  /**
   * Does a member with this name exist in the enumeration?
   *
   * <h3>Example</h3>
   * <code>
   * class Animal extends Dreamscapes\Enumeration
   * {
   *   const Horse = 0;
   *   const Dog = 1;
   * }
   *
   * echo Animal::isDefined( 'Dog' ); // Prints an integer, 1 (bool true)
   * echo Animal::isDefined( 'Cat' ); // Prints nothing (bool false)
   * </code>
   *
   * @param     string      $member     The member's expected name
   *
   * @return    bool                    <b>true</b> if such member is defined, <b>false</b> otherwise
   */
  public static function isDefined( $member )
  {
    return array_key_exists( (string)$member, static::toArray() );
  }

  /**
   * Get all members defined in this Enumeration
   *
   * <p class="alert">The returned array's order is determined by the order
   * in which the constants are defined in the class.</p>
   *
   * <h3>Example</h3>
   * <code>
   * class Animal extends Dreamscapes\Enumeration
   * {
   *   const Horse = 0;
   *   const Dog = 1;
   * }
   *
   * print_r( Animal::allMembers() );
   * // Array
   * // (
   * //   0 => 'Horse'
   * //   1 => 'Dog'
   * // )
   * </code>
   *
   * @return    array     An ordered list of all Enumeration members
   */
  public static function allMembers()
  {
    return array_keys( static::toArray() );
  }

  /**
   * Convert the Enumeration into an array
   *
   * <h3>Example</h3>
   * <code>
   * class Animal extends Dreamscapes\Enumeration
   * {
   *   const Horse = 0;
   *   const Dog = 1;
   * }
   *
   * print_r( Animal::allMembers() );
   * // Array
   * // (
   * //   'Horse' => 0
   * //   'Dog' => 1
   * // )
   * </code>
   *
   * @return    array
   */
  public static function toArray()
  {
    $enumClass = new \ReflectionClass( get_called_class() );

    return $enumClass->getConstants();
  }

  /**
   * Get the string representation of the Enumeration, without namespace
   *
   * <h3>Example</h3>
   * <code>
   * namespace Fauna;
   *
   * class Animal extends Dreamscapes\Enumeration {}
   *
   * echo Animal::getType(); // Animal
   * echo Dreamscapes\Enumeration::getType(); // Enumeration
   * </code>
   *
   * @return    string      The name of the Enumeration class, without namespace
   */
  public static function getType()
  {
    $type = get_called_class();

    return end( explode( "\\", $type ) );
  }

  /**
   * Maps static method calls to defined enumeration members
   *
   * @internal
   *
   * @return    Enumeration     Instance of the Enumeration subclass
   */
  public static function __callStatic( $method, $args )
  {
    return static::getMemberInstance( get_called_class(), $method );
  }

  /**
   * Allow enumeration members to be typecast to strings
   *
   * @return    string      The value of the enumeration member in a string representation
   */
  public function __toString()
  {
    return (string)$this->name;
  }

  /**
   * Factory for enumeration members' instance representations
   *
   * @param     string        $enumeration    The class for which the instance should be obtained/generated
   * @param     string        $member         The enumerated member's name to retrieve
   *
   * @return    Enumeration   An instance of the Enumeration subclass, representing the given member's value
   */
  final private static function getMemberInstance( $enumeration, $member )
  {
    if ( ! isset( self::$instances[$enumeration] ) || ! isset( self::$instances[$enumeration][$member] ) )
    {
      // Instance of this enumeration member does not exist yet - let's create one
      self::$instances[$enumeration][$member] = new $enumeration( $member, static::getValue( $member ) );
    }

    return self::$instances[$enumeration][$member];
  }

  final private static function triggerUndefinedConstantError( $const )
  {
    $trace = debug_backtrace();
      trigger_error(
        'Undefined class constant ' . $const .
        ' in ' . $trace[1]['file'] .
        ' on line ' . $trace[1]['line'],
        E_USER_ERROR );
  }
}
