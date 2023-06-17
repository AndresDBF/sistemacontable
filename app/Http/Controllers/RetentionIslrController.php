<?php

namespace App\Http\Controllers;

use App\Models\Asiento;
use App\Models\CatgCuenta;
use App\Models\CatCuenta;
use App\Models\CatgSubCuenta;
use App\Models\OrdenPago;
use App\Models\Proveedor;
use App\Models\TipoAgente;
use App\Models\RetencionIslr;
use App\Models\DetalleRetencionIslr;
use App\Models\Retencion;
use App\Models\DetalleRetencion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class RetentionIslrController extends Controller
{
    public function index()
    {
        $tipagente = TipoAgente::orderBy('concepto','asc')->get();
        return view('islr.index',compact('tipagente'));
    }

    public function listreten(Request $request)
    {
        $tipagent = TipoAgente::where('idage',intval($request->get('tipagente')))->first();

        if ($tipagent->sustraendo != 'N/A' && $tipagent->mayorpago != 'TODO PAGO') {
            $registerOrderPay = OrdenPago::join('detalle_orden_pagos','orden_pagos.idorpa','=','detalle_orden_pagos.idorpa')
            ->select('orden_pagos.idorpa','orden_pagos.idprov','orden_pagos.numfact','orden_pagos.fec_emi','orden_pagos.moneda',
            'detalle_orden_pagos.baseimponiblelocal')
            ->where('orden_pagos.stsorpa','PEN')
            // ->where('detalle_orden_pagos.baseimponiblelocal', '<', floatval($tipagent->mayorpago))
            ->first();

            if (floatval($request->baseimponiblelocal) > floatval($tipagent->mayorpago)) {
                $idprov = $registerOrderPay->pluck('idprov');
                $supplier = Proveedor::select('nombre','tipid','identificacion','tiprif')
                    ->whereIn('idprov',$idprov)->get();
                return view('islr.list',compact('registerOrderPay','tipagent','supplier'));
            }
            else {
                Session::flash('message','El monto es menor a lo estimado, no requiere retencion');
                return redirect()->route('findagent');
            }

        }
        elseif ($tipagent->mayorpago == 'TODO PAGO') {
            $registerOrderPay = OrdenPago::join('detalle_orden_pagos','orden_pagos.idorpa','=','detalle_orden_pagos.idorpa')
            ->select('orden_pagos.idorpa','orden_pagos.idprov','orden_pagos.numfact','orden_pagos.fec_emi','orden_pagos.moneda',
            'detalle_orden_pagos.montototallocal','detalle_orden_pagos.montototalmoneda','detalle_orden_pagos.baseimponiblelocal')
            ->where('orden_pagos.stsorpa','PEN')
            ->get();
            $idprov = $registerOrderPay->pluck('idprov');
            $supplier = Proveedor::select('nombre','tipid','identificacion','tiprif')
                ->whereIn('idprov',$idprov)->get();
            return view('islr.list',compact('registerOrderPay','tipagent','supplier'));
        }
        else {
            Session::flash('message','el proveedor no requiere Retencion de I.S.L.R');
            return redirect()->route('findagent');
        }
    }

    public function create($idorpa,$idprov,$idage)
    {
        $fecEmi = Carbon::now()->format('Y-m-d');
        $perFiscal = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        $retention = Retencion::orderBy('idret','asc')
            ->take(1)
            ->get();
        $countReten = count($retention);
        if ($countReten < 1) {
            $formatVoucher = Carbon::now()->format('Ymd');
            $numOper = 1;
            $nVoucher = ($formatVoucher . '000' . 1);
        }
        else {
            $formatVoucher = Carbon::now()->format('Ymd');
            $numOper = $countReten + 1;
            $nVoucher = ($formatVoucher . '000' . intval($countReten + 1));
        }

        $supplier = Proveedor::where('idprov',$idprov)->first();
        $registerOrderPay = OrdenPago::join('detalle_orden_pagos','orden_pagos.idorpa','=','detalle_orden_pagos.idorpa')
            ->select('orden_pagos.idorpa','orden_pagos.idprov','orden_pagos.numfact','orden_pagos.numctrl',
                'orden_pagos.fec_emi','orden_pagos.moneda','detalle_orden_pagos.baseimponiblelocal')
            ->where('orden_pagos.stsorpa','PEN')
            ->where('orden_pagos.idorpa',intval($idorpa))
            ->first();
            dd($registerOrderPay);
        $tipagent = TipoAgente::where('idage',$idage)->first();
        return view('islr.create',compact('registerOrderPay','supplier','tipagent','fecEmi','perFiscal','month','nVoucher','numOper'));
    }

    public function store(Request $request)
    {
/*         $this->validate($request,[
            'numfact' => 'required',
            'numcontrl' => 'required',
            'base' => 'required',
        ]); */
        
      
        $pay = OrdenPago::join('comprobante_pagos','orden_pagos.idorpa','=','comprobante_pagos.idorpa')
            ->select('comprobante_pagos.idpag','orden_pagos.numfact','orden_pagos.numctrl')
            ->where('orden_pagos.idorpa',intval($request->get('idorpa')))
            ->first();
        $idcta1 = CatgSubCuenta::select('idcta')
            ->where('idscu', $request->get('subaccountname1'))
            ->first();
        $idcta2 = CatgSubCuenta::where('idscu', $request->get('subaccountname2'))
            ->first();

        $seatAmount = new Asiento();
        $seatAmount->fec_asi = $request->get('fecemi');
        $seatAmount->observacion = $request->get('observation');
        $seatAmount->idcta1 = $idcta1->idcta;
        $seatAmount->idcta2 = $idcta2->idcta;
        $seatAmount->descripcion = $request->get('description');
        $seatAmount->monto_deb = floatval($request->get('taxesreten'));
        $seatAmount->monto_hab = floatval($request->get('taxesreten'));
        $seatAmount->save();

        $seatislr = new Asiento();
        $seatislr->fec_asi = $request->get('fecemi');
        $seatislr->observacion = $request->get('observation');
        $seatislr->idcta1 = 87;
        $seatislr->idcta2 = 87;
        $seatislr->descripcion = $request->get('description');
        $seatislr->monto_deb = floatval($request->get('taxesreten'));
        $seatislr->monto_hab = floatval($request->get('taxesreten'));
        $seatislr->save();

        $retention = new RetencionIslr();
        $retention->idpag = $pay->idpag;
        $retention->idasi = $seatAmount->idasi;
        $retention->idprov = intval($request->get('idprov'));
        $retention->idorpa = intval($request->get('idorpa'));
        $retention->idage = intval($request->get('idage'));
        $retention->ncomprobante = $request->get('nvoucher');
        $retention->fecemi = $request->get('fecemi');
        $retention->save();

        $detRetention = new DetalleRetencionIslr();
        $detRetention->idreti = $retention->idreti;
        $detRetention->fecemifact = $request->get('fecemifact');
        if ($request->get('numfact') != $pay->numfact) {
            $registerIslr = RetencionIslr::where('idreti',intval($retention->idreti))->delete();
            $registerDetIslr = Asiento::where('fec_asi',$request->get('fecemi'))->delete();
            Session::flash('error','el numero de factura no coincide con el Pago registrado');
            return redirect()->route('createislr',['idorpa' => intval($request->get('idorpa')), 'idprov' => intval($request->get('idprov'))]);
        }
        elseif ($request->get('numctrl') != $pay->numctrl) {
            $registerIslr = RetencionIslr::where('idreti',intval($retention->idreti))->delete();
            $registerDetIslr = Asiento::where('fec_asi',$request->get('fecemi'))->delete();
            Session::flash('error','el numero de Control no coincide con el Pago registrado');
            return redirect()->route('createislr',['idorpa' => intval($request->get('idorpa')), 'idprov' => intval($request->get('idprov'))]);
        }
        else {
            $detRetention->numfact = $request->get('numfact');
            $detRetention->numctrl = $request->get('numctrl');
            $detRetention->concepto = $request->get('concept');
            $detRetention->baseimponible = floatval($request->get('base'));
            $detRetention->porcentajeret = floatval($request->get('reten'));
            $detRetention->montoretenido = floatval($request->get('taxesreten'));
            $detRetention->save();

            Session::flash('message','Se ha realizado la Retención de I.S.L.R. correctamente');
            return redirect()->route('findagent');
        }
    }
    public function tipcontribuyente()
    {
        return TipoAgente::distinct()->get(['tippersona']);
    }
    public function tipagente(Request $request)
    {
        return TipoAgente::where("tippersona",$request->tippersona)->orderBy('concepto','asc')->get();
    }
}



