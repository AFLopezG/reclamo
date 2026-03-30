<?php

namespace App\Http\Controllers;

use App\Models\Taxi;
use App\Http\Requests\StoreTaxiRequest;
use App\Http\Requests\UpdateTaxiRequest;

class TaxiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaxiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Taxi $taxi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Taxi $taxi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxiRequest $request, Taxi $taxi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Taxi $taxi)
    {
        //
    }
}
