# Laravel Data Mapper

[![Latest Stable Version](https://poser.pugx.org/markusjwetzel/laravel-datamapper/v/stable)](https://packagist.org/packages/markusjwetzel/laravel-datamapper) [![Total Downloads](https://poser.pugx.org/markusjwetzel/laravel-datamapper/downloads)](https://packagist.org/packages/markusjwetzel/laravel-datamapper) [![Latest Unstable Version](https://poser.pugx.org/markusjwetzel/laravel-datamapper/v/unstable)](https://packagist.org/packages/markusjwetzel/laravel-datamapper) [![License](https://poser.pugx.org/markusjwetzel/laravel-datamapper/license)](https://packagist.org/packages/markusjwetzel/laravel-datamapper)

**Important: For now the package does not work! The first version of the Laravel Data Mapper is actually under development and will be published before the end of may (plus some more days). You can star this repository to show your interest in this package.**

An easy to use data mapper ORM for Laravel 5 that fits perfectly to the approach of Domain Driven Design (DDD). In general the Laravel Data Mapper is an extension to the Laravel Query Builder. You can build queries by using all of the query builder methods and in addition you can pass plain old PHP objects (popo's) to the builder and also return popo's from the builder.

## Installation

Laravel Data Mapper is distributed as a composer package. So you first have to add the package to your `composer.json` file:

```
"markusjwetzel/laravel-datamapper": "~1.0@dev"
```

Then you have to run `composer update` to install the package. Once this is completed, you have to add the service provider to the providers array in `config/app.php`:

```
'Wetzel\Datamapper\LaravelDatamapperServiceProvider'
```

If you want to use a facade for the entity manager, you can create an alias in the aliases array of `config/app.php`:

```
'Entity' => 'Wetzel\Datamapper\Facades\Entity'
```

Run php artisan vendor:publish to publish this package configuration. Afterwards you can edit the file `config/datamapper.php`.

## Usage

### Annotations

We will map all classes to a database table by using annotations. Annotations are doc-comments that you add to a class. The annotations are quite similar to the Doctrine 2 annotations. Here is a simple example of a `User` class:

```php
<?php

use Wetzel\Datamapper\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    ...
}
```

For a full documentation on all annotations see the wiki.

### Migrations

Once you have defined the annotations, you can run `php artisan datamapper:generate`. This command will walk through all registered classes and will generate migration files in the default migrations directory (e. g. `database/migrations`) based on the defined annotations (Important: old migration files will be overwritten).

You can combine this command with all the migrate commands by adding the `--migrate` flag (e. g. `--migrate:refresh`). If you set the `--migrate` flag, you can also set the `--seed` flag to migrate and seed the database.

Furthermore you can use a `--timestamps:false` flag if you do not want a timestamp prefix in the migration files.

### Entity Manager

As already mentioned the Laravel Data Mapper is an extension of the Laravel Query Builder, so you can use all methods of the query builder. You can get an instance of the entity manager by using the `Entity` facade or by using method injection:

```php
<?php

use Wetzel\Datamapper\EntityManager;

class UserRepository {

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    ...
    
}
```

In addition to define a table for the query with `$em->table('users')`, you can pass an entity class or an entity object to select a table (e. g. `$em->class('Entity\User')` or `$em->object($user)`).

#### Example #1: Get one or many User objects

`$user = $em->class('Entity\User')->where('id',$id)->get();` (returns a User object)

`$users = $em->class('Entity\User')->all();` (returns an ArrayCollection of User objects)

#### Example #2: Insert, update and delete a record

`$em->insert($user);`

`$em->update($user);`

`$em->delete($user);`

Hint: Relational objects are not inserted or updated.

#### Example #3: Eager Loading

`$users = $em->class('Entity\User')->with('User.comments')->get();`

You can use the `with()` method the same way as you use it with Eloquent objects. Chained dot notations can be used (e. g. `->with('User.comments.likes')`

#### Example #4: Versioning Plugin

If an entity has the `@ORM\Versionable` annotation, you can use the versioning methods:

`$users = $em->class('Entity\User')->where('id',$id)->allVersions();`

`$user = $em->class('Entity\User')->where('id',$id)->getVersion(1);`

Hint: `get()` returns always the latest version.

#### Example #5: SoftDeleting Plugin

If an entity has the `@ORM\Softdeleteable` annotation, you can use the soft deleting methods from Eloquent, e. g.:

`$users = $em->class('Entity\User')->withTrashed()->all();`

## Support

Bugs and feature requests are tracked on [GitHub](https://github.com/markusjwetzel/laravel-datamapper/issues).

## License

This package is released under the [MIT License](LICENSE).
