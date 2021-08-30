<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'text',
        'post_id',
        'user_id'
    ];

    use HasFactory;

    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function store($request)
    {
        try {
            return Comment::create([
                'text' => $request->comment,
                'post_id' => $request->postId,
                'user_id' => \Auth::user()->id
            ]);
        }catch (\Exception $exception){
            logThis($exception);
        }
    }
}
