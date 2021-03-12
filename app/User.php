<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    
    // このユーザが所有する投稿。（ Micropostモデルとの関係を定義）
    public function microposts()
    {
        return $this->hasMany(Micropost::class);
    }
    
    // このユーザがフォロー中のユーザ。（ Userモデルとの関係を定義）
    public function followings()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }
    
    // このユーザをフォロー中のユーザ。（ Userモデルとの関係を定義）
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }
    
    // $userIdで指定されたユーザをフォローする。
    public function follow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if($exist || $its_me) {
            // すでにフォローしていれば何もしない
            return false;
        } else {
            // 未フォローであればフォローする
            $this->followings()->attach($userId);
            return true;
        }
    }
    
    public function unfollow($userId)
    {
        // すでにフォローしているかの確認
        $exist = $this->is_following($userId);
        // 対象が自分自身かどうかの確認
        $its_me = $this->id == $userId;
        
        if($exist && !$its_me) {
            // すでにフォローしていればフォローを外す
            $this->followings()->detach($userId);
            return true;
        }else{
            // 未フォローであれば何もしない
            return false;
        }
    }
    
    // 指定された $userIdのユーザをこのユーザがフォロー中であるか調べる。フォロー中ならtrueを返す。
    
    public function is_following($userId)
    {
        // フォロー中ユーザの中に $userIdのものが存在するか
        return $this->followings()->where('follow_id', $userId)->exists();
    }
    
    // このユーザとフォロー中ユーザの投稿に絞り込む。
    public function feed_microposts()
    {
        // このユーザがフォロー中のユーザのidを取得して配列にする
        $userIds = $this->followings()->pluck('users.id')->toArray();
        // このユーザのidもその配列に追加
        $userIds [] =$this->id;
        // それらのユーザが所有する投稿に絞り込む
        return Micropost::whereIn('user_id', $userIds);
    }
    
    // このユーザに関係するモデルの件数＋お気に入りの数をロードする。
    public function loadRelationshipCounts()
    {
        $this->loadCount(['microposts', 'followings', 'followers', 'favorites']);
    }
    
    
    // お気に入り一覧を取得
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'micropost_id')->withTimestamps();
    }
    
    // 指定された $micropostIdをお気に入りに追加していか調べる。追加しているならtrueを返す。
    public function is_favorite($micropostId)
    {
        // お気に入りに追加した中に $userIdのものが存在するか
        return $this->favorites()->where('user_id', $micropostId)->exists();
    }
    
    // $micropostIdで指定された投稿を追加する。
    
    public function favorite($micropostId)
    {
        // すでにお気に入りにしているかの確認
        $exist = $this->is_favorite($micropostId);
        // 対象が自分自身の投稿かどうかの確認
        $its_me = $this->id == $micropostId;
        
        if($exist || $its_me) {
            // すでに追加していれば何もしない
            return false;
        } else {
            // 未追加であればお気に入りに追加する
            $this->favorites()->attach($micropostId);
            return true;
        }
    }
    
    public function unfavorite($micropostId)
    {
        // すでにお気に入りにしているかの確認
        $exist = $this->is_favorite($micropostId);
        // 対象が自分自身の投稿かどうかの確認
        $its_me_post = $this->id == $micropostId;
        
        if($exist && !$its_me) {
            // すでに追加していればお気に入りを外す
            $this->favorites()->detach($micropostId);
            return true;
        }else{
            // 未追加であれば何もしない
            return false;
        }
    }
}
