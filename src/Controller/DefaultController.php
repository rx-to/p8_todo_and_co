<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

   /**
    * The function indexAction() is a public function that returns the render of the
    * default/index.html.twig file.
    * 
    * @return The render method is returning the contents of the file default/index.html.twig.
    */
    #[Route('/', name: 'homepage')]
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
