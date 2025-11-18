<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProcessImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\customException;
use App\Utils\DefaultResponse;

class ImportDataController extends Controller
{
    protected $defaultResponse;

    public function __construct(DefaultResponse $defaultResponse)
    {
        $this->defaultResponse = $defaultResponse;
    }
    /**
     * Display a listing of the resource.
     */
    public function all(Request $request)
    {
        try{
            $request->validate([
                'file' => 'required|mimes:xlsx,xls',
            ]);

            Excel::import(new ProcessImport, $request->file('file'));

            return $this->defaultResponse->isSuccess('Data imported successfully', 200);

        } catch (\Exception $e) {
            throw new customException($e->getMessage(), 500);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
