<?php
return [
 // Bật sau khi cửa hàng xác nhận có thể phục vụ. Chưa kết nối API vận chuyển.
 'express_enabled'=>env('PETIO_EXPRESS_ENABLED',false),
 'express_fee'=>60000,
 'express_provinces'=>['Hồ Chí Minh','TP. Hồ Chí Minh','Thành phố Hồ Chí Minh','Hà Nội','Thành phố Hà Nội'],
];
