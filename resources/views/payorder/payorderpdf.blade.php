<!DOCTYPE html>
<html>
<head>
    <style>
        body {

       
        margin-bottom:30px;
        
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    #img {
        position:absolute;
        left: 40px;
        top: 35px;
        bottom: 20px;

    }

    #title {
        position: relative;
        left: 150px;
    }
    #titlel {
        
        margin-left: 150px;
    }

    .nom-empresa {
        text-align:justify;
        margin-left: 10px;
        margin-right: 10px;

    }

    .border {
        border-bottom: 5px inset gray;
    }

    .datos {
        text-align: center;
        margin-top: 45px;
        font-size: 30px;
        background-color: rgb(223, 221, 221);
    }

    .derecho {
        position: absolute;
        top: 410px;
        right: 50px;
        margin-bottom: 5px;
    }

    .control {
        float: right;
        margin-top: 50px;
        margin-right: 40px;
    }

    div input {
        border: transparent;
        border-bottom: 1px inset gray;
        margin-bottom: 2px;


    }

    form {
        border: 5px inset gray;
        border-radius: 10px;
        margin-top: 10px;
    }

    .nombre {
        margin-left: 3px;
    }

    .cedula {
        margin-left: 3px;
    }
    .gris {
        background-color:rgb(223, 221, 221);
    }

    .table {
        width: 100%;
        border: 2px inset black;
        border-radius: 10px;
        margin-top: 4px;
    }

    .th, td {
    background-size: 24px ;
        
        border: 2px inset black;
    }

    .borde-total {
        float: right;
        margin-top: 50px;
        border: 2px inset grey;
        border-radius: 10px;
        padding: 15px;
        clear: right; /* Agrega esta línea */
    }

    #total {
    position: absolute;
    margin-left: 5.3%;
    }

    #IVA {
        position: absolute;
        margin-left: 2%;
    }

    .los-input {
    position: absolute;
    margin-top: 10%;
    padding-top: 18px;
    padding-bottom: 18px;
    padding-left: 10px;
    }
    .los-input {
        border: 3px inset grey;
        border-radius: 10px;
    }

    .formas {
        display: flex;
        justify-content: center;
        align-items: flex-end;
        margin-top: 30%;
    }

    .formas .los-input {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .formas p {
        margin: 0;
        text-align: center;
        flex: 1;
    }
    </style>
    <title>Factura</title>
</head>
<body>
      <div class="border">
            <h1 id="titlel">
      
                    <img src="data:image/png;base64,{{$image}}" height="100px" width="100px">  

                    FIX4U Solutions
            </h1>
             <div>
                <div class="control"><p>N-control: {{$payOrder->numctrl}}</p></div>
            </div>
            <div class="nom-empresa">

                <div class="direccion"><p>Direccion: {{$supplier->direccion}}</p></div>
                <div class="telefono"><p>Telefono: {{$supplier->telefono}}</p></div>
                <div class="correo"><p>Correo: {{$supplier->correo}}</p></div>
            </div>
     </div>
     <h3 class="datos">Orden de Pago</h3>
        <br>
     {{-- <div class="derecho">
       <p>Nro.: {{$payOrder->numfact}}</p> 
       <p>Fecha: {{$payOrder->fec_emi}} </p>
     </div>  --}}  
     <div class="formulario">
        <form >
           
                <p class="nombre">Nombre y apellido o Razón social: {{$supplier->nombre}}</p>
                {{-- <input class="nombre" name="nombre" value="{{$customer->nombre}}"> --}}
 
                @if ($supplier->tiprif != null)
                    <p class="cedula">C.I./RIF o pasaporte: {{$supplier->tipid}}-{{$supplier->identificacion}}-{{$supplier->tiprif}}</p>
                @else
                    <p class="cedula">C.I./RIF o pasaporte: {{$supplier->tipid}}-{{$supplier->identificacion}}</p>
                @endif
                
                {{-- <input class="cedula" name="cedula" value="{{$customer->tipid}}-{{$customer->identificacion}}-{{$customer->tiprif}}"> --}}
        </form>
     </div>
     <div>
        <br>
        <table class="table">
            <thead class="gris">
                <th>Cantidad</th>
                <th>Descripcion</th>
                <th>Precio Unitario</th>
                <th>Total</th>
            </thead>
            <tbody>
                @php
                    $countFiles = 1;
                @endphp
               
                    @foreach ($amountOrder as $order)
                    <tr>  
                    
                    <td>{{$countFiles}}</td>
                    <td>{{$order->descripcion}}</td>
                    <td>{{$order->montounitariolocal}}</td>
                    <td>{{$order->montobienlocal}}</td>
                    
                    

                    @php
                        $countFiles = $countFiles + 1;
                    @endphp
                    </tr>
                @endforeach
            </tbody>
        </table>
     </div>
     <div class="borde-total"> 
        <form >

            <p class="base">Base imponible: {{$detailOrder->baseimponiblelocal}}</p>
             


            <p class="IVA">IVA(16.00%): {{$detailOrder->montoivalocal}} </p>
           


            <p class="total">Total Factura: {{$detailOrder->montototallocal}} </p>


        </form>
     </div>
     <div class="formas">
        <div class="los-input">
            <p class="form-pago">Forma de pago: {{$payOrder->tippago}}</p>
            <p class="relining">Relación de Egreso: {{$payOrder->num_egre}}</p>
        </div>
    </div>
    
</body>
</html>