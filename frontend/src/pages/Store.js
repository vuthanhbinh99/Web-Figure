import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import { productService, categoryService } from '../services/api';
import ProductCard from '../components/ProductCard';

const Store = () => {
  const [searchParams] = useSearchParams();
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [perPage] = useState(20);

  const q = searchParams.get('q') || '';
  const category = searchParams.get('category') || '';

  useEffect(() => {
    loadCategories();
  }, []);

  // eslint-disable-next-line react-hooks/exhaustive-deps
  useEffect(() => {
    console.log('Store.js - q changed:', q, 'category changed:', category);
    loadProducts(1);
    setPage(1);
  }, [q, category]);

  const loadCategories = async () => {
    try {
      const response = await categoryService.getAll();
      setCategories(response.data.data || response.data);
    } catch (error) {
      console.error('Error loading categories:', error);
    }
  };

  const loadProducts = async (pageNum = 1) => {
    try {
      setLoading(true);
      let response;
      
      if (q) {
        console.log('Searching for:', q);
        response = await productService.search(q, pageNum, perPage);
      } else if (category) {
        console.log('Loading category:', category);
        response = await productService.getByCategory(category, pageNum, perPage);
      } else {
        console.log('Loading all products');
        response = await productService.getAll(pageNum, perPage);
      }
      
      console.log('API Response:', response.data);
      setProducts(response.data.data || response.data || []);
      setTotalPages(response.data.pages || 1);
      setLoading(false);
    } catch (error) {
      console.error('Error loading products:', error);
      setProducts([]);
      setLoading(false);
    }
  };

  const handlePageChange = (newPage) => {
    setPage(newPage);
    loadProducts(newPage);
    window.scrollTo(0, 0);
  };

  if (loading) {
    return <div className="text-center p-5"><div className="spinner-border" role="status"></div></div>;
  }

  return (
    <div className="container mt-5">
      <div className="row">
        {/* Sidebar */}
        <div className="col-md-3 mb-4">
          <div className="card shadow-sm">
            <div className="card-header bg-dark text-white">
              <h5 className="mb-0">Danh mục</h5>
            </div>
            <div className="card-body">
              <a href="/store" className={`d-block text-decoration-none mb-2 ${!category ? 'text-danger fw-bold' : ''}`}>
                Tất cả sản phẩm
              </a>
              {categories.map(cat => (
                <a 
                  key={cat.id_categories}
                  href={`/store?category=${cat.slug}`}
                  className={`d-block text-decoration-none mb-2 ${category === cat.slug ? 'text-danger fw-bold' : ''}`}
                >
                  {cat.name}
                </a>
              ))}
            </div>
          </div>
        </div>

        {/* Products */}
        <div className="col-md-9">
          <div className="mb-4">
            <h3>
              {q && `Kết quả tìm kiếm: "${q}"`}
              {category && !q && `Danh mục: ${categories.find(c => c.slug === category)?.name || category}`}
              {!q && !category && 'Tất cả sản phẩm'}
            </h3>
            <p className="text-muted">Tìm thấy {products.length} sản phẩm</p>
          </div>

          {products.length > 0 ? (
            <>
              <div className="row g-4">
                {products.map(product => (
                  <div key={product.id_products} className="col-md-4 col-sm-6">
                    <ProductCard product={product} />
                  </div>
                ))}
              </div>

              {/* Pagination */}
              {totalPages > 1 && (
                <nav className="mt-5" aria-label="Page navigation">
                  <ul className="pagination justify-content-center">
                    <li className={`page-item ${page === 1 ? 'disabled' : ''}`}>
                      <button 
                        className="page-link" 
                        onClick={() => handlePageChange(page - 1)}
                        disabled={page === 1}
                      >
                        Trước
                      </button>
                    </li>
                    
                    {Array.from({ length: totalPages }, (_, i) => i + 1).map(pageNum => (
                      <li key={pageNum} className={`page-item ${page === pageNum ? 'active' : ''}`}>
                        <button 
                          className="page-link"
                          onClick={() => handlePageChange(pageNum)}
                        >
                          {pageNum}
                        </button>
                      </li>
                    ))}
                    
                    <li className={`page-item ${page === totalPages ? 'disabled' : ''}`}>
                      <button 
                        className="page-link"
                        onClick={() => handlePageChange(page + 1)}
                        disabled={page === totalPages}
                      >
                        Tiếp
                      </button>
                    </li>
                  </ul>
                </nav>
              )}
            </>
          ) : (
            <div className="alert alert-info text-center">
              Không tìm thấy sản phẩm nào
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Store;
