<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if($request)
        {
            $sql = trim($request->get('buscarTexto'));
            $usuarios = DB::table('users')
            ->join('roles', 'users.idrol', '=', 'roles.id')
            ->select('users.id', 'users.nombre', 'users.tipo_documento', 'users.num_documento', 'users.direccion', 'users.telefono', 'users.email', 'users.usuario', 'users.password', 'users.condicion', 'users.idrol', 'users.imagen', 'roles.nombre as rol')
            ->where('users.nombre', 'LIKE', '%'.$sql.'%')
            ->orderBy('users.id', 'desc')
            ->paginate(10);

            $roles = DB::table('roles')
            ->select('id', 'nombre', 'descripcion')
            ->where('condicion', '=', 'true')->get();
            return view('user.index', ["usuarios"=>$usuarios, "roles"=>$roles, "buscarTexto"=>$sql]);
            // return $usuarios;
        }
    }

    public function store(Request $request)
    {
        $user = new User(); 
        $user->nombre = $request->nombre;
        $user->tipo_documento = $request->tipo_documento;
        $user->num_documento = $request->num_documento;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->direccion = $request->direccion;
        $user->usuario = $request->usuario;
        $user->password = bcrypt($request->password);
        $user->condicion = true;
        $user->idrol = $request->id_rol;

        if($request->hasFile('imagen'))
        {
            $filenamewithExt = $request->file('imagen')->getClientOriginalName();
            $filename = pathinfo($filenamewithExt, PATHINFO_FILENAME);
            $extension = $request->file('imagen')->guessClientExtension();
            $fileNameToStore = time().'.'.$extension;
            $path = $request->file('imagen')->storeAs('public/img/usuario',$fileNameToStore);
        }else{
            $fileNameToStore = "noimagen.png";
        }
        $user->imagen=$fileNameToStore;
        $user->save();
        return Redirect::to('user');
    }

    public function update(Request $request)
    {
        $user = User::findOrFail($request->id_usuario);
        $user->nombre = $request->nombre;
        $user->tipo_documento = $request->tipo_documento;
        $user->num_documento = $request->num_documento;
        $user->telefono = $request->telefono;
        $user->email = $request->email;
        $user->direccion = $request->direccion;
        $user->usuario = $request->usuario;
        $user->password = bcrypt($request->password);
        $user->condicion = true;
        $user->idrol = $request->id_rol;

        if($request->hasFile('imagen'))
        {
            if($user->imagen != 'noimagen.png')
            {
                Storage::delete('public/img/usuario/'.$user->imagen);
            }

            $filenamewithExt = $request->file('imagen')->getClientOriginalName();
            $filename = pathinfo($filenamewithExt, PATHINFO_FILENAME);
            $extension = $request->file('imagen')->guessClientExtension();
            $fileNameToStore = time().'.'.$extension;
            $path = $request->file('imagen')->storeAs('public/img/usuario',$fileNameToStore);
        }else{
            $fileNameToStore = $user->imagen;
        }
        $user->imagen=$fileNameToStore;
        $user->save();
        return Redirect::to("user");
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id_usuario);
        if($user->condicion == true)
        {
            $user->condicion = false;
            $user->save();
            return Redirect::to("user");
        }else{
            $user->condicion = true;
            $user->save();
            return Redirect::to("user");
        }
    }

}