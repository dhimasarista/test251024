<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\ApproveExpenseRequest;
use App\Services\ExpenseService;
use Exception;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * @OA\Post(
     *     path="api/expenses",
     *     tags={"Expenses"},
     *     summary="Tambah pengeluaran",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="amount", type="number", description="Jumlah pengeluaran dengan minimal value 1"),
     *         )
     *     ),
     *     @OA\Response(response=201, description="Pengeluaran berhasil ditambahkan"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        try {
            $expense = $this->expenseService->createExpense($request->validated());
            return response()->json($expense, 201);
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="api/expenses/{id}/approve",
     *     tags={"Expenses"},
     *     summary="Setujui pengeluaran",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID pengeluaran",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="approved_by", type="integer", description="ID yang menyetujui pengeluaran"),
     *             @OA\Property(property="approval_note", type="string", description="Catatan approval")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pengeluaran berhasil disetujui"),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function approveExpense($id, ApproveExpenseRequest $request): JsonResponse
    {
        try {
            $expense = $this->expenseService->approveExpense($id, $request->validated());
            return response()->json($expense, 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="api/expenses/{id}",
     *     tags={"Expenses"},
     *     summary="Ambil pengeluaran",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID pengeluaran"),
     *     @OA\Response(response=200, description="Pengeluaran ditemukan"),
     *     @OA\Response(response=404, description="Pengeluaran tidak ditemukan")
     * )
     */
    public function show($id): JsonResponse
    {
        $expense = $this->expenseService->getExpense($id);
        return response()->json($expense, 200);
    }
}
