<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController
{
    private $connection;

    public function __construct()
    {
        // Establece la conexión a la base de datos usando PDO
        try {
            $this->connection = new \PDO('mysql:host=db;dbname=db', 'root', 'admin');
        } catch (\PDOException $e) {
            throw new \Exception('Error al conectar a la base de datos: ' . $e->getMessage());
        }
    }

    public function test()
    {
        return new Response(
            '<html><body>Hello world! Soy el primer usuario de Docker!</body></html>'
        );
    }

    public function getUsers()
    {
        $query = $this->connection->query('SELECT * FROM users');
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return new JsonResponse($result);
    }

    public function createUser(Request $request)
    {
        $name = $request->query->get('nombre');
        $lastname = $request->query->get('apellido');
        $email = $request->query->get('email');

        $stmt = $this->connection->prepare("
            INSERT INTO users (name, lastname, email)
            VALUES (:name, :lastname, :email)
        ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);
        $count = $stmt->execute();

        return new Response($count == 1 ? 'Usuario guardado' : 'No se ha podido guardar el usuario');
    }
}
