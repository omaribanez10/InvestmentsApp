const crearInversion = ()=>{
    let tipo_inversion = $('#tipo_inversion').val()
    
    if(tipo_inversion!=''){
        switch (tipo_inversion) {
            
            case '1'://reinversion
                    crearReinversion()
                break;
            case '2'://Nueva inversion
                    crearNuevaInversion()
                break;
        
            default:
                break;
        }
    }
      
}

const crearReinversion = () =>{
    
    if($('#monto_reinversion').val() != '' && $('#monto_reinversion').is(':visible')){

        $('#btn_crear_inversion').text('Creando Reinversión...');
        $('#btn_crear_inversion').addClass('placeholder'); 
        $('#btn_crear_inversion').prop('disabled', true);

        let id_cliente = $('#id_cliente').val().trim();
        let numero_documento = $('#numero_documento').val().trim()
        let monto_inversion = quitarformatNumber(
            $('#monto_reinversion').val().trim()
        );
    
        var form_data = new FormData();
        form_data.append('id_customer', id_cliente);
        form_data.append('registered_by', 1);
        form_data.append('amount', monto_inversion);
        form_data.append('document_number', numero_documento);
        
        
        let url = document.location.origin + '/api/v1/reinvestment';
        let method = 'POST';

        Swal.fire({
            icon: 'warning',
            title: 'Asegurese que los datos ingresados sean los correctos.',
            html: `Por favor, revise que los datos ingresados, como el monto de la reinversión coincida con lo generado en extractos
            sean los correctos. <br/><br/>
                    El monto a invertir es: <b>$${formatNumber(
                        monto_inversion
                    )}</b><br> 
                    El cliente es: <b>${$('#nombre_cliente').val()}</b><br>
                    El correo es: <b>${$('#email_cliente').val()}</b><br> 
                   `,

            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            allowEscapeKey: false,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                enviarPeticion(url, method, form_data, 'continuarCrearReinversion');
            }
            if (result.isDismissed) {
                $('#btn_crear_inversion').text('Crear inversión');
                $('#btn_crear_inversion').prop('disabled', false);
                $('#btn_crear_inversion').removeClass('placeholder'); 
            }
        });

    }
}

const continuarCrearReinversion = (response) =>{


    setResponseMessage(response);
    $('#btn_crear_inversion').text('Crear Inversion');
    $('#btn_crear_inversion').prop('disabled', false);
    $('#btn_crear_inversion').removeClass('placeholder'); 

}




const crearNuevaInversion = ()=>{
    
    
    if(validarCrearInversion()){

        $('#btn_crear_inversion').text('Creando inversión...');
        $('#btn_crear_inversion').addClass('placeholder'); 
        $('#btn_crear_inversion').prop('disabled', true);

        let id_cliente = $('#id_cliente').val().trim();
        let tipo_moneda = $('#tipo_moneda').val().trim();
        let metodo_pago = $('#metodo_pago').val().trim();
        let base_monto_inversion = quitarformatNumber(
            $('#base_monto_inversion').val().trim()
        );
        let numero_documento = $('#numero_documento').val().trim()
        let monto_inversion = quitarformatNumber(
            $('#monto_inversion').val().trim()
        );
        let archivo_consignacion = document.getElementById(
            'archivo_consignacion'
        ).files[0];

        let tipo_inversion = $('#tipo_inversion').val();


        var form_data = new FormData();
        form_data.append('id_customer', id_cliente);
        form_data.append('code_currency', tipo_moneda);
        form_data.append('id_investment_type', tipo_inversion);
        form_data.append('id_payment_method', metodo_pago);
        form_data.append('base_amount', base_monto_inversion);
        form_data.append('amount', monto_inversion);
        form_data.append('consignment_file', archivo_consignacion);
        form_data.append('document_number', numero_documento);
    
        form_data.append('registered_by', 1);
        
        let url = document.location.origin + '/api/v1/investment';
        let method = 'POST';

        Swal.fire({
            icon: 'warning',
            title: 'Asegurese que los datos ingresados sean los correctos.',
            html: `Por favor, revise que los datos ingresados, como el monto, el medio de pago de la inversión 
            son los correctos <br/><br/>
                    El monto a invertir es: <b>$${formatNumber(
                        monto_inversion
                    )}</b><br> 
                    El cliente es: <b>${$('#nombre_cliente').val()}</b><br>
                    El correo es: <b>${$('#email_cliente').val()}</b><br> 
                   `,

            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            allowEscapeKey: false,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                enviarPeticion(url, method, form_data, 'continuarCrearInversion');
            }
            if (result.isDismissed) {
                $('#btn_crear_inversion').text('Crear Inversion');
                $('#btn_crear_inversion').prop('disabled', false);
                $('#btn_crear_inversion').removeClass('placeholder'); 
            }
        });
    }
}

