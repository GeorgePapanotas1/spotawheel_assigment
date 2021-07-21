<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Faker\Provider\ar_SA\Payment;

class ClientController extends Controller
{
    //

    public function getClients(){
        $clients = Client::with(['payments' => function ($q){
            $q->orderBy('created_at', 'DESC');
        }])
        ->get()
        ->map(function($cl){
            $cl->latest_payment = $cl->payments->first();
            unset($cl->payments);
            return $cl;
        });

        return view('clients', [
            'clients' => $clients
        ]);
    }

    public function test(){
        $clients = Client::whereHas('payments', function ($query) {
            return $query->where('created_at', '>', Carbon::now()->subDays(1500));
        })->with(['payments' => function ($q){
            $q->orderBy('created_at', 'DESC');
        }])
        ->get()
        ->map(function($cl){
            $cl->latest_payment = $cl->payments->first();
            unset($cl->payments);
            return $cl;
        });

        return ['clients'=>$clients];
    }
}
