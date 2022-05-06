<?php

declare(strict_types=1);

namespace kissj\Participant\Admin;

use DateTimeImmutable;
use kissj\AbstractController;
use kissj\BankPayment\BankPayment;
use kissj\BankPayment\BankPaymentRepository;
use kissj\BankPayment\FioBankPaymentService;
use kissj\Event\Event;
use kissj\Orm\Order;
use kissj\Participant\Guest\GuestService;
use kissj\Participant\Ist\IstService;
use kissj\Participant\ParticipantRepository;
use kissj\Participant\ParticipantService;
use kissj\Participant\Patrol\PatrolService;
use kissj\Participant\Troop\TroopService;
use kissj\Payment\PaymentRepository;
use kissj\Payment\PaymentService;
use kissj\User\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AdminController extends AbstractController
{
    public function __construct(
        private ParticipantService $participantService,
        private ParticipantRepository $participantRepository,
        private PaymentService $paymentService,
        private PaymentRepository $paymentRepository,
        private BankPaymentRepository $bankPaymentRepository,
        private FioBankPaymentService $bankPaymentService,
        private PatrolService $patrolService,
        private IstService $istService,
        private GuestService $guestService,
        private TroopService $troopService,
        private AdminService $adminService,
    ) {
    }

    public function showDashboard(Response $response, Event $event, User $user): Response
    {
        return $this->view->render(
            $response,
            'admin/dashboard-admin.twig',
            [
                'patrols' => $this->patrolService->getAllPatrolsStatistics($event, $user),
                'ists' => $this->istService->getAllIstsStatistics($event, $user),
                'troopLeaders' => $this->troopService->getAllTroopLeaderStatistics($event, $user),
                'troopParticipants' => $this->troopService->getAllTroopParticipantStatistics($event, $user),
                'guests' => $this->guestService->getAllGuestsStatistics($event, $user),
            ],
        );
    }

    public function showStats(
        Response $response,
        Event $event,
        User $user,
    ): Response {
        $orderByUpdatedAtDesc = new Order(Order::FILED_UPDATED_AT, Order::DIRECTION_DESC);

        return $this->view->render($response, 'admin/stats-admin.twig', [
            'paidPatrolLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_PATROL_LEADER],
                [USER::STATUS_PAID],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'paidTroopLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_LEADER],
                [USER::STATUS_PAID],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'paidTroopParticipants' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_PARTICIPANT],
                [USER::STATUS_PAID],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'paidIsts' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_IST],
                [USER::STATUS_PAID],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'paidGuests' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_GUEST],
                [USER::STATUS_PAID],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'caIst' => $event->getEventType()->getContentArbiterIst(),
            'caPl' => $event->getEventType()->getContentArbiterPatrolLeader(),
            'caPp' => $event->getEventType()->getContentArbiterPatrolParticipant(),
            'caTl' => $event->getEventType()->getContentArbiterTroopLeader(),
            'caTp' => $event->getEventType()->getContentArbiterTroopParticipant(),
            'caGuest' => $event->getEventType()->getContentArbiterGuest(),
        ]);
    }

    public function showOpen(
        Response $response,
        Event $event,
        User $user,
    ): Response {
        $orderByUpdatedAtDesc = new Order(Order::FILED_UPDATED_AT, Order::DIRECTION_DESC);

        return $this->view->render($response, 'admin/open-admin.twig', [
            'openPatrolLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_PATROL_LEADER],
                [USER::STATUS_OPEN],
                $event,
                $user,
                $orderByUpdatedAtDesc,
                true,
            ),
            'openTroopLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_LEADER],
                [USER::STATUS_OPEN],
                $event,
                $user,
                $orderByUpdatedAtDesc,
                true,
            ),
            'openTroopParticipants' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_PARTICIPANT],
                [USER::STATUS_OPEN],
                $event,
                $user,
                $orderByUpdatedAtDesc,
                true,
            ),
            'openIsts' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_IST],
                [USER::STATUS_OPEN],
                $event,
                $user,
                $orderByUpdatedAtDesc,
                true,
            ),
            'openGuests' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_GUEST],
                [USER::STATUS_OPEN],
                $event,
                $user,
                $orderByUpdatedAtDesc,
                true,
            ),
            'caIst' => $event->getEventType()->getContentArbiterIst(),
            'caPl' => $event->getEventType()->getContentArbiterPatrolLeader(),
            'caPp' => $event->getEventType()->getContentArbiterPatrolParticipant(),
            'caTl' => $event->getEventType()->getContentArbiterTroopLeader(),
            'caTp' => $event->getEventType()->getContentArbiterTroopParticipant(),
            'caGuest' => $event->getEventType()->getContentArbiterGuest(),
        ]);
    }

    public function showApproving(
        Response $response,
        Event $event,
        User $user,
    ): Response {
        $orderByUpdatedAtDesc = new Order(Order::FILED_UPDATED_AT, Order::DIRECTION_DESC);

        return $this->view->render($response, 'admin/approve-admin.twig', [
            'closedPatrolLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_PATROL_LEADER],
                [USER::STATUS_CLOSED],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'closedTroopLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_LEADER],
                [USER::STATUS_CLOSED],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'closedTroopParticipants' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_PARTICIPANT],
                [USER::STATUS_CLOSED],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'closedIsts' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_IST],
                [USER::STATUS_CLOSED],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'closedGuests' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_GUEST],
                [USER::STATUS_CLOSED],
                $event,
                $user,
                $orderByUpdatedAtDesc,
            ),
            'caIst' => $event->getEventType()->getContentArbiterIst(),
            'caPl' => $event->getEventType()->getContentArbiterPatrolLeader(),
            'caPp' => $event->getEventType()->getContentArbiterPatrolParticipant(),
            'caTl' => $event->getEventType()->getContentArbiterTroopLeader(),
            'caTp' => $event->getEventType()->getContentArbiterTroopParticipant(),
            'caGuest' => $event->getEventType()->getContentArbiterGuest(),
        ]);
    }

    public function approveParticipant(int $participantId, Request $request, Response $response): Response
    {
        $participant = $this->participantRepository->get($participantId);

        $this->participantService->approveRegistration($participant);
        $this->logger->info('Approved registration for participant with ID ' . $participant->id);

        return $this->redirect($request, $response, 'admin-show-approving');
    }

    public function showDenyParticipant(int $participantId, Response $response): Response
    {
        $participant = $this->participantRepository->get($participantId);

        return $this->view->render($response, 'admin/deny-admin.twig', ['participant' => $participant]);
    }

    public function denyParticipant(int $participantId, Request $request, Response $response): Response
    {
        // TODO check if correct event
        $reason = $this->getParameterFromBody($request, 'reason', true);
        $participant = $this->participantRepository->get($participantId);
        $this->participantService->denyRegistration($participant, $reason);
        $this->flashMessages->info($this->translator->trans('flash.info.denied'));
        $this->logger->info('Denied registration for participant with ID '
            . $participantId . ' and role ' . $participant->role . ' with reason: ' . $reason);

        return $this->redirect($request, $response, 'admin-show-approving');
    }

    public function showPayments(
        Response $response,
        Event $event,
        User $user,
    ): Response {
        return $this->view->render($response, 'admin/payments-admin.twig', [
            'approvedIsts' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_IST],
                [USER::STATUS_APPROVED],
                $event,
                $user,
            ),
            'approvedPatrolLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_PATROL_LEADER],
                [USER::STATUS_APPROVED],
                $event,
                $user,
            ),
            'approvedTroopLeaders' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_LEADER],
                [USER::STATUS_APPROVED],
                $event,
                $user,
            ),
            'approvedTroopParticipants' => $this->participantRepository->getAllParticipantsWithStatus(
                [User::ROLE_TROOP_PARTICIPANT],
                [USER::STATUS_APPROVED],
                $event,
                $user,
            ),
        ]);
    }

    public function showCancelPayment(int $paymentId, Response $response): Response
    {
        $payment = $this->paymentRepository->get($paymentId);

        return $this->view->render($response, 'admin/cancelPayment-admin.twig', ['payment' => $payment]);
    }

    public function cancelPayment(int $paymentId, User $user, Request $request, Response $response): Response
    {
        $reason = $this->getParameterFromBody($request, 'reason', true);
        $payment = $this->paymentRepository->get($paymentId);
        if ($payment->participant->getUserButNotNull()->event->id !== $user->event->id) {
            $this->flashMessages->warning($this->translator->trans('flash.error.confirmNotAllowed'));
            $this->logger->info('Payment ID ' . $paymentId
                . ' cannot be confirmed from admin with event id ' . $user->event->id);
        } else {
            $this->participantService->cancelPayment($payment, $reason);
            $this->flashMessages->info($this->translator->trans('flash.info.paymentCanceled'));
            $this->logger->info('Cancelled payment ID ' . $paymentId . ' for participant with reason: ' . $reason);
        }

        return $this->redirect(
            $request,
            $response,
            'admin-show-payments',
        );
    }

    /** TODO test */
    public function cancelAllDuePayments(Request $request, Response $response, User $user): Response
    {
        $this->paymentService->cancelDuePayments($user->event);

        return $this->redirect(
            $request,
            $response,
            'admin-show-payments',
        );
    }

    public function confirmPayment(int $paymentId, User $user, Request $request, Response $response): Response
    {
        $payment = $this->paymentRepository->get($paymentId);
        $participant = $payment->participant;
        if ($participant->getUserButNotNull()->event->id !== $user->event->id) {
            $this->flashMessages->warning($this->translator->trans('flash.error.confirmNotAllowed'));
            $this->logger->info('Payment ID ' . $paymentId
                . ' cannot be confirmed from admin with event id ' . $user->event->id);
        } else {
            $participant->registrationCloseDate = new DateTimeImmutable();
            $this->participantRepository->persist($participant);
            $this->paymentService->confirmPayment($payment);
            $this->flashMessages->success($this->translator->trans('flash.success.comfirmPayment'));
            $this->logger->info('Payment ID ' . $paymentId . ' manually confirmed as paid');
        }

        return $this->redirect(
            $request,
            $response,
            'admin-show-payments',
        );
    }

    public function showFile(string $filename): Response
    {
        // TODO check if correct event
        $file = $this->fileHandler->getFile($filename);
        $response = new \Slim\Psr7\Response(200, null, $file->stream);
        $response = $response->withAddedHeader('Content-Type', $file->mimeContentType);

        return $response;
    }

    public function showAutoPayments(Response $response, Event $event): Response
    {
        $arguments = [
            'bankPayments' => $this->bankPaymentRepository->getAllBankPaymentsOrdered($event),
            'bankPaymentsTodo' => $this->bankPaymentRepository->getBankPaymentsOrderedWithStatus(
                $event,
                BankPayment::STATUS_UNKNOWN,
            ),
        ];

        return $this->view->render($response, 'admin/paymentsAuto-admin.twig', $arguments);
    }

    public function setBreakpointFromRoute(Request $request, Response $response, Event $event): Response
    {
        $result = $this->bankPaymentService->setBreakpoint(new \DateTimeImmutable('2022-01-01'), $event);

        if ($result) {
            $this->flashMessages->success('Set breakpoint successfully');
        } else {
            $this->flashMessages->error('Something gone wrong, probably unvalid token :(');
        }

        return $this->redirect(
            $request,
            $response,
            'admin-show-auto-payments',
        );
    }

    public function updatePayments(Request $request, Response $response, Event $event): Response
    {
        $this->paymentService->updatePayments($event);

        return $this->redirect(
            $request,
            $response,
            'admin-show-auto-payments',
        );
    }

    public function markBankPaymentPaired(Request $request, Response $response, int $paymentId): Response
    {
        // TODO check if correct event
        $notice = $this->getParameterFromBody($request, 'notice', true);
        $this->bankPaymentService->setBankPaymentPaired($paymentId);
        $this->logger->info('Payment with ID ' . $paymentId . ' has been marked as paired with notice: ' . $notice);
        $this->flashMessages->info($this->translator->trans('flash.info.markedAsPaired'));

        return $this->redirect(
            $request,
            $response,
            'admin-show-auto-payments',
        );
    }

    public function markBankPaymentUnrelated(Request $request, Response $response, int $paymentId): Response
    {
        $this->bankPaymentService->setBankPaymentUnrelated($paymentId);
        $this->logger->info('Payment with ID ' . $paymentId . ' has been marked as unrelated');
        $this->flashMessages->info($this->translator->trans('flash.info.markedAsUnrelated'));

        return $this->redirect(
            $request,
            $response,
            'admin-show-auto-payments',
        );
    }

    /** TODO check */
    public function showTransferPayment(Request $request, Response $response): Response
    {
        // TODO check if correct event
        $queryParams = $request->getQueryParams();

        $emailFrom = $queryParams['emailFrom'];
        $emailTo = $queryParams['emailTo'];

        $participantFrom = $this->participantService->findParticipantFromUserMail($emailFrom);
        $participantTo = $this->participantService->findParticipantFromUserMail($emailTo);

        return $this->view->render($response, 'admin/transferPayment-admin.twig', [
            'emailFrom' => $emailFrom,
            'emailTo' => $emailTo,
            'from' => $participantFrom,
            'to' => $participantTo,
            'transferPossible' => $this->adminService->isPaymentTransferPossible(
                $participantFrom,
                $participantTo,
                $this->flashMessages
            ),
        ]);
    }

    /** TODO check */
    public function transferPayment(Request $request, Response $response): Response
    {
        // TODO check if correct event
        $participantFrom = $this->participantService->findParticipantFromUserMail(
            $this->getParameterFromBody($request, 'emailFrom')
        );
        $participantTo = $this->participantService->findParticipantFromUserMail(
            $this->getParameterFromBody($request, 'emailTo')
        );

        // TODO refactor findParticipantFromUserMail into get method
        if ($participantFrom === null || $participantTo === null) {
            throw new \RuntimeException('Found no participant');
        }

        if (!$this->adminService->isPaymentTransferPossible(
            $participantFrom,
            $participantTo,
            $this->flashMessages
        )) {
            throw new \RuntimeException('Cannot do transfer');
        }

        $this->adminService->transferPayment($participantFrom, $participantTo);
        $this->flashMessages->success($this->translator->trans('flash.success.transfer'));

        return $this->redirect(
            $request,
            $response,
            'admin-dashboard',
        );
    }
}
