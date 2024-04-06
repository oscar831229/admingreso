<?php

namespace App\Http\Controllers\income;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Income\IcmAffiliateCategory;
use App\Models\Income\IcmFamilyCompensationFund;

class BillingIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user = auth()->user();

        # Ambientes ingreso.
        $icm_environments = $user->icm_environments()->get();

        # Tipos documento identificación
        $identification_document_types = getDetailDefinitions('identification_document_types');

        # Tipos de ingreso
        $types_of_income   = getDetailDefinitions('types_of_income');

        # Cajas de compensación
        $icm_family_compensation_funds = IcmFamilyCompensationFund::where(['state' => 'A'])->get()->pluck('name', 'id');

        # Categoria
        $icm_affiliate_categories = IcmAffiliateCategory::where(['state' => 'A'])->get()->pluck('name', 'id');

        return view('income.billing-incomes.index', compact('icm_environments', 'identification_document_types', 'types_of_income', 'icm_affiliate_categories', 'icm_family_compensation_funds'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
