<?php

namespace App\Http\Controllers;

use App\Models\ConceptoGasto;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\OrdenCompra;
use App\Models\DetalleOrdenCompra;
use App\Models\DetalleOrdenPago;
use App\Models\TipPago;
use App\Models\Moneda;
use App\Models\OrdenPago;
use App\Models\ConceptoOrden;
use App\Models\ProyeccionGasto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;


class PayOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:registerorder')->only('index');
        $this->middleware('can:createpayorder')
             ->only('createpayorder','store','detorder','storedetorder',
            'totalorder','payorderpdf','deleteorderpa','deletedetorderpa');
    }

    public function index(){
        $registerPurchase = Proveedor::join('orden_compras','proveedors.idprov','=','orden_compras.idprov')
                                    ->select('orden_compras.idorco','proveedors.idprov','orden_compras.numorden','proveedors.nombre',
                                    'orden_compras.tiempo_pago','proveedors.direccion')
                                    ->where('orden_compras.stsorden','AUT')
                                    ->orderBy('proveedors.nombre','asc')
                                    ->get();
        
        
        return view('payorder.index',compact('registerPurchase'));
    }

    public function createpayorder($idprov, $idorco){
        $numegre = rand(100000,999999);
        $supplier = Proveedor::find($idprov);
        $fecEmi = Carbon::now()->format('Y-m-d');
        $tippag = TipPago::where('tip_proceso','ingresos_gastos')
                            ->orderBy('descripcion')
                            ->get();
        $money = Moneda::all();
        

        return view('payorder.create',compact('numegre','supplier','fecEmi','tippag','money','idprov','idorco'));
    }

    public function store(Request $request){
        
        $this->validate($request,[
            'numfact' => 'required|numeric',
           // 'numctrl' => 'required|regex:/^\d{2}-\d{3}$/',
            'name' => 'required|regex:/^[A-Z][A-Z,a-z, ,á,é,í,ó,ú]+$/',
            'direction'=>'required',
            'tipid' => 'required',
            'identification' => 'required|numeric',
            'phone' => 'required',
            
        ]); 
         
        
        $idprov = $request->get('idprov');
        $idorco = $request->get('idorco');
        if ($request->get('tasa_cambio') != null) {
            $tasa_cambio = $request->get('tasa_cambio');
        } else {
            $tasa_cambio = 0;
        }
        
        
        if ($tasa_cambio == null && ($request->get('money') != 'BS')) {
            Session::flash('error','debe seleccionar una tasa de cambio');
            return redirect()->route('createpayorder',['idprov' => $idprov, 'idorco' => $idorco]);
        }
        
        $idorco = $request->get('idorco');
        $idprov = $request->get('idprov');
        $purchase = OrdenCompra::where('idorco',$idorco)->first();

        //para sumar a la fecha de vencimiento de la factura
        $fecemi = $request->get('fecemi');
        $newFecemi = Carbon::createFromFormat('Y-m-d',$fecemi);
        $value = intval($purchase->tiempo_pago);
        $fecven = $newFecemi->addDays($value);
    
        $newFecven = $fecven->format('Y-m-d');

        $payOrder = new OrdenPago();
        $payOrder->idorco = $request->get('idorco');
        $payOrder->idprov = $request->get('idprov');
        $payOrder->num_egre = $request->get('numrelegre');
        $payOrder->stsorpa = 'ACT';
        $payOrder->numfact = $request->get('numfact');
        $payOrder->numctrl = $request->get('numctrl');
        $payOrder->fec_emi = $fecemi;
        $payOrder->fec_vencimiento = $newFecven;
        if($request->get('tip_pag') == 'Selecciona un tipo de pago'){
            
            Session::flash('errorpag','debe seleccionar un tipo de pago');
            return redirect()->route('createpayorder',['idprov' => $idprov,'idorco' => $idorco]);
        }else{
            $payOrder->tippago = $request->get('tip_pag');
        }

        if($request->get('money') == 'Selecciona un tipo de moneda'){
            
            Session::flash('errormon','debe seleccionar un tipo de moneda');
            return redirect()->route('createpayorder',['idprov' => $idprov,'idorco' => $idorco]);
        }else{
            $payOrder->moneda = $request->get('money');
        }

        
        $payOrder->save();
        $numConcept = $request->get('numconcept');
        OrdenCompra::where('idorco', $request->get('idorco'))->update([
            'stsorden' => 'INC'
        
        ]);    
        return redirect()->route('detorder',['numConcept' => $numConcept, 'tasa' => $tasa_cambio]);
    }

    public function detorder($numConcept,$tasa){
        $payOrder = OrdenPago::orderBy('idorpa','desc')
                            ->take(1)
                            ->get();
        $idprov = $payOrder->pluck('idprov')->values()->first();
        $idorco = $payOrder->pluck('idorco')->values()->first();
        $tippag = $payOrder->pluck('tippago')->values()->first();
        $money  = $payOrder->pluck('moneda')->values()->first();
        $idorpa = $payOrder->pluck('idorpa')->values()->first();
        $supplier = Proveedor::where('idprov',$idprov)
                            ->first();
        $pay = TipPago::where('tippago',$tippag)->first();
        $tipmon = Moneda::where('tipmoneda',$money)->get();
        //dd($pay);
        return view('payorder.detorder',compact('supplier','pay','tipmon','numConcept','idprov','idorco','idorpa','tasa'))
                ->with('payOrder',$payOrder[0])
                ->with('pay',$pay)
                ->with('tipmon',$tipmon[0]);//seguir aqui
    }

    public function storedetorder(Request $request){
        $proyect = ProyeccionGasto::orderBy('fecstsini','asc')->first();
        $tasa_cambio = floatval($request->get('tasa'));
        $numConcept = intval($request->get('numconcept'));
        $taxes = 0;
        $amountTot = 0;
        $totOrder = 0;
        if ($numConcept == 1) {
            if ($request->get('amountUnit_0') == null) {
                Session::flash('error','Debe ingresar el monto en el concepto de la factura');
                return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
            }
            if ($request->get('CantUnit_0') == null) {
                Session::flash('error','Debe ingresar la cantidad en el concepto de la factura');
                return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
            }
            if ($request->get('concept_0') == null) {
                Session::flash('error','Debe ingresar una descripcion en el concepto de la factura');
                return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
            }
            $amountUnit = floatval($request->get('amountUnit_0'));
            $amountTot = floatval($request->get('total-amount0'));
            $conceptOrder = new ConceptoOrden();
            $conceptOrder->idorpa = $request->get('idorpa');
            $conceptOrder->descripcion = $request->get("concept_0");
            if ($request->get('money') == 'BS'){
                if ($proyect->presupuesto < floatval($amountTot) ) {
                    Session::flash('error','ha superado el monto total del presupuesto ');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                $conceptOrder->montounitariolocal = $amountUnit;
                $conceptOrder->montounitariomoneda = 0;
                $conceptOrder->montobienlocal = $amountTot;
                $conceptOrder->montobienmoneda = 0;
                $conceptOrder->save();
            }
            elseif ($request->get('money') == 'USD' || $request->get('money') == 'EUR') {
                if ($proyect->presupuesto < floatval($amountTot * $tasa_cambio) ) {
                    Session::flash('error','ha superado el monto total del presupuesto ');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                $conceptOrder->montounitariolocal =  $amountUnit * $tasa_cambio;
                $conceptOrder->montounitariomoneda = $amountUnit;
                $conceptOrder->montobienlocal = $amountTot * $tasa_cambio;
                $conceptOrder->montobienmoneda = $amountTot;
                $conceptOrder->save();
            }
            else {
                if ($proyect->presupuesto < floatval($amountTot / $tasa_cambio) ) {
                    Session::flash('error','ha superado el monto total del presupuesto ');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                $conceptOrder->montounitariolocal = $amountUnit / $tasa_cambio;
                $conceptOrder->montounitariomoneda =  $amountUnit;
                $conceptOrder->montobienlocal = $amountTot / $tasa_cambio;
                $conceptOrder->montobienmoneda = $amountTot;
                $conceptOrder->save();
            }
            
            if ($request->get('iva') == 'S') {
                $taxeslocal =  $conceptOrder->montobienlocal * 0.16;
                $taxesmoneda =  $conceptOrder->montobienmoneda * 0.16;
            }else {
                $taxeslocal =  0;
                $taxesmoneda =  0;
            }
            
           

            $amountTotlocal = $taxeslocal + $conceptOrder->montobienlocal;
            $amountTotmoneda = $taxesmoneda + $conceptOrder->montobienmoneda;

            if ($request->get('money') != 'BS') {
                $igtflocal = $amountTotlocal * 0.03;
                $igtfmoneda = $amountTotmoneda * 0.03;
            }
            else {
                $igtflocal = 0;
                $igtfmoneda = 0;
            }
            

            $totpayorderlocal = $amountTotlocal + $igtflocal;
            $totpayordermoneda = $amountTotmoneda + $igtfmoneda;
            if ($proyect->presupuesto < $totpayorderlocal ) {  
                $conceptOrdens = ConceptoOrden::where('idorpa',intval($request->get('idorpa')))->delete();

                Session::flash('error','ha superado el monto total del presupuesto');
                return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
            }

            $detOrder = new DetalleOrdenPago();
            $detOrder->idorpa = $request->get('idorpa');
            $detOrder->idcon = $conceptOrder->idcon;
            $detOrder->indiva = $request->get('iva');
            $detOrder->baseimponiblelocal = $conceptOrder->montobienlocal;
            $detOrder->baseimponiblemoneda = $conceptOrder->montobienmoneda;
            $detOrder->montoivalocal = $taxeslocal;
            $detOrder->montoivamoneda = $taxesmoneda;
            $detOrder->montototallocal = $totpayorderlocal;
            $detOrder->montototalmoneda = $totpayordermoneda;
            $detOrder->tasa_cambio = $tasa_cambio;
            $detOrder->save();
            $idorpa = $detOrder->idorpa;
            /* $sumAmount = ConceptoOrden::where('idorpa',$idorpa)
                                            ->sum('monto_bien');
            $taxes =  floatval($sumAmount * 0.16);
            $totOrder = floatval($sumAmount + $taxes);   
            DetalleOrdenPago::where('idorpa', $idorpa)->update([
                'monto_iva' => $taxes,
                'monto_total' => $totOrder
            ]); */
        }
        else {
            for ($i=0; $i < $numConcept; $i++) { 
               
                if ($request->get("amountUnit_" . $i) == null) {
                    Session::flash('error','Debe ingresar el monto en el concepto de la factura');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                if ($request->get("CantUnit_" . $i) == null) {
                    Session::flash('error','Debe ingresar la cantidad en el concepto de la factura');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                if ($request->get("concept_" .$i) == null) {
                    Session::flash('error','Debe ingresar una descripcion en el concepto de la factura');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                $amountUnit = floatval($request->get("amountUnit_" . $i));
                $amountTot = floatval($request->get("total-amount" . $i));
                $conceptOrder = new ConceptoOrden();
                $conceptOrder->idorpa = $request->get('idorpa');
                $conceptOrder->descripcion = $request->get("concept_" . $i);
                if ($request->get('money') == 'BS'){
                    if ($proyect->presupuesto < floatval($amountTot) ) {
                        Session::flash('error','ha superado el monto total del presupuesto ');
                        return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                    }
                    $conceptOrder->montounitariolocal = $amountUnit;
                    $conceptOrder->montounitariomoneda = 0;
                    $conceptOrder->montobienlocal = $amountTot;
                    $conceptOrder->montobienmoneda = 0;
                    $conceptOrder->save();
                }
                elseif ($request->get('money') == 'USD' || $request->get('money') == 'EUR') {
                    if ($proyect->presupuesto < floatval($amountTot * $tasa_cambio) ) {
                        Session::flash('error','ha superado el monto total del presupuesto ');
                        return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                    }
                    $conceptOrder->montounitariolocal =  $amountUnit * $tasa_cambio;
                    $conceptOrder->montounitariomoneda = $amountUnit;
                    $conceptOrder->montobienlocal = $amountTot * $tasa_cambio;
                    $conceptOrder->montobienmoneda = $amountTot;
                    $conceptOrder->save();
                }
                else {
                    if ($proyect->presupuesto < floatval($amountTot / $tasa_cambio) ) {
                        Session::flash('error','ha superado el monto total del presupuesto ');
                        return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                    }
                    $conceptOrder->montounitariolocal = $amountUnit / $tasa_cambio;
                    $conceptOrder->montounitariomoneda = $amountUnit;
                    $conceptOrder->montobienlocal = $amountTot / $tasa_cambio;
                    $conceptOrder->montobienmoneda = $amountTot;
                    $conceptOrder->save();
                }
            }   
            $conceptOrdensBienlocal = ConceptoOrden::where('idorpa',intval($request->get('idorpa')))->sum('montobienlocal');
            $conceptOrdensBienmoneda = ConceptoOrden::where('idorpa',intval($request->get('idorpa')))->sum('montobienmoneda');
                if ($request->get('iva') == 'S') {
                    $taxeslocal =  $conceptOrdensBienlocal * 0.16;
                    $taxesmoneda =  $conceptOrdensBienmoneda * 0.16;
                }else {
                    $taxeslocal =  0;
                    $taxesmoneda =  0;
                }
                
               
    
                $amountTotlocal = $taxeslocal + $conceptOrdensBienlocal;
                $amountTotmoneda = $taxesmoneda + $conceptOrdensBienmoneda;
    
                if ($request->get('money') != 'BS') {
                    $igtflocal = $amountTotlocal * 0.03;
                    $igtfmoneda = $amountTotmoneda * 0.03;
                }
                else {
                    $igtflocal = 0;
                    $igtfmoneda = 0;
                }
                
    
                $totpayorderlocal = $amountTotlocal + $igtflocal;
                $totpayordermoneda = $amountTotmoneda + $igtfmoneda;
                if ($proyect->presupuesto < $totpayorderlocal ) {  
                    $conceptOrdens = ConceptoOrden::where('idorpa',intval($request->get('idorpa')))->delete();
    
                    Session::flash('error','ha superado el monto total del presupuesto');
                    return redirect()->route('detorder',['numConcept' => intval($request->get('numconcept')), 'tasa' => floatval($request->get('tasa'))]);
                }
                $detOrder = new DetalleOrdenPago();
                $detOrder->idorpa = $request->get('idorpa');
                $detOrder->idcon = $conceptOrder->idcon;
                $detOrder->indiva = $request->get('iva');
                $detOrder->baseimponiblelocal = $conceptOrdensBienlocal;
                $detOrder->baseimponiblemoneda = $conceptOrdensBienmoneda;
                $detOrder->montoivalocal = $taxeslocal;
                $detOrder->montoivamoneda = $taxesmoneda;
                $detOrder->montototallocal = $totpayorderlocal;
                $detOrder->montototalmoneda = $totpayordermoneda;
                $detOrder->save();
                $idorpa = $detOrder->idorpa;
            
        }
        
        return redirect()->route('totalorderpa', ['idorpa' => $idorpa]);
    }

    public function totalorder($idorpa){
        $valueIdorpa = intval($idorpa);
        $amountOrder = ConceptoOrden::where('idorpa',$idorpa)
                                            ->get();
        $detailOrder = DetalleOrdenPago::where('idorpa',$idorpa)->first();    
        $payOrder = OrdenPago::where('idorpa',$idorpa)->first();    

        return view('payorder.total',['amountOrder' => $amountOrder],compact('detailOrder','idorpa','payOrder'));
    }

    public function payorderpdf($idorpa,$idprov){
        $amountOrder = ConceptoOrden::where('idorpa',$idorpa)
                                     ->get();
        $detailOrder = DetalleOrdenPago::where('idorpa',$idorpa)->first();    
        $payOrder = OrdenPago::where('idorpa',$idorpa)->first();    
        $supplier = Proveedor::find($idprov);
        $imagePath = storage_path("img/logo.png");
      //  $image = "data:img/logo.png;base64,".base64_encode(file_get_contents($imagePath));     
        $image = base64_encode(file_get_contents($imagePath));
        

        $pdf = PDF::loadView('payorder.payorderpdf',compact('amountOrder','detailOrder','payOrder','supplier','image'));
        
        return $pdf->download("orden_pago_" . $supplier->nombre . ".pdf");
    }
    public function deleteorderpa($idprov,$idorco){
       $payOrder = OrdenPago::where('idorco',$idorco)->delete();
       return redirect()->route('createpayorder',['idprov' => $idprov, 'idorco' => $idorco]);
    }

    public function deletedetorderpa($idorpa){
        $detailorder = DetalleOrdenPago::where('idorpa',$idorpa)->first();
        $proyeccionGasto = ProyeccionGasto::orderBy('fecstsfin', 'asc')->first();
        $proyeccionGasto->presupuesto = $detailorder->sum('montototallocal');
        $proyeccionGasto->save();
        $detailorder = DetalleOrdenPago::where('idorpa',$idorpa)->delete();
        $conceptpayorder = ConceptoOrden::where('idorpa',$idorpa)->delete();

        $payorder = OrdenPago::where('idorpa',$idorpa)->first();
        OrdenCompra::where('idorco',intval($payorder->idorco))->update([
            'stsorden' => 'AUT'
        ]);
        $payorder = OrdenPago::where('idorpa',$idorpa)->delete();
        return redirect()->route('registerorder');
    }

}
