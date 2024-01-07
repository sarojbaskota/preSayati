<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    
    protected $fillable = ['content','from','to','seen_at','edited_at'];
    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function sender()
    {
         return $this->belongsTo(User::class, 'from');
    }

    public function receiver()
    {
          return $this->belongsTo(User::class, 'to');
    }
}
