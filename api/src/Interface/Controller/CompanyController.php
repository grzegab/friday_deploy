<?php

declare(strict_types=1);

namespace App\Interface\Controller;

use App\Application\Command\CreateCompanyCommand;
use App\Application\Command\RemoveCompanyCommand;
use App\Application\Command\UpdateCompanyDetailsCommand;
use App\Application\Exceptions\CompanyContainWorkersException;
use App\Application\Exceptions\CompanyNotExistsException;
use App\Application\Query\CompanyDetailsQuery;
use App\Application\Query\CompanyListQuery;
use App\Application\Service\ValidationService;
use App\Domain\Company;
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
    path: '/company',
    name: 'company_',
)]
readonly class CompanyController
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
            new OA\Response(response: Response::HTTP_OK, description: 'Returns details of newly created company',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Company::class, groups: ['details']))
                )),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bad request data'),
            new OA\Response(response: Response::HTTP_UNSUPPORTED_MEDIA_TYPE, description: 'Bad request format'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function createCompany(#[MapRequestPayload(acceptFormat: 'json')] CreateCompanyCommand $command): Response
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
    #[Route(path: '/list', name: 'list_all', methods: ['GET'], format: 'json')]
    #[OA\Get(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns list of existing companies',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: Company::class, groups: ['list']))
                    ))
                )),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function listCompanies(): Response
    {
        $query = new CompanyListQuery();
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
    #[Route(path: '/{companyUuid}', name: 'details', methods: ['GET'], format: 'json')]
    #[OA\Get(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns detail of existing company',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Company::class, groups: ['detail']))
                )),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Company not found'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function companyDetails(string $companyUuid): Response
    {
        $query = new CompanyDetailsQuery(uuid: $companyUuid);
        $errors = $this->validatorService->validate($query);
        if ($errors) {
            return new JsonResponse('Bad Input', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $envelope = $this->queryBus->dispatch($query);
        } catch (\Exception $e) {
            if ($e->getPrevious()::class === CompanyNotExistsException::class) {
                return new JsonResponse('Company with uuid not found', Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse('Problem fetching company', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('Problem fetching companies', Response::HTTP_INTERNAL_SERVER_ERROR);
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
    #[Route(path: '/{companyUuid}', name: 'update', methods: ['PATCH'], format: 'json')]
    #[OA\Patch(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns UUID of updated company',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: new Model(type: Company::class, groups: ['updated']))
                )),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Bad request data'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function updateCompanyDetails(string $companyUuid, #[MapRequestPayload(acceptFormat: 'json')] UpdateCompanyDetailsCommand $command): Response
    {
        $command->setUuid($companyUuid);

        try {
            $envelope = $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            if ($e->getPrevious()::class === CompanyNotExistsException::class) {
                return new JsonResponse('Company with uuid not found', Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse('Problem fetching company', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $lastHandledEnvelope = $envelope->last(HandledStamp::class);
        if ($lastHandledEnvelope === null) {
            return new JsonResponse('Problem fetching companies', Response::HTTP_INTERNAL_SERVER_ERROR);
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
    #[Route(path: '/{companyUuid}', name: 'remove', methods: ['DELETE'], format: 'json')]
    #[OA\Delete(
        responses: [
            new OA\Response(response: Response::HTTP_OK, description: 'Returns UUID of deleted'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Company not found'),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: 'Unprocessible entity'),
            new OA\Response(response: Response::HTTP_INTERNAL_SERVER_ERROR, description: 'Internal problem'),
        ]
    )]
    public function removeCompany(string $companyUuid): Response
    {
        $command = new RemoveCompanyCommand($companyUuid);

        try {
            $envelope = $this->commandBus->dispatch($command);
        } catch (\Exception $e) {
            if ($e->getPrevious()::class === CompanyNotExistsException::class) {
                return new JsonResponse('Company with uuid not found', Response::HTTP_NOT_FOUND);
            }

            if ($e->getPrevious()::class === CompanyContainWorkersException::class) {
                return new JsonResponse('Company contain workers and cannot be deleted', Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return new JsonResponse('Problem fetching companies', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

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
