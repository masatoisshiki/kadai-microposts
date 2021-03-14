<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function store($micropostId)
    {
        // 認証済みユーザ（閲覧者）が、 投稿をお気に入りに追加する
        \Auth::user()->favorite($micropostId);
        // 前のURLへリダイレクトさせる
        return back();
    }
    
    public function destroy($micropostId)
    {
        // 認証済みユーザ（閲覧者）が、 お気に入りに追加した投稿を削除する
        \Auth::user()->unfavorite($micropostId);
        // 前のURLへリダイレクトさせる
        return back();
    }
}
