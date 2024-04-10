<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request)
        {
            $sql = trim($request->get('buscarTexto'));
            $productos = DB::table('productos as p')
            ->join('categorias as c', 'p.idcategoria', '=', 'c.id')
            ->select('p.id', 'p.idcategoria', 'p.nombre', 'p.precio_venta', 'p.codigo', 'p.stock', 'p.imagen', 'p.condicion', 'c.nombre as Categoria')
            ->where('p.nombre', 'LIKE', '%'.$sql.'%')
            ->orwhere('p.codigo', 'LIKE', '%'.$sql.'%')
            ->orderby('p.id', 'desc')
            ->paginate(10);

            //Listar las categorias en ventana modal
            $categorias = DB::table('categorias')
            ->select('id', 'nombre', 'descripcion')
            ->where('condicion', '=', true)->get();

            return view('producto.index', ["productos"=>$productos, "categorias"=>$categorias, "buscarTexto"=>$sql]);
            // return $productos;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $producto = new Producto();
        $producto->idcategoria = $request->id;
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->precio_venta = $request->precio_venta;
        $producto->stock = '0';
        $producto->condicion = true;
        if($request->hasFile('imagen'))
        {
            $filenamewithExt = $request->file('imagen')->getClientOriginalName();   //Obtener el nombre del archivo con la extension
            $filename = pathinfo($filenamewithExt, PATHINFO_FILENAME);              //Obtener solamente el nombre del archivo
            $extension = $request->file('imagen')->guessClientExtension();         //Obtener solamente la extension
            $fileNameToStore = time().'.'.$extension;                               //COnfigurar el nombre con el que se va a almacenar la imnagen
            $path = $request->file('imagen')->storeAs('public/img/producto', $fileNameToStore);     //Configura la ruta en donde sera almacenada
        }else{
            $fileNameToStore='noimagen.png';    //En caso de no haber imagen, guarda una por defecto
        }
        $producto->imagen = $fileNameToStore;
        $producto->save();
        return Redirect::to('producto');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        $producto = Producto::findOrFail($request->id_producto);
        $producto->idcategoria = $request->id;
        $producto->codigo = $request->codigo;
        $producto->nombre = $request->nombre;
        $producto->precio_venta = $request->precio_venta;
        $producto->stock = '0';
        $producto->condicion = true;

        if($request->hasFile('imagen'))
        {
            if($producto->imagen != 'noimagen.png')
            {
                Storage::delete('public/img/producto/'.$producto->imagen);
            }
            $filenamewithExt = $request->file('imagen')->getClientOriginalName();   //Obtener el nombre del archivo con la extension
            $filename = pathinfo($filenamewithExt, PATHINFO_FILENAME);              //Obtener solamente el nombre del archivo
            $extension = $request->file('imagen')->guessClientExtension();         //Obtener solamente la extension
            $fileNameToStore = time().'.'.$extension;                               //COnfigurar el nombre con el que se va a almacenar la imnagen
            $path = $request->file('imagen')->storeAs('public/img/producto', $fileNameToStore);     //Configura la ruta en donde sera almacenada
        }else{
            $fileNameToStore = $producto->imagen;    //En caso de no haber imagen, guarda una por defecto
        }
        $producto->imagen = $fileNameToStore;
        $producto->save();
        return Redirect::to('producto');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $producto = Producto::findOrFail($request->id_producto);

        if($producto->condicion==true)
        {
            $producto->condicion = false;
            $producto->save();
            return Redirect::to('producto');
        }else{
            $producto->condicion = true;
            $producto->save();
            return Redirect::to('producto');
        }
    }

    public function listarPdf()
    {
        $productos = Producto::join('categorias', 'productos.idcategoria', '=', 'categorias.id')
        ->select('productos.id', 'productos.idcategoria', 'productos.codigo', 'productos.nombre', 'categorias.nombre AS nombre-categoria', 'productos.stock', 'productos.condicion')
        ->orderBy('productos.nombre', 'desc')
        ->get();

        $cont = Producto::count();

        $pdf = \PDF::loadview('pdf.productospdf', ['productos'=>$productos, 'cont'=>$cont]);
        return $pdf->download('productos.pdf');
    }
}