const continuarCrearInversion = (response) => {
    setResponseMessage(response);
    $('#btn_crear_inversion').text('Crear Inversion');
    $('#btn_crear_inversion').prop('disabled', false);
    $('#btn_crear_inversion').removeClass('placeholder'); 
   
}

const validarCrearInversion = ()=>{
    let validador = true;
   
    if ($('#tipo_moneda').val() == '' && $('#tipo_moneda').is(':visible')) {
        agregarError('tipo_moneda');
        validador = false;
    } else {
        quitarError('tipo_moneda');
    }

    if ($('#metodo_pago').val() == '' && $('#metodo_pago').is(':visible')) {
        agregarError('metodo_pago');
        validador = false;
    } else {
        quitarError('metodo_pago');
    }
    if ($('#base_monto_inversion').val() == '' && $('#base_monto_inversion').is(':visible')) {
        agregarError('base_monto_inversion');
        validador = false;
    } else {
        quitarError('base_monto_inversion');
    }

    if ($('#archivo_consignacion').val().trim() == '' && $('#archivo_consignacion').is(':visible')) {
        agregarError('archivo_consignacion');
        validador = false;
    } else {
        quitarError('archivo_consignacion');
    }
    if (!validarMontoMinimo() && $('#monto_inversion').is(':visible')) {
        validador = false;
    }
    
    if($('#tipo_inversion').val() == ''){
        agregarError('tipo_inversion');
        validador = false;
    } else {
        quitarError('tipo_inversion');
    }

    return validador;
   

}

const buscarInversionesPorParametros = () => {
    quitarError('busqueda_inversiones');
    if($('#busqueda_inversiones').val().trim() != ''){
    
        form_data = {}
        let param = $('#busqueda_inversiones').val().trim();
        let url = document.location.origin + `/api/v1/investments_by_param/${param}`;
        let method = 'GET';
        enviarPeticion(url, method, form_data, 'continuarBuscarInversionesPorParametros');
        
    }else{
        agregarError('busqueda_inversiones');
    }
}

const continuarBuscarInversionesPorParametros = (response) => {
    let inversiones = response.data
    inversiones = inversiones == undefined || null ? {} : inversiones
    $('#investments_container').empty()
    let html=''
    if(!isObjEmpty(inversiones)){
        let tr_inversiones=''
       
        inversiones.forEach(function (inversion) {

            console.log(inversion)

            let url = document.location.origin + `/editar_inversion/${inversion.id}`
            tr_inversiones +=`
            <tr>
                <th scope="row"><a href="${url}">${inversion.id}</a></th>
                <td><a href="${url}">${inversion.amount}</a></td>
                <td><a href="${url}">${inversion.investment_date}</a></td>
                <td><a href="${url}">${inversion.customer.name} ${inversion.customer.lastname}</a></td>
                <td><a href="${url}">${inversion.investment_type}</a></td>

                
            </tr>`
        })

        html+=`<table class="table">
        <thead>
          <tr>
            <th scope="col">Id</th>
            <th scope="col">Monto</th>
            <th scope="col">Fecha</th>
            <th scope="col">Cliente</th>
            <th scope="col">Inversión</th>
          </tr>
        </thead>
        <tbody>
        `+ tr_inversiones +`
        </tbody>
      </table>`

    }else{
        html+=`<span>No se han encontrado inversiones para los parámetros ingresados.</span>` 
    }
    
    $('#investments_container').append(html)
}

const seleccionarTipoInversion = () =>{

    $('#div_inversion').css('display', 'none');
    $('#div_inversion_2').css('display', 'none');
    $('#div_reinversion').css('display', 'none');
    $('#btn_crear_inversion').prop('disabled', false);
    

    if($('#tipo_inversion').val() != '' && $('#tipo_inversion').val()=='2'){

        $('#div_inversion').css('display', 'flex');
        $('#div_inversion_2').css('display', 'flex');

    }else if($('#tipo_inversion').val()=='1'){
        $('#content_reinversion').removeClass('col-md-12');
        $('#div_reinversion').css('display', 'block');
        consultarExtractos()

    }

}

const consultarExtractos = ()=>{
    form_data = {}
    let param = $('#id_cliente').val().trim();
    let url = document.location.origin + `/api/v1/extracts/${param}`;
    let method = 'GET';
    enviarPeticion(url, method, form_data, 'continuarConsultarExtractos');
}

