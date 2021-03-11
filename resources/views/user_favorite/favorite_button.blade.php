@if (Auth::id() != $user->id)
    @if (Auth::user()->is_favorite($user->id))
        {{-- お気に入り削除ボタンのフォーム --}}
        {!! Form::open(['route' => ['favorites.unfavorite', $user->id], 'method' => 'delete']) !!}
            {!! Form::submit('Unfavorite', ['class' => "btn btn-danger btn-block"]) !!}
        {!! Form::close() !!}
    @else
        {{-- お気に入り追加ボタンのフォーム --}}
        {!! Form::open(['route' => ['favorites.favorite', $user->id]]) !!}
            {!! Form::submit('Favorite', ['class' => "btn btn-primary btn-block"]) !!}
        {!! Form::close() !!}
    @endif
@endif