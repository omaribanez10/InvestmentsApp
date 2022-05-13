<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div style="margin-top:2rem;" class="container">
        
        <div style="width: 100%;" class="row">
        
          <div style="width: 100%; display:flex; justify-content: center;" class="col-12">
            <h3>XXXXXXXXXXXXXXXXXXXXXXXXXX</h3>
          </div>
          
        </div>
        <div style="width: 100%;" class="row">
          
          <div style="width: 100%; display:flex; justify-content: center;" class="col-12">
            <h4>C.C. XXXXXXXXX de Bucaramanga</h4>
          </div>
        
        </div>
        <div style="width: 100%;" class="row">
          
            <div style="width: 100%; display:flex; justify-content: center;" class="col-12">
                <h5>REGISTRO DE RENTABILIDAD: MES DE 2022</h5>
            </div>
          
          </div>
       
        <div class="row">
            <div class="col-4">
              Inversionista
            </div>
            <div class="col-8">
              Omar Yesid Ibanez Ortiz
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                Identificación
            </div>
            <div class="col-8">
              1098796215
            </div>
        </div>

        <table style="margin-top:5rem" class="table table-bordered">
            <thead>
              <tr> 
                <th scope="col">Fecha consignación</th>
                <th scope="col">Fecha inicio </th>
                <th scope="col">Número de pagaré</th>
                <th scope="col">Valor inversión</th>
                <th scope="col">Capital inicial mes</th>
                <th scope="col">Rentabilidad del mes</th>
                <th scope="col">Capital + rentabilidad</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($extracts as $item)
                
                <tr>
                  <th scope="row">{{$item['fecha_consignacion']}}</th>
                  <td>{{$item['fecha_inicio']}}</td>
                  <td>P-{{$item['numero_pagare']}}</td>
                  <td>{{$item['valor_inversion']}}</td>
                  <td>{{$item['capital_inicial_mes']}}</td>
                  <td>{{$item['investment_return']}}</td>
                  <td>{{intval($item['capital_inicial_mes']) + intval($item['investment_return'])}}</td>
                </tr>
              @endforeach
            
            </tbody>
        </table>

    </div>

</body>
</html>