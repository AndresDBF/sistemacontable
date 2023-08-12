<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Pagination\Paginator;
use Spatie\Permission\Models\Role;
class UserController extends Controller
{
    public function __construct()
    {
        //utilizado para restriccion de rutas
        $this->middleware('can:users.index')->only('index'); //solo se aplicara al metodo index
        $this->middleware('can:users.edit')->only('edit','update');

    }
    public function index()
    {
        $users = User::paginate(5);
        return view('admin/users.index',compact('users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $password = $user->password;
        return view('admin/users.edit',compact('user','roles','password'));
    }


    public function update(Request $request, $id)
    {

        $user = User::where('id',intval($id))->first();
        $user->roles()->sync($request->roles);

        return redirect()->route('users.edit',$id)->with('info','se asignó el rol correctamente');
    }

}
