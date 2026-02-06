<?php

use App\Models\Category;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new vendors can register', function () {
    $category = Category::factory()->create(['is_active' => true]);

    $response = $this->post(route('register.store'), [
        'name' => 'Acme Ltd',
        'email' => 'vendor@example.com',
        'phone' => '+90 555 555 55 55',
        'category_id' => $category->id,
        'company_type' => 'limited',
        'tax_number' => '1234567890',
        'business_license_number' => 'BL123456',
        'iban' => 'TR330006100519786457841326',
        'bank_name' => 'Test Bankası',
        'account_holder_name' => 'Acme Ltd',
        'city' => 'İstanbul',
        'district' => 'Kadıköy',
        'address' => 'Test Adresi 123',
        'reference_code' => 'TRND-2026',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertGuest();
    $user = User::where('email', 'vendor@example.com')->firstOrFail();

    $this->assertDatabaseHas('vendors', [
        'user_id' => $user->id,
        'name' => 'Acme Ltd',
        'slug' => 'acme-ltd',
        'company_type' => 'limited',
        'tax_number' => '1234567890',
        'business_license_number' => 'BL123456',
        'iban' => 'TR330006100519786457841326',
        'bank_name' => 'Test Bankası',
        'account_holder_name' => 'Acme Ltd',
        'city' => 'İstanbul',
        'district' => 'Kadıköy',
        'address' => 'Test Adresi 123',
        'reference_code' => 'TRND-2026',
        'status' => 'pending',
    ]);

    // Kategori pivot tablosunda kontrol
    $vendor = $user->vendor;
    expect($vendor->categories)->toHaveCount(1);
    expect($vendor->categories->first()->id)->toBe($category->id);

    $response->assertRedirect(route('vendor.application.pending', absolute: false));
});

test('vendor registration requires application fields', function (string $missingField) {
    $category = Category::factory()->create(['is_active' => true]);

    $payload = [
        'name' => 'Acme Ltd',
        'email' => 'vendor@example.com',
        'phone' => '+90 555 555 55 55',
        'category_id' => $category->id,
        'company_type' => 'limited',
        'tax_number' => '1234567890',
        'city' => 'İstanbul',
        'district' => 'Kadıköy',
        'address' => 'Test Adresi 123',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    unset($payload[$missingField]);

    $response = $this->post(route('register.store'), $payload);

    $response->assertSessionHasErrors($missingField);
})->with([
    'phone' => 'phone',
    'category_id' => 'category_id',
    'company_type' => 'company_type',
    'tax_number' => 'tax_number',
    'city' => 'city',
    'district' => 'district',
    'address' => 'address',
]);
