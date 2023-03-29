# Redis Model

The Redis Model will help create multiple keys with the same prefix in Redis and group those keys together as a table in a SQL database. The Redis Model will create an instance similar to the Eloquent Model in Laravel. It will also provide complete methods for adding, deleting, updating, and retrieving data arrays with methods that are similar to those used in Eloquent.

> No Relationship :
Redis is not the place to store complex relational data and Redis emphasizes its ability to access data very quickly, so building Relationship methods between models is not necessary and it will take a lot of time to retrieve data.

## Supports

### Laravel version supports

| Laravel | Is Support |
| :---: | :---: |
| < 8 | No |
| 8 | Yes |
| 9 | Yes |
| 10 | Yes |

### Model Supports

| Function | Is Working |
| --- | :---: |
| CURD | Yes |
| Condition Select | Yes |
| Chunking | Yes |
| Transaction | Yes |
| Insert a lot of data | Yes |
| Delete a lot of data | Yes |
| Update a lot of data | comming soon |
| Relationship | No |

### Redis Key Concept
- An sample key:
laravel_redis_model_users:email:email@example:name:alvin:role:admin

Laravel's prefix: laravel_redis_model
The model name: users
The primary key of model: email
The sub-key of model: name, role


## Installation

You may install Redis Model via the Composer package manager:
```
 composer require alvin0/redis-model
```

You should publish the RedisModel configuration and migration files using the `vendor:publish` Artisan command. The `redis-model` configuration file will be placed in your application's `config` directory:

```
php artisan vendor:publish --provider="Alvin0\RedisModel\RedisModelServiceProvider"
```
## Generate Model
```
php artisan redis-model:model User
```
## Model Conventions
### Primary Keys, Sub-Keys And Fillable

Primary Keys are properties that are not assigned by default, and they determine the search behavior of the model. Make sure that the values of primary keys are unique to avoid confusion when retrieving data based on conditions.
Sub keys are keys that can be duplicated, and they will help you search using the where method in the model.
To declare attributes for a model, you need to declare them in the $fillable variable. You should also declare the primary key and subkeys in this variable.

```php
use Alvin0\RedisModel\Model;

class User extends Model {
    /**
     * The primary key for the model.
     *
     * @var bool
     */
    protected $primaryKey = 'email';

    /**
     * The model's sub keys for the model.
     *
     * @var array
     */
    protected $subKeys = [
        'name',
        'role',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
            'email',
            'name',
            'role',
            'address'
    ];
}
```
### Table Names
So, in this case, RedisModel will assume the `User` model stores records in the `users` table.

```php
use Alvin0\RedisModel\Model;

class User extends Model {
    // ...
}
```

The final name before creating the hash code is based on the `prefix of the table + table name`.
If your model's corresponding database table does not fit this convention, you may manually specify the model's table name by defining a table property on the model:
```php
use Alvin0\RedisModel\Model;

class User extends Model {
    /**
     * The model's table.
     *
     * @var array
     */
    protected $table = "";

    /**
     * The model's prefixTable.
     *
     * @var array
     */
    protected $prefixTable = null;
}
```
### Timestamps
By default, RedisModel expects `created_at` and `updated_at` columns to exist on your model's corresponding database table. RedisModel will automatically set these column's values when models are created or updated. If you do not want these columns to be automatically managed by RedisModel, you should define a `$timestamps` property on your model with a value of `false`:

```php
use Alvin0\RedisModel\Model;

class User extends Model {
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
```
### Configuring Connection Model
You can change the connection name for the model's connection. Make sure it is declared in the `redis-model` configuration file. By default, the model will use the `redis_model_default` connection name.
```php
use Alvin0\RedisModel\Model;

class User extends Model {

    /**
     * @var string|null
     */
    protected $connectionName = null;
}
```

## Retrieving Models

### Building
Due to limitations in searching model properties, where is the only supported method. The where method will facilitate the search for primary key and sub-key in the model's table easily. You can add additional constraints to the query and then call the `get` method to retrieve the results:
The where method can only search for fields that are `primary key` and `sub keys`.

```php
use App\RedisModels\User;

User::where('email', 'email@gmail.com')
    ->where('role', 'admin')
    ->get();
```
> Tip: where("field", "something_*")
> You can use * to match any keywords following the required keywords. Looks like the same place as in SQL

```php
use App\RedisModels\User;

User::where('name', "user_*")->get();
// result collection 
// [
//  ["name" => "user_1"],
//  ["name" => "user_2"],
//  ["name" => "user_3"],
//  ["name" => "user_4"],
// ]
```
### Collection
As we have seen, Eloquent methods like `all` and `get` retrieve multiple records from the redis. However, these methods don't return a plain PHP array. Instead, an instance of `Alvin0\RedisModel\Collection` is returned.
- Method all()
```php
use App\RedisModels\User;

User::all();
```
- Method get()
```php
use App\RedisModels\User;

User::where('name', "user_*")->get();
```

