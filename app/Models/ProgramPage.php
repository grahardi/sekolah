<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramPage extends Model
{
    protected $table = 'program_pages';
    protected $fillable = ['slug', 'title', 'status', 'summary', 'detail', 'href', 'cta', 'demo_href'];
}
