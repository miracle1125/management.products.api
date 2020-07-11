<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Picklist\StoreRequest;
use App\Http\Resources\PicklistResource;
use App\Models\Picklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PicklistController extends Controller
{
    public function index(Request $request)
    {
        $query = Picklist::query()
            ->select([
                'picklists.*',
                'pick_location_inventory.shelve_location'
            ])
            ->leftJoin('inventory as pick_location_inventory', function ($join) {
                $join->on('pick_location_inventory.product_id', '=', 'picklists.product_id');
                $join->on('pick_location_inventory.location_id', '=', DB::raw(100));
            })
            ->where('quantity_to_pick','>',0)
            ->with('product')
            ->with('inventory')
            ->orderBy('pick_location_inventory.shelve_location');

        return $query->paginate(20);
    }

    public function store(StoreRequest $request, Picklist $picklist)
    {
        $picklist->update([
            'quantity_to_pick' => $picklist->quantity_to_pick - $request->input('quantity_picked')
        ]);

        return new PicklistResource($picklist);
    }
}