import React from 'react';

const Footer = () => {
  return (
    <footer className="bg-dark text-white mt-5 py-5">
      <div className="container">
        <div className="row">
          <div className="col-md-3 mb-4">
            <h5>FigureStore</h5>
            <p>Cửa hàng bán hàng trực tuyến hàng đầu với sản phẩm chất lượng cao.</p>
          </div>
          <div className="col-md-3 mb-4">
            <h5>Liên kết nhanh</h5>
            <ul className="list-unstyled">
              <li><a href="/" className="text-decoration-none text-white">Trang chủ</a></li>
              <li><a href="/store" className="text-decoration-none text-white">Sản phẩm</a></li>
              <li><a href="/cart" className="text-decoration-none text-white">Giỏ hàng</a></li>
            </ul>
          </div>
          <div className="col-md-3 mb-4">
            <h5>Chính sách</h5>
            <ul className="list-unstyled">
              <li><button className="btn btn-link text-decoration-none text-white p-0 text-start">Chính sách bảo mật</button></li>
              <li><button className="btn btn-link text-decoration-none text-white p-0 text-start">Điều khoản dịch vụ</button></li>
              <li><button className="btn btn-link text-decoration-none text-white p-0 text-start">Hỗ trợ khách hàng</button></li>
            </ul>
          </div>
          <div className="col-md-3 mb-4">
            <h5>Liên hệ</h5>
            <p>
              Email: contact@figurestore.com<br/>
              Phone: 0123 456 789<br/>
              Address: 123 Đường ABC, TP.HCM
            </p>
          </div>
        </div>
        <hr />
        <div className="text-center">
          <p>&copy; 2025 FigureStore. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
