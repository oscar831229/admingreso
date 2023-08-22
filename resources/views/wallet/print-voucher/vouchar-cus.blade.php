<html xmlns="http://www.w3.org/1999/xhtml"><head>
  <title>Billetera electrónica</title>
  <meta http-equiv="Content-type" content="text/html; charset=UTF-8">
  <style type="text/css">
	td, span, div, th, pre {
		font-family: 'Lucida Console', 'Courier New';
		font-size: 12px;
	}
	body {
		margin: 0px;
		margin-left:2px;
	}
  </style>
<body>
<br>
<br>
<div align="left" style="width:270px">
<center>
CAJA SANTANDEREANA DE SUBSIDIO FAMILIAR
<br>
REGIMEN COMUN
<br>
NIT No. : 890.200.106-1

<br>
<br>
BILLETERA ELECTRÓNICA CAJASAN
<br>
TRANSACCIÓN CUS No. {{ $movement->cus }}
<br>
CONSUMO EN ESTABLECIMIENTO
<br>	  
{{ $movement->store->name }}
<br>
Fecha: {{ $movement->created_at }}
<br>
Cajero: {{ $movement->user_code }}  
</center>
</div>
<br>
<table width="250" cellspacing="0" border="0" align="left">
	<tr>
		<td>BOLSILLO {{$movement->electrical_pocket->code}} - {{ $movement->electrical_pocket->name }}</td>
	</tr>
	<tr>
		<td>VALOR TRANSACCION: {{ $movement->value }}</td>
	</tr>
</table>
<br>
<br>
<table width="250" cellspacing="0" border="0" align="left">
	<tbody>
		<tr>
			<th>Ticket</th>
			<th>Valor</th>
		</tr>
		@foreach ($movement->statetickets as $ticket)
		<tr>
			<td>{{ $ticket->number_ticket }}</td>
			<td>{{ $ticket->value }}</td>
		</tr>	
		@endforeach
	</tbody>
</table>
<br><br><br>
<div align="left" style="width:270px">
Firma:---------------------
<br>
{{ $walleteuser->first_name.' '.$walleteuser->second_name.' '.$walleteuser->first_surname.' '.$walleteuser->second_surname }}
<br>
C.C. {{ $walleteuser->document_number }}</pre>
</div>

</body></html>