@extends('layouts.app')

@section('title', 'Dịch vụ spa – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/spa.css') }}" />
@endpush

@section('content')
    <!-- main -->
    <div class="main">
      <div class="content">
        <h1 class="title reveal">Dịch vụ spa chuyên nghiệp cho thú cưng tại Petio!</h1>
        <div>
          <h1>PETIO SPA - ĐỊA CHỈ ĐÁNG TIN CẬY CỦA CÁC BẠN NUÔI THÚ CƯNG:</h1>
          <p>
            Dịch vụ spa trọn gói cho chó mèo ở Petio Spa được làm theo quy trình
            chăm sóc toàn diện từ A - Z. Quy trình tắm vệ sinh bao gồm 12 bước:
          </p>
        </div>

        <img src="{{ asset('assets') }}/file_anh/lI37NZi.jpeg" alt="" class="anh1 reveal" />

        <img
          src="{{ asset('assets') }}/file_anh/img_6304_361223fe49aa441dada2f926be9faf17_grande.jpg"
          class="anh2 reveal"
          alt=""
        />

        <p>
          Với đội ngũ nhân viên giàu kinh nghiệm, được đào tạo bài bản về kiến
          thức làm đẹp cũng như chăm sóc sức khỏe tại Petio. Thú cưng của bạn sẽ
          được tận hưởng dịch vụ chăm sóc làm đẹp chất lượng và chu đáo nhất.
          Các bé sẽ được theo dõi sức khỏe xuyên suốt quá trình spa và có nước
          uống, bữa ăn phụ trong thời gian chờ.
        </p>

        <h1>I. NHỮNG CAM KẾT TẠI PETIO SPA VỚI KHÁCH HÀNG:</h1>

        <h2>
          1. Đội ngũ nhân viên tại Petio làm việc nhiệt huyết và trách nhiệm với
          công việc:
        </h2>
        <p>
          - Với tiêu chí đặt khách hàng lên hàng đầu, Petio cố gắng để tất cả
          Khách hàng đều cảm thấy thoải mái và hài lòng khi đến trải nghiệm dịch
          vụ.
        </p>
        <p>
          - Bên cạnh việc tư vấn dịch vụ spa, các bạn nhân viên luôn sẵn lòng
          chia sẻ kinh nghiệm chăm sóc khi thú cưng của bạn gặp các vấn đề về
          sức khỏe. Các dịch vụ và
          <a href="{{ route('shop') }}">sản phẩm phân phối</a> tại Petio luôn được cam kết
          về chất lượng, trách nhiệm khi đến tay Khách hàng.
        </p>
        <h2>2. Giá dịch vụ rẻ và vẫn chất lượng nhất:</h2>
        <p>
          - Chi phí cho dịch vụ spa chó mèo tại Petio luôn đảm bảo hợp lý và
          cạnh tranh nhất hiện nay để tất cả thú cưng đều có thể đến và trải
          nghiệm dịch vụ.
        </p>
        <p>
          - Bên cạnh chi phí hợp lý, còn có rất nhiều ưu đãi kèm theo ví dụ như:
          Thứ 4 vui vẻ giảm ngay 15% giá dịch vụ, đi spa càng đông giảm càng
          nhiều đến 20%, miễn phí đưa đón thú cưng, giảm giá vào các ngày trong
          tuần...
        </p>
        <p>
          - Petio không ngừng phát triển trình độ và tay nghề của nhân viên spa
          để đem lại kết quả tốt nhất khi làm dịch vụ. Tại Petio, chúng tôi
          không cam kết mức giá dịch vụ rẻ nhất nhưng với mức giá đó, đảm bào
          Khách hàng sẽ hài lòng nhất khi chọn dịch vụ tại Petio.
        </p>
        <img
          src="{{ asset('assets') }}/file_anh/img_6305_bf15092c4b5d430cb4c52f7e2a263592_grande.jpg"
          class="anh3 reveal"
          alt=""
        />
        <h1>II. TẠI SAO NÊN TẮM SPA CHO CHÓ MÈO ĐỊNH KỲ VÀ THƯỜNG XUYÊN:</h1>
        <p>
          <strong>Giữ bộ lông và da luôn sạch sẽ:</strong> Đa số các bạn chó mèo
          đều thích chạy nhảy chơi đùa nên lông thường bám bẩn, việc tắm thường
          xuyên sẽ giúp loại bỏ bụi bẩn, mùi hôi khó chịu trên da và lông chó
          mèo.
        </p>
        <p>
          <strong>Phòng chống ký sinh trùng và các bệnh ngoài da:</strong> Khí
          hậu ở nước ta là nóng ẩm, đây là điều kiện thích hợp để vi khuẩn và
          các loài ký sinh như ve, rận, bọ chét phát triển trên cơ thể chó mèo.
          Vì vậy, việc tắm rửa để giữ bộ lông sạch thoáng là rất cần thiết để
          phòng tránh ve rận ký sinh trên da các bé.
        </p>
        <p>
          <strong
            >Giảm tình trạng rụng lông và phòng các bệnh về đường ruột:</strong
          >
          Khi tắm cho chó mèo, bộ lông sẽ được chải chuốt để loại bỏ những phần
          lông thừa bị xơ, rối, tránh tình trạng lông rơi khắp nhà. Việc này
          giúp hạn chế cho chó mèo vô tình liếm hay ăn phải, lâu ngày sẽ tích tụ
          trong ruột gây rối loạn đường ruột rất nguy hiểm.
        </p>
        <h1>
          III. BẢNG GIÁ DỊCH VỤ TẮM VỆ SINH, CẠO CẮT TỈA LÔNG CHO CHÓ MÈO:
        </h1>
        <p>
          Bảng giá đã bao gồm đầy đủ quy trình 12 bước spa tại Petio. Dịch vụ có
          thể phát sinh thêm phụ phí theo yêu cầu thêm của Khách hàng như: Gỡ
          rối lông hay làm ngoài giờ.
        </p>
        <div class="spa-prices reveal">
          <table class="spa-table">
            <thead>
              <tr>
                <th>Cân nặng</th>
                @foreach ($services->where('pricing_type', 'by_weight') as $service)
                  <th>{{ $service->name }}<small>{{ $service->description }}</small></th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach ($services->firstWhere('pricing_type', 'by_weight')?->prices ?? [] as $i => $band)
                <tr>
                  <td>{{ $band->label() }}</td>
                  @foreach ($services->where('pricing_type', 'by_weight') as $service)
                    <td>{{ vnd($service->prices[$i]->price ?? 0) }}</td>
                  @endforeach
                </tr>
              @endforeach
            </tbody>
          </table>
          @foreach ($services->where('pricing_type', 'range') as $service)
            <p class="spa-range">
              <strong>{{ $service->name }}</strong> ({{ $service->description }}):
              {{ vnd($service->price_from) }} – {{ vnd($service->price_to) }}
            </p>
          @endforeach
        </div>
        <h1>IV. NHỮNG LƯU Ý KHI SỬ DỤNG DỊCH VỤ SPA TẠI PETIO:</h1>
        <p>
          Petio không nhận spa khi các bé đang mang thai, đang điều trị bệnh,
          mới phẫu thuật, có tiểu sử bệnh hen, co giật hay các bệnh lý khác
          khiến thú cưng không có khả năng tự chủ.
        </p>
        <p>
          Để đảm bảo sức khỏe cho thú cưng đến làm dịch vụ spa, khi đưa các bé
          đến Khách hàng lưu ý:
          <strong>
            Khách hàng lưu ý: Không để thú cưng quá đói, quá no hay vận động quá
            sức trước khi đến spa Khách hàng lưu ý: </strong
          >. Nếu thú cưng có những biểu hiện hiện bất thường xin hãy liên hệ với
          Petio để được hỗ trợ.
        </p>
        <p>
          Làm xong dịch vụ, Khách hàng vui lòng
          <strong>kiểm tra thật kỹ thú cưng</strong> của mình khi đến đón về.
          Điều này nhằm đảm bảo nhân viên spa tại Petio đã hoàn thành đúng quy
          trình spa cho các bé. Nếu có bất cứ điều gì chưa hài lòng, hãy liên hệ
          với Petio qua <strong>hotline</strong>, fanpage
          <strong>Petio Shop</strong> để được hỗ trợ.
        </p>
        <h1>
          V. HÌNH ẢNH CÁC BÉ CHÓ MÈO SAU KHI SỬ DỤNG DỊCH VỤ SPA TẠI PETIO:
        </h1>
        <div class="nhomanh reveal-stagger">
          <img src="{{ asset('assets') }}/file_anh/iFKSfJA.jpeg" alt="" />
          <img src="{{ asset('assets') }}/file_anh/7axAtCd.jpeg" alt="" />
          <img src="{{ asset('assets') }}/file_anh/ibNiCoA.jpeg" alt="" />
          <img src="{{ asset('assets') }}/file_anh/fifmfES.jpeg" alt="" />
          <img src="{{ asset('assets') }}/file_anh/0L9cMh8.jpeg" alt="" />
        </div>

        <p>
          Trên đây là tất cả những thông tin mà bạn cần biết về dịch vụ spa cho
          thú cưng tại Petio . Nếu quý khách có bất kỳ nhu cầu gì, hãy gọi ngay
          tới hotline 123-456-789 để được hỗ trợ.
        </p>
      </div>
    </div>
    <!-- uu dai -->
    <div class="bennefit">
      <div class="body">
        @include('partials.newsletter')
      </div>
    </div>
@endsection
