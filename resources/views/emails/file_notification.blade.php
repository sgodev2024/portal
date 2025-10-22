<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Báo cáo mới</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #005aa1;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .file-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #005aa1;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #005aa1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #004080;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📄 Báo cáo mới</h2>
    </div>
    
    <div class="content">
        <h3>Xin chào!</h3>
        
        <p>Bạn có một file báo cáo mới từ hệ thống:</p>
        
        <div class="file-info">
            <h4>{{ $file->title }}</h4>
            @if($file->description)
                <p><strong>Mô tả:</strong> {{ $file->description }}</p>
            @endif
            <p><strong>File:</strong> {{ $file->file_name }}</p>
            <p><strong>Kích thước:</strong> {{ $file->file_size_formatted }}</p>
            <p><strong>Gửi bởi:</strong> {{ $file->uploader->name }}</p>
            <p><strong>Ngày gửi:</strong> {{ $file->sent_at->format('d/m/Y H:i') }}</p>
        </div>
        
        <p>Nhấn nút bên dưới để tải file:</p>
        
        <a href="{{ $downloadUrl }}" class="btn">📥 Tải xuống</a>
        
        <p><strong>Lưu ý:</strong> Link tải có thể hết hạn sau 7 ngày. Vui lòng tải file sớm nhất có thể.</p>
        
        <p>Nếu bạn không thể truy cập link, vui lòng đăng nhập vào hệ thống để tải file.</p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống. Vui lòng không trả lời email này.</p>
        <p>© {{ date('Y') }} - Hệ thống quản lý</p>
    </div>
</body>
</html>

