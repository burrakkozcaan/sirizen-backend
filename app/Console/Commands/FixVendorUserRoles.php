<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Vendor;
use App\UserRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Mevcut onaylı satıcıların kullanıcı rollerini düzeltir.
 *
 * Eğer satıcı aktif ama kullanıcı rolü CUSTOMER ise, VENDOR olarak günceller.
 */
class FixVendorUserRoles extends Command
{
    protected $signature = 'vendor:fix-roles {--dry-run : Gerçek işlem yapmadan simüle et}';

    protected $description = 'Mevcut onaylı satıcıların kullanıcı rollerini düzelt';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN modu - gerçek işlem yapılmayacak');
        }

        // Aktif vendor'ları bul, kullanıcı rolü CUSTOMER olanlar
        $vendors = Vendor::query()
            ->where('status', 'active')
            ->whereHas('user', function ($q) {
                $q->where('role', UserRole::CUSTOMER);
            })
            ->with('user')
            ->get();

        if ($vendors->isEmpty()) {
            $this->info('Düzeltilecek satıcı bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info("Düzeltilecek satıcı sayısı: {$vendors->count()}");

        $fixedCount = 0;

        foreach ($vendors as $vendor) {
            $user = $vendor->user;

            $this->line("  - {$vendor->name} (User: {$user->email}) - Rol: {$user->role->value} → vendor");

            if (!$dryRun) {
                $user->update(['role' => UserRole::VENDOR]);
                $fixedCount++;
            } else {
                $fixedCount++;
            }
        }

        $this->newLine();
        $this->info("Düzeltilen kullanıcı: {$fixedCount} adet");

        return Command::SUCCESS;
    }
}
