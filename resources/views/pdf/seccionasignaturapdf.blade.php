<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Asignaturas de la seccion</title>
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
                    <th>Año: {{ $seccion->ano }}</th>
                    <th>Seccion: {{ $seccion->nombre_seccion }}</th>
                    <th>Periodo: {{ $seccion->periodo->periodo_inicio.'-'.$seccion->periodo->periodo_fin }}</th>
                </tr>
            </thead>
        </table>
    </div>
    <div>
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>Asignaturas</th>
                </tr>
            </thead>
            <tbody>
                @if ( count($seccion->asignaturas) )
                    @foreach ($seccion->asignaturas as $asignatura)
                        <tr>
                            <td>{{ $asignatura->nombre_asignatura }}</td>
                        </tr>
                    @endforeach
                        <tr>
                            <td>
                                Total asignaturas: {{ count($seccion->asignaturas) }}
                            </td>
                        </tr>
                @else
                    <tr>
                        <td>No hay asignaturas en esta seccion</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</body>
</html>