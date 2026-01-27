import React from 'react';
import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';

const ProductCard = ({ product }) => {
  const { addToCart } = useCart();

  const handleAddToCart = () => {
    addToCart({
      id_products: product.id_products,
      name: product.name,
      price: parseFloat(product.price),
      image: product.image,
      stock: product.stock
    });
    alert('Sản phẩm đã được thêm vào giỏ hàng');
  };

  // Build image URL - database stores as "images/anh1.jpg"
  const getImageUrl = () => {
    if (!product.image) return 'https://via.placeholder.com/300x300';
    // Image is stored as "images/anh1.jpg" in database
    return `http://localhost:8000/QuanLyBanHangFigure/${product.image}`;
  };

  return (
    <div className="card h-100 shadow-sm">
      <div className="product-image-container">
        <img
          src={getImageUrl()}
          className="card-img-top"
          alt={product.name}
          style={{ width: '100%', height: '100%', objectFit: 'cover' }}
          onError={(e) => { e.target.src = 'https://via.placeholder.com/300x300'; }}
        />
      </div>
      <div className="card-body d-flex flex-column">
        <h5 className="card-title text-truncate">{product.name}</h5>
        <p className="card-text text-muted flex-grow-1" style={{ fontSize: '0.9rem' }}>
          {product.description?.substring(0, 50)}...
        </p>
        <div className="d-flex justify-content-between align-items-center">
          <span className="h5 mb-0 text-danger">{parseFloat(product.price).toLocaleString('vi-VN')}đ</span>
          <small className="text-muted">Kho: {product.stock}</small>
        </div>
      </div>
      <div className="card-footer bg-white border-top">
        <Link to={`/product/${product.id_products}`} className="btn btn-sm btn-info w-100 mb-2">
          <i className="bi bi-eye"></i> Xem chi tiết
        </Link>
        <button 
          onClick={handleAddToCart}
          disabled={product.stock <= 0}
          className="btn btn-sm btn-danger w-100"
        >
          <i className="bi bi-cart-plus"></i> Thêm vào giỏ
        </button>
      </div>
    </div>
  );
};

export default ProductCard;
