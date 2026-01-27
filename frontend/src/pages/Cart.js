import React from 'react';
import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const Cart = () => {
  const { cart, removeFromCart, updateQuantity, cartTotal } = useCart();

  const getImageUrl = (image) => {
    if (!image) return 'https://via.placeholder.com/80';
    // Image is stored as "images/anh1.jpg" in database
    return `http://localhost:8000/QuanLyBanHangFigure/${image}`;
  };

  if (cart.length === 0) {
    return (
      <div className="container mt-5">
        <div className="alert alert-info text-center p-5">
          <h4>Giỏ hàng của bạn trống</h4>
          <p>Hãy tiếp tục mua sắm để thêm sản phẩm vào giỏ hàng</p>
          <Link to="/store" className="btn btn-danger">
            Tiếp tục mua sắm
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="container mt-5 mb-5">
      <h2 className="mb-4">Giỏ hàng của bạn</h2>
      
      <div className="row">
        <div className="col-md-8">
          <div className="card shadow-sm">
            <div className="table-responsive">
              <table className="table mb-0">
                <thead className="table-dark">
                  <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng cộng</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  {cart.map(item => (
                    <tr key={item.id_products}>
                      <td>
                        <div className="d-flex align-items-center">
                          <img 
                            src={getImageUrl(item.image)} 
                            alt={item.name}
                            style={{ width: '60px', height: '60px', objectFit: 'cover', marginRight: '10px' }}
                            onError={(e) => { e.target.src = 'https://via.placeholder.com/80'; }}
                          />
                          <div>
                            <h6 className="mb-0">{item.name}</h6>
                          </div>
                        </div>
                      </td>
                      <td>{(item.price || 0).toLocaleString('vi-VN')}đ</td>
                      <td>
                        <div className="input-group" style={{ width: '100px' }}>
                          <button 
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => updateQuantity(item.id_products, item.quantity - 1)}
                          >
                            −
                          </button>
                          <input 
                            type="text"
                            className="form-control text-center"
                            value={item.quantity}
                            readOnly
                          />
                          <button 
                            className="btn btn-sm btn-outline-secondary"
                            onClick={() => updateQuantity(item.id_products, item.quantity + 1)}
                          >
                            +
                          </button>
                        </div>
                      </td>
                      <td className="fw-bold">
                        {((item.price || 0) * item.quantity).toLocaleString('vi-VN')}đ
                      </td>
                      <td>
                        <button 
                          className="btn btn-sm btn-danger"
                          onClick={() => removeFromCart(item.id_products)}
                        >
                          <i className="bi bi-trash"></i> Xóa
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div className="col-md-4">
          <div className="card shadow-sm">
            <div className="card-body">
              <h5 className="card-title mb-4">Tóm tắt đơn hàng</h5>
              
              <div className="d-flex justify-content-between mb-3">
                <span>Tạm tính:</span>
                <span>{cartTotal.toLocaleString('vi-VN')}đ</span>
              </div>
              
              <div className="d-flex justify-content-between mb-3">
                <span>Phí vận chuyển:</span>
                <span>Miễn phí</span>
              </div>

              <div className="d-flex justify-content-between mb-4">
                <span>Giảm giá:</span>
                <span>0đ</span>
              </div>

              <hr />

              <div className="d-flex justify-content-between mb-4">
                <h5 className="mb-0">Tổng cộng:</h5>
                <h5 className="mb-0 text-danger">{cartTotal.toLocaleString('vi-VN')}đ</h5>
              </div>

              <Link to="/checkout" className="btn btn-danger w-100 mb-2">
                <i className="bi bi-credit-card"></i> Thanh toán
              </Link>

              <Link to="/store" className="btn btn-outline-secondary w-100">
                Tiếp tục mua sắm
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Cart;
