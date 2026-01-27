import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { productService, categoryService } from '../services/api';
import ProductCard from '../components/ProductCard';

const Home = () => {
  const [featuredProducts, setFeaturedProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      const [productsRes, categoriesRes] = await Promise.all([
        productService.getFeatured(),
        categoryService.getAll()
      ]);
      setFeaturedProducts(productsRes.data.data || productsRes.data);
      setCategories(categoriesRes.data.data || categoriesRes.data);
    } catch (error) {
      console.error('Error loading data:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border" role="status"><span className="visually-hidden">Loading...</span></div></div>;
  }

  return (
    <div className="container mt-5">
      {/* Hero Section */}
      <div className="row bg-light rounded-3 p-5 align-items-center mb-5">
        <div className="col-md-6">
          <h1 className="display-4 fw-bold">FigureStore</h1>
          <p className="lead">Chào mừng bạn đến với cửa hàng bán hàng trực tuyến hàng đầu với những sản phẩm chất lượng cao nhất.</p>
          <Link to="/store" className="btn btn-danger btn-lg">
            <i className="bi bi-shop"></i> Mua sắm ngay
          </Link>
        </div>
        <div className="col-md-6 text-center">
          <i className="bi bi-shop" style={{ fontSize: '120px', color: '#dc3545' }}></i>
        </div>
      </div>

      {/* Banner Carousel */}
      <div className="mb-5">
        <div id="bannerCarousel" className="carousel slide rounded-3 overflow-hidden shadow-lg" data-bs-ride="carousel">
          <div className="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" className="active" aria-current="true"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
          </div>
          <div className="carousel-inner" style={{ minHeight: '600px' }}>
            <div className="carousel-item active">
              <img src="http://localhost:8000/QuanLyBanHangFigure/images/banner.jpg" className="d-block w-100" alt="Banner 1" style={{ objectFit: 'cover', height: '600px' }} />
            </div>
            <div className="carousel-item">
              <img src="http://localhost:8000/QuanLyBanHangFigure/images/banner1.jpg" className="d-block w-100" alt="Banner 2" style={{ objectFit: 'cover', height: '600px' }} />
            </div>
            <div className="carousel-item">
              <img src="http://localhost:8000/QuanLyBanHangFigure/images/banner2.jpg" className="d-block w-100" alt="Banner 3" style={{ objectFit: 'cover', height: '600px' }} />
            </div>
          </div>
          <button className="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span className="carousel-control-prev-icon" aria-hidden="true"></span>
            <span className="visually-hidden">Previous</span>
          </button>
          <button className="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span className="carousel-control-next-icon" aria-hidden="true"></span>
            <span className="visually-hidden">Next</span>
          </button>
        </div>
      </div>

      {/* Categories Section */}
      <div className="mb-5">
        <h2 className="mb-4">Danh mục sản phẩm</h2>
        <div className="row g-3">
          {categories.slice(0, 6).map(category => (
            <div key={category.id_categories} className="col-md-2 col-sm-4 col-6">
              <Link 
                to={`/store?category=${category.slug}`}
                className="text-decoration-none"
              >
                <div className="card h-100 text-center border-0 shadow-sm">
                  <div className="card-body">
                    <i className="bi bi-tag-fill" style={{ fontSize: '32px', color: '#dc3545' }}></i>
                    <h6 className="card-title mt-2">{category.name}</h6>
                  </div>
                </div>
              </Link>
            </div>
          ))}
        </div>
      </div>

      {/* Featured Products Section */}
      <div className="mb-5">
        <h2 className="mb-4">Sản phẩm nổi bật</h2>
        <div className="row g-4">
          {featuredProducts.map(product => (
            <div key={product.id_products} className="col-md-3 col-sm-6">
              <ProductCard product={product} />
            </div>
          ))}
        </div>
      </div>

      {/* Features Section */}
      <div className="row bg-light rounded-3 p-5 mb-5">
        <div className="col-md-4 text-center mb-4 mb-md-0">
          <i className="bi bi-truck" style={{ fontSize: '48px', color: '#dc3545' }}></i>
          <h5 className="mt-3">Giao hàng miễn phí</h5>
          <p>Miễn phí vận chuyển cho đơn hàng trên 500.000đ</p>
        </div>
        <div className="col-md-4 text-center mb-4 mb-md-0">
          <i className="bi bi-shield-check" style={{ fontSize: '48px', color: '#dc3545' }}></i>
          <h5 className="mt-3">Thanh toán an toàn</h5>
          <p>Hỗ trợ nhiều phương thức thanh toán</p>
        </div>
        <div className="col-md-4 text-center">
          <i className="bi bi-arrow-counterclockwise" style={{ fontSize: '48px', color: '#dc3545' }}></i>
          <h5 className="mt-3">Hoàn trả dễ dàng</h5>
          <p>30 ngày hoàn trả tiền nếu không hài lòng</p>
        </div>
      </div>
    </div>
  );
};

export default Home;
