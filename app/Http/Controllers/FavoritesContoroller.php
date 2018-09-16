<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Micropost;
use App\User;

class FavoritesController extends Controller
{
    public function store(Request $request, $id)
    {
        \Auth::user()->favorites($id);
        return redirect()->back();
    }

    public function destroy($id)
    {
        \Auth::user()->unfavorites($id);
        return redirect()->back();
    }
    
    
    
}
