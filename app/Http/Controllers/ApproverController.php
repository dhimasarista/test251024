<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApproverRequest;
use App\Models\Approver;
use App\Services\ApprovalStageService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class ApproverController extends Controller
{
    /**
     * @OA\Post(
     *     path="api/approvers",
     *     summary="Tambah approver",
     *     tags={"Approvers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Ana")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Approver ditambahkan",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Ana"),
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     * )
     */
    public function store(StoreApproverRequest $request): JsonResponse
    {
        try {
            $approver = Approver::create($request->validated());
            return response()->json($approver, 201);
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
