@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Ingreso</h1>
@stop

@section('content')
<div class="container">
    @if(session('error'))
    <div class="alert alert-danger" role="alert">
        {{ session('error') }}
    </div>
    @endif
    <div class="card">
        <div class="card-body pl-6">
        <h3 class="text-center fw-bolder pb-4">Ingresa la Identificación del Beneficiario de la Factura</h3>
            <div class="well">
                <div class="row">
                    <div class="col-xs-3 col-sm-6 col-md-4">
                        <div class="form-label">
                            <label for="dni">Tipo de Identificación</label>
                            <select name = 'tipid' class="custom-select">
                                <option selected="">Seleccionar Identificación</option>
                                <option value="V">V</option>
                                <option value="J">J</option>
                                <option value="E">E</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-6 col-md-4">
                        <label for="" class="form-label">Rif o Cedula del Cliente</label>
                        <input type="number" name="identification" id="identification" class="form-control text-decoration-none" tabindex="6">
                    </div>
                    <div class="col-xs-3 col-sm-6 col-md-4">
                        <label for="" class="form-label">Numero de Chequeo</label>
                        <select name = 'numcheck' class="custom-select">
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
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body pl-6">
        <h3 class="text-center fw-bolder pb-2">Facturas Pendientes</h3>
        <table id="invoice" class="table table-striped table-bordered shadow-lg mt-4" style="width: 100%">

            <thead class="bd-primary text-dark">
                <tr>
                    <th scope="col">Nombre del Cliente</th>
                    <th scope="col">Identificacion</th>
                    <th scope="col">Telefono</th>
                    <th scope="col">Monto del Contrato</th>
                    <th scope="col">Moneda</th>
                    <th scope="col">Seleccionar</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer as $cus)
                    @foreach ($contrCli as $contr)
                    <tr>
                    
                        <th class="text-center">{{$cus->nombre}}</th>
                        <th class="text-center">{{$cus->identificacion}}</th>
                        <th class="text-center">{{$cus->telefono}}</th> 
                        <th class="text-center">{{$contr->monto_pag}}</th>
                        <th class="text-center">{{$contr->moneda}}</th> 
                        <th>
                            <a href="{{route('createinvoiceing',['idcli'=>$customer->nombre,'idcli'=>$contrCli->idcont])}}">
                            <button type="button" class="btn btn-success">Ir</button>
                        </th>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop