# Cache Integration Guide

## Simple Integration Steps

Add caching to your API files in 3 steps:

### Step 1: Include Cache Class

At the top of your file, after config.php:

```php
require_once '../includes/functions/cache.php';
```

### Step 2: Try Cache First

After authentication checks:

```php
// Define a unique cache key
$cacheKey = 'my_api_data_' . $userId; // or any unique identifier

// Try to get from cache
$data = Cache::get($cacheKey);

if ($data !== null) {
    echo json_encode($data);
    exit;
}
```

### Step 3: Store After Query

Before outputting JSON:

```php
// Your data array
$response = [
    'success' => true,
    'data' => $result
];

// Store in cache (TTL in seconds)
Cache::set($cacheKey, $response, 900); // 15 minutes

echo json_encode($response);
```

## Example Files to Update

### get_analytics.php

- Cache key: `'analytics_dashboard'`
- TTL: 900 seconds (15 minutes)

### getAppointments.php

- Cache key: `'appointments_user_' . $userId`
- TTL: 300 seconds (5 minutes)

### getPackageDetails.php

- Cache key: `'package_' . $packageId`
- TTL: 1800 seconds (30 minutes)

## Cache Invalidation

When data changes, delete the cache:

```php
// In processBooking.php after successful booking
Cache::delete('appointments_user_' . $userId);
Cache::delete('analytics_dashboard');

// In updatePackage.php after package update
Cache::delete('package_' . $packageId);
```

## Manual Integration

If you prefer to integrate manually, here's a template:

```php
<?php
require_once 'config.php';
require_once 'cache.php';

// Auth checks...

$cacheKey = 'unique_key_here';
$cachedData = Cache::get($cacheKey);

if ($cachedData !== null) {
    header('Content-Type: application/json');
    echo json_encode($cachedData);
    exit;
}

// Your database queries...
$data = /* query results */;

// Store before output
Cache::set($cacheKey, $data, 600); // 10 minutes

header('Content-Type: application/json');
echo json_encode($data);
```

Done! Your API is now cached.
