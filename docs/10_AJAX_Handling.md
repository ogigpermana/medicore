# AJAX Handling with Enhanced Fetch API

**Project:** MediCore - Pharmacy Management System  
**Approach:** Enhanced Fetch API with Axios-like Features  
**Date:** August 16, 2026

## 1. Technology Choice: Enhanced Fetch API

### Why Enhanced Fetch API over Axios?

**Benefits for MediCore:**
- <i class="fas fa-check-circle"></i> **No Dependencies:** Sesuai dengan Modern Vanilla JS philosophy
- <i class="fas fa-check-circle"></i> **Full Control:** Complete control over request/response handling
- <i class="fas fa-check-circle"></i> **Performance:** Lebih ringan dari Axios (~15KB saved)
- <i class="fas fa-check-circle"></i> **Modern:** Native browser API, future-proof
- <i class="fas fa-check-circle"></i> **Learning Value:** Menunjukkan understanding native web APIs
- <i class="fas fa-check-circle"></i> **Custom Features:** Bisa add features sesuai kebutuhan

**Axios-like Features Implemented:**
- Request/Response interceptors
- Request cancellation (AbortController)
- Automatic retries
- Timeout handling
- File upload with progress
- Concurrent requests (all/race)
- Axios-like response structure

## 2. Enhanced API Client Features

### 2.1 Core Methods

```javascript
import { api } from './modules/api.js';

// GET request
const products = await api.get('/products');
const product = await api.get('/products/1');
const searchResults = await api.get('/products/search', { q: 'paracetamol' });

// POST request
const newProduct = await api.post('/products', {
  name: 'Paracetamol',
  price: 5000,
  stock: 100
});

// PUT request
const updated = await api.put('/products/1', {
  name: 'Paracetamol 500mg',
  price: 5500
});

// PATCH request
const partialUpdate = await api.patch('/products/1', {
  price: 6000
});

// DELETE request
await api.delete('/products/1');
```

### 2.2 Advanced Features

#### Request Interceptors (Like Axios)

```javascript
// Add authentication token to all requests
api.addRequestInterceptor(async (config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`;
  }
  return config;
});

// Add logging
api.addRequestInterceptor(async (config) => {
  console.log('API Request:', config.method, config.url || config);
  return config;
});
```

#### Response Interceptors

```javascript
// Handle global errors
api.addResponseInterceptor(async (response) => {
  if (response.status === 401) {
    // Redirect to login
    window.location.href = '/login';
  }
  if (response.status >= 500) {
    // Show server error notification
    showServerError();
  }
  return response;
});

// Transform response data
api.addResponseInterceptor(async (response) => {
  // Add timestamp to all responses
  response.data._timestamp = new Date().toISOString();
  return response;
});
```

#### Request Cancellation

```javascript
// Cancel specific request
api.cancelRequest('search_products');

// Cancel all pending requests
api.cancelAllRequests();

// Cancel request after delay
const searchRequest = async () => {
  return await api.get('/products/search', { q: query }, {
    requestKey: 'search_products'
  });
};

// Cancel if user navigates away
window.addEventListener('beforeunload', () => {
  api.cancelAllRequests();
});
```

#### Request with Timeout

```javascript
// Timeout after 5 seconds
try {
  const result = await api.withTimeout('/products/slow-endpoint', {}, 5000);
} catch (error) {
  if (error.message === 'Request timeout') {
    console.log('Request took too long');
  }
}
```

#### Automatic Retry

```javascript
// Retry failed request up to 3 times
const result = await api.retry(
  () => api.get('/unstable-endpoint'),
  3,      // max retries
  1000    // delay between retries (ms)
);
```

#### File Upload with Progress

```javascript
// Upload file with progress tracking
const fileInput = document.getElementById('file-input');
const file = fileInput.files[0];

try {
  const result = await api.upload(
    '/api/products/upload',
    file,
    (percent, loaded, total) => {
      // Progress callback
      console.log(`Upload: ${percent.toFixed(2)}%`);
      console.log(`${loaded} / ${total} bytes`);
    },
    {
      requestKey: 'product_upload',
      formData: {
        product_id: 123,
        category: 'images'
      }
    }
  );
  console.log('Upload successful:', result);
} catch (error) {
  console.error('Upload failed:', error);
}
```

#### Concurrent Requests

```javascript
// Execute multiple requests concurrently
const [products, categories, suppliers] = await api.all([
  api.get('/products'),
  api.get('/categories'),
  api.get('/suppliers')
]);

// Race between requests (first to complete wins)
const result = await api.race([
  api.get('/api/fast'),
  api.get('/api/slow')
]);
```

## 3. Usage Examples in MediCore

### 3.1 Product Management

```javascript
// Fetch all products
async function loadProducts() {
  try {
    const products = await api.get('/products');
    renderProductTable(products);
  } catch (error) {
    showError('Failed to load products');
  }
}

