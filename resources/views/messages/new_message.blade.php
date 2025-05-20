{{-- resources/views/messages/new_message.blade.php --}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pesan Baru – {{ $post->title }}</title>
</head>
<body style="margin:0;padding:0;font-family:sans-serif;background-color:#f3f3f3;">

  {{-- Header Oranye --}}
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td style="background: #F97316; padding: 20px; text-align: center;">
        <h1 style="margin:0;color:#fff;font-size:24px;">Anda mendapat pesan baru</h1>
      </td>
    </tr>
  </table>

  {{-- Body Putih --}}
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" style="padding: 20px;">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
          <tr>
            <td style="padding:20px;color:#333333;">

              <p><strong>Dari:</strong> {{ $sender->name }} &lt;{{ $sender->email }}&gt;</p>
              <p><strong>Kepada:</strong> {{ $post->email }}</p>
              <p><strong>Subjek:</strong> {{ $subjectLine }}</p>

              <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

              <div style="line-height:1.6;color:#555;">
                {!! nl2br(e($bodyText)) !!}
              </div>

              <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

              <p style="font-size:14px;color:#888;">
                Klik 
                <a href="{{ route('posts.messages.index', $post) }}" 
                   style="color:#F97316;text-decoration:none;font-weight:bold;">
                  di sini
                </a>
                untuk membuka website.
              </p>

            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

</body>
</html>
