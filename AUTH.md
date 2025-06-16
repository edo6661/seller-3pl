# API Authentication Documentation

## Base URL
```
https://yourdomain.com/api
```

## Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token} // untuk endpoint yang memerlukan authentication
```

## Authentication Endpoints

### 1. Login
**POST** `/auth/login`

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password123",
    "remember": false
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "avatar": null,
            "role": "seller",
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "is_email_verified": true,
            "is_admin": false,
            "is_seller": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### 3. Logout
**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Logout berhasil"
}
```

### 4. Get Current User
**GET** `/auth/me`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "avatar": null,
            "role": "seller",
            "email_verified_at": "2024-01-01T00:00:00.000000Z",
            "is_email_verified": true,
            "is_admin": false,
            "is_seller": true,
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### 5. Refresh Token
**POST** `/auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Token berhasil di-refresh",
    "data": {
        "user": {...},
        "token": "2|newtoken123456...",
        "token_type": "Bearer"
    }
}
```

### 6. Forgot Password
**POST** `/auth/forgot-password`

**Request Body:**
```json
{
    "email": "user@example.com"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Link reset password telah dikirim ke email Anda."
}
```

### 7. Reset Password
**POST** `/auth/reset-password`

**Request Body:**
```json
{
    "token": "reset_token_from_email",
    "email": "user@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Password berhasil direset."
}
```

### 8. Verify Email
**GET** `/auth/verify-email/{id}/{hash}`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Email berhasil diverifikasi."
}
```

### 9. Resend Verification Email
**POST** `/auth/resend-verification`

**Request Body:**
```json
{
    "email": "user@example.com"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Email verifikasi telah dikirim ulang."
}
```

## Social Authentication

### 10. Get Social Login URL
**GET** `/auth/redirect/{provider}`

**Parameters:**
- `provider`: google, facebook, twitter, etc.

**Response Success (200):**
```json
{
    "success": true,
    "data": {
        "redirect_url": "https://accounts.google.com/oauth/authorize?..."
    }
}
```

### 11. Social Login Callback
**GET** `/auth/callback/{provider}`

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login berhasil dengan Google",
    "data": {
        "user": {...},
        "token": "3|socialtoken123456...",
        "token_type": "Bearer"
    }
}
```

### 12. Login with Social Token (for Mobile)
**POST** `/auth/callback/{provider}`

**Request Body:**
```json
{
    "access_token": "social_access_token_from_mobile"
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Login berhasil dengan Google",
    "data": {
        "user": {...},
        "token": "4|mobiletoken123456...",
        "token_type": "Bearer"
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Data tidak valid",
    "errors": {
        "email": ["Email wajib diisi."],
        "password": ["Password minimal 8 karakter."]
    }
}
```

### Authentication Error (401)
```json
{
    "success": false,
    "message": "Unauthenticated"
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Terjadi kesalahan pada server",
    "error": "Error message details"
}
```

## Implementation Example

### Flutter/Mobile Implementation
```dart
// Login example
Future<AuthResponse> login(String email, String password) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/auth/login'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: jsonEncode({
      'email': email,
      'password': password,
      'remember': false,
    }),
  );
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    // Save token to secure storage
    await secureStorage.write(key: 'auth_token', value: data['data']['token']);
    return AuthResponse.fromJson(data);
  } else {
    throw Exception('Login failed');
  }
}

// Authenticated request example
Future<User> getCurrentUser() async {
  final token = await secureStorage.read(key: 'auth_token');
  
  final response = await http.get(
    Uri.parse('$baseUrl/api/auth/me'),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': 'Bearer $token',
    },
  );
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    return User.fromJson(data['data']['user']);
  } else {
    throw Exception('Failed to get user data');
  }
}
```

### JavaScript/Web Implementation
```javascript
// Login example
async function login(email, password) {
  try {
    const response = await fetch('/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        email: email,
        password: password,
        remember: false,
      }),
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Save token to localStorage
      localStorage.setItem('auth_token', data.data.token);
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Login error:', error);
    throw error;
  }
}

// Authenticated request example
async function getCurrentUser() {
  const token = localStorage.getItem('auth_token');
  
  try {
    const response = await fetch('/api/auth/me', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });
    
    const data = await response.json();
    
    if (data.success) {
      return data.data.user;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Get user error:', error);
    throw error;
  }
}
```

## Notes

1. **Token Storage**: Simpan token dengan aman menggunakan secure storage untuk mobile atau httpOnly cookies untuk web
2. **Token Expiration**: Implementasikan refresh token mechanism jika diperlukan
3. **Error Handling**: Selalu handle error response dengan baik
4. **CSRF Protection**: Untuk web application, pastikan CSRF protection diaktifkan
5. **Rate Limiting**: Implementasikan rate limiting untuk endpoint authentication
6. **HTTPS**: Selalu gunakan HTTPS di production2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abcdef123456...",
        "token_type": "Bearer"
    }
}
```

### 2. Register
**POST** `/auth/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "seller"
}
```