// Create new product
async function createProduct(productData) {
  try {
    const newProduct = await api.post('/products', productData);
    showSuccess('Product created successfully');
    return newProduct;
  } catch (error) {
    showError('Failed to create product');
    throw error;
  }
}

// Update product
async function updateProduct(id, updates) {
  try {
    const updated = await api.put(`/products/${id}`, updates);
    showSuccess('Product updated successfully');
    return updated;
  } catch (error) {
    showError('Failed to update product');
    throw error;
  }
}

// Delete product
async function deleteProduct(id) {
  if (!confirm('Are you sure you want to delete this product?')) {
    return;
  }

  try {
    await api.delete(`/products/${id}`);
    showSuccess('Product deleted successfully');
    loadProducts(); // Reload list
  } catch (error) {
    showError('Failed to delete product');
  }
}
```

### 3.2 Shopping Cart Operations

```javascript
// Add product to cart via API
async function addToCartViaAPI(productId, quantity) {
  try {
    const result = await api.post('/cart/add', {
      product_id: productId,
      quantity: quantity
    });
    
    // Update local cart
    cart.addItem(result.product, quantity);
    showSuccess('Added to cart');
  } catch (error) {
    showError('Failed to add to cart');
  }
}

// Sync cart with server
async function syncCart() {
  const cartItems = cart.getItems();
  
  try {
    const result = await api.post('/cart/sync', {
      items: cartItems
    });
    
    // Update server cart ID
    if (result.cart_id) {
      localStorage.setItem('server_cart_id', result.cart_id);
    }
  } catch (error) {
    console.error('Failed to sync cart:', error);
  }
}
```

### 3.3 Search with Debounce

```javascript
let searchTimeout;

async function searchProducts(query) {
  // Cancel previous search request
  api.cancelRequest('product_search');
  
  // Clear previous timeout
  clearTimeout(searchTimeout);
  
  // Set new timeout
  searchTimeout = setTimeout(async () => {
    try {
      const results = await api.get('/products/search', { q: query }, {
        requestKey: 'product_search'
      });
      displaySearchResults(results);
    } catch (error) {
      if (error.message !== 'Request cancelled') {
        showError('Search failed');
      }
    }
  }, 300); // 300ms debounce
}
```

### 3.4 Real-time Stock Updates

```javascript
// Poll for stock updates
let stockUpdateInterval;

function startStockUpdates() {
  stockUpdateInterval = setInterval(async () => {
    try {
      const stockData = await api.get('/products/stock-updates');
      updateStockDisplay(stockData);
    } catch (error) {
      console.error('Failed to fetch stock updates:', error);
    }
  }, 30000); // Every 30 seconds
}

function stopStockUpdates() {
  clearInterval(stockUpdateInterval);
  api.cancelAllRequests();
}
```

### 3.5 Report Generation

```javascript
// Generate PDF report
async function generateReport(type, filters) {
  try {
    const result = await api.post(`/reports/${type}`, filters, {
      requestKey: `report_${type}`
    });
    
    // Download PDF
    if (result.pdf_url) {
      window.open(result.pdf_url, '_blank');
    }
    
    showSuccess('Report generated successfully');
  } catch (error) {
    showError('Failed to generate report');
  }
}

// Generate report with timeout
async function generateReportWithTimeout(type, filters) {
  try {
    const result = await api.withTimeout(
      `/reports/${type}`,
      { method: 'POST', body: JSON.stringify(filters) },
      10000 // 10 second timeout
    );
    
    return result;
  } catch (error) {
    if (error.message === 'Request timeout') {
      showError('Report generation timed out. Please try again.');
    } else {
      showError('Failed to generate report');
    }
    throw error;
  }
}
```

## 4. Error Handling Patterns

### 4.1 Global Error Handler

```javascript
// Add global error interceptor
api.addResponseInterceptor(async (response) => {
  if (response.status >= 400) {
    handleApiError(response);
  }
  return response;
});

function handleApiError(response) {
  const errorMap = {
    400: 'Invalid request data',
    401: 'Authentication required',
    403: 'Permission denied',
    404: 'Resource not found',
    422: 'Validation error',
    429: 'Too many requests',
    500: 'Server error',
    503: 'Service unavailable'
  };

  const message = errorMap[response.status] || 'An error occurred';
  const details = response.data?.message || response.data?.error;

  showError(`${message}: ${details}`);
}
```

### 4.2 Try-Catch Pattern

```javascript
async function safeApiCall(apiFunction, errorMessage) {
  try {
    return await apiFunction();
  } catch (error) {
    console.error(errorMessage, error);
    showError(errorMessage);
    return null;
  }
}

