<?php

namespace App\Models;

use SwiftPHP\Core\Model;

class Task extends Model
{
    protected string $table = 'tasks';

    protected array $fillable = [
        'title',
        'completed'
    ];
}
