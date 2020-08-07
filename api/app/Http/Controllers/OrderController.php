<?php

namespace App\Http\Controllers;

use App\User;
use App\Order;
use App\Warehouse;
use App\Client;
use Illuminate\Http\Request;
use App\Http\Resources\OrderCollection;

class OrderController extends Controller
{
    
    /**
     * Display a listing of the items.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::where('secret_token', $request->secret_token)->first();
        if (is_null($user)) {
            return response()->json(['message' => 'Not authorized'], 401);
        } else {
            switch ($user->role) {
                case 'warehouse_manager':
                    $warehouse = Warehouse::where('manager_id', $user->id)->first();
                    return new OrderCollection(Order::where('warehouse_id', $warehouse->id)->paginate());
                  break;
                case 'client':
                    $client = Client::where('user_id', $user->id)->first();
                    return new OrderCollection(Order::where('client_id', $client->id)->paginate());
                  break;
            }
        }
    }
}
