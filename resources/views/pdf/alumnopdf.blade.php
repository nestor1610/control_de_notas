<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de notas</title>
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
                    <th colspan="4">Datos del alumno</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ 'Periodo: '.$alumno->periodo_inicio.'-'.$alumno->periodo_fin }}</td>
                    <td colspan="3">{{ 'Seccion: '.$alumno->nombre_seccion.' año: '.$alumno->ano }}</td>
                </tr>
                <tr>
                    <td>{{ 'Cedula: V-'.$alumno->cedula }}</td>
                    <td colspan="2">{{ 'Apellido: '.$alumno->apellido }}</td>
                    <td>{{ 'Nombre: '.$alumno->nombre }}</td>
                </tr>
                <tr>
                    <td colspan="2">{{ 'Telefono: '.$alumno->telefono }}</td>
                    <td colspan="2">{{ 'email: '.$alumno->email }}</td>
                </tr>
                <tr>
                    <td>{{ 'Ciudad: '.$alumno->dir_ciudad }}</td>
                    <td>{{ 'Avenida: '.$alumno->dir_avenida }}</td>
                    <td>{{ 'Calle: '.$alumno->dir_calle }}</td>
                    <td>{{ 'Casa: '.$alumno->dir_casa }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th colspan="5">Calificaciones</th>
                </tr>
                <tr>
                    <th>Asignatura</th>
                    <th>Lapso 1</th>
                    <th>Lapso 2</th>
                    <th>Lapso 3</th>
                    <th>Final</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notas as $nota)
                <tr>
                    <td>{{ $nota['asignatura'] }}</td>
                    <td>{{ $nota['primer_lapso'] }}</td>
                    <td>{{ $nota['segundo_lapso'] }}</td>
                    <td>{{ $nota['tercer_lapso'] }}</td>
                    <td>{{ $nota['promedio_asignatura'] }}</td>
                </tr>
                @endforeach                               
            </tbody>
        </table>
    </div>
</body>
</html>