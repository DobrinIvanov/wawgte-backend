<?php
use Firebase\JWT\JWT;

class JwtUtils {
    // Constructor method to initialize the Jwt object with secret key passed from outside
    public function __construct(private string $secretKey){
    }

    public function generateToken($userId) {
        $payload = [
            'iss' => 'backend.wawgte.com',
            'iat' => time(),
            'exp' => time() + 86400,
            'data' => [
                'userId' => $userId,
            ]
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
    // TODO - Does not work as expected
    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secretKey, 'HS256'));
            return (array) $decoded->data;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }
}
?>