// Usage
const products = await safeApiCall(
  () => api.get('/products'),
  'Failed to load products'
);
```

### 4.3 Validation Error Handling

```javascript
async function submitForm(formData) {
  try {
    const result = await api.post('/products', formData);
    return result;
  } catch (error) {
    if (error.status === 422 && error.response?.data?.errors) {
      // Display validation errors
      displayValidationErrors(error.response.data.errors);
    } else {
      showError('Failed to submit form');
    }
    throw error;
  }
}
```

## 5. Performance Optimization

### 5.1 Request Caching

```javascript
const cache = new Map();

async function cachedGet(endpoint, params = {}) {
  const cacheKey = `${endpoint}_${JSON.stringify(params)}`;
  
  if (cache.has(cacheKey)) {
    return cache.get(cacheKey);
  }
  
  const data = await api.get(endpoint, params);
  cache.set(cacheKey, data);
  
  // Clear cache after 5 minutes
  setTimeout(() => cache.delete(cacheKey), 5 * 60 * 1000);
  
  return data;
}
```

### 5.2 Request Batching

```javascript
async function batchRequests(requests) {
  const batchSize = 5;
  const results = [];
  
  for (let i = 0; i < requests.length; i += batchSize) {
    const batch = requests.slice(i, i + batchSize);
    const batchResults = await api.all(batch.map(req => req()));
    results.push(...batchResults);
  }
  
  return results;
}
```

### 5.3 Lazy Loading

```javascript
async function loadProductsLazy() {
  let page = 1;
  let hasMore = true;
  
  while (hasMore) {
    const products = await api.get('/products', { 
      page,
      per_page: 20 
    });
    
    renderProducts(products.data);
    hasMore = products.hasMore;
    page++;
    
    // Small delay to prevent overwhelming
    await new Promise(resolve => setTimeout(resolve, 100));
  }
}
```

## 6. Security Considerations

### 6.1 CSRF Protection

**Already implemented in API client:**
```javascript
headers: {
  'X-CSRF-Token': this.#getCsrfToken()
}
```

### 6.2 Secure Data Transmission

```javascript
// Only send HTTPS in production
if (window.location.protocol === 'https:') {
  // Ensure all API calls use HTTPS
  api.addRequestInterceptor(async (config) => {
    config.url = config.url.replace('http://', 'https://');
    return config;
  });
}
```

### 6.3 Sensitive Data Handling

```javascript
// Don't log sensitive data
api.addRequestInterceptor(async (config) => {
  const safeConfig = { ...config };
  
  // Remove sensitive data from logs
  if (safeConfig.body) {
    const body = JSON.parse(safeConfig.body);
    delete body.password;
    delete body.credit_card;
    safeConfig.body = JSON.stringify(body);
  }
  
  console.log('API Request:', safeConfig.method, safeConfig.url);
  return config;
});
```

## 7. Testing AJAX Calls

### 7.1 Unit Testing Example

```javascript
import { api } from './modules/api.js';

describe('API Client', () => {
  beforeEach(() => {
    // Reset interceptors
    api.#interceptors = { request: [], response: [] };
  });

  test('makes GET request', async () => {
    const data = await api.get('/test');
    expect(data).toBeDefined();
  });

  test('handles errors correctly', async () => {
    await expect(api.get('/nonexistent')).rejects.toThrow();
  });

  test('applies request interceptors', async () => {
    let interceptorCalled = false;
    
    api.addRequestInterceptor(async (config) => {
      interceptorCalled = true;
      return config;
    });

    await api.get('/test');
    expect(interceptorCalled).toBe(true);
  });
});
```

## 8. Comparison: Enhanced Fetch vs Axios

| Feature | Enhanced Fetch | Axios |
|---------|---------------|-------|
| **Bundle Size** | 0KB (native) | ~15KB |
| **Dependencies** | None | axios |
| **Interceptors** | <i class="fas fa-check-circle"></i> Custom | <i class="fas fa-check-circle"></i> Built-in |
| **Cancellation** | <i class="fas fa-check-circle"></i> AbortController | <i class="fas fa-check-circle"></i> CancelToken |
| **Timeout** | <i class="fas fa-check-circle"></i> Custom | <i class="fas fa-check-circle"></i> Built-in |
| **Retry** | <i class="fas fa-check-circle"></i> Custom | ❌ Need plugin |
| **Progress** | <i class="fas fa-check-circle"></i> XHR wrapper | <i class="fas fa-check-circle"></i> Built-in |
| **Learning Value** | Very High | Medium |
| **Control** | Full | High |
| **Performance** | Excellent | Good |

## 9. Conclusion

Enhanced Fetch API provides the perfect balance for MediCore:

- **No Dependencies:** Sesuai Modern Vanilla JS philosophy
- **Full Control:** Complete customizability
- **Axios-like Features:** All important features implemented
- **Performance:** Native browser API performance
- **Learning Value:** Demonstrates deep understanding
- **Security:** Full control over security implementation

This approach shows ability to build sophisticated tools without framework dependencies while maintaining modern development practices.

---

**Document Status:** Approved  
**Implementation:** Complete in `modules/api.js`