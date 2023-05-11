@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Creació de Ingreso</h1>
@stop

@section('content')
<div class="container">
    @if(session('error'))
    <div class="alert alert-success" role="alert">
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
                                @if ($tipId == 'V')
                                    <option value="V">V</option>
                                    <option value="J">J</option>
                                    <option value="E">E</option>
                                @elseif(tipId == 'J')
                                    <option value="J">J</option>
                                    <option value="V">V</option>
                                    <option value="E">E</option>
                                @elseif($tipId == 'E')
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
                        <input type="number" name="identification" value="{{$identification}}" id="identification" class="form-control text-decoration-none">
                    </div>
                    <div class="col-xs-3 col-sm-6 col-md-4">
                        <label for="" class="form-label">Numero de Chequeo</label>
                        <select name = 'numcheck' class="custom-select">
                            @if ($numCheck == null)
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
                                    @if ($i == $numCheck)
                                        <option value="{{$i}}" selected readonly = "readonly">{{$i}}</option>
                                    @endif
                                    <option value="{{$i}}">{{$i}}</option>
                                @endfor
                            
                            @endif
                            
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body pl-6">
        <h3 class="text-center fw-bolder">Creacion de  Ingreso</h3>
        <form action="{{route('storeIncome')}}" method="POST" id="myform">
            @csrf
            <div class="well">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="form-label">Fecha de Transacción</label>
                        <input type="text" name="fecTransiction" value="{{$detProof->fec_trans}}" id="fecTransiction" readonly="readonly" class="form-control text-decoration-none text-center">
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="form-label">Fecha de Ingreso</label>
                        <input type="text" name="fecIncome" value="{{$fecRegister}}" id="fecTransiction" readonly="readonly" class="form-control text-decoration-none text-center">
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="form-label">Numero de Confirmación</label>
                        <input type="text" name="numconfirm" value="{{$proofIncome->numconfirm}}" id="numconfirm" class="form-control text-decoration-none" tabindex="1">
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="form-label">Numero de factura</label>
                        <input type="text" name="numfact" value="{{$detInvoice->numfact}}" id="numfact" readonly="readonly" class="form-control text-decoration-none">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Nombre y Apellido o Razon Social</label>
                <input type="text" name="name" id="name" value="{{$invoice->nomacre}}" class="form-control" readonly="readonly">
            </div>
            <div class="well">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="">Forma de Pago</label>
                        <select name="formPay" class="custom-select">
                            @if ($detProof->formpago == "EFE")
                                <option value="{{$detProof->formpago}}" readonly="readonly" >EFECTIVO</option>
                            @elseif ($detProof->formpago == "TRA")
                                <option value="{{$detProof->formpago}}" readonly="readonly" >TRANSFERENCIA BANCARIA</option>
                            @elseif ($detProof->formpago == "PMO")
                                <option value="{{$detProof->formpago}}" readonly="readonly" >PAGO MOVIL</option>
                            @elseif ($detProof->formpago == "TDE")
                                <option value="{{$detProof->formpago}}" readonly="readonly" >TARJETA DE DEBITO</option>    
                            @elseif($detProof->formpago == "TRC")
                                <option value="{{$detProof->formpago}}">TARJETA DE CREDITO</option>    
                            @endif
                        </select>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="form-label">Moneda</label>
                        <select name = 'money' class="custom-select" @readonly(true)>
                            @if ($contrCli->moneda == 'BS')
                                <option value="{{$contrCli->moneda}}">BOLIVARES</option>
                            @elseif ($contrCli->moneda == 'USD')
                                <option value="{{$contrCli->moneda}}">DOLAR ESTADOUNIDENSE</option>
                            @elseif ($contrCli->moneda == 'COP')
                                <option value="{{$contrCli->moneda}}">PESOS COLOMBIANOS</option>
                            @elseif ($contrCli->moneda == "EUR")
                                <option value="{{$contrCli->moneda}}">EUROS</option>
                            @endif
                           
                               
                          
                        </select>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <label for="" class="form-label">Cantidad</label>
                        <input type="text" name="amount" value="{{$proofIncome->cantidad}}" id="amount" class="form-control" tabindex="4" readonly="readonly">
                    </div>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label for="" class="form-label">Por Concepto de</label>
                <input type="text" name="byconcept" id="byconcept" class="form-control" tabindex="5">
            </div>
            <div class="mb-3 mt-3">
                <label for="" class="form-label">Descripción de Comprobante de Ingreso</label>
                <textarea class="form-control" name="description" aria-valuemax="{{$detProof->descripcion}}" id="description" tabindex="6"></textarea>
            </div>
            <input type="hidden" name="iddcomp" id="iddcomp" value="{{$detProof->iddcomp}}">
            <input type="hidden" name="idcli" id="iddfact" value="{{$customer->idcli}}">
            <input type="hidden" name="iddfact" id="iddfact" value="{{$detInvoice->iddfact}}">

            
           {{--  <div class="well pb-3 mt-3">
                <a href="{{route('searchIncome')}}">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Atras</button>
                </a>
                <button type="submit" class="btn btn-primary" id="submitForm">Aceptar</button>
            </div> --}}
            <div class="well pb-3">
                <a href="{{route('searchIncome')}}" class="btn btn-secondary" tabindex="5">Cancelar</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#seatmodal">Confirmar</button>
                {{-- <button type="submit" class="btn btn-primary" tabindex="6">Guardar</button>--}}
                <!-- Modal -->
            </div>
        
    </div>
