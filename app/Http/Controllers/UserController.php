<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()

    {
        $users = Cache::remember('users', now()->addMinutes(10), function () {
            return User::query()->latest()->paginate(10);
        });

        return view('user.index', compact('users'));

        // $users = User::query()->latest()->paginate(10);

        // return view('user.index',compact('users'));
    }

    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {   
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'num' => '|numeric|min:2.50|max:99.99' ,
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $fileName = time() . '.' . $request->image->extension();
        $request->image->storeAs('public/images', $fileName);
        
        $user = new User;
        $user->name = $request->input('name');
        $user->email = trim($request->input('email'));
        $user->password = bcrypt($request->input('password'));
        $user->num = ($request->input('num'));
        $user->image = $fileName;
        $user->save();

        return redirect()->route('user.index')->with([
            'message' => 'User added successfully!', 
            'status' => 'success'
        ]);
    }
}