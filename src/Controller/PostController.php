<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    private $em;

    function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/all-posts', name: 'all_posts')]
    public function index()
    {
        $posts = $this->em->getRepository(Post::class)->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/create-post', name: 'create_post')]
    function createPost(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(postType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_dir'),
                        $newFileName
                    );
                } catch (Exception $e) {
                    return $e;
                }

                $post->setImage($newFileName);
            }

            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('success', 'Post created');
            return $this->redirectToRoute('all_posts');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit-post/{id}', name: 'edit_post')]
    function editPost(Request $request, $id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $newFileName = uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_dir'),
                        $newFileName
                    );
                } catch (Exception $e) {
                    return $e;
                }

                $post->setImage($newFileName);
            }

            $this->em->persist($post);
            $this->em->flush();

            $this->addFlash('success', 'post updated');

            return $this->redirectToRoute('all_posts');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete-post{id}', name: 'delete_post')]
    function deletePost($id)
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $this->em->remove($post);
        $this->em->flush();

        return $this->redirectToRoute('all_posts');
    }
}
