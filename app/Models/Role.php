<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasOne;


class Role extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mysql_only_users';

    public $timestamps = false;

    protected $fillable = [
            'end_date',
            'start_date',
            'is_active',
            'employee_id',
            'position_id'
    ];

     /**
     * Get the positions for the blog post.
     */
    public function position(): HasOne
    {
        return $this->hasOne(Position::class, 'position_id');
    }
}
