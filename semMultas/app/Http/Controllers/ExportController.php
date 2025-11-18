<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exceptions\CustomException;

class ExportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function all(Request $request)
    {
        try{
            $searchParams = $request->all();

            $client = Client::with(['processes', 'processes.service'])
                ->where(function ($query) use ($searchParams) {
                    if (isset($searchParams['name'])) {
                        $query->where('name', 'like', '%' . $searchParams['name'] . '%');
                    }

                    if (isset($searchParams['cpf'])) {
                        $query->where('cpf', 'like', '%' . $searchParams['cpf'] . '%');
                    }

                    if (isset($searchParams['state'])) {
                        $query->where('state', 'like', '%' . $searchParams['state'] . '%');
                    }

                    if (isset($searchParams['city'])) {
                        $query->where('city', 'like', '%' . $searchParams['city'] . '%');
                    }
                })
                ->get();

                if ($client->isEmpty()) {
                    throw new CustomException('No data found');
                }
                
            return Excel::download(new ClientsExport($client), 'clients.xlsx');
        } catch (\Exception $e) {
            throw new CustomException($e->getMessage());
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
