import axios from 'axios';

// Base URL cho API backend
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/QuanLyBanHangFigure/api';
const ADMIN_API_BASE_URL = API_BASE_URL.replace('/api', '') + '/api/admin';

// Log for debugging
console.log('API Base URL:', API_BASE_URL);
console.log('Admin API Base URL:', ADMIN_API_BASE_URL);

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  }
});

const adminApi = axios.create({
  baseURL: ADMIN_API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true
});

// Auth endpoints
export const authService = {
  login: (username, password) => 
    api.post('/auth/login.php', { username, password }, { withCredentials: true }),
  
  register: (username, password, email, full_name) =>
    api.post('/auth/register.php', { username, password, email, full_name }),
  
  logout: () =>
    api.post('/auth/logout.php', {}, { withCredentials: true }),
  
  getCurrentUser: () =>
    api.get('/auth/me.php', { withCredentials: true }),

  sendOTP: (email) =>
    api.post('/auth/send_otp.php', { email }),

  verifyOTP: (email, otp_code) =>
    api.post('/auth/verify_otp.php', { email, otp_code })
};

// Product endpoints
export const productService = {
  getAll: (page = 1, limit = 20) =>
    api.get('/products/list.php', { params: { page, limit } }),
  
  getById: (id_products) =>
    api.get(`/products/detail.php`, { params: { id_products } }),
  
  search: (q, page = 1, limit = 20) =>
    api.get('/products/search.php', { params: { q, page, limit } }),
  
  getByCategory: (category_slug, page = 1, limit = 20) =>
    api.get('/products/category.php', { params: { category_slug, page, limit } }),
  
  getFeatured: () =>
    api.get('/products/featured.php'),

  create: (formData) =>
    api.post('/products/create.php', formData, { headers: { 'Content-Type': 'multipart/form-data' } }),

  update: (id_products, formData) =>
    api.put(`/products/update.php?id_products=${id_products}`, formData, { headers: { 'Content-Type': 'multipart/form-data' } }),

  delete: (id_products) =>
    api.delete(`/products/delete.php?id_products=${id_products}`)
};

// Category endpoints
export const categoryService = {
  getAll: () =>
    api.get('/categories/list.php'),
  
  getById: (id_categories) =>
    api.get('/categories/detail.php', { params: { id_categories } }),

  create: (name, slug) =>
    api.post('/categories/create.php', { name, slug }),

  update: (id_categories, name, slug) =>
    api.put(`/categories/update.php?id_categories=${id_categories}`, { name, slug }),

  delete: (id_categories) =>
    api.delete(`/categories/delete.php?id_categories=${id_categories}`)
};

// Cart endpoints
export const cartService = {
  getByUser: () =>
    api.get('/cart/list.php'),
  
  addItem: (id_products, quantity) =>
    api.post('/cart/add.php', { id_products, quantity }),
  
  updateQuantity: (id_carts, quantity) =>
    api.put(`/cart/update.php?id_carts=${id_carts}`, { quantity }),
  
  removeItem: (id_carts) =>
    api.delete(`/cart/delete.php?id_carts=${id_carts}`),

  clear: () =>
    api.delete('/cart/clear.php')
};

// Order endpoints
export const orderService = {
  getAll: (page = 1, limit = 20) =>
    api.get('/orders/list.php', { params: { page, limit } }),
  
  getById: (id_order) =>
    api.get('/orders/detail.php', { params: { id_order } }),
  
  getByUser: () =>
    api.get('/orders/my-orders.php'),
  
  create: (payment_method, full_name, phone, address) =>
    api.post('/orders/create.php', { payment_method, full_name, phone, address }),

  updateStatus: (id_order, status) =>
    api.put(`/orders/update-status.php?id_order=${id_order}`, { status })
};

// User endpoints
export const userService = {
  getById: (id_users) =>
    api.get('/users/detail.php', { params: { id_users } }),

  update: (id_users, full_name, email, phone, address) =>
    api.put(`/users/update.php?id_users=${id_users}`, { full_name, email, phone, address })
};

// Review endpoints
export const reviewService = {
  getByProduct: (id_products) =>
    api.get('/reviews/list.php', { params: { id_products } }),
  
  create: (id_products, rating, comment) =>
    api.post('/reviews/create.php', { id_products, rating, comment })
};

// Voucher endpoints
export const voucherService = {
  validate: (code) =>
    api.get('/vouchers/validate.php', { params: { code } }),

  getAll: () =>
    api.get('/vouchers/list.php')
};

// Payment endpoints
export const paymentService = {
  createMomoPayment: (amount, order_id) =>
    api.post('/payment/momo-create.php', { amount, order_id }),

  verifyPayment: (id_order) =>
    api.get('/payment/verify.php', { params: { id_order } })
};

export default api;

// Admin API Services
export const adminProductService = {
  getAll: (page = 1, limit = 20) =>
    adminApi.get('/products/list.php', { params: { page, limit } }),
  
  create: (formData) =>
    adminApi.post('/products/create.php', formData),

  update: (id_products, formData) =>
    adminApi.put(`/products/update.php?id_products=${id_products}`, formData),

  delete: (id_products) =>
    adminApi.delete(`/products/delete.php?id_products=${id_products}`)
};

export const adminCategoryService = {
  getAll: () =>
    adminApi.get('/categories/list.php'),

  create: (formData) =>
    adminApi.post('/categories/create.php', formData),

  update: (id_categories, formData) =>
    adminApi.put(`/categories/update.php?id_categories=${id_categories}`, formData),

  delete: (id_categories) =>
    adminApi.delete(`/categories/delete.php?id_categories=${id_categories}`)
};

export const adminOrderService = {
  getAll: (page = 1, limit = 20) =>
    adminApi.get('/orders/list.php', { params: { page, limit } })
};

export const adminVoucherService = {
  getAll: (page = 1, limit = 20) =>
    adminApi.get('/vouchers/list.php', { params: { page, limit } }),
  
  create: (formData) =>
    adminApi.post('/vouchers/create.php', formData),

  update: (id_vouchers, formData) =>
    adminApi.put(`/vouchers/update.php?id_vouchers=${id_vouchers}`, formData),

  delete: (id_vouchers) =>
    adminApi.delete(`/vouchers/delete.php?id_vouchers=${id_vouchers}`)
};
