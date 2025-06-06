<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    private const HTML = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReactPHP Runtime</title>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: sans-serif;
            text-align: center;
            background-color: #333; /* Dark gray background */
            color: #eee; /* Light gray text for contrast */
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px; /* Space between elements */
        }

        img {
            max-width: 100%;
            height: auto;
            display: block; /* Removes extra space below image */
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>ReactPHP Runtime</h1>
        <img src="/logo.png" alt="Placeholder Image">
        <p>By crazy goat software</p>
    </div>
</body>
</html>
HTML;



    #[Route('/')]
    public function index(): Response
    {
        return new Response(self::HTML);
    }
}
