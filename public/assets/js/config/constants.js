/**
 * Application Constants
 * Centralized configuration for the MediCore Pharmacy System
 */

export const API_BASE_URL = '/api';
export const WEBSOCKET_URL = 'ws://localhost:8080';

export const CART_STORAGE_KEY = 'medicore_cart';
export const AUTH_STORAGE_KEY = 'medicore_auth';

export const PAGINATION = {
  DEFAULT_PAGE: 1,
  DEFAULT_PER_PAGE: 15,
  MAX_PER_PAGE: 100
};

export const VALIDATION = {
  PRODUCT_NAME_MIN_LENGTH: 2,
  PRODUCT_NAME_MAX_LENGTH: 255,
  PRICE_MIN: 0,
  STOCK_MIN: 0,
  BARCODE_LENGTH: 13
};

export const ALERT_THRESHOLDS = {
  LOW_STOCK_DAYS: 7,
  EXPIRY_WARNING_DAYS: 90,
  EXPIRY_CRITICAL_DAYS: 30
};

export const PAYMENT_METHODS = {
  CASH: 'cash',
  TRANSFER: 'transfer',
  EWALLET: 'ewallet',
  QRIS: 'qris',
  CREDIT: 'credit'
};

export const USER_ROLES = {
  SUPERADMIN: 'superadmin',
  OWNER: 'owner',
  PHARMACIST: 'pharmacist',
  CASHIER: 'cashier',
  WAREHOUSE: 'warehouse'
};

export const ERROR_MESSAGES = {
  NETWORK_ERROR: 'Network error occurred. Please check your connection.',
  VALIDATION_ERROR: 'Please check your input and try again.',
  AUTH_ERROR: 'Authentication failed. Please login again.',
  PERMISSION_ERROR: 'You do not have permission to perform this action.',
  GENERIC_ERROR: 'An error occurred. Please try again later.'
};