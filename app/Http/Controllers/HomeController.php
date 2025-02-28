<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $comprasmes = DB::select ('SELECT extract('month from fecha_compra') as month, to_char(');

        

        $comprasmes = DB::select("SELECT EXTRACT(MONTH from fecha_compra) as mes, sum(c.total) as totalmes from compras c WHERE c.estado = 'Registrado' GROUP BY fecha_compra ORDER BY mes DESC LIMIT 12");
        // $comprasmes = DB::select('SELECT monthname(c.fecha_compra) as mes, sum(c.total) as totalmes from compras c where c.estado="Registrado" group by monthname(c.fecha_compra) order by month(c.fecha_compra) desc limit 12');
       
        $ventasmes = DB::select("SELECT EXTRACT(MONTH FROM fecha_venta) as mes, sum(v.total) as totalmes from ventas v WHERE v.estado = 'Registrado' GROUP BY fecha_venta ORDER BY mes DESC LIMIT 12");
        // $ventasmes = DB::select('SELECT monthname(v.fecha_venta) as mes, sum(v.total) as totalmes from ventas v where v.estado="Registrado" group by monthname(v.fecha_venta) order by month(v.fecha_venta) desc limit 12');
        
        $ventasdia = DB::select("SELECT to_char(v.fecha_venta, 'DD-MON-yyyy') AS dia, SUM(v.total) AS totaldia FROM ventas v WHERE v.estado = 'Registrado' GROUP BY v.fecha_venta ORDER BY v.fecha_venta DESC LIMIT 15");
        // $ventasdia = DB::select('SELECT DATE_FORMAT(v.fecha_venta,"%d/%m/%Y") as dia, sum(v.total) as totaldia from ventas v where v.estado="Registrado" group by v.fecha_venta order by day(v.fecha_venta) desc limit 15');

        $productosvendidos = DB::select("SELECT p.nombre as producto, sum(dv.cantidad) as cantidad from productos p inner join detalle_ventas dv on p.id=dv.idproducto inner join ventas v on dv.idventa=v.id where v.estado='Registrado' and EXTRACT(YEAR FROM v.fecha_venta) = EXTRACT(YEAR FROM current_date) group by p.nombre order by sum(dv.cantidad) desc limit 10");
        // $productosvendidos = DB::select('SELECT p.nombre as producto, sum(dv.cantidad) as cantidad from productos p inner join detalle_ventas dv on p.id=dv.idproducto inner join ventas v on dv.idventa=v.id where v.estado="Registrado" and year(v.fecha_venta)=year(curdate()) group by p.nombre order by sum(dv.cantidad) desc limit 10');

        $totales = DB::select("SELECT (select COALESCE(sum(c.total),0) from compras c where DATE(c.fecha_compra) = current_date and c.estado = 'Registrado') AS totalcompra, (select COALESCE(sum(v.total),0) from ventas v where DATE(v.fecha_venta) = current_date and v.estado='Registrado') as totalventa;");
        // SELECT (select COALESCE(sum(c.total),0) from compras c where DATE(c.fecha_compra) = current_date and c.estado = "Registrado") AS totalcompra, (select COALESCE(sum(v.total),0) from ventas v where DATE(v.fecha_venta) = current_date and v.estado="Registrado") as totalventa;
        // $totales = DB::select('SELECT (select ifnull(sum(c.total),0) from compras c where DATE(c.fecha_compra)=curdate() and c.estado="Registrado") as totalcompra, (select ifnull(sum(v.total),0) from ventas v where DATE(v.fecha_venta)=curdate() and v.estado="Registrado") as totalventa');

        return view('home',["comprasmes"=>$comprasmes, "ventasmes"=>$ventasmes, "ventasdia"=>$ventasdia, "productosvendidos"=>$productosvendidos, "totales"=>$totales]);
        // return view('home',["comprasmes"=>$comprasmes, "ventasmes"=>$ventasmes, "ventasdia"=>$ventasdia, "productosvendidos"=>$productosvendidos]);
        // return view('home');
        // return $totales;
    
    }

}
