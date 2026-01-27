import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import { categoryService, adminProductService } from '../../services/api';
import axios from 'axios';

const AdminProducts = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [formData, setFormData] = useState({
    products_name: '',
    id_categories: '',
    description: '',
    price: 0,
    stock_quantity: 0
  });

  const baseUrl = process.env.REACT_APP_API_URL || 'http://localhost:8000/QuanLyBanHangFigure/api';
  const adminUrl = `${baseUrl.replace('/api', '')}/api/admin`;

  useEffect(() => {
    if (user?.role !== 'admin') {
      navigate('/');
      return;
    }
    loadData();
  }, [user, navigate, page]);

  const loadData = async () => {
    try {
      setLoading(true);
      const [productsRes, categoriesRes] = await Promise.all([
        adminProductService.getAll(page, 20),
        categoryService.getAll()
      ]);
      
      console.log('Products Response:', productsRes.data);
      console.log('Products Data:', productsRes.data.data);
      console.log('Categories Response:', categoriesRes.data);
      console.log('Categories Data:', categoriesRes.data.data);
      
      setProducts(productsRes.data.data || []);
      setCategories(categoriesRes.data.data || []);
      setTotalPages(Math.ceil((productsRes.data.total || 0) / 20));
    } catch (error) {
      console.error('Error loading data:', error);
      alert('Lỗi tải dữ liệu: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: name === 'price' || name === 'stock_quantity' ? parseFloat(value) || 0 : value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await axios.put(`${adminUrl}/products/update.php?id_products=${editingId}`, formData, { withCredentials: true });
        alert('Cập nhật sản phẩm thành công');
      } else {
        await axios.post(`${adminUrl}/products/create.php`, formData, { withCredentials: true });
        alert('Thêm sản phẩm thành công');
      }
      resetForm();
      loadData();
    } catch (error) {
      console.error('Error saving product:', error);
      alert('Lỗi: ' + (error.response?.data?.message || error.message));
    }
  };

  const handleEdit = (product) => {
    setEditingId(product.id_products);
    setFormData({
      products_name: product.products_name,
      id_categories: product.id_categories,
      description: product.description || '',
      price: product.price || 0,
      stock_quantity: product.stock_quantity || 0
    });
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
      try {
        await axios.delete(`${adminUrl}/products/delete.php?id_products=${id}`, { withCredentials: true });
        alert('Xóa sản phẩm thành công');
        loadData();
      } catch (error) {
        console.error('Error deleting product:', error);
        alert('Lỗi: ' + (error.response?.data?.message || error.message));
      }
    }
  };

  const resetForm = () => {
    setShowForm(false);
    setEditingId(null);
    setFormData({
      products_name: '',
      id_categories: '',
      description: '',
      price: 0,
      stock_quantity: 0
    });
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  return (
    <div className="container-fluid mt-4 mb-5">
      <div className="row mb-4">
        <div className="col-md-6">
          <h2>Quản lý sản phẩm</h2>
        </div>
        <div className="col-md-6 text-end">
          <button 
            className="btn btn-primary"
            onClick={() => showForm ? resetForm() : setShowForm(true)}
          >
            {showForm ? '✕ Hủy' : '+ Thêm sản phẩm'}
          </button>
        </div>
      </div>

      {/* Form */}
      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5>{editingId ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới'}</h5>
            <form onSubmit={handleSubmit}>
              <div className="row">
                <div className="col-md-6 mb-3">
                  <label className="form-label">Tên sản phẩm *</label>
                  <input
                    type="text"
                    className="form-control"
                    name="products_name"
                    value={formData.products_name}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="col-md-6 mb-3">
                  <label className="form-label">Danh mục *</label>
                  <select
                    className="form-control"
                    name="id_categories"
                    value={formData.id_categories}
                    onChange={handleInputChange}
                    required
                  >
                    <option value="">-- Chọn danh mục --</option>
                    {categories.map(cat => (
                      <option key={cat.id_categories} value={cat.id_categories}>
                        {cat.name || cat.categories_name}
                      </option>
                    ))}
                  </select>
                </div>
              </div>
              <div className="mb-3">
                <label className="form-label">Mô tả</label>
                <textarea
                  className="form-control"
                  name="description"
                  value={formData.description}
                  onChange={handleInputChange}
                  rows="3"
                ></textarea>
              </div>
              <div className="row">
                <div className="col-md-6 mb-3">
                  <label className="form-label">Giá *</label>
                  <input
                    type="number"
                    className="form-control"
                    name="price"
                    value={formData.price}
                    onChange={handleInputChange}
                    required
                    min="0"
                  />
                </div>
                <div className="col-md-6 mb-3">
                  <label className="form-label">Số lượng *</label>
                  <input
                    type="number"
                    className="form-control"
                    name="stock_quantity"
                    value={formData.stock_quantity}
                    onChange={handleInputChange}
                    required
                    min="0"
                  />
                </div>
              </div>
              <button type="submit" className="btn btn-success">
                {editingId ? 'Cập nhật' : 'Thêm'} sản phẩm
              </button>
            </form>
          </div>
        </div>
      )}

      {/* Products Table */}
      <div className="card shadow-sm">
        <div className="card-body">
          {products.length > 0 ? (
            <div className="table-responsive">
              <table className="table table-striped">
                <thead>
                  <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  {products.map(product => (
                    <tr key={product.id_products}>
                      <td>#{product.id_products}</td>
                      <td>{product.name || product.products_name}</td>
                      <td>{product.category_name || 'N/A'}</td>
                      <td>{(product.price || 0).toLocaleString('vi-VN')}đ</td>
                      <td>{product.stock || product.stock || 'N/A'}</td>
                      <td>
                        <button 
                          className="btn btn-sm btn-warning me-2"
                          onClick={() => handleEdit(product)}
                        >
                          Sửa
                        </button>
                        <button 
                          className="btn btn-sm btn-danger"
                          onClick={() => handleDelete(product.id_products)}
                        >
                          Xóa
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <p className="text-muted text-center">Không có sản phẩm nào</p>
          )}
        </div>
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <nav className="mt-4">
          <ul className="pagination justify-content-center">
            <li className={`page-item ${page === 1 ? 'disabled' : ''}`}>
              <button className="page-link" onClick={() => setPage(p => Math.max(1, p - 1))}>
                Trước
              </button>
            </li>
            {[...Array(totalPages)].map((_, i) => (
              <li key={i + 1} className={`page-item ${page === i + 1 ? 'active' : ''}`}>
                <button className="page-link" onClick={() => setPage(i + 1)}>
                  {i + 1}
                </button>
              </li>
            ))}
            <li className={`page-item ${page === totalPages ? 'disabled' : ''}`}>
              <button className="page-link" onClick={() => setPage(p => Math.min(totalPages, p + 1))}>
                Sau
              </button>
            </li>
          </ul>
        </nav>
      )}
    </div>
  );
};

export default AdminProducts;
