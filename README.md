# ZMP OpenAPI PHP SDK

> Một thư viện PHP giúp kết nối và thao tác với hệ thống MiniApp của ZaloPlatform một cách dễ dàng.

## 1. Yêu cầu hệ thống

- PHP >= 7.4
- Composer
- GuzzleHTTP (đã có trong `composer.json`)

## 2. Cài đặt

```bash
composer require vdhoangson/zmp-openapi-php
```

## 3. Sử dụng nhanh

### Khởi tạo client

```php
use Vdhoangson\ZmpOpenApi\Classes\PartnerClient;
$proxy = [
    "host" => "127.0.0.1",
    "port" => 123
];

$client = new PartnerClient(
    "{YOUR-PARTNER-API-KEY}",
    "{YOUR-PARTNER-ID}",
    $proxy, // optional
);
```

### Gọi API lấy danh sách miniapp

```php
$response = $client->getMiniApps();
print_r($response);
```

### Triển khai miniapp (upload file zip)

```php
$deployApp = [
    'file' => '/path/to/file.zip',
    'miniAppId' => 123,
    'name' => 'Tên app',
    'description' => 'Mô tả app'
];
$response = $client->deployMiniApp($deployApp);
```

## 4. Các hàm phổ biến

- Lấy danh sách miniapp: `getMiniApps(array $params = [])`
- Tạo miniapp: `createMiniApp(array $appInfo)`
- Triển khai miniapp: `deployMiniApp(array $deployApp)`
- Yêu cầu publish: `requestPublishMiniApp(array $requestPublishApp)`
- Publish: `publishMiniApp(array $publishApp)`
- Quản lý kênh thanh toán: `listPaymentChannels`, `createPaymentChannel`, `updatePaymentChannel`
- Quản lý API domain: `listApiDomain`, `createApiDomain`, `updateApiDomain`

Tài liệu từ Zalo: [https://miniapp.zaloplatforms.com/documents/open-apis/]

## 5. Lưu ý sử dụng

- Hàm `validateInit()` sẽ kiểm tra cấu hình, nếu thiếu sẽ báo lỗi.
- Có thể truyền proxy khi khởi tạo hoặc dùng hàm `setProxy($proxy)`.
- Các hàm trả về mảng gồm `error`, `message` và dữ liệu (nếu có).

## 6. Đóng góp & hỗ trợ

- Đóng góp: Tạo pull request hoặc issue trên repository.
- Hỗ trợ: Liên hệ team phát triển hoặc mở issue.

You can sponsor this project through [GitHub Sponsors](https://github.com/sponsors/vdhoangson):

[![GitHub Sponsors](https://img.shields.io/badge/sponsor-30363D?style=for-the-badge&logo=GitHub-Sponsors&logoColor=#EA4AAA)](https://github.com/sponsors/vdhoangson)

### ☕ Buy Me a Coffee

Support ongoing development with a coffee:

[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ffdd00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black)](https://buymeacoffee.com/vdhoangson)
