<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Security\Voter\ClientVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/list', name: 'app_client_list', methods: ['GET'])]
    public function list(ClientRepository $clientRepository): Response
    {
        if (!$this->isGranted(ClientVoter::VIEW)) {
            $this->addFlash('error', 'You do not have permission to view the client list.');
            return $this->redirectToRoute('home');
        }

        $clients = $clientRepository->findAll();
        return $this->render('client/list.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted(ClientVoter::CREATE)) {
            $this->addFlash('error', 'You do not have permission to create a client.');
            return $this->redirectToRoute('home');
        }

        $client = new Client();
        $client->setCreatedAt(new \DateTimeImmutable());

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_list');
        }

        return $this->render('client/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted(ClientVoter::EDIT)) {
            $this->addFlash('error', 'You do not have permission to edit this client.');
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_list');
        }

        return $this->render('client/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_client_delete', methods: ['GET', 'POST'])]
    #[IsGranted('delete_client')]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted(ClientVoter::DELETE)) {
            $this->addFlash('error', 'You do not have permission to delete this client.');
            return $this->redirectToRoute('home');
        }

        $entityManager->remove($client);
        $entityManager->flush();

        $this->addFlash('success', 'Client deleted successfully!');
        return $this->redirectToRoute('app_client_list');
    }
}
