<?php

use Alvin0\RedisModel\Tests\Models\User;
use Illuminate\Support\Facades\Redis;

beforeEach(function () {
    Redis::flushall();
});

it('a user can be created without id', function ($userInput, $expect) {
    $user = User::create($userInput);
    expect($user->name)->toEqual($expect['name']);
    expect($user->email)->toEqual($expect['email']);
})->with([
    [
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ],
    [
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
    ],
    [
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
    ],
]);

it('can insert multiple users without id', function ($data) {

    User::insert($data);

    $users = User::get();
    expect($users->count())->toBe(10);
})->with([
    function () {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
            ];
        }

        return $data;
    }
]);

it('a user can be force created', function ($userInput, $expect) {
    $user = User::create($userInput);

    expect($user->name)->toEqual($expect['name']);
    expect($user->email)->toEqual($expect['email']);

    expect(User::count())->toBe(1);

    $userForceCreate = User::forceCreate([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
    ]);

    expect($userForceCreate->id)->toEqual($user['id']);
    expect($userForceCreate->name)->toEqual($user['name']);
    expect($userForceCreate->email)->toEqual($user['email']);

    expect(User::count())->toBe(1);
})->with([
    [
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ],
    [
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
    ],
    [
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
    ],
]);

it('a user can be created, updated, and deleted', function ($userInput, $expect) {
    $user = User::create($userInput);

    expect($user->name)->toEqual($expect['name']);
    expect($user->email)->toEqual($expect['email']);

    // Update the user's name and email
    $user->name = 'New Name';
    $user->email = 'new_email@example.com';
    $user->save();

    // Reload the user from the database and assert that the name and email were updated
    $updatedUser = User::find($user->id);
    expect($updatedUser->name)->toEqual('New Name');
    expect($updatedUser->email)->toEqual('new_email@example.com');

    // Delete the user from the database
    $updatedUser->delete();

    // Assert that the user was deleted by checking that it can no longer be found in the database
    $deletedUser = User::find($user->id);
    expect($deletedUser)->toBeNull();
})->with([
    [
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ],
    [
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
    ],
    [
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
    ],
]);

it('can retrieve all users', function () {
    expect(User::all()->count())->toBeGreaterThan(0);
})->with([
    [
        function () {
            $data = [];

            for ($i = 1; $i <= 10; $i++) {
                $data[] = [
                    'name' => 'User ' . $i,
                    'email' => 'user' . $i . '@example.com',
                ];
            }

            return User::insert($data);
        }
    ],
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com']),
    ],
]);

it('can retrieve a single user by ID', function ($expect) {
    $user = User::find(1);

    expect($user->name)->toEqual($expect['name']);
    expect($user->email)->toEqual($expect['email']);
})->with([
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com']),
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ],
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Madurop', 'email' => 'nuno_nadurop@example.com']),
        ['name' => 'Nuno Madurop', 'email' => 'nuno_nadurop@example.com'],
    ],
]);

it('can retrieve users matching a given criteria', function ($expect) {
    $users = User::where('name', 'Nuno*')->get();
    expect($users->count())->toBeGreaterThan(0);

    foreach ($users as $user) {
        expect($user->name)->toContain($expect['name']);
        expect($user->email)->toContain($expect['email']);
    }
})->with([
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com']),
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ],
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Madurop', 'email' => 'nuno_nadurop@example.com']),
        ['name' => 'Nuno Madurop', 'email' => 'nuno_nadurop@example.com'],
    ],
]);

it('can insert multiple users', function ($data) {
    User::insert($data);

    $users = User::get();
    expect($users->count())->toBe(10);

    foreach ($data as $userInput) {
        $user = User::where('email', $userInput['email'])->first();
        expect($user->id)->toBeString();
        expect($user->name)->toEqual($userInput['name']);
        expect($user->email)->toEqual($userInput['email']);
    }
})->with([
    function () {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'id' => $i,
            ];
        }

        return $data;
    }
]);

it('can remove multiple users', function ($data) {
    User::insert($data);

    $users = User::get();
    expect($users->count())->toBe(10);

    User::where('email', 'user' . rand(1, 3) . '@example.com')->destroy();
    User::where('email', 'user' . rand(4, 6) . '@example.com')->destroy();
    User::where('email', 'user' . rand(7, 10) . '@example.com')->destroy();

    expect(User::count())->toBe(7);

    User::destroy();

    expect(User::get()->count())->toBe(0);
})->with([
    function () {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'id' => $i,
            ];
        }

        return $data;
    }
]);

it('it can insert multiple users with transaction', function ($data) {
    User::transaction(function ($conTransaction) use ($data) {
        User::insert($data, $conTransaction);
    });

    expect(User::get()->count())->toBe(10);
})->with([
    function () {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'id' => $i,
            ];
        }

        return $data;
    }
]);

it('it cant insert multiple users with transaction', function ($data) {
    User::transaction(function ($conTransaction) use ($data) {
        User::insert($data, $conTransaction);

        throw new \Exception('Something went wrong');
    });

    expect(User::get()->count())->toBe(0);
})->with([
    function () {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'id' => $i,
            ];
        }

        return $data;
    }
]);

it('can retrieve users by email', function () {
    $users = User::query()->where('email', 'nuno_naduro@example.com')->get();

    expect(2)->toEqual($users->count());
})->with([
    [
        fn() => User::insert([
            ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
            ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
            ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.net'],
        ]),
    ],
    [
        fn() => User::insert([
            ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.net'],
            ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
            ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
        ]),
    ]
]);

it('can create user assigning model property values', function ($userData, $expected) {
    $user = User::query()->where('email', 'nuno_naduro@example.com')->first();

    expect($expected['name'])->toEqual($user->name);
    expect($expected['email'])->toEqual($user->email);
})->with([
    [
        function () {
            $user = new User;
            $user->name = 'Nuno Maduro';
            $user->email = 'nuno_naduro@example.com';
            $user->save();
        },
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ]
]);

it('can update user subKey without duplication', function () {
    expect(1)->toEqual(User::query()->count());
})->with([
    [
        function () {
            $user = new User;
            $user->name = 'Nuno Maduro';
            $user->email = 'nuno_naduro@example.com';
            $user->save();
            $user->email = 'nuno_naduro@example.net';
            $user->save();
        },
    ],
    [
        function () {
            $user = new User;
            $user->name = 'Nuno Maduro';
            $user->email = 'nuno_naduro@example.com';
            $user->save();
            $user->name = 'Nuno';
            $user->email = 'nuno_naduro@example.net';
            $user->save();
        },
    ]
]);

it('can update user primaryKey without duplication', function () {
    expect(1)->toEqual(User::query()->count());
})->with([
    [
        function () {
            $user = new User;
            $user->id = '1';
            $user->name = 'Nuno Maduro';
            $user->email = 'nuno_naduro@example.com';
            $user->save();
            $user->id = 2;
            $user->save();
        },
    ],
    [
        function () {
            $user = new User;
            $user->id = 1;
            $user->name = 'Nuno Maduro';
            $user->email = 'nuno_naduro@example.com';
            $user->save();
            $user->id = 2;
            $user->save();
        },
    ]
]);