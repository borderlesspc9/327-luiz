<?php

namespace App\Http\Controllers;

use App\Models\ProcessField;
use Illuminate\Http\Request;
use App\Http\Requests\IndexRequest;

class ProcessFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request)
    {
        try{
            $searchParams = $request->validated();
            $columns = ['id', 'name', 'type', 'mask', 'required', 'description'];

            $processFields = ProcessField::all($columns);

            return response()->json($processFields);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProcessField $processField)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProcessField $processField)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProcessField $processField)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProcessField $processField)
    {
        //
    }
}
