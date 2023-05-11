$( document ).ready(function() 
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

                  var tipsubcta = data[0].tipsubcta;
                  var descripcion = data[0].descripcion;
                  $("#subaccount_tipsubcta1").val(tipsubcta);
                  $("#subaccount_descripcion1").val(descripcion);
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