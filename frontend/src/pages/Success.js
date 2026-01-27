import React from 'react';
import { Link, useSearchParams } from 'react-router-dom';

const Success = () => {
  const [searchParams] = useSearchParams();
  const orderId = searchParams.get('order_id');

  return (
    <div className="container mt-5 mb-5">
      <div className="row justify-content-center">
        <div className="col-md-6 text-center">
          <div className="card shadow">
            <div className="card-body p-5">
              <i className="bi bi-check-circle" style={{ fontSize: '80px', color: '#28a745' }}></i>
              <h2 className="mt-4 mb-3">Đơn hàng thành công!</h2>
              <p className="text-muted mb-4">
                Cảm ơn bạn đã mua hàng. Chúng tôi sẽ sớm xác nhận đơn hàng của bạn.
              </p>
              
              {orderId && (
                <div className="alert alert-info mb-4">
                  <strong>Mã đơn hàng:</strong> #{orderId}
                </div>
              )}

              <p className="mb-4">
                Bạn sẽ nhận được email xác nhận. Vui lòng kiểm tra thư rơi rác nếu không thấy email.
              </p>

              <div className="btn-group w-100" role="group">
                <Link to="/" className="btn btn-primary">
                  <i className="bi bi-house"></i> Trang chủ
                </Link>
                <Link to="/store" className="btn btn-success">
                  <i className="bi bi-shop"></i> Tiếp tục mua sắm
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Success;
