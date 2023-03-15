<?php
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

return function (App $app) {

    // user register
    $app->post('/register', function (Request $request, Response $response) {

        // รับค่าจาก client
        $body = $this->request->getparsedBody();
        $password = sha1($body['password']);

        // sql
        $sql = "INSERT INTO users (
            first_name, 
            last_name, 
            email,
            password,
            created_at,
            updated_at
        ) VALUES (
            :first_name, 
            :last_name, 
            :email, 
            :password, 
            :created_at, 
            :updated_at
        )";

        $stm = $this->db->prepare($sql);
        $stm->bindParam(':first_name', $body['first_name']);
        $stm->bindParam(':last_name', $body['last_name']);
        $stm->bindParam(':email', $body['email']);
        $stm->bindParam(':password', $password);
        $stm->bindParam(':created_at', Date('Y-m-d H:i:s'));
        $stm->bindParam(':updated_at', Date('Y-m-d H:i:s'));

        if($stm->execute()){
            return $this->response->withJson(['status' => 'success']);
        }else{
            return $this->response->withJson(['status' => 'fail']);
        }

    });

    
    // route group for api version 1
    $app->group('/api/v1', function () use ($app){

        // Read all products
        $app->get('/products', function (Request $request, Response $response) {
            $sql = $this->db->prepare("SELECT * FROM products ORDER BY id DESC");
            $sql->execute();
            $products = $sql->fetchAll();
            return $this->response->withJson($products);
        });

        // Read single product by id
        $app->get('/products/{id}', function (Request $request, Response $response, array $args) {
            $sql = "SELECT * FROM products WHERE id='$args[id]'";
            $stm = $this->db->prepare($sql);
            $stm->execute();
            $products = $stm->fetchAll();
            return $this->response->withJson($products);
        });

        // Create new product
        $app->post('/products', function (Request $request, Response $response) {

            // รับค่าจาก client
            $body = $this->request->getparsedBody();
            // print_r($body);

            $sql = "INSERT INTO products(
                        product_detail,
                        product_name,
                        product_barcode,
                        product_qty,
                        product_price,
                        product_date,
                        product_image,
                        product_category,
                        product_status
                    ) VALUES (
                        :product_detail,
                        :product_name,
                        :product_barcode,
                        :product_qty,
                        :product_price,
                        :product_date,
                        :product_image,
                        :product_category,
                        :product_status
                    )";

            $stm = $this->db->prepare($sql);
            $stm->bindParam(':product_detail', $body['product_detail']);
            $stm->bindParam(':product_name', $body['product_name']);
            $stm->bindParam(':product_barcode', $body['product_barcode']);
            $stm->bindParam(':product_qty', $body['product_qty']);
            $stm->bindParam(':product_price', $body['product_price']);
            $stm->bindParam(':product_date', Date('Y-m-d H:i:s'));
            $stm->bindParam(':product_image', $body['product_image']);
            $stm->bindParam(':product_category', $body['product_category']);
            $stm->bindParam(':product_status', $body['product_status']);
            
            if($stm->execute()){
                return $this->response->withJson(['status' => 'success']);
            }else{
                return $this->response->withJson(['status' => 'fail']);
            }

        });

        // Update product
        $app->put('/products/{id}', function (Request $request, Response $response, array $args) {

            // รับค่าจาก client
            $body = $this->request->getparsedBody();

            // sql
            $sql = "UPDATE products SET
                        product_detail = :product_detail,
                        product_name = :product_name,
                        product_barcode = :product_barcode,
                        product_qty = :product_qty,
                        product_price = :product_price,
                        product_image = :product_image,
                        product_category = :product_category,
                        product_status = :product_status
                    WHERE id = '$args[id]'";
            
            $stm = $this->db->prepare($sql);
            $stm->bindParam(':product_detail', $body['product_detail']);
            $stm->bindParam(':product_name', $body['product_name']);
            $stm->bindParam(':product_barcode', $body['product_barcode']);
            $stm->bindParam(':product_qty', $body['product_qty']);
            $stm->bindParam(':product_price', $body['product_price']);
            $stm->bindParam(':product_image', $body['product_image']);
            $stm->bindParam(':product_category', $body['product_category']);
            $stm->bindParam(':product_status', $body['product_status']);

            if($stm->execute()){
                return $this->response->withJson(['status' => 'success']);
            }else{
                return $this->response->withJson(['status' => 'fail']);
            }

        });

        // Delete product
        $app->delete('/products/{id}', function (Request $request, Response $response, array $args) {
            
            $sql = "DELETE FROM products WHERE id='$args[id]'";
            $stm = $this->db->prepare($sql);
            if($stm->execute()){
                return $this->response->withJson(['status' => 'success']);
            }else{
                return $this->response->withJson(['status' => 'fail']);
            }

        });

    });

};
