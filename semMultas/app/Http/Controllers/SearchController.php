<?php

namespace App\Http\Controllers;

use App\Utils\DefaultResponse;
use App\Exceptions\CustomException;
use App\Models\Client;
use App\Models\Process;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    protected $user;
    protected $model;
    protected $defaultResponse;

    public function __construct(DefaultResponse $defaultResponse)
    {
        $this->defaultResponse = $defaultResponse;
        $this->user = Auth::guard('api')->user();
    }

    public function search(Request $request){
        
        $query = $request->input('search');
        $results = [];

        if($this->user->hasPermission('Read client')){
            $results['clients'] = Client::where('name', 'like', "%$query%")
                ->orWhere('phone', 'like', "%$query%")
                ->orWhere('cpf', 'like', "%$query%")
                ->get();
        }

        if($this->user->hasPermission('Read process')){
            $results['processes'] = Process::where('ait', 'like', "%$query%")
                ->orWhere('ait', 'like', "%$query%")
                ->orWhere('plate', 'like', "%$query%")
                ->orWhere('chassis', 'like', "%$query%")
                ->orWhere('renavam', 'like', "%$query%")
                ->orWhere('process_number', 'like', "%$query%")
                ->orWhere('process_value', 'like', "%$query%")
                ->orWhere('observation', 'like', "%$query%")
                ->get();
        }

        if($this->user->hasPermission('Read service')){
            $results['services'] = Service::where('name', 'like', "%$query%")
                ->orWhere('name', 'like', "%$query%")
                ->get();
        }

        if($this->user->hasPermission('Read user')){
            $results['users'] = User::where('name', 'like', "%$query%")
                ->orWhere('name', 'like', "%$query%")
                ->orWhere('email', 'like', "%$query%")
                ->get();
        }


        return response()->json([
            'data' => $results
        ]);
    }

    private function setModel($model){
        $this->model = new $model();
        $this->globalRepository->setModel($this->model);
    }
}
