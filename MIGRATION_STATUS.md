# Migration Status - Next.js Structure

## ✅ Completed
1. **users** - Updated with avatar, two_factor fields
2. **vendors** - Already created and updated
3. **vendor_tiers** - Completed
4. **categories** - Completed
5. **brands** - Completed
6. **products** - Completed with all fields
7. **product_images** - Completed
8. **product_variants** - Completed
9. **product_attributes** - Completed
10. **product_vendors** - Completed (multi-vendor pricing)
11. **addresses** - Completed
12. **carts** - Completed
13. **cart_items** - Completed
14. **orders** - Completed
15. **order_items** - Completed
16. **product_reviews** - Completed
17. **seller_reviews** - Completed
18. **product_questions** - Completed
19. **campaigns** - Completed
20. **product_campaigns** - Completed
21. **favorites** - Completed
22. **recently_vieweds** - Completed (recently_viewed in your spec)
23. **shipments** - Completed
24. **payments** - Completed
25. **commissions** - Completed
26. **vendor_balances** - Completed
27. **vendor_payouts** - Completed
28. **refunds** - Completed
29. **product_returns** - Completed
30. **vendor_performance_logs** - Completed
31. **search_logs** - Completed (search_history in your spec)
32. **activity_logs** - Completed
33. **disputes** - Completed

## 🔄 Need Updates (Just Created)
34. **user_roles** - Created, needs schema
35. **review_images** - Created, needs schema
36. **review_helpful_votes** - Created, needs schema
37. **product_answers** - Created, needs schema
38. **coupons** - Created, needs schema
39. **coupon_usages** - Created, needs schema
40. **wishlists** - Created, needs schema
41. **wishlist_items** - Created, needs schema
42. **price_alerts** - Created, needs schema
43. **stock_alerts** - Created, needs schema
44. **notification_settings** - Created, needs schema
45. **vendor_followers** - Created, needs schema
46. **hero_slides** - Created, needs schema
47. **blog_posts** - Created, needs schema
48. **static_pages** - Created, needs schema
49. **contact_messages** - Created, needs schema
50. **search_histories** - Created, needs schema

## ❌ Still Missing from Next.js Spec
- **return_images** (for returns)
- Notification table (generic notifications)

## Quick Commands to Run

```bash
# After all migrations are updated, run:
php artisan migrate:fresh

# Then seed with vendor tiers:
php artisan db:seed --class=VendorTierSeeder

# Format code:
vendor/bin/pint --dirty

# Create Filament resources:
php artisan make:filament-resource Product --generate
php artisan make:filament-resource Order --generate
php artisan make:filament-resource Vendor --generate
```

## Summary
- Total tables in Next.js spec: **40**
- Already completed from previous work: **33**
- New tables just created: **17**
- Need to add: **2** (return_images, generic notifications)

Next step: Update the 17 new migrations with proper schemas from your Next.js specification.
