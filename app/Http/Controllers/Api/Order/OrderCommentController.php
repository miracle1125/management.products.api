<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderCommentStoreRequest;
use App\Http\Resources\OrderCommentResource;
use App\Models\OrderComment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class OrderCommentController
 * @package App\Http\Controllers\Api\Order
 *
 * @group Order
 */
class OrderCommentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        $query = OrderComment::getSpatieQueryBuilder();

        return $this->getPaginatedResult($query);
    }

    /**
     * @param OrderCommentStoreRequest $request
     * @return AnonymousResourceCollection
     */
    public function store(OrderCommentStoreRequest $request): AnonymousResourceCollection
    {
        $shipment = new OrderComment($request->validated());
        $shipment->user()->associate($request->user());
        $shipment->save();

        return OrderCommentResource::collection(collect([$shipment]));
    }
}
