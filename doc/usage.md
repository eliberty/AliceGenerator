# Usage

## Basic Usage

There are two primary methods you can use in the `FixtureGenerator`, these are:

   * `string generateYaml ( object|array<object> $objects [, FixtureGenerationContext $context ] )` - Returns a string of formatted YAML Alice fixtures
   * `array generateArray ( object|array<object> $objects [, FixtureGenerationContext $context ] )` - Returns an array of Alice fixtures

Example:

```php
<?php
use Trappar\AliceGenerator\FixtureGeneratorBuilder;

class MyObject
{
    public $foo;
}

$obj = new MyObject();
$obj->foo = 'bar';

$yaml = FixtureGeneratorBuilder::create()->build()->generateYaml($obj);
echo $yaml; // Or save this to the filesystem
```

## Fixture Generation Contexts

Fixture Generation Contexts allow you to specify options which will a particular fixture generation run. Using them is easy:

```php
<?php
use Trappar\AliceGenerator\FixtureGenerationContext;

// This will include only the Post
$fixtureGenerator->generateYaml(
    $post,
    FixtureGenerationContext::create()
        ->setMaximumRecursion(2)
);
```

### Limiting Recursion

Since generating fixtures involves recursing through objects, you will often find yourself in a situation where generating fixtures yields quite a lot more information than you would like - this is just the nature of recursion! `FixtureGenerationContext` offers two solutions to this problem.

Pictures make these concepts quite a lot easier to grok, but it's important to understand what these diagrams mean. Here's a key for these diagrams:

   * Each rounded square represents an Object
   * Objects store references to each other using properties like `$user->posts` (think Doctrine relations)
   * Green object(s) are initially passed to the `FixtureGenerator`
   * Blue object(s) are included in the generated fixtures (Green is also included)
   * Red object(s) are excluded due to the particular option set in the `FixtureGenerationContext`
   * White object(s) aren't touched in any way in the process of generating fixtures

#### Limiting Recursion Depth

`setMaximumRecursion( int $depth )` - allows you to limit the recursion depth. Examples:

![depth0](https://cloud.githubusercontent.com/assets/525726/17834184/478734ce-66e9-11e6-93f9-2726e5ffdc22.png)

![depth1](https://cloud.githubusercontent.com/assets/525726/17834183/4785e9fc-66e9-11e6-8bb0-50c1a9b97c00.png)

![depth2](https://cloud.githubusercontent.com/assets/525726/17834171/f7222b42-66e8-11e6-9f9a-4632abf4e1bd.png)

#### Limiting Recursion With Object Constraints

Fixture generation will traverse from one object to related objects through an object's properties. In our example objects, generating Fixtures with no maximum recursion on virtually any object can theoretically result in practically the whole database being dumped as Fixtures. For example, since a Post has a User and that User is part of a Group - any Users also in that Group and all Posts for all of those Users will be returned. Object constraints give you a way to avoid this problem.

`addPersistedObjectConstraint( object $constrainingObject )` - allows you to limit any object type to those that you specify. Examples:

![userconstrained](https://cloud.githubusercontent.com/assets/525726/17834169/f720e5e8-66e8-11e6-90ff-1107e412da71.png)

![postsconstrained](https://cloud.githubusercontent.com/assets/525726/17834170/f7218eee-66e8-11e6-8234-b495fd00299b.png)

### Customizing Reference Naming Strategy

`setReferenceNamer( ReferenceNamerInterface $referenceNamer )` - allows you to specify a custom class for handling reference names.

There are two ReferenceNamer classes included in this library:

* `Trappar\AliceGenerator\ReferenceNamer\ClassNamer` (default) - names references like '@User-1'.
* `Trappar\AliceGenerator\ReferenceNamer\NamespaceNamer` - names references like '@Appbundle-Entity-Post-1'.

Example:

```php
<?php
use Trappar\AliceGenerator\FixtureGenerationContext;

// Using the alternative NamespaceNamer with default options ignores "AppBundle" and "Entity", so references will still be like "Post-1"
$namer = new \Trappar\AliceGenerator\ReferenceNamer\NamespaceNamer();
$fixtureGenerator->generateYaml($post, FixtureGenerationContext::create()->setReferenceNamer($namer));

// NamespaceNamer has several options...
$namer->setIgnoredNamespaces(['AppBundle']);
$namer->setNamespaceSeparator('-');

// Now references will be like "Entity-Post-1"
$fixtureGenerator->generateYaml($post, FixtureGenerationContext::create()->setReferenceNamer($namer));
```

You can also create your own ReferenceNamer by implementing `Trappar\AliceGenerator\ReferenceNamer\ReferenceNamerInterface` in your class.

### Force Inclusion of Properties at Default Values

`setExcludeDefaultValues( bool $exclude )` - Setting this to true/false causes properties to be handled differently during fixture generation.

* When true (default) - a property which is the same value as a newly initialized object of the same class will be excluded from fixture generation
* When false - all properties will be included during fixture generation

[Back to Table of Contents](/README.md#table-of-contents)