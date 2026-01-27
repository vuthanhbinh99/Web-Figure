import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

const AdminVouchers = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [vouchers, setVouchers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [formData, setFormData] = useState({
    code: '',
    discount_value: 0,
    discount_type: 'percentage',
    min_purchase: 0,
    max_uses: null,
    expire_date: ''
  });

  const baseUrl = process.env.REACT_APP_API_URL || 'http://localhost:8000/QuanLyBanHangFigure/api';
  const adminUrl = `${baseUrl.replace('/api', '')}/api/admin`;

  useEffect(() => {
    if (user?.role !== 'admin') {
      navigate('/');
      return;
    }
    loadVouchers();
  }, [user, navigate]);

  const loadVouchers = async () => {
    try {
      setLoading(true);
      const res = await axios.get(`${adminUrl}/vouchers/list.php`, { withCredentials: true });
      setVouchers(res.data.data || res.data.products || []);
    } catch (error) {
      console.error('Error loading vouchers:', error);
      alert('Lỗi tải vouchers: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: name === 'discount_value' || name === 'min_purchase' || name === 'max_uses' 
        ? (value ? parseFloat(value) : (name === 'max_uses' ? null : 0))
        : value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await axios.put(`${adminUrl}/vouchers/update.php?id_vouchers=${editingId}`, formData, { withCredentials: true });
        alert('Cập nhật voucher thành công');
      } else {
        await axios.post(`${adminUrl}/vouchers/create.php`, formData, { withCredentials: true });
        alert('Thêm voucher thành công');
      }
      resetForm();
      loadVouchers();
    } catch (error) {
      console.error('Error saving voucher:', error);
      alert('Lỗi: ' + (error.response?.data?.message || error.message));
    }
  };

  const handleEdit = (voucher) => {
    setEditingId(voucher.id_vouchers);
    setFormData({
      code: voucher.code,
      discount_value: voucher.discount_value,
      discount_type: voucher.discount_type,
      min_purchase: voucher.min_purchase || 0,
      max_uses: voucher.max_uses || null,
      expire_date: voucher.expire_date || ''
    });
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Bạn có chắc muốn xóa voucher này?')) {
      try {
        await axios.delete(`${adminUrl}/vouchers/delete.php?id_vouchers=${id}`, { withCredentials: true });
        alert('Xóa voucher thành công');
        loadVouchers();
      } catch (error) {
        console.error('Error deleting voucher:', error);
        alert('Lỗi: ' + (error.response?.data?.message || error.message));
      }
    }
  };

  const resetForm = () => {
    setShowForm(false);
    setEditingId(null);
    setFormData({
      code: '',
      discount_value: 0,
      discount_type: 'percentage',
      min_purchase: 0,
      max_uses: null,
      expire_date: ''
    });
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  return (
    <div className="container-fluid mt-4 mb-5">
      <div className="row mb-4">
        <div className="col-md-6">
          <h2>Quản lý Vouchers</h2>
        </div>
        <div className="col-md-6 text-end">
          <button 
            className="btn btn-primary"
            onClick={() => showForm ? resetForm() : setShowForm(true)}
          >
            {showForm ? '✕ Hủy' : '+ Thêm Voucher'}
          </button>
        </div>
      </div>

      {/* Form */}
      {showForm && (
        <div className="card shadow-sm mb-4">
          <div className="card-body">
            <h5>{editingId ? 'Sửa voucher' : 'Thêm voucher mới'}</h5>
            <form onSubmit={handleSubmit}>
              <div className="row">
                <div className="col-md-6 mb-3">
                  <label className="form-label">Mã Voucher *</label>
                  <input
                    type="text"
                    className="form-control"
                    name="code"
                    value={formData.code}
                    onChange={handleInputChange}
                    required
                    placeholder="Ví dụ: SUMMER20"
                    style={{ textTransform: 'uppercase' }}
                  />
                </div>
                <div className="col-md-6 mb-3">
                  <label className="form-label">Loại giảm giá *</label>
                  <select
                    className="form-control"
                    name="discount_type"
                    value={formData.discount_type}
                    onChange={handleInputChange}
                  >
                    <option value="percentage">Phần trăm (%)</option>
                    <option value="fixed">Cố định (đ)</option>
                  </select>
                </div>
              </div>
              <div className="row">
                <div className="col-md-6 mb-3">
                  <label className="form-label">Giá trị giảm giá *</label>
                  <input
                    type="number"
                    className="form-control"
                    name="discount_value"
                    value={formData.discount_value}
                    onChange={handleInputChange}
                    required
                    min="0"
                    placeholder={formData.discount_type === 'percentage' ? '20' : '100000'}
                  />
                </div>
                <div className="col-md-6 mb-3">
                  <label className="form-label">Đơn hàng tối thiểu (đ)</label>
                  <input
                    type="number"
                    className="form-control"
                    name="min_purchase"
                    value={formData.min_purchase}
                    onChange={handleInputChange}
                    min="0"
                    placeholder="0"
                  />
                </div>
              </div>
              <div className="row">
                <div className="col-md-6 mb-3">
                  <label className="form-label">Số lần sử dụng tối đa</label>
                  <input
                    type="number"
                    className="form-control"
                    name="max_uses"
                    value={formData.max_uses || ''}
                    onChange={handleInputChange}
                    min="1"
                    placeholder="Để trống nếu không giới hạn"
                  />
                </div>
                <div className="col-md-6 mb-3">
                  <label className="form-label">Ngày hết hạn *</label>
                  <input
                    type="date"
                    className="form-control"
                    name="expire_date"
                    value={formData.expire_date}
                    onChange={handleInputChange}
                    required
                  />
                </div>
              </div>
              <button type="submit" className="btn btn-success">
                {editingId ? 'Cập nhật' : 'Thêm'} voucher
              </button>
            </form>
          </div>
        </div>
      )}

      {/* Vouchers Table */}
      <div className="card shadow-sm">
        <div className="card-body">
          {vouchers.length > 0 ? (
            <div className="table-responsive">
              <table className="table table-striped">
                <thead>
                  <tr>
                    <th>Mã Voucher</th>
                    <th>Giảm giá</th>
                    <th>Điều kiện</th>
                    <th>Ngày hết hạn</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  {vouchers.map(voucher => (
                    <tr key={voucher.id_vouchers}>
                      <td><code>{voucher.code}</code></td>
                      <td>
                        {voucher.discount_type === 'percentage' 
                          ? `${voucher.discount_value}%` 
                          : `${(voucher.discount_value || 0).toLocaleString('vi-VN')}đ`
                        }
                      </td>
                      <td>
                        {voucher.min_purchase > 0 
                          ? `Tối thiểu: ${(voucher.min_purchase || 0).toLocaleString('vi-VN')}đ`
                          : 'Không giới hạn'
                        }
                      </td>
                      <td>{new Date(voucher.expire_date).toLocaleDateString('vi-VN')}</td>
                      <td>
                        <button 
                          className="btn btn-sm btn-warning me-2"
                          onClick={() => handleEdit(voucher)}
                        >
                          Sửa
                        </button>
                        <button 
                          className="btn btn-sm btn-danger"
                          onClick={() => handleDelete(voucher.id_vouchers)}
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
            <p className="text-muted text-center">Không có voucher nào</p>
          )}
        </div>
      </div>
    </div>
  );
};

export default AdminVouchers;
