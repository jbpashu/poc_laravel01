<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ItemCollection;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\DB;
use App\Item;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($this->checkAccount($request)) {
            return new ItemCollection(Item::on($request->account)->paginate());
        } else {
            return response()->json([
                'message' => 'Account not Found'], 404);
        }
    }

    /**
     * Store a newly created item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->checkAccount($request)) {
            $item = new Item;
            // Setting DB connection
            $item->setConnection($request->account);
            $item->name = $request->name;
            $item->description = $request->description;
            $item->save();
            
            return new ItemResource($item);
        } else {
            return response()->json([
                'message' => 'Account not Found'], 404);
        }
    }

    private function checkAccount($request) {

        if (config()->has('database.connections.' . $request->account)) {
            return true;
        }

        return false;
    }
}
