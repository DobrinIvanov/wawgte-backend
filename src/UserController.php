<?php

class UserController {
    public function __construct(private UserGateway $gateway) {

    }
    public function processRequest(string $method, ?string $id): void
    {
        // If an ID is provided, process resource request, otherwise process collection request
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        };
    }
    public function processResourceRequest(string $method,int $id) {
        $recipe = $this->gateway->get($id);

        if ( ! $recipe) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found!"]);
            return;
        }
    }

}

?>