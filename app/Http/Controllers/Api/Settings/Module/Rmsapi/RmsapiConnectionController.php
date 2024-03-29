<?php

namespace App\Http\Controllers\Api\Settings\Module\Rmsapi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConfigurationRmsApiRequest;
use App\Http\Resources\RmsapiConnectionResource;
use App\Models\RmsapiConnection;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * Class RmsapiConnectionController
 * @package App\Http\Controllers\Api\Settings\Module\Rmsapi
 */
class RmsapiConnectionController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return RmsapiConnectionResource::collection(RmsapiConnection::all());
    }

    /**
     * @param StoreConfigurationRmsApiRequest $request
     * @return RmsapiConnectionResource
     */
    public function store(StoreConfigurationRmsApiRequest $request)
    {
        $config = new RmsapiConnection();
        $config->fill($request->only($config->getFillable()));
        $config->save();

        return new RmsapiConnectionResource($config);
    }

    /**
     * @param RmsapiConnection $rms_api_configuration
     * @return Application|ResponseFactory|Response
     * @throws \Exception
     */
    public function destroy(RmsapiConnection $rms_api_configuration)
    {
        $rms_api_configuration->delete();

        return response('ok');
    }
}
