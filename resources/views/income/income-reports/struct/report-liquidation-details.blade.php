@extends('layouts.informe.reportes')
@section('title', 'donantes')

@section('content')
    @php
        $stylecab = "text-align:center; font-weight: bold;font-size: 14px; vertical-align: middle; color:#f7fafc;";
        $stylebgp = "background-color: #80bf60;";
        $stylebgc = "background-color: #f3bb6b;";
        $stylebge = "background-color: #75a7d2;";
    @endphp
	<table border="1" cellspacing="0">
    <thead>
    		<tr>
        		<td colspan="8" style="text-align:center; font-weight: bold;font-size: 15px; height: 20px; vertical-align: middle;">
        			REPORTE DETALLADO LIQUIDACIONES FACTURADAS INGRESOS A SEDE
        		</td>
        	</tr>
            <tr>
                <td colspan="17">Fecha impresion: @php echo date('Y-m-d H:i:s'); @endphp</td>
            </tr>
            <tr>
                <td colspan="17">&nbsp;</td>
            </tr>
    </thead>
    <tbody>
        <tr>
            @foreach ($columns as $index => $column)
                <td>{{ $column }}</td>
            @endforeach
        </tr>
        @foreach ($data as $key => $row)
        <tr>
            @foreach ($row as $column)
                <td>{{ strip_tags($column)  }}</td>
            @endforeach
        </tr>
		@endforeach
	</table>
@endsection
