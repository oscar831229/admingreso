
<style type='text/css'>
	td, span, div, th, pre {
		font-family: 'Lucida Console', 'Courier New';
		font-size: 12px;
	}
	body {
		margin: 0px;
		margin-left:2px;
	}
</style>

<div style='pading: 0px; width:250px' align='right'>===================================</div>
<div style='pading: 0px; width:250px' align='center'>
<br>
{{  $configuration->company_name }}
<br>
NIT No. : {{ $configuration->identification_number }}
<br>
{{ $configuration->system_names }}
<br>
DIRECCION {{ $configuration->address }}
<br>
Tel. {{ $configuration->phone }}
<br><br>
COMPROBANTE COBERTURAS No. {{ $icm_liquidation->billing_prefix }}-{{ $icm_liquidation->consecutive_billing }}
<br>
Fecha: {{ $icm_liquidation->voucher_date }}
<br>
USUARIO: {{ $icm_liquidation->users->name ?? '' }}
</div>
<br>

<table width="250" cellspacing="0" border="0" align='left'>
    <tr>
		<th style="padding-right: 20px"># </th>
		<th>Servicio</th>
	</tr>
    @foreach ($icm_liquidation->icm_liquidation_services()->where(['is_deleted' => 0])->get() as $key => $service)
        <tr>
            <td align='center'>{{ ($key + 1) }}</td>
            <td>{{ $service->icm_income_items->name }}</td>

        </tr>
        @foreach ($service->icm_liquidation_details as $detail)
        <tr>
            <td align='center'></td>
            <td> * {{ $detail->getFullName() }}</td>
        </tr>
        @endforeach
    @endforeach



</table>

<br>
<br>
<br>
<br>
<br>
<div style='pading: 0px; width:250px' align='right'>===================================</div>