</div>
<div class="modal fade" id="seatmodal" tabindex="-1" aria-labelledby="seatmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h5 class="modal-title" id="seatmodalLabel">Completar Asiento Contable Final</h5>
          {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <p>Cerrar</p>
          </button> --}}
        </div>
        <div class="modal-body">
            <div class="well">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <h3 class="text-center">Cuenta Debe</h3>
                          <div class="form-group">
                          <label for="groupaccount">Grupo de Cuenta</label>
                          <select name = 'groupaccount1' id ="groupaccount1" class="custom-select">
                              <option selected="">Seleccionar Grupo</option>
                          </select>
                          </div>
                          <div class="form-group">
                          <label for="subgroupaccount">Subgrupo de Cuenta</label>
                          <select name = 'subgroupaccount1' id ="subgroupaccount1" class="custom-select">
                              <option selected="">Seleccionar Subgrupo</option>
                          </select>
                          </div> 
                          <div class="form-group">
                          <label for="accountname">Nombre de Cuenta</label>
                          <select name = 'accountname1'id="accountname1" class="custom-select">
                              <option selected="">Seleccionar Cuenta</option>
                          </select>
                          </div>
                          <div class="form-group">
                          <label for="subaccountname">Nombre de Subcuenta</label>
                          <select name = 'subaccountname1'id="subaccountname1" class="custom-select">
                              <option selected="">Seleccionar Subcuenta</option>
                          </select>
                          <input type="hidden" name="subaccount_tipsubcta1" id="subaccount_tipsubcta1" value="">
                          <input type="hidden" name="subaccount_descripcion1" id="subaccount_tipsubcta1" value="">
                          </div>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6">
                        <h3 class="text-center">Cuenta Haber</h3>
                          <div class="form-group">
                          <label for="groupaccount">Grupo de Cuenta</label>
                          <select name = 'groupaccount2' id ="groupaccount2" class="custom-select">
                              <option selected="">Seleccionar Grupo</option>
                          </select>
                          </div>
                          <div class="form-group">
                          <label for="subgroupaccount">Subgrupo de Cuenta</label>
                          <select name = 'subgroupaccount2' id ="subgroupaccount2" class="custom-select">
                              <option selected="">Seleccionar Subgrupo</option>
                          </select>
                          </div> 
                          <div class="form-group">
                          <label for="accountname">Nombre de Cuenta</label>
                          <select name = 'accountname2'id="accountname2" class="custom-select">
                              <option selected="">Seleccionar Cuenta</option>
                          </select>
                          </div>
                          <div class="form-group">
                          <label for="subaccountname">Nombre de Subcuenta</label>
                          <select name = 'subaccountname2'id="subaccountname2" class="custom-select">
                              <option selected="">Seleccionar Subcuenta</option>
                          </select>
                          <input type="hidden" name="subaccount_tipsubcta2" id="subaccount_tipsubcta2" value="">
                          <input type="hidden" name="subaccount_descripcion2" id="subaccount_tipsubcta2" value="">
                          </div>
                    </div>
                </div>
            </div>
            {{-- <div class="well">
                <div class="row">
                    <div class="col-xs-3 col-sm-3 col-md-3">
                        <label for="" class="form-label">Codigo</label>
                        <input type="text" name="account1" id="account1" value="" readonly="readonly" class="form-control text-decoration-none">
                        <input type="text" name="account2" id="account2" value="" readonly="readonly" class="form-control text-decoration-none">
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-5">
                        <label for="" class="form-label">Nombre de cuenta</label>
                        <input type="text" name="nameaccount1" id="nameaccount1" value="" readonly="readonly" class="form-control text-decoration-none">
                        <input type="text" name="nameaccount2" id="nameaccount2" value="" readonly="readonly" class="form-control text-decoration-none">
                        
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <label for="" class="form-label">Debito</label>
                        <input type="text" name="debit1" id="debit1" value="" readonly="readonly" class="form-control text-decoration-none">
                        <input type="text" name="debit2" id="debit2" value="" readonly="readonly" class="form-control text-decoration-none">
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <label for="" class="form-label">Credito</label>
                        <input type="text" name="credit1" id="credit1" value="" readonly="readonly" class="form-control text-decoration-none">
                        <input type="text" name="credit2" id="credit2" value="" readonly="readonly" class="form-control text-decoration-none">
                    </div>
                </div>
            </div> --}}
            <div class="mb-3">
              <label for="recipient-name" class="col-form-label">Observacion</label>
              <input type="text" class="form-control" name="observation" id="observation">
            </div>
            <div class="mb-3">
              <label for="message-text" class="col-form-label">Descripcion</label>
              <textarea class="form-control" name="description" id="description"></textarea>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </div>
    </div>
