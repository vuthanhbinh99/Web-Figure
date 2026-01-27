import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { orderService } from '../services/api';

const Checkout = () => {
  const navigate = useNavigate();
  const { user } = useAuth();
  const { cart, cartTotal, clearCart } = useCart();
  
  const [formData, setFormData] = useState({
    full_name: user?.full_name || '',
    phone: '',
    address: '',
    payment_method: 'momo'
  });
  
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  if (!user) {
    return (
      <div className="container mt-5">
        <div className="alert alert-warning text-center">
          <h4>Vui lòng đăng nhập để tiếp tục thanh toán</h4>
        </div>
      </div>
    );
  }

  if (cart.length === 0) {
    return (
      <div className="container mt-5">
        <div className="alert alert-info text-center">
          <h4>Giỏ hàng của bạn trống</h4>
        </div>
      </div>
    );
  }

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!formData.full_name || !formData.phone || !formData.address) {
      setError('Vui lòng điền đầy đủ thông tin');
      return;
    }

    try {
      setLoading(true);
      const response = await orderService.create(
        formData.payment_method,
        formData.full_name,
        formData.phone,
        formData.address
      );

      if (response.data.status === 'success') {
        clearCart();
        navigate(`/success?order_id=${response.data.order_id}`);
      } else {
        setError(response.data.message);
      }
    } catch (err) {
      setError(err.response?.data?.message || 'Lỗi tạo đơn hàng. Vui lòng thử lại.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="container mt-5 mb-5">
      <h2 className="mb-4">Thanh toán</h2>

      {error && <div className="alert alert-danger">{error}</div>}

      <div className="row">
        <div className="col-md-8">
          <div className="card shadow-sm mb-4">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Thông tin giao hàng</h5>
            </div>
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label">Tên người nhận</label>
                  <input
                    type="text"
                    className="form-control"
                    name="full_name"
                    value={formData.full_name}
                    onChange={handleChange}
                    required
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
                    required
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label">Địa chỉ giao hàng</label>
                  <textarea
                    className="form-control"
                    name="address"
                    rows="3"
                    value={formData.address}
                    onChange={handleChange}
                    required
                  ></textarea>
                </div>

                <div className="mb-4">
                  <label className="form-label">Phương thức thanh toán</label>
                  <div>
                    <div className="form-check">
                      <input
                        className="form-check-input"
                        type="radio"
                        name="payment_method"
                        id="momo"
                        value="momo"
                        checked={formData.payment_method === 'momo'}
                        onChange={handleChange}
                      />
                      <label className="form-check-label" htmlFor="momo">
                        Ví Momo
                      </label>
                    </div>
                    <div className="form-check">
                      <input
                        className="form-check-input"
                        type="radio"
                        name="payment_method"
                        id="bank"
                        value="bank"
                        checked={formData.payment_method === 'bank'}
                        onChange={handleChange}
                      />
                      <label className="form-check-label" htmlFor="bank">
                        Chuyển khoản ngân hàng
                      </label>
                    </div>
                    <div className="form-check">
                      <input
                        className="form-check-input"
                        type="radio"
                        name="payment_method"
                        id="cod"
                        value="cod"
                        checked={formData.payment_method === 'cod'}
                        onChange={handleChange}
                      />
                      <label className="form-check-label" htmlFor="cod">
                        Thanh toán khi nhận hàng
                      </label>
                    </div>
                  </div>
                </div>

                <button 
                  type="submit" 
                  className="btn btn-danger btn-lg w-100"
                  disabled={loading}
                >
                  {loading ? 'Đang xử lý...' : 'Đặt hàng'}
                </button>
              </form>
            </div>
          </div>
        </div>

        <div className="col-md-4">
          <div className="card shadow-sm sticky-top">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Đơn hàng của bạn</h5>
            </div>
            <div className="card-body">
              <div className="mb-3">
                <h6 className="mb-3">Chi tiết sản phẩm:</h6>
                {cart.map(item => (
                  <div key={item.id_products} className="d-flex justify-content-between mb-2 pb-2 border-bottom">
                    <div>
                      <span>{item.name}</span><br/>
                      <small className="text-muted">x{item.quantity}</small>
                    </div>
                    <span>{((item.price || 0) * item.quantity).toLocaleString('vi-VN')}đ</span>
                  </div>
                ))}
              </div>

              <hr />

              <div className="d-flex justify-content-between mb-2">
                <span>Tạm tính:</span>
                <span>{cartTotal.toLocaleString('vi-VN')}đ</span>
              </div>

              <div className="d-flex justify-content-between mb-3">
                <span>Vận chuyển:</span>
                <span>Miễn phí</span>
              </div>

              <hr />

              <div className="d-flex justify-content-between">
                <h5 className="mb-0">Tổng cộng:</h5>
                <h5 className="mb-0 text-danger">{cartTotal.toLocaleString('vi-VN')}đ</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Checkout;
