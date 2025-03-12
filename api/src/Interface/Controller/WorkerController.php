<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Command\CreateWorkerCommand;
use App\Application\Command\RemoveWorkerCommand;
use App\Application\Command\UpdateWorkerCommand;
use App\Application\Exceptions\WorkerNotExistsException;
use App\Application\Query\WorkerDetailsQuery;
use App\Application\Query\WorkerListQuery;
use App\Application\Service\ValidationService;
use App\Domain\Worker;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
#[Route(
    path: '/worker',
    name: 'worker_',
)]
readonly class WorkerController
{
    public function __construct(
        private MessageBusInterface $queryBus,
        private MessageBusInterface $commandBus,
        private SerializerInterface $serializer,
        private ValidationService $validatorService,
    ) {
    }

    #[OA\Tag(name: 'company')]
    #[Route(path: '', name: 'create', methods: ['POST'], format: 'json')]
    #[OA\Post(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns details of newly created worker',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Worker::class, groups: ['details']))
                )),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bad request data'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function createWorker(#[MapRequestPayload(acceptFormat: 'json')] CreateWorkerCommand $command): Response
    {
        $envelope = $this->commandBus->dispatch($command);
        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('Company not created', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = $lastHandledEnvelope->getResult();
        $json = $this->serializer->serialize(
            $result,
            'json',
            ['groups' => 'created']
        );

        return new Response($json, Response::HTTP_CREATED);
    }

    #[OA\Tag(name: 'company')]
    #[Route(path: '/list/{companyUuid}', name: 'list_all', methods: ['GET'], format: 'json')]
    #[OA\Get(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns list of existing companies',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: Worker::class, groups: ['list']))
                    ))
                )),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bad input'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function listWorkers(string $companyUuid): Response
    {
        $query = new WorkerListQuery($companyUuid);
        $errors = $this->validatorService->validate($query);
        if ($errors) {
            return new JsonResponse('Bad Input', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $envelope = $this->queryBus->dispatch($query);
        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('Problem fetching companies', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = $lastHandledEnvelope->getResult();
        $json = $this->serializer->serialize(
            $result,
            'json',
            ['groups' => 'list']
        );

        return new Response($json, Response::HTTP_OK);
    }

    #[OA\Tag(name: 'company')]
    #[Route(path: '/{workerUuid}', name: 'details', methods: ['GET'], format: 'json')]
    #[OA\Get(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns details of worker',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Worker::class, groups: ['details']))
                )),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Worker not found'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bad input'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function workerDetails(string $workerUuid): Response
    {
        $query = new WorkerDetailsQuery($workerUuid);
        $errors = $this->validatorService->validate($query);
        if ($errors) {
            return new JsonResponse('Bad Input', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $envelope = $this->queryBus->dispatch($query);
        } catch (\Exception $e) {
            if ($e->getPrevious()::class === WorkerNotExistsException::class) {
                return new JsonResponse('Worker with uuid not found', Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse('Problem fetching worker', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('Worker with uuid not found', Response::HTTP_NOT_FOUND);
        }

        $result = $lastHandledEnvelope->getResult();
        $json = $this->serializer->serialize(
            $result,
            'json',
            ['groups' => 'details']
        );

        return new Response($json, Response::HTTP_OK);
    }

    #[OA\Tag(name: 'company')]
    #[Route(path: '/{workerUuid}', name: 'update', methods: ['PATCH'], format: 'json')]
    #[OA\Patch(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns details of updated worker',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Worker::class, groups: ['details']))
                )),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Worker not found'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bad request data'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function updateWorkerDetails(string $workerUuid, #[MapRequestPayload(acceptFormat: 'json')] UpdateWorkerCommand $command): Response
    {
        $command->setUuid($workerUuid);

        $envelope = $this->commandBus->dispatch($command);
        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('Problem fetching worker', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $result = $lastHandledEnvelope->getResult();
        $json = $this->serializer->serialize(
            $result,
            'json',
            ['groups' => 'details']
        );

        return new Response($json, Response::HTTP_OK);
    }

    #[OA\Tag(name: 'company')]
    #[Route(path: '/{workerUuid}', name: 'remove', methods: ['DELETE'], format: 'json')]
    #[OA\Delete(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns UUID of deleted worker'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Worker not found'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bard input'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function removeWorker(string $workerUuid): Response
    {
        $command = new RemoveWorkerCommand($workerUuid);
        $errors = $this->validatorService->validate($command);
        if ($errors) {
            return new JsonResponse('Bad Input', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $envelope = $this->commandBus->dispatch($command);
        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('No company with this uuid', Response::HTTP_NOT_FOUND);
        }

        $result = $lastHandledEnvelope->getResult();
        $json = $this->serializer->serialize(
            $result,
            'json',
            ['groups' => 'removed']
        );

        return new Response($json, Response::HTTP_OK);
    }
}
