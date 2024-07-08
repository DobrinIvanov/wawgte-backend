<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtils {
    // Constructor method to initialize the Jwt object with secret key passed from outside
    public function __construct(private string $secretKey) {
    }

    public function generateToken($user): string{
        $payload = [
            'iss' => 'backend.wawgte.com',
            'iat' => time(),
            'exp' => time() + 86400,
            'data' => [
                'userId' => $user['user_id'],
                'email' => $user['email'],
                'fname' => $user['first_name'],
                'lname' => $user['last_name']
            ]
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }
}
?>