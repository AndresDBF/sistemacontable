@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="container"> 
        <div class="card">
            <div class="card-body pl-6">
                <form action="{{route('storedetinvoiceing')}}" method="POST">
                    @csrf
                    <div class="well">
                        <div class="row">
                            <div class="col-xs-3 col-sm-6 col-md-6">
                                <label for="" class="form-label">Numero de Relacion de ingreso</label>
                                <input type="text" name="numreling" id="numreling" class="form-control" readonly="readonly" value="{{$query->num_ing}}" tabindex="1">
                            </div>
                            <div class="col-xs-3 col-sm-6 col-md-6">
                                <label for="" class="form-label">fecha de emision</label>
                                <input type="text" name="fecemi" id="fecemi"  class="form-control" tabindex="4" value="{{$detInvoice->fec_emi}}" readonly="readonly">
                            </div>
                        </div>
                    </div>
                    <div class="well">
                        <div class="row">
                            <div class="col-xs-3 col-sm-6 col-md-6">
                                <label for="" class="form-label">Numero de Factura</label>
                                <input type="text" name="numfact" id="numfact" value="000{{$detInvoice->numfact}}" class="form-control" tabindex="2">
                            </div>
                            <div class="col-xs-3 col-sm-6 col-md-6">
                                <label for="" class="form-label">Control de Factura</label>
                                <input type="text" name="numctrl" id="numctrl" value="{{$detInvoice->numctrl}}" class="form-control" tabindex="3">
                            </div>
                            
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Nombre y Apellido o Razon Social</label>
                        <input type="text" name="name" id="name" value="{{$invoice->nomacre}}" class="form-control" tabindex="4">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Direccion de Factura</label>
                        <input type="text" name="direction" id="direction" value="{{$invoice->dirfact}}" class="form-control" tabindex="5">
                    </div>
                    <div class="well">
                        <div class="row">
                            <div class="col-xs-3 col-sm-6 col-md-4">
                                <div class="form-label">
                                    <label for="dni">Tipo de Identificación</label>
                                    <select name = 'tipid' class="custom-select">
                                        @if ($invoice->tipid == 'V')
                                            <option value="V">V</option>
                                            <option value="J">J</option>
                                            <option value="E">E</option>
                                        @elseif($invoice->tipid == 'J')
                                            <option value="J">J</option>
                                            <option value="V">V</option>
                                            <option value="E">E</option>
                                        @elseif($invoice->tipid == 'E')
                                         <option value="E">E</option>
                                         <option value="V">V</option>
                                         <option value="E">E</option>
                                        @else
                                            <option selected="">Seleccionar Identificación</option>
                                            <option value="V">V</option>
                                            <option value="J">J</option>
                                            <option value="E">E</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-3 col-sm-6 col-md-4">
                                <label for="" class="form-label">Rif o Cedula del Cliente</label>
                                <input type="number" name="identification" id="identification" value="{{$invoice->identificacion}}" class="form-control text-decoration-none" tabindex="6">
                            </div>
                            <div class="col-xs-3 col-sm-6 col-md-4">
                                <label for="" class="form-label">Numero de Chequeo</label>
                                <select name = 'numcheck' class="custom-select">
                                    @if ($invoice->tiprif == null)
                                        <option selected="">Seleccionar Numero</option>
                                        <option value="0">0</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>                               
                                    @else
                                        @for ($i = 0; $i < 10; $i++)
                                            @if ($i == $invoice->tiprif)
                                                <option value="{{$i}}" selected>{{$i}}</option>
                                            @endif
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    
                                    @endif
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Telefono</label>
                        <input type="number" name="phone" id="phone" value="{{$invoice->telefono}}" class="form-control text-decoration-none" tabindex="7">
                    </div>
                    <div class="well">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6">
                                <div class="form-group">
                                  <label for="dni">Tipo de Pago</label>
                                  <select name = 'tip_pag' class="custom-select">
                                    @if ($invoice->tip_pago == 'ANU')
                                        <option value="{{$invoice->tip_pago}}">ANUAL</option>
                                    @endif
                                    @if ($invoice->tip_pago == 'MEN')
                                        <option value="{{$invoice->tip_pago}}">MENSUAL</option>
                                    @endif
                                    @if ($invoice->tip_pago == 'SEM')
                                        <option value="{{$invoice->tip_pago}}">SEMESTRAL</option>
                                    @endif
                                    @if ($invoice->tip_pago == 'TRI')
                                        <option value="{{$invoice->tip_pago}}">TRIMESTRAL</option>
                                    @endif
                                    
                                    @foreach ($tippag as $tip)
                                        @if ($invoice->tip_pago == $tip->tippago)
                                        @continue
                                        @endif
                                        <option value="{{$tip->tippago}}">{{$tip->descripcion}}</option>
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-6">
                                <label for="" class="form-label">Numero Total de descripcion de factura</label>
                                <input type="numeric" name="numconcept" id="numconcept" value="{{$cantConcept}}" readonly="readonly" class="form-control" tabindex="4">
                            </div>
                        </div>
                    </div>
                    
            </div>
        </div>  
        <div class="card">
            <div class="card-body pl-6">
                
                <div class="well">
                    <div class="row">
                        <div class="col-xs-1 col-sm-1 col-md-1">
                            <label for="" class="form-label">Cantidad</label>
                            @for ($i = 0; $i < $cantConcept; $i++)
                                <input type="number" name="CantUnit_{{$i}}" id="CantUnit_{{$i}}" class="form-control text-decoration-none price-input" tabindex="7">
                                <br>
                            @endfor
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-5">
                            <label for="" class="form-label">Concepto de factura</label>
                            @for ($i = 0; $i < $cantConcept; $i++)
                                <input type="text" name="concept_{{$i}}" id="concept_{{$i}}" class="form-control text-decoration-none" tabindex="7">
                                <br>
                            @endfor
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <label for="" class="form-label">Precio Unitario</label>
                            @for ($i = 0; $i < $cantConcept; $i++)
                                <input type="number" name="amountUnit_{{$i}}" id="amountUnit_{{$i}}" class="form-control text-decoration-none price-input" tabindex="7">
                                <br>
                            @endfor
                        </div>
                        <div class="col-xs-3 col-sm-3 col-md-3">
                            <label for="" class="form-label">Precio del Bien o Servicio</label>
                            @for ($i = 0; $i < $cantConcept; $i++)
                                <input type="number" name="total-amount{{$i}}" id="total-amount{{$i}}" class="form-control text-decoration-none total-amount" tabindex="7" readonly>
                                <br>
                            @endfor
                        </div>
                    </div>
                </div>                                                
            </div>
        </div> 
        <div class="well pb-3 mt-3">
            <a href="/home" class="btn btn-secondary" tabindex="5">Cancelar</a>
            <button type="submit" class="btn btn-primary" tabindex="6">Siguiente</button>
        </div> 
        </form>      
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
    $('.price-input').change(function() {
        var index = $(this).attr('id').replace(/[^\d]/g, '');
        var cantidad = $('#CantUnit_'+index).val();
        var precio_unitario = $('#amountUnit_'+index).val();
        var total = cantidad * precio_unitario;
        $('#total-amount'+index).val(total);
    });
});

</script>

@endsection