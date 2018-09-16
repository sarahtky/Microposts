@if (Auth::user()->is_favorites($micropost->id))
        {!! Form::open(['route' => ['micropost.unfavorites', $micropost->id], 'method' => 'delete']) !!}
            {!! Form::submit('Unfavorite', ['class' => "btn btn-success btn-xs"]) !!}
        {!! Form::close() !!}
@else
        {!! Form::open(['route' => ['micropost.favorite', $micropost->id]]) !!}
            {!! Form::submit('Favorites', ['class' => "btn btn-default btn-xs"]) !!}
        {!! Form::close() !!}
@endif