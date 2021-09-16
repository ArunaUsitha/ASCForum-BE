<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;


class Post extends Model
{
    const POST_STATUS_APPROVED = '1';
    const POST_STATUS_PENDING = '0';
    const POST_STATUS_REJECTED = '2';

    protected $fillable = [
        'user_id',
        'title',
        'text',
        'status'
    ];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * create a post
     * @param $request
     * @return mixed
     */
    public static function store($request)
    {
        try {

            $user = \Auth::user();

            return Post::create([
                'user_id' => \Auth::user()->id,
                'title' => $request->title,
                'text' => $request->text,
                'status' => $user->hasRole(Config::get('app.access.role.admin')) ? '1' : '0'
            ]);

        } catch (\Exception $exception) {
            logThis($exception);
        }
    }

    /**
     * get posts by users role
     * @param User $user
     * @param $search
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getPostsByRole(User $user, $search)
    {
        try {

            $postsQuery = Post::with('comments')->whereHas('user', function ($query) use ($search) {
                if ($search !== '') {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orwhere('title', 'like', '%' . $search . '%')
                        ->orwhere('text', 'like', '%' . $search . '%');
                }
            })->with('user')->where('status', '1');

            return $postsQuery->get();

        } catch (\Exception $exception) {
            logThis($exception);
        }

    }

    /**
     * get all pending posts.
     * @param User $user
     * @return mixed
     */
    public static function getPendingPosts(User $user)
    {
        try {

            $postsQuery = Post::orderBy('created_at', 'DESC')->with('comments')->where('status', '0');

            if (!$user->hasRole(Config::get('app.access.role.admin'))) {
                $postsQuery->where('user_id', $user->id);
            }

            return $postsQuery->get();

        } catch (\Exception $exception) {
            logThis($exception);
        }

    }

    /**
     * approve posts
     * @param $postId
     * @return mixed
     */
    public static function approve($postId)
    {
        try {

            $post = Post::find($postId);
            $post->status = self::POST_STATUS_APPROVED;
            return $post->save();

        } catch (\Exception $exception) {
            logThis($exception);
        }
    }

    /**
     * reject a post
     * @param $postId
     */
    public static function reject($postId)
    {
        try {

            $post = Post::find($postId);
            $post->status = self::POST_STATUS_REJECTED;
            $post->save();

        } catch (\Exception $exception) {
            logThis($exception);
        }
    }
}
