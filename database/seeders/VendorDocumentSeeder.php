<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();
        $adminUsers = User::where('role', 'admin')->get();

        if ($vendors->isEmpty()) {
            return;
        }

        $documentTypes = [
            'tax_certificate' => ['name' => 'Vergi Levhası', 'ext' => 'pdf'],
            'trade_registry' => ['name' => 'Ticaret Sicil Gazetesi', 'ext' => 'pdf'],
            'signature_circular' => ['name' => 'İmza Sirküleri', 'ext' => 'pdf'],
            'identity_card' => ['name' => 'Kimlik Fotokopisi', 'ext' => 'jpg'],
            'bank_statement' => ['name' => 'Banka Hesap Bilgisi', 'ext' => 'pdf'],
            'activity_certificate' => ['name' => 'Faaliyet Belgesi', 'ext' => 'pdf'],
        ];

        foreach ($vendors as $vendor) {
            foreach ($documentTypes as $type => $info) {
                $status = fake()->randomElement(['pending', 'approved', 'rejected']);
                $verifiedBy = $status !== 'pending' && $adminUsers->isNotEmpty()
                    ? $adminUsers->random()->id
                    : null;

                DB::table('vendor_documents')->insert([
                    'vendor_id' => $vendor->id,
                    'document_type' => $type,
                    'file_path' => 'vendor-documents/' . $vendor->id . '/' . $type . '.' . $info['ext'],
                    'file_name' => $info['name'] . '.' . $info['ext'],
                    'mime_type' => $info['ext'] === 'pdf' ? 'application/pdf' : 'image/jpeg',
                    'file_size' => fake()->numberBetween(50000, 2000000),
                    'status' => $status,
                    'rejection_reason' => $status === 'rejected' ? 'Belge okunamıyor veya eksik' : null,
                    'verified_by' => $verifiedBy,
                    'verified_at' => $verifiedBy ? fake()->dateTimeBetween('-30 days', 'now') : null,
                    'notes' => fake()->optional(0.3)->sentence(),
                    'created_at' => fake()->dateTimeBetween('-60 days', '-30 days'),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