</div>
</form>
@stop

@section('js')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script>
    var myModal = document.getElementById('myModal')
    var myInput = document.getElementById('myInput')

    myModal.addEventListener('shown.bs.modal', function () {
      myInput.focus()
    })
  </script>
  {{-- scripts js --}}
  <script>$( document ).ready(function() 
    {
        cargartipocuenta1()
        $( "#groupaccount1" ).change(function() /* el # busca el id del div html */
        {
            var groupaccount = $('#groupaccount1').val();
            $.ajax(
            {
              url: "/subgroupaccount1/"+groupaccount,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              dataType: 'json', // what to expect back from the server                                                                  
              data: {},
              processData: false,
              cache: false,
              contentType: false,
              type: 'post',
              success: function(data) 
              {
                  if (data)
                  {
                    var $subgroupaccount = $('#subgroupaccount1');
                    $subgroupaccount.empty();
                    var $accountname = $('#accountname1');
                    $accountname.empty();
                    $subgroupaccount.append('<option selected="">Seleccionar SubGrupo</option>')
                    data.forEach(element=>
                    {
                        $subgroupaccount.append('<option value=' + element.idsgr + '>' + element.descripcion + '</option>')
                    });
                  }
              }
            });
        });
        $( "#subgroupaccount1" ).change(function() 
        {
            var subgroupaccount = $('#subgroupaccount1').val();
            $.ajax(
            {
              url: "/accountname1/"+subgroupaccount,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              dataType: 'json', // what to expect back from the server                                                                  
              data: {},
              processData: false,
              cache: false,
              contentType: false,
              type: 'post',
              success: function(data) 
              {
                  if (data)
                  {
                    var $accountname = $('#accountname1');
                    $accountname.empty();
                    var $subaccountname = $('#subaccountname1');
                    $subaccountname.empty();
                    $accountname.append('<option selected="">Seleccionar Cuenta</option>')
                    data.forEach(element=>
                    {
                        $accountname.append('<option value=' + element.idgcu + '>' + element.descripcion + '</option>')
                    });
                  }
              }
            });
        });
        $( "#accountname1" ).change(function() /* el # busca el id del div html */
        {
            var accountname = $('#accountname1').val();
            $.ajax(
            {
              url: "/subaccountname1/"+accountname,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              dataType: 'json', // what to expect back from the server                                                                  
              data: {},
              processData: false,
              cache: false,
              contentType: false,
              type: 'post',
              success: function(data) 
              {
                  if (data)
                  {
                    var $subaccountname = $('#subaccountname1');
                    $subaccountname.empty();
                    $subaccountname.append('<option selected="">Seleccionar SubCuenta</option>')
                    data.forEach(element=>
                    {
                        $subaccountname.append('<option value=' + element.idscu + '>' + element.descripcion + '</option>')
                    });
  
                    var values = { 
                        tipsubcta: data[0].tipsubcta,
                        descripcion: data[0].descripcion
                    };

                    // Actualizar valores de los inputs
                    $('#subaccount_tipsubcta1').val(values.tipsubcta);
                    $('#subaccount_descripcion1').val(values.descripcion);
                  }
              }
            });
        });
    });
    function cargartipocuenta1()
    {
      var datas = new FormData();  
      $.ajax({
          url: "/groupaccount1",
          dataType: 'json', // what to expect back from the server                                                                  
          data: {},
          processData: false,
          cache: false,
          contentType: false,
          type: 'get',
          success: function(data) 
          {
              if (data) 
              {
                var $groupaccount = $('#groupaccount1');
                $groupaccount.empty();
                $groupaccount.append('<option selected="">Seleccionar Grupo</option>');
                data.forEach(element=>
                {
                    $groupaccount.append('<option value=' + element.idgru + '>' + element.descripcion + '</option>')
                });
              }
              else
              {
                
              }
              
          }
      });
  }
  </script>
  <script>$( document ).ready(function() 
    {
        cargartipocuenta2()
        $( "#groupaccount2" ).change(function() /* el # busca el id del div html */
        {
            var groupaccount = $('#groupaccount2').val();
            $.ajax(
            {
              url: "/subgroupaccount2/"+groupaccount,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              dataType: 'json', // what to expect back from the server                                                                  
              data: {},
              processData: false,
              cache: false,
              contentType: false,
              type: 'post',
              success: function(data) 
              {
                  if (data)
                  {
                    var $subgroupaccount = $('#subgroupaccount2');
                    $subgroupaccount.empty();
                    var $accountname = $('#accountname2');
                    $accountname.empty();
                    $subgroupaccount.append('<option selected="">Seleccionar SubGrupo</option>')
                    data.forEach(element=>
                    {
                        $subgroupaccount.append('<option value=' + element.idsgr + '>' + element.descripcion + '</option>')
                    });
                  }
              }
            });
        });
        $( "#subgroupaccount2" ).change(function() 
        {
            var subgroupaccount = $('#subgroupaccount2').val();
            $.ajax(
            {
              url: "/accountname2/"+subgroupaccount,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              dataType: 'json', // what to expect back from the server                                                                  
              data: {},
              processData: false,
              cache: false,
              contentType: false,
              type: 'post',
              success: function(data) 
              {
                  if (data)
                  {
                    var $accountname = $('#accountname2');
                    $accountname.empty();
                    var $subaccountname = $('#subaccountname2');
                    $subaccountname.empty();
                    $accountname.append('<option selected="">Seleccionar Cuenta</option>')
                    data.forEach(element=>
                    {
                        $accountname.append('<option value=' + element.idgcu + '>' + element.descripcion + '</option>')
                    });
                  }
              }
            });
        });
        $( "#accountname2" ).change(function() /* el # busca el id del div html */
        {
            var accountname = $('#accountname2').val();
            $.ajax(
            {
              url: "/subaccountname2/"+accountname,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              dataType: 'json', // what to expect back from the server                                                                  
              data: {},
              processData: false,
              cache: false,
              contentType: false,
              type: 'post',
              success: function(data) 
              {
                  if (data)
                  {
                    var $subaccountname = $('#subaccountname2');
                    $subaccountname.empty();
                    $subaccountname.append('<option selected="">Seleccionar SubCuenta</option>')
                    data.forEach(element=>
                    {
                        $subaccountname.append('<option value=' + element.idscu + '>' + element.descripcion + '</option>')
                    });
                    var values = { 
                        tipsubcta: data[0].tipsubcta,
                        descripcion: data[0].descripcion
                    };

                    // Actualizar valores de los inputs
                    $('#subaccount_tipsubcta2').val(values.tipsubcta);
                    $('#subaccount_descripcion2').val(values.descripcion);

                  }
              }
            });
        });
    });
    function cargartipocuenta2()
    {
      var datas = new FormData();  
      $.ajax({
          url: "/groupaccount2",
          dataType: 'json', // what to expect back from the server                                                                  
          data: {},
          processData: false,
          cache: false,
          contentType: false,
          type: 'get',
          success: function(data) 
          {
              if (data) 
              {
                var $groupaccount = $('#groupaccount2');
                $groupaccount.empty();
                $groupaccount.append('<option selected="">Seleccionar Grupo</option>');
                data.forEach(element=>
                {
                    $groupaccount.append('<option value=' + element.idgru + '>' + element.descripcion + '</option>')
                });
              }
              else
              {
                
              }
              
          }
      });
    }
  </script>
  <script>// Obtener los elementos select y input
    var subaccount_tipsubcta1 = document.getElementById("subaccount_tipsubcta1");
    var subaccount_descripcion1 = document.getElementById("subaccount_descripcion1");

    var subaccount_tipsubcta2 = document.getElementById("subaccount_tipsubcta2");
    var subaccount_descripcion2 = document.getElementById("subaccount_descripcion2");

    var account1 = document.getElementById("account1");
    var account1 = document.getElementById("account1");

    var nameaccount2 = document.getElementById("nameaccount2");
    var nameaccount2 = document.getElementById("nameaccount2");
    
    
    // Agregar eventos "change" a los select
    subaccount_tipsubcta1.addEventListener("change", updateInputs);
    subaccount_descripcion1.addEventListener("change", updateInputs);
    subaccount_tipsubcta2.addEventListener("change", updateInputs);
    subaccount_descripcion2.addEventListener("change", updateInputs);
    
    function updateInputs() {
        // Obtener los valores seleccionados en los select
        var subaccount_tipsubcta1Value = subaccount_tipsubcta1.value;
        var subaccount_descripcion1Value = subaccount_descripcion1.value;
        var subaccount_tipsubcta2Value = subaccount_tipsubcta2.value;
        var subaccount_descripcion2Value = subaccount_descripcion2.value;
    
        // Asignar los valores a los inputs correspondientes
        account1.value = subaccount_tipsubcta1Value;
        nameaccount1.value = subaccount_descripcion1Value;
        account2.value = subaccount_tipsubcta2Value;
        nameaccount2.value = subaccount_descripcion2Value;
    }
    </script>

  <script 
      src="http://code.jquery.com/jquery-3.3.1.min.js"
      integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
      crossorigin="anonymous">
  </script>
@stop