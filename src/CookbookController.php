<?php

class CookbookController {
    public function __construct(private CookbookGateway $gateway)
    {
    }
    // Method to process incoming requests
    public function processRequest(string $method, ?string $id): void
    {
        // If an ID is provided, process resource request, otherwise process collection request
        if ($id) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        };
    }
    public function processResourceRequest(string $method, ?string $id): void {
            
    }
    public function processCollectionRequest(string $method): void {

    }
}
