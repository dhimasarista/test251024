<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApprovalStageRequest;
use App\Http\Requests\UpdateApprovalStageRequest;
use App\Services\ApprovalStageService;
use Illuminate\Http\JsonResponse;

class ApprovalStageController extends Controller
{
    protected $approvalStageService;

    public function __construct(ApprovalStageService $approvalStageService)
    {
        $this->approvalStageService = $approvalStageService;
    }

   /**
     * @OA\Post(
     *     path="/approval-stages",
     *     tags={"Approval Stages"},
     *     summary="Tambah tahap approval",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="approver_id", type="int", description="tersedia di tabel approvers, unik satu sama lain"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Tahap approval berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreApprovalStageRequest $request): JsonResponse
    {
        $approvalStage = $this->approvalStageService->createApprovalStage($request->validated());
        return response()->json($approvalStage, 201);
    }

    /**
     * @OA\Patch(
     *     path="/approval-stages/{id}",
     *     tags={"Approval Stages"},
     *     summary="Ubah tahap approval",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID tahap approval",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="approver_id", type="int", description="tersedia di tabel approvers, unik satu sama lain"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Tahap approval berhasil diubah"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function update(UpdateApprovalStageRequest $request, $id): JsonResponse
    {
        $approvalStage = $this->approvalStageService->updateApprovalStage($id, $request->validated());
        return response()->json($approvalStage, 200);
    }
}
