<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactConsentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $adminUsers = User::where('role', 'admin')->get();

        // Contact Messages
        $contactSubjects = [
            'Sipariş hakkında bilgi almak istiyorum',
            'İade sürecim hakkında',
            'Kargo takibi yapamıyorum',
            'Ürün hakkında sorum var',
            'Satıcı olmak istiyorum',
            'Teknik destek gerekiyor',
            'Şikayetim var',
            'Öneri ve görüşlerim',
        ];

        for ($i = 0; $i < 20; $i++) {
            $isRead = fake()->boolean(60);
            $user = fake()->boolean(50) && $users->isNotEmpty() ? $users->random() : null;

            DB::table('contact_messages')->insert([
                'user_id' => $user?->id,
                'name' => $user?->name ?? fake()->name(),
                'email' => $user?->email ?? fake()->email(),
                'phone' => fake()->optional(0.6)->numerify('05#########'),
                'subject' => fake()->randomElement($contactSubjects),
                'message' => fake()->paragraphs(2, true),
                'is_read' => $isRead,
                'replied_at' => $isRead && fake()->boolean(70) ? fake()->dateTimeBetween('-7 days', 'now') : null,
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'updated_at' => now(),
            ]);
        }

        // User Consents
        $consentTypes = [
            'marketing_email' => 'Pazarlama e-postaları',
            'marketing_sms' => 'Pazarlama SMS',
            'push_notifications' => 'Push bildirimleri',
            'data_processing' => 'Veri işleme',
            'third_party_sharing' => '3. taraf paylaşım',
            'cookies' => 'Çerez kullanımı',
        ];

        foreach ($users as $user) {
            foreach ($consentTypes as $type => $description) {
                $isGranted = fake()->boolean(70);
                $grantedAt = $isGranted ? fake()->dateTimeBetween($user->created_at, 'now') : null;
                $revokedAt = $isGranted && fake()->boolean(10) ? fake()->dateTimeBetween($grantedAt, 'now') : null;

                DB::table('user_consents')->insert([
                    'user_id' => $user->id,
                    'consent_type' => $type,
                    'consent_version' => '1.0',
                    'is_granted' => $isGranted && ! $revokedAt,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'granted_at' => $grantedAt,
                    'revoked_at' => $revokedAt,
                    'created_at' => $user->created_at,
                    'updated_at' => now(),
                ]);
            }
        }

        // Data Deletion Requests
        if ($users->count() >= 3) {
            for ($i = 0; $i < 3; $i++) {
                $user = $users->random();
                $status = fake()->randomElement(['pending', 'processing', 'completed', 'rejected']);
                $processedBy = in_array($status, ['completed', 'rejected']) && $adminUsers->isNotEmpty()
                    ? $adminUsers->random()->id
                    : null;

                $requestedAt = fake()->dateTimeBetween('-14 days', 'now');
                $processedAt = $processedBy ? fake()->dateTimeBetween($requestedAt, 'now') : null;
                $completedAt = $status === 'completed' ? $processedAt : null;

                DB::table('data_deletion_requests')->insert([
                    'user_id' => $user->id,
                    'request_type' => fake()->randomElement(['full_deletion', 'data_export', 'account_anonymization']),
                    'status' => $status,
                    'reason' => fake()->randomElement([
                        'Artık hizmeti kullanmak istemiyorum',
                        'Başka bir platform tercih ediyorum',
                        'Kişisel verilerimin silinmesini istiyorum',
                    ]),
                    'admin_notes' => $processedBy ? fake()->optional(0.5)->sentence() : null,
                    'processed_by' => $processedBy,
                    'requested_at' => $requestedAt,
                    'processed_at' => $processedAt,
                    'completed_at' => $completedAt,
                    'created_at' => $requestedAt,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
