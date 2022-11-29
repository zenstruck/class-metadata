# zenstruck/class-metadata

[![CI](https://github.com/zenstruck/class-metadata/actions/workflows/ci.yml/badge.svg)](https://github.com/zenstruck/class-metadata/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/zenstruck/class-metadata/branch/1.x/graph/badge.svg?token=A8BKCMI6KD)](https://codecov.io/gh/zenstruck/class-metadata)

Add human-readable class aliases and scalar metadata to your classes with
an efficient runtime lookup. These can be added via [attributes](#attributes) or
[composer.json configuration](#composerjson).

1. **Alias**: short name for a class (could be used as an alternative to storing a FQCN
   in the database).
2. **Metadata**: key-value map of scalar values specific to a class (could be used
   to mark a class as _trackable_ in an auditing system).

This library provides a Composer plugin that hooks into Composer's dump-autoload
event to generate a lookup map for use at [runtime](#runtime-api).

## Installation

> **Note**: This package requires composer 2.4+.

1. `composer require zenstruck/class-metadata`
2. When asked to enable the Composer plugin, choose `y` (yes).

## Configuration

Metadata and aliases can be added to class' via [attributes](#attributes) or
[`composer.json`](#composerjson) config.

1. Aliases must be a non-empty string.
2. Only 1 alias allowed per class.
3. Multiple classes cannot have the same alias.
4. Metadata keys must be strings.
5. Metadata values must be scalar (`bool|float|int|string`).

**NOTE:** During development, when adding/changing/removing aliases and
metadata, you need to run `composer dump-autoload` for the changes to take
effect.

### Attributes

When creating the autoload configuration for your application, the composer
plugin scans your PSR-4 `autoload` path(s) (defined in your `composer.json`)
to look for classes with the `Alias` & `Metadata` attributes. These are
parsed and a mapping file is generated.

```php
namespace App\Entity;

use Zenstruck\Alias;
use Zenstruck\Metadata;

#[Alias('user')]
#[Metadata('track', true)]
#[Metadata('identifier', 'getId')]
class User
{
    // ...
}
```

#### Customize Paths

If you have a large code-base, scanning all the files could be time-consuming.
You can configure specific paths to scan in your `composer.json`:

```json
{
    "extra": {
        "class-metadata": {
            "paths": [
                "src/Domain/*/Entity"
            ]
        }
    }
}
```

You can disable path scanning entirely and only configure via your
[`composer.json`](#composerjson):

```json
{
    "extra": {
        "class-metadata": {
            "paths": false
        }
    }
}
```

### `composer.json`

Metadata and aliases can also be configured in your `composer.json`. This allows
adding aliases and metadata to 3rd party classes:

```json
{
    "extra": {
        "class-metadata": {
            "map": {
                "Vendor\\Code\\Class1": "class1",
                "Vendor\\Code\\Class2": {
                    "alias": "class2",
                    "key1": "value",
                    "key2": 7
                }
            }
        }
    }
}
```

For the mapping, a string is a shortcut for an alias (`class1` in the example
above). An array will be used as the metadata except the special `alias` key.
This value will be used as the _alias_ (`class2` in the example above).

## Runtime API

Since the alias/metadata maps are created when Composer creates the
autoload files, runtime lookups are fast - just fetching from generated
PHP _constant_ array's.

The following examples use the [user class shown above](#attributes).

### Alias Lookup

```php
use Zenstruck\Alias;

// alias for class lookup:
Alias::for(User::class); // "user" (string|null - the alias for User or null if none)
Alias::for(new User()); // "user" (alternatively, you can pass the object directly)

// class for alias lookup:
Alias::classFor('user'); // "App\Entity" (class-string|null - the FQCN whose alias is "user")
```

### Metadata Lookup

```php
use Zenstruck\Metadata;

// metadata for a class
Metadata::for(User::class); // ['track' => true, 'identifier' => 'getId'] (array<string,scalar> - metadata array for User or empty array if none)
Metadata::for(new User()); // ['track' => true, 'identifier' => 'getId'] (alternatively, you can pass the object directly)
Metadata::for('user'); // ['track' => true, 'identifier' => 'getId'] (alternatively, fetch metadata by a class' alias)

// metadata value for key
Metadata::get(User::class, 'track'); // true (scalar|null - the metadata value for "key" or null if none)
Metadata::get(new User(), 'track'); // true (alternatively, you can pass the object directly)
Metadata::get('user', 'track'); // true (alternatively, fetch metadata by a class' alias)

// "first" metadata value for list of keys
Metadata::first(User::class, 'audit_id', 'identifier', 'id'); // "getId" (scalar|null - first metadata value found for" keys" (left to right) or null if none)
Metadata::first(new User(), 'audit_id', 'identifier', 'id'); // "getId" (alternatively, you can pass the object directly)
Metadata::first('user', 'audit_id', 'identifier', 'id'); // "getId" (alternatively, fetch metadata by a class' alias)

// all classes with metadata key
Metadata::classesWith('identifier'); // ["App\Entity"] (class-string[] - FQCN's that have metadata with key "identifier")
```

## `list-class-metadata` Command

A custom Composer command is provided to debug/list all classes that
have metadata/aliases configured:

```bash
composer list-class-metadata
```

Using the [User example above](#attributes), the following would be output:

```
 ----------------- ------- -------------------------------------
  Class             Alias   Metadata
 ----------------- ------- -------------------------------------
  App\Entity\User   user    {"track":true,"identifier":"getId"}
 ----------------- ------- -------------------------------------
```

## Doctrine Bridge

### `AliasManagerRegistry`

This is a decorated `ManagerRegistry` that allows using aliases for the
`getRepository()` and `getManagerForClass()` methods:

```php
use Zenstruck\Metadata\Bridge\Doctrine\AliasManagerRegistry;

/** @var \Doctrine\Persistence\ManagerRegistry $inner */

$registry = new AliasManagerRegistry($inner);

$registry->getRepository('user'); // converts "user" alias to FQCN
$registry->getRepository(User::class); // can still use FQCN

$registry->getManagerForClass('user'); // converts "user" alias to FQCN
$registry->getManagerForClass(User::class); // can still use FQCN
```
