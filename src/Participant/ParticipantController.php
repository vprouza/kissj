<?php declare(strict_types=1);

namespace kissj\Participant;

use kissj\AbstractController;
use kissj\Event\AbstractContentArbiter;
use kissj\Participant\Troop\TroopService;
use kissj\User\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\UploadedFile;

// now usage for TroopParticipants and TroopLeaders, TODO for all roles
class ParticipantController extends AbstractController
{
    public function __construct(
        private TroopService $troopService,
        private ParticipantService $participantService,
        private ParticipantRepository $participantRepository,
    ) {}

    public function showDashboard(Response $response, User $user): Response
    {
        return $this->view->render(
            $response,
            'participant/dashboard.twig',
            $this->getTemplateData($user),
        );
    }

    public function showDetailsChangeable(Response $response, User $user): Response
    {
        return $this->view->render(
            $response,
            'participant/changeDetails.twig',
            $this->getTemplateData($user),
        );
    }

    public function changeDetails(Request $request, Response $response, User $user): Response
    {
        $participant = $this->getParticipantFromUser($user);

        /** @var string[] $parsed */
        $parsed = $request->getParsedBody();
        $this->participantService->addParamsIntoParticipant($participant, $parsed);

        if ($this->getContentArbiterForParticipant($user)->uploadFile) {
            $uploadedFile = $this->resolveUploadedFiles($request);
            if ($uploadedFile instanceof UploadedFile) {
                $this->fileHandler->saveFileTo($participant, $uploadedFile);
            }
        }

        $this->participantRepository->persist($participant);
        $this->flashMessages->success($this->translator->trans('flash.success.detailsSaved'));

        return $this->redirect($request, $response, 'dashboard');
    }

    public function showCloseRegistration(Request $request, Response $response, User $user): Response
    {
        $participant = $this->getParticipantFromUser($user);

        if ($this->participantService->isCloseRegistrationValid($participant)) {
            return $this->view->render(
                $response,
                'participant/closeRegistration.twig',
                ['dataProtectionUrl' => $user->event->dataProtectionUrl]
            );
        }

        return $this->redirect($request, $response, 'dashboard');
    }

    public function closeRegistration(Request $request, Response $response, User $user): Response
    {
        $participant = $this->getParticipantFromUser($user);
        $participant = $this->participantService->closeRegistration($participant);

        if ($participant->getUserButNotNull()->status === User::STATUS_CLOSED) {
            $this->flashMessages->success($this->translator->trans('flash.success.locked'));
            $this->logger->info('Locked registration for IST with ID ' . $participant->id 
                . ', user ID ' . $participant->id);
        } else {
            $this->flashMessages->error($this->translator->trans('flash.error.wrongData'));
        }

        return $this->redirect($request, $response, 'dashboard');
    }

    /**
     * @param User $user
     * @return array<string, mixed>
     */
    private function getTemplateData(User $user): array
    {
        return match ($user->role) {
            User::ROLE_TROOP_LEADER => [
                'user' => $user,
                'person' => $this->troopService->getTroopLeader($user),
                'ca' => $user->event->eventType->getContentArbiterTroopLeader(),
            ],
            User::ROLE_TROOP_PARTICIPANT => [
                'user' => $user,
                'person' => $this->troopService->getTroopParticipant($user),
                'ca' => $user->event->eventType->getContentArbiterTroopParticipant(),
            ],
            default => throw new \RuntimeException('Unexpected role ' . $user->role),
        };
    }

    private function getParticipantFromUser(User $user): Participant
    {
        return match ($user->role) {
            User::ROLE_TROOP_LEADER => $this->troopService->getTroopLeader($user),
            User::ROLE_TROOP_PARTICIPANT => $this->troopService->getTroopParticipant($user),
            default => throw new \RuntimeException('Unexpected role ' . $user->role),
        };
    }
    
    private function getContentArbiterForParticipant(User $user): AbstractContentArbiter
    {
        return match ($user->role) {
            User::ROLE_TROOP_LEADER => $user->event->eventType->getContentArbiterTroopLeader(),
            User::ROLE_TROOP_PARTICIPANT => $user->event->eventType->getContentArbiterTroopParticipant(),
            default => throw new \RuntimeException('Unexpected role ' . $user->role),
        };
    }
}