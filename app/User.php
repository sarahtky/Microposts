<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    /**
     * follow model
     *
     * belongsToMany() 
     * 第一引数=>Model クラス (User::class) 第二引数=>中間テーブル (user_follow) 
     * 第三引数=>自分の id を示すカラム名 (user_id)  第四引数=>関係先の id を示すカラム名 (follow_id) 
     */
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    
    public function follow($userId)
    {
    // 既にフォローしているかの確認
    $exist = $this->is_following($userId);
    // 自分自身ではないかの確認
    $its_me = $this->id == $userId;

    if ($exist || $its_me) {
        // 既にフォローしていれば何もしない
        return false;
    } else {
        // 未フォローであればフォローする
        $this->followings()->attach($userId);
        return true;
    }
    }
    
    public function unfollow($userId)
    {
    // 既にフォローしているかの確認
    $exist = $this->is_following($userId);
    // 自分自身ではないかの確認
    $its_me = $this->id == $userId;

    if ($exist && !$its_me) {
        // 既にフォローしていればフォローを外す
        $this->followings()->detach($userId);
        return true;
    } else {
        // 未フォローであれば何もしない
        return false;
    }
    }

    public function is_following($userId) {
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    public function feed_microposts()
    {
        $follow_user_ids = $this->followings()-> pluck('users.id')->toArray();
        $follow_user_ids[] = $this->id;
        return Micropost::whereIn('user_id', $follow_user_ids);
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Micropost::class, 'favorite_post', 'user_id', 'favorite_id')->withTimestamps();
    }
    
    public function favorite($favorite_id)
    {
        // 既にお気に入りに追加しているかの確認
        $exist = $this->is_favorites($favorite_id);

        if ($exist) {
            // 既にお気に入りに追加していれば何もしない
            return false;
        } else {
            // 未追加であれば追加する
            $this->favorites()->attach($favorite_id);
            return true;
        }
    }
    public function unfavorite($favorite_id)
    {
        // 既にお気に入りに追加しているかの確認
        $exist = $this->is_favorites($favorite_id);

        if ($exist) {
            // 既に追加していれば外す
            $this->favorites()->detach($favorite_id);
            return true;
        } else {
            // 未追加であれば何もしない
            return false;
    }
    }

    public function is_favorites($favorite_id) {
        return $this->favorites()->where('favorite_id', $favorite_id)->exists();
    }
}
