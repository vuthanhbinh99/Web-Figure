import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { adminOrderService, adminProductService } from '../../services/api';

const AdminDashboard = () => {
  const { user } = useAuth();
  const navigate = useNavigate();
  const [stats, setStats] = useState({
    totalOrders: 0,
    totalProducts: 0,
    totalRevenue: 0
  });
  const [recentOrders, setRecentOrders] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    if (user?.role !== 'admin') {
      navigate('/');
      return;
    }
    
    loadDashboardData();
  }, [user, navigate]);

  const loadDashboardData = async () => {
    try {
      setLoading(true);
      const [ordersRes, productsRes] = await Promise.all([
        adminOrderService.getAll(1, 10),
        adminProductService.getAll(1, 1) // Get just count, minimal data
      ]);
      
      const orders = ordersRes.data.data || [];
      
      const totalRevenue = orders.reduce((sum, order) => sum + (parseFloat(order.total_amount) || 0), 0);
      
      setStats({
        totalOrders: ordersRes.data.pagination?.total || 0,
        totalProducts: productsRes.data.total || 0,
        totalRevenue: totalRevenue
      });
      
      setRecentOrders(orders.slice(0, 10));
    } catch (error) {
      console.error('Error loading dashboard data:', error);
      alert('Lỗi tải dữ liệu: ' + error.message);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  return (
    <div className="container-fluid mt-4 mb-5">
      <h2 className="mb-4">Dashboard</h2>

      {/* Stats Cards */}
      <div className="row mb-4">
        <div className="col-md-4 mb-3">
          <div className="card shadow-sm border-start border-primary border-5">
            <div className="card-body">
              <h6 className="card-title text-muted">Tổng đơn hàng</h6>
              <h2 className="text-primary">{stats.totalOrders}</h2>
            </div>
          </div>
        </div>
        
        <div className="col-md-4 mb-3">
          <div className="card shadow-sm border-start border-success border-5">
            <div className="card-body">
              <h6 className="card-title text-muted">Tổng sản phẩm</h6>
              <h2 className="text-success">{stats.totalProducts}</h2>
            </div>
          </div>
        </div>
        
        <div className="col-md-4 mb-3">
          <div className="card shadow-sm border-start border-danger border-5">
            <div className="card-body">
              <h6 className="card-title text-muted">Tổng doanh thu</h6>
              <h2 className="text-danger">{stats.totalRevenue.toLocaleString('vi-VN')}đ</h2>
            </div>
          </div>
        </div>
      </div>

      {/* Admin Menu */}
      <div className="row mb-4">
        <div className="col-md-12">
          <div className="card shadow-sm">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Quản lý</h5>
            </div>
            <div className="card-body">
              <div className="row g-2">
                <div className="col-md-3">
                  <Link to="/admin/products" className="btn btn-outline-primary w-100">
                    <i className="bi bi-box"></i> Sản phẩm
                  </Link>
                </div>
                <div className="col-md-3">
                  <Link to="/admin/categories" className="btn btn-outline-success w-100">
                    <i className="bi bi-tag"></i> Danh mục
                  </Link>
                </div>
                <div className="col-md-3">
                  <Link to="/admin/orders" className="btn btn-outline-info w-100">
                    <i className="bi bi-clipboard-check"></i> Đơn hàng
                  </Link>
                </div>
                <div className="col-md-3">
                  <Link to="/admin/vouchers" className="btn btn-outline-warning w-100">
                    <i className="bi bi-gift"></i> Vouchers
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Recent Orders */}
      <div className="row">
        <div className="col-md-12">
          <div className="card shadow-sm">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Đơn hàng gần đây</h5>
            </div>
            <div className="card-body">
              {recentOrders.length > 0 ? (
                <div className="table-responsive">
                  <table className="table table-striped">
                    <thead>
                      <tr>
                        <th>Mã đơn hàng</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày</th>
                        <th>Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                      {recentOrders.map(order => (
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
                            <Link to={`/admin/orders/${order.id_order}`} className="btn btn-sm btn-primary">
                              Chi tiết
                            </Link>
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
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;
