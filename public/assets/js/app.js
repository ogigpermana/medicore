/**
 * MediCore Pharmacy System - Main Application
 * Modern Vanilla JS with ES2025 Features
 */

import { cart } from './modules/cart.js';
import { api } from './modules/api.js';
import { Validator } from './modules/validator.js';
import { formatCurrency, formatDateTime } from './utils/format.js';

class PharmacyApp {
  #initialized = false;

  constructor() {
    if (this.#initialized) return;
    
    this.#initializeEventListeners();
    this.#initializeCartUpdates();
    this.#initializeBarcodeScanner();
    
    this.#initialized = true;
    console.log('MediCore Application Initialized');
  }

  /**
   * Initialize global event listeners
   * @private
   */
  #initializeEventListeners() {
    // Form submissions with validation
    document.querySelectorAll('form[data-validate]').forEach(form => {
      form.addEventListener('submit', (e) => this.#handleFormSubmit(e));
    });

    // API action buttons
    document.querySelectorAll('[data-action]').forEach(button => {
      button.addEventListener('click', (e) => this.#handleAction(e));
    });

    // Search inputs with debounce
    document.querySelectorAll('input[data-search]').forEach(input => {
      let debounceTimer;
      input.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
          this.#handleSearch(e.target);
        }, 300);
      });
    });

    // Modal triggers
    document.querySelectorAll('[data-modal]').forEach(trigger => {
      trigger.addEventListener('click', (e) => this.#handleModal(e));
    });
  }

  /**
   * Initialize cart update listeners
   * @private
   */
  #initializeCartUpdates() {
    cart.subscribe((items) => {
      this.#updateCartUI(items);
    });
  }

  /**
   * Initialize barcode scanner (placeholder for integration)
   * @private
   */
  #initializeBarcodeScanner() {
    // This would integrate with a barcode scanner library
    // For now, we'll use keyboard input simulation
    document.addEventListener('keydown', (e) => {
      if (e.target.tagName === 'INPUT' && e.target.dataset.barcodeInput) {
        if (e.key === 'Enter') {
          e.preventDefault();
          this.#handleBarcodeScan(e.target.value);
          e.target.value = '';
        }
      }
    });
  }

  /**
   * Handle form submission with validation
   * @private
   */
  async #handleFormSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Sanitize input
    const sanitizedData = Validator.sanitizeObject(data);

    // Validate based on form type
    const formType = form.dataset.validate;
    let validation;

    switch (formType) {
      case 'product':
        validation = Validator.validateProduct(sanitizedData);
        break;
      case 'customer':
        validation = Validator.validateCustomer(sanitizedData);
        break;
      case 'sale':
        validation = Validator.validateSale(sanitizedData);
        break;
      default:
        validation = { isValid: true, errors: {} };
    }

    if (!validation.isValid) {
      this.#showValidationErrors(validation.errors, form);
      return;
    }

    // Submit form
    await this.#submitForm(form.action, sanitizedData, form.method);
  }

  /**
   * Handle action buttons
   * @private
   */
  async #handleAction(event) {
    const button = event.currentTarget;
    const action = button.dataset.action;
    const confirmMessage = button.dataset.confirm;

    if (confirmMessage && !confirm(confirmMessage)) {
      return;
    }

    try {
      await this.#executeAction(action, button.dataset);
      this.#showSuccess('Operation completed successfully');
    } catch (error) {
      this.#showError(error.message);
    }
  }

  /**
   * Handle search input
   * @private
   */
  async #handleSearch(input) {
    const searchType = input.dataset.search;
    const query = input.value;

    if (query.length < 2) return;

    try {
      const results = await api.get(`/${searchType}/search`, { q: query });
      this.#displaySearchResults(results, searchType);
    } catch (error) {
      console.error('Search failed:', error);
    }
  }

  /**
   * Handle modal triggers
   * @private
   */
  #handleModal(event) {
    const trigger = event.currentTarget;
    const modalId = trigger.dataset.modal;
    const modal = document.getElementById(modalId);

    if (modal) {
      modal.classList.add('show');
      modal.style.display = 'block';
    }
  }

  /**
   * Handle barcode scan
   * @private
   */
  async #handleBarcodeScan(code) {
    try {
      const product = await api.get(`/products/barcode/${code}`);
      
      if (product) {
        cart.addItem(product);
        this.#showSuccess(`Added: ${product.name}`);
      } else {
        this.#showError('Product not found');
      }
    } catch (error) {
      this.#showError('Failed to add product');
    }
  }

  /**
   * Submit form via API
   * @private
   */
  async #submitForm(url, data, method = 'POST') {
    try {
      let response;
      switch (method.toUpperCase()) {
        case 'PUT':
          response = await api.put(url, data);
          break;
        case 'DELETE':
          response = await api.delete(url);
          break;
        default:
          response = await api.post(url, data);
      }

      this.#showSuccess('Operation completed successfully');
      
      // Reset form if successful
      const form = document.querySelector(`form[action="${url}"]`);
      if (form) form.reset();

      return response;
    } catch (error) {
      this.#showError(error.message);
      throw error;
    }
  }

  /**
   * Execute action based on type
   * @private
   */
  async #executeAction(action, data) {
    switch (action) {
      case 'delete':
        await api.delete(data.url);
        break;
      case 'addToCart':
        const product = await api.get(`/products/${data.id}`);
        cart.addItem(product);
        break;
      case 'clearCart':
        cart.clear();
        break;
      default:
        throw new Error(`Unknown action: ${action}`);
    }
  }

  /**
   * Update cart UI
   * @private
   */
  #updateCartUI(items) {
    const cartContainer = document.getElementById('cart-items');
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');

    if (cartContainer) {
      if (items.length === 0) {
        cartContainer.innerHTML = '<p class="text-muted">Cart is empty</p>';
      } else {
        cartContainer.innerHTML = items.map(item => `
          <div class="cart-item d-flex justify-content-between align-items-center mb-2">
            <div>
              <div class="fw-bold">${item.name}</div>
              <small class="text-muted">${item.quantity} x ${formatCurrency(item.price)}</small>
            </div>
            <div class="d-flex align-items-center">
              <button class="btn btn-sm btn-outline-secondary" data-action="updateQuantity" data-id="${item.id}" data-qty="${item.quantity - 1}">-</button>
              <span class="mx-2">${item.quantity}</span>
              <button class="btn btn-sm btn-outline-secondary" data-action="updateQuantity" data-id="${item.id}" data-qty="${item.quantity + 1}">+</button>
              <button class="btn btn-sm btn-outline-danger ms-2" data-action="removeFromCart" data-id="${item.id}">×</button>
            </div>
          </div>
        `).join('');
      }
    }

    if (cartCount) {
      cartCount.textContent = cart.getItemCount();
    }

    if (cartTotal) {
      cartTotal.textContent = formatCurrency(cart.getSubtotal());
    }
  }

  /**
   * Display search results
   * @private
   */
  #displaySearchResults(results, type) {
    const resultsContainer = document.getElementById(`${type}-results`);
    
    if (resultsContainer) {
      if (results.length === 0) {
        resultsContainer.innerHTML = '<p class="text-muted">No results found</p>';
      } else {
        resultsContainer.innerHTML = results.map(item => `
          <div class="search-result p-2 border-bottom" data-id="${item.id}">
            <div class="fw-bold">${item.name}</div>
            <small class="text-muted">${item.sku || item.barcode || ''}</small>
          </div>
        `).join('');
      }
    }
  }

  /**
   * Show validation errors
   * @private
   */
  #showValidationErrors(errors, form) {
    // Clear previous errors
    form.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
    
    // Show new errors
    Object.entries(errors).forEach(([field, message]) => {
      const errorElement = form.querySelector(`[data-error="${field}"]`);
      if (errorElement) {
        errorElement.textContent = message;
      }
    });
  }

  /**
   * Show success message
   * @private
   */
  #showSuccess(message) {
    this.#showAlert(message, 'success');
  }

  /**
   * Show error message
   * @private
   */
  #showError(message) {
    this.#showAlert(message, 'danger');
  }

  /**
   * Show alert message
   * @private
   */
  #showAlert(message, type) {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    alertContainer.appendChild(alert);

    // Auto-dismiss after 3 seconds
    setTimeout(() => {
      alert.classList.remove('show');
      setTimeout(() => alert.remove(), 150);
    }, 3000);
  }
}

// Initialize application when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    window.pharmacyApp = new PharmacyApp();
  });
} else {
  window.pharmacyApp = new PharmacyApp();
}

// Export for testing
export { PharmacyApp };