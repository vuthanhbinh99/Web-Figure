import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { categoryService } from '../services/api';

const Navbar = () => {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const { cartCount } = useCart();
  const [categories, setCategories] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');

  useEffect(() => {
    loadCategories();
  }, []);

  const loadCategories = async () => {
    try {
      const response = await categoryService.getAll();
      setCategories(response.data.data || response.data);
    } catch (error) {
      console.error('Error loading categories:', error);
    }
  };

  const handleSearch = (e) => {
    e.preventDefault();
    const trimmedQuery = searchQuery.trim();
    if (trimmedQuery) {
      navigate(`/store?q=${encodeURIComponent(trimmedQuery)}`);
      setSearchQuery('');
    }
  };

  const handleLogout = () => {
    logout();
    navigate('/');
  };

  return (
    <nav className="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div className="container-fluid px-3">
        <Link className="navbar-brand fw-bold me-3" to="/">FigureStore</Link>

        <form className="form-search d-none d-lg-flex me-auto" onSubmit={handleSearch} style={{ maxWidth: '350px' }}>
          <input
            className="form-control form-control-sm me-2"
            type="search"
            placeholder="Tìm kiếm sản phẩm..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
          />
          <button className="btn btn-outline-light btn-sm" type="submit">Tìm</button>
        </form>

        <button className="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
          <span className="navbar-toggler-icon"></span>
        </button>

        <div className="collapse navbar-collapse" id="menu">
          <form className="form-search d-lg-none w-100 mb-3" onSubmit={handleSearch}>
            <div className="input-group">
              <input
                className="form-control"
                type="search"
                placeholder="Tìm kiếm sản phẩm..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
              />
              <button className="btn btn-outline-light" type="submit">Tìm</button>
            </div>
          </form>

          <ul className="navbar-nav ms-auto align-items-center">
            <li className="nav-item"><Link className="nav-link" to="/">Trang chủ</Link></li>
            
            <li className="nav-item dropdown">
              <button className="nav-link dropdown-toggle btn btn-link text-decoration-none" id="cateMenu" data-bs-toggle="dropdown">
                Danh mục
              </button>
              <ul className="dropdown-menu">
                {categories.map(cate => (
                  <li key={cate.id_categories}>
                    <Link className="dropdown-item" to={`/store?category=${cate.slug}`}>
                      {cate.name}
                    </Link>
                  </li>
                ))}
              </ul>
            </li>
            
            <li className="nav-item"><Link className="nav-link" to="/store">Sản phẩm</Link></li>

            {user?.role === 'admin' && (
              <li className="nav-item">
                <Link className="nav-link btn btn-outline-warning btn-sm ms-2 text-warning" to="/admin/dashboard">
                  <i className="bi bi-speedometer2 me-1"></i>Dashboard Admin
                </Link>
              </li>
            )}

            <li className="nav-item position-relative">
              <Link className="nav-link" to="/cart">
                <i className="bi bi-cart3" style={{ fontSize: '22px' }}></i>
                {cartCount > 0 && (
                  <span className="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {cartCount}
                  </span>
                )}
              </Link>
            </li>

            {user ? (
              <>
                <li className="nav-item dropdown">
                  <button className="nav-link dropdown-toggle btn btn-link text-decoration-none" id="userMenu" data-bs-toggle="dropdown">
                    <i className="bi bi-person-circle" style={{ fontSize: '22px' }}></i>
                  </button>
                  <ul className="dropdown-menu dropdown-menu-end">
                    <li><span className="dropdown-item">{user.full_name || user.username}</span></li>
                    <li><hr className="dropdown-divider" /></li>
                    {user.role === 'admin' && (
                      <>
                        <li><Link className="dropdown-item" to="/admin/dashboard">Quản lý</Link></li>
                        <li><hr className="dropdown-divider" /></li>
                      </>
                    )}
                    <li><Link className="dropdown-item" to="/profile">Hồ sơ</Link></li>
                    <li><Link className="dropdown-item" to="/my-orders">Đơn hàng của tôi</Link></li>
                    <li><hr className="dropdown-divider" /></li>
                    <li>
                      <button className="dropdown-item" onClick={handleLogout}>
                        Đăng xuất
                      </button>
                    </li>
                  </ul>
                </li>
              </>
            ) : (
              <>
                <li className="nav-item"><Link className="nav-link" to="/login">Đăng nhập</Link></li>
                <li className="nav-item"><Link className="nav-link" to="/register">Đăng ký</Link></li>
              </>
            )}
          </ul>
        </div>
      </div>
    </nav>
  );
};

export default Navbar;
