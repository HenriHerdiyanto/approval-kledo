<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/statuses",
     *     operationId="getStatusesList",
     *     tags={"Statuses"},
     *     summary="Get list of statuses",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Status"))
     *     )
     * )
     */
    public function index()
    {
        return Status::all();
    }

    /**
     * @OA\Post(
     *     path="/api/v1/statuses",
     *     operationId="storeStatus",
     *     tags={"Statuses"},
     *     summary="Store a new status",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="menunggu persetujuan")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Status"))
     * )
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        return Status::create($request->only('name'));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/statuses/{id}",
     *     operationId="getStatusById",
     *     tags={"Statuses"},
     *     summary="Get a specific status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Status")),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(Status $status)
    {
        return $status;
    }

    /**
     * @OA\Put(
     *     path="/api/v1/statuses/{id}",
     *     operationId="updateStatus",
     *     tags={"Statuses"},
     *     summary="Update a status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="disetujui")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/Status")),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(Request $request, Status $status)
    {
        $status->update($request->only('name'));
        return $status;
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/statuses/{id}",
     *     operationId="deleteStatus",
     *     tags={"Statuses"},
     *     summary="Delete a status",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="No Content"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(Status $status)
    {
        $status->delete();
        return response()->json(null, 204);
    }
}
