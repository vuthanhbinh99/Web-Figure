import React, { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import { orderService } from '../services/api';

const MyOrders = () => {
  const { user } = useAuth();
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (user) {
      loadOrders();
    }
  }, [user]);

  const loadOrders = async () => {
    try {
      const response = await orderService.getByUser();
      setOrders(response.data.data || response.data || []);
    } catch (error) {
      console.error('Error loading orders:', error);
      setOrders([]);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  return (
    <div className="container mt-5 mb-5">
      <h2 className="mb-4">Đơn hàng của tôi</h2>

      {Array.isArray(orders) && orders.length > 0 ? (
        <div className="table-responsive">
          <table className="table table-striped">
            <thead className="table-dark">
              <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
              </tr>
            </thead>
            <tbody>
              {Array.isArray(orders) && orders.map(order => (
                <tr key={order.id_order}>
                  <td>#{order.id_order}</td>
                  <td>{new Date(order.created_at).toLocaleDateString('vi-VN')}</td>
                  <td>{(order.total_amount || 0).toLocaleString('vi-VN')}đ</td>
                  <td>
                    <span className={`badge bg-${
                      order.status === 'pending' ? 'warning' :
                      order.status === 'confirmed' ? 'info' :
                      order.status === 'shipped' ? 'primary' :
                      order.status === 'delivered' ? 'success' :
                      order.status === 'cancelled' ? 'danger' :
                      'secondary'
                    }`}>
                      {order.status === 'pending' ? 'Chờ xác nhận' :
                       order.status === 'confirmed' ? 'Đã xác nhận' :
                       order.status === 'shipped' ? 'Đang giao' :
                       order.status === 'delivered' ? 'Đã giao' :
                       order.status === 'cancelled' ? 'Đã hủy' :
                       order.status}
                    </span>
                  </td>
                  <td>
                    <button className="btn btn-sm btn-outline-primary">
                      Xem chi tiết
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      ) : (
        <div className="alert alert-info">
          Bạn chưa có đơn hàng nào
        </div>
      )}
    </div>
  );
};

export default MyOrders;
