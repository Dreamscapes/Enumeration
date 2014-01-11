# Enumerations for php [![Build Status](https://api.travis-ci.org/Dreamscapes/Enumeration.png)](https://travis-ci.org/Dreamscapes/Enumeration) [![Dependency Status](https://gemnasium.com/Dreamscapes/Enumeration.png)](https://gemnasium.com/Dreamscapes/Enumeration)

Every php programmer at some point wants to put some structure to all those constants defined in their code. Usually one would put those constants under a dedicated class, but that's it; no additional features, no real benefit, no nothing.

## What if you could get more?

This small library aims at providing additional features to your "Enumerations" - a set of static values that represent something meaningful to your code.

## The problem with constants

Let's say our program will work with animals - various different animals, and we would like each type of animal to have a distinct value ( which I completely made up in this example ). Here's what many programmers currently do:

```php
define( 'MYAPP_ANIMAL_HORSE', 0 );
define( 'MYAPP_ANIMAL_DOG', 1 );
define( 'MYAPP_ANIMAL_CAT', 2 );
// ...
```

While this certaily works, there is a better way of defining those.

```php
namespace MyApp;

class Animal
{
  const Horse = 0;
  const Dog = 1;
  const Cat = 2;
}
```

Defining the constants as a class has several benefits:

1. You can use real namespacing, which can save you a few typing when using the class
2. It feels more natural to use `Animal::Horse` than `MYAPP_ANIMAL_HORSE`
3. Since it's a class it opens up new possibilities and ideas -> that's where this library comes to use :)

## How this library helps

The above example with a class introduces some issues but also opens up new possibilities.

1. There's nothing preventing the programmer from instantiating the class
2. What if you wanted to do it the opposite way? -> you have a value and you want to know the constant's name that holds such value in the enumeration?
3. What if you wanted to check if a constant is defined in the Enumeration?
4. What if you wanted to type-hint an enumerated value in a function/method's parameter list?

Let's take a look at another example that demonstrates the use of **Enumerations** provided by this library.

```php
namespace MyApp;

// Let's extend our Enumeration class
class Animal extends \Dreamscapes\Enumeration
{
  const Horse = 0;
  const Dog = 1;
  const Cat = 2;
}

// So far looks the same, but watch now...

$animal = new Animal; // Raises fatal error (private constructor)

Animal::isDefined( 'Horse' ); // Returns (bool)true
Animal::isDefined( 'Cow' ); // Returns (bool)false

// "Reverse resolution"
$value = Animal::Dog;
echo Animal::getName( $value ); // prints (string)"Dog"

// Type-hinting
function doSomething( Animal $animal )
{
  // $animal is now an instance of the Animal class
  // that can be easily represented as string
  echo $animal;
}
doSomething( Animal::Horse() ); // prints (string)"Horse"

// To get the actual value
$value = $animal->value();
// Or, use the Enumeration::getValue() class method
$value = Animal::getValue( $animal );
```

As you can see, suddenly there's much more you can possibly do with a class as simple as enumeration can be. Learn more in the [API Docs](http://dreamscapes.github.io/Enumeration/docs) which also include code examples and full method description.

## Installation

### Via Composer:

`composer require dreamscapes\enumeration:dev-master`

Composer's autoloading is supported so as long as you `require "vendor/autoload.php";` somewhere in your code you can simply start using it.

## Documentation

[API documentation](http://dreamscapes.github.io/Enumeration/docs) is available online - it includes all public methods you can use and also several code samples and use cases.

### Offline documentation

Sure! Just install the development dependencies and generate the docs.

```
composer require --dev dreamscapes\enumeration:dev-master
php vendor/bin/phpdoc.php
```

Documentation is now available at *./docs/index.html*.

## License

This software is licensed under the **BSD (3-Clause) License**. See the [LICENSE](LICENSE) file for more information.
