<?php

use Alvin0\RedisModel\Tests\Models\User;

it('a user can be created, updated, and deleted', function ($userInput, $expect) {
    $user = User::create($userInput);

    expect($user->id)->toEqual($userInput['id']);
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
        ['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
        ['name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com'],
    ],
    [
        ['id' => 2, 'name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
        ['name' => 'Luke Downing', 'email' => 'luke_downing@example.com'],
    ],
    [
        ['id' => 3, 'name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
        ['name' => 'Freek Van Der Herten', 'email' => 'freek_van_der@example.com'],
    ],
]);

it('can retrieve all users', function ($setup, $clean) {
    $setup();

    $users = User::all();
    expect($users->count())->toBeGreaterThan(0);

    $clean();
})->with([
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com']),
        fn() => User::find(1)->delete(),
    ],
]);

it('can retrieve a single user by ID', function ($setup, $clean) {
    $setup();

    $user = User::find(1);
    expect($user->name)->toEqual('Nuno Maduro');
    expect($user->email)->toEqual('nuno_naduro@example.com');

    $clean();
})->with([
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com']),
        fn() => User::find(1)->delete(),
    ],
]);

it('can retrieve users matching a given criteria', function ($setup, $clean) {
    $setup();

    $users = User::where('name', 'Nuno*')->get();
    expect($users->count())->toBeGreaterThan(0);

    foreach ($users as $user) {
        expect($user->name)->toContain('Nuno Maduro');
        expect($user->email)->toContain('nuno_naduro@example.com');
    }

    $clean();
})->with([
    [
        fn() => User::create(['id' => 1, 'name' => 'Nuno Maduro', 'email' => 'nuno_naduro@example.com']),
        fn() => User::find(1)->delete(),
    ],
]);

it('can insert multiple users and remove all', function ($data) {
    User::insert($data);

    $users = User::get();
    expect($users->count())->toBe(10);

    foreach ($data as $userInput) {
        $user = User::where('email', $userInput['email'])->first();
        expect($user->id)->toBeString();
        expect($user->name)->toEqual($userInput['name']);
        expect($user->email)->toEqual($userInput['email']);
    }

    User::where('email', 'user1@example.com')->destroy();

    expect(User::count())->toBe(9);

    User::destroy();

    $users = User::get();
    expect($users->count())->toBe(0);
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
]
);
