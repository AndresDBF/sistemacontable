<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\Moneda;
use Illuminate\Http\Request;
use App\Models\Nomina;
use App\Models\TipCargoEmpleado;
use App\Models\ValoresNomina;
use App\Models\PagoNomina;
use App\Models\TotalPagoNomina;
use App\Models\CatgCuenta;
use App\Models\CatgSubCuenta;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:payroll.index')->only('index');
        $this->middleware('can:payroll.create')->only('create','store');
        $this->middleware('can:payroll.edit')->only('edit','update');
        $this->middleware('can:payroll.destroy')->only('destroy');
        $this->middleware('can:payemployee')->only('payemployee','storepayemployee');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registerEmployee = TipCargoEmpleado::join('nominas','tip_cargo_empleados.idcarg','=','nominas.idcarg')
                                            ->select('nominas.idnom','nominas.nombre','nominas.tipid','nominas.identificacion','nominas.tiprif'
                                                    ,'nominas.telefono','nominas.fec_ingr','concepto_cargo')
                                            ->where('stsemp','ACT')
                                            ->orderBy('nominas.nombre','asc')
                                            ->paginate(10);
        $registerCharges = TipCargoEmpleado::all();
        $registerValuePay = ValoresNomina::all();
        return view('payroll.index',compact('registerEmployee','registerCharges','registerValuePay'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $charges = TipCargoEmpleado::all();
        $countCharges = count($charges);
        $fecing = Carbon::now()->format('Y-m-d');
        if ($countCharges < 1) {
            Session::flash('error','no existen cargos registrados');
            return redirect('/payroll');
        }
        return view('payroll.employee.create',compact('charges','fecing'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $this->validate($request, [
            'name' => 'required|string|min:3|regex:/^[A-Z][a-zA-Z\s]+$/',
            'identification' => 'required|numeric',
            'phone' => 'required',
            'email' => 'required|email',
            'direction' => 'required'
        ], [
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.regex' => 'El nombre debe tener al menos dos palabras con la primera en mayúscula.',
            'identification.required' => 'La identificación es requerida.',
            'identification.numeric' => 'La identificación debe ser numérica.',
            'phone.required' => 'El teléfono es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'direction.required' => 'La dirección es requerida.'
        ]);    
        
        $employee = new Nomina();
        $employee->idcarg = intval($request->get('tipcarg'));
        $employee->nombre = $request->get('name');
        $employee->tipid = $request->get('tipid');
        $employee->identificacion = $request->get('identification');
        if (strlen($request->get('tiprif')) > 1) {
            $employee->tiprif = null;
            
        } else {
            $employee->tiprif->$request->get('tiprif');
        }
        $employee->telefono = $request->get('phone');
        $employee->direccion =$request->get('direction');
        $employee->correo = $request->get('email');
        $employee->stsemp = 'ACT';
        $employee->sueldo = floatval($request->get('salary'));
        $employee->fec_ingr = $request->get('fec_ing');
        $employee->save();
        Session::flash('employee','Empleado Registrado Correctamente');
        return redirect('/payroll');
        
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Nomina::find($id);
       
        $charges = TipCargoEmpleado::where('idcarg', '!=', $employee->idcarg)->get();
        $chargeEmployee = TipCargoEmpleado::where('idcarg',$employee->idcarg)->first();

        return view('payroll.employee.edit',compact('employee','charges','chargeEmployee'));
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
        $this->validate($request, [
            'name' => 'required|string|min:3|regex:/^[A-Z][a-zA-Z\s]+$/',
            'identification' => 'required|numeric',
            'phone' => 'required',
            'email' => 'required|email',
            'direction' => 'required'
        ], [
            'name.required' => 'El nombre es requerido.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.regex' => 'El nombre debe tener al menos dos palabras con la primera en mayúscula.',
            'identification.required' => 'La identificación es requerida.',
            'identification.numeric' => 'La identificación debe ser numérica.',
            'phone.required' => 'El teléfono es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'direction.required' => 'La dirección es requerida.'
        ]);  
        $employee = Nomina::find($id);
        $employee->idcarg = intval($request->get('tipcarg'));
        $employee->nombre = $request->get('name');
        $employee->tipid = $request->get('tipid');
        $employee->identificacion = $request->get('identification');       
        $employee->telefono = $request->get('phone');
        $employee->direccion =$request->get('direction');
        $employee->correo = $request->get('email');
        $employee->fec_ingr = $request->get('fec_ing');

        $employee->save();
        Session::flash('employee','Empleado Modificado Correctamente');
        return redirect('/payroll');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $employee = Nomina::find($id);
        $verifyPayroll = PagoNomina::where('idnom',intval($employee->idnom))->get();
        $countValues = count($verifyPayroll);
        if ($countValues > 0) {
            Session::flash('error','Existen Pagos Registrados no se puede borrar el empleado');
            return redirect('/payroll');
        } else{
            $employee->Delete();
        }
        Session::flash('destroy','Se ha borrado el empleado correctamente');
        return redirect('/payroll');
    }

    public function payemployee($idnom){
        $sysdate = Carbon::now();
        $threeMonthsAgo = $sysdate->copy()->subMonths(3);

        $seats = Asiento::select('monto_deb')
                        ->whereBetween('fec_asi', [$threeMonthsAgo->format('Y-m-d'), $sysdate->format('Y-m-d')])
                        ->whereBetween('idcta1', [4,16])
                        ->sum('monto_deb');

        
                       
        if ($seats > 200) {

            $employee = Nomina::find($idnom);
            $valueHed = ValoresNomina::where('idval',2)->first();
            $valueVh = ValoresNomina::where('idval',1)->first();
            $valueFes = ValoresNomina::where('idval',3)->first();
            $valueHen = ValoresNomina::where('idval',4)->first();
            $valueCes = ValoresNomina::where('idval',5)->first();
            $money = Moneda::all();
            return view('payroll.pay',compact('valueHed','employee','valueVh','valueFes','valueHen','valueCes','money','idnom'));
        }else {
            Session::flash('error','no cuenta con suficientes ingresos');
            return redirect('payroll');
        }

    }

    public function storepayemployee(Request $request){
        $this->validate($request,[
            'dayst' => 'required',
            'incent' => 'required'
        ]);
                
        if (strlen($request->get('money')) > 3) {
            Session::flash('error','debe seleccionar un tipo de moneda para el incentivo');
            return redirect()->route('payemployee',intval($request->get('idnom')));
        }
        if (strlen($request->get('money')) <= 3 && $request->get('incent') == null) {
            Session::flash('error','debe seleccionar el monto del incentivo');
            return redirect()->route('payemployee',intval($request->get('idnom')));
        }
        if (strlen($request->get('money')) <= 3 && $request->get('incent') != null && $request->tasa_cambio == null) {
            Session::flash('error','debe seleccionar la tasa de cambio');
            return redirect()->route('payemployee',intval($request->get('idnom')));
        }
        if (($request->get('indhed') == 'N' && $request->get('amounthed') != null) || ($request->get('indhed') == 'S' && $request->get('amounthed') == null)) {
            Session::flash('error','ingrese el valor de horas extras diurnas');
            return redirect()->route('payemployee',intval($request->get('idnom')));
        }
        elseif (($request->get('indfer') == 'N' && $request->get('amountfer') != null) || ($request->get('indfer') == 'S' && $request->get('amountfer') == null)) {
            Session::flash('error','ingrese el los dias feriados');
            return redirect()->route('payemployee',intval($request->get('idnom')));
        }
        elseif (($request->get('indhec') == 'N' && $request->get('amounthec') != null) || $request->get('indhec') == 'S' && $request->get('amounthec') == null) {
            Session::flash('error','ingrese el valor de horas extras nocturnas');
            return redirect()->route('payemployee',intval($request->get('idnom')));
        }
        $idcta1 = CatgSubCuenta::select('idcta')
                                ->where('idscu', $request->get('subaccountname1'))
                                ->first();
        $idcta2 = CatgSubCuenta::where('idscu', $request->get('subaccountname2'))
                                ->first();
        $fecpag = Carbon::now()->format('Y-m-d');
        $valueHed = ValoresNomina::where('idval',2)->first();
        $valueFes = ValoresNomina::where('idval',3)->first();
        $valueHen = ValoresNomina::where('idval',4)->first();
        $employee = Nomina::where('idnom',intval($request->get('idnom')))
                            ->first();
                            
        $valueMen = round(floatval($employee->sueldo / 30),2);
        //SUELDO DEL EMPLEADO
        $payrollSuel = new PagoNomina();
        $payrollSuel->idnom = intval($request->get('idnom'));
        $payrollSuel->concepto_pago = 'total de sueldo mensual';
        $payrollSuel->montopago = (floatval($valueMen * intval($request->get('dayst') ) ));
        $payrollSuel->diast = $request->get('dayst');
        $payrollSuel->fecpag = $fecpag;
        $payrollSuel->save();
        //HORAS EXTRAS DIURNAS SI REQUIERE
        $payrollHed = new PagoNomina();
        $payrollHed->idnom = intval($request->get('idnom'));
        $payrollHed->concepto_pago = $request->get('concepthed');
        if ($request->get('amounthed') == null) {
            $payrollHed->montopago = 0;
            $payrollHed->diast = 0;
        } else {
            $payrollHed->montopago = (floatval($request->get('amounthed') * $valueHed->monto_valor));
            $payrollHed->diast = $request->get('amounthed');
        }
        $payrollHed->fecpag = $fecpag;
        $payrollHed->save();
        //HORAS EXTRAS NOCTURNAS SI REQUIERE
        $payrollHen = new PagoNomina();
        $payrollHen->idnom = intval($request->get('idnom'));
        $payrollHen->concepto_pago = $request->get('concepthen');
        if ($request->get('amounthen') == null) {
            $payrollHen->montopago = 0;
            $payrollHen->diast = 0;
        } else {
            $payrollHen->montopago = (floatval($request->get('amounthen')) * floatval($valueHen->monto_valor));
            $payrollHen->diast = $request->get('amounthen');
        }

        $payrollHen->fecpag = $fecpag;
        $payrollHen->save();
        //DIAS FERIADO SI REQUIERE
        $payrollFer = new PagoNomina();
        $payrollFer->idnom = intval($request->get('idnom'));
        $payrollFer->concepto_pago = $request->get('conceptfer');
        if ($request->get('amountfer') == null) {
            $payrollFer->montopago = 0;
            $payrollFer->diast = 0;
        } else {
            $payrollFer->montopago = (floatval($request->get('amountfer')) * floatval($valueFes->monto_valor));
            $payrollFer->diast = $request->get('amountfer');
        }
        $payrollFer->fecpag = $fecpag;
        $payrollFer->save();
        //CESTATICKET
        $payrollCes = new PagoNomina();
        $payrollCes->idnom = intval($request->get('idnom'));
        $payrollCes->concepto_pago = $request->get('conceptces');
        $payrollCes->montopago = (floatval($request->get('cestaticket')));
        $payrollCes->diast = $request->get('dayst');
        $payrollCes->fecpag = $fecpag;
        $payrollCes->save();
        //INCENTIVO
        if (floatval($request->get('incent')) > 0 && floatval($request->get('incent') != null) ) {
            $payrollInc = new PagoNomina();
            $payrollInc->idnom = intval($request->get('idnom'));
            $payrollInc->concepto_pago = 'Incentivo';
            $payrollInc->montopago = (floatval($request->get('incent') / $request->get('tasa_cambio')));
            $payrollInc->diast = $request->get('dayst');
            $payrollInc->fecpag = $fecpag;
            $payrollInc->save();
        }

        $totalAsing = floatval($payrollSuel->montopag + $payrollSuel->montopago + $payrollHen->montopago +  $payrollFer->montopago + $payrollCes->montopago);
        $SSO = floatval(9.55);
        $FAOV = floatval(1.15);
        $totalDeduc = floatval( $SSO + $FAOV);
        $totalNeto = floatval($totalAsing - $totalDeduc);


        $seatAmount = new Asiento();
        $seatAmount->fec_asi = $fecpag;
        $seatAmount->observacion = $request->get('observation');
        $seatAmount->idcta1 = $idcta1->idcta;
        $seatAmount->idcta2 = $idcta2->idcta;
        $seatAmount->descripcion = $request->get('description');
        $seatAmount->monto_deb = $totalNeto;
        $seatAmount->monto_hab = $totalNeto;
        $seatAmount->save();

        if ($request->get('incent') != null) {
            $seatAmount = new Asiento();
            $seatAmount->fec_asi = $fecpag;
            $seatAmount->observacion = $request->get('observation');
            $seatAmount->idcta1 = 211;
            $seatAmount->idcta2 = $idcta2->idcta;
            $seatAmount->descripcion = $request->get('description');
            switch ($request->get('money')) {
                case 'USD':
                    $seatAmount->monto_deb = floatval($request->get('incent') * $request->get('tasa_cambio'));
                    $seatAmount->monto_hab = floatval($request->get('incent') * $request->get('tasa_cambio'));
                    break;
                case 'EUR':
                    $seatAmount->monto_deb = floatval($request->get('incent') * $request->get('tasa_cambio'));
                    $seatAmount->monto_hab = floatval($request->get('incent') * $request->get('tasa_cambio'));
                    break; 
                case 'COP':
                    $seatAmount->monto_deb = floatval($request->get('incent') / $request->get('tasa_cambio'));
                    $seatAmount->monto_hab = floatval($request->get('incent') / $request->get('tasa_cambio'));
                    break;
                case 'BS':
                    $seatAmount->monto_deb = floatval($request->get('incent'));
                    $seatAmount->monto_hab = floatval($request->get('incent'));
                    break; 
            }
            $seatAmount->save();
        }

        $totalPag = new TotalPagoNomina();
        $totalPag->idnom = intval($request->get('idnom'));
        $totalPag->idasi = $seatAmount->idasi;
        $totalPag->totalasignacion = $totalAsing;
        $totalPag->totaldeduccion = $totalDeduc;
        $totalPag->netocobrar = $totalNeto;
        $totalPag->fecpag = $fecpag;
        $totalPag->save();

        $idtnom = intval($totalPag->idtnom);
        $idnom = intval($employee->idnom);
        
        Session::flash('message','Se realizo el pago correctamente');
        return redirect()->route('totalpayemployee',['idnom' => $idnom, 'idtnom' => $idtnom,'fecpag' => $fecpag,'dayst' => intval($request->get('dayst'))]);

    }

    public function totalpayemployee($idnom,$idtnom,$fecpag,$dayst){
        $employee = Nomina::find($idnom);
        $totalpay = TotalPagoNomina::where('idnom',$idnom)
                                    ->where('fecpag',$fecpag)
                                    ->first();
        $tipcarg = TipCargoEmpleado::where('idcarg',intval($employee->idcarg))->first();
        return view('payroll.totalpay',compact('employee','totalpay','idnom','idtnom','fecpag','tipcarg','dayst'));

    }

    public function proofemployee($idnom,$idtnom,$fecpag,$dayst){
        $employee = Nomina::find($idnom);
        $totalpay = TotalPagoNomina::where('idnom',$idnom)
                                    ->where('fecpag',$fecpag)
                                    ->first();
        $salary = PagoNomina::where('idnom',$idnom)
                            ->where('concepto_pago','total de sueldo mensual')
                            ->where('fecpag',$fecpag)
                            ->first();
        $valueHED = PagoNomina::where('idnom',$idnom)
                            ->where('concepto_pago','Horas Extras Diurnas')
                            ->where('fecpag',$fecpag)
                            ->first();
        $valueHEN = PagoNomina::where('idnom',$idnom)
                            ->where('concepto_pago','Horas Extras Diurnas')
                            ->where('fecpag',$fecpag)
                            ->first();
        $valueFer= PagoNomina::where('idnom',$idnom)
                            ->where('concepto_pago','Feriada')
                            ->where('fecpag',$fecpag)
                            ->first();
        $valueCest = PagoNomina::where('idnom',$idnom)
                                ->where('concepto_pago','CestaTicket')
                                ->where('fecpag',$fecpag)
                                ->first();
        
        $salaryph = floatval($salary->montopago / 30);
        $sysdate = Carbon::now();
        $primerDia = $sysdate->startOfMonth()->format('Y-m-d');
        //obtener los dias no laborados
        $daysNT = intval(30 - $dayst);
        if ($daysNT > 0) {
            $restDays = floatval($salaryph * $daysNT);
        }else{
            $restDays= floatval(0.00);
        }

        // Obtener el último día del mes
        $ultimoDia = $sysdate->endOfMonth()->format('Y-m-d');
        $tipcarg = TipCargoEmpleado::where('idcarg',intval($employee->idcarg))->first();
        $pdf = PDF::loadView('payroll.proofpdf',compact('employee','daysNT','restDays','primerDia','ultimoDia','salaryph','totalpay','tipcarg','fecpag','dayst','salary','valueHED','valueHEN','valueFer','valueCest'));
        
        return $pdf->download("pago_empleado" . $employee->nombre . ".pdf");
    }

}
