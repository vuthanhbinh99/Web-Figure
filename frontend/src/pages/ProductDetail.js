import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { productService, reviewService } from '../services/api';
import { useCart } from '../context/CartContext';

const ProductDetail = () => {
  const { id_products } = useParams();
  const { addToCart } = useCart();
  const [product, setProduct] = useState(null);
  const [reviews, setReviews] = useState([]);
  const [quantity, setQuantity] = useState(1);
  const [loading, setLoading] = useState(true);
  const [newReview, setNewReview] = useState({ rating: 5, comment: '' });

  // eslint-disable-next-line react-hooks/exhaustive-deps
  useEffect(() => {
    loadProduct();
    loadReviews();
  }, [id_products]);

  const loadProduct = async () => {
    try {
      const response = await productService.getById(id_products);
      setProduct(response.data.data || response.data);
      setLoading(false);
    } catch (error) {
      console.error('Error loading product:', error);
      setLoading(false);
    }
  };

  const getImageUrl = () => {
    if (!product || !product.image) return 'https://via.placeholder.com/500';
    // Image is stored as "images/anh1.jpg" in database
    return `http://localhost:8000/QuanLyBanHangFigure/${product.image}`;
  };

  const loadReviews = async () => {
    try {
      const response = await reviewService.getByProduct(id_products);
      setReviews(response.data.data || response.data || []);
    } catch (error) {
      console.error('Error loading reviews:', error);
    }
  };

  const handleAddToCart = () => {
    addToCart({
      id_products: product.id_products,
      name: product.name,
      price: parseFloat(product.price),
      image: product.image,
      stock: product.stock,
      quantity: quantity
    });
    alert('Sản phẩm đã được thêm vào giỏ hàng');
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border"></div></div>;
  }

  if (!product) {
    return <div className="alert alert-danger">Sản phẩm không tồn tại</div>;
  }

  return (
    <div className="container mt-5 mb-5">
      <div className="row">
        <div className="col-md-5 mb-4">
          <img 
            src={getImageUrl()} 
            alt={product.name}
            className="img-fluid rounded"
            style={{ maxHeight: '500px', objectFit: 'cover' }}
            onError={(e) => { e.target.src = 'https://via.placeholder.com/500'; }}
          />
        </div>

        <div className="col-md-7">
          <h2 className="mb-3">{product.name}</h2>
          
          <div className="mb-4">
            <span className="h4 text-danger me-3">{parseFloat(product.price).toLocaleString('vi-VN')}đ</span>
            <span className={`badge ${product.stock > 0 ? 'bg-success' : 'bg-danger'}`}>
              {product.stock > 0 ? `Còn ${product.stock}` : 'Hết hàng'}
            </span>
          </div>

          <div className="card mb-4">
            <div className="card-body">
              <h5 className="card-title">Mô tả sản phẩm</h5>
              <p className="card-text">{product.description}</p>
            </div>
          </div>

          <div className="card">
            <div className="card-body">
              <div className="mb-3">
                <label className="form-label">Số lượng:</label>
                <div className="input-group" style={{ width: '150px' }}>
                  <button 
                    className="btn btn-outline-secondary"
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  >
                    −
                  </button>
                  <input 
                    type="text"
                    className="form-control text-center"
                    value={quantity}
                    readOnly
                  />
                  <button 
                    className="btn btn-outline-secondary"
                    onClick={() => setQuantity(quantity + 1)}
                  >
                    +
                  </button>
                </div>
              </div>

              <button 
                onClick={handleAddToCart}
                disabled={product.stock <= 0}
                className="btn btn-danger btn-lg w-100"
              >
                <i className="bi bi-cart-plus"></i> Thêm vào giỏ hàng
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Reviews Section */}
      <div className="row mt-5">
        <div className="col-md-8">
          <h4 className="mb-4">Đánh giá sản phẩm ({reviews.length})</h4>
          
          {reviews.length > 0 ? (
            <div>
              {reviews.map((review, index) => (
                <div key={index} className="card mb-3">
                  <div className="card-body">
                    <div className="d-flex justify-content-between mb-2">
                      <h6 className="card-title mb-0">{review.full_name}</h6>
                      <div className="text-warning">
                        {'⭐'.repeat(review.rating)}
                      </div>
                    </div>
                    <p className="card-text">{review.comment}</p>
                    <small className="text-muted">{new Date(review.created_at).toLocaleDateString('vi-VN')}</small>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-muted">Chưa có đánh giá nào</p>
          )}
        </div>

        <div className="col-md-4">
          <div className="card">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Viết đánh giá</h5>
            </div>
            <div className="card-body">
              <form onSubmit={(e) => {
                e.preventDefault();
                // Submit review logic here
                setNewReview({ rating: 5, comment: '' });
              }}>
                <div className="mb-3">
                  <label className="form-label">Đánh giá:</label>
                  <select 
                    className="form-select"
                    value={newReview.rating}
                    onChange={(e) => setNewReview({...newReview, rating: e.target.value})}
                  >
                    {[5,4,3,2,1].map(n => (
                      <option key={n} value={n}>{n} sao</option>
                    ))}
                  </select>
                </div>
                <div className="mb-3">
                  <label className="form-label">Bình luận:</label>
                  <textarea 
                    className="form-control"
                    rows="4"
                    value={newReview.comment}
                    onChange={(e) => setNewReview({...newReview, comment: e.target.value})}
                    placeholder="Chia sẻ trải nghiệm của bạn..."
                  ></textarea>
                </div>
                <button type="submit" className="btn btn-danger w-100">Gửi đánh giá</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetail;
