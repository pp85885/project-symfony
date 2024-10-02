<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/products', name: 'product')]
    public function index(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/products/create', name: 'product.create')]
    public function create(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request); // handle the request while the post

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFileName = uniqid() . "." . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_dir_products'),
                        $newFileName
                    );
                } catch (Exception $e) {
                    return $e;
                }

                $product->setImage($newFileName);
            }

            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash('success', 'Product added successfully');

            return $this->redirectToRoute('product');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/products/edit/{id}', name: 'product.edit')]
    public function edit(Request $request, $id)
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        // is edit change the submit button name
        $form = $this->createForm(ProductType::class, $product, ['is_edit' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFileName = uniqid() . "." . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('upload_dir_products'),
                    $newFileName
                );

                $product->setImage($newFileName);
            }

            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash('success', 'Updated successfully');

            return $this->redirectToRoute('product');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // basic method
    // #[Route('product/delete/{id}', name: 'product.delete')]
    // function delete($id)
    // {
    //     $product = $this->em->getRepository(Product::class)->find($id);
    //     $this->em->remove($product);
    //     $this->em->flush();

    //     return $this->redirectToRoute('product');
    // }

    // called by the ajax method
    #[Route('product/delete/{id}', name: 'product.delete', methods: ['DELETE'])]
    function delete($id)
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($product);
        $this->em->flush();

        return $this->json(['message' => 'Post deleted successfully'], Response::HTTP_OK);
    }
}
