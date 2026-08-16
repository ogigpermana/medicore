/**
 * Validator Module
 * Input validation and sanitization for security
 */

import { VALIDATION } from '../config/constants.js';

export class Validator {
  /**
   * Validate product data
   */
  static validateProduct(data) {
    const errors = {};

    if (!data.name?.trim()) {
      errors.name = 'Product name is required';
    } else if (data.name.length < VALIDATION.PRODUCT_NAME_MIN_LENGTH) {
      errors.name = `Product name must be at least ${VALIDATION.PRODUCT_NAME_MIN_LENGTH} characters`;
    } else if (data.name.length > VALIDATION.PRODUCT_NAME_MAX_LENGTH) {
      errors.name = `Product name must not exceed ${VALIDATION.PRODUCT_NAME_MAX_LENGTH} characters`;
    }

    if (!data.price || data.price < VALIDATION.PRICE_MIN) {
      errors.price = 'Price must be greater than 0';
    }

    if (data.stock === undefined || data.stock < VALIDATION.STOCK_MIN) {
      errors.stock = 'Stock cannot be negative';
    }

    if (data.barcode && data.barcode.length !== VALIDATION.BARCODE_LENGTH) {
      errors.barcode = `Barcode must be ${VALIDATION.BARCODE_LENGTH} characters`;
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    };
  }

  /**
   * Validate customer data
   */
  static validateCustomer(data) {
    const errors = {};

    if (!data.name?.trim()) {
      errors.name = 'Customer name is required';
    }

    if (data.phone && !this.#isValidPhone(data.phone)) {
      errors.phone = 'Invalid phone number format';
    }

    if (data.email && !this.#isValidEmail(data.email)) {
      errors.email = 'Invalid email format';
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    };
  }

  /**
   * Validate sale data
   */
  static validateSale(data) {
    const errors = {};

    if (!data.items || data.items.length === 0) {
      errors.items = 'Cart cannot be empty';
    }

    if (!data.payment_method) {
      errors.payment_method = 'Payment method is required';
    }

    if (data.payment_method === 'cash' && (!data.paid_amount || data.paid_amount < data.total_amount)) {
      errors.paid_amount = 'Insufficient payment amount';
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors
    };
  }

  /**
   * Sanitize user input to prevent XSS
   */
  static sanitizeInput(input) {
    if (typeof input !== 'string') return input;

    return input
      .trim()
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#x27;')
      .replace(/\//g, '&#x2F;');
  }

  /**
   * Sanitize object recursively
   */
  static sanitizeObject(obj) {
    if (typeof obj !== 'object' || obj === null) {
      return this.sanitizeInput(obj);
    }

    if (Array.isArray(obj)) {
      return obj.map(item => this.sanitizeObject(item));
    }

    return Object.fromEntries(
      Object.entries(obj).map(([key, value]) => [
        key,
        this.sanitizeObject(value)
      ])
    );
  }

  /**
   * Validate email format
   * @private
   */
  static #isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  /**
   * Validate phone number format (Indonesia)
   * @private
   */
  static #isValidPhone(phone) {
    const phoneRegex = /^(\+62|62|0)[0-9]{9,12}$/;
    return phoneRegex.test(phone.replace(/[-\s]/g, ''));
  }

  /**
   * Validate barcode format
   */
  static isValidBarcode(barcode) {
    return barcode && barcode.length === VALIDATION.BARCODE_LENGTH && /^\d+$/.test(barcode);
  }

  /**
   * Check if date is expired
   */
  static isExpired(expiryDate) {
    const today = new Date();
    const expiry = new Date(expiryDate);
    return expiry < today;
  }

  /**
   * Check if date is near expiry
   */
  static isNearExpiry(expiryDate, days = 90) {
    const today = new Date();
    const expiry = new Date(expiryDate);
    const diffTime = expiry - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays > 0 && diffDays <= days;
  }
}