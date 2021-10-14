<html>
<head>
  <meta charset="utf-8">
  <title>Callback</title>
  <script>
    window.opener.postMessage({ token: "{{ $token }}", user:'{{ $user }}'}, "http://127.0.0.1:8080");
    window.close();
  </script>
</head>
<body>
</body>
</html>