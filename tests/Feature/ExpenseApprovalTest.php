<?php
namespace Tests\Feature;

use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_approval_flow()
    {
        // 1. Tambahin 3 approver pakai endpoint
        $approvers = ['Ana', 'Ani', 'Ina'];
        $approverIds = [];
        foreach ($approvers as $approverName) {
            $response = $this->postJson('/api/approvers', ['name' => $approverName]);
            $response->assertStatus(201);
            $approverIds[] = $response->json('id');
        }

        // 2. Buat status 'menunggu persetujuan' sama 'disetujui'
        Status::create(['name' => 'menunggu persetujuan']);
        Status::create(['name' => 'disetujui']);

        // 3. Bikin 3 tahap approval pakai ID approver yang udah kita buat
        foreach ($approverIds as $approverId) {
            $response = $this->postJson('/api/approval-stages', [
                'approver_id' => $approverId,
            ]);
            $response->assertStatus(201);
        }

        // 4. Buat 4 data pengeluaran lewat endpoint
        $expenses = [];
        for ($i = 1; $i <= 4; $i++) {
            $response = $this->postJson('/api/expenses', [
                'amount' => 100 * $i,
            ]);
            $response->assertStatus(201);
            $expenses[] = $response->json('id');
        }

        // 5. Proses approval buat tiap pengeluaran

        // 5.1 Pengeluaran Pertama - Disetujui Semua
        foreach ($approverIds as $approverId) {
            $this->patchJson("/api/expenses/{$expenses[0]}/approve", [
                'approver_id' => $approverId,
            ])->assertStatus(200);
        }
        // Pastikan status pengeluarannya jadi 'disetujui'
        $response = $this->getJson("/api/expenses/{$expenses[0]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'disetujui']
            ]);

        // 5.2 Pengeluaran Kedua - Disetujui 2 approver aja
        foreach (array_slice($approverIds, 0, 2) as $approverId) {
            $this->patchJson("/api/expenses/{$expenses[1]}/approve", [
                'approver_id' => $approverId,
            ])->assertStatus(200);
        }
        // Statusnya masih 'menunggu persetujuan' karena belum disetujui semua
        $response = $this->getJson("/api/expenses/{$expenses[1]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'menunggu persetujuan']
            ]);

        // 5.3 Pengeluaran Ketiga - Disetujui 1 approver aja
        $this->patchJson("/api/expenses/{$expenses[2]}/approve", [
            'approver_id' => $approverIds[0],
        ])->assertStatus(200);
        // Statusnya masih 'menunggu persetujuan'
        $response = $this->getJson("/api/expenses/{$expenses[2]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'menunggu persetujuan']
            ]);

        // 5.4 Pengeluaran Keempat - Belum ada yang approve
        // Statusnya otomatis masih 'menunggu persetujuan'
        $response = $this->getJson("/api/expenses/{$expenses[3]}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => ['name' => 'menunggu persetujuan']
            ]);
    }
}
