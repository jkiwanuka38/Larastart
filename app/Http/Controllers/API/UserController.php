<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'name'=>'required|string|max:191',
          'email'=>'required|string|email|max:255|unique:users',
          'password'=>'required|string|min:6'
        ]);

        return User::create([
           'name' => $request['name'],
           'email' => $request['email'],
           'type' => $request['type'],
           'bio' => $request['bio'],
           'photo' => $request['photo'],
           'password' => Hash::make($request['password']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $user = User::findorFail($id);

      $this->validate($request, [
        'name'=>'required|string|max:191',
        'email'=>'required|string|email|max:255|unique:users,email,'.$user->id,
        'password'=>'sometimes|min:6'
      ]);

      $user->update($request->all());

      return ['message' => 'User Updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findorFail($id);

        //delete the users
        $user->delete();

        return ['message' => 'User Deleted'];
    }
}
