<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notas por seccion</title>
    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-size: 0.875rem;
            font-weight: normal;
            line-height: 1.5;
            color: #151b1e;           
        }
        .table {
            display: table;
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
        }
        .table-bordered {
            border: 1px solid #c2cfd6;
        }
        thead {
            display: table-header-group;
            vertical-align: middle;
            border-color: inherit;
        }
        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        .table th, .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #c2cfd6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c2cfd6;
        }
        .table-bordered thead th, .table-bordered thead td {
            border-bottom-width: 2px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #c2cfd6;
        }
        th, td {
            display: table-cell;
            vertical-align: inherit;
        }
        th {
            font-weight: bold;
            text-align: -internal-center;
            text-align: left;
        }
        tbody {
            display: table-row-group;
            vertical-align: middle;
            border-color: inherit;
        }
        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .izquierda{
            float:left;
        }
        .derecha{
            float:right;
        }
    </style>
</head>
<body>
    <div>
        <h3>U.E.C.P "Andrés Bello"  <span class="derecha">{{date('Y/m/d')}}</span></h3>
    </div>
    <div>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th colspan="{{ count($asignaturas) + 1 }}">{{'Alumnos de la seccion: '.$seccion['nombre_seccion'].' Año: '.$seccion['ano'].' Periodo: '.$seccion['periodo']['periodo_inicio'].'-'.$seccion['periodo']['periodo_fin']}}</th>
                </tr>
                <tr>
                    @if ( count($asignaturas) )
                    	<th>Alumnos</th>
                    	@foreach ($asignaturas as $key => $asignatura)
                        	<th>{{ $key }}</th>
                    	@endforeach
                    @else
                    	<th>No hay asignaturas en esta seccion</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @if ( count($alumnos) > 0 )
                    @foreach ($alumnos as $alumno)
                        <tr>
                            <td>{{ $alumno['tipo_documento'].'-'.$alumno['cedula'].' '.$alumno['alumno'] }}</td>
                            @foreach ( $alumno['asignatura_notas'] as $key => $notas )
                            	<td>{{ 'Primer lapso: '.$notas[1].' Segundo lapso: '.$notas[2].' Tercer lapso: '.$notas[3].' Promedio: '.$notas['promedio'] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                       <td colspan="{{ count($asignaturas) + 1 }}">No hay alumnos con notas en esta seccion.</td> 
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>