<?php
namespace Alvin0\RedisModel\Tests\Models;

use Alvin0\RedisModel\Model;

class User extends Model
{
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The model's sub keys for the model.
     *
     * @var array
     */
    protected $subKeys = ['email'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'email',
        'name',
    ];
}
