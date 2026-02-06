<?php

namespace App\Actions\Fortify;

use App\Models\Category;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'company_type' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:50'],
            'business_license_number' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'reference_code' => ['nullable', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $vendorSlug = $this->generateVendorSlug($input['name']);

            $this->ensureVendorIdSequence();

            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'password' => $input['password'],
            ]);

            $vendor = $user->vendor()->create([
                'name' => $input['name'],
                'slug' => $vendorSlug,
                'company_type' => $input['company_type'],
                'tax_number' => $input['tax_number'],
                'business_license_number' => $input['business_license_number'] ?? null,
                'iban' => $input['iban'] ?? null,
                'bank_name' => $input['bank_name'] ?? null,
                'account_holder_name' => $input['account_holder_name'] ?? null,
                'city' => $input['city'],
                'district' => $input['district'],
                'address' => $input['address'],
                'reference_code' => $input['reference_code'] ?? null,
                'status' => 'pending',
                'application_status' => 'pending',
                'application_submitted_at' => now(),
            ]);

            // Kategoriyi pivot tabloya ekle
            if (isset($input['category_id'])) {
                $category = Category::find($input['category_id']);
                if ($category) {
                    $vendor->categories()->attach($category->id);
                }
            }

            return $user;
        });
    }

    private function ensureVendorIdSequence(): void
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('vendors', 'id'), (SELECT COALESCE(MAX(id), 0) FROM vendors))"
            );
        } elseif ($driver === 'mysql') {
            $maxId = (int) DB::table('vendors')->max('id');
            $next = $maxId + 1;
            DB::statement("ALTER TABLE vendors AUTO_INCREMENT = {$next}");
        }
    }

    private function generateVendorSlug(string $name): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'vendor';
        }

        $slug = $baseSlug;
        $suffix = 2;

        while (Vendor::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
