import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const AdminCategories = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [formData, setFormData] = useState({
    categories_name: '',
    slug: '',
    description: ''
  });

  const baseUrl = process.env.REACT_APP_API_URL || 'http://localhost:8000/QuanLyBanHangFigure/api';
  const adminUrl = `${baseUrl.replace('/api', '')}/api/admin`;

  useEffect(() => {
    if (user?.role !== 'admin') {
      navigate('/');
      return;
    }
    loadCategories();
  }, [user, navigate]);

  const loadCategories = async () => {
    try {
      setLoading(true);
      const res = await axios.get(`${adminUrl}/categories/list.php`, { withCredentials: true });
      setCategories(res.data.data || res.data.products || []);
    } catch (error) {
      console.error('Error loading categories:', error);
      alert('Lỗi tải danh mục: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const generateSlug = (text) => {
    return text.toLowerCase().replace(/[^\w\s-]/g, '').replace(/\s+/g, '-');
  };

  const handleCategoryNameChange = (e) => {
    const value = e.target.value;
    setFormData(prev => ({
      ...prev,
      categories_name: value,
      slug: generateSlug(value)
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await axios.put(`${adminUrl}/categories/update.php?id_categories=${editingId}`, formData, { withCredentials: true });
        alert('Cập nhật danh mục thành công');
      } else {
        await axios.post(`${adminUrl}/categories/create.php`, formData, { withCredentials: true });
        alert('Thêm danh mục thành công');
      }
      resetForm();
      loadCategories();
    } catch (error) {
      console.error('Error saving category:', error);
      alert('Lỗi: ' + (error.response?.data?.message || error.message));
    }
  };

  const handleEdit = (category) => {
    setEditingId(category.id_categories);
    setFormData({
      categories_name: category.categories_name,
      slug: category.slug,
      description: category.description || ''
    });
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Bạn có chắc muốn xóa danh mục này?')) {
      try {
        await axios.delete(`${adminUrl}/categories/delete.php?id_categories=${id}`, { withCredentials: true });
        alert('Xóa danh mục thành công');
        loadCategories();
      } catch (error) {
        console.error('Error deleting category:', error);
        alert('Lỗi: ' + (error.response?.data?.message || error.message));
      }
    }
  };

  const resetForm = () => {
    setShowForm(false);
    setEditingId(null);
    setFormData({
      categories_name: '',
      slug: '',
      description: ''
    });
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  return (
    <div className="container-fluid mt-4 mb-5">
      <div className="row mb-4">
        <div className="col-md-6">
          <h2>Quản lý danh mục</h2>
        </div>
        <div className="col-md-6 text-end">
          <button 
            className="btn btn-primary"
            onClick={() => showForm ? resetForm() : setShowForm(true)}
          >
            {showForm ? '✕ Hủy' : '+ Thêm danh mục'}
          </button>
        </div>
      </div>

      {/* Form */}
      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5>{editingId ? 'Sửa danh mục' : 'Thêm danh mục mới'}</h5>
            <form onSubmit={handleSubmit}>
              <div className="mb-3">
                <label className="form-label">Tên danh mục *</label>
                <input
                  type="text"
                  className="form-control"
                  name="categories_name"
                  value={formData.categories_name}
                  onChange={handleCategoryNameChange}
                  required
                  placeholder="Ví dụ: Điện tử"
                />
              </div>
              <div className="mb-3">
                <label className="form-label">Slug (URL) *</label>
                <input
                  type="text"
                  className="form-control"
                  name="slug"
                  value={formData.slug}
                  onChange={handleInputChange}
                  required
                  placeholder="Ví dụ: dien-tu"
                />
              </div>
              <div className="mb-3">
                <label className="form-label">Mô tả</label>
                <textarea
                  className="form-control"
                  name="description"
                  value={formData.description}
                  onChange={handleInputChange}
                  rows="3"
                  placeholder="Mô tả danh mục"
                ></textarea>
              </div>
              <button type="submit" className="btn btn-success">
                {editingId ? 'Cập nhật' : 'Thêm'} danh mục
              </button>
            </form>
          </div>
        </div>
      )}

      {/* Categories Table */}
      <div className="card shadow-sm">
        <div className="card-body">
          {categories.length > 0 ? (
            <div className="table-responsive">
              <table className="table table-striped">
                <thead>
                  <tr>
                    <th>Mã danh mục</th>
                    <th>Tên danh mục</th>
                    <th>Slug</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  {categories.map(category => (
                    <tr key={category.id_categories}>
                      <td>#{category.id_categories}</td>
                      <td>{category.name}</td>
                      <td><code>{category.slug}</code></td>
                      <td>{category.description ? category.description.substring(0, 50) : 'N/A'}</td>
                      <td>
                        <button 
                          className="btn btn-sm btn-warning me-2"
                          onClick={() => handleEdit(category)}
                        >
                          Sửa
                        </button>
                        <button 
                          className="btn btn-sm btn-danger"
                          onClick={() => handleDelete(category.id_categories)}
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
            <p className="text-muted text-center">Không có danh mục nào</p>
          )}
        </div>
      </div>
    </div>
  );
};

export default AdminCategories;
