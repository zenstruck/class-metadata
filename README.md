# zenstruck/class-metadata

## Installation

1. `composer require zenstruck/class-metadata`
2. When asked to enable the Composer plugin, choose `y` (yes).

## Configuration

### Attributes

#### Customize Namespaces

### `composer.json`

## Runtime API

Since the alias/metadata maps are created when Composer creates the
autoload files, runtime lookups are fast - just fetching from generated
PHP _constant_ array's.

### Alias Lookup

```php
use Zenstruck\Alias;

// alias for class lookup:
Alias::for(MyClass::class); // string|null - the alias for MyClass or null if none
Alias::for(new MyClass()); // alternatively, you can pass the object directly

// class for alias lookup:
Alias::classFor('user'); // class-string|null - the FQCN whose alias is "user"
```

### Metadata Lookup

```php
use Zenstruck\Metadata;

// metadata for a class
Metadata::for(MyClass::class); // array<string,scalar> - metadata array for MyClass or empty array if none
Metadata::for(new MyClass()); // alternatively, you can pass the object directly
Metadata::for('alias'); // alternatively, fetch metadata by a class' alias

// metadata value for key
Metadata::get(MyClass::class, 'key'); // scalar|null - the metadata value for "key" or null if none
Metadata::get(new MyClass(), 'key'); // alternatively, you can pass the object directly
Metadata::get('alias', 'key'); // alternatively, fetch metadata by a class' alias

// "first" metadata value for list of keys
Metadata::first(MyClass::class, 'key1-', 'key-2', 'key-n'); // scalar|null - first metadata value found for" keys" (left to right) or null if none
Metadata::first(new MyClass(), 'key1-', 'key-2', 'key-n'); // alternatively, you can pass the object directly
Metadata::first('alias', 'key1-', 'key-2', 'key-n'); // alternatively, fetch metadata by a class' alias

// all classes with metadata key
Metadata::classesWith('identifier'); // class-string[] - FQCN's that have metadata with key "identifier"
```

## `list-class-metadata` Command

A custom Composer command is provided to debug/list all classes that
have metadata/aliases configured:

```bash
composer list-class-metadata
```