## Chunking Results
Your application may run out of memory if you attempt to load tens of thousands of Eloquent records via the `all` or `get` methods. Instead of using these methods, the chunk method may be used to process large numbers of models more efficiently.

- Method chunk
```php
use App\RedisModels\User;
use Alvin0\RedisModel\Collection;

User::where('user_id', 1)
    ->chunk(10, function (Collection $items) {
        foreach ($items as $item) {
            dump($item);
        }
    });
```
### Retrieving Single Models
In addition to retrieving all of the records matching a given query, you may also retrieve single records using the `find`, `first` methods.Instead of returning a collection of models, these methods return a single model instance:

```php
use App\RedisModels\User;
 
// Retrieve a model by its primary key...
$user = User::find('value_primary_key');
 
// Retrieve the first model matching the query constraints...
$user = User::where('email', 'email@gmail.com')->first();

```
## Inserting & Updating Models
### Inserts
We also need to insert new records. Thankfully, Eloquent makes it simple. To insert a new record into the database, you should instantiate a new model instance and set attributes on the model. Then, call the save method on the model instance:
```php
use App\RedisModels\User;

$user = new User;
$user->email = 'email@gmail.com';
$user->name = 'Alvin0';
$user->token = '8f8e847890354d23b9a762f4d2612ce5';
$user->token = now();
$user->save()
```
### Create Model
Alternatively, you may use the create method to `save` a new model using a single PHP statement. The inserted model instance will be returned to you by the create method:
```php
use App\RedisModels\User;

$user = User::create([
    'email' => 'email@gmail.com',
    'name' => 'Alvin0'
    'token' => '8f8e847890354d23b9a762f4d2612ce5',
    'expire_at' => now(),
])
$user->email //email@gmail.com 
```

### Force Create Model
By default, the create method will automatically throw an error if the primary key is duplicated (`Alvin0\RedisModel\Exceptions\KeyExistException`). If you want to ignore this error, you can try the following approaches:
- Change property `preventCreateForce` to `false`
```php
use Alvin0\RedisModel\Model;

class User extends Model {
    /**
     * Indicates when generating but key exists
     *
     * @var bool
     */
    protected $preventCreateForce = true;
}
```

- Use method forceCreate
```php
use App\RedisModels\User;

User::forceCreate([
    'email' => 'email@gmail.com',
    'name' => 'Alvin0'
    'token' => '8f8e847890354d23b9a762f4d2612ce5',
    'expire_at' => now(),
]);

$user->email //email@gmail.com 

```

### Insert Statements
To solve the issue of inserting multiple items into a table, you can use the inserts function. It will perform the insert within a transaction to ensure a rollback in case of errors that you may not be aware of. It is recommended to use array chunk to ensure performance.

```php
use App\RedisModels\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$seed = function ($limit) {
    $users = [];
    for ($i = 0; $i < $limit; $i++) {
        $users[] = [
            'email' => Str::random(10) . '@gmail.com',
            'name' => Str::random(8),
            'token' => md5(Str::random(10)),
            'expire_at' => now(),
        ];
    }

    return $users;
};

User::insert($seed(10));
```

### Update Model
The `save` method may also be used to update models that already exist in the database. To update a model, you should retrieve it and set any attributes you wish to update. Then, you should call the model's `save` method. Again, the `updated_at` timestamp will automatically be updated, so there is no need to manually set its value:

```php
use App\RedisModels\User;
 
$user = User::find('email@gmail.com');
 
$user->name = 'Alvin1';

$user->save();
```

Method update is not supported for making changes on a collection. Please use it with an existing instance instead:

```php
$user = User::find('email@gmail.com')->update(['name' => 'Alvin1']);
```

### Deleting Models
To delete a model, you may call the delete method on the model instance:

```php
use App\RedisModels\User;
 
$user = User::find('email@gmail.com')->delete();
```

### Delete Statements
The query builder's delete method may be used to delete records from the redis model.
```php
    User::where('email', '*@gmail.com')->destroy();

    //or remove all data model
    User::destroy();
```

## Expire
The special thing when working with `Redis` is that you can set the expiration time of a key, and with a model instance, there will be a method to `set` and `get` the expiration time for it.
### Set Expire Model

```php
use App\RedisModels\User;

$user = User::find('email@gmail.com')->setExpire(60); // The instance will have a lifespan of 60 seconds
```

### Get Expire Model

```php
use App\RedisModels\User;

$user = User::find('email@gmail.com')->getExpire(); // The remaining time to live of the instance is 39 seconds.
```
