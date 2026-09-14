<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Income\IcmCustomer;
use App\Models\Income\CommonCity;

class CustomerController extends Controller
{
    public function index()
    {
        $identification_document_types = getDetailDefinitions('identification_document_types');
        $genders = getDetailDefinitions('gender');
        $common_cities = CommonCity::orderBy('city_name')->get()->pluck('city_name', 'id');
        $tax_regime = ['49' => 'No responsables del IVA', '48' => 'Impuestos sobre la venta del IVA'];

        return view('income.customers.index', compact(
            'identification_document_types',
            'genders',
            'common_cities',
            'tax_regime'
        ));
    }

    public function datatableCustomers(Request $request)
    {
        $draw = (int)$request->input('draw', 0);
        $start = max((int)$request->input('start', 0), 0);
        $length = (int)$request->input('length', 25);

        if ($length <= 0) $length = 25;
        if ($length > 50) $length = 50;

        $filters = [
            'document_number' => trim((string)$request->input('document_number', '')),
            'name' => trim((string)$request->input('name', '')),
            'phone' => trim((string)$request->input('phone', '')),
            'email' => trim((string)$request->input('email', ''))
        ];

        $message = $this->validateCustomerFilters($filters);

        if ($message !== null) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'message' => $message
            ]);
        }

        $model = new IcmCustomer();

        if (!$model->hasSearchFilters($filters)) {
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $total = $model->getCustomersSearchCount($filters);
        $rows = $model->getCustomersSearch($filters, $start, $length);

        $data = [];
        $number = $start + 1;

        foreach ($rows as $row) {
            $data[] = [
                $row->id,
                $row->document_number,
                $row->name,
                $row->phone,
                $row->email,
                '',
                $number++
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);
    }

    protected function validateCustomerFilters(array $filters)
    {
        if (
            empty($filters['document_number']) &&
            empty($filters['name']) &&
            empty($filters['phone']) &&
            empty($filters['email'])
        ) {
            return null;
        }

        if (!empty($filters['name']) && mb_strlen($filters['name']) < 3) {
            return 'El nombre o apellido debe contener mínimo 3 caracteres.';
        }

        if (!empty($filters['phone']) && mb_strlen($filters['phone']) < 3) {
            return 'El teléfono debe contener mínimo 3 caracteres.';
        }

        if (!empty($filters['email']) && mb_strlen($filters['email']) < 3) {
            return 'El correo debe contener mínimo 3 caracteres.';
        }

        return null;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_type' => 'required',
            'id' => 'required',
            'first_name' => 'required',
            'first_surname' => 'required',
            'birthday_date' => 'required',
            'gender' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->messages(),
                'data' => []
            ]);
        }

        $data = $request->all();
        $user = auth()->user();

        if (isset($data['id']) && !empty($data['id'])) {
            IcmCustomer::find($data['id'])->update(array_merge($request->all(), ['user_updated' => $user->id]));
        } else {
            IcmCustomer::create(array_merge($request->all(), ['user_created' => $user->id]));
        }

        return response()->json([
            'success' => true,
            'message' => '',
            'data' => []
        ]);
    }

    public function show($id)
    {
        $ratetype = IcmCustomer::find($id);

        return response()->json([
            'success' => true,
            'message' => '',
            'ratetype' => $ratetype
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