const continuarConsultarExtractos = (response)=>{
    let extractos = response.data
    extractos = extractos == undefined || null ? {} : extractos
   
    if(!isObjEmpty(extractos)){

        extractos.forEach(function (extracto) {

           let valor_reinversion = extracto.grand_total_invested + extracto.total_profitability
           $('#monto_reinversion').val(formatNumber(valor_reinversion))
          
        })

    }else{
        $('#btn_crear_inversion').prop('disabled', true);
        $('#content_reinversion').empty(); 
        $('#content_reinversion').removeClass('col-md-3');
        $('#content_reinversion').addClass('col-md-12');
        $('#content_reinversion').append('<span>Este cliente no tiene montos disponibles para realizar reinversiones, por favor primero genere el extracto del mes.</span>');
    }

}

const actualizarInversion = () =>{

    if(validarActualizarInversion()){

        $('#btn_editar_inversion').text('Actualizando inversion...');
        $('#btn_editar_inversion').addClass('placeholder'); 
        $('#btn_editar_inversion').prop('disabled', true);

        let id_cliente = $('#id_cliente').val().trim();
        let id_inversion = $('#id_inversion').val().trim();
        let numero_documento = $('#numero_documento').val().trim()
        let tipo_moneda = $('#tipo_moneda').val().trim();
        let metodo_pago = $('#metodo_pago').val().trim();
        let base_monto_inversion = quitarformatNumber(
            $('#base_monto_inversion').val().trim()
        );
        let monto_inversion = quitarformatNumber(
            $('#monto_inversion').val().trim()
        );

    
        let archivo_consignacion = document.getElementById(
            'archivo_consignacion'
        ).files[0] == undefined ? $('#archivo_consignacion_txt').val() : document.getElementById(
            'archivo_consignacion'
        ).files[0];

        let tipo_inversion = $('#tipo_inversion').val();

        var form_data = new FormData();
        form_data.append('id_customer', id_cliente);
        form_data.append('code_currency', tipo_moneda);
        form_data.append('id_investment_type', tipo_inversion);
        form_data.append('id_payment_method', metodo_pago);
        form_data.append('base_amount', base_monto_inversion);
        form_data.append('amount', monto_inversion);
        form_data.append('consignment_file', archivo_consignacion);
        form_data.append('document_number', numero_documento);        
        form_data.append('updated_by', 1);
        form_data.append('status', 1);
        
        let url = document.location.origin + `/api/v1/investment/update/${id_inversion}`;
        let method = 'POST';

        Swal.fire({
            icon: 'warning',
            title: 'Asegurese que los datos ingresados sean los correctos.',
            html: `Por favor, revise que los datos ingresados, como el monto, el medio de pago de la inversión 
            son los correctos <br/><br/>
                    El monto a invertir es: <b>$${formatNumber(
                        monto_inversion
                    )}</b><br> 
                    El cliente es: <b>${$('#nombre_cliente').val()}</b><br>
                    El correo es: <b>${$('#email_cliente').val()}</b><br> 
                `,

            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            allowEscapeKey: false,
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                enviarPeticion(url, method, form_data, 'continuarActualizarInversion');
            }
            if (result.isDismissed) {
                $('#btn_editar_inversion').text('Actualizar inversion');
                $('#btn_editar_inversion').prop('disabled', false);
                $('#btn_editar_inversion').removeClass('placeholder'); 
            }
        });
    }
}


const continuarActualizarInversion = (response) =>{
    $('#btn_editar_inversion').text('Actualizar inversion');
    $('#btn_editar_inversion').prop('disabled', false);
    $('#btn_editar_inversion').removeClass('placeholder'); 
    setResponseMessage(response);
}

const validarActualizarInversion = ()=>{

    let validador = true;
   
    if ($('#tipo_moneda').val() == '' && $('#tipo_moneda').is(':visible')) {
        agregarError('tipo_moneda');
        validador = false;
    } else {
        quitarError('tipo_moneda');
    }

    if ($('#metodo_pago').val() == '' && $('#metodo_pago').is(':visible')) {
        agregarError('metodo_pago');
        validador = false;
    } else {
        quitarError('metodo_pago');
    }
    if ($('#base_monto_inversion').val() == '' && $('#base_monto_inversion').is(':visible')) {
        agregarError('base_monto_inversion');
        validador = false;
    } else {
        quitarError('base_monto_inversion');
    }

    if ($('#archivo_consignacion').is(':visible') && 
        ($('#archivo_consignacion').val().trim() == '' && $('#archivo_consignacion_txt').val().trim() == '')
       ){
        agregarError('archivo_consignacion');
        validador = false;
    } else {
        quitarError('archivo_consignacion');
    }
    if (!validarMontoMinimo() && $('#monto_inversion').is(':visible')) {
        validador = false;
    }
    
    if($('#tipo_inversion').val() == ''){
        agregarError('tipo_inversion');
        validador = false;
    } else {
        quitarError('tipo_inversion');
    }

    return validador;
}