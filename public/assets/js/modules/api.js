/**
 * API Client Module
 * Enhanced Fetch API wrapper with Axios-like features
 * Handles all API communication with security features
 */

import { API_BASE_URL, ERROR_MESSAGES } from '../config/constants.js';

export class ApiClient {
  #baseUrl;
  #headers;
  #defaultOptions;
  #interceptors = {
    request: [],
    response: []
  };
  #abortControllers = new Map();

  constructor(baseUrl = API_BASE_URL) {
    this.#baseUrl = baseUrl;
    this.#headers = {
      'Content-Type': 'application/json',
      'X-CSRF-Token': this.#getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest'
    };
    this.#defaultOptions = {
      credentials: 'same-origin',
      headers: this.#headers
    };
  }

  /**
   * Get CSRF token from meta tag
   * @private
   */
  #getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
  }

  /**
   * Update CSRF token (useful after login)
   */
  updateCsrfToken() {
    this.#headers['X-CSRF-Token'] = this.#getCsrfToken();
  }

  /**
   * Add request interceptor (like Axios)
   * @param {Function} interceptor - Function to transform request config
   */
  addRequestInterceptor(interceptor) {
    this.#interceptors.request.push(interceptor);
  }

  /**
   * Add response interceptor (like Axios)
   * @param {Function} interceptor - Function to transform response
   */
  addResponseInterceptor(interceptor) {
    this.#interceptors.response.push(interceptor);
  }

  /**
   * Apply request interceptors
   * @private
   */
  async #applyRequestInterceptors(config) {
    let transformedConfig = { ...config };
    
    for (const interceptor of this.#interceptors.request) {
      transformedConfig = await interceptor(transformedConfig);
    }
    
    return transformedConfig;
  }

  /**
   * Apply response interceptors
   * @private
   */
  async #applyResponseInterceptors(response) {
    let transformedResponse = response;
    
    for (const interceptor of this.#interceptors.response) {
      transformedResponse = await interceptor(transformedResponse);
    }
    
    return transformedResponse;
  }

  /**
   * Create abort controller for cancellable requests
   * @private
   */
  #createAbortController(requestKey) {
    const controller = new AbortController();
    this.#abortControllers.set(requestKey, controller);
    return controller;
  }

  /**
   * Cancel request by key
   * @param {string} requestKey - Unique identifier for the request
   */
  cancelRequest(requestKey) {
    const controller = this.#abortControllers.get(requestKey);
    if (controller) {
      controller.abort();
      this.#abortControllers.delete(requestKey);
    }
  }

  /**
   * Cancel all pending requests
   */
  cancelAllRequests() {
    this.#abortControllers.forEach((controller) => {
      controller.abort();
    });
    this.#abortControllers.clear();
  }

  /**
   * Generic request handler with enhanced features
   * @private
   */
  async #request(endpoint, options = {}) {
    const requestKey = options.requestKey || `${endpoint}_${Date.now()}`;
    const abortController = this.#createAbortController(requestKey);

    let config = {
      ...this.#defaultOptions,
      ...options,
      signal: abortController.signal,
      headers: {
        ...this.#headers,
        ...options.headers
      }
    };

    // Apply request interceptors
    config = await this.#applyRequestInterceptors(config);

    const url = `${this.#baseUrl}${endpoint}`;

    try {
      const response = await fetch(url, config);

      // Clean up abort controller
      this.#abortControllers.delete(requestKey);

      // Handle non-JSON responses
      const contentType = response.headers.get('content-type');
      let data;

      if (!contentType || !contentType.includes('application/json')) {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        data = await response.text();
      } else {
        data = await response.json();
      }

      // Create response object (Axios-like structure)
      const responseObj = {
        data: data,
        status: response.status,
        statusText: response.statusText,
        headers: Object.fromEntries(response.headers.entries()),
        config: config
      };

      // Apply response interceptors
      const transformedResponse = await this.#applyResponseInterceptors(responseObj);

      if (!response.ok) {
        const error = new Error(data?.message || ERROR_MESSAGES.GENERIC_ERROR);
        error.response = transformedResponse;
        error.status = response.status;
        throw error;
      }

      return transformedResponse.data;
    } catch (error) {
      // Clean up abort controller on error
      this.#abortControllers.delete(requestKey);

      // Handle abort errors
      if (error.name === 'AbortError') {
        console.log('Request was cancelled:', requestKey);
        throw new Error('Request cancelled');
      }

      // Handle network errors
      if (!error.response) {
        console.error('Network error:', error);
        throw new Error(ERROR_MESSAGES.NETWORK_ERROR);
      }

      console.error('API request failed:', error);
      throw error;
    }
  }

  /**
   * GET request
   * @param {string} endpoint - API endpoint
   * @param {Object} params - Query parameters
   * @param {Object} options - Additional options
   */
  async get(endpoint, params = {}, options = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;
    return this.#request(url, { ...options, method: 'GET' });
  }

  /**
   * POST request
   * @param {string} endpoint - API endpoint
   * @param {Object} data - Request body data
   * @param {Object} options - Additional options
   */
  async post(endpoint, data = {}, options = {}) {
    return this.#request(endpoint, {
      ...options,
      method: 'POST',
      body: JSON.stringify(data)
    });
  }

  /**
   * PUT request
   * @param {string} endpoint - API endpoint
   * @param {Object} data - Request body data
   * @param {Object} options - Additional options
   */
  async put(endpoint, data = {}, options = {}) {
    return this.#request(endpoint, {
      ...options,
      method: 'PUT',
      body: JSON.stringify(data)
    });
  }

  /**
   * PATCH request
   * @param {string} endpoint - API endpoint
   * @param {Object} data - Request body data
   * @param {Object} options - Additional options
   */
  async patch(endpoint, data = {}, options = {}) {
    return this.#request(endpoint, {
      ...options,
      method: 'PATCH',
      body: JSON.stringify(data)
    });
  }

  /**
   * DELETE request
   * @param {string} endpoint - API endpoint
   * @param {Object} options - Additional options
   */
  async delete(endpoint, options = {}) {
    return this.#request(endpoint, { ...options, method: 'DELETE' });
  }

  /**
   * File upload with progress tracking
   * @param {string} endpoint - API endpoint
   * @param {File} file - File to upload
   * @param {Function} onProgress - Progress callback
   * @param {Object} options - Additional options
   */
  async upload(endpoint, file, onProgress = null, options = {}) {
    const formData = new FormData();
    formData.append('file', file);

    // Add additional form data if provided
    if (options.formData) {
      Object.entries(options.formData).forEach(([key, value]) => {
        formData.append(key, value);
      });
    }

    const requestKey = options.requestKey || `upload_${Date.now()}`;
    const abortController = this.#createAbortController(requestKey);

    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();

      // Progress tracking
      if (onProgress) {
        xhr.upload.addEventListener('progress', (e) => {
          if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            onProgress(percentComplete, e.loaded, e.total);
          }
        });
      }

      // Load complete
      xhr.addEventListener('load', () => {
        this.#abortControllers.delete(requestKey);

        if (xhr.status >= 200 && xhr.status < 300) {
          try {
            const response = JSON.parse(xhr.responseText);
            resolve(response);
          } catch {
            resolve(xhr.responseText);
          }
        } else {
          const error = new Error(`Upload failed with status ${xhr.status}`);
          error.status = xhr.status;
          error.response = xhr.responseText;
          reject(error);
        }
      });

      // Error handling
      xhr.addEventListener('error', () => {
        this.#abortControllers.delete(requestKey);
        reject(new Error('Upload failed'));
      });

      // Abort handling
      xhr.addEventListener('abort', () => {
        this.#abortControllers.delete(requestKey);
        reject(new Error('Upload cancelled'));
      });

      xhr.open('POST', `${this.#baseUrl}${endpoint}`);
      xhr.setRequestHeader('X-CSRF-Token', this.#getCsrfToken());
      
      // Add custom headers if provided
      if (options.headers) {
        Object.entries(options.headers).forEach(([key, value]) => {
          xhr.setRequestHeader(key, value);
        });
      }

      xhr.send(formData);
    });
  }

  /**
   * Multiple concurrent requests (like Axios.all)
   * @param {Array} requests - Array of request functions
   */
  async all(requests) {
    return Promise.all(requests);
  }

  /**
   * Race between multiple requests (like Promise.race)
   * @param {Array} requests - Array of request functions
   */
  async race(requests) {
    return Promise.race(requests);
  }

  /**
   * Request with timeout
   * @param {string} endpoint - API endpoint
   * @param {Object} options - Request options
   * @param {number} timeout - Timeout in milliseconds
   */
  async withTimeout(endpoint, options = {}, timeout = 5000) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeout);

    try {
      const response = await this.#request(endpoint, {
        ...options,
        signal: controller.signal
      });
      clearTimeout(timeoutId);
      return response;
    } catch (error) {
      clearTimeout(timeoutId);
      if (error.name === 'AbortError') {
        throw new Error('Request timeout');
      }
      throw error;
    }
  }

  /**
   * Retry failed requests
   * @param {Function} requestFn - Request function to retry
   * @param {number} maxRetries - Maximum number of retries
   * @param {number} delay - Delay between retries in ms
   */
  async retry(requestFn, maxRetries = 3, delay = 1000) {
    let lastError;

    for (let i = 0; i < maxRetries; i++) {
      try {
        return await requestFn();
      } catch (error) {
        lastError = error;
        if (i < maxRetries - 1) {
          await new Promise(resolve => setTimeout(resolve, delay * (i + 1)));
        }
      }
    }

    throw lastError;
  }
}

// Export singleton instance
export const api = new ApiClient();

// Example: Add global request interceptor for logging
api.addRequestInterceptor(async (config) => {
  console.log('API Request:', config.method, config.url || config);
  return config;
});

// Example: Add global response interceptor for error handling
api.addResponseInterceptor(async (response) => {
  if (response.status >= 400) {
    console.error('API Error:', response.status, response.data);
  }
  return response;
});