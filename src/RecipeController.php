<?php

class RecipeController
{
    public function __construct(private RecipeGateway $gateway)
    {
    }
    public function processRequest(string $method, ?string $id): void
    {
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        };
    }

    private function processResourceRequest(string $method, string $id): void
    {
    }
    private function processCollectionRequest(string $method): void
    {
        switch ($method) {
            case "GET":
                echo json_encode($this->gateway->getAll());
                break;
            case "POST":
                // because our request works with JSON, we need to use file_get_contents instead of $_POST
                // also we need to use array format, so we convert that to array with json_decode( _ ,true)
                // we also use (array) so that empty request returns an array instead of NULL
                $data = (array) json_decode(file_get_contents("php://input"), true);
                var_dump($data);
        }
    }
}