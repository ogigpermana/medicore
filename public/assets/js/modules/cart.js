/**
 * Cart Manager Module
 * Shopping cart functionality with persistence
 */

import { CART_STORAGE_KEY } from '../config/constants.js';

export class CartManager {
  #items = [];
  #listeners = [];

  constructor() {
    this.#loadFromStorage();
  }

  /**
   * Add item to cart
   */
  addItem(product, quantity = 1) {
    const existingItem = this.#items.find(item => item.id === product.id);

    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.#items.push({
        id: product.id,
        name: product.name,
        price: product.sell_price,
        cost_price: product.cost_price,
        quantity: quantity,
        barcode: product.barcode,
        sku: product.sku
      });
    }

    this.#saveToStorage();
    this.#notifyListeners();
  }

  /**
   * Update item quantity
   */
  updateItemQuantity(productId, quantity) {
    const item = this.#items.find(item => item.id === productId);
    
    if (item) {
      if (quantity <= 0) {
        this.removeItem(productId);
      } else {
        item.quantity = quantity;
        this.#saveToStorage();
        this.#notifyListeners();
      }
    }
  }

  /**
   * Remove item from cart
   */
  removeItem(productId) {
    this.#items = this.#items.filter(item => item.id !== productId);
    this.#saveToStorage();
    this.#notifyListeners();
  }

  /**
   * Clear all items
   */
  clear() {
    this.#items = [];
    this.#saveToStorage();
    this.#notifyListeners();
  }

  /**
   * Get all items
   */
  getItems() {
    return [...this.#items];
  }

  /**
   * Get item count
   */
  getItemCount() {
    return this.#items.reduce((total, item) => total + item.quantity, 0);
  }

  /**
   * Get subtotal
   */
  getSubtotal() {
    return this.#items.reduce((total, item) => {
      return total + (item.price * item.quantity);
    }, 0);
  }

  /**
   * Get total cost (for profit calculation)
   */
  getTotalCost() {
    return this.#items.reduce((total, item) => {
      return total + (item.cost_price * item.quantity);
    }, 0);
  }

  /**
   * Get total profit
   */
  getTotalProfit() {
    return this.getSubtotal() - this.getTotalCost();
  }

  /**
   * Calculate total with discount and tax
   */
  getTotal(discount = 0, taxRate = 0.11) {
    const subtotal = this.getSubtotal();
    const discountAmount = subtotal * (discount / 100);
    const taxableAmount = subtotal - discountAmount;
    const taxAmount = taxableAmount * taxRate;
    return taxableAmount + taxAmount;
  }

  /**
   * Check if cart is empty
   */
  isEmpty() {
    return this.#items.length === 0;
  }

  /**
   * Check if product exists in cart
   */
  hasItem(productId) {
    return this.#items.some(item => item.id === productId);
  }

  /**
   * Get item by product ID
   */
  getItem(productId) {
    return this.#items.find(item => item.id === productId);
  }

  /**
   * Subscribe to cart changes
   */
  subscribe(listener) {
    this.#listeners.push(listener);
    // Return unsubscribe function
    return () => {
      this.#listeners = this.#listeners.filter(l => l !== listener);
    };
  }

  /**
   * Save cart to localStorage
   * @private
   */
  #saveToStorage() {
    try {
      localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(this.#items));
    } catch (error) {
      console.error('Failed to save cart to storage:', error);
    }
  }

  /**
   * Load cart from localStorage
   * @private
   */
  #loadFromStorage() {
    try {
      const stored = localStorage.getItem(CART_STORAGE_KEY);
      if (stored) {
        this.#items = JSON.parse(stored);
      }
    } catch (error) {
      console.error('Failed to load cart from storage:', error);
      this.#items = [];
    }
  }

  /**
   * Notify all listeners of changes
   * @private
   */
  #notifyListeners() {
    this.#listeners.forEach(listener => {
      try {
        listener(this.#items);
      } catch (error) {
        console.error('Listener error:', error);
      }
    });
  }
}

// Export singleton instance
export const cart = new CartManager();