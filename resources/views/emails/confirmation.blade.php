<body>
  <h1>Email Address Confirmation</h1>
  <p>Hello, {{ $username }}</p>
  <p>Thank you for signing up for a demo of Trellis! Please click the following link to confirm your email and activate your account.</p>
  <p><a href="{{$link}}">Confirm Email</a></p>
  <p>If you're having trouble clicking the link above, try copy pasting the following link into the address bar of your browser.</p>
  <p><a href="{{$link}}">{{$link}}</a></p>
  <p>We hope you enjoy Trellis!</p>
  <p>Thanks,</p>
  <p>The Trellis Team</p>
</body>
