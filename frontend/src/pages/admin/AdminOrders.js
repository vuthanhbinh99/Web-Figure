import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import { useNavigate, useParams } from 'react-router-dom';
import { orderService } from '../../services/api';
import axios from 'axios';

const AdminOrders = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const { id_order } = useParams();
  const [orders, setOrders] = useState([]);
  const [selectedOrder, setSelectedOrder] = useState(null);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const baseUrl = process.env.REACT_APP_API_URL || 'http://localhost:8000/QuanLyBanHangFigure/api';
  const adminUrl = `${baseUrl.replace('/api', '')}/api/admin`;

  useEffect(() => {
    if (user?.role !== 'admin') {
      navigate('/');
      return;
    }
    loadOrders();
  }, [user, navigate, page]);

  const loadOrders = async () => {
    try {
      setLoading(true);
      const res = await axios.get(`${adminUrl}/orders/list.php`, {
        params: { page, limit: 10 },
        withCredentials: true
      });
      setOrders(res.data.data || res.data.products || []);
      setTotalPages(Math.ceil((res.data.pagination?.total || 0) / 10));
    } catch (error) {
      console.error('Error loading orders:', error);
    } finally {
      setLoading(false);
    }
  };

  const loadOrderDetail = async (orderId) => {
    try {
      const res = await orderService.getById(orderId);
      setSelectedOrder(res.data.data);
    } catch (error) {
      console.error('Error loading order detail:', error);
    }
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  if (selectedOrder) {
    return (
      <div className="container-fluid mt-4 mb-5">
        <button className="btn btn-secondary mb-3" onClick={() => setSelectedOrder(null)}>
          ← Quay lại
        </button>
        
        <div className="card shadow-sm">
          <div className="card-header bg-dark text-white">
            <h5 className="mb-0">Chi tiết đơn hàng #{selectedOrder.order?.id_order}</h5>
          </div>
          <div className="card-body">
            <div className="row mb-4">
              <div className="col-md-6">
                <h6>Thông tin khách hàng</h6>
                <p><strong>Tên:</strong> {selectedOrder.order?.full_name}</p>
                <p><strong>Email:</strong> {selectedOrder.order?.email}</p>
                <p><strong>Điện thoại:</strong> {selectedOrder.order?.phone}</p>
              </div>
              <div className="col-md-6">
                <h6>Thông tin đơn hàng</h6>
                <p><strong>Trạng thái:</strong> <span className="badge bg-warning">{selectedOrder.order?.status}</span></p>
                <p><strong>Tổng tiền:</strong> {(selectedOrder.order?.total || 0).toLocaleString('vi-VN')}đ</p>
                <p><strong>Ngày tạo:</strong> {new Date(selectedOrder.order?.created_at).toLocaleDateString('vi-VN')}</p>
              </div>
            </div>

            {selectedOrder.items && selectedOrder.items.length > 0 && (
              <div>
                <h6>Chi tiết sản phẩm</h6>
                <table className="table table-striped">
                  <thead>
                    <tr>
                      <th>Sản phẩm</th>
                      <th>Số lượng</th>
                      <th>Giá</th>
                      <th>Thành tiền</th>
                    </tr>
                  </thead>
                  <tbody>
                    {selectedOrder.items.map((item, idx) => (
                      <tr key={idx}>
                        <td>{item.products_name}</td>
                        <td>{item.quantity}</td>
                        <td>{(item.price || 0).toLocaleString('vi-VN')}đ</td>
                        <td>{((item.price || 0) * item.quantity).toLocaleString('vi-VN')}đ</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="container-fluid mt-4 mb-5">
      <h2 className="mb-4">Quản lý đơn hàng</h2>

      <div className="card shadow-sm">
        <div className="card-body">
          {orders.length > 0 ? (
            <div className="table-responsive">
              <table className="table table-striped">
                <thead>
                  <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  {orders.map(order => (
                    <tr key={order.id_order}>
                      <td>#{order.id_order}</td>
                      <td>{order.full_name || 'N/A'}</td>
                      <td>{(order.total || 0).toLocaleString('vi-VN')}đ</td>
                      <td>
                        <span className={`badge bg-${
                          order.status === 'pending' ? 'warning' :
                          order.status === 'confirmed' ? 'info' :
                          order.status === 'shipped' ? 'primary' :
                          order.status === 'delivered' ? 'success' :
                          'danger'
                        }`}>
                          {order.status}
                        </span>
                      </td>
                      <td>{new Date(order.created_at).toLocaleDateString('vi-VN')}</td>
                      <td>
                        <button 
                          className="btn btn-sm btn-primary"
                          onClick={() => loadOrderDetail(order.id_order)}
                        >
                          Chi tiết
                        </button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <p className="text-muted text-center">Không có đơn hàng nào</p>
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

export default AdminOrders;
