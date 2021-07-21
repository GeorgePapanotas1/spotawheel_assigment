<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Faker\Provider\ar_SA\Payment;

use function PHPUnit\Framework\isEmpty;

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

    public function getClientsInRange(Request $request){

        $from = $request['startDate'];
        $to = $request['endDate'];

        $clients = Client::whereHas('payments', function ($query) use ($from, $to) {
            return $query->whereBetween('payments.created_at', [$from, $to]);
        })
        ->with(['payments' => function ($q) use ($from, $to){
            $q->whereBetween('payments.created_at', [$from, $to]);
            $q->orderBy('payments.created_at', 'DESC');
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
