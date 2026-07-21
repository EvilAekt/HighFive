<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'is_admin',
        'content',
        'is_read',
        'reply_to_id',
        'product_id',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
