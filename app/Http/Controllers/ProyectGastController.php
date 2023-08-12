<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProyeccionGasto;
use App\Models\Asiento;
use App\Models\CatgSubCuenta;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class ProyectGastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:proyectgast')->only('index');
        $this->middleware('can:createproyectgast')->only('createproyectgast','storeproyectgast');

    }
    public function index(){
        $sysdate = Carbon::now()->format('Y-m-d');
        $proyect = ProyeccionGasto::orderBy('fecstsfin','asc')
                                ->take(1)
                                ->first();
        if ($proyect == null) {
            /* $newProyect = new ProyeccionGasto();
            $newProyect->presupuesto = 1000;
            $newProyect->fecstsini = $sysdate;
            $newProyect->fecstsfin = $sysdate;
            $newProyect->save(); */
            
            Session::flash('message','Debe Crear el Primer Presupuesto');
            return view('proyect.index',compact('proyect'));
        }                      
        $seats = Asiento::whereBetween('fec_asi',[$proyect->fecstsini,$proyect->fecstsfin])
                        ->whereBetween('idcta1',[222,260])
                        ->whereNotIn('idcta1',[259  ])
                        ->orderBy('fec_asi','asc')
                        ->paginate(12);        
        if (count($seats)> 0) {
            
            $amountGast = $seats->pluck('monto_deb')->values();
            $amountSald = floatval($proyect->presupuesto);
            $amountSald =$amountSald - $amountGast[0];
            for ($i=0; $i < count($amountGast) ; $i++) { 
                $amountSald = $amountSald - $amountGast[$i];
            }
            
        }else{
            $amountGast = 0;
            $amountSald = floatval($proyect->presupuesto);
            $amountSald =$amountSald - $amountGast;
        }          
        

       
       
        
        if ($proyect->fecstsfin < $sysdate) {
            Session::flash('message','debe actualizar el presupuesto');
            return view('proyect.index',compact('seats','amountGast','proyect'));
        }

        

        return view('proyect.index',compact('seats','amountGast','proyect'));
    }

    public function createproyectgast(){
        $sysdate = Carbon::now();
        $threeMonthsAgo = $sysdate->copy()->subMonths(3);

        $seats = Asiento::select('monto_deb')
                        ->whereBetween('fec_asi', [$threeMonthsAgo->format('Y-m-d'), $sysdate->format('Y-m-d')])
                        ->whereBetween('idcta1', [4,16])
                        ->sum('monto_deb');

        $fecstsini = Carbon::now()->toDateString();
        $fecstsfin = Carbon::now()->addDays(15)->toDateString();


                        
                    
        return view('proyect.create',compact('seats','fecstsini','fecstsfin'));
    }

    public function storeproyectgast(Request $request){
        $this->validate($request,[
            'amount' => 'required'
        ]);
        if (floatval($request->get('amount')) > floatval($request->get('amountseat'))) {
            Session::flash('error','el monto supera el monto disponible de la empresa');
            return redirect()->route('createproyectgast');
        }
        $proyectGast = new ProyeccionGasto();
        $proyectGast->presupuesto = floatval($request->get('amount'));
        $proyectGast->presupuesto_ini = floatval($request->get('amount'));
        $proyectGast->fecstsini = $request->get('fecstsini');
        $proyectGast->fecstsfin = $request->get('fecstsfin');
        $proyectGast->save();

        return redirect()->route('proyectgast');
    }
}
