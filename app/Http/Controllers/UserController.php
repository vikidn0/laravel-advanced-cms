<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('id','desc')->paginate(10);
       return view('users.index')->withUsers($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
     return view('users.create');
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
            'name' =>'required|max:255',
            'email'=>'required|email|unique:users'
            ]);
        if (Request::has('password') && !empty($request->password)) {
            $password = trim($request->password);
        }
         else{
            $length = 10;
            $keyspace='123456789abcdefghijkmpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
            $str= '';
            $max = mb_strlen($keyspace,'8bit')-1;
            for($i =0; $i< $length; ++$i){
                $str .= $keyspace[random_int(0,$max)];
            }
            $password = $str;
         }
        $user = new User();
        $user->name = $request->name;
        $user->name = $request->email;
        $user->password = Hash::make($password);
        $user->save();

        if($user->save()){
            return redirect()->route('users.show',$user->id);
        }
         else{
            Session::flash('danger','sorry a problem occured while creating this user.');
            return redirect()->route('users.create');
         }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $user = User::findOrFail($id);
      return view("users.show")->withUser($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       $user =  User::findOrFail($id);
       return view("users.edit")->withUser($user);
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
    $this->validate($request,[
    'name' =>'required|max:255',
    'email'=> 'required|email|unique:users,email,'.$id ]);
    $user = User::findOrFail($id);
    $user->name = $request->name;
    $user->email = $request->email;
     if($request->password_options =='auto'){
        $length = 10;
        $keyspace='123456789abcdefghijkmpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
        $str= '';
        $max = mb_strlen($keyspace,'8bit')-1;
        for($i =0; $i< $length; ++$i){
            $str .= $keyspace[random_int(0,$max)];
        }
        $password = $str;
     }
      elseif ($request->password_options =='manual') {
          $user->password =  Hash::make($request->password);
      }
      if($user->save()){
        return redirect()->route('users.show',$id);
      }
       else{
        Session::flash('error','There was a problem saving the updated user info to the database');
        return redirect()->route('users.edit',$id);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
