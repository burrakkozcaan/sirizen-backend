# 🚀 Laravel API Documentation for Next.js

Complete API endpoint documentation for the Next.js frontend marketplace application.

## 📋 Table of Contents

- [Authentication](#authentication)
- [Products](#products)
- [Categories](#categories)
- [Vendors (Stores)](#vendors-stores)
- [Cart](#cart)
- [Orders](#orders)
- [Favorites](#favorites)
- [Addresses](#addresses)
- [Reviews](#reviews)
- [Search](#search)
- [Setup Guide](#setup-guide)

---

## 🔐 Authentication

All protected endpoints require Laravel Sanctum authentication with `Bearer token`.

### Base URL
```
http://localhost:8000/api
```

### CORS Configuration

Already configured in `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=localhost:3000,127.0.0.1:3000
ALLOWED_ORIGINS=http://localhost:3000,http://127.0.0.1:3000
SESSION_DOMAIN=localhost
```

---

## 🔑 Auth Endpoints

### Register
```http
POST /api/auth/register
```

**Body:**
```json
{
  "name": "Ahmet Yılmaz",
  "email": "ahmet@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "05321234567"
}
```

**Response:**
```json
{
  "message": "Kayıt başarılı!",
  "user": {
    "id": 1,
    "name": "Ahmet Yılmaz",
    "email": "ahmet@example.com",
    "role": "customer"
  },
  "token": "1|abc123..."
}
```

---

### Login
```http
POST /api/auth/login
```

**Body:**
```json
{
  "email": "ahmet@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "message": "Giriş başarılı!",
  "user": {
    "id": 1,
    "name": "Ahmet Yılmaz",
    "email": "ahmet@example.com",
    "role": "customer"
  },
  "token": "2|xyz789..."
}
```

---

### Get Current User
```http
GET /api/auth/me
Authorization: Bearer {token}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "Ahmet Yılmaz",
    "email": "ahmet@example.com",
    "phone": "05321234567",
    "avatar": null,
    "role": "customer",
    "email_verified": false,
    "created_at": "2026-01-13T12:00:00.000000Z"
  }
}
```

---

### Logout
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Çıkış başarılı!"
}
```

---

## 📦 Products

### Get All Products
```http
GET /api/products
```

**Query Parameters:**
- `category_id` - Filter by category
- `brand_id` - Filter by brand
- `vendor_id` - Filter by vendor
- `min_price` - Minimum price
- `max_price` - Maximum price
- `is_bestseller` - Filter bestsellers (boolean)
- `is_new` - Filter new products (boolean)
- `has_free_shipping` - Filter free shipping (boolean)
- `sort_by` - Sort field: `created_at`, `price`, `rating`, `sales_count`, `view_count`
- `sort_order` - Sort direction: `asc`, `desc`
- `per_page` - Items per page (default: 24, max: 100)

**Example:**
```http
GET /api/products?category_id=5&sort_by=price&sort_order=asc&per_page=20
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Apple iPhone 15 Pro Max",
      "slug": "apple-iphone-15-pro-max",
      "price": "54999.00",
      "original_price": "59999.00",
      "discount_percentage": 8,
      "rating": "4.80",
      "review_count": 234,
      "is_bestseller": true,
      "is_new": true,
      "has_free_shipping": true,
      "images": [
        {
          "id": 1,
          "url": "https://example.com/image1.jpg",
          "is_primary": true
        }
      ],
      "vendor": {
        "id": 1,
        "name": "TechStore Official",
        "slug": "techstore-official",
        "is_official": true
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 156,
    "per_page": 24
  }
}
```

---

### Get Single Product
```http
GET /api/products/{slug}
```

**Example:**
```http
GET /api/products/apple-iphone-15-pro-max
```

**Response:**
```json
{
  "id": 1,
  "name": "Apple iPhone 15 Pro Max",
  "slug": "apple-iphone-15-pro-max",
  "description": "Full description...",
  "short_description": "Short description...",
  "price": "54999.00",
  "original_price": "59999.00",
  "discount_percentage": 8,
  "stock": 150,
  "sku": "IPH15PM256",
  "rating": "4.80",
  "review_count": 234,
  "question_count": 45,
  "view_count": 5678,
  "sales_count": 890,
  "specifications": {
    "Ekran": "6.7 inch",
    "İşlemci": "A17 Pro",
    "RAM": "8 GB"
  },
  "tags": ["iphone", "apple", "smartphone"],
  "images": [...],
  "variants": [...],
  "product_sellers": [
    {
      "id": 1,
      "vendor": {
        "name": "TechStore Official",
        "slug": "techstore-official"
      },
      "price": "54999.00",
      "stock": 50,
      "dispatch_days": 1,
      "shipping_type": "normal",
      "estimated_delivery_days": 3,
      "estimated_delivery_date": "2026-01-20",
      "district_extra_delivery_days": 2,
      "is_featured": true
    }
  ],
  "fastest_seller_id": 1,
  "district_extra_delivery_days": 2
}
```

---

### Get Product Reviews
```http
GET /api/products/{id}/reviews
```

**Query Parameters:**
- `per_page` - Items per page (default: 10, max: 50)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user": {
        "id": 5,
        "name": "Mehmet K.",
        "avatar": null
      },
      "rating": 5,
      "title": "Mükemmel telefon!",
      "comment": "Çok beğendim, herkese tavsiye ederim.",
      "verified_purchase": true,
      "images": [],
      "helpful_count": 12,
      "created_at": "2026-01-10T15:30:00Z"
    }
  ]
}
```

---

### Get Product Questions
```http
GET /api/products/{id}/questions
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "user": {
        "name": "Ali Y."
      },
      "question": "Türkiye garantisi var mı?",
      "answers": [
        {
          "id": 1,
          "answer": "Evet, 2 yıl Türkiye garantisi vardır.",
          "user": {
            "name": "Satıcı"
          },
          "created_at": "2026-01-11T10:00:00Z"
        }
      ],
      "created_at": "2026-01-10T18:00:00Z"
    }
  ]
}
```

---

## 📂 Categories

### Get All Categories
```http
GET /api/categories
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Elektronik",
    "slug": "elektronik",
    "icon": "🔌",
    "image": "https://example.com/electronics.jpg",
    "sort_order": 1,
    "children": [
      {
        "id": 2,
        "name": "Telefonlar",
        "slug": "telefonlar",
        "parent_id": 1
      }
    ]
  }
]
```

---

### Get Category Details
```http
GET /api/categories/{slug}
```

---

### Get Products in Category
```http
GET /api/categories/{slug}/products
```

**Query Parameters:** Same as `/api/products`

---

## 🏪 Vendors (Stores)

### Get All Vendors
```http
GET /api/vendors
```

**Query Parameters:**
- `is_official` - Filter official stores (boolean)
- `sort_by` - Sort by: `follower_count`, `rating`, `product_count`, `created_at`
- `per_page` - Items per page

---

### Get Vendor Details
```http
GET /api/vendors/{slug}
```

**Example:**
```http
GET /api/vendors/techstore-official
```

**Response:**
```json
{
  "id": 1,
  "name": "TechStore Official",
  "slug": "techstore-official",
  "logo": "https://example.com/logo.png",
  "banner": "https://example.com/banner.jpg",
  "description": "Official Apple store in Turkey",
  "rating": "4.85",
  "review_count": 1234,
  "follower_count": 45678,
  "product_count": 567,
  "is_official": true
}
```

---

### Get Vendor Products
```http
GET /api/vendors/{slug}/products
```

---

### Get Vendor Reviews
```http
GET /api/vendors/{id}/reviews
```

---

## 🛒 Cart (Protected)

**All cart endpoints require authentication.**

### Get Cart
```http
GET /api/cart
Authorization: Bearer {token}
```

**Response:**
```json
{
  "cart": {
    "id": 1,
    "user_id": 1,
    "items": [
      {
        "id": 1,
        "product": {
          "id": 1,
          "name": "iPhone 15 Pro",
          "images": [...]
        },
        "product_seller": {
          "vendor": {
            "name": "TechStore"
          },
          "dispatch_days": 1,
          "shipping_type": "normal"
        },
        "quantity": 2,
        "price": "54999.00"
      }
    ]
  },
  "total": "109998.00",
  "items_count": 2
}
```

---

### Add to Cart
```http
POST /api/cart/add
Authorization: Bearer {token}
```

**Body:**
```json
{
  "product_id": 1,
  "product_seller_id": 1,
  "quantity": 1
}
```

**Response:**
```json
{
  "message": "Ürün sepete eklendi.",
  "item": {...}
}
```

---

### Update Cart Item
```http
PUT /api/cart/{id}
Authorization: Bearer {token}
```

**Body:**
```json
{
  "quantity": 3
}
```

---

### Remove from Cart
```http
DELETE /api/cart/{id}
Authorization: Bearer {token}
```

---

### Clear Cart
```http
DELETE /api/cart
Authorization: Bearer {token}
```

---

## 📦 Orders (Protected)

### Get All Orders
```http
GET /api/orders
Authorization: Bearer {token}
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "order_number": "ORD-ABC123XYZ",
      "total_price": "109998.00",
      "status": "shipped",
      "payment_method": "credit_card",
      "items": [...],
      "created_at": "2026-01-12T10:00:00Z"
    }
  ]
}
```

---

### Get Order Details
```http
GET /api/orders/{id}
Authorization: Bearer {token}
```

---

### Create Order
```http
POST /api/orders
Authorization: Bearer {token}
```

**Body:**
```json
{
  "address_id": 1,
  "payment_method": "credit_card"
}
```

**Response:**
```json
{
  "message": "Siparişiniz oluşturuldu!",
  "order": {
    "id": 1,
    "order_number": "ORD-ABC123XYZ",
    "total_price": "109998.00",
    "status": "pending"
  }
}
```

---

### Track Order
```http
GET /api/orders/{id}/tracking
Authorization: Bearer {token}
```

---

## ❤️ Favorites (Protected)

### Get Favorites
```http
GET /api/favorites
Authorization: Bearer {token}
```

---

### Add to Favorites
```http
POST /api/favorites
Authorization: Bearer {token}
```

**Body:**
```json
{
  "product_id": 1
}
```

---

### Remove from Favorites
```http
DELETE /api/favorites/{productId}
Authorization: Bearer {token}
```

---

## 📍 Addresses (Protected)

### Get All Addresses
```http
GET /api/addresses
Authorization: Bearer {token}
```

---

### Create Address
```http
POST /api/addresses
Authorization: Bearer {token}
```

**Body:**
```json
{
  "title": "Ev",
  "full_name": "Ahmet Yılmaz",
  "phone": "05321234567",
  "address_line": "Atatürk Caddesi No:123 Daire:4",
  "city": "İstanbul",
  "state": "Kadıköy",
  "postal_code": "34000",
  "country": "Türkiye",
  "is_default": true
}
```

---

### Update Address
```http
PUT /api/addresses/{id}
Authorization: Bearer {token}
```

---

### Delete Address
```http
DELETE /api/addresses/{id}
Authorization: Bearer {token}
```

---

## ⭐ Reviews (Protected)

### Create Review
```http
POST /api/reviews
Authorization: Bearer {token}
```

**Body:**
```json
{
  "product_id": 1,
  "order_item_id": 1,
  "rating": 5,
  "title": "Harika ürün!",
  "comment": "Çok memnun kaldım."
}
```

---

### Update Review
```http
PUT /api/reviews/{id}
Authorization: Bearer {token}
```

---

### Delete Review
```http
DELETE /api/reviews/{id}
Authorization: Bearer {token}
```

---

## 🔍 Search

### Search Products
```http
GET /api/search?q=iphone
```

**Query Parameters:**
- `q` - Search query (min 2 characters)
- `sort_by` - Sort: `relevance`, `price`, `rating`, `sales_count`
- `sort_order` - `asc`, `desc`
- `per_page` - Items per page

**Response:**
```json
{
  "query": "iphone",
  "products": {
    "data": [...]
  }
}
```

---

## 🛠️ Setup Guide for Next.js

### 1. Create API Client

Create `src/lib/api.ts`:

```typescript
const API_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000/api';

export async function apiFetch<T>(
  path: string,
  init: RequestInit = {}
): Promise<T> {
  const token = localStorage.getItem('token'); // or cookies

  const res = await fetch(`${API_URL}${path}`, {
    ...init,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(token && { Authorization: `Bearer ${token}` }),
      ...(init.headers || {}),
    },
  });

  if (!res.ok) {
    const error = await res.text();
    throw new Error(error || `Request failed: ${res.status}`);
  }

  return res.json() as Promise<T>;
}
```

---

### 2. Auth Functions

Create `src/lib/auth.ts`:

```typescript
import { apiFetch } from './api';

export async function login(email: string, password: string) {
  const data = await apiFetch<{ user: any; token: string }>('/auth/login', {
    method: 'POST',
    body: JSON.stringify({ email, password }),
  });

  localStorage.setItem('token', data.token);
  return data;
}

export async function register(userData: any) {
  const data = await apiFetch<{ user: any; token: string }>('/auth/register', {
    method: 'POST',
    body: JSON.stringify(userData),
  });

  localStorage.setItem('token', data.token);
  return data;
}

export async function logout() {
  await apiFetch('/auth/logout', { method: 'POST' });
  localStorage.removeItem('token');
}

export async function me() {
  return apiFetch<{ user: any }>('/auth/me');
}
```

---

### 3. React Query Setup

Create `src/hooks/useProducts.ts`:

```typescript
import { useQuery } from '@tanstack/react-query';
import { apiFetch } from '@/lib/api';

export function useProducts(params?: any) {
  return useQuery({
    queryKey: ['products', params],
    queryFn: () => {
      const query = new URLSearchParams(params).toString();
      return apiFetch(`/products?${query}`);
    },
  });
}

export function useProduct(slug: string) {
  return useQuery({
    queryKey: ['product', slug],
    queryFn: () => apiFetch(`/products/${slug}`),
  });
}
```

---

### 4. Example Usage in Component

```typescript
'use client';

import { useProducts } from '@/hooks/useProducts';

export default function ProductsPage() {
  const { data, isLoading, error } = useProducts({
    category_id: 5,
    sort_by: 'price',
    per_page: 24,
  });

  if (isLoading) return <div>Yükleniyor...</div>;
  if (error) return <div>Hata: {error.message}</div>;

  return (
    <div className="grid grid-cols-4 gap-4">
      {data.data.map((product) => (
        <ProductCard key={product.id} product={product} />
      ))}
    </div>
  );
}
```

---

## 🎯 Environment Variables

Add to your Next.js `.env.local`:

```env
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

---

## ✅ Testing Endpoints

You can test all endpoints using:
- **Postman** - Import the endpoints
- **Thunder Client** (VS Code extension)
- **curl** commands

Example:
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@sirizen.com","password":"password"}'
```

---

## 🚀 Quick Start Checklist

- [x] Laravel Sanctum configured
- [x] CORS configured for Next.js
- [x] All API routes created
- [x] Controllers implemented
- [x] Code formatted with Pint
- [ ] Start Laravel server: `php artisan serve`
- [ ] Create Next.js API client
- [ ] Set up React Query
- [ ] Test authentication flow
- [ ] Build product listing page

---

## 📞 Support

If you need any clarification or additional endpoints, feel free to ask!

**Admin Panel:** http://localhost:8000/admin
**API Base URL:** http://localhost:8000/api

Happy coding! 🎉
