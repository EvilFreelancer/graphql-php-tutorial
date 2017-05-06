<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use GraphQL\GraphQL;
use GraphQL\Schema;

use AppBundle\DB;
use AppBundle\Types;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // Настройки подключения к БД
        $config = [
            'host' => 'localhost',
            'database' => 'lc',
            'username' => 'lc',
            'password' => 'lc'
        ];

        // Инициализация соединения с БД
        DB::init($config);

        try {
            // Get data from php://input
            $content = $request->getContent();

            // Parameters
            $params = array();
            if (!empty($content)) $params = json_decode($content, true);
            $query = $params['query'];

            $schema = new Schema([
                'query' => Types::query()
            ]);
            $result = GraphQL::execute($schema, $query);

        } catch (\Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
        die();
    }
}
