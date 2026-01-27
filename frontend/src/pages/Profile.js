import React, { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import { userService } from '../services/api';

const Profile = () => {
  const { user } = useAuth();
  const [formData, setFormData] = useState({
    full_name: '',
    email: '',
    phone: '',
    address: ''
  });
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState('');

  // eslint-disable-next-line react-hooks/exhaustive-deps
  useEffect(() => {
    if (user) {
      loadProfile();
    }
    setLoading(false);
  }, [user]);

  const loadProfile = async () => {
    try {
      const response = await userService.getById(user.id_users);
      const userData = response.data.data || response.data;
      setFormData({
        full_name: userData.full_name || '',
        email: userData.email || '',
        phone: userData.phone || '',
        address: userData.address || ''
      });
    } catch (error) {
      console.error('Error loading profile:', error);
    }
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      setSaving(true);
      await userService.update(
        user.id_users,
        formData.full_name,
        formData.email,
        formData.phone,
        formData.address
      );
      setMessage('Cập nhật hồ sơ thành công!');
      setTimeout(() => setMessage(''), 3000);
    } catch (error) {
      setMessage('Lỗi cập nhật hồ sơ. Vui lòng thử lại.');
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  return (
    <div className="container mt-5 mb-5">
      <h2 className="mb-4">Hồ sơ của tôi</h2>

      {message && (
        <div className={`alert alert-${message.includes('lỗi') ? 'danger' : 'success'}`}>
          {message}
        </div>
      )}

      <div className="row">
        <div className="col-md-6 mb-4">
          <div className="card shadow-sm">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Thông tin cá nhân</h5>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label">Tên đầy đủ</label>
                  <input
                    type="text"
                    className="form-control"
                    name="full_name"
                    value={formData.full_name}
                    onChange={handleChange}
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">Email</label>
                  <input
                    type="email"
                    className="form-control"
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">Số điện thoại</label>
                  <input
                    type="tel"
                    className="form-control"
                    name="phone"
                    value={formData.phone}
                    onChange={handleChange}
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">Địa chỉ</label>
                  <textarea
                    className="form-control"
                    name="address"
                    rows="3"
                    value={formData.address}
                    onChange={handleChange}
                  ></textarea>
                </div>

                <button 
                  type="submit" 
                  className="btn btn-danger w-100"
                  disabled={saving}
                >
                  {saving ? 'Đang cập nhật...' : 'Cập nhật thông tin'}
                </button>
              </form>
            </div>
          </div>
        </div>

        <div className="col-md-6">
          <div className="card shadow-sm">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Thông tin tài khoản</h5>
            </div>
            <div className="card-body">
              <p><strong>Tên đăng nhập:</strong> {user?.username}</p>
              <p><strong>Email:</strong> {formData.email || user?.email || 'N/A'}</p>
              <p><strong>Tên đầy đủ:</strong> {formData.full_name || user?.full_name || 'N/A'}</p>
              <p><strong>Vai trò:</strong> {user?.role === 'admin' ? 'Quản trị viên' : 'Khách hàng'}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Profile